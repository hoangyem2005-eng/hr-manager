<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Tự động seed roles và departments nếu chưa có
        if (\App\Models\Department::count() == 0) {
            \App\Models\Department::create(['id' => 1, 'TENPHONG' => 'Nhân sự', 'name' => 'Human Resources']);
            \App\Models\Department::create(['id' => 2, 'TENPHONG' => 'Đào tạo', 'name' => 'Training']);
            \App\Models\Department::create(['id' => 3, 'TENPHONG' => 'Pháp chế', 'name' => 'Legal']);
        }

        if (\App\Models\Role::count() == 0) {
            \App\Models\Role::create(['id' => 1, 'name' => 'Admin']);
            \App\Models\Role::create(['id' => 2, 'name' => 'Quản lý']);
            \App\Models\Role::create(['id' => 3, 'name' => 'Nhân viên']);
        }

        if (\App\Models\User::count() == 0) {
            \App\Models\User::create([
                'id' => 1,
                'name' => 'Hoàng Thị Em',
                'email' => 'em.hoang@mobifone.vn',
                'password' => bcrypt('123456'),
                'role_id' => 1, // Admin
                'department_id' => 1 // Nhân sự
            ]);
        }

        if (Auth::check()) {
            return redirect()->route('dashboard.index');
        }
        return view('auth.login');
    }

    public function showRegister()
    {
        // Tự động seed roles và departments nếu chưa có
        if (\App\Models\Department::count() == 0) {
            \App\Models\Department::create(['id' => 1, 'TENPHONG' => 'Nhân sự', 'name' => 'Human Resources']);
            \App\Models\Department::create(['id' => 2, 'TENPHONG' => 'Đào tạo', 'name' => 'Training']);
            \App\Models\Department::create(['id' => 3, 'TENPHONG' => 'Pháp chế', 'name' => 'Legal']);
        }

        if (\App\Models\Role::count() == 0) {
            \App\Models\Role::create(['id' => 1, 'name' => 'Admin']);
            \App\Models\Role::create(['id' => 2, 'name' => 'Quản lý']);
            \App\Models\Role::create(['id' => 3, 'name' => 'Nhân viên']);
        }

        $departments = \App\Models\Department::all();
        $roles = \App\Models\Role::all();

        return view('auth.register', compact('departments', 'roles'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role_id' => 'required',
            'department_id' => 'required|exists:departments,id'
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',
            'role_id.required' => 'Vui lòng chọn chức vụ',
            'department_id.required' => 'Vui lòng chọn phòng ban',
            'department_id.exists' => 'Phòng ban không hợp lệ'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id,
            'department_id' => $request->department_id,
        ]);

        return redirect()->route('login')
            ->with('success', 'Đăng ký tài khoản thành công!');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'password.required' => 'Vui lòng nhập mật khẩu',
        ]);

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {
            $request->session()->regenerate();
            return redirect()->route('dashboard.index');
        }

        return back()->with(
            'error',
            'Sai email hoặc mật khẩu'
        );
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
