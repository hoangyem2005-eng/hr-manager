<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;  
use Illuminate\Support\Facades\Log; // <-- ĐÃ THÊM: Để ghi log link sạch không bị lỗi bẻ dòng
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // 1. Hiển thị form nhập email quên mật khẩu
    public function showForgotForm()
    {
        return view('layouts.auth.forgot-password');
    }

    // 2. Xử lý lưu token vào bảng password_resets và GHI LINK SẠCH VÀO LOG
    public function sendResetLinkEmail(Request $request)
    {
        // 1. Kiểm tra Email nhập vào có tồn tại trên hệ thống không
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Địa chỉ email này không tồn tại trên hệ thống.'
        ]);

        $email = $request->email;

        // 2. Sử dụng Broker mặc định của Laravel để tự tạo Token hợp lệ VÀ tự lưu vào DB
        $token = Password::getRepository()->create(
            \App\Models\User::where('email', $email)->first()
        );

        // 3. Tạo đường dẫn reset mật khẩu chuẩn dựa trên token vừa tạo
        $resetLink = route('password.reset', ['token' => $token, 'email' => $email]);

        // 4. GIẢI PHÁP TRIỆT ĐỂ: Ghi thẳng link vào log hệ thống (Bỏ qua bộ mã hóa Mailer)
        // Điều này giúp link hiển thị thẳng tuột, không bao giờ bị chèn thêm dấu '=' hay '=3D' nữa
        Log::info("=================================================================");
        Log::info("LINK_RESET_CUA_BAN_LA: " . $resetLink);
        Log::info("=================================================================");

        // 5. BẢO MẬT: Chỉ thông báo thành công ra màn hình giao diện
        return back()->with([
            'status' => 'MobiFone đã gửi liên kết đặt lại mật khẩu đến email của bạn. Vui lòng kiểm tra hộp thư!'
        ]);
    }

    // 3. Hiển thị form nhập mật khẩu mới khi user click từ link sạch trong file log
    public function showResetForm($token, Request $request)
    {
        // Lấy thông tin record dựa trên email của user
        $user = \App\Models\User::where('email', $request->email)->first();

        // CHUẨN HOÁ: Dùng hàm của Laravel kiểm tra token mã hóa khớp 100% với link thô trên trình duyệt
        if (!$user || !Password::getRepository()->exists($user, $token)) {
            return redirect()->route('password.request')->withErrors(['email' => 'Liên kết xác thực không hợp lệ hoặc đã hết hạn!']);
        }

        return view('layouts.auth.reset-password', ['token' => $token, 'email' => $request->email]);
    }

    // 4. Xử lý cập nhật mật khẩu mới vào bảng users
    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => 'required',
            'password' => 'required|string|min:8|confirmed', // Mật khẩu tối thiểu 8 ký tự và phải khớp với ô nhập lại
        ]);

        $user = \App\Models\User::where('email', $request->email)->first();

        // Xác thực lại token một lần nữa trước khi cập nhật dữ liệu vào DB
        if (!$user || !Password::getRepository()->exists($user, $request->token)) {
            return back()->withErrors(['email' => 'Mã xác thực không hợp lệ hoặc đã hết hạn!']);
        }

        // Cập nhật mật khẩu mới đã được mã hóa Hash vào bảng users
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Đổi mật khẩu xong thì xóa luôn token trong bảng password_resets để bảo mật an toàn
        Password::getRepository()->delete($user);

        return redirect()->route('login')->with('status', 'Chúc mừng bạn đã đổi mật khẩu thành công!');
    }
}