<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Notification;
use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            $user = Auth::user();

            if (!$user) {
                return redirect()->route('login');
            }

            // Cho phép nhận việc / từ chối việc kể cả khi là Trưởng phòng (được Giám đốc giao việc)
            if ($request->routeIs('employee.task.accept', 'employee.task.decline')) {
                return $next($request);
            }

            if ($user->isDirector()) {
                return redirect()->route('admin.dashboard');
            }

            if ($user->isLeader()) {
                return redirect()->route('manager.dashboard');
            }

            return $next($request);
        });
    }

    public function dashboard()
    {
        $user = Auth::user();

        $allTasks = Task::where('is_proposal', false)
            ->where(function ($query) use ($user) {
                $query->where('assigned_to', $user->id)
                    ->orWhereHas('assignees', fn ($assignees) => $assignees->where('users.id', $user->id));
            })
            ->orderByRaw("CASE WHEN status IN ('Hoàn thành', 'Done', 'hoàn thành') THEN 1 ELSE 0 END ASC")
            ->orderByRaw("CASE WHEN deadline IS NULL THEN 1 ELSE 0 END ASC")
            ->orderBy('deadline', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Chuẩn hóa trạng thái công việc từ database
        $replacements = [
            'hoÃ n thÃ nh' => 'hoàn thành',
            'hoÃ nthÃ nh' => 'hoàn thành',
            'Ä‘ang lÃ m' => 'đang làm',
            'Ä‘anglÃ m' => 'đang làm',
            'chá» xá»­ lÃ½' => 'chờ xử lý',
            'chá»xá»­lÃ½' => 'chờ xử lý',
            'đang lÃ m' => 'đang làm',
            'chờ xá»­ lÃ½' => 'chờ xử lý',
            'Ä‘ang review' => 'chờ đánh giá',
            'quÃ¡ háº¡n' => 'quá hạn',
        ];

        $normalizeStatus = function($statusStr) use ($replacements) {
            $status = trim($statusStr ?? '');
            $status = str_replace(array_keys($replacements), array_values($replacements), $status);
            $statusLower = mb_strtolower($status);
            if (in_array($statusLower, ['done', 'hoàn thành'], true)) {
                return 'Hoàn thành';
            }
            if (in_array($statusLower, ['in progress', 'inprogress', 'in_progress', 'đang làm'], true)) {
                return 'Đang làm';
            }
            if (in_array($statusLower, ['todo', 'chờ xử lý'], true)) {
                return 'Chờ xử lý';
            }
            if (in_array($statusLower, ['đang review', 'review', 'chờ đánh giá'], true)) {
                return 'Chờ đánh giá';
            }
            if ($statusLower === 'quá hạn') {
                return 'Quá hạn';
            }
            return $status;
        };

        foreach ($allTasks as $task) {
            if (in_array($task->status, ['Chờ xử lý', 'Todo', 'chờ xử lý'], true)) {
                if ($task->checkAndUpdateAcceptanceTimeout()) {
                    $task->refresh();
                }
            }
            $task->status = $normalizeStatus($task->status);
        }

        $total = $allTasks->count();
        $done = $allTasks->where('status', 'Hoàn thành')->count();
        $doing = $allTasks->where('status', 'Đang làm')->count();
        $overdue = $allTasks->where('status', 'Quá hạn')->count();
        $pending = $allTasks->where('status', 'Chờ xử lý')->count();
        $completionRate = $total > 0 ? round(($done / $total) * 100) : 0;

        $tasksTodo = $allTasks->where('status', 'Chờ xử lý')->values();
        $tasksDoing = $allTasks->where('status', 'Đang làm')->values();
        $tasksDone = $allTasks->where('status', 'Hoàn thành')->values();
        $tasksOverdue = $allTasks->where('status', 'Quá hạn')->values();

        $mappedTasks = $allTasks->map(fn ($task) => [
            'id' => $task->id,
            'code' => 'WH-' . str_pad($task->id, 3, '0', STR_PAD_LEFT),
            'name' => $task->task_name,
            'description' => $task->description,
            'status' => $task->status,
            'progress' => $task->progress ?? 0,
            'deadline' => $task->deadline ? Carbon::parse($task->deadline)->format('d/m/Y') : '—',
            'deadline_raw' => $task->deadline,
            'acceptance_deadline' => $task->acceptance_deadline ? $task->acceptance_deadline->format('H:i d/m/Y') : null,
            'is_acceptance_expired' => $task->is_acceptance_expired,
            'is_overdue' => $task->status === 'Quá hạn',
            'days_left' => $task->deadline
                ? (int) Carbon::now()->diffInDays(Carbon::parse($task->deadline), false)
                : null,
        ]);

        $notifications = Notification::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();

        $unreadCount = Notification::where('user_id', $user->id)
            ->where('is_read', 0)
            ->count();

        return view('employee.dashboard', compact(
            'total',
            'done',
            'doing',
            'overdue',
            'pending',
            'completionRate',
            'mappedTasks',
            'tasksTodo',
            'tasksDoing',
            'tasksDone',
            'tasksOverdue',
            'notifications',
            'unreadCount'
        ));
    }

    public function taskDetail($id)
    {
        $user = Auth::user();

        $task = Task::with(['assignee', 'assignees', 'creator', 'documents.uploader'])
            ->where(function ($query) use ($user) {
                $query->where('assigned_to', $user->id)
                    ->orWhere('assigned_by', $user->id)
                    ->orWhereHas('assignees', fn ($assignees) => $assignees->where('users.id', $user->id));
            })
            ->findOrFail($id);

        return view('employee.task-detail', compact('task'));
    }

    public function updateProgress(Request $request, $id)
    {
        $user = Auth::user();

        $task = Task::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('assigned_to', $user->id)
                    ->orWhereHas('assignees', fn ($assignees) => $assignees->where('users.id', $user->id));
            })
            ->firstOrFail();

        $request->validate([
            'status' => 'required|in:Chờ xử lý,Đang làm,Chờ đánh giá,Hoàn thành',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        $isChangingToOrFromDone = ($request->status === 'Hoàn thành' || $task->status === 'Hoàn thành');
        if ($isChangingToOrFromDone && !$user->isDirector() && !$user->isLeader()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'ok' => false,
                    'message' => 'Chỉ có cấp Quản lý hoặc Ban Giám đốc mới được hoàn thành công việc.'
                ], 403);
            }
            return redirect()->back()->with('error', 'Chỉ có cấp Quản lý hoặc Ban Giám đốc mới được hoàn thành công việc.');
        }

        $task->update([
            'status' => $request->status,
            'progress' => $request->progress ?? $task->progress,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'ok' => true,
                'status' => $task->status,
                'progress' => $task->progress,
            ]);
        }

        return redirect()
            ->route('employee.dashboard')
            ->with('success', "Đã cập nhật tiến độ: {$task->task_name}");
    }

    /**
     * Nhân viên bấm nhận công việc.
     */
    public function acceptTask(Request $request, $id)
    {
        $user = Auth::user();

        $task = Task::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('assigned_to', $user->id)
                    ->orWhereHas('assignees', fn ($assignees) => $assignees->where('users.id', $user->id));
            })
            ->firstOrFail();

        $task->update([
            'status' => 'Đang làm',
            'progress' => $task->progress > 0 ? $task->progress : 10,
        ]);

        // Đánh dấu các thông báo liên quan là đã đọc
        Notification::where('user_id', $user->id)
            ->where('task_id', $task->id)
            ->update(['is_read' => true]);

        // Gửi thông báo cho cấp trên giao việc
        if ($task->assigned_by && $task->assigned_by !== $user->id) {
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
        $user = Auth::user();

        $task = Task::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('assigned_to', $user->id)
                    ->orWhereHas('assignees', fn ($assignees) => $assignees->where('users.id', $user->id));
            })
            ->firstOrFail();

        $request->validate([
            'reason' => 'nullable|string|max:1000',
        ]);

        $reason = trim($request->input('reason', ''));

        $task->update([
            'status' => 'Từ chối',
            'employee_note' => $reason ? ('[Lý do không nhận việc]: ' . $reason) : $task->employee_note,
        ]);

        // Đánh dấu thông báo là đã đọc
        Notification::where('user_id', $user->id)
            ->where('task_id', $task->id)
            ->update(['is_read' => true]);

        // Gửi thông báo cho cấp trên giao việc
        if ($task->assigned_by && $task->assigned_by !== $user->id) {
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
     * Nhân viên nộp công việc cho cấp trên duyệt kèm đoạn mô tả / báo cáo.
     */
    public function submitForReview(Request $request, $id)
    {
        $user = Auth::user();

        $task = Task::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('assigned_to', $user->id)
                    ->orWhereHas('assignees', fn ($assignees) => $assignees->where('users.id', $user->id));
            })
            ->firstOrFail();

        $request->validate([
            'employee_note' => 'nullable|string|max:2000',
            'progress' => 'nullable|integer|min:0|max:100',
        ], [
            'employee_note.max' => 'Đoạn mô tả không được vượt quá 2000 ký tự.',
        ]);

        $note = $request->input('employee_note');
        $progress = $request->filled('progress') ? (int) $request->input('progress') : 100;

        $task->update([
            'status' => 'Chờ đánh giá',
            'progress' => $progress,
            'employee_note' => $note,
        ]);

        // Gửi thông báo cho cấp trên giao việc
        if ($task->assigned_by && $task->assigned_by !== $user->id) {
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
                'message' => 'Đã nộp công việc cho cấp trên duyệt thành công.',
                'task' => [
                    'id' => $task->id,
                    'status' => $task->status,
                    'progress' => $task->progress,
                    'employee_note' => $task->employee_note,
                ],
            ]);
        }

        return redirect()->back()->with('success', 'Đã nộp công việc cho cấp trên duyệt thành công.');
    }

    public function uploadFile(Request $request, $id)
    {
        $user = Auth::user();

        $task = Task::where('id', $id)
            ->where(function ($query) use ($user) {
                $query->where('assigned_to', $user->id)
                    ->orWhere('assigned_by', $user->id)
                    ->orWhereHas('assignees', fn ($assignees) => $assignees->where('users.id', $user->id));
            })
            ->firstOrFail();

        $request->validate([
            'attachments' => 'required|array|max:5',
            'attachments.*' => [
                'required',
                'file',
                'max:20480',
                'mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar,txt',
            ],
        ], [
            'attachments.required' => 'Vui lòng chọn ít nhất một tệp.',
            'attachments.*.max' => 'Mỗi tệp không được vượt quá 20 MB.',
            'attachments.*.mimes' => 'Định dạng không được hỗ trợ. Cho phép: PDF, Word, Excel, ảnh, ZIP...',
        ]);

        foreach ($request->file('attachments') as $file) {
            $stored = $file->store("tasks/{$task->id}", 'public');

            Document::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $stored,
                'file_type' => strtolower($file->getClientOriginalExtension()),
                'disk' => 'public',
                'review_status' => Document::STATUS_MANAGER_REVIEW,
            ]);
        }

        if ($request->input('redirect_to') === 'employee.tasks') {
            return redirect()
                ->route('employee.tasks')
                ->with('success', 'Tải lên file đính kèm thành công.');
        }

        return redirect()
            ->route('employee.task.detail', $id)
            ->with('success', 'Tải lên file đính kèm thành công.');
    }

    public function deleteFile($documentId)
    {
        $user = Auth::user();

        $document = Document::where('id', $documentId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        Storage::disk($document->disk ?? 'public')->delete($document->file_path);
        $taskId = $document->task_id;
        $document->delete();

        return redirect()
            ->route('employee.task.detail', $taskId)
            ->with('success', 'Đã xóa file đính kèm.');
    }
}
