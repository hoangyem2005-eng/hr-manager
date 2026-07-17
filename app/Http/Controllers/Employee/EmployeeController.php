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

        $allTasks = Task::where(function ($query) use ($user) {
                $query->where('assigned_to', $user->id)
                    ->orWhereHas('assignees', fn ($assignees) => $assignees->where('users.id', $user->id));
            })
            ->orderBy('updated_at', 'desc')
            ->get();

        $total = $allTasks->count();
        $done = $allTasks->where('status', 'Hoàn thành')->count();
        $doing = $allTasks->where('status', 'Đang làm')->count();
        $overdue = $allTasks->where('status', 'Quá hạn')->count();
        $pending = $allTasks->where('status', 'Chờ xử lý')->count();
        $completionRate = $total > 0 ? round(($done / $total) * 100) : 0;

        $tasksTodo = $allTasks->whereIn('status', ['Chờ xử lý', 'Todo'])->values();
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
            'status' => 'required|in:Chờ xử lý,Đang làm,Đang review,Hoàn thành',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

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
