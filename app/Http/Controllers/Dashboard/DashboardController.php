<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Task;
use App\Models\Department;
use App\Models\Role;
use App\Models\Notification;
use App\Models\HrDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Tự động khởi tạo dữ liệu mẫu nếu database hoàn toàn trống
     * để đảm bảo giao diện hiển thị đẹp mắt ngay lập tức.
     */
    protected function ensureMockDataExists()
    {
        if (Department::count() == 0) {
            Department::create(['id' => 1, 'TENPHONG' => 'Nhân sự', 'name' => 'Human Resources']);
            Department::create(['id' => 2, 'TENPHONG' => 'Đào tạo', 'name' => 'Training']);
            Department::create(['id' => 3, 'TENPHONG' => 'Pháp chế', 'name' => 'Legal']);
        }

        if (Role::count() == 0) {
            Role::create(['id' => 1, 'name' => 'Admin']);
            Role::create(['id' => 2, 'name' => 'Quản lý']);
            Role::create(['id' => 3, 'name' => 'Nhân viên']);
        }

        if (User::count() == 0) {
            // Tạo tài khoản admin mặc định nếu chưa có
            $admin = User::create([
                'id' => 1,
                'name' => 'Hoàng Thị Em',
                'email' => 'em.hoang@mobifone.vn',
                'password' => bcrypt(env('DEFAULT_DIRECTOR_PASSWORD', 'change-me')),
                'role_id' => 1, // Admin
                'department_id' => 1 // Nhân sự
            ]);

            // Thêm các thành viên mẫu khác
            User::create([
                'id' => 2,
                'name' => 'Nguyễn Văn An',
                'email' => 'an.nguyen@mobifone.vn',
                'password' => bcrypt(env('DEFAULT_USER_PASSWORD', 'change-me')),
                'role_id' => 2, // Quản lý
                'department_id' => 1 // Nhân sự
            ]);
            User::create([
                'id' => 3,
                'name' => 'Trần Thị Bích',
                'email' => 'bich.tran@mobifone.vn',
                'password' => bcrypt(env('DEFAULT_USER_PASSWORD', 'change-me')),
                'role_id' => 3, // Nhân viên
                'department_id' => 1 // Nhân sự
            ]);
            User::create([
                'id' => 4,
                'name' => 'Lê Minh Châu',
                'email' => 'chau.le@mobifone.vn',
                'password' => bcrypt(env('DEFAULT_USER_PASSWORD', 'change-me')),
                'role_id' => 3, // Nhân viên
                'department_id' => 2 // Đào tạo
            ]);
            User::create([
                'id' => 5,
                'name' => 'Phạm Quốc Dũng',
                'email' => 'dung.pham@mobifone.vn',
                'password' => bcrypt(env('DEFAULT_USER_PASSWORD', 'change-me')),
                'role_id' => 3, // Nhân viên
                'department_id' => 3 // Pháp chế
            ]);
        }

        if (Task::count() == 0) {
            Task::create([
                'task_name' => 'Cập nhật quy trình tuyển dụng Q3 2025',
                'description' => 'Rà soát và cập nhật quy trình tuyển dụng nhân sự quý 3 năm 2025.',
                'assigned_by' => 1,
                'assigned_to' => 2,
                'deadline' => Carbon::now()->addDays(3),
                'status' => 'Đang làm'
            ]);
            Task::create([
                'task_name' => 'Báo cáo KPI tháng 6 phòng Nhân sự',
                'description' => 'Hoàn thiện báo cáo hiệu suất KPI của cả phòng gửi Giám đốc.',
                'assigned_by' => 1,
                'assigned_to' => 3,
                'deadline' => Carbon::now()->subDays(2),
                'status' => 'Quá hạn'
            ]);
            Task::create([
                'task_name' => 'Tổ chức đào tạo kỹ năng mềm nội bộ',
                'description' => 'Chuẩn bị phòng họp và slide đào tạo kỹ năng mềm cho chuyên viên.',
                'assigned_by' => 1,
                'assigned_to' => 4,
                'deadline' => Carbon::now()->addDays(15),
                'status' => 'Chờ xử lý'
            ]);
            Task::create([
                'task_name' => 'Review hợp đồng lao động mới ký',
                'description' => 'Pháp chế review các điều khoản hợp đồng thử việc mới.',
                'assigned_by' => 1,
                'assigned_to' => 5,
                'deadline' => Carbon::now()->addDays(5),
                'status' => 'Đang review'
            ]);
            Task::create([
                'task_name' => 'Cập nhật chính sách phúc lợi nhân viên',
                'description' => 'Bổ sung chính sách bảo hiểm và nghỉ mát hè 2025.',
                'assigned_by' => 1,
                'assigned_to' => 1,
                'deadline' => Carbon::now()->subDays(5),
                'status' => 'Hoàn thành'
            ]);
        }
    }

    public function landing()
    {
        return view('landing');
    }

    public function index()
    {
        $this->ensureMockDataExists();

        // 1. Tính toán số lượng tác vụ theo trạng thái
        $totalTasks = Task::count();
        $doingTasks = Task::where('status', 'Đang làm')->count();
        $doneTasks = Task::where('status', 'Hoàn thành')->count();
        $overdueTasks = Task::where('status', 'Quá hạn')->count();

        // 2. Lấy danh sách công việc gần đây
        $tasks = Task::orderBy('updated_at', 'desc')->take(5)->get();

        // Map thông tin giả lập cho phù hợp giao diện view
        $mappedTasks = $tasks->map(function ($t) {
            $user = User::find($t->assigned_to) ?? Auth::user();
            return [
                'id' => 'WH-' . str_pad($t->id, 3, '0', STR_PAD_LEFT),
                'name' => $t->task_name,
                'assignee' => $user->name,
                'avatar' => $this->getInitials($user->name),
                'priority' => $t->id % 3 == 0 ? 'Cao' : ($t->id % 3 == 1 ? 'Trung bình' : 'Thấp'),
                'deadline' => $t->deadline ? Carbon::parse($t->deadline)->format('d/m/Y') : 'Không có',
                'status' => $t->status,
                'progress' => $t->status == 'Hoàn thành' ? 100 : ($t->status == 'Đang làm' ? 65 : ($t->status == 'Đang review' ? 80 : 0))
            ];
        });

        // 3. Danh sách thành viên đang hoạt động
        $members = User::with('department')->take(5)->get()->map(function ($u) {
            $taskCount = Task::where('assigned_to', $u->id)->count();
            return [
                'id' => 'NV' . str_pad($u->id, 3, '0', STR_PAD_LEFT),
                'name' => $u->name,
                'dept' => $u->department->TENPHONG ?? 'Nhân sự',
                'tasks' => $taskCount,
                'avatar' => $this->getInitials($u->name),
                'color' => $u->id == 1 ? '#003DA5' : ($u->id == 2 ? '#001F5B' : '#059669')
            ];
        });

        // 4. Phân bổ công việc (Donut chart data)
        $statusDistribution = [
            ['name' => 'Hoàn thành', 'value' => Task::where('status', 'Hoàn thành')->count()],
            ['name' => 'Đang làm', 'value' => Task::where('status', 'Đang làm')->count()],
            ['name' => 'Đang review', 'value' => Task::where('status', 'Đang review')->count()],
            ['name' => 'Chờ xử lý', 'value' => Task::where('status', 'Chờ xử lý')->count()],
        ];

        return view('dashboard.index', compact(
            'totalTasks', 'doingTasks', 'doneTasks', 'overdueTasks',
            'mappedTasks', 'members', 'statusDistribution'
        ));
    }

    public function tasks(Request $request)
    {
        $this->ensureMockDataExists();

        $viewType = $request->query('view', 'kanban'); // kanban hoặc list
        $filter = $request->query('filter', 'Tất cả');

        $query = Task::query();

        if ($filter == 'Của tôi') {
            $query->where('assigned_to', Auth::id());
        } elseif ($filter == 'Quá hạn') {
            $query->where('status', 'Quá hạn');
        }

        $allTasks = $query->orderBy('created_at', 'desc')->get();

        // Phân loại task theo cột Kanban
        $cols = [
            'pending' => ['label' => '📋 Chờ xử lý', 'color' => '#6B7280', 'bg' => '#F9FAFB', 'tasks' => []],
            'doing' => ['label' => '⚡ Đang làm', 'color' => '#D97706', 'bg' => '#FFFBEB', 'tasks' => []],
            'review' => ['label' => '🔍 Đang review', 'color' => '#003DA5', 'bg' => '#EFF6FF', 'tasks' => []],
            'done' => ['label' => '✅ Hoàn thành', 'color' => '#16A34A', 'bg' => '#F0FDF4', 'tasks' => []],
        ];

        $mappedTasksList = [];

        foreach ($allTasks as $t) {
            $user = User::find($t->assigned_to) ?? Auth::user();
            $mapped = [
                'id' => $t->id,
                'code' => 'WH-' . str_pad($t->id, 3, '0', STR_PAD_LEFT),
                'name' => $t->task_name,
                'description' => $t->description,
                'assignee' => $user->name,
                'avatar' => $this->getInitials($user->name),
                'priority' => $t->id % 3 == 0 ? 'Cao' : ($t->id % 3 == 1 ? 'Trung bình' : 'Thấp'),
                'deadline' => $t->deadline ? Carbon::parse($t->deadline)->format('d/m/Y') : 'Không có',
                'status' => $t->status,
                'progress' => $t->status == 'Hoàn thành' ? 100 : ($t->status == 'Đang làm' ? 65 : ($t->status == 'Đang review' ? 80 : 0))
            ];

            $mappedTasksList[] = $mapped;

            if ($t->status == 'Chờ xử lý' || $t->status == 'Todo') {
                $cols['pending']['tasks'][] = $mapped;
            } elseif ($t->status == 'Đang làm') {
                $cols['doing']['tasks'][] = $mapped;
            } elseif ($t->status == 'Đang review') {
                $cols['review']['tasks'][] = $mapped;
            } elseif ($t->status == 'Hoàn thành') {
                $cols['done']['tasks'][] = $mapped;
            } else {
                // Mặc định hoặc Quá hạn cho vào Đang làm hoặc cột riêng, tạm cho vào Đang làm
                $cols['doing']['tasks'][] = $mapped;
            }
        }

        $allUsers = User::all();

        return view('dashboard.tasks', compact('viewType', 'filter', 'cols', 'mappedTasksList', 'allUsers'));
    }

    public function saveTask(Request $request)
    {
        $request->validate([
            'task_name' => 'required|string|max:255',
            'deadline' => 'required|date',
            'assigned_to' => 'required|exists:users,id'
        ]);

        Task::create([
            'task_name' => $request->task_name,
            'description' => $request->description,
            'assigned_by' => Auth::id(),
            'assigned_to' => $request->assigned_to,
            'deadline' => $request->deadline,
            'status' => $request->status ?? 'Chờ xử lý'
        ]);

        return redirect()->route('dashboard.tasks')->with('success', 'Tạo công việc thành công!');
    }

    public function members(Request $request)
    {
        $this->ensureMockDataExists();

        $search = $request->query('search', '');
        $deptFilter = $request->query('dept', '');

        $query = User::with('department');

        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if (!empty($deptFilter) && $deptFilter != 'Tất cả phòng ban') {
            $query->whereHas('department', function($q) use ($deptFilter) {
                $q->where('TENPHONG', $deptFilter);
            });
        }

        $usersList = $query->get()->map(function ($u) {
            $taskCount = Task::where('assigned_to', $u->id)->count();
            return [
                'id' => 'NV' . str_pad($u->id, 3, '0', STR_PAD_LEFT),
                'db_id' => $u->id,
                'name' => $u->name,
                'email' => $u->email,
                'dept' => $u->department->TENPHONG ?? 'Chưa xếp phòng',
                'position' => $u->role_id == User::ROLE_ADMIN ? 'Admin' : ($u->role_id == User::ROLE_MANAGER ? 'Quản lý' : 'Nhân viên'),
                'role' => $u->role_id == User::ROLE_ADMIN ? 'Admin' : ($u->role_id == User::ROLE_MANAGER ? 'Quản lý' : 'Nhân viên'),
                'tasks' => $taskCount,
                'joined' => $u->created_at ? $u->created_at->format('d/m/Y') : '15/03/2022',
                'status' => 'active',
                'avatar' => $this->getInitials($u->name),
                'color' => $u->role_id == 1 ? '#003DA5' : ($u->role_id == 2 ? '#001F5B' : '#7C3AED')
            ];
        });

        $departments = Department::all();
        $roles = Role::all();

        return view('dashboard.members', compact('usersList', 'departments', 'roles', 'search', 'deptFilter'));
    }

    public function saveMember(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'department_id' => 'required|exists:departments,id',
            'role_id' => 'required|exists:roles,id'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'department_id' => $request->department_id,
            'role_id' => $request->role_id
        ]);

        return redirect()->route('dashboard.members')->with('success', 'Thêm thành viên mới thành công!');
    }

    public function reports()
    {
        $this->ensureMockDataExists();

        // 1. Tính toán KPIs tổng quan
        $total = Task::count();
        $doneRate = $total > 0 ? round((Task::where('status', 'Hoàn thành')->count() / $total) * 100) : 0;
        $overdueRate = $total > 0 ? round((Task::where('status', 'Quá hạn')->count() / $total) * 100) : 0;

        // 2. Mock dữ liệu tiến độ hàng tuần
        $lineData = [
            ['week' => 'T1', 'assigned' => 18, 'completed' => 12],
            ['week' => 'T2', 'assigned' => 25, 'completed' => 20],
            ['week' => 'T3', 'assigned' => 22, 'completed' => 18],
            ['week' => 'T4', 'assigned' => 30, 'completed' => 25],
            ['week' => 'T5', 'assigned' => 28, 'completed' => 22],
            ['week' => 'T6', 'assigned' => 35, 'completed' => 30],
            ['week' => 'T7', 'assigned' => 32, 'completed' => 28]
        ];

        // 3. Thống kê theo ưu tiên
        $priorityDistribution = [
            ['priority' => 'Cao', 'value' => 45],
            ['priority' => 'Trung bình', 'value' => 62],
            ['priority' => 'Thấp', 'value' => 35]
        ];

        // 4. Hiệu suất theo thành viên
        $perfData = User::take(5)->get()->map(function($u) {
            $totalUserTasks = Task::where('assigned_to', $u->id)->count();
            $done = $totalUserTasks > 0 ? round((Task::where('assigned_to', $u->id)->where('status', 'Hoàn thành')->count() / $totalUserTasks) * 100) : 60;
            $overdue = 100 - $done;
            return [
                'name' => $u->name,
                'completion' => $done,
                'overdue' => $overdue
            ];
        });

        // 5. Trạng thái phân bổ donut
        $pieData = [
            ['name' => 'Hoàn thành', 'value' => Task::where('status', 'Hoàn thành')->count() ?: 58, 'color' => '#16A34A'],
            ['name' => 'Đang làm', 'value' => Task::where('status', 'Đang làm')->count() ?: 42, 'color' => '#D97706'],
            ['name' => 'Đang review', 'value' => Task::where('status', 'Đang review')->count() ?: 22, 'color' => '#003DA5'],
            ['name' => 'Chờ xử lý', 'value' => Task::where('status', 'Chờ xử lý')->count() ?: 20, 'color' => '#6B7280'],
        ];

        return view('dashboard.reports', compact('total', 'doneRate', 'overdueRate', 'lineData', 'priorityDistribution', 'perfData', 'pieData'));
    }

    public function roles()
    {
        return view('dashboard.roles');
    }

    public function notifications()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($n) {
                $type = 'task';
                $titleLower = mb_strtolower($n->title);
                if (str_contains($titleLower, 'quá hạn')) {
                    $type = 'overdue';
                } elseif (str_contains($titleLower, 'deadline') || str_contains($titleLower, 'hạn chót') || str_contains($titleLower, 'nhắc nhở')) {
                    $type = 'deadline';
                } elseif (str_contains($titleLower, 'hoàn thành')) {
                    $type = 'complete';
                }

                return [
                    'id' => $n->id,
                    'type' => $type,
                    'title' => $n->title,
                    'desc' => $n->message,
                    'time' => $n->created_at ? $n->created_at->diffForHumans() : 'Vừa xong',
                    'read' => (bool)$n->is_read,
                ];
            });

        return view('dashboard.notifications', compact('notifications'));
    }

    // Helper tạo chữ viết tắt Avatar
    protected function getInitials($name)
    {
        $words = explode(' ', $name);
        $initials = '';
        if (count($words) >= 2) {
            $initials = mb_substr($words[count($words) - 2], 0, 1) . mb_substr($words[count($words) - 1], 0, 1);
        } else if (count($words) == 1) {
            $initials = mb_substr($words[0], 0, 2);
        }
        return mb_strtoupper($initials);
    }
}
