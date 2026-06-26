<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProgressController extends Controller
{
    // ==================== DANH SÁCH TIẾN ĐỘ ====================

    /**
     * Hiển thị bảng theo dõi tiến độ tất cả công việc.
     * Hỗ trợ lọc theo trạng thái và tìm kiếm theo tên.
     */
    public function index(Request $request)
    {
        $filter = $request->input('filter', 'all');
        $search = $request->input('search');
        $user   = Auth::user();

        $tasks = Task::with(['assignee', 'creator'])
            // Lọc theo từ khóa tên công việc
            ->when($search, fn ($q) => $q->where('task_name', 'like', "%{$search}%"))
            // Lọc theo trạng thái
            ->when($filter === 'todo',    fn ($q) => $q->where('status', 'Todo'))
            ->when($filter === 'doing',   fn ($q) => $q->where('status', 'In Progress'))
            ->when($filter === 'done',    fn ($q) => $q->where('status', 'Done'))
            ->when($filter === 'overdue', function ($q) {
                $q->where(function ($inner) {
                    $inner->where('status', 'Overdue')
                        ->orWhere(function ($late) {
                            $late->whereNotNull('deadline')
                                ->whereDate('deadline', '<', Carbon::today())
                                ->where('status', '!=', 'Done');
                        });
                });
            })
            // Giới hạn theo vai trò
            ->when(!$user->isDirector() && $user->isLeader(), function ($q) use ($user) {
                $q->where(fn ($inner) => $inner
                    ->where('assigned_by', $user->id)
                    ->orWhere('assigned_to', $user->id)
                );
            })
            ->when(!$user->isDirector() && !$user->isLeader(), function ($q) use ($user) {
                $q->where('assigned_to', $user->id);
            })
            ->orderByRaw('ISNULL(deadline)')   // Task có deadline lên trước
            ->orderBy('deadline')
            ->paginate(15)
            ->withQueryString();

        // Tổng hợp số liệu thống kê (scope theo vai trò)
        $baseQuery = $this->buildScopedQuery($user);

        $summary = [
            'all'     => (clone $baseQuery)->count(),
            'todo'    => (clone $baseQuery)->where('status', 'Todo')->count(),
            'doing'   => (clone $baseQuery)->where('status', 'In Progress')->count(),
            'done'    => (clone $baseQuery)->where('status', 'Done')->count(),
            'overdue' => (clone $baseQuery)->where(function ($q) {
                $q->where('status', 'Overdue')
                    ->orWhere(function ($inner) {
                        $inner->whereNotNull('deadline')
                            ->whereDate('deadline', '<', Carbon::today())
                            ->where('status', '!=', 'Done');
                    });
            })->count(),
        ];

        // Tỉ lệ hoàn thành trung bình (dùng cột progress)
        $avgProgress = (clone $baseQuery)->avg('progress') ?? 0;

        return view('admin.layouts.tiendo.index', compact(
            'tasks', 'filter', 'search', 'summary', 'avgProgress'
        ));
    }

    // ==================== CẬP NHẬT TIẾN ĐỘ ====================

    /**
     * Cập nhật % tiến độ và/hoặc trạng thái của một công việc.
     * Hỗ trợ cả AJAX request lẫn form submit.
     */
    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // Chỉ người được giao việc, người giao việc, Trưởng phòng, Giám đốc mới được cập nhật
        $user = Auth::user();
        $this->authorizeProgressUpdate($task, $user);

        $request->validate([
            'status'   => 'sometimes|required|in:Todo,In Progress,Done,Overdue',
            'progress' => 'sometimes|required|integer|min:0|max:100',
            'note'     => 'nullable|string|max:500',
        ], [
            'status.in'          => 'Trạng thái không hợp lệ.',
            'progress.integer'   => 'Tiến độ phải là số nguyên.',
            'progress.min'       => 'Tiến độ không được nhỏ hơn 0.',
            'progress.max'       => 'Tiến độ không được lớn hơn 100.',
        ]);

        $data = [];

        // --- Xử lý cập nhật trạng thái ---
        if ($request->filled('status')) {
            $data['status'] = $request->input('status');

            // Khi chuyển sang Done → tự động đặt 100%
            if ($data['status'] === 'Done') {
                $data['progress'] = 100;
            }
            // Khi kéo ngược về Todo → reset tiến độ
            if ($data['status'] === 'Todo' && !$request->filled('progress')) {
                $data['progress'] = 0;
            }
        }

        // --- Xử lý cập nhật % tiến độ ---
        if ($request->filled('progress')) {
            $progress = (int) $request->input('progress');
            $data['progress'] = $progress;

            // Chỉ tự suy status nếu request không truyền status riêng
            if (!$request->filled('status')) {
                $data['status'] = $this->inferStatus($task, $progress);
            }
        }

        $task->update($data);

        // Phản hồi AJAX
        if ($request->expectsJson()) {
            return response()->json([
                'success'     => true,
                'task_id'     => $task->id,
                'status'      => $task->fresh()->status,
                'progress'    => $task->fresh()->progress,
                'status_label'=> $this->statusLabel($task->fresh()->status),
                'message'     => 'Cập nhật tiến độ thành công.',
            ]);
        }

        return redirect()
            ->back()
            ->with('thongbao', 'Cập nhật tiến độ thành công.');
    }

    // ==================== CẬP NHẬT NHANH KANBAN ====================

    /**
     * API endpoint cập nhật nhanh trạng thái (Kanban drag & drop).
     * Chỉ nhận JSON – trả về JSON.
     */
    public function quickStatus(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        $request->validate([
            'status' => 'required|in:Todo,In Progress,Done,Overdue',
        ]);

        $newStatus = $request->input('status');
        $progress  = $task->progress;

        // Tự động đồng bộ % khi kéo sang Done / Todo
        if ($newStatus === 'Done') {
            $progress = 100;
        } elseif ($newStatus === 'Todo') {
            $progress = 0;
        } elseif ($newStatus === 'In Progress' && $progress === 0) {
            $progress = 10; // Khởi đầu mặc định
        }

        $task->update([
            'status'   => $newStatus,
            'progress' => $progress,
        ]);

        return response()->json([
            'success'  => true,
            'status'   => $task->status,
            'progress' => $task->progress,
        ]);
    }

    // ==================== PRIVATE HELPERS ====================

    /**
     * Tạo query builder có scope theo role của user hiện tại.
     */
    private function buildScopedQuery($user)
    {
        $query = Task::query();

        if (!$user->isDirector() && $user->isLeader()) {
            $query->where(fn ($q) => $q
                ->where('assigned_by', $user->id)
                ->orWhere('assigned_to', $user->id)
            );
        } elseif (!$user->isDirector() && !$user->isLeader()) {
            $query->where('assigned_to', $user->id);
        }

        return $query;
    }

    /**
     * Suy luận trạng thái dựa trên % tiến độ và deadline.
     */
    private function inferStatus(Task $task, int $progress): string
    {
        if ($progress === 100) {
            return 'Done';
        }

        if ($progress === 0) {
            return 'Todo';
        }

        // Quá hạn?
        if ($task->deadline && Carbon::today()->toDateString() > $task->deadline) {
            return 'Overdue';
        }

        return 'In Progress';
    }

    /**
     * Kiểm tra quyền cập nhật tiến độ.
     * Người giao, người nhận, Trưởng phòng, Giám đốc đều được phép.
     */
    private function authorizeProgressUpdate(Task $task, $user): void
    {
        if ($user->isDirector() || $user->isLeader()) {
            return;
        }

        if ($task->assigned_to === $user->id || $task->assigned_by === $user->id) {
            return;
        }

        abort(403, 'Bạn không có quyền cập nhật tiến độ công việc này.');
    }

    /**
     * Chuyển đổi status sang nhãn tiếng Việt.
     */
    private function statusLabel(string $status): string
    {
        return match ($status) {
            'Todo'        => 'Chờ thực hiện',
            'In Progress' => 'Đang thực hiện',
            'Done'        => 'Hoàn thành',
            'Overdue'     => 'Quá hạn',
            default       => $status,
        };
    }
}
