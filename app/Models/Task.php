<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_name',
        'description',
        'priority',
        'assigned_by',
        'assigned_to',
        'deadline',
        'status',
        'progress',
        'overdue_email_sent_at',
        'is_proposal',
        'proposal_step',
    ];

    protected $casts = [
        'deadline' => 'date',
        'progress' => 'integer',
        'overdue_email_sent_at' => 'datetime',
        'is_proposal' => 'boolean',
        'proposal_step' => 'integer',
    ];

    // ==================== QUAN HỆ ====================

    /** Người tạo / giao công việc */
    public function creator()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /** Alias cho creator (dùng trong DashboardController) */
    public function assigner()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /** Người nhận công việc */
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'task_assignees')->withTimestamps();
    }

    /** File đính kèm của công việc */
    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    // ==================== ACCESSORS ====================

    /** Nhãn tiếng Việt của trạng thái */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'Todo'        => 'Chờ thực hiện',
            'In Progress' => 'Đang thực hiện',
            'Done'        => 'Hoàn thành',
            'Overdue'     => 'Quá hạn',
            default       => $this->status,
        };
    }

    /** CSS class badge tương ứng với trạng thái */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'Todo'        => 'badge-secondary',
            'In Progress' => 'badge-info',
            'Done'        => 'badge-success',
            'Overdue'     => 'badge-danger',
            default       => 'badge-light',
        };
    }

    /** Màu thanh tiến độ */
    public function getProgressColorAttribute(): string
    {
        if ($this->progress >= 100) return 'bg-success';
        if ($this->progress >= 70)  return 'bg-info';
        if ($this->progress >= 40)  return 'bg-warning';
        return 'bg-danger';
    }

    // Notification KHÔNG được tạo ở đây (không dùng booted()).
    // Xem TaskController::notifyAssignee() để biết logic gửi thông báo.
}
