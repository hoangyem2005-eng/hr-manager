<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Task;
use App\Models\Department;
use App\Models\Role;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user || !$user->isDirector()) {
                return redirect()->route('dashboard.index')
                    ->with('error', 'Chỉ Giám đốc mới có quyền truy cập trang này.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        // KPI tổng công ty
        $totalUsers      = User::count();
        $totalTasks      = Task::count();
        $doneTasks       = Task::whereIn('status', ['Hoàn thành', 'HoÃ n thÃ nh'])->count();
        $overdueTasks    = Task::whereIn('status', ['Quá hạn', 'QuÃ¡ háº¡n'])->count();
        $doingTasks      = Task::whereIn('status', ['Đang làm', 'Äang lÃ m'])->count();
        $totalDepts      = Department::count();

        $completionRate  = $totalTasks > 0
            ? round(($doneTasks / $totalTasks) * 100)
            : 0;

        // Danh sách tất cả user (để bảng nhân sự)
        $allUsers = User::with(['department', 'role'])
            ->orderBy('role_id')
            ->get()
            ->map(function ($u) {
                $taskCount = Task::where('assigned_to', $u->id)->count();
                return [
                    'id'         => $u->id,
                    'code'       => 'NV' . str_pad($u->id, 3, '0', STR_PAD_LEFT),
                    'name'       => $this->cleanVietnameseText($u->name),
                    'email'      => $u->email,
                    'dept'       => $this->cleanVietnameseText($u->department->TENPHONG ?? '—'),
                    'dept_id'    => $u->department_id,
                    'role_id'    => $u->role_id,
                    'role_name'  => $u->role_display_name,
                    'tasks'      => $taskCount,
                    'joined'     => $u->created_at ? $u->created_at->format('d/m/Y') : '—',
                    'avatar'     => $this->getInitials($u->name),
                ];
            });

        // Thống kê công việc theo phòng ban
        $deptStats = Department::withCount('users')->get()->map(function ($d) {
            $tasks  = Task::whereHas('assignee', fn($q) => $q->where('department_id', $d->id))->count();
            $done   = Task::whereHas('assignee', fn($q) => $q->where('department_id', $d->id))->whereIn('status', ['Hoàn thành', 'HoÃ n thÃ nh'])->count();
            return [
                'name'  => $this->cleanVietnameseText($d->TENPHONG ?? $d->name),
                'users' => $d->users_count,
                'tasks' => $tasks,
                'done'  => $done,
                'rate'  => $tasks > 0 ? round(($done / $tasks) * 100) : 0,
            ];
        });

        // Chart: Trạng thái công việc toàn công ty
        $statusChart = [
            ['name' => 'Hoàn thành', 'value' => $doneTasks,  'color' => '#16A34A'],
            ['name' => 'Đang làm',   'value' => $doingTasks, 'color' => '#D97706'],
            ['name' => 'Chờ đánh giá','value' => Task::whereIn('status', ['Chờ đánh giá', 'Đang review', 'Ä ang review'])->count(), 'color' => '#2563EB'],
            ['name' => 'Chờ xử lý', 'value' => Task::whereIn('status', ['Chờ xử lý', 'Chá»  xá»­ lÃ½'])->count(),  'color' => '#6B7280'],
        ];

        // Công việc gần đây toàn công ty (ưu tiên việc sắp hết hạn/quá hạn)
        $recentTasks = Task::with(['assignee.department', 'assignees.department', 'creator'])
            ->orderByRaw("CASE WHEN status IN ('Hoàn thành', 'Done', 'hoàn thành') THEN 1 ELSE 0 END ASC")
            ->orderByRaw("CASE WHEN deadline IS NULL THEN 1 ELSE 0 END ASC")
            ->orderBy('deadline', 'asc')
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get()
            ->map(function ($t) {
                $status = $this->cleanVietnameseText($t->status);
                $isOverdue = ($status === 'Quá hạn') || ($status !== 'Hoàn thành' && $t->deadline && Carbon::parse($t->deadline)->endOfDay()->isPast());
                $isDueSoon = false;
                $daysLeft = null;
                if (!$isOverdue && $status !== 'Hoàn thành' && $t->deadline) {
                    $deadlineEnd = Carbon::parse($t->deadline)->endOfDay();
                    if ($deadlineEnd->isFuture()) {
                        $daysLeft = (int) Carbon::now()->startOfDay()->diffInDays($deadlineEnd->copy()->startOfDay(), false);
                        if ($daysLeft <= 3 && $daysLeft >= 0) {
                            $isDueSoon = true;
                        }
                    }
                }

                return [
                    'id'       => 'WH-' . str_pad($t->id, 3, '0', STR_PAD_LEFT),
                    'name'     => $this->cleanVietnameseText($t->task_name),
                    'assignee' => $this->cleanVietnameseText($t->assignees->isNotEmpty() ? $t->assignees->pluck('name')->join(', ') : ($t->assignee->name ?? '—')),
                    'dept'     => $this->cleanVietnameseText($t->assignees->isNotEmpty() ? $t->assignees->map(fn ($user) => $user->department->TENPHONG ?? null)->filter()->unique()->join(', ') : ($t->assignee->department->TENPHONG ?? '—')),
                    'status'   => $status,
                    'deadline' => $t->deadline ? Carbon::parse($t->deadline)->format('d/m/Y') : '—',
                    'is_overdue' => $isOverdue,
                    'is_due_soon' => $isDueSoon,
                    'days_left' => $daysLeft,
                ];
            });

        $departments = Department::all();
        $roles       = Role::all();

        // Thông báo chưa đọc
        $unreadCount = Notification::where('user_id', Auth::id())->where('is_read', 0)->count();

        return view('admin.dashboard', compact(
            'totalUsers', 'totalTasks', 'doneTasks', 'overdueTasks', 'doingTasks',
            'totalDepts', 'completionRate', 'allUsers', 'deptStats',
            'statusChart', 'recentTasks', 'departments', 'roles', 'unreadCount'
        ));
    }

    /** Thêm mới nhân sự (không giới hạn phòng ban) */
    public function storeUser(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:6',
            'role_id'       => 'required|exists:roles,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        $newUser = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role_id'       => $request->role_id,
            'department_id' => $request->department_id,
        ]);

        // Backfill thông báo chung (broadcast từ giám đốc) mà user mới chưa nhận được.
        // Lấy tất cả thông báo chung từ user đại diện bất kỳ (user id nhỏ nhất có thông báo đó),
        // rồi sao chép cho user mới.
        $referenceUserId = User::where('id', '!=', $newUser->id)->min('id');
        if ($referenceUserId) {
            $broadcastNotifs = Notification::where('user_id', $referenceUserId)
                ->whereNull('task_id')
                ->get();

            if ($broadcastNotifs->isNotEmpty()) {
                $now  = now();
                $rows = $broadcastNotifs->map(fn ($n) => [
                    'user_id'    => $newUser->id,
                    'task_id'    => null,
                    'title'      => $n->title,
                    'message'    => $n->message,
                    'is_read'    => false,
                    'created_at' => $n->created_at,  // Giữ nguyên thời gian gốc
                    'updated_at' => $now,
                ])->all();

                Notification::insert($rows);
            }
        }

        return redirect()->route('admin.dashboard')
            ->with('success', "Đã tạo tài khoản {$request->name} thành công!");
    }


    /** Cập nhật thông tin nhân sự */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => "required|email|unique:users,email,{$id}",
            'role_id'       => 'required|exists:roles,id',
            'department_id' => 'required|exists:departments,id',
        ]);

        $user->update([
            'name'          => $request->name,
            'email'         => $request->email,
            'role_id'       => $request->role_id,
            'department_id' => $request->department_id,
        ]);

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('admin.dashboard')
            ->with('success', "Đã cập nhật tài khoản {$user->name}!");
    }

    /** Xóa nhân sự */
    public function destroyUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'Không thể xóa tài khoản đang đăng nhập!');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', "Đã xóa tài khoản {$name}!");
    }

    /** Hồ sơ nhân viên — trang lưu trữ toàn bộ thông tin nhân sự */
    public function employees(Request $request)
    {
        $search   = trim((string) $request->get('search', ''));
        $deptId   = trim((string) $request->get('dept', ''));
        $roleId   = trim((string) $request->get('role', ''));
        $statusF  = trim((string) $request->get('status', ''));
        $sortBy   = $request->get('sort', 'name');

        $query = User::with(['department', 'role', 'assignedTasks', 'documents']);

        if ($search !== '') {
            $employeeId = null;
            if (preg_match('/^(?:NV|ID)?\s*[-#:]?\s*0*(\d+)$/i', $search, $matches)) {
                $employeeId = (int) $matches[1];
            } elseif (ctype_digit($search)) {
                $employeeId = (int) $search;
            }

            $query->where(function ($inner) use ($search, $employeeId) {
                $inner->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");

                if ($employeeId !== null) {
                    $inner->orWhere('id', $employeeId);
                }
            });
        }

        if ($deptId !== '' && $deptId !== 'Tất cả phòng ban') {
            if (is_numeric($deptId)) {
                $query->where(function ($q) use ($deptId) {
                    $q->where('department_id', (int) $deptId)
                      ->orWhereHas('department', fn ($sub) => $sub->where('id', (int) $deptId));
                });
            } else {
                $query->whereHas('department', function ($q) use ($deptId) {
                    $q->where('TENPHONG', $deptId)
                      ->orWhere('name', $deptId);
                });
            }
        }

        if ($roleId !== '') {
            $query->where('role_id', (int) $roleId);
        }

        if ($statusF !== '') {
            $query->where('is_active', $statusF === 'active');
        }

        $query = match ($sortBy) {
            'tasks'   => $query->withCount('assignedTasks')->orderByDesc('assigned_tasks_count'),
            'joined'  => $query->orderByDesc('created_at'),
            'dept'    => $query->orderBy('department_id'),
            default   => $query->orderBy('name'),
        };

        $employees = $query->get()->map(function (User $u) {
            $tasks        = $u->assignedTasks;
            $totalTasks   = $tasks->count();
            $doneTasks    = $tasks->filter(fn ($t) => in_array(mb_strtolower($this->cleanVietnameseText($t->status ?? '')), ['hoàn thành', 'done'], true) || (int)($t->progress ?? 0) >= 100)->count();
            $overdueTasks = $tasks->filter(fn ($t) => !in_array(mb_strtolower($this->cleanVietnameseText($t->status ?? '')), ['hoàn thành', 'done'], true) && $t->deadline && Carbon::parse($t->deadline)->lt(Carbon::today()))->count();
            $doingTasks   = $tasks->filter(fn ($t) => in_array(mb_strtolower($this->cleanVietnameseText($t->status ?? '')), ['đang làm', 'chờ đánh giá'], true))->count();
            $doneRate     = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;

            return [
                'id'           => $u->id,
                'code'         => 'NV' . str_pad($u->id, 3, '0', STR_PAD_LEFT),
                'name'         => $this->cleanVietnameseText($u->name),
                'email'        => $u->email,
                'dept'         => $this->cleanVietnameseText($u->department?->TENPHONG ?? $u->department?->name ?? '—'),
                'dept_id'      => $u->department_id,
                'role_id'      => $u->role_id,
                'role_name'    => $this->cleanVietnameseText($u->role?->name ?? '—'),
                'is_active'    => $u->is_active,
                'avatar'       => $this->getInitials($u->name),
                'joined'       => $u->created_at?->format('d/m/Y') ?? '—',
                'total_tasks'  => $totalTasks,
                'done_tasks'   => $doneTasks,
                'overdue_tasks'=> $overdueTasks,
                'doing_tasks'  => $doingTasks,
                'done_rate'    => $doneRate,
                'doc_count'    => $u->documents->count(),
                'last_active'  => $u->updated_at?->format('d/m/Y H:i') ?? '—',
            ];
        });

        $departments = Department::orderBy('TENPHONG')->get();
        $roles       = Role::all();
        $unreadCount = Notification::where('user_id', Auth::id())->where('is_read', 0)->count();

        // Summary stats
        $totalEmployees  = $employees->count();
        $activeEmployees = $employees->where('is_active', true)->count();
        $totalTasksSum   = $employees->sum('total_tasks');
        $avgDoneRate     = $totalEmployees > 0 ? round($employees->avg('done_rate')) : 0;

        return view('admin.employees', compact(
            'employees', 'departments', 'roles',
            'totalEmployees', 'activeEmployees', 'totalTasksSum', 'avgDoneRate',
            'search', 'deptId', 'roleId', 'statusF', 'sortBy',
            'unreadCount'
        ));
    }

    // ===================== Helper =====================
    private function getInitials(string $name): string
    {
        $name = $this->cleanVietnameseText($name);
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            return mb_strtoupper(
                mb_substr($words[count($words) - 2], 0, 1) .
                mb_substr($words[count($words) - 1], 0, 1)
            );
        }
        return mb_strtoupper(mb_substr($name, 0, 2));
    }

    private function cleanVietnameseText($value)
    {
        if (!is_string($value) || $value === '') {
            return $value;
        }

        return str_replace(
            [
                'NhÃ¢n sá»±',
                'Ä Ã o táº¡o',
                'PhÃ¡p cháº¿',
                'Quáº£n lÃ½',
                'NhÃ¢n viÃªn',
                'HoÃ n thÃ nh',
                'Ä ang lÃ m',
                'QuÃ¡ háº¡n',
                'Chá»  xá»­ lÃ½',
                'HoÃ ng Thá»‹ Em',
                'Nguyá»…n VÄƒn An',
                'Tráº§n Thá»‹ BÃ­ch',
                'LÃª Minh ChÃ¢u',
                'Pháº¡m Quá»‘c DÅ©ng',
            ],
            [
                'Nhân sự',
                'Đào tạo',
                'Pháp chế',
                'Trưởng phòng',
                'Nhân viên',
                'Hoàn thành',
                'Đang làm',
                'Quá hạn',
                'Chờ xử lý',
                'Hoàng Thị Em',
                'Nguyễn Văn An',
                'Trần Thị Bích',
                'Lê Minh Châu',
                'Phạm Quốc Dũng',
            ],
            $value
        );
    }
}
