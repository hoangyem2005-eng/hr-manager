<?php

namespace App\Http\Controllers\Employee;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

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
            // Giám đốc vào trang nhân viên → redirect về admin
            if ($user->isDirector()) {
                return redirect()->route('admin.dashboard');
            }
            // Trưởng phòng vào trang nhân viên → redirect về manager
            if ($user->isLeader()) {
                return redirect()->route('manager.dashboard');
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $user = Auth::user();

        // Tất cả task được giao cho nhân viên này
        $allTasks = Task::where('assigned_to', $user->id)
            ->orderBy('updated_at', 'desc')
            ->get();

        $total   = $allTasks->count();
        $done    = $allTasks->where('status', 'Hoàn thành')->count();
        $doing   = $allTasks->where('status', 'Đang làm')->count();
        $overdue = $allTasks->where('status', 'Quá hạn')->count();
        $pending = $allTasks->where('status', 'Chờ xử lý')->count();

        $completionRate = $total > 0 ? round(($done / $total) * 100) : 0;

        // Nhóm task theo trạng thái để hiển thị
        $tasksTodo    = $allTasks->whereIn('status', ['Chờ xử lý', 'Todo'])->values();
        $tasksDoing   = $allTasks->where('status', 'Đang làm')->values();
        $tasksDone    = $allTasks->where('status', 'Hoàn thành')->values();
        $tasksOverdue = $allTasks->where('status', 'Quá hạn')->values();

        // Map chi tiết task
        $mappedTasks = $allTasks->map(fn($t) => [
            'id'         => $t->id,
            'code'       => 'WH-' . str_pad($t->id, 3, '0', STR_PAD_LEFT),
            'name'       => $t->task_name,
            'description'=> $t->description,
            'status'     => $t->status,
            'progress'   => $t->progress ?? 0,
            'deadline'   => $t->deadline ? Carbon::parse($t->deadline)->format('d/m/Y') : '—',
            'deadline_raw'  => $t->deadline,
            'is_overdue' => $t->status === 'Quá hạn',
            'days_left'  => $t->deadline
                ? (int) Carbon::now()->diffInDays(Carbon::parse($t->deadline), false)
                : null,
        ]);

        // Thông báo chưa đọc
        $notifications = Notification::where('user_id', $user->id)
            ->where('is_read', 0)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $unreadCount = $notifications->count();

        return view('employee.dashboard', compact(
            'total', 'done', 'doing', 'overdue', 'pending',
            'completionRate', 'mappedTasks',
            'tasksTodo', 'tasksDoing', 'tasksDone', 'tasksOverdue',
            'notifications', 'unreadCount'
        ));
    }

    /**
     * Nhân viên cập nhật trạng thái / tiến độ task của mình.
     */
    public function updateProgress(Request $request, $id)
    {
        $user = Auth::user();
        $task = Task::where('id', $id)
            ->where('assigned_to', $user->id)
            ->firstOrFail();

        $request->validate([
            'status'   => 'required|in:Chờ xử lý,Đang làm,Đang review,Hoàn thành',
            'progress' => 'nullable|integer|min:0|max:100',
        ]);

        $task->update([
            'status'   => $request->status,
            'progress' => $request->progress ?? $task->progress,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['ok' => true, 'status' => $task->status, 'progress' => $task->progress]);
        }

        return redirect()->route('employee.dashboard')
            ->with('success', "Đã cập nhật tiến độ: {$task->task_name}");
    }
}
