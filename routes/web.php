<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Employee\EmployeeController;

// ==================== FRONTEND LANDING PAGE ====================
Route::get('/', [DashboardController::class, 'landing'])->name('landing');

// ==================== MODULE AUTHENTICATION ====================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// ==================== ROLE-BASED DASHBOARDS ====================
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('user.store');
    Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('user.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('user.destroy');
});

Route::middleware(['auth'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
    Route::post('/employees', [ManagerController::class, 'storeEmployee'])->name('employee.store');
    Route::post('/tasks', [ManagerController::class, 'assignTask'])->name('task.assign');
    Route::patch('/tasks/{task}/delegate', [ManagerController::class, 'delegateIncomingTask'])->name('task.delegate');
    Route::patch('/files/{document}/forward', [ManagerController::class, 'forwardDocumentToDirector'])->name('file.forward');
});

Route::middleware(['auth'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
    Route::patch('/tasks/{id}/progress', [EmployeeController::class, 'updateProgress'])->name('task.progress');
    // Nhân viên xem chi tiết task của mình (không dùng layout admin)
    Route::get('/tasks/{id}', [EmployeeController::class, 'taskDetail'])->name('task.detail');
    // Nhân viên upload file đính kèm cho task
    Route::post('/tasks/{id}/upload', [EmployeeController::class, 'uploadFile'])->name('task.upload');
    // Nhân viên xóa file của chính họ
    Route::delete('/files/{documentId}', [EmployeeController::class, 'deleteFile'])->name('file.delete');
});

// ==================== MODULE QUÊN MẬT KHẨU ====================
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ForgotPasswordController::class, 'updatePassword'])->name('password.update');

// ==================== WORKHUB DASHBOARD (PURE BLADE) ====================
Route::middleware(['auth'])->prefix('dashboard')->group(function () {

    // 1. Trang tổng quan KPI
    Route::get('/', function () {
        $user = Auth::user();

        if ($user->isDirector()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isLeader()) {
            return redirect()->route('manager.dashboard');
        }

        return redirect()->route('employee.dashboard');
    })->name('dashboard.index');

    // 2. Phân hệ Quản lý công việc (Kanban & List)
    Route::get('/tasks', [DashboardController::class, 'tasks'])->name('dashboard.tasks');
    Route::post('/tasks/save', [DashboardController::class, 'saveTask'])->name('dashboard.tasks.save');

    // 3. Phân hệ Quản lý thành viên (Chỉ dành cho Trưởng phòng - role:1)
    Route::middleware(['role:1,2'])->group(function () {
        Route::get('/members', [DashboardController::class, 'members'])->name('dashboard.members');
        Route::post('/members/save', [DashboardController::class, 'saveMember'])->name('dashboard.members.save');
        Route::patch('/members/{user}', [DashboardController::class, 'updateMember'])->name('dashboard.members.update');
        Route::patch('/members/{user}/role', [DashboardController::class, 'updateMemberRole'])->name('dashboard.members.role');
        Route::patch('/members/{user}/status', [DashboardController::class, 'toggleMemberStatus'])->name('dashboard.members.status');
    });

    // 4. Phân hệ Báo cáo & Thống kê (ApexCharts)
    Route::get('/reports', [DashboardController::class, 'reports'])->name('dashboard.reports');

    // 5. Phân hệ Phân quyền chi tiết (Chỉ dành cho Trưởng phòng - role:1)
    Route::get('/roles', [DashboardController::class, 'roles'])->name('dashboard.roles')->middleware('role:1,2');

    // 6. Phân hệ Thông báo & Cấu hình email
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('dashboard.notifications');
    Route::post('/notifications/broadcast', [DashboardController::class, 'broadcastNotification'])->name('dashboard.notifications.broadcast');
    Route::post('/notifications/mark-all-read', [DashboardController::class, 'markAllNotificationsAsRead'])->name('dashboard.notifications.markAllRead');
    Route::post('/notifications/{notification}/open', [DashboardController::class, 'openNotification'])->name('dashboard.notifications.open');
});

