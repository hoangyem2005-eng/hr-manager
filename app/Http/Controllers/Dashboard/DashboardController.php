<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Task;
use App\Models\Department;
use App\Models\Role;
use App\Models\Notification;
use App\Models\HrDocument;
use App\Models\Document;
use App\Events\TaskCreated;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        $tasks = Task::with('assignee')->orderBy('updated_at', 'desc')->take(5)->get();

        // Map thông tin thực tế từ database
        $mappedTasks = $tasks->map(function ($t) {
            $user = $t->assignee ?? Auth::user();
            return [
                'id' => 'WH-' . str_pad($t->id, 3, '0', STR_PAD_LEFT),
                'name' => $t->task_name,
                'assignee' => $user->name ?? 'Chưa giao',
                'avatar' => $this->getInitials($user->name ?? 'CG'),
                'priority' => $t->priority ?? 'Trung bình',
                'deadline' => $t->deadline ? Carbon::parse($t->deadline)->format('d/m/Y') : 'Không có',
                'status' => $t->status,
                'progress' => $t->progress ?? ($t->status == 'Hoàn thành' ? 100 : ($t->status == 'Đang làm' ? 65 : ($t->status == 'Đang review' ? 80 : 0)))
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
            ['name' => 'Chờ xử lý', 'value' => Task::whereIn('status', ['Chờ xử lý', 'Todo'])->count()],
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
            $query->where(function($q) {
                $q->where('status', 'Quá hạn')
                  ->orWhere(function($sub) {
                      $sub->where('status', '!=', 'Hoàn thành')
                          ->where('deadline', '<', Carbon::now());
                  });
            });
        }

        // Lọc theo nhân viên phụ trách
        $assigneeId = $request->query('assignee_id');
        if ($assigneeId) {
            $query->where('assigned_to', $assigneeId);
        }

        $allTasks = $query->with('documents.uploader')->orderBy('created_at', 'desc')->get();

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
            
            $attachments = $t->documents->map(function ($doc) {
                return [
                    'id' => $doc->id,
                    'file_name' => $doc->file_name,
                    'file_type' => $doc->file_type,
                    'download_url' => route('document.download', $doc->id),
                    'preview_url' => route('document.preview', $doc->id),
                    'uploader' => $doc->uploader->name ?? 'Không rõ',
                    'created_at' => $doc->created_at ? $doc->created_at->format('d/m/Y H:i') : ''
                ];
            })->toArray();

            $mapped = [
                'id' => $t->id,
                'code' => 'WH-' . str_pad($t->id, 3, '0', STR_PAD_LEFT),
                'name' => $t->task_name,
                'description' => $t->description,
                'assignee' => $user->name,
                'assignee_id' => $t->assigned_to,
                'avatar' => $this->getInitials($user->name),
                'priority' => $t->priority ?? ($t->id % 3 == 0 ? 'Cao' : ($t->id % 3 == 1 ? 'Trung bình' : 'Thấp')),
                'deadline' => $t->deadline ? Carbon::parse($t->deadline)->format('d/m/Y') : 'Không có',
                'deadline_raw' => $t->deadline ? Carbon::parse($t->deadline)->format('Y-m-d') : '',
                'status' => $t->status,
                'progress' => $t->progress ?? ($t->status == 'Hoàn thành' ? 100 : ($t->status == 'Đang làm' ? 65 : ($t->status == 'Đang review' ? 80 : 0))),
                'attachments' => $attachments
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
                // Cho việc quá hạn vào cột Đang làm hoặc cột phù hợp
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
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'required|string|in:Thấp,Trung bình,Cao',
            'status' => 'nullable|string',
            'attachments'   => 'nullable|array|max:5',
            'attachments.*' => 'file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar,txt,csv',
        ]);

        $task = Task::create([
            'task_name' => $request->task_name,
            'description' => $request->description,
            'assigned_by' => Auth::id(),
            'assigned_to' => $request->assigned_to,
            'deadline' => $request->deadline,
            'priority' => $request->priority ?? 'Trung bình',
            'status' => $request->status ?? 'Chờ xử lý',
            'progress' => $request->status === 'Hoàn thành' ? 100 : 0,
        ]);

        if ($request->hasFile('attachments')) {
            $this->handleFileUploads($request->file('attachments'), $task);
        }

        event(new TaskCreated($task));

        return redirect()->route('dashboard.tasks')->with('success', 'Tạo công việc thành công!');
    }

    public function updateTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'task_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'assigned_to' => 'required|exists:users,id',
            'priority' => 'required|string|in:Thấp,Trung bình,Cao',
            'status' => 'required|string',
            'progress' => 'nullable|integer|min:0|max:100',
            'attachments'   => 'nullable|array|max:5',
            'attachments.*' => 'file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar,txt,csv',
            'delete_attachments' => 'nullable|array',
            'delete_attachments.*' => 'integer|exists:documents,id',
        ]);

        $oldAssignee = $task->assigned_to;
        $progress = $request->input('progress');
        if ($request->input('status') === 'Hoàn thành') {
            $progress = 100;
        }

        $task->update([
            'task_name' => $request->task_name,
            'description' => $request->description,
            'deadline' => $request->deadline,
            'assigned_to' => $request->assigned_to,
            'priority' => $request->priority,
            'status' => $request->status,
            'progress' => $progress ?? $task->progress,
        ]);

        if ($request->has('delete_attachments')) {
            foreach ($request->input('delete_attachments') as $docId) {
                $doc = Document::where('task_id', $task->id)->find($docId);
                if ($doc) {
                    if (Storage::disk('public')->exists($doc->file_path)) {
                        Storage::disk('public')->delete($doc->file_path);
                    }
                    $doc->delete();
                }
            }
        }

        if ($request->hasFile('attachments')) {
            $this->handleFileUploads($request->file('attachments'), $task);
        }

        if ($task->assigned_to && $task->assigned_to !== $oldAssignee) {
            event(new TaskCreated($task));
        }

        return redirect()->route('dashboard.tasks')->with('success', 'Cập nhật công việc thành công!');
    }

    public function deleteTask($id)
    {
        $task = Task::with('documents')->findOrFail($id);

        foreach ($task->documents as $doc) {
            if (Storage::disk('public')->exists($doc->file_path)) {
                Storage::disk('public')->delete($doc->file_path);
            }
            $doc->delete();
        }

        $task->delete();

        return redirect()->route('dashboard.tasks')->with('success', 'Xóa công việc thành công!');
    }

    private function handleFileUploads(array $files, Task $task): void
    {
        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $extension    = strtolower($file->getClientOriginalExtension());
            $storedName   = Str::uuid() . '.' . $extension;
            $directory    = 'tasks/' . $task->id;

            $storedPath   = $file->storeAs($directory, $storedName, 'public');

            Document::create([
                'task_id'   => $task->id,
                'user_id'   => Auth::id(),
                'file_name' => $originalName,
                'file_path' => $storedPath,
                'file_type' => $extension,
                'disk'      => 'public',
            ]);
        }
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

        // 2. Tính toán dữ liệu tiến độ 7 tuần gần đây
        $lineData = [];
        for ($i = 6; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();
            
            $assigned = Task::whereBetween('created_at', [$startOfWeek, $endOfWeek])->count();
            $completed = Task::where('status', 'Hoàn thành')
                ->whereBetween('updated_at', [$startOfWeek, $endOfWeek])
                ->count();
                
            $lineData[] = [
                'week' => 'T. ' . Carbon::now()->subWeeks($i)->weekOfYear,
                'assigned' => $assigned,
                'completed' => $completed
            ];
        }

        // 3. Thống kê theo ưu tiên thực tế từ DB
        $priorityDistribution = [
            ['priority' => 'Cao', 'value' => Task::where('priority', 'Cao')->count()],
            ['priority' => 'Trung bình', 'value' => Task::where('priority', 'Trung bình')->count()],
            ['priority' => 'Thấp', 'value' => Task::where('priority', 'Thấp')->count()],
        ];

        // 4. Hiệu suất thực tế theo thành viên có nhận việc
        $perfData = User::whereHas('assignedTasks')->take(5)->get()->map(function($u) {
            $totalUserTasks = Task::where('assigned_to', $u->id)->count();
            $done = $totalUserTasks > 0 ? round((Task::where('assigned_to', $u->id)->where('status', 'Hoàn thành')->count() / $totalUserTasks) * 100) : 0;
            $overdue = 100 - $done;
            return [
                'name' => $u->name,
                'completion' => $done,
                'overdue' => $overdue
            ];
        });

        if ($perfData->isEmpty()) {
            $perfData = User::take(5)->get()->map(function($u) {
                return [
                    'name' => $u->name,
                    'completion' => 0,
                    'overdue' => 0
                ];
            });
        }

        // 5. Trạng thái phân bổ donut từ DB
        $pieData = [
            ['name' => 'Hoàn thành', 'value' => Task::where('status', 'Hoàn thành')->count(), 'color' => '#16A34A'],
            ['name' => 'Đang làm', 'value' => Task::where('status', 'Đang làm')->count(), 'color' => '#D97706'],
            ['name' => 'Đang review', 'value' => Task::where('status', 'Đang review')->count(), 'color' => '#003DA5'],
            ['name' => 'Chờ xử lý', 'value' => Task::whereIn('status', ['Chờ xử lý', 'Todo'])->count(), 'color' => '#6B7280'],
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

    public function readNotification($id)
    {
        $notif = Notification::findOrFail($id);
        
        if ($notif->user_id === Auth::id()) {
            $notif->update(['is_read' => 1]);
        }

        if ($notif->task_id) {
            $user = Auth::user();
            if ($user && $user->role_id == 3) {
                return redirect()->route('employee.dashboard', ['task_id' => $notif->task_id]);
            }
            return redirect()->route('dashboard.tasks', ['task_id' => $notif->task_id]);
        }

        return redirect()->route('dashboard.notifications');
    }

    public function pollNotifications()
    {
        if (!Auth::check()) {
            return response()->json(['unread_count' => 0, 'new_notifications' => []]);
        }

        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        // Lấy thông báo mới tạo trong vòng 10 giây qua
        $newNotifications = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->where('created_at', '>=', now()->subSeconds(10))
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'unread_count' => $unreadCount,
            'new_notifications' => $newNotifications
        ]);
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
