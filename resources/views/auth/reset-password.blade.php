<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <title>Đặt lại mật khẩu - MobiFone Style</title>
    <style>
        * { 
            box-sizing: border-box; 
            margin: 0; 
            padding: 0; 
            font-family: 'Segoe UI', -apple-system, BlinkMacSystemFont, Roboto, Helvetica, Arial, sans-serif; 
        }
        
        body { 
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            min-height: 100vh; 
            padding: 20px;
        }
        
        .box { 
            background: #ffffff; 
            padding: 40px; 
            border-radius: 16px; 
            width: 100%; 
            max-width: 420px; 
            box-shadow: 0 10px 25px -5px rgba(0, 84, 166, 0.05), 0 8px 10px -6px rgba(0, 84, 166, 0.05); 
            border-top: 6px solid #0054A6; 
            position: relative;
        }

        .box::before {
            content: "";
            position: absolute;
            top: -6px;
            right: 15%;
            width: 30px;
            height: 6px;
            background-color: #ED1C24;
        }
        
        .brand-title { 
            color: #0054A6; 
            font-size: 28px; 
            font-weight: 800; 
            text-align: center; 
            margin-bottom: 8px; 
            text-transform: uppercase; 
            letter-spacing: 1px;
        }
        
        .brand-title span { 
            color: #ED1C24; 
            text-transform: lowercase; 
            font-weight: 700;
        }
 
        .title { 
            color: #475569; 
            font-size: 15px; 
            font-weight: 500; 
            text-align: center; 
            margin-bottom: 30px; 
        }
        
        .group { 
            margin-bottom: 22px; 
            position: relative; 
        }
        
        .group label { 
            display: block; 
            font-size: 14px; 
            margin-bottom: 8px; 
            font-weight: 600; 
            color: #334155; 
        }
        
        .control { 
            width: 100%; 
            padding: 13px 60px 13px 16px; 
            border: 1.5px solid #cbd5e1; 
            border-radius: 8px; 
            font-size: 15px; 
            color: #1e293b;
            background-color: #f8fafc;
            transition: all 0.25s ease;
        }

        .control::placeholder {
            color: #94a3b8;
            font-size: 14px;
        }
        
        .control:focus { 
            outline: none; 
            border-color: #0054A6; 
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(0, 84, 166, 0.1); 
        }
        
        .btn { 
            width: 100%; 
            background: #0054A6; 
            color: #ffffff; 
            border: none; 
            padding: 14px; 
            font-weight: 700; 
            font-size: 15px; 
            border-radius: 8px; 
            cursor: pointer; 
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(0, 84, 166, 0.15);
            transition: all 0.25s ease; 
            margin-top: 10px; 
        }
        
        .btn:hover { 
            background: #004485; 
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 84, 166, 0.25);
        }

        .btn:active {
            transform: translateY(0);
        }
        
        .error { 
            color: #dc2626; 
            font-size: 13px; 
            margin-top: 6px; 
            font-weight: 500;
            display: flex;
            align-items: center;
        }
        
        .toggle-password { 
            position: absolute; 
            right: 16px; 
            top: 38px; 
            cursor: pointer; 
            color: #64748b; 
            user-select: none; 
            width: 30px;
            height: 30px;
            border: 0;
            background: transparent;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            background: #e2e8f0;
            color: #0054A6;
        }

        .toggle-password:focus-visible {
            outline: 2px solid rgba(0, 84, 166, 0.3);
            outline-offset: 2px;
        }
    </style>
</head>
<body>
<div class="box">
    <div class="brand-title">mobi<span>fone</span></div>
    <div class="title">Đặt lại mật khẩu mới cho tài khoản</div>
    
    @if (session('status'))
        <div style="background-color: #f0fdf4; color: #166534; padding: 14px; border-radius: 8px; font-size: 14px; margin-bottom: 24px; text-align: left; border: 1px solid #bbf7d0; line-height: 1.5;">
            ✨ {{ session('status') }}
        </div>
    @endif
    
    <form action="{{ route('password.update') }}" method="POST" autocomplete="off">
        @csrf
        
        <div class="group">
            <label for="email">Địa chỉ Email</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                class="control" 
                style="padding-right: 16px;"
                required 
                placeholder="ten.nguyen@mobifone.vn"
                value="{{ old('email', $email) }}"
            >
            @error('email') 
                <div class="error">⚠️ {{ $message }}</div> 
            @enderror
        </div>

        <div class="group">
            <label for="token">Mã xác thực (OTP)</label>
            <input 
                type="text" 
                id="token" 
                name="token" 
                class="control" 
                style="padding-right: 16px; font-weight: 700; letter-spacing: 2px; text-align: center;"
                required 
                placeholder="6 chữ số"
                maxlength="6"
                pattern="\d{6}"
                value="{{ old('token') }}"
            >
            @error('token') 
                <div class="error">⚠️ {{ $message }}</div> 
            @enderror
        </div>

        <div class="group">
            <label for="password">Mật khẩu mới</label>
            <input 
                type="password" 
                id="password"
                name="password" 
                class="control" 
                required 
                placeholder="Tối thiểu 8 ký tự"
                autocomplete="new-password"
                minlength="8"
            >
            <button type="button" class="toggle-password" onclick="toggleField('password', this)" aria-label="Hiện mật khẩu" title="Hiện mật khẩu"><i data-lucide="eye"></i></button>
            @error('password') 
                <div class="error">⚠️ {{ $message }}</div> 
            @enderror
        </div>

        <div class="group">
            <label for="password_confirmation">Xác nhận mật khẩu mới</label>
            <input 
                type="password" 
                id="password_confirmation"
                name="password_confirmation" 
                class="control" 
                required 
                placeholder="Nhập lại mật khẩu giống phía trên"
                autocomplete="new-password"
                minlength="8"
            >
            <button type="button" class="toggle-password" onclick="toggleField('password_confirmation', this)" aria-label="Hiện mật khẩu" title="Hiện mật khẩu"><i data-lucide="eye"></i></button>
        </div>

        <button type="submit" class="btn">CẬP NHẬT MẬT KHẨU</button>
    </form>
    
    <a href="{{ route('login') }}" style="display: flex; align-items: center; justify-content: center; margin-top: 25px; font-size: 14px; color: #64748b; text-decoration: none; font-weight: 500; transition: color 0.2s ease;">
        Quay lại trang đăng nhập
    </a>
</div>

<script src="https://unpkg.com/lucide@latest"></script>
<script>
    lucide.createIcons();

    function toggleField(id, el) {
        const input = document.getElementById(id);
        const icon = el.querySelector('i');
        const shouldShow = input.type === "password";

        input.type = shouldShow ? "text" : "password";
        icon.setAttribute('data-lucide', shouldShow ? 'eye-off' : 'eye');
        el.setAttribute('aria-label', shouldShow ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
        el.setAttribute('title', shouldShow ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
        lucide.createIcons();
    }
</script>
</body>
</html>
