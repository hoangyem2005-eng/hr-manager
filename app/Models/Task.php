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

    /** Hạn nhận việc / Thời gian chờ xử lý (2h tiêu chuẩn, +2h nếu giao từ 17h trở đi) */
    public function getAcceptanceDeadlineAttribute(): ?\Carbon\Carbon
    {
        if (!$this->created_at) {
            return null;
        }

        $createdAt = \Carbon\Carbon::parse($this->created_at);
        $hoursToAdd = $createdAt->hour >= 17 ? 4 : 2;

        return $createdAt->copy()->addHours($hoursToAdd);
    }

    /** Kiểm tra công việc ở trạng thái Chờ xử lý có bị quá thời gian chờ nhận hay không */
    public function getIsAcceptanceExpiredAttribute(): bool
    {
        if (!in_array($this->status, ['Chờ xử lý', 'Todo'], true)) {
            return false;
        }

        $deadline = $this->acceptance_deadline;

        return $deadline ? \Carbon\Carbon::now()->greaterThan($deadline) : false;
    }

    /** Tự động kiểm tra và chuyển trạng thái sang Quá hạn nếu quá hạn chờ nhận việc */
    public function checkAndUpdateAcceptanceTimeout(): bool
    {
        if ($this->is_acceptance_expired) {
            $this->update(['status' => 'Quá hạn']);

            if ($this->assigned_to && User::where('id', $this->assigned_to)->exists()) {
                try {
                    Notification::create([
                        'user_id' => $this->assigned_to,
                        'task_id' => $this->id,
                        'title'   => 'Hết hạn thời gian chờ nhận việc',
                        'message' => 'WH-' . str_pad($this->id, 3, '0', STR_PAD_LEFT)
                                   . ': ' . $this->task_name . ' đã quá thời gian chờ xử lý (2h/4h sau 17h).',
                        'is_read' => false,
                    ]);
                } catch (\Exception $e) {
                    \Log::warning('Không thể tạo notification hết hạn chờ nhận cho task #' . $this->id . ': ' . $e->getMessage());
                }
            }

            return true;
        }

        return false;
    }
}
