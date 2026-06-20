<?php

use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ForgotPasswordController; 

// Trang chủ: Tự động điều hướng sang trang Login
Route::get('/', function () {
    return redirect('/login');
});

<<<<<<< HEAD
// ==================== MODULE AUTHENTICATION ====================
Route::get('/login', [AuthController::class, 'showLogin']);
=======

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
>>>>>>> e2cf03273774ff86755ac4fb2a79323e85bd7f40
Route::post('/login', [AuthController::class, 'login']);

Route::get('/register', [AuthController::class, 'showRegister']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/logout', [AuthController::class, 'logout']);

Route::get('/dashboard', function () {
<<<<<<< HEAD
    return view('welcome');

})->middleware('auth');


// ==================== MODULE QUẢN LÝ NHÂN VIÊN ====================
Route::prefix('admin/nhanvien')->group(function () {
    // Trang hồ sơ nhân sự (Upload/Download)
    Route::get('hoso', [NhanVienController::class, 'hoso'])->name('nhanvien.hoso');
    Route::post('hoso/upload', [NhanVienController::class, 'uploadHoso'])->name('nhanvien.hoso.upload');
    Route::get('hoso/download/{filename}', [NhanVienController::class, 'downloadHoso'])
        ->where('filename', '.*')
        ->name('nhanvien.hoso.download');

    // Trang danh sách (có kèm tìm kiếm)
    Route::get('danhsach', [NhanVienController::class, 'index'])->name('nhanvien.danhsach');

    // Trang thêm mới nhân viên
    Route::get('them', [NhanVienController::class, 'create'])->name('nhanvien.them');
    Route::post('luu', [NhanVienController::class, 'store']); 

    // Trang sửa và xóa nhân viên
    Route::get('sua/{id}', [NhanVienController::class, 'edit']);
    Route::post('capnhat/{id}', [NhanVienController::class, 'update']); 
    Route::get('xoa/{id}', [NhanVienController::class, 'destroy']);

    // Trang thống kê biểu đồ
    Route::get('thongke', [NhanVienController::class, 'thongke'])->name('nhanvien.thongke');
});

// Khai báo nhóm Route CRUD theo chuẩn Resource cho Profile nhân viên
Route::resource('admin/nhanvien', ProfileController::class);
=======
    return view('auth.dashboard');
})->middleware('auth');


Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');

Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

Route::get('/reset-password/{token}', [ForgotPasswordController::class, 'showResetForm'])->name('password.reset');

Route::post('/reset-password', [ForgotPasswordController::class, 'updatePassword'])->name('password.update');
>>>>>>> e2cf03273774ff86755ac4fb2a79323e85bd7f40
