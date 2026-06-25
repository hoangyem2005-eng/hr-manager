<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $guarded = [];

    // Quan hệ: người phân công công việc
    public function creator()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    // Quan hệ: người nhận công việc
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Bắt sự kiện tự động khi Task được tạo
    protected static function booted()
    {
        static::created(function ($task) {
            if ($task->assigned_to) {
                // Lấy thông tin người tạo công việc để đưa vào nội dung thông báo
                $creatorName = $task->creator ? $task->creator->name : 'Quản lý';
                
                Notification::create([
                    'user_id' => $task->assigned_to,
                    'task_id' => $task->id,
                    'title' => 'Bạn được giao công việc mới',
                    'message' => 'WH-' . str_pad($task->id, 3, '0', STR_PAD_LEFT) . ': ' . $task->task_name . ' (Được giao bởi ' . $creatorName . ')',
                    'is_read' => false,
                ]);
            }
        });
    }
}
