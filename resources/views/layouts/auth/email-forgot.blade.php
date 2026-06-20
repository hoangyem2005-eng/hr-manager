<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Khôi phục mật khẩu </title>
</head>
<body style="font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; margin: 0; padding: 50px 20px; -webkit-font-smoothing: antialiased;">
    <div style="max-width: 560px; background: #ffffff; margin: 0 auto; border-radius: 12px; border-top: 6px solid #0054A6; box-shadow: 0 4px 20px rgba(0, 84, 166, 0.05); overflow: hidden;">
        
        <!-- Main Content Container -->
        <div style="padding: 40px 40px 30px 40px;">
            
            <!-- Logo Brand -->
            <div style="text-align: center; margin-bottom: 35px;">
                <h2 style="color: #0054A6; font-size: 26px; font-weight: 800; letter-spacing: 1.5px; margin: 0; font-family: 'Helvetica Neue', Arial, sans-serif;">
                    MOBI<span style="color: #ED1C24; text-transform: lowercase; font-weight: 700;">fone</span>
                </h2>
            </div>
            
            <!-- Greeting & Body -->
            <p style="font-size: 16px; color: #1e293b; line-height: 1.6; margin-top: 0; margin-bottom: 12px; font-weight: 600;">Xin chào,</p>
            <p style="font-size: 15px; color: #334155; line-height: 1.6; margin-bottom: 12px;">Hệ thống quản lý nhân sự vừa nhận được yêu cầu thay đổi mật khẩu từ tài khoản của bạn.</p>
            <p style="font-size: 15px; color: #334155; line-height: 1.6; margin-bottom: 30px;">Vui lòng bấm vào nút dưới đây để tiến hành thiết lập mật khẩu mới. <span style="color: #dc2626; font-weight: 500;">(Liên kết này chỉ có giá trị trong vòng 60 phút)</span>:</p>
            
            <!-- Action Button -->
            <div style="text-align: center; margin: 35px 0;">
                <a href="{{ route('password.reset', ['token' => $token]) }}" 
                   style="background-color: #0054A6; color: #ffffff; padding: 14px 35px; text-decoration: none; border-radius: 6px; font-weight: 600; display: inline-block; font-size: 15px; letter-spacing: 0.5px; box-shadow: 0 4px 12px rgba(0, 84, 166, 0.2); transition: all 0.3s ease;">
                   ĐẶT LẠI MẬT KHẨU
                </a>
            </div>
            
            <!-- Security Notice -->
            <div style="background-color: #fff1f2; padding: 14px 18px; border-left: 4px solid #ED1C24; border-radius: 4px; margin-top: 35px;">
                <p style="color: #9f1239; font-size: 13px; line-height: 1.5; margin: 0;">
                    <strong>An toàn thông tin:</strong> Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email. Tài khoản của bạn vẫn sẽ được bảo mật an toàn.
                </p>
            </div>
            
        </div>
        
        <!-- Divider -->
        <hr style="border: none; border-top: 1px solid #f1f5f9; margin: 0;">
        
        <!-- Footer -->
        <div style="background-color: #fafafa; padding: 25px 40px; text-align: center;">
            <p style="font-size: 13px; color: #64748b; font-weight: 600; margin-top: 0; margin-bottom: 6px;">
                Hệ thống Quản lý Nhân sự HR-Manager
            </p>
            <p style="font-size: 12px; color: #94a3b8; line-height: 1.4; margin: 0;">
                © Tổng công ty Viễn thông MobiFone. <br>
                Đây là email tự động từ hệ thống, vui lòng không phản hồi lại email này.
            </p>
        </div>
        
    </div>
</body>
</html>