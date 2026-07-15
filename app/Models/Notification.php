<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'task_id',
        'title',
        'message',
        'is_read',
    ];

    // Mối quan hệ: Một thông báo thuộc về 1 User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Mối quan hệ: Một thông báo có thể gắn với 1 Đầu việc
    public function task()
    {
        return $this->belongsTo(Task::class);
    }
}
