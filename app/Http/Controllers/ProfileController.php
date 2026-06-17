<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Hiển thị danh sách hồ sơ nhân viên
     */
    public function index()
    {
        // 1. Lấy toàn bộ hồ sơ nhân viên từ database, phân trang 10 người/trang
        $profiles = Profile::paginate(10);

        // 2. Trả về view tĩnh đã gộp của bạn Kha và truyền biến $profiles ra ngoài
        return view('admin.layouts.nhanvien.danhsach', compact('profiles'));
    }
}