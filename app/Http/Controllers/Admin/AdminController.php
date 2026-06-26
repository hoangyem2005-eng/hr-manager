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
        $doneTasks       = Task::where('status', 'Hoàn thành')->count();
        $overdueTasks    = Task::where('status', 'Quá hạn')->count();
        $doingTasks      = Task::where('status', 'Đang làm')->count();
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
                    'name'       => $u->name,
                    'email'      => $u->email,
                    'dept'       => $u->department->TENPHONG ?? '—',
                    'dept_id'    => $u->department_id,
                    'role_id'    => $u->role_id,
                    'role_name'  => $u->role_id == User::ROLE_ADMIN ? 'Admin' : ($u->role_id == User::ROLE_MANAGER ? 'Quản lý' : 'Nhân viên'),
                    'tasks'      => $taskCount,
                    'joined'     => $u->created_at ? $u->created_at->format('d/m/Y') : '—',
                    'avatar'     => $this->getInitials($u->name),
                ];
            });

        // Thống kê công việc theo phòng ban
        $deptStats = Department::withCount('users')->get()->map(function ($d) {
            $tasks  = Task::whereHas('assignee', fn($q) => $q->where('department_id', $d->id))->count();
            $done   = Task::whereHas('assignee', fn($q) => $q->where('department_id', $d->id))->where('status', 'Hoàn thành')->count();
            return [
                'name'  => $d->TENPHONG ?? $d->name,
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
            ['name' => 'Đang review','value' => Task::where('status', 'Đang review')->count(), 'color' => '#2563EB'],
            ['name' => 'Chờ xử lý', 'value' => Task::where('status', 'Chờ xử lý')->count(),  'color' => '#6B7280'],
        ];

        // Công việc gần đây toàn công ty
        $recentTasks = Task::with(['assignee', 'creator'])
            ->orderBy('created_at', 'desc')
            ->take(8)
            ->get()
            ->map(fn($t) => [
                'id'       => 'WH-' . str_pad($t->id, 3, '0', STR_PAD_LEFT),
                'name'     => $t->task_name,
                'assignee' => $t->assignee->name ?? '—',
                'dept'     => $t->assignee->department->TENPHONG ?? '—',
                'status'   => $t->status,
                'deadline' => $t->deadline ? Carbon::parse($t->deadline)->format('d/m/Y') : '—',
                'is_overdue' => $t->status === 'Quá hạn',
            ]);

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

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role_id'       => $request->role_id,
            'department_id' => $request->department_id,
        ]);

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

    // ===================== Helper =====================
    private function getInitials(string $name): string
    {
        $words = explode(' ', $name);
        if (count($words) >= 2) {
            return mb_strtoupper(
                mb_substr($words[count($words) - 2], 0, 1) .
                mb_substr($words[count($words) - 1], 0, 1)
            );
        }
        return mb_strtoupper(mb_substr($name, 0, 2));
    }
}