// ==================== MODULE CÔNG VIỆC (TASK CRUD) ====================
Route::middleware(['auth'])->prefix('cong-viec')->group(function () {
    Route::middleware('role:1')->group(function () {
        Route::get('/', [TaskController::class, 'index'])->name('congviec.danhsach');
        Route::get('/tao-moi', [TaskController::class, 'create'])->name('congviec.taomoi');
        Route::post('/luu', [TaskController::class, 'store'])->name('congviec.luu');
        Route::get('/{id}/sua', [TaskController::class, 'edit'])->name('congviec.sua');
        Route::put('/{id}', [TaskController::class, 'update'])->name('congviec.capnhat');
        Route::delete('/{id}', [TaskController::class, 'destroy'])->name('congviec.xoa');
        Route::patch('/{id}/tien-do', [TaskController::class, 'updateProgress'])->name('congviec.tiendo');
        Route::post('/{id}/upload', [TaskController::class, 'uploadFile'])->name('congviec.upload');
        Route::delete('/file/{documentId}', [TaskController::class, 'deleteFile'])->name('congviec.file.xoa');
    });

    Route::get('/file/{documentId}/download', [DocumentController::class, 'download'])->name('congviec.file.download');
    Route::get('/file/{documentId}/preview', [DocumentController::class, 'preview'])->name('congviec.file.preview');

    // Chi tiết công việc: người giao/người nhận được xem, giao diện theo đúng vai trò.
    Route::get('/{id}', [TaskController::class, 'show'])->name('congviec.chitiet');
});

// ==================== MODULE PHÒNG BAN ====================
Route::middleware(['auth', 'role:1'])->prefix('phong-ban')->group(function () {
    Route::get('/', [DepartmentController::class, 'index'])->name('phongban.danhsach');
    Route::get('/tao-moi', [DepartmentController::class, 'create'])->name('phongban.them');
    Route::post('/luu', [DepartmentController::class, 'store'])->name('phongban.luu');
    Route::get('/{id}/sua', [DepartmentController::class, 'edit'])->name('phongban.sua');
    Route::put('/{id}', [DepartmentController::class, 'update'])->name('phongban.capnhat');
    Route::delete('/{id}', [DepartmentController::class, 'destroy'])->name('phongban.xoa');
});

// ==================== MODULE TIẾN ĐỘ (PROGRESS) ====================
Route::middleware(['auth', 'role:1'])->prefix('tien-do')->group(function () {

    // Bảng theo dõi tiến độ (lọc theo filter, phân trang)
    Route::get('/',           [ProgressController::class, 'index'])->name('tiendo.index');

    // Cập nhật tiến độ (hỗ trợ AJAX + form)
    Route::patch('/{id}',     [ProgressController::class, 'update'])->name('tiendo.capnhat');

    // Cập nhật nhanh trạng thái Kanban drag & drop (JSON)
    Route::patch('/{id}/status', [ProgressController::class, 'quickStatus'])->name('tiendo.quickstatus');
});

// ==================== MODULE FILE ĐÍNH KÈM (DOCUMENT) ====================
Route::middleware(['auth'])->prefix('tai-lieu')->group(function () {

    // Upload file cho một task (JSON)
    Route::post('/task/{taskId}/upload',    [DocumentController::class, 'upload'])->name('document.upload');

    // Liệt kê file của một task (JSON)
    Route::get('/task/{taskId}',            [DocumentController::class, 'listByTask'])->name('document.list');

    // Download file về máy
    Route::get('/{documentId}/download',    [DocumentController::class, 'download'])->name('document.download');

    // Preview file inline (ảnh, PDF)
    Route::get('/{documentId}/preview',     [DocumentController::class, 'preview'])->name('document.preview');

    // Xóa file khỏi storage và DB
    Route::delete('/{documentId}',          [DocumentController::class, 'destroy'])->name('document.destroy');
});
