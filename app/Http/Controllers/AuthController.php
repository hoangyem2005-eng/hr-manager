<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('layouts.auth.login');
    }

    public function showRegister()
    {
        return view('layouts.auth.register');
    }

   public function register(Request $request)
{
$request->validate([
'name' => 'required',
'email' => 'required|email|unique:users,email',
'password' => 'required|min:6|confirmed',
'role_id' => 'required'
], [
'name.required' => 'Vui lòng nhập họ tên',
'email.required' => 'Vui lòng nhập email',
'email.email' => 'Email không đúng định dạng',
'email.unique' => 'Email đã tồn tại',
'password.required' => 'Vui lòng nhập mật khẩu',
'password.min' => 'Mật khẩu phải có ít nhất 6 ký tự',
'password.confirmed' => 'Mật khẩu xác nhận không khớp',
'role_id.required' => 'Vui lòng chọn chức vụ'
]);

User::create([
    'name' => $request->name,
    'email' => $request->email,
    'password' => Hash::make($request->password),
    'role_id' => $request->role_id,
    'department_id' => $request->department_id,
]);

return redirect('/register')
    ->with('success', 'Đăng ký tài khoản thành công!');


}

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        if (Auth::attempt([
            'email' => $request->email,
            'password' => $request->password
        ])) {

            $request->session()->regenerate();

            return redirect('/dashboard');
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

        return redirect('/login');
    }
}

