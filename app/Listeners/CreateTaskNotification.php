<?php

namespace App\Listeners;

use App\Events\TaskCreated;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CreateTaskNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
     }

    /**
     * Handle the event.
     *
     * @param  TaskCreated  $event
     * @return void
     */
    public function handle(TaskCreated $event)
    {
        $task = $event->task;

        if (!$task->assigned_to) {
            return;
        }

        // Kiểm tra user thực sự tồn tại để tránh FK violation
        if (!User::where('id', $task->assigned_to)->exists()) {
            return;
        }

        $creatorName = $task->creator ? $task->creator->name : ($task->assigner ? $task->assigner->name : 'Quản lý');

        try {
            Notification::create([
                'user_id' => $task->assigned_to,
                'task_id' => $task->id,
                'title'   => 'Bạn vừa được giao công việc mới',
                'message' => 'WH-' . str_pad($task->id, 3, '0', STR_PAD_LEFT)
                           . ': ' . $task->task_name
                           . ' (Người giao: ' . $creatorName . ')',
                'is_read' => false,
            ]);
        } catch (\Exception $e) {
            Log::warning('Không thể tạo thông báo tự động cho task #' . $task->id . ': ' . $e->getMessage());
        }
    }
}
