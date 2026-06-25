<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Dashboard\DashboardController;

// ==================== FRONTEND LANDING PAGE ====================
Route::get('/', [DashboardController::class, 'landing'])->name('landing');

// ==================== MODULE AUTHENTICATION ====================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==================== MODULE QUÊN MẬT KHẨU ====================
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'updatePassword'])->name('password.update');

// ==================== WORKHUB DASHBOARD (PURE BLADE) ====================
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    // 1. Trang tổng quan KPI
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard.index');

    // 2. Phân hệ Quản lý công việc (Kanban & List)
    Route::get('/tasks', [DashboardController::class, 'tasks'])->name('dashboard.tasks');
    Route::post('/tasks/save', [DashboardController::class, 'saveTask'])->name('dashboard.tasks.save');

    // 3. Phân hệ Quản lý thành viên (Chỉ dành cho Trưởng phòng - role:1)
    Route::middleware(['role:1'])->group(function () {
        Route::get('/members', [DashboardController::class, 'members'])->name('dashboard.members');
        Route::post('/members/save', [DashboardController::class, 'saveMember'])->name('dashboard.members.save');
    });

    // 4. Phân hệ Báo cáo & Thống kê (ApexCharts)
    Route::get('/reports', [DashboardController::class, 'reports'])->name('dashboard.reports');

    // 5. Phân hệ Phân quyền chi tiết (Chỉ dành cho Trưởng phòng - role:1)
    Route::get('/roles', [DashboardController::class, 'roles'])->name('dashboard.roles')->middleware('role:1');

    // 6. Phân hệ Thông báo & Cấu hình email
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('dashboard.notifications');
});
