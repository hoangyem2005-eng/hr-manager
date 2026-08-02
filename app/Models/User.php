<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const ROLE_ADMIN = 1;
    public const ROLE_MANAGER = 2;
    public const ROLE_EMPLOYEE = 3;
    public const ROLE_DEPT_HEAD_BIZ = 4;
    public const ROLE_DEPT_DEP_BIZ = 5;
    public const ROLE_DEPT_HEAD_TEL = 6;
    public const ROLE_DEPT_HEAD_GEN = 7;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
        'department_id',
        'avatar_path',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function collaborativeTasks()
    {
        return $this->belongsToMany(Task::class, 'task_assignees')->withTimestamps();
    }

    public function createdTasks()
    {
        return $this->hasMany(Task::class, 'assigned_by');
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function isDirector(): bool
    {
        return in_array((int) $this->role_id, [self::ROLE_ADMIN, self::ROLE_MANAGER], true);
    }

    public function isLeader(): bool
    {
        return in_array((int) $this->role_id, [
            self::ROLE_DEPT_HEAD_BIZ,
            self::ROLE_DEPT_DEP_BIZ,
            self::ROLE_DEPT_HEAD_TEL,
            self::ROLE_DEPT_HEAD_GEN
        ], true);
    }

    public function isEmployee(): bool
    {
        return (int) $this->role_id === self::ROLE_EMPLOYEE;
    }

    public function getRoleDisplayNameAttribute(): string
    {
        return match ((int) $this->role_id) {
            self::ROLE_ADMIN => 'Phụ trách chi nhánh',
            self::ROLE_MANAGER => 'Phó giám đốc chi nhánh',
            self::ROLE_EMPLOYEE => 'Nhân viên',
            self::ROLE_DEPT_HEAD_BIZ => 'Giám đốc trung tâm kinh doanh',
            self::ROLE_DEPT_DEP_BIZ => 'Phó giám đốc trung tâm kinh doanh',
            self::ROLE_DEPT_HEAD_TEL => 'Phụ trách phòng viễn thông',
            self::ROLE_DEPT_HEAD_GEN => 'Phụ trách phòng tổng hợp',
            default => $this->role->name ?? 'Chưa có chức vụ',
        };
    }
}
