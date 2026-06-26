<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

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

    public function showRegister()
    {
        $this->ensureBaseData();

        $departments = Department::all();
        $roles = Role::where('id', User::ROLE_EMPLOYEE)->get();

        return view('auth.register', compact('departments', 'roles'));
    }

    public function register(Request $request)
    {
        $this->ensureBaseData();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'role_id' => [
                'required',
                'integer',
                Rule::exists('roles', 'id'),
                Rule::in([User::ROLE_EMPLOYEE]),
            ],
            'department_id' => 'required|exists:departments,id',
        ], [
            'name.required' => 'Vui lòng nhập họ tên',
            'email.required' => 'Vui lòng nhập email',
            'email.email' => 'Email không đúng định dạng',
            'email.unique' => 'Email đã tồn tại',
            'password.required' => 'Vui lòng nhập mật khẩu',
            'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
            'password.confirmed' => 'Mật khẩu xác nhận không khớp',
            'role_id.required' => 'Vui lòng chọn chức vụ',
            'role_id.in' => 'Đăng ký công khai chỉ dành cho Nhân viên',
            'department_id.required' => 'Vui lòng chọn phòng ban',
            'department_id.exists' => 'Phòng ban không hợp lệ',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => User::ROLE_EMPLOYEE,
            'department_id' => $request->department_id,
        ]);

        return redirect()->route('login')
            ->with('success', 'Đăng ký tài khoản thành công!');
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
            ['TENPHONG' => 'Nhân sự', 'name' => 'Human Resources']
        );
        Department::updateOrCreate(
            ['id' => 2],
            ['TENPHONG' => 'Đào tạo', 'name' => 'Training']
        );
        Department::updateOrCreate(
            ['id' => 3],
            ['TENPHONG' => 'Pháp chế', 'name' => 'Legal']
        );

        Role::updateOrCreate(['id' => User::ROLE_ADMIN], ['name' => 'Giám đốc / Phó Giám đốc']);
        Role::updateOrCreate(['id' => User::ROLE_MANAGER], ['name' => 'Trưởng phòng / Tổ trưởng']);
        Role::updateOrCreate(['id' => User::ROLE_EMPLOYEE], ['name' => 'Nhân viên']);

        if (User::count() === 0) {
            User::create([
                'id' => 1,
                'name' => 'Hoàng Thị Em',
                'email' => 'em.hoang@mobifone.vn',
                'password' => bcrypt(env('DEFAULT_DIRECTOR_PASSWORD', 'change-me')),
                'role_id' => User::ROLE_ADMIN,
                'department_id' => 1,
            ]);
        }
    }
}
