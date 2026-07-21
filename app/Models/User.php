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
        if ((int) $this->role_id === self::ROLE_ADMIN) {
            return true;
        }

        $roleName = mb_strtolower($this->role->name ?? '');

        return str_contains($roleName, 'admin')
            || str_contains($roleName, 'giam doc')
            || str_contains($roleName, 'giám đốc');
    }

    public function isLeader(): bool
    {
        if ((int) $this->role_id === self::ROLE_MANAGER) {
            return true;
        }

        $roleName = mb_strtolower($this->role->name ?? '');

        return str_contains($roleName, 'quan ly')
            || str_contains($roleName, 'quản lý')
            || str_contains($roleName, 'truong phong')
            || str_contains($roleName, 'trưởng phòng');
    }

    public function isEmployee(): bool
    {
        return (int) $this->role_id === self::ROLE_EMPLOYEE;
    }

    public function getRoleDisplayNameAttribute(): string
    {
        return match ((int) $this->role_id) {
            self::ROLE_ADMIN => 'Giám đốc',
            self::ROLE_MANAGER => 'Trưởng phòng',
            self::ROLE_EMPLOYEE => 'Nhân viên',
            default => $this->role->name ?? 'Chưa có chức vụ',
        };
    }
}
