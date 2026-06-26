<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\ProgressController;
use App\Http\Controllers\DocumentController;
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
});

Route::middleware(['auth'])->prefix('employee')->name('employee.')->group(function () {
    Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('dashboard');
    Route::patch('/tasks/{id}/progress', [EmployeeController::class, 'updateProgress'])->name('task.progress');
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
    });

    // 4. Phân hệ Báo cáo & Thống kê (ApexCharts)
    Route::get('/reports', [DashboardController::class, 'reports'])->name('dashboard.reports');

    // 5. Phân hệ Phân quyền chi tiết (Chỉ dành cho Trưởng phòng - role:1)
    Route::get('/roles', [DashboardController::class, 'roles'])->name('dashboard.roles')->middleware('role:1,2');

    // 6. Phân hệ Thông báo & Cấu hình email
    Route::get('/notifications', [DashboardController::class, 'notifications'])->name('dashboard.notifications');
});

// ==================== MODULE CÔNG VIỆC (TASK CRUD) ====================
Route::middleware(['auth'])->prefix('cong-viec')->group(function () {

    // Danh sách công việc (có lọc + phân trang)
    Route::get('/',          [TaskController::class, 'index'])->name('congviec.danhsach');

    // Tạo mới
    Route::get('/tao-moi',   [TaskController::class, 'create'])->name('congviec.taomoi');
    Route::post('/luu',      [TaskController::class, 'store'])->name('congviec.luu');

    // Chi tiết
    Route::get('/{id}',      [TaskController::class, 'show'])->name('congviec.chitiet');

    // Chỉnh sửa
    Route::get('/{id}/sua',  [TaskController::class, 'edit'])->name('congviec.sua');
    Route::put('/{id}',      [TaskController::class, 'update'])->name('congviec.capnhat');

    // Xóa
    Route::delete('/{id}',   [TaskController::class, 'destroy'])->name('congviec.xoa');

    // Cập nhật nhanh tiến độ (AJAX hoặc form)
    Route::patch('/{id}/tien-do', [TaskController::class, 'updateProgress'])->name('congviec.tiendo');

    // Upload file đính kèm vào task đã tồn tại
    Route::post('/{id}/upload', [TaskController::class, 'uploadFile'])->name('congviec.upload');

    // Xóa một file đính kèm
    Route::delete('/file/{documentId}', [TaskController::class, 'deleteFile'])->name('congviec.file.xoa');

    // Download file đính kèm
    Route::get('/file/{documentId}/download', [DocumentController::class, 'download'])->name('congviec.file.download');

    // Preview file trực tiếp trên trình duyệt
    Route::get('/file/{documentId}/preview',  [DocumentController::class, 'preview'])->name('congviec.file.preview');
});

// ==================== MODULE TIẾN ĐỘ (PROGRESS) ====================
Route::middleware(['auth'])->prefix('tien-do')->group(function () {

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
