<?php

use App\Http\Controllers\NhanVienController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('trang-chu');

Route::prefix('admin/nhanvien')->group(function () {
    // Trang ho so nhan su
    Route::get('hoso', [NhanVienController::class, 'hoso'])->name('nhanvien.hoso');
    Route::post('hoso/upload', [NhanVienController::class, 'uploadHoso'])->name('nhanvien.hoso.upload');
    Route::get('hoso/download/{filename}', [NhanVienController::class, 'downloadHoso'])
        ->where('filename', '.*')
        ->name('nhanvien.hoso.download');

    // Trang danh sach (co kem tim kiem)
    Route::get('danhsach', [NhanVienController::class, 'index'])->name('nhanvien.danhsach');

    // Trang them moi
    Route::get('them', [NhanVienController::class, 'create'])->name('nhanvien.them');
    Route::post('luu', [NhanVienController::class, 'store']); // Se dung cho form them

    // Trang sua va xoa
    Route::get('sua/{id}', [NhanVienController::class, 'edit']);
    Route::post('capnhat/{id}', [NhanVienController::class, 'update']); // Se dung cho form sua
    Route::get('xoa/{id}', [NhanVienController::class, 'destroy']);

    // Trang thong ke bieu do
    Route::get('thongke', [NhanVienController::class, 'thongke'])->name('nhanvien.thongke');
});

// Khai bao nhom Route CRUD theo chuan Resource cua Laravel
Route::resource('admin/nhanvien', ProfileController::class);
