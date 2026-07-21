<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    // 1. Hiển thị form nhập email quên mật khẩu
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    // 2. Xử lý lưu mã xác thực (OTP) vào bảng password_resets và gửi email.
    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email'
        ], [
            'email.exists' => 'Địa chỉ email này không tồn tại trên hệ thống.'
        ]);

        $email = $request->email;

        // Tạo mã xác thực ngẫu nhiên 6 chữ số
        $otp = (string) random_int(100000, 999999);
        $expiresAt = Carbon::now()->addMinutes(19);

        // Lưu mã xác thực vào DB
        DB::table('password_resets')->updateOrInsert(
            ['email' => $email],
            [
                'token' => $otp,
                'created_at' => Carbon::now(),
                'expires_at' => $expiresAt,
                'used_at' => null,
            ]
        );

        // Gửi email HTML thực tế
        try {
            Mail::send([], [], function ($message) use ($email, $otp) {
                $message->to($email)
                        ->subject('Mã xác thực đặt lại mật khẩu - MobiFone WorkHub')
                        ->html("
                            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px;'>
                                <div style='text-align: center; margin-bottom: 20px;'>
                                    <h2 style='color: #0054A6; margin: 0;'>MobiFone <span style='color: #ED1C24;'>WorkHub</span></h2>
                                </div>
                                <div style='background-color: #f8fafc; padding: 20px; border-radius: 6px; border-left: 4px solid #0054A6;'>
                                    <p style='font-size: 16px; color: #334155; margin-top: 0;'>Xin chào,</p>
                                    <p style='font-size: 15px; color: #475569; line-height: 1.5;'>Bạn đã yêu cầu đặt lại mật khẩu cho tài khoản tại MobiFone WorkHub. Dưới đây là mã xác thực của bạn:</p>
                                    <div style='text-align: center; margin: 25px 0;'>
                                        <span style='display: inline-block; font-family: monospace; font-size: 32px; font-weight: 700; color: #0054A6; letter-spacing: 5px; background: #e2e8f0; padding: 10px 20px; border-radius: 6px;'>{$otp}</span>
                                    </div>
                                    <p style='font-size: 14px; color: #64748b;'>Mã xác thực này có hiệu lực trong vòng 19 phút. Vui lòng không chia sẻ mã này với bất kỳ ai.</p>
                                </div>
                                <hr style='border: 0; border-top: 1px solid #e2e8f0; margin: 20px 0;'>
                                <p style='font-size: 12px; color: #94a3b8; text-align: center;'>Email này được gửi tự động từ hệ thống MobiFone WorkHub. Vui lòng không trả lời email này.</p>
                            </div>
                        ");
            });
        } catch (\Exception $e) {
            report($e);

            return back()
                ->withInput()
                ->withErrors([
                    'email' => 'Chưa gửi được mã OTP vì hệ thống email chưa được cấu hình đúng. Vui lòng kiểm tra MAIL_HOST/SMTP rồi thử lại.',
                ]);
        }

        return redirect()->route('password.reset', ['email' => $email])->with([
            'status' => 'Mã xác thực OTP đã được gửi đến email của bạn. Vui lòng kiểm tra hộp thư!'
        ]);
    }

    // 3. Hiển thị form nhập mã xác thực và mật khẩu mới
    public function showResetForm(Request $request)
    {
        $email = $request->email;
        return view('auth.reset-password', ['email' => $email]);
    }

    // 4. Xử lý cập nhật mật khẩu mới vào bảng users
    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
            'token' => ['required', 'string', 'size:6', 'regex:/^\d{6}$/'],
            'password' => 'required|string|min:8|confirmed',
        ], [
            'email.required' => 'Vui lòng nhập email.',
            'email.exists' => 'Email không tồn tại trên hệ thống.',
            'token.required' => 'Vui lòng nhập mã xác thực OTP.',
            'token.size' => 'Mã xác thực OTP phải gồm 6 chữ số.',
            'password.required' => 'Vui lòng nhập mật khẩu mới.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        // Kiểm tra OTP trong bảng password_resets
        $record = DB::table('password_resets')
            ->where('email', $request->email)
            ->first();

        if (!$record || $record->used_at || $record->token !== $request->token) {
            return back()->withErrors(['token' => 'Mã xác thực OTP không chính xác!'])->withInput();
        }

        // Kiểm tra hết hạn (19 phút)
        $expiresAt = $record->expires_at
            ? Carbon::parse($record->expires_at)
            : Carbon::parse($record->created_at)->addMinutes(19);

        if ($expiresAt->isPast()) {
            return back()->withErrors(['token' => 'Mã xác thực OTP đã hết hạn!'])->withInput();
        }

        $user = \App\Models\User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // Đánh dấu OTP đã sử dụng để không thể dùng lại.
        DB::table('password_resets')
            ->where('email', $request->email)
            ->update(['used_at' => Carbon::now()]);

        return redirect()->route('login')->with('success', 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập bằng mật khẩu mới.');
    }
}
