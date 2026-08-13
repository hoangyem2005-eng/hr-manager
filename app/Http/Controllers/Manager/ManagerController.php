<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Document;
use App\Models\User;
use App\Models\Task;
use App\Models\Department;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user || !$user->isLeader()) {
                // Nếu là Giám đốc, redirect sang admin dashboard
                if ($user && $user->isDirector()) {
                    return redirect()->route('admin.dashboard');
                }
                return redirect()->route('employee.dashboard')
                    ->with('error', 'Trang này chỉ dành cho Trưởng phòng.');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $user       = Auth::user();
        $deptId     = $user->department_id;

        // Chỉ lấy nhân viên trong phòng mình
        $myTeam = User::with('role')
            ->where('department_id', $deptId)
            ->where('id', '!=', $user->id) // bỏ bản thân
            ->get()
            ->map(function ($u) {
                $memberTasks = fn () => Task::where(function ($query) use ($u) {
                    $query->where('assigned_to', $u->id)
                        ->orWhereHas('assignees', fn ($assignees) => $assignees->where('users.id', $u->id));
                });
                $total  = $memberTasks()->count();
                $done   = $memberTasks()->whereIn('status', ['Hoàn thành', 'Done', 'hoÃ n thÃ nh', 'hoÃ nthÃ nh'])->count();
                $doing  = $memberTasks()->whereIn('status', ['Đang làm', 'In Progress', 'inprogress', 'in_progress', 'Ä‘ang lÃ m', 'Ä‘anglÃ m', 'đang lÃ m', 'Đang review', 'Review', 'Ä‘ang review'])->count();
                $overdue = $memberTasks()->whereIn('status', ['Quá hạn', 'Overdue', 'quÃ¡ háº¡n'])->count();
                return [
                    'id'         => $u->id,
                    'name'       => $u->name,
                    'email'      => $u->email,
                    'role_name'  => $u->role->name ?? 'Nhân viên',
                    'avatar'     => $this->getInitials($u->name),
                    'total'      => $total,
                    'done'       => $done,
                    'doing'      => $doing,
                    'overdue'    => $overdue,
                    'rate'       => $total > 0 ? round(($done / $total) * 100) : 0,
                ];
            });

        // Công việc trong phòng mình đã giao (assigned_by = tôi)
        $myAssignedTasks = Task::with(['assignee', 'assignees', 'documents'])
            ->where('assigned_by', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn($t) => [
                'id'       => $t->id,
                'code'     => 'WH-' . str_pad($t->id, 3, '0', STR_PAD_LEFT),
                'name'     => $t->task_name,
                'assignee' => ($t->assignees->isNotEmpty() ? $t->assignees->pluck('name')->join(', ') : ($t->assignee->name ?? '—')),
                'status'   => $t->status,
                'deadline' => $t->deadline ? Carbon::parse($t->deadline)->format('d/m/Y') : '—',
                'progress' => $t->progress ?? 0,
                'documents_count' => $t->documents->count(),
            ]);

        $incomingTasks = Task::with('assigner')
            ->where('assigned_to', $user->id)
            ->where('assigned_by', '!=', $user->id)
            ->whereNotIn('status', ['Hoàn thành'])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->map(fn($t) => [
                'id' => $t->id,
                'code' => 'WH-' . str_pad($t->id, 3, '0', STR_PAD_LEFT),
                'name' => $t->task_name,
                'assigner' => $t->assigner->name ?? 'Giám đốc',
                'status' => $t->status,
                'deadline' => $t->deadline ? Carbon::parse($t->deadline)->format('d/m/Y') : '—',
                'description' => $t->description,
            ]);

        // Công việc được giao cho nhân viên trong phòng
        $teamMemberIds = User::where('department_id', $deptId)->pluck('id');
        $teamTasks = fn () => Task::where(function ($query) use ($teamMemberIds) {
            $query->whereIn('assigned_to', $teamMemberIds)
                ->orWhereHas('assignees', fn ($assignees) => $assignees->whereIn('users.id', $teamMemberIds));
        });
        $pendingCount  = $teamTasks()->whereIn('status', ['Chờ xử lý', 'Todo', 'chá»  xá»­ lÃ½', 'chá» xá»­lÃ½', 'chờ xá»­ lÃ½'])->count();
        $doingCount    = $teamTasks()->whereIn('status', ['Đang làm', 'In Progress', 'inprogress', 'in_progress', 'Ä‘ang lÃ m', 'Ä‘anglÃ m', 'đang lÃ m', 'Đang review', 'Review', 'Ä‘ang review'])->count();
        $doneCount     = $teamTasks()->whereIn('status', ['Hoàn thành', 'Done', 'hoÃ n thÃ nh', 'hoÃ nthÃ nh'])->count();
        $overdueCount  = $teamTasks()->whereIn('status', ['Quá hạn', 'Overdue', 'quÃ¡ háº¡n'])->count();

        $department = $user->department;
        $allTeamMembers = User::where('department_id', $deptId)->get(); // cho form giao việc
        $roles = Role::all();

        return view('manager.dashboard', compact(
            'myTeam', 'myAssignedTasks', 'incomingTasks',
            'pendingCount', 'doingCount', 'doneCount', 'overdueCount',
            'department', 'allTeamMembers', 'roles'
        ));
    }

    public function members(Request $request)
    {
        $manager = Auth::user();
        $department = $manager->department;
        $search = trim((string) $request->query('search', ''));

        $query = User::with('role')
            ->where('department_id', $manager->department_id)
            ->where('id', '!=', $manager->id);

        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $teamMembers = $query
            ->orderBy('role_id')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();

        $teamMembers->setCollection($teamMembers->getCollection()->map(function (User $member) {
            $total = Task::where('assigned_to', $member->id)->count();
            $done = Task::where('assigned_to', $member->id)->whereIn('status', ['Hoàn thành', 'Done', 'hoÃ n thÃ nh', 'hoÃ nthÃ nh'])->count();
            $doing = Task::where('assigned_to', $member->id)->whereIn('status', ['Đang làm', 'In Progress', 'inprogress', 'in_progress', 'Ä‘ang lÃ m', 'Ä‘anglÃ m', 'đang lÃ m', 'Đang review', 'Review', 'Ä‘ang review'])->count();
            $overdue = Task::where('assigned_to', $member->id)
                ->whereNotIn('status', ['Hoàn thành', 'Done', 'hoÃ n thÃ nh', 'hoÃ nthÃ nh'])
                ->whereNotNull('deadline')
                ->whereDate('deadline', '<', now()->toDateString())
                ->count();

            return [
                'id' => $member->id,
                'code' => 'NV' . str_pad((string) $member->id, 3, '0', STR_PAD_LEFT),
                'name' => $member->name,
                'email' => $member->email,
                'role_name' => $member->role_display_name,
                'avatar' => $this->getInitials($member->name),
                'total' => $total,
                'done' => $done,
                'doing' => $doing,
                'overdue' => $overdue,
                'rate' => $total > 0 ? round(($done / $total) * 100) : 0,
                'joined' => $member->created_at ? $member->created_at->format('d/m/Y') : '—',
                'status' => $member->is_active ? 'active' : 'inactive',
            ];
        }));

        $visibleMembers = collect($teamMembers->items());
        $summary = [
            'visible' => $visibleMembers->count(),
            'total' => $teamMembers->total(),
            'active' => $visibleMembers->where('status', 'active')->count(),
            'tasks' => $visibleMembers->sum('total'),
            'overdue' => $visibleMembers->sum('overdue'),
        ];

        return view('manager.members', compact('department', 'teamMembers', 'search', 'summary'));
    }

    /**
     * Thêm nhân viên mới – department_id bị LOCK theo phòng của Trưởng phòng.
     */
    public function storeEmployee(Request $request)
    {
        $manager = Auth::user();

        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|min:6',
        ]);

        $newUser = User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role_id'       => User::ROLE_EMPLOYEE,    // Luôn là Nhân viên
            'department_id' => $manager->department_id, // Bị lock theo phòng của Trưởng phòng
        ]);

        // Backfill thông báo chung (broadcast) mà user mới chưa nhận được
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
                    'created_at' => $n->created_at,
                    'updated_at' => $now,
                ])->all();

                Notification::insert($rows);
            }
        }

        return redirect()->route('manager.dashboard')
            ->with('success', "Đã thêm nhân viên {$request->name} vào phòng của bạn!");
    }


    /**
     * Giao công việc cho cấp dưới trong phòng.
     */
    public function assignTask(Request $request)
    {
        $manager = Auth::user();
        $deptId  = $manager->department_id;
        $assignedToInput = $request->input('assigned_to', []);
        $assignedToIds = collect(is_array($assignedToInput) ? $assignedToInput : [$assignedToInput])
            ->filter(fn ($id) => $id !== null && $id !== '')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values()
            ->all();

        $request->merge(['assigned_to' => $assignedToIds]);

        $request->validate([
            'task_name'   => 'required|string|max:255',
            'assigned_to' => 'required|array|min:1',
            'assigned_to.*' => 'integer|exists:users,id',
            'deadline'    => 'required|date',
            'description' => 'nullable|string',
            'status'      => 'nullable|string',
            'attachments.*' => 'nullable|file|max:20480', // Max 20MB per file
        ]);

        // Verify: người được giao phải trong phòng của mình
        $assignees = User::whereIn('id', $assignedToIds)->get();
        $invalidAssignee = $assignees->first(fn ($assignee) => (int) $assignee->department_id !== (int) $deptId);
        if ($invalidAssignee) {
            return back()->with('error', 'Bạn chỉ có thể giao việc cho nhân viên trong phòng của mình!');
        }

        $task = Task::create([
            'task_name'   => $request->task_name,
            'description' => $request->description,
            'assigned_by' => $manager->id,
            'assigned_to' => $assignedToIds[0],
            'deadline'    => $request->deadline,
            'status'      => $request->status ?? 'Chờ xử lý',
            'progress'    => 0,
        ]);

        $task->assignees()->sync($assignedToIds);
        $createdTasks = [$task];

        foreach ($assignedToIds as $assignedTo) {
            Notification::create([
                'user_id' => $assignedTo,
                'task_id' => $task->id,
                'title' => 'Bạn vừa được giao công việc mới',
                'message' => 'WH-' . str_pad($task->id, 3, '0', STR_PAD_LEFT) . ': ' . $task->task_name,
                'is_read' => false,
            ]);
        }

        // Xử lý tệp đính kèm
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $extension    = strtolower($file->getClientOriginalExtension());
                    
                    foreach ($createdTasks as $task) {
                        $storedName   = time() . '_' . uniqid() . '.' . $extension;
                        $directory    = 'tasks/' . $task->id;
                        $storedPath   = $file->storeAs($directory, $storedName, 'public');

                        \App\Models\Document::create([
                            'task_id'   => $task->id,
                            'user_id'   => Auth::id(),
                            'file_name' => $originalName,
                            'file_path' => $storedPath,
                            'file_type' => $extension,
                            'disk'      => 'public',
                        ]);
                    }
                }
            }
        }

        // Bắn event thông báo
        foreach ($createdTasks as $task) {
            event(new \App\Events\TaskCreated($task));
        }

        return redirect()->route('manager.dashboard')
            ->with('success', 'Đã giao một công việc chung cho ' . count($assignedToIds) . ' nhân viên!');
    }

    public function delegateIncomingTask(Request $request, Task $task)
    {
        $manager = Auth::user();

        if ((int) $task->assigned_to !== (int) $manager->id) {
            return back()->with('error', 'Bạn chỉ có thể phân công công việc đang được giao cho mình.');
        }

        $request->validate([
            'assigned_to' => 'required|exists:users,id',
        ]);

        $assignee = User::findOrFail($request->assigned_to);

        if ((int) $assignee->department_id !== (int) $manager->department_id || !$assignee->isEmployee()) {
            return back()->with('error', 'Bạn chỉ có thể phân công cho nhân viên trong phòng của mình.');
        }

        $task->update([
            'assigned_by' => $manager->id,
            'assigned_to' => $assignee->id,
            'status' => 'Chờ xử lý',
            'progress' => 0,
        ]);
        $task->assignees()->sync([$assignee->id]);

        Notification::create([
            'user_id' => $assignee->id,
            'task_id' => $task->id,
            'title' => 'Bạn vừa được phân công công việc mới',
            'message' => 'WH-' . str_pad($task->id, 3, '0', STR_PAD_LEFT) . ': ' . $task->task_name
                . ' (Người giao: ' . $manager->name . ')',
            'is_read' => false,
        ]);

        // Xử lý tệp đính kèm
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file->isValid()) {
                    $originalName = $file->getClientOriginalName();
                    $extension    = strtolower($file->getClientOriginalExtension());
                    $storedName   = time() . '_' . uniqid() . '.' . $extension;
                    $directory    = 'tasks/' . $task->id;

                    $storedPath   = $file->storeAs($directory, $storedName, 'public');

                    \App\Models\Document::create([
                        'task_id'   => $task->id,
                        'user_id'   => Auth::id(),
                        'file_name' => $originalName,
                        'file_path' => $storedPath,
                        'file_type' => $extension,
                        'disk'      => 'public',
                    ]);
                }
            }
        }

        // Bắn event thông báo
        event(new \App\Events\TaskCreated($task));

        return redirect()->route('manager.dashboard')
            ->with('success', "Đã phân công {$task->task_name} cho {$assignee->name}.");
    }

    public function forwardDocumentToDirector(Document $document)
    {
        $manager = Auth::user();
        $document->loadMissing(['task.assignee', 'task.assignees', 'uploader']);

        if (!$this->canManageDocument($document, $manager)) {
            abort(403, 'Bạn không có quyền chuyển file này lên Giám đốc.');
        }

        $document->update([
            'review_status' => Document::STATUS_DIRECTOR_VISIBLE,
            'forwarded_by' => $manager->id,
            'forwarded_at' => now(),
        ]);

        $directors = User::where('role_id', User::ROLE_ADMIN)->get();
        foreach ($directors as $director) {
            Notification::create([
                'user_id' => $director->id,
                'task_id' => $document->task_id,
                'title' => 'Trưởng phòng đã chuyển file lên Giám đốc',
                'message' => 'WH-' . str_pad($document->task_id, 3, '0', STR_PAD_LEFT)
                    . ': ' . ($document->task->task_name ?? 'Công việc')
                    . ' - ' . $document->file_name,
                'is_read' => false,
            ]);
        }

        return back()->with('success', 'Đã chuyển file lên Giám đốc.');
    }

    // ===================== Helper =====================
    private function canManageDocument(Document $document, User $manager): bool
    {
        return (int) optional($document->task?->assignee)->department_id === (int) $manager->department_id
            || optional($document->task)->assignees?->contains(fn (User $assignee) => (int) $assignee->department_id === (int) $manager->department_id)
            || (int) optional($document->uploader)->department_id === (int) $manager->department_id
            || (int) optional($document->task)->assigned_by === (int) $manager->id
            || (int) optional($document->task)->assigned_to === (int) $manager->id;
    }

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
