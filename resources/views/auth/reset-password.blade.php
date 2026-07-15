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
            font-size: 13px; 
            font-weight: 600;
            user-select: none; 
            padding: 4px;
            transition: color 0.2s;
        }

        .toggle-password:hover {
            color: #0054A6;
        }
    </style>
</head>
<body>
<div class="box">
    <div class="brand-title">mobi<span>fone</span></div>
    <div class="title">Đặt lại mật khẩu mới cho tài khoản</div>
    
    <form action="{{ route('password.update') }}" method="POST" autocomplete="off">
        @csrf
        
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email }}">

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
            <span class="toggle-password" onclick="toggleField('password', this)">Hiện</span>
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
            <span class="toggle-password" onclick="toggleField('password_confirmation', this)">Hiện</span>
        </div>

        <button type="submit" class="btn">CẬP NHẬT MẬT KHẨU</button>
    </form>
</div>

<script>
    function toggleField(id, el) {
        const input = document.getElementById(id);
        if (input.type === "password") {
            input.type = "text";
            el.innerText = "Ẩn";
        } else {
            input.type = "password";
            el.innerText = "Hiện";
        }
    }
</script>
</body>
</html>
