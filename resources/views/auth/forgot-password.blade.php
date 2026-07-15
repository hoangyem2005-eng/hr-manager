<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-Frame-Options" content="DENY">
    <title>Quên mật khẩu - MobiFone Style</title>
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
        
        .forgot-container { 
            background-color: #ffffff; 
            padding: 40px; 
            border-radius: 16px; 
            box-shadow: 0 10px 25px -5px rgba(0, 84, 166, 0.05), 0 8px 10px -6px rgba(0, 84, 166, 0.05); 
            width: 100%; 
            max-width: 420px; 
            border-top: 6px solid #0054A6; 
            position: relative;
        }

        .forgot-container::before {
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
            margin-bottom: 12px; 
            text-transform: uppercase; 
            letter-spacing: 1px;
        }
        
        .brand-title span { 
            color: #ED1C24; 
            text-transform: lowercase; 
            font-weight: 700;
        }
        
        .form-desc { 
            color: #64748b; 
            font-size: 14.5px; 
            text-align: center; 
            margin-bottom: 30px; 
            line-height: 1.6; 
        }
        
        .form-group { 
            margin-bottom: 24px; 
        }
        
        .form-group label { 
            display: block; 
            font-size: 14px; 
            color: #334155; 
            margin-bottom: 8px; 
            font-weight: 600; 
        }
        
        .form-control { 
            width: 100%; 
            padding: 13px 16px; 
            border: 1.5px solid #cbd5e1; 
            border-radius: 8px; 
            font-size: 15px; 
            color: #1e293b;
            background-color: #f8fafc;
            transition: all 0.25s ease; 
        }
        
        .form-control::placeholder {
            color: #94a3b8;
        }
        
        .form-control:focus { 
            outline: none; 
            border-color: #0054A6; 
            background-color: #ffffff;
            box-shadow: 0 0 0 4px rgba(0, 84, 166, 0.1); 
        }
        
        .btn-submit { 
            width: 100%; 
            background-color: #0054A6; 
            color: #ffffff; 
            border: none; 
            padding: 14px; 
            font-size: 15px; 
            font-weight: 700; 
            border-radius: 8px; 
            cursor: pointer; 
            letter-spacing: 0.5px;
            box-shadow: 0 4px 12px rgba(0, 84, 166, 0.15);
            transition: all 0.25s ease; 
        }
        
        .btn-submit:hover { 
            background-color: #004485; 
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(0, 84, 166, 0.25);
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        .alert-success { 
            background-color: #f0fdf4; 
            color: #166534; 
            padding: 14px; 
            border-radius: 8px; 
            font-size: 14px; 
            margin-bottom: 24px; 
            text-align: left; 
            border: 1px solid #bbf7d0; 
            line-height: 1.5;
        }

        .debug-link-box {
            background-color: #f8fafc;
            border: 1.5px dashed #0054A6;
            padding: 16px;
            border-radius: 10px;
            margin-bottom: 24px;
            text-align: left;
        }

        .debug-title {
            font-size: 12px;
            color: #0054A6;
            font-weight: 700;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }

        .debug-input {
            width: 100%;
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            padding: 10px 12px;
            border-radius: 6px;
            font-size: 12.5px;
            color: #334155;
            font-family: monospace;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.2s;
        }

        .debug-input:hover {
            border-color: #0054A6;
            background-color: #fff;
        }

        .debug-btn-test {
            display: block;
            text-align: center;
            background-color: #ED1C24;
            color: #ffffff;
            padding: 11px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 13.5px;
            font-weight: 700;
            box-shadow: 0 4px 10px rgba(237, 28, 36, 0.15);
            transition: all 0.2s ease;
        }

        .debug-btn-test:hover {
            background-color: #d11219;
            box-shadow: 0 6px 14px rgba(237, 28, 36, 0.25);
        }
        
        .error-message { 
            color: #dc2626; 
            font-size: 13px; 
            margin-top: 6px; 
            display: block; 
            font-weight: 500;
        }
        
        .back-to-login { 
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 25px; 
            font-size: 14px; 
            color: #64748b; 
            text-decoration: none; 
            font-weight: 500;
            transition: color 0.2s ease;
        }
        
        .back-to-login:hover { 
            color: #0054A6; 
        }

        .back-to-login svg {
            margin-right: 6px;
            transition: transform 0.2s ease;
        }

        .back-to-login:hover svg {
            transform: translateX(-3px);
        }
    </style>
</head>
<body>

<div class="forgot-container">
    <div class="brand-title">mobi<span>fone</span></div>
    <div class="form-desc">Nhập email của bạn để nhận liên kết thiết lập lại mật khẩu hệ thống.</div>
    
    @if (session('status'))
        <div class="alert-success">
            ✨ {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('password.email') }}" method="POST" autocomplete="off">
        @csrf
        
        <div class="form-group">
            <label for="email">Địa chỉ Email</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                class="form-control" 
                placeholder="nguyenvanb@mobifone.vn" 
                value="{{ old('email') }}" 
                required
                maxlength="255"
                autocomplete="email"
            >
            @error('email') 
                <span class="error-message">⚠️ {{ $message }}</span> 
            @enderror
        </div>

        <button type="submit" class="btn-submit">GỬI YÊU CẦU</button>
    </form>

    <a href="{{ route('login') }}" class="back-to-login">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        Quay lại trang đăng nhập
    </a>
</div>

</body>
</html>
