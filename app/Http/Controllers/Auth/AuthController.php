<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        $this->ensureBaseData();

        if (Auth::check()) {
            return $this->redirectToRoleDashboard(Auth::user());
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $this->ensureBaseData();

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'password.required' => 'Vui lòng nhập mật khẩu',
        ]);

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password,
        ])) {
            if (Auth::user()->is_active === false) {
                Auth::logout();

                return back()->with('error', 'Tài khoản đã bị vô hiệu hóa. Vui lòng liên hệ quản lý.');
            }

            $request->session()->regenerate();

            return $this->redirectToRoleDashboard(Auth::user());
        }

        return back()->with('error', 'Sai email hoặc mật khẩu');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectToRoleDashboard(User $user)
    {
        if ($user->isDirector()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isLeader()) {
            return redirect()->route('manager.dashboard');
        }

        return redirect()->route('employee.dashboard');
    }

    private function ensureBaseData(): void
    {
        Department::updateOrCreate(
            ['id' => 1],
            ['TENPHONG' => 'Trung tâm kinh doanh', 'name' => 'trung-tam-kinh-doanh']
        );
        Department::updateOrCreate(
            ['id' => 2],
            ['TENPHONG' => 'Phòng viễn thông', 'name' => 'phong-vien-thong']
        );
        Department::updateOrCreate(
            ['id' => 3],
            ['TENPHONG' => 'Phòng tổng hợp', 'name' => 'phong-tong-hop']
        );

        Role::updateOrCreate(['id' => User::ROLE_ADMIN], ['name' => 'Phụ trách chi nhánh']);
        Role::updateOrCreate(['id' => User::ROLE_MANAGER], ['name' => 'Phó giám đốc chi nhánh']);
        Role::updateOrCreate(['id' => User::ROLE_EMPLOYEE], ['name' => 'Nhân viên']);
        Role::updateOrCreate(['id' => User::ROLE_DEPT_HEAD_BIZ], ['name' => 'Giám đốc trung tâm kinh doanh']);
        Role::updateOrCreate(['id' => User::ROLE_DEPT_DEP_BIZ], ['name' => 'Phó giám đốc trung tâm kinh doanh']);
        Role::updateOrCreate(['id' => User::ROLE_DEPT_HEAD_TEL], ['name' => 'Phụ trách phòng viễn thông']);
        Role::updateOrCreate(['id' => User::ROLE_DEPT_HEAD_GEN], ['name' => 'Phụ trách phòng tổng hợp']);

        if (User::count() === 0) {
            User::create([
                'id' => 1,
                'name' => 'Hoàng Thị Em',
                'email' => 'em.hoang@mobifone.vn',
                'password' => bcrypt(env('DEFAULT_DIRECTOR_PASSWORD', 'change-me')),
                'role_id' => User::ROLE_ADMIN,
                'department_id' => 1,
                'is_active' => true,
            ]);
        }
    }
}
