<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;  
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // 1. Hiển thị form nhập email quên mật khẩu
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // 2. Xử lý lưu token vào bảng password_resets và GHI LINK SẠCH VÀO LOG
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Địa chỉ email này không tồn tại trên hệ thống.'
        ]);

        $email = $request->email;

        // Sử dụng Broker mặc định của Laravel để tự tạo Token hợp lệ VÀ tự lưu vào DB
        $token = Password::getRepository()->create(
            \App\Models\User::where('email', $email)->first()
        );

        // Tạo đường dẫn reset mật khẩu chuẩn dựa trên token vừa tạo
        $resetLink = route('password.reset', ['token' => $token, 'email' => $email]);

        // Ghi thẳng link vào log hệ thống
        Log::info("=================================================================");
        Log::info("LINK_RESET_CUA_BAN_LA: " . $resetLink);
        Log::info("=================================================================");

        return back()->with([
            'status' => 'MobiFone đã gửi liên kết đặt lại mật khẩu đến email của bạn. Vui lòng kiểm tra hộp thư!'
        ]);
    }

    // 3. Hiển thị form nhập mật khẩu mới khi user click từ link sạch trong file log
    public function showResetForm($token, Request $request)
    {
        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user || !Password::getRepository()->exists($user, $token)) {
            return redirect()->route('password.request')->withErrors(['email' => 'Liên kết xác thực không hợp lệ hoặc đã hết hạn!']);
        }

        return view('auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    // 4. Xử lý cập nhật mật khẩu mới vào bảng users
    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        if (!$user || !Password::getRepository()->exists($user, $request->token)) {
            return back()->withErrors(['email' => 'Mã xác thực không hợp lệ hoặc đã hết hạn!']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        Password::getRepository()->delete($user);

        return redirect()->route('login')->with('status', 'Chúc mừng bạn đã đổi mật khẩu thành công!');
    }
}
