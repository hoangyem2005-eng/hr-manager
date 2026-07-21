<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Document;
use App\Models\Notification;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use App\Models\HrDocument;
use App\Models\Document;
use App\Events\TaskCreated;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    /**
     * Tự động khởi tạo dữ liệu mẫu nếu database hoàn toàn trống
     * để giao diện có dữ liệu đẹp ngay khi chạy lần đầu.
     */
    protected function ensureMockDataExists()
    {
        if (Department::count() == 0) {
            Department::create(['id' => 1, 'TENPHONG' => 'Nhân sự', 'name' => 'Human Resources']);
            Department::create(['id' => 2, 'TENPHONG' => 'Đào tạo', 'name' => 'Training']);
            Department::create(['id' => 3, 'TENPHONG' => 'Pháp chế', 'name' => 'Legal']);
        }

        if (Role::count() == 0) {
            Role::create(['id' => User::ROLE_ADMIN, 'name' => 'Giám đốc']);
            Role::create(['id' => User::ROLE_MANAGER, 'name' => 'Trưởng phòng']);
            Role::create(['id' => User::ROLE_EMPLOYEE, 'name' => 'Nhân viên']);
        }

        Role::updateOrCreate(['id' => User::ROLE_ADMIN], ['name' => 'Giám đốc']);
        Role::updateOrCreate(['id' => User::ROLE_MANAGER], ['name' => 'Trưởng phòng']);
        Role::updateOrCreate(['id' => User::ROLE_EMPLOYEE], ['name' => 'Nhân viên']);

        if (User::count() == 0) {
            $admin = User::create([
                'id' => 1,
                'name' => 'Hoàng Thị Em',
                'email' => 'em.hoang@mobifone.vn',
                'password' => bcrypt(env('DEFAULT_DIRECTOR_PASSWORD', 'change-me')),
                'role_id' => 1,
                'department_id' => 1,
            ]);

            User::create([
                'id' => 2,
                'name' => 'Nguyễn Văn An',
                'email' => 'an.nguyen@mobifone.vn',
                'password' => bcrypt(env('DEFAULT_USER_PASSWORD', 'change-me')),
                'role_id' => 2,
                'department_id' => 1,
            ]);
            User::create([
                'id' => 3,
                'name' => 'Trần Thị Bích',
                'email' => 'bich.tran@mobifone.vn',
                'password' => bcrypt(env('DEFAULT_USER_PASSWORD', 'change-me')),
                'role_id' => 3,
                'department_id' => 1,
            ]);
            User::create([
                'id' => 4,
                'name' => 'Lê Minh Châu',
                'email' => 'chau.le@mobifone.vn',
                'password' => bcrypt(env('DEFAULT_USER_PASSWORD', 'change-me')),
                'role_id' => 3,
                'department_id' => 2,
            ]);
            User::create([
                'id' => 5,
                'name' => 'Phạm Quốc Dũng',
                'email' => 'dung.pham@mobifone.vn',
                'password' => bcrypt(env('DEFAULT_USER_PASSWORD', 'change-me')),
                'role_id' => 3,
                'department_id' => 3,
            ]);
        }

        if (Task::count() == 0) {
            Task::create([
                'task_name' => 'Cập nhật quy trình tuyển dụng Q3 2025',
                'description' => 'Rà soát và cập nhật quy trình tuyển dụng nhân sự quý 3 năm 2025.',
                'assigned_by' => 1,
                'assigned_to' => 2,
                'deadline' => Carbon::now()->addDays(3),
                'status' => 'Đang làm',
            ]);
            Task::create([
                'task_name' => 'Báo cáo KPI tháng 6 phòng Nhân sự',
                'description' => 'Hoàn thiện báo cáo hiệu suất KPI của cả phòng gửi Giám đốc.',
                'assigned_by' => 1,
                'assigned_to' => 3,
                'deadline' => Carbon::now()->subDays(2),
                'status' => 'Quá hạn',
            ]);
            Task::create([
                'task_name' => 'Tổ chức đào tạo kỹ năng mềm nội bộ',
                'description' => 'Chuẩn bị phòng họp và slide đào tạo kỹ năng mềm cho chuyên viên.',
                'assigned_by' => 1,
                'assigned_to' => 4,
                'deadline' => Carbon::now()->addDays(15),
                'status' => 'Chờ xử lý',
            ]);
            Task::create([
                'task_name' => 'Review hợp đồng lao động mới ký',
                'description' => 'Pháp chế review các điều khoản hợp đồng thử việc mới.',
                'assigned_by' => 1,
                'assigned_to' => 5,
                'deadline' => Carbon::now()->addDays(5),
                'status' => 'Đang review',
            ]);
            Task::create([
                'task_name' => 'Cập nhật chính sách phúc lợi nhân viên',
                'description' => 'Bổ sung chính sách bảo hiểm và nghỉ mát hè 2025.',
                'assigned_by' => 1,
                'assigned_to' => 1,
                'deadline' => Carbon::now()->subDays(5),
                'status' => 'Hoàn thành',
            ]);
        }

        $this->normalizeLegacyVietnameseData();
    }

    protected function normalizeLegacyVietnameseData(): void
    {
        $replacements = $this->legacyVietnameseReplacements();

        DB::transaction(function () use ($replacements) {
            foreach (Department::all() as $department) {
                $department->forceFill([
                    'TENPHONG' => $this->cleanVietnameseText($department->TENPHONG, $replacements),
                    'name' => $this->cleanVietnameseText($department->name, $replacements),
                ])->save();
            }

            foreach (Role::all() as $role) {
                $role->forceFill([
                    'name' => $this->cleanVietnameseText($role->name, $replacements),
                ])->save();
            }

            foreach (User::all() as $user) {
                $user->forceFill([
                    'name' => $this->cleanVietnameseText($user->name, $replacements),
                ])->save();
            }

            foreach (Task::all() as $task) {
                $task->forceFill([
                    'task_name' => $this->cleanVietnameseText($task->task_name, $replacements),
                    'description' => $this->cleanVietnameseText($task->description, $replacements),
                    'status' => $this->cleanVietnameseText($task->status, $replacements),
                ])->save();
            }

            foreach (Notification::all() as $notification) {
                $notification->forceFill([
                    'title' => $this->cleanVietnameseText($notification->title, $replacements),
                    'message' => $this->cleanVietnameseText($notification->message, $replacements),
                ])->save();
            }
        });
    }

    protected function legacyVietnameseReplacements(): array
    {
        return [
            'NhÃ¢n sá»±' => 'Nhân sự',
            'ÄÃ o táº¡o' => 'Đào tạo',
            'PhÃ¡p cháº¿' => 'Pháp chế',
            'Quáº£n lÃ½' => 'Trưởng phòng',
            'NhÃ¢n viÃªn' => 'Nhân viên',
            'HoÃ ng Thá»‹ Em' => 'Hoàng Thị Em',
            'Nguyá»…n VÄƒn An' => 'Nguyễn Văn An',
            'Tráº§n Thá»‹ BÃ­ch' => 'Trần Thị Bích',
            'LÃª Minh ChÃ¢u' => 'Lê Minh Châu',
            'Pháº¡m Quá»‘c DÅ©ng' => 'Phạm Quốc Dũng',
            'Cáº­p nháº­t' => 'Cập nhật',
            'quy trÃ¬nh' => 'quy trình',
            'tuyá»ƒn dá»¥ng' => 'tuyển dụng',
            'RÃ  soÃ¡t' => 'Rà soát',
            'nhÃ¢n sá»±' => 'nhân sự',
            'quÃ½' => 'quý',
            'nÄƒm' => 'năm',
            'BÃ¡o cÃ¡o' => 'Báo cáo',
            'bÃ¡o cÃ¡o' => 'báo cáo',
            'thÃ¡ng' => 'tháng',
            'phÃ²ng' => 'phòng',
            'HoÃ n thiá»‡n' => 'Hoàn thiện',
            'hiá»‡u suáº¥t' => 'hiệu suất',
            'cá»§a' => 'của',
            'cáº£' => 'cả',
            'gá»­i' => 'gửi',
            'GiÃ¡m Ä‘á»‘c' => 'Giám đốc',
            'Tá»• chá»©c' => 'Tổ chức',
            'Ä‘Ã o táº¡o' => 'đào tạo',
            'ká»¹ nÄƒng' => 'kỹ năng',
            'má»m' => 'mềm',
            'ná»™i bá»™' => 'nội bộ',
            'Chuáº©n bá»‹' => 'Chuẩn bị',
            'phÃ²ng há»p' => 'phòng họp',
            'vÃ ' => 'và',
            'chuyÃªn viÃªn' => 'chuyên viên',
            'há»£p Ä‘á»“ng' => 'hợp đồng',
            'lao Ä‘á»™ng' => 'lao động',
            'má»›i kÃ½' => 'mới ký',
            'PhÃ¡p cháº¿' => 'Pháp chế',
            'Ä‘iá»u khoáº£n' => 'điều khoản',
            'thá»­ viá»‡c' => 'thử việc',
            'chÃ­nh sÃ¡ch' => 'chính sách',
            'phÃºc lá»£i' => 'phúc lợi',
            'Bá»• sung' => 'Bổ sung',
            'báº£o hiá»ƒm' => 'bảo hiểm',
            'nghá»‰ mÃ¡t' => 'nghỉ mát',
            'HoÃ n thÃ nh' => 'Hoàn thành',
            'Äang lÃ m' => 'Đang làm',
            'QuÃ¡ háº¡n' => 'Quá hạn',
            'Chá» xá»­ lÃ½' => 'Chờ xử lý',
            'Äang review' => 'Đang review',
            'Trung bÃ¬nh' => 'Trung bình',
            'Tháº¥p' => 'Thấp',
            'KhÃ´ng cÃ³' => 'Không có',
            'Táº¥t cáº£ phÃ²ng ban' => 'Tất cả phòng ban',
            'ChÆ°a xáº¿p phÃ²ng' => 'Chưa xếp phòng',
            'ThÃªm thÃ nh viÃªn má»›i thÃ nh cÃ´ng!' => 'Thêm thành viên mới thành công!',
            'quÃ¡ háº¡n' => 'quá hạn',
            'háº¡n chÃ³t' => 'hạn chót',
            'nháº¯c nhá»Ÿ' => 'nhắc nhở',
            'hoÃ n thÃ nh' => 'hoàn thành',
            'Vá»«a xong' => 'Vừa xong',
            'ThÃ´ng bÃ¡o Ä‘Ã£ Ä‘Æ°á»£c Ä‘Ã¡nh dáº¥u lÃ  Ä‘Ã£ Ä‘á»c.' => 'Thông báo đã được đánh dấu là đã đọc.',
            'ÄÃ£ Ä‘Ã¡nh dáº¥u táº¥t cáº£ thÃ´ng bÃ¡o lÃ  Ä‘Ã£ Ä‘á»c.' => 'Đã đánh dấu tất cả thông báo là đã đọc.',
        ];
    }

    protected function cleanVietnameseText($value, ?array $replacements = null)
    {
        if (!is_string($value) || $value === '') {
            return $value;
        }

        $replacements = $replacements ?? $this->legacyVietnameseReplacements();

        return str_replace(array_keys($replacements), array_values($replacements), $value);
    }

    public function landing()
    {
        return view('landing');
    }

    public function index()
    {
        $this->ensureMockDataExists();

        $totalTasks = Task::count();
        $doingTasks = Task::where('status', 'Đang làm')->count();
        $doneTasks = Task::where('status', 'Hoàn thành')->count();
        $overdueTasks = Task::where('status', 'Quá hạn')->count();

        // 2. Lấy danh sách công việc gần đây
        $tasks = Task::with('assignee')->orderBy('updated_at', 'desc')->take(5)->get();

        // Map thông tin thực tế từ database
        $mappedTasks = $tasks->map(function ($t) {
            $user = $t->assignee ?? Auth::user();
            $status = $this->cleanVietnameseText($t->status);
            $userName = $user ? $this->cleanVietnameseText($user->name) : 'Chưa giao';
            return [
                'id' => 'WH-' . str_pad($t->id, 3, '0', STR_PAD_LEFT),
                'name' => $this->cleanVietnameseText($t->task_name),
                'assignee' => $userName,
                'avatar' => $this->getInitials($userName),
                'priority' => $t->priority ?? 'Trung bình',
                'deadline' => $t->deadline ? Carbon::parse($t->deadline)->format('d/m/Y') : 'Không có',
                'status' => $status,
                'progress' => $t->progress ?? ($status == 'Hoàn thành' ? 100 : ($status == 'Đang làm' ? 65 : ($status == 'Đang review' ? 80 : 0)))
            ];
        });

        $members = User::with('department')->take(5)->get()->map(function ($u) {
            $taskCount = Task::where('assigned_to', $u->id)->count();

            return [
                'id' => 'NV' . str_pad($u->id, 3, '0', STR_PAD_LEFT),
                'name' => $this->cleanVietnameseText($u->name),
                'dept' => $this->cleanVietnameseText($u->department->TENPHONG ?? 'Nhân sự'),
                'tasks' => $taskCount,
                'avatar' => $this->getInitials($u->name),
                'color' => $u->id == 1 ? '#003DA5' : ($u->id == 2 ? '#001F5B' : '#059669'),
            ];
        });

        $statusDistribution = [
            ['name' => 'Hoàn thành', 'value' => Task::where('status', 'Hoàn thành')->count()],
            ['name' => 'Đang làm', 'value' => Task::where('status', 'Đang làm')->count()],
            ['name' => 'Đang review', 'value' => Task::where('status', 'Đang review')->count()],
            ['name' => 'Chờ xử lý', 'value' => Task::whereIn('status', ['Chờ xử lý', 'Todo'])->count()],
        ];

        return view('dashboard.index', compact(
            'totalTasks',
            'doingTasks',
            'doneTasks',
            'overdueTasks',
            'mappedTasks',
            'members',
            'statusDistribution'
        ));
    }

    public function tasks(Request $request)
    {
        $this->ensureMockDataExists();

        $currentUser = Auth::user();
        $viewType = $request->query('view', 'kanban');
        $filter = $request->query('filter', 'Tất cả');

        $query = Task::with(['assignee', 'assignees.department', 'documents.uploader']);

        if ($currentUser->isLeader() && !$currentUser->isDirector()) {
            $teamMemberIds = User::where('department_id', $currentUser->department_id)->pluck('id');
            $query->where(function ($q) use ($currentUser, $teamMemberIds) {
                $q->where('assigned_by', $currentUser->id)
                    ->orWhereIn('assigned_to', $teamMemberIds)
                    ->orWhereHas('assignees', fn ($assignees) => $assignees->whereIn('users.id', $teamMemberIds));
            });
        } elseif ($currentUser->isEmployee()) {
            $query->where(function ($q) use ($currentUser) {
                $q->where('assigned_to', $currentUser->id)
                    ->orWhereHas('assignees', fn ($assignees) => $assignees->where('users.id', $currentUser->id));
            });
        }

        if ($filter === 'Của tôi') {
            $query->where(function ($q) use ($currentUser) {
                $q->where('assigned_to', $currentUser->id)
                    ->orWhereHas('assignees', fn ($assignees) => $assignees->where('users.id', $currentUser->id));
            });
        } elseif ($filter === 'Quá hạn') {
            $query->where(function ($q) {
                $q->where('status', 'Quá hạn')
                  ->orWhere(function ($sub) {
                      $sub->where('status', '!=', 'Hoàn thành')
                          ->where('deadline', '<', Carbon::now());
                  });
            });
        }

        // Lọc theo nhân viên phụ trách
        $assigneeId = $request->query('assignee_id');
        if ($assigneeId) {
            $query->where(function ($q) use ($assigneeId) {
                $q->where('assigned_to', $assigneeId)
                    ->orWhereHas('assignees', fn ($assignees) => $assignees->where('users.id', $assigneeId));
            });
        }

        $allTasks = $query->with('documents.uploader')->orderBy('created_at', 'desc')->get();

        $cols = [
            'pending' => ['label' => 'Chờ xử lý', 'color' => '#6B7280', 'bg' => '#F9FAFB', 'tasks' => []],
            'doing' => ['label' => 'Đang làm', 'color' => '#D97706', 'bg' => '#FFFBEB', 'tasks' => []],
            'review' => ['label' => 'Đang review', 'color' => '#2563EB', 'bg' => '#EFF6FF', 'tasks' => []],
            'done' => ['label' => 'Hoàn thành', 'color' => '#16A34A', 'bg' => '#F0FDF4', 'tasks' => []],
        ];

        $mappedTasksList = [];

        foreach ($allTasks as $t) {
            $assignees = $t->assignees->isNotEmpty()
                ? $t->assignees
                : collect([$t->assignee ?? $currentUser])->filter();
            $user = $assignees->first() ?? $currentUser;
            $status = $this->cleanVietnameseText($t->status);
            $visibleDocuments = $t->documents
                ->filter(function ($document) use ($currentUser, $t, $assignees) {
                    if ((int) $document->user_id === (int) $currentUser->id) {
                        return true;
                    }

                    if ($currentUser->isDirector()) {
                        return $document->review_status === \App\Models\Document::STATUS_DIRECTOR_VISIBLE;
                    }

                    if ($currentUser->isLeader()) {
                        return (int) optional($document->uploader)->department_id === (int) $currentUser->department_id
                            || (int) optional($t->assignee)->department_id === (int) $currentUser->department_id
                            || $assignees->contains(fn ($assignee) => (int) $assignee->department_id === (int) $currentUser->department_id)
                            || (int) $t->assigned_by === (int) $currentUser->id
                            || (int) $t->assigned_to === (int) $currentUser->id;
                    }

                    return false;
                })
                ->sortByDesc('created_at')
                ->values();

            $mapped = [
                'id' => $t->id,
                'code' => 'WH-' . str_pad($t->id, 3, '0', STR_PAD_LEFT),
                'name' => $this->cleanVietnameseText($t->task_name),
                'description' => $this->cleanVietnameseText($t->description),
<<<<<<< HEAD
                'assignee' => $user ? $this->cleanVietnameseText($user->name) : 'Chưa giao',
                'assignee_id' => $t->assigned_to,
                'avatar' => $this->getInitials($user ? $user->name : 'CG'),
                'priority' => $t->priority ?? 'Trung bình',
=======
                'assignee' => $this->cleanVietnameseText($assignees->pluck('name')->join(', ')),
                'avatar' => $this->getInitials($user->name),
                'assignee_count' => $assignees->count(),
                'priority' => $t->id % 3 == 0 ? 'Cao' : ($t->id % 3 == 1 ? 'Trung bình' : 'Thấp'),
>>>>>>> ed1625cf337db5c518ca0e6040eb706bf6818b93
                'deadline' => $t->deadline ? Carbon::parse($t->deadline)->format('d/m/Y') : 'Không có',
                'deadline_raw' => $t->deadline ? Carbon::parse($t->deadline)->format('Y-m-d') : '',
                'status' => $status,
                'progress' => $t->progress ?? ($status === 'Hoàn thành' ? 100 : ($status === 'Đang làm' ? 65 : ($status === 'Đang review' ? 80 : 0))),
                'documents_count' => $visibleDocuments->count(),
                'documents' => $visibleDocuments
                    ->map(fn ($document) => [
                        'id' => $document->id,
                        'file_name' => $document->file_name,
                        'file_type' => strtoupper($document->file_type ?? 'FILE'),
                        'uploader' => $document->uploader?->name ?? 'Không rõ',
                        'uploaded_at' => $document->created_at?->format('d/m/Y H:i'),
                        'preview_url' => route('congviec.file.preview', $document->id),
                        'download_url' => route('congviec.file.download', $document->id),
                    ])
                    ->values()
                    ->all(),
            ];

            $mappedTasksList[] = $mapped;

            if (in_array($status, ['Chờ xử lý', 'Todo'], true)) {
                $cols['pending']['tasks'][] = $mapped;
            } elseif ($status === 'Đang làm') {
                $cols['doing']['tasks'][] = $mapped;
            } elseif ($status === 'Đang review') {
                $cols['review']['tasks'][] = $mapped;
            } elseif ($status === 'Hoàn thành') {
                $cols['done']['tasks'][] = $mapped;
            } else {
                // Cho việc quá hạn vào cột Đang làm hoặc cột phù hợp
                $cols['doing']['tasks'][] = $mapped;
            }
        }

        if ($currentUser->isDirector()) {
            $allUsers = User::with(['department', 'role'])->orderBy('role_id')->orderBy('name')->get();
            $roleVariant = 'director';
        } elseif ($currentUser->isLeader()) {
            $allUsers = User::with(['department', 'role'])
                ->where('department_id', $currentUser->department_id)
                ->where('role_id', User::ROLE_EMPLOYEE)
                ->orderBy('name')
                ->get();
            $roleVariant = 'manager';
        } else {
            $allUsers = collect([$currentUser]);
            $roleVariant = 'employee';
        }

        if ($request->routeIs('employee.*')) {
            return view('employee.tasks', compact('viewType', 'filter', 'cols', 'mappedTasksList', 'allUsers', 'roleVariant'));
        }

        if ($request->routeIs('admin.*')) {
            return view('admin.tasks', compact('viewType', 'filter', 'cols', 'mappedTasksList', 'allUsers', 'roleVariant'));
        }

        if ($request->routeIs('manager.*')) {
            return view('manager.tasks', compact('viewType', 'filter', 'cols', 'mappedTasksList', 'allUsers', 'roleVariant'));
        }

        return view('dashboard.tasks', compact('viewType', 'filter', 'cols', 'mappedTasksList', 'allUsers', 'roleVariant'));
    }

    public function saveTask(Request $request)
    {
        $currentUser = Auth::user();
        $assignedToInput = $request->input('assigned_to', []);
        $assignedToIds = collect(is_array($assignedToInput) ? $assignedToInput : [$assignedToInput])
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $request->merge(['assigned_to' => $assignedToIds]);

        $request->validate([
            'task_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'required|date',
            'assigned_to' => 'nullable|array',
            'assigned_to.*' => 'integer|exists:users,id',
            'priority' => 'nullable|string|in:Thấp,Trung bình,Cao',
            'status' => 'nullable|string|max:50',
            'attachments'   => 'nullable|array|max:5',
            'attachments.*' => 'file|max:20480|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar,txt,csv',
        ]);

        $status = $request->status ?: 'Chờ xử lý';
        $priority = $request->priority ?: 'Trung bình';
        $successMessage = 'Tạo công việc thành công!';

        if ($currentUser->isDirector()) {
            if (empty($assignedToIds)) {
                return back()->withInput()->with('error', 'Vui lòng chọn người phụ trách công việc.');
            }
            $successMessage = 'Đã giao công việc cấp công ty thành công!';
        } elseif ($currentUser->isLeader()) {
            if (empty($assignedToIds)) {
                return back()->withInput()->with('error', 'Vui lòng chọn nhân viên trong phòng.');
            }

            $invalidAssignee = User::whereIn('id', $assignedToIds)
                ->get()
                ->first(fn ($assignee) => (int) $assignee->department_id !== (int) $currentUser->department_id || !$assignee->isEmployee());

            if ($invalidAssignee) {
                return back()
                    ->withInput()
                    ->with('error', 'Trưởng phòng chỉ được giao việc cho nhân viên trong phòng của mình.');
            }

            $status = in_array($status, ['Chờ xử lý', 'Đang làm'], true) ? $status : 'Chờ xử lý';
            $successMessage = 'Đã giao công việc cho nhân viên trong phòng!';
        } else {
            $assignedToIds = [$currentUser->id];
            $status = 'Chờ xử lý';
            $successMessage = 'Đã gửi đề xuất công việc để quản lý xem xét!';
        }

<<<<<<< HEAD
        $createdTasks = collect($assignedToIds)->map(function (int $assignedTo) use ($request, $currentUser, $status, $priority) {
            $task = Task::create([
                'task_name' => $request->task_name,
                'description' => $request->description,
                'assigned_by' => $currentUser->id,
                'assigned_to' => $assignedTo,
                'deadline' => $request->deadline,
                'priority' => $priority,
                'status' => $status,
                'progress' => $status === 'Hoàn thành' ? 100 : 0,
            ]);

            if ($request->hasFile('attachments')) {
                $this->handleFileUploads($request->file('attachments'), $task);
            }

            if ($assignedTo !== (int) $currentUser->id) {
                Notification::create([
                    'user_id' => $assignedTo,
                    'task_id' => $task->id,
                    'title' => 'Bạn vừa được giao công việc mới',
                    'message' => 'WH-' . str_pad($task->id, 3, '0', STR_PAD_LEFT) . ': ' . $task->task_name,
                    'is_read' => false,
                ]);
            }

            event(new TaskCreated($task));

            return $task;
        });

        if ($createdTasks->count() > 1) {
            $successMessage .= ' (' . $createdTasks->count() . ' nhân viên)';
=======
        $primaryAssignee = $assignedToIds[0] ?? $currentUser->id;
        $task = Task::create([
            'task_name' => $request->task_name,
            'description' => $request->description,
            'assigned_by' => $currentUser->id,
            'assigned_to' => $primaryAssignee,
            'deadline' => $request->deadline,
            'status' => $status,
            'progress' => 0,
        ]);

        $task->assignees()->sync($assignedToIds);

        foreach ($assignedToIds as $assignedTo) {
            if ($assignedTo === (int) $currentUser->id) {
                continue;
            }

            Notification::create([
                'user_id' => $assignedTo,
                'task_id' => $task->id,
                'title' => 'Bạn vừa được giao công việc mới',
                'message' => 'WH-' . str_pad($task->id, 3, '0', STR_PAD_LEFT) . ': ' . $task->task_name,
                'is_read' => false,
            ]);
>>>>>>> ed1625cf337db5c518ca0e6040eb706bf6818b93
        }

        if (count($assignedToIds) > 1) {
            $successMessage .= ' (' . count($assignedToIds) . ' người cùng làm)';
        }

        $taskIndexRoute = $this->roleRouteName($request, 'tasks');

        return redirect()->route($taskIndexRoute)->with('success', $successMessage);
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

        $search = trim((string) $request->query('search', ''));
        $deptFilter = $request->query('dept', '');

        $query = User::with('department');

        if ($search !== '') {
            $employeeId = null;
            if (preg_match('/^NV0*(\d+)$/i', $search, $matches)) {
                $employeeId = (int) $matches[1];
            } elseif (ctype_digit($search)) {
                $employeeId = (int) $search;
            }

            $query->where(function ($q) use ($search, $employeeId) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");

                if ($employeeId !== null) {
                    $q->orWhere('id', $employeeId);
                }
            });
        }

        if (!empty($deptFilter) && $deptFilter != 'Tất cả phòng ban') {
            $query->whereHas('department', function ($q) use ($deptFilter) {
                $q->where('TENPHONG', $deptFilter);
            });
        }

        $usersList = $query
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

        $usersList->setCollection($usersList->getCollection()->map(function ($u) {
            $taskCount = Task::where('assigned_to', $u->id)->count();
            $roleName = match ((int) $u->role_id) {
                User::ROLE_ADMIN => 'Giám đốc',
                User::ROLE_MANAGER => 'Trưởng phòng',
                default => 'Nhân viên',
            };

            return [
                'id' => 'NV' . str_pad($u->id, 3, '0', STR_PAD_LEFT),
                'db_id' => $u->id,
                'name' => $this->cleanVietnameseText($u->name),
                'email' => $u->email,
                'dept' => $this->cleanVietnameseText($u->department->TENPHONG ?? 'Chưa xếp phòng'),
                'department_id' => $u->department_id,
                'position' => $roleName,
                'role' => $roleName,
                'role_id' => $u->role_id,
                'tasks' => $taskCount,
                'joined' => $u->created_at ? $u->created_at->format('d/m/Y') : '15/03/2022',
                'status' => $u->is_active ? 'active' : 'inactive',
                'avatar' => $this->getInitials($u->name),
                'color' => $u->role_id == 1 ? '#003DA5' : ($u->role_id == 2 ? '#001F5B' : '#7C3AED'),
            ];
        }));

        $departments = Department::all();
        $roles = Role::all();

        if ($request->routeIs('admin.*')) {
            return view('admin.members', compact('usersList', 'departments', 'roles', 'search', 'deptFilter'));
        }

        return view('dashboard.members', compact('usersList', 'departments', 'roles', 'search', 'deptFilter'));
    }

    public function saveMember(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'department_id' => 'required|exists:departments,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'department_id' => $request->department_id,
            'role_id' => $request->role_id,
            'is_active' => true,
        ]);

        return redirect()->route('dashboard.members')->with('success', 'Thêm thành viên mới thành công!');
    }

    public function updateMember(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'department_id' => 'required|exists:departments,id',
            'password' => 'nullable|min:6',
        ]);

        $user->fill([
            'name' => $request->name,
            'email' => $request->email,
            'department_id' => $request->department_id,
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('dashboard.members')->with('success', "Đã cập nhật thành viên {$user->name}.");
    }

    public function updateMemberRole(Request $request, User $user)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        if ((int) $user->id === (int) Auth::id() && (int) $request->role_id !== (int) $user->role_id) {
            return back()->with('error', 'Bạn không thể tự thay đổi vai trò của chính mình.');
        }

        $user->update(['role_id' => $request->role_id]);

        return redirect()->route('dashboard.members')->with('success', "Đã cập nhật vai trò cho {$user->name}.");
    }

    public function toggleMemberStatus(User $user)
    {
        if ((int) $user->id === (int) Auth::id()) {
            return back()->with('error', 'Bạn không thể tự vô hiệu hóa tài khoản đang đăng nhập.');
        }

        $user->update(['is_active' => !$user->is_active]);

        $message = $user->is_active
            ? "Đã kích hoạt lại tài khoản {$user->name}."
            : "Đã vô hiệu hóa tài khoản {$user->name}.";

        return redirect()->route('dashboard.members')->with('success', $message);
    }

    public function reports()
    {
        $this->ensureMockDataExists();

        $today = Carbon::today();
        $allTasks = Task::with(['assignee.department', 'assigner', 'documents'])->get();
        $allUsers = User::with('department')->get();
        $employeeUploadedDocuments = Document::with(['uploader.department', 'task'])
            ->whereHas('uploader', fn ($query) => $query->where('role_id', User::ROLE_EMPLOYEE))
            ->latest()
            ->get();

<<<<<<< HEAD
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
=======
        $normalizedStatus = function (Task $task): string {
            return mb_strtolower($this->cleanVietnameseText($task->status ?? ''));
        };

        $isDone = function (Task $task) use ($normalizedStatus): bool {
            $status = $normalizedStatus($task);

            return in_array($status, ['hoàn thành', 'done'], true) || (int) ($task->progress ?? 0) >= 100;
        };
>>>>>>> ed1625cf337db5c518ca0e6040eb706bf6818b93

        $isOverdue = function (Task $task) use ($today, $isDone): bool {
            return !$isDone($task)
                && $task->deadline
                && Carbon::parse($task->deadline)->lt($today);
        };

        $total = $allTasks->count();
        $completedTasks = $allTasks->filter($isDone);
        $overdueTasks = $allTasks->filter($isOverdue);
        $inProgressTasks = $allTasks->filter(function (Task $task) use ($normalizedStatus, $isDone, $isOverdue) {
            $status = $normalizedStatus($task);

            return !$isDone($task)
                && !$isOverdue($task)
                && (str_contains($status, 'đang làm') || str_contains($status, 'review') || (int) ($task->progress ?? 0) > 0);
        });
        $pendingTasks = $allTasks->filter(function (Task $task) use ($normalizedStatus, $isDone, $isOverdue) {
            $status = $normalizedStatus($task);

            return !$isDone($task)
                && !$isOverdue($task)
                && (str_contains($status, 'chờ') || $status === 'todo' || (int) ($task->progress ?? 0) === 0);
        });

<<<<<<< HEAD
        if ($perfData->isEmpty()) {
            $perfData = User::take(5)->get()->map(function($u) {
                return [
                    'name' => $this->cleanVietnameseText($u->name),
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
=======
        $doneRate = $total > 0 ? round(($completedTasks->count() / $total) * 100) : 0;
        $overdueRate = $total > 0 ? round(($overdueTasks->count() / $total) * 100) : 0;
        $activeUsers = $allUsers->where('is_active', true)->count();
        $documentCount = $employeeUploadedDocuments->count();

        $completedWithDates = $completedTasks->filter(fn (Task $task) => $task->created_at && $task->updated_at);
        $avgCompletionDays = $completedWithDates->count() > 0
            ? round($completedWithDates->avg(fn (Task $task) => max(0, $task->created_at->diffInHours($task->updated_at) / 24)), 1)
            : 0;

        $monthStart = $today->copy()->startOfMonth();
        $previousMonthStart = $today->copy()->subMonthNoOverflow()->startOfMonth();
        $previousMonthEnd = $today->copy()->subMonthNoOverflow()->endOfMonth();
        $tasksThisMonth = $allTasks->filter(fn (Task $task) => $task->created_at && $task->created_at->gte($monthStart))->count();
        $tasksPreviousMonth = $allTasks->filter(fn (Task $task) => $task->created_at && $task->created_at->betweenIncluded($previousMonthStart, $previousMonthEnd))->count();
        $monthlyTaskChange = $tasksPreviousMonth > 0
            ? round((($tasksThisMonth - $tasksPreviousMonth) / $tasksPreviousMonth) * 100)
            : ($tasksThisMonth > 0 ? 100 : 0);

        $lineData = collect(range(6, 0))->map(function (int $daysAgo) use ($today, $allTasks, $isDone) {
            $date = $today->copy()->subDays($daysAgo);

            return [
                'label' => $date->format('d/m'),
                'assigned' => $allTasks->filter(fn (Task $task) => $task->created_at && $task->created_at->isSameDay($date))->count(),
                'completed' => $allTasks->filter(fn (Task $task) => $isDone($task) && $task->updated_at && $task->updated_at->isSameDay($date))->count(),
            ];
        })->values();

        $departmentData = Department::orderBy('TENPHONG')->get()->map(function (Department $department) use ($allTasks, $isDone, $isOverdue) {
            $departmentTasks = $allTasks->filter(fn (Task $task) => (int) optional($task->assignee)->department_id === (int) $department->id);
            $totalDepartmentTasks = $departmentTasks->count();
            $completedDepartmentTasks = $departmentTasks->filter($isDone)->count();

            return [
                'name' => $this->cleanVietnameseText($department->TENPHONG ?: $department->name),
                'total' => $totalDepartmentTasks,
                'completed' => $completedDepartmentTasks,
                'overdue' => $departmentTasks->filter($isOverdue)->count(),
                'completion_rate' => $totalDepartmentTasks > 0 ? round(($completedDepartmentTasks / $totalDepartmentTasks) * 100) : 0,
            ];
        })->filter(fn ($department) => $department['total'] > 0)->values();

        $perfData = $allUsers->map(function (User $user) use ($allTasks, $isDone, $isOverdue) {
            $userTasks = $allTasks->where('assigned_to', $user->id);
            $totalUserTasks = $userTasks->count();
            $completedUserTasks = $userTasks->filter($isDone)->count();
            $overdueUserTasks = $userTasks->filter($isOverdue)->count();

            return [
                'name' => $this->cleanVietnameseText($user->name),
                'department' => $this->cleanVietnameseText(optional($user->department)->TENPHONG ?? 'Chưa xếp phòng'),
                'total' => $totalUserTasks,
                'completed' => $completedUserTasks,
                'overdue' => $overdueUserTasks,
                'completion' => $totalUserTasks > 0 ? round(($completedUserTasks / $totalUserTasks) * 100) : 0,
                'overdue_rate' => $totalUserTasks > 0 ? round(($overdueUserTasks / $totalUserTasks) * 100) : 0,
            ];
        })->filter(fn ($user) => $user['total'] > 0)
            ->sortByDesc('total')
            ->take(8)
            ->values();

        $documentUploaderData = $employeeUploadedDocuments
            ->groupBy('user_id')
            ->map(function ($documents) {
                $uploader = $documents->first()->uploader;

                return [
                    'name' => $this->cleanVietnameseText(optional($uploader)->name ?? 'Không rõ'),
                    'department' => $this->cleanVietnameseText(optional(optional($uploader)->department)->TENPHONG ?? 'Chưa xếp phòng'),
                    'total' => $documents->count(),
                    'waiting' => $documents->where('review_status', Document::STATUS_MANAGER_REVIEW)->count(),
                    'forwarded' => $documents->where('review_status', Document::STATUS_DIRECTOR_VISIBLE)->count(),
                    'latest_at' => optional($documents->first()->created_at)->format('d/m/Y H:i') ?? 'Không rõ',
                ];
            })
            ->sortByDesc('total')
            ->values();

        $recentEmployeeDocuments = $employeeUploadedDocuments
            ->take(6)
            ->map(fn (Document $document) => [
                'file_name' => $document->file_name,
                'file_type' => strtoupper($document->file_type ?? 'FILE'),
                'uploader' => $this->cleanVietnameseText(optional($document->uploader)->name ?? 'Không rõ'),
                'department' => $this->cleanVietnameseText(optional(optional($document->uploader)->department)->TENPHONG ?? 'Chưa xếp phòng'),
                'task_code' => $document->task ? 'WH-' . str_pad($document->task->id, 3, '0', STR_PAD_LEFT) : 'Không có',
                'task_name' => $this->cleanVietnameseText(optional($document->task)->task_name ?? 'Không gắn công việc'),
                'uploaded_at' => optional($document->created_at)->format('d/m/Y H:i') ?? 'Không rõ',
                'status' => $document->review_status === Document::STATUS_DIRECTOR_VISIBLE ? 'Đã chuyển lên Giám đốc' : 'Chờ trưởng phòng duyệt',
            ])
            ->values();

        $pieData = collect([
            ['name' => 'Chờ xử lý', 'value' => $pendingTasks->count(), 'color' => '#64748B'],
            ['name' => 'Đang làm', 'value' => $inProgressTasks->count(), 'color' => '#003DA5'],
            ['name' => 'Hoàn thành', 'value' => $completedTasks->count(), 'color' => '#16A34A'],
            ['name' => 'Quá hạn', 'value' => $overdueTasks->count(), 'color' => '#E4002B'],
        ]);

        $summary = [
            'total' => $total,
            'completed' => $completedTasks->count(),
            'in_progress' => $inProgressTasks->count(),
            'pending' => $pendingTasks->count(),
            'overdue' => $overdueTasks->count(),
            'done_rate' => $doneRate,
            'overdue_rate' => $overdueRate,
            'active_users' => $activeUsers,
            'departments' => Department::count(),
            'documents' => $documentCount,
            'documents_waiting' => $employeeUploadedDocuments->where('review_status', Document::STATUS_MANAGER_REVIEW)->count(),
            'documents_forwarded' => $employeeUploadedDocuments->where('review_status', Document::STATUS_DIRECTOR_VISIBLE)->count(),
            'avg_completion_days' => $avgCompletionDays,
            'monthly_task_change' => $monthlyTaskChange,
>>>>>>> ed1625cf337db5c518ca0e6040eb706bf6818b93
        ];

        $reportView = 'dashboard.reports';

        if (request()->routeIs('admin.*')) {
            $reportView = 'admin.reports';
        } elseif (request()->routeIs('manager.*')) {
            $reportView = 'manager.reports';
        }

        return view($reportView, compact(
            'total',
            'doneRate',
            'overdueRate',
            'lineData',
            'departmentData',
            'perfData',
            'pieData',
            'documentUploaderData',
            'recentEmployeeDocuments',
            'summary'
        ));
    }

    public function roles()
    {
        return view('dashboard.roles');
    }

    public function notifications()
    {
        $this->ensureMockDataExists();

        $user = Auth::user();

        $this->syncDeadlineReminderNotifications($user);
        $this->syncOverdueTaskNotifications($user);

        $canBroadcastNotifications = $user->isDirector() || $user->isLeader();
        $broadcastRecipientCount = User::where('is_active', true)->count();

        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($n) {
                $title = $this->cleanVietnameseText($n->title);
                $message = $this->cleanVietnameseText($n->message);

                return [
                    'id' => $n->id,
                    'task_id' => $n->task_id,
                    'type' => $this->notificationType($n, $title),
                    'title' => $title,
                    'desc' => $message,
                    'time' => $n->created_at ? $n->created_at->diffForHumans() : 'Vừa xong',
                    'read' => (bool) $n->is_read,
                ];
            });
        $notificationTypeCounts = [
            'task' => $notifications->where('type', 'task')->count(),
            'deadline' => $notifications->where('type', 'deadline')->count(),
        ];

        $notificationView = 'dashboard.notifications';

        if (request()->routeIs('employee.*')) {
            $notificationView = 'employee.notifications';
        } elseif (request()->routeIs('admin.*')) {
            $notificationView = 'admin.notifications';
        } elseif (request()->routeIs('manager.*')) {
            $notificationView = 'manager.notifications';
        }

        return view($notificationView, compact(
            'notifications',
            'unreadCount',
            'canBroadcastNotifications',
            'broadcastRecipientCount',
            'notificationTypeCounts'
        ));
    }

    public function broadcastNotification(Request $request)
    {
        $user = Auth::user();

        if (!$user->isDirector() && !$user->isLeader()) {
            return back()->with('error', 'Bạn không có quyền gửi thông báo toàn hệ thống.');
        }

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:1500'],
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề thông báo.',
            'title.max' => 'Tiêu đề không được vượt quá 160 ký tự.',
            'message.required' => 'Vui lòng nhập nội dung thông báo.',
            'message.max' => 'Nội dung không được vượt quá 1500 ký tự.',
        ]);

        $recipients = User::where('is_active', true)->pluck('id');

        if ($recipients->isEmpty()) {
            return back()->with('error', 'Chưa có nhân viên đang hoạt động để nhận thông báo.');
        }

        $now = now();
        $rows = $recipients->map(fn ($userId) => [
            'user_id' => $userId,
            'task_id' => null,
            'title' => $data['title'],
            'message' => $data['message'],
            'is_read' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ])->all();

        Notification::insert($rows);

        return redirect()
            ->route($this->roleRouteName($request, 'notifications'))
            ->with('success', 'Đã gửi thông báo đến ' . $recipients->count() . ' nhân viên.');
    }

    protected function syncDeadlineReminderNotifications(User $user): void
    {
        $upcomingTasks = Task::where(function ($query) use ($user) {
                $query->where('assigned_to', $user->id)
                    ->orWhereHas('assignees', fn ($assignees) => $assignees->where('users.id', $user->id));
            })
            ->whereNotIn('status', ['Hoàn thành'])
            ->whereNotNull('deadline')
            ->whereDate('deadline', '>=', now()->toDateString())
            ->whereDate('deadline', '<=', now()->addDays(2)->toDateString())
            ->get();

        foreach ($upcomingTasks as $task) {
            $deadline = Carbon::parse($task->deadline)->format('d/m/Y');

            Notification::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'title' => 'Nhắc deadline công việc sắp đến',
                ],
                [
                    'message' => 'WH-' . str_pad($task->id, 3, '0', STR_PAD_LEFT)
                        . ': ' . $this->cleanVietnameseText($task->task_name)
                        . ' - Hạn hoàn thành: ' . $deadline,
                    'is_read' => false,
                ]
            );
        }
    }

    protected function syncOverdueTaskNotifications(User $user): void
    {
        $overdueTasks = Task::where(function ($query) use ($user) {
                $query->where('assigned_to', $user->id)
                    ->orWhereHas('assignees', fn ($assignees) => $assignees->where('users.id', $user->id));
            })
            ->whereNotIn('status', [
                'Done',
                'Hoàn thành',
                'HoÃ n thÃ nh',
                'HoÃƒÂ n thÃƒÂ nh',
                'HoÃƒÆ’Ã‚Â n thÃƒÆ’Ã‚Â nh',
            ])
            ->whereNotNull('deadline')
            ->whereDate('deadline', '<', now()->toDateString())
            ->where(function ($query) {
                $query->whereNull('progress')->orWhere('progress', '<', 100);
            })
            ->get();

        foreach ($overdueTasks as $task) {
            $deadline = Carbon::parse($task->deadline)->format('d/m/Y');

            Notification::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'task_id' => $task->id,
                    'title' => 'Công việc đã quá hạn',
                ],
                [
                    'message' => 'WH-' . str_pad($task->id, 3, '0', STR_PAD_LEFT)
                        . ': ' . $this->cleanVietnameseText($task->task_name)
                        . ' - Đã quá hạn từ: ' . $deadline,
                    'is_read' => false,
                ]
            );
        }
    }

    protected function notificationType(Notification $notification, string $title): string
    {
        $titleLower = mb_strtolower($title);

        if (str_contains($titleLower, 'quá hạn') || str_contains($titleLower, 'quÃ¡ háº¡n')) {
            return 'overdue';
        }

        if (str_contains($titleLower, 'deadline') || str_contains($titleLower, 'hạn chót') || str_contains($titleLower, 'nhắc nhở')) {
            return 'deadline';
        }

        if (str_contains($titleLower, 'hoàn thành')) {
            return 'complete';
        }

        return $notification->task_id ? 'task' : 'general';
    }

    public function openNotification(Notification $notification)
    {
        abort_unless((int) $notification->user_id === (int) Auth::id(), 403);

        if (!$notification->is_read) {
            $notification->update(['is_read' => true]);
        }

        if ($notification->task_id) {
            $user = Auth::user();

            // Nhân viên → redirect về trang chi tiết task trong không gian nhân viên
            if (!$user->isDirector() && !$user->isLeader()) {
                return redirect()->route('employee.task.detail', $notification->task_id);
            }

            // Trưởng phòng → redirect về màn hình congviec (admin.layouts.index)
            if ($user->isLeader() && !$user->isDirector()) {
                return redirect()->route('congviec.chitiet', $notification->task_id);
            }

            // Giám đốc → redirect về congviec chi tiết
            return redirect()->route('congviec.chitiet', $notification->task_id);
        }

        $notificationIndexRoute = $this->roleRouteName(request(), 'notifications');

        return redirect()
            ->route($notificationIndexRoute)
            ->with('success', 'Thông báo đã được đánh dấu là đã đọc.');
    }

    public function markAllNotificationsAsRead()
    {
        $user = Auth::user();

        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $notificationIndexRoute = $this->roleRouteName(request(), 'notifications');

        return redirect()
            ->route($notificationIndexRoute)
            ->with('success', 'Đã đánh dấu tất cả thông báo là đã đọc.');
    }

    public function readNotification($id)
    {
        $notif = Notification::findOrFail($id);
        
        if ($notif->user_id === Auth::id()) {
            $notif->update(['is_read' => 1]);
        }

        if ($notif->task_id && !Auth::user()->isDirector() && !Auth::user()->isLeader()) {
            return redirect()->route('employee.task.detail', $notif->task_id);
        }

        if ($notif->task_id) {
            $user = Auth::user();
            if ($user && $user->role_id == 3) {
                return redirect()->route('employee.dashboard', ['task_id' => $notif->task_id]);
            }
            return redirect()->route('dashboard.tasks', ['task_id' => $notif->task_id]);
        }

        return redirect()->route($this->roleRouteName(request(), 'notifications'));
    }

    protected function roleRouteName(Request $request, string $page): string
    {
        if ($request->routeIs('employee.*')) {
            return 'employee.' . $page;
        }

        if ($request->routeIs('admin.*')) {
            return 'admin.' . $page;
        }

        if ($request->routeIs('manager.*')) {
            return 'manager.' . $page;
        }

        return 'dashboard.' . $page;
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
        $name = $this->cleanVietnameseText($name);
        $words = explode(' ', $name);
        $initials = '';

        if (count($words) >= 2) {
            $initials = mb_substr($words[count($words) - 2], 0, 1) . mb_substr($words[count($words) - 1], 0, 1);
        } elseif (count($words) == 1) {
            $initials = mb_substr($words[0], 0, 2);
        }

        return mb_strtoupper($initials);
    }
}
