<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Model Document – đại diện cho file đính kèm của một Task.
 *
 * Bảng: documents
 * Disk: 'public' (symlink public/storage → storage/app/public)
 */
class Document extends Model
{
    use HasFactory;

    public const STATUS_MANAGER_REVIEW = 'manager_review';
    public const STATUS_DIRECTOR_VISIBLE = 'director_visible';

    protected $table = 'documents';

    protected $fillable = [
        'task_id',
        'user_id',
        'file_name',   // Tên file gốc (Báo cáo.pdf)
        'file_path',   // Đường dẫn lưu trên disk (tasks/1/uuid.pdf)
        'file_type',   // Phần mở rộng (pdf, docx, ...)
        'disk',        // Disk lưu trữ ('public', 'local', 's3')
        'review_status',
        'forwarded_by',
        'forwarded_at',
    ];

    protected $casts = [
        'forwarded_at' => 'datetime',
    ];

    // ==================== QUAN HỆ ====================

    /** Task mà file này thuộc về */
    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    /** Người đã tải file lên */
    public function uploader()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function forwarder()
    {
        return $this->belongsTo(User::class, 'forwarded_by');
    }

    // ==================== ACCESSORS ====================

    /**
     * URL công khai qua symlink (public/storage/tasks/...)
     * Không cần qua controller – phục vụ trực tiếp từ web server.
     */
    public function getPublicUrlAttribute(): string
    {
        return Storage::disk($this->disk ?? 'public')->url($this->file_path);
    }

    /** Kiểm tra file có phải ảnh không */
    public function getIsImageAttribute(): bool
    {
        return in_array(strtolower($this->file_type ?? ''), ['jpg', 'jpeg', 'png', 'gif', 'webp']);
    }

    /** Kiểm tra file có phải PDF không */
    public function getIsPdfAttribute(): bool
    {
        return strtolower($this->file_type ?? '') === 'pdf';
    }

    /**
     * Icon Font Awesome tương ứng với loại file
     */
    public function getIconClassAttribute(): string
    {
        return match (strtolower($this->file_type ?? '')) {
            'pdf'           => 'fa-file-pdf text-red-500',
            'doc', 'docx'   => 'fa-file-word text-blue-500',
            'xls', 'xlsx'   => 'fa-file-excel text-green-500',
            'ppt', 'pptx'   => 'fa-file-powerpoint text-orange-500',
            'jpg', 'jpeg',
            'png', 'gif',
            'webp'          => 'fa-file-image text-purple-500',
            'zip', 'rar'    => 'fa-file-archive text-yellow-500',
            'txt', 'csv'    => 'fa-file-alt text-gray-400',
            default         => 'fa-file text-gray-400',
        };
    }

    /** Kiểm tra file có tồn tại trên disk không */
    public function existsOnDisk(): bool
    {
        return Storage::disk($this->disk ?? 'public')->exists($this->file_path);
    }
}
