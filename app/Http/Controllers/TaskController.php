<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\Notification;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TaskController extends Controller
{
    // ==================== DANH SÁCH CÔNG VIỆC ====================

    /**
     * Hiển thị danh sách công việc, có lọc theo trạng thái & phân trang.
     */
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');
        $user   = Auth::user();

        $tasks = Task::with(['assignee', 'creator', 'documents'])
            // Lọc theo trạng thái
            ->when($status, fn ($q) => $q->where('status', $status))
            // Lọc theo từ khóa tên công việc
            ->when($search, fn ($q) => $q->where('task_name', 'like', "%{$search}%"))
            // Trưởng phòng chỉ thấy công việc liên quan đến mình
            ->when($user->isLeader() && !$user->isDirector(), function ($q) use ($user) {
                $q->where(fn ($inner) => $inner
                    ->where('assigned_by', $user->id)
                    ->orWhere('assigned_to', $user->id)
                );
            })
            // Nhân viên chỉ thấy công việc được giao cho mình
            ->when(!$user->isLeader() && !$user->isDirector(), function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })
            ->orderByRaw("CASE WHEN status IN ('Hoàn thành', 'Done') THEN 1 ELSE 0 END ASC")
            ->orderByRaw("CASE WHEN deadline IS NULL THEN 1 ELSE 0 END ASC")
            ->orderBy('deadline', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.layouts.congviec.danhsach', compact('tasks', 'status', 'search'));
    }

    // ==================== TẠO MỚI CÔNG VIỆC ====================

    /**
     * Hiển thị form tạo mới công việc.
     */
    public function create()
    {
        $currentUser = Auth::user();
        if ($currentUser->isDirector()) {
            $users = User::with(['department', 'role'])
                ->where('role_id', '!=', User::ROLE_EMPLOYEE)
                ->where('id', '!=', $currentUser->id)
                ->orderBy('department_id')
                ->orderBy('name')
                ->get();
            $title = 'Giao công việc cho Trưởng phòng';
        } elseif ($currentUser->isLeader()) {
            $users = User::with(['department', 'role'])
                ->where('department_id', $currentUser->department_id)
                ->where('role_id', User::ROLE_EMPLOYEE)
                ->orderBy('name')
                ->get();
            $title = 'Giao công việc cho nhân viên';
        } else {
            $users = collect([$currentUser]);
            $title = 'Đề xuất công việc mới';
        }

        $task   = new Task(['status' => 'Todo', 'progress' => 0]);
        $action = route('congviec.luu');

        return view('admin.layouts.congviec.form', compact('users', 'task', 'action', 'title'));
    }

    /**
     * Lưu công việc mới vào DB + upload file đính kèm (nếu có).
     */
    public function store(Request $request)
    {
        $validated = $this->validateTask($request);
        $currentUser = Auth::user();

        if ($currentUser->isDirector() && !empty($validated['assigned_to'])) {
            $assignee = User::find($validated['assigned_to']);
            if ($assignee && $assignee->role_id === User::ROLE_EMPLOYEE) {
                return back()->withInput()->withErrors(['assigned_to' => 'Lãnh đạo không trực tiếp giao việc cho nhân viên, vui lòng giao cho Trưởng phòng/Quản lý đơn vị.']);
            }
        }

        $task = Task::create([
            'task_name'   => $validated['task_name'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'assigned_by' => Auth::id(),
            'deadline'    => $validated['deadline'] ?? null,
            'status'      => $validated['status'],
            'progress'    => $validated['progress'] ?? 0,
        ]);
        $task->assignees()->sync(array_filter([$validated['assigned_to'] ?? null]));

        // Xử lý upload file đính kèm (nhiều file)
        if ($request->hasFile('attachments')) {
            $this->handleFileUploads($request->file('attachments'), $task);
        }

        // Gửi thông báo cho người được giao việc
        $this->notifyAssignee($task, $currentUser->isDirector() ? 'Lãnh đạo giao công việc mới cho đơn vị' : 'Bạn vừa được giao công việc mới');

        return redirect()
            ->route('congviec.danhsach')
            ->with('thongbao', 'Giao công việc thành công.');
    }

    // ==================== CHI TIẾT CÔNG VIỆC ====================

    /**
     * Hiển thị chi tiết một công việc cùng tệp đính kèm.
     */
    public function show($id)
    {
        $task = Task::with(['assignee', 'assignees.department', 'creator', 'documents.uploader'])->findOrFail($id);
        $user = Auth::user();

        abort_unless($this->canViewTask($task, $user), 403);

        $task->setRelation(
            'documents',
            $task->documents->filter(fn (Document $document) => $this->canViewDocument($document, $user))->values()
        );

        if (!$user->isDirector() && !$user->isLeader()) {
            return view('employee.task-detail', compact('task'));
        }

        return view('admin.layouts.congviec.chitiet', compact('task'));
    }

    public function managerShow(Task $task)
    {
        $task->load(['assignee', 'assignees.department', 'creator', 'documents.uploader']);
        $user = Auth::user();

        abort_unless($user && $user->isLeader() && !$user->isDirector(), 403);
        abort_unless($this->canViewTask($task, $user), 403);

        $task->setRelation(
            'documents',
            $task->documents->filter(fn (Document $document) => $this->canViewDocument($document, $user))->values()
        );

        $myTeam = User::where('department_id', $user->department_id)
            ->where('role_id', User::ROLE_EMPLOYEE)
            ->orderBy('name')
            ->get();

        return view('manager.task-detail', compact('task', 'myTeam'));
    }

    // ==================== CHỈNH SỬA CÔNG VIỆC ====================

    /**
     * Hiển thị form chỉnh sửa công việc.
     */
    public function edit($id)
    {
        $task = Task::with('documents')->findOrFail($id);
        $currentUser = Auth::user();

        if ($currentUser->isDirector()) {
            $users = User::with(['department', 'role'])
                ->where('role_id', '!=', User::ROLE_EMPLOYEE)
                ->where('id', '!=', $currentUser->id)
                ->orderBy('department_id')
                ->orderBy('name')
                ->get();
            $title = 'Sửa công việc giao Trưởng phòng';
        } elseif ($currentUser->isLeader()) {
            $users = User::with(['department', 'role'])
                ->where('department_id', $currentUser->department_id)
                ->where('role_id', User::ROLE_EMPLOYEE)
                ->orderBy('name')
                ->get();
            $title = 'Sửa công việc nhân viên';
        } else {
            $users = collect([$currentUser]);
            $title = 'Sửa đề xuất công việc';
        }

        $action = route('congviec.capnhat', $task->id);

        return view('admin.layouts.congviec.form', compact('users', 'task', 'action', 'title'));
    }

    /**
     * Cập nhật thông tin công việc + thêm/xóa file đính kèm.
     */
    public function update(Request $request, $id)
    {
        $task        = Task::findOrFail($id);
        $oldAssignee = $task->assigned_to;
        $validated   = $this->validateTask($request);

        $task->update([
            'task_name'   => $validated['task_name'],
            'description' => $validated['description'] ?? null,
            'assigned_to' => $validated['assigned_to'] ?? null,
            'deadline'    => $validated['deadline'] ?? null,
            'status'      => $validated['status'],
            'progress'    => $validated['progress'] ?? $task->progress,
            'is_proposal' => false,
        ]);
        $task->assignees()->sync(array_filter([$validated['assigned_to'] ?? null]));

        // Thêm file đính kèm mới (nếu có)
        if ($request->hasFile('attachments')) {
            $this->handleFileUploads($request->file('attachments'), $task);
        }

        // Thông báo khi đổi người nhận việc
        if ($task->assigned_to && $task->assigned_to !== $oldAssignee) {
            $this->notifyAssignee($task, 'Công việc đã được chuyển giao cho bạn');
        }

        return redirect()
            ->route('congviec.danhsach')
            ->with('thongbao', 'Cập nhật công việc thành công.');
    }

    // ==================== XÓA CÔNG VIỆC ====================

    /**
     * Xóa công việc + tất cả file đính kèm khỏi storage.
     */
    public function destroy($id)
    {
        $task = Task::with('documents')->findOrFail($id);

        // Xóa từng file đính kèm khỏi disk trước khi xóa record DB
        foreach ($task->documents as $doc) {
            $this->deleteDocumentFile($doc);
        }

        $task->delete();
        $this->resetAutoIncrement('tasks');

        return redirect()
            ->route('congviec.danhsach')
            ->with('thongbao', 'Xóa công việc thành công.');
    }

    // ==================== CẬP NHẬT TIẾN ĐỘ (AJAX / FORM) ====================

    /**
     * Cập nhật nhanh trạng thái & % tiến độ công việc.
     * Dùng cho Kanban drag-drop hoặc quick-update form.
     */
    public function updateProgress(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'status'   => 'sometimes|in:Todo,In Progress,Done,Overdue',
            'progress' => 'sometimes|integer|min:0|max:100',
        ]);

        $data = [];

        if ($request->has('status')) {
            $data['status'] = $request->input('status');

            // Tự động đặt progress khi chuyển Done
            if ($data['status'] === 'Done') {
                $data['progress'] = 100;
            }
        }

        if ($request->has('progress')) {
            $data['progress'] = (int) $request->input('progress');

            // Tự suy luận trạng thái từ %
            if (!isset($data['status'])) {
                if ($data['progress'] === 0) {
                    $data['status'] = 'Todo';
                } elseif ($data['progress'] === 100) {
                    $data['status'] = 'Done';
                } elseif ($task->deadline && now()->toDateString() > $task->deadline && $data['progress'] < 100) {
                    $data['status'] = 'Overdue';
                } else {
                    $data['status'] = 'In Progress';
                }
            }
        }

        $task->update($data);

        if ($request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'status'   => $task->status,
                'progress' => $task->progress,
                'message'  => 'Cập nhật tiến độ thành công.',
            ]);
        }

        return redirect()
            ->back()
            ->with('thongbao', 'Cập nhật tiến độ thành công.');
    }

    // ==================== UPLOAD FILE (riêng lẻ cho task đã tồn tại) ====================

    /**
     * Upload thêm file đính kèm vào một task đã tồn tại.
     */
    public function uploadFile(Request $request, $taskId)
    {
        $task = Task::findOrFail($taskId);

        $request->validate([
            'attachments'   => 'required|array|max:5',
            'attachments.*' => [
                'required',
                'file',
                'max:20480',           // Tối đa 20 MB / file
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar,txt',
            ],
        ], [
            'attachments.required'    => 'Vui lòng chọn ít nhất một tệp.',
            'attachments.*.max'       => 'Mỗi tệp không được vượt quá 20 MB.',
            'attachments.*.mimes'     => 'Định dạng tệp không được hỗ trợ.',
        ]);

        $uploaded = $this->handleFileUploads($request->file('attachments'), $task);

        if ($request->expectsJson()) {
            return response()->json([
                'success'  => true,
                'count'    => count($uploaded),
                'files'    => $uploaded,
                'message'  => 'Tải lên ' . count($uploaded) . ' tệp thành công.',
            ]);
        }

        return redirect()
            ->back()
            ->with('thongbao', 'Tải lên tệp đính kèm thành công.');
    }

    // ==================== ĐÁNH GIÁ CÔNG VIỆC ====================

    /**
     * Nhân viên tiếp nhận công việc.
     */
    public function acceptTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $user = Auth::user();

        abort_unless(
            (int) $task->assigned_to === (int) $user->id
                || $task->assignees->contains('id', $user->id),
            403,
            'Bạn không có quyền thao tác trên công việc này.'
        );

        $task->update([
            'status' => 'Đang làm',
            'progress' => $task->progress > 0 ? $task->progress : 10,
        ]);

        Notification::where('user_id', $user->id)
            ->where('task_id', $task->id)
            ->update(['is_read' => true]);

        if ($task->assigned_by && (int)$task->assigned_by !== (int)$user->id) {
            try {
                Notification::create([
                    'user_id' => $task->assigned_by,
                    'task_id' => $task->id,
                    'title'   => 'Nhân viên đã nhận việc',
                    'message' => 'WH-' . str_pad($task->id, 3, '0', STR_PAD_LEFT)
                               . ': ' . $user->name . ' đã tiếp nhận công việc "' . $task->task_name . '".',
                    'is_read' => false,
                ]);
            } catch (\Exception $e) {
                \Log::warning('Lỗi tạo notification nhận việc: ' . $e->getMessage());
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Đã tiếp nhận công việc thành công.',
                'task' => [
                    'id' => $task->id,
                    'status' => $task->status,
                    'progress' => $task->progress,
                ],
            ]);
        }

        return redirect()->back()->with('success', "Bạn đã tiếp nhận công việc: {$task->task_name}");
    }

    /**
     * Nhân viên từ chối nhận công việc kèm lý do.
     */
    public function declineTask(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $user = Auth::user();

        abort_unless(
            (int) $task->assigned_to === (int) $user->id
                || $task->assignees->contains('id', $user->id),
            403,
            'Bạn không có quyền thao tác trên công việc này.'
        );

        $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        $reason = trim($request->input('reason', ''));

        $task->update([
            'status' => 'Từ chối',
            'employee_note' => $reason ? ('[Lý do không nhận việc]: ' . $reason) : $task->employee_note,
        ]);

        Notification::where('user_id', $user->id)
            ->where('task_id', $task->id)
            ->update(['is_read' => true]);

        if ($task->assigned_by && (int)$task->assigned_by !== (int)$user->id) {
            try {
                Notification::create([
                    'user_id' => $task->assigned_by,
                    'task_id' => $task->id,
                    'title'   => 'Nhân viên không nhận việc',
                    'message' => 'WH-' . str_pad($task->id, 3, '0', STR_PAD_LEFT)
                               . ': ' . $user->name . ' từ chối nhận việc "' . $task->task_name . '".'
                               . ($reason ? ' Lý do: ' . $reason : ''),
                    'is_read' => false,
                ]);
            } catch (\Exception $e) {
                \Log::warning('Lỗi tạo notification từ chối nhận việc: ' . $e->getMessage());
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Đã phản hồi không nhận công việc.',
                'task' => [
                    'id' => $task->id,
                    'status' => $task->status,
                ],
            ]);
        }

        return redirect()->back()->with('success', "Đã phản hồi không nhận công việc: {$task->task_name}");
    }

    /**
     * Nhân viên gửi ghi chú tự đánh giá / nộp việc cho cấp trên duyệt.
     */
    public function submitEmployeeNote(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $user = Auth::user();

        // Chỉ người được giao (hoặc cộng tác viên) mới được gửi
        abort_unless(
            (int) $task->assigned_to === (int) $user->id
                || $task->assignees->contains('id', $user->id),
            403,
            'Bạn không có quyền gửi đánh giá cho công việc này.'
        );

        $request->validate([
            'employee_note' => 'nullable|string|max:2000',
            'progress' => 'nullable|integer|min:0|max:100',
        ], [
            'employee_note.max' => 'Ghi chú / mô tả không được vượt quá 2000 ký tự.',
        ]);

        $note = $request->input('employee_note');
        $progress = $request->filled('progress') ? (int) $request->input('progress') : ($task->progress < 80 ? 100 : $task->progress);

        $task->update([
            'status' => 'Chờ đánh giá',
            'progress' => $progress,
            'employee_note' => $note,
        ]);

        if ($task->assigned_by && (int)$task->assigned_by !== (int)$user->id) {
            try {
                Notification::create([
                    'user_id' => $task->assigned_by,
                    'task_id' => $task->id,
                    'title'   => 'Nhân viên nộp việc chờ duyệt',
                    'message' => 'WH-' . str_pad($task->id, 3, '0', STR_PAD_LEFT)
                               . ': ' . $user->name . ' đã nộp công việc "' . $task->task_name . '" để chờ duyệt.'
                               . ($note ? ' Mô tả: ' . \Illuminate\Support\Str::limit($note, 80) : ''),
                    'is_read' => false,
                ]);
            } catch (\Exception $e) {
                \Log::warning('Lỗi tạo notification nộp việc: ' . $e->getMessage());
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'message' => 'Đã gửi ghi chú / nộp công việc cho cấp trên duyệt.',
                'task' => [
                    'id' => $task->id,
                    'status' => $task->status,
                    'progress' => $task->progress,
                    'employee_note' => $task->employee_note,
                ]
            ]);
        }

        return redirect()->back()->with('thongbao', 'Đã nộp công việc cho cấp trên duyệt.');
    }

    /**
     * Cấp trên chấm điểm (1-5 sao) + nhận xét + tùy chọn duyệt Hoàn thành / Trả lại.
     */
    public function submitEvaluation(Request $request, $id)
    {
        $task = Task::findOrFail($id);
        $user = Auth::user();

        // Chỉ Trưởng phòng hoặc Giám đốc mới được chấm
        abort_unless($user->isLeader() || $user->isDirector(), 403, 'Bạn không có quyền chấm điểm.');
        abort_unless($task->status === 'Chờ đánh giá', 422, 'Công việc chưa ở trạng thái Chờ đánh giá.');

        $validated = $request->validate([
            'evaluation_score'   => 'required|integer|min:1|max:5',
            'evaluation_comment' => 'nullable|string|max:2000',
            'action'             => 'required|in:approve,return',
        ], [
            'evaluation_score.required' => 'Vui lòng chọn số sao đánh giá.',
            'evaluation_score.min'      => 'Số sao tối thiểu là 1.',
            'evaluation_score.max'      => 'Số sao tối đa là 5.',
            'action.required'           => 'Vui lòng chọn hành động.',
            'action.in'                 => 'Hành động không hợp lệ.',
        ]);

        $newStatus = $validated['action'] === 'approve' ? 'Hoàn thành' : 'Đang làm';
        $newProgress = $validated['action'] === 'approve' ? 100 : $task->progress;

        $task->update([
            'evaluation_score'   => $validated['evaluation_score'],
            'evaluation_comment' => $validated['evaluation_comment'] ?? null,
            'evaluated_by'       => $user->id,
            'evaluated_at'       => now(),
            'status'             => $newStatus,
            'progress'           => $newProgress,
        ]);

        // Thông báo cho nhân viên
        if ($task->assigned_to && User::where('id', $task->assigned_to)->exists()) {
            $actionLabel = $validated['action'] === 'approve' ? 'được duyệt hoàn thành' : 'trả lại để tiếp tục';
            try {
                Notification::create([
                    'user_id' => $task->assigned_to,
                    'task_id' => $task->id,
                    'title'   => 'Kết quả đánh giá công việc',
                    'message' => 'WH-' . str_pad($task->id, 3, '0', STR_PAD_LEFT)
                               . ': ' . $task->task_name
                               . ' đã ' . $actionLabel
                               . ' (' . $validated['evaluation_score'] . '★ bởi ' . $user->name . ').',
                    'is_read' => false,
                ]);
            } catch (\Exception $e) {
                \Log::warning('Không thể tạo thông báo đánh giá cho task #' . $task->id . ': ' . $e->getMessage());
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'ok'      => true,
                'status'  => $newStatus,
                'message' => $validated['action'] === 'approve'
                    ? 'Đã duyệt hoàn thành công việc.'
                    : 'Đã trả lại công việc để tiếp tục.',
            ]);
        }

        $msg = $validated['action'] === 'approve'
            ? 'Đã duyệt hoàn thành công việc.'
            : 'Đã trả lại công việc để tiếp tục.';

        return redirect()->back()->with('thongbao', $msg);
    }

    // ==================== XÓA FILE ĐỘC LẬP ====================

    /**
     * Xóa một file đính kèm khỏi storage và DB.
     */
    public function deleteFile($documentId)
    {
        $doc = Document::findOrFail($documentId);

        // Chỉ người tải lên hoặc Trưởng phòng mới được xóa
        $user = Auth::user();
        if (!$this->canDeleteDocument($doc, $user)) {
            abort(403, 'Bạn không có quyền xóa tệp này.');
        }

        $this->deleteDocumentFile($doc);

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Đã xóa tệp thành công.']);
        }

        return redirect()->back()->with('thongbao', 'Đã xóa tệp đính kèm.');
    }

    // ==================== TẢI FILE ====================

    /**
     * Download / xem trực tiếp một file đính kèm.
     */
    public function downloadFile($documentId)
    {
        $doc = Document::findOrFail($documentId);

        // Kiểm tra file thực sự tồn tại trên disk
        if (!Storage::disk($doc->disk ?? 'public')->exists($doc->file_path)) {
            abort(404, 'Tệp không tìm thấy trên máy chủ.');
        }

        return Storage::disk($doc->disk ?? 'public')
            ->download($doc->file_path, $doc->file_name);
    }

    // ==================== PRIVATE HELPERS ====================

    /**
     * Xử lý upload nhiều file, lưu vào disk 'public' (symlink), ghi DB.
     *
     * Cơ chế Symlink:
     *   - File thực lưu tại: storage/app/public/tasks/{taskId}/{unique}.{ext}
     *   - Symlink public/storage → storage/app/public (php artisan storage:link)
     *   - URL công khai:       http://app/storage/tasks/{taskId}/{unique}.{ext}
     *
     * @param  \Illuminate\Http\UploadedFile[]  $files
     * @param  Task  $task
     * @return array  Danh sách thông tin file đã lưu
     */
    private function handleFileUploads(array $files, Task $task): array
    {
        $uploaded = [];

        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }

            $originalName = $file->getClientOriginalName();
            $extension    = strtolower($file->getClientOriginalExtension());
            $storedName   = Str::uuid() . '.' . $extension;

            // Thư mục theo task ID → dễ quản lý và dọn dẹp
            $directory    = 'tasks/' . $task->id;

            // Lưu vào disk 'public' (storage/app/public) – được symlink ra public/storage
            $storedPath   = $file->storeAs($directory, $storedName, 'public');

            // Ghi record vào bảng documents
            $doc = Document::create([
                'task_id'       => $task->id,
                'user_id'       => Auth::id(),
                'file_name'     => $originalName,
                'file_path'     => $storedPath,
                'file_type'     => $extension,
                'disk'          => 'public',
                'review_status' => $this->initialDocumentStatus(),
            ]);

            $uploaded[] = [
                'id'        => $doc->id,
                'name'      => $originalName,
                'path'      => $storedPath,
                'url'       => Storage::disk('public')->url($storedPath),
                'extension' => $extension,
            ];
        }

        return $uploaded;
    }

    /**
     * Xóa file vật lý khỏi disk và record khỏi DB.
     */
    private function deleteDocumentFile(Document $doc): void
    {
        $disk = $doc->disk ?? 'public';

        if (Storage::disk($disk)->exists($doc->file_path)) {
            Storage::disk($disk)->delete($doc->file_path);
        }

        $doc->delete();
    }

    /**
     * Validate dữ liệu form công việc.
     */
    private function validateTask(Request $request): array
    {
        return $request->validate([
            'task_name'   => 'required|string|max:255',
            'description' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'deadline'    => 'nullable|date|after_or_equal:today',
            'status'      => 'required|in:Todo,In Progress,Done,Overdue',
            'progress'    => 'nullable|integer|min:0|max:100',
            'attachments'   => 'nullable|array|max:5',
            'attachments.*' => [
                'file',
                'max:20480',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar,txt',
            ],
        ], [
            'task_name.required'    => 'Vui lòng nhập tên công việc.',
            'assigned_to.exists'    => 'Nhân viên nhận việc không hợp lệ.',
            'deadline.date'         => 'Hạn xử lý không hợp lệ.',
            'deadline.after_or_equal' => 'Hạn xử lý phải từ hôm nay trở đi.',
            'progress.integer'      => 'Tiến độ phải là số nguyên.',
            'progress.between'      => 'Tiến độ phải từ 0 đến 100.',
            'attachments.*.max'     => 'Mỗi tệp không được vượt quá 20 MB.',
            'attachments.*.mimes'   => 'Định dạng tệp không hỗ trợ.',
        ]);
    }

    /**
     * Gửi thông báo nội hệ thống cho người được giao việc.
     *
     * Luôn kiểm tra user tồn tại trước khi tạo notification để
     * tránh lỗi FK constraint khi user đã bị xóa khỏi DB.
     */
    private function notifyAssignee(Task $task, string $title = 'Thông báo công việc'): void
    {
        if (!$task->assigned_to) {
            return;
        }

        // Kiểm tra user thực sự tồn tại (tránh FK violation)
        if (!User::where('id', $task->assigned_to)->exists()) {
            return;
        }

        $creatorName = optional($task->creator)->name ?? 'Trưởng phòng';

        try {
            Notification::create([
                'user_id' => $task->assigned_to,
                'task_id' => $task->id,
                'title'   => $title,
                'message' => 'WH-' . str_pad($task->id, 3, '0', STR_PAD_LEFT)
                           . ': ' . $task->task_name
                           . ' (Người giao: ' . $creatorName . ')',
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            // Log lỗi nhưng không crash luồng chính
            \Log::warning('Không thể tạo thông báo cho task #' . $task->id . ': ' . $e->getMessage());
        }
    }

    private function canViewTask(Task $task, User $user): bool
    {
        if ($user->isDirector()) {
            return true;
        }

        if ((int) $task->assigned_to === (int) $user->id
            || (int) $task->assigned_by === (int) $user->id
            || $task->assignees->contains('id', $user->id)) {
            return true;
        }

        if ($user->isLeader()) {
            return (int) optional($task->assignee)->department_id === (int) $user->department_id
                || $task->assignees->contains(fn (User $assignee) => (int) $assignee->department_id === (int) $user->department_id);
        }

        return false;
    }

    private function canViewDocument(Document $document, User $user): bool
    {
        if ((int) $document->user_id === (int) $user->id) {
            return true;
        }

        if ($user->isLeader() || (int) $user->role_id === User::ROLE_MANAGER) {
            $document->loadMissing(['task.assignee', 'uploader']);
            $document->task?->loadMissing('assignees');

            return (int) optional($document->task?->assignee)->department_id === (int) $user->department_id
                || optional($document->task)->assignees?->contains(fn (User $assignee) => (int) $assignee->department_id === (int) $user->department_id)
                || (int) optional($document->uploader)->department_id === (int) $user->department_id
                || (int) optional($document->task)->assigned_by === (int) $user->id
                || (int) optional($document->task)->assigned_to === (int) $user->id
                || optional($document->task)->assignees?->contains('id', $user->id);
        }

        if ($user->isDirector()) {
            return $document->review_status === Document::STATUS_DIRECTOR_VISIBLE;
        }

        return false;
    }

    private function canDeleteDocument(Document $document, User $user): bool
    {
        if ((int) $document->user_id === (int) $user->id) {
            return true;
        }

        if ($user->isDirector()) {
            return $this->canViewDocument($document, $user);
        }

        return $user->isLeader();
    }

    private function initialDocumentStatus(): string
    {
        $user = Auth::user();

        return $user && $user->isEmployee()
            ? Document::STATUS_MANAGER_REVIEW
            : Document::STATUS_DIRECTOR_VISIBLE;
    }


    /**
     * Reset AUTO_INCREMENT sau khi xóa record để tránh ID bị nhảy số.
     */
    private function resetAutoIncrement(string $table): void
    {
        $nextId = ((int) DB::table($table)->max('id')) + 1;
        DB::statement('ALTER TABLE ' . $table . ' AUTO_INCREMENT = ' . max(1, $nextId));
    }
}
