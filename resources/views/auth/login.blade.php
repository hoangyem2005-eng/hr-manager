<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - MobiFone WorkHub</title>

    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])

    <style>
        :root {
            --mf-navy: #001F5B;
            --mf-blue: #003DA5;
            --mf-sky: #0B66D8;
            --mf-red: #E4002B;
            --mf-line: #D8E4F5;
            --mf-soft: #EEF5FF;
            --mf-ink: #10233F;
            --mf-muted: #7182A0;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Be Vietnam Pro', sans-serif;
            color: var(--mf-ink);
            background: #F5F8FD;
        }

        .auth-shell {
            min-height: 100vh;
            display: grid;
            grid-template-columns: minmax(0, 1.05fr) minmax(440px, .95fr);
            overflow: hidden;
        }

        .brand-panel {
            position: relative;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 56px;
            color: #fff;
            background:
                radial-gradient(circle at 76% 12%, rgba(255,255,255,.16), transparent 26%),
                linear-gradient(135deg, #001D58 0%, #003DA5 58%, #075FD0 100%);
        }

        .brand-panel::before {
            content: "";
            position: absolute;
            inset: 0;
            opacity: .36;
            background-image:
                linear-gradient(rgba(255,255,255,.09) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.09) 1px, transparent 1px);
            background-size: 46px 46px;
            animation: gridDrift 22s linear infinite;
        }

        .brand-panel::after {
            content: "";
            position: absolute;
            width: 720px;
            height: 720px;
            right: -250px;
            bottom: -260px;
            border-radius: 50%;
            border: 98px solid rgba(255,255,255,.08);
            animation: ringBreathe 8s ease-in-out infinite;
        }

        .brand-content {
            position: relative;
            z-index: 2;
            width: min(610px, 100%);
        }

        .brand-lockup {
            display: inline-flex;
            align-items: center;
            gap: 14px;
            padding: 10px;
            border-radius: 8px;
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.16);
            backdrop-filter: blur(10px);
            animation: floatIn 700ms ease both;
        }

        .brand-mark {
            position: relative;
            overflow: hidden;
            width: 58px;
            height: 58px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            color: var(--mf-blue);
            background: #fff;
            font-size: 27px;
            font-weight: 900;
            box-shadow: inset 7px 0 0 var(--mf-red), 0 18px 36px rgba(0,0,0,.18);
            animation: logoHop 3.8s ease-in-out infinite;
        }

        .brand-mark::after,
        .brand-word::after {
            content: "";
            position: absolute;
            inset: -45% auto -45% -55%;
            width: 42%;
            transform: rotate(18deg);
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.9), transparent);
            animation: shine 4.4s ease-in-out infinite;
        }

        .brand-word {
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: baseline;
            padding: 6px 13px;
            border-radius: 7px;
            background: #fff;
            line-height: 1;
            box-shadow: 0 14px 28px rgba(0,0,0,.12);
        }

        .brand-word strong { font-size: 25px; font-weight: 900; letter-spacing: -.04em; }
        .brand-word .blue { color: var(--mf-blue); }
        .brand-word .red { color: var(--mf-red); }
        .brand-sub { margin-top: 8px; color: #D3E5FF; font-size: 11px; font-weight: 900; letter-spacing: .22em; }

        .brand-title {
            margin: 42px 0 0;
            max-width: 760px;
            font-size: clamp(42px, 5.3vw, 74px);
            line-height: 1.14;
            font-weight: 900;
            letter-spacing: 0;
        }

        .brand-title span {
            position: relative;
            display: block;
            padding: .08em 0 .18em;
            overflow: visible;
            animation: titleRise 800ms ease both;
        }

        .brand-title span:nth-child(2) { animation-delay: 90ms; }
        .brand-title span:nth-child(3) { animation-delay: 180ms; }
        .brand-title .accent::after {
            content: "";
            position: absolute;
            left: 0;
            right: 22%;
            bottom: .24em;
            height: .24em;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--mf-red), rgba(11,102,216,.96));
            z-index: -1;
            transform-origin: left;
            animation: markerSweep 1.2s .35s ease both;
        }

        .brand-desc {
            margin-top: 18px;
            max-width: 560px;
            color: #DDEBFF;
            font-size: 16px;
            line-height: 1.75;
        }

        .orbit {
            position: absolute;
            z-index: 3;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 9px 12px;
            border-radius: 999px;
            color: #EAF3FF;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.18);
            font-size: 12px;
            font-weight: 900;
            backdrop-filter: blur(10px);
            box-shadow: 0 18px 36px rgba(0,0,0,.14);
            animation: chipFloat 6s ease-in-out infinite;
        }

        .orbit i { width: 16px; height: 16px; }
        .orbit.one { top: 18%; right: 11%; }
        .orbit.two { top: 56%; right: 8%; animation-delay: -2s; }
        .orbit.three { top: 29%; right: 8%; animation-delay: -3.6s; }

        .mini-board {
            margin-top: 28px;
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 10px;
            max-width: 540px;
        }

        .mini-card {
            min-height: 112px;
            padding: 17px 18px 18px;
            border-radius: 8px;
            background: rgba(255,255,255,.13);
            border: 1px solid rgba(255,255,255,.18);
            backdrop-filter: blur(12px);
            animation: cardPop 650ms ease both;
        }

        .mini-card:nth-child(2) { animation-delay: 100ms; }
        .mini-card:nth-child(3) { animation-delay: 200ms; }
        .mini-card b { display: block; color: #fff; font-size: 26px; line-height: 1.18; }
        .mini-card span { display: block; margin-top: 8px; padding-bottom: 2px; color: #CFE2FF; font-size: 11px; line-height: 1.55; font-weight: 900; text-transform: uppercase; }
        .mini-line { margin-top: 10px; height: 5px; border-radius: 999px; background: rgba(255,255,255,.16); overflow: hidden; }
        .mini-line i { display: block; height: 100%; border-radius: inherit; background: var(--mf-red); transform-origin: left; animation: growBar 1.2s ease both; }

        .form-panel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 42px;
            background:
                radial-gradient(circle at 16% 16%, #EAF2FF, transparent 30%),
                linear-gradient(180deg, #FFFFFF, #F7FAFF);
        }

        .form-panel::before {
            content: "";
            position: absolute;
            inset: 28px;
            border: 1px solid rgba(0,61,165,.08);
            border-radius: 8px;
            pointer-events: none;
        }

        .auth-card {
            position: relative;
            z-index: 1;
            width: min(520px, 100%);
            padding: 28px;
            border-radius: 8px;
            background: rgba(255,255,255,.82);
            border: 1px solid rgba(216,228,245,.95);
            box-shadow: 0 24px 70px rgba(0,31,91,.12);
            backdrop-filter: blur(14px);
        }

        .form-badge {
            width: 64px;
            height: 64px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            color: #fff;
            background: linear-gradient(135deg, var(--mf-blue), var(--mf-sky));
            box-shadow: inset 6px 0 0 var(--mf-red), 0 16px 30px rgba(0,61,165,.22);
            margin: 0 auto 18px;
            animation: badgePulse 4s ease-in-out infinite;
        }

        .form-title {
            margin: 0;
            color: var(--mf-navy);
            font-size: 34px;
            line-height: 1.2;
            text-align: center;
            font-weight: 900;
        }

        .form-desc { margin: 10px 0 24px; color: #8290AA; text-align: center; font-size: 14px; }
        .alert { display: flex; gap: 9px; align-items: flex-start; padding: 12px; border-radius: 8px; margin-bottom: 14px; font-size: 13px; font-weight: 700; }
        .alert.error { color: #B91C1C; background: #FEF2F2; border: 1px solid #FECACA; }
        .alert.success { color: #15803D; background: #ECFDF5; border: 1px solid #BBF7D0; }
        .field { margin-bottom: 16px; }
        .field label { display: block; margin-bottom: 8px; color: #1E293B; font-size: 13px; font-weight: 900; }
        .input-wrap { position: relative; }
        .input-wrap > i,
        .input-wrap > svg:not(.lucide-eye):not(.lucide-eye-off) {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: #8CA0BD;
            pointer-events: none;
        }

        .input {
            width: 100%;
            min-height: 54px;
            border: 1px solid var(--mf-line);
            border-radius: 8px;
            background: #F8FBFF;
            padding: 0 14px 0 46px;
            color: #0F172A;
            font-size: 15px;
            outline: none;
            transition: border-color .18s ease, box-shadow .18s ease, background .18s ease, transform .18s ease;
        }

        .input:focus {
            background: #fff;
            border-color: var(--mf-blue);
            box-shadow: 0 0 0 4px rgba(0,61,165,.11);
            transform: translateY(-1px);
        }

        .password-toggle {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 36px;
            height: 36px;
            border: 0;
            border-radius: 8px;
            background: transparent;
            color: #8CA0BD;
            display: grid;
            place-items: center;
            cursor: pointer;
        }

        .password-toggle:hover { color: var(--mf-blue); background: var(--mf-soft); }
        .row { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin: 4px 0 18px; }
        .remember { display: inline-flex; align-items: center; gap: 8px; color: #334155; font-size: 13px; font-weight: 700; }
        .remember input { width: 15px; height: 15px; accent-color: var(--mf-blue); }
        .link { color: var(--mf-blue); font-size: 13px; font-weight: 900; text-decoration: none; }
        .link:hover { color: var(--mf-sky); text-decoration: underline; }

        .btn {
            width: 100%;
            min-height: 56px;
            border: 0;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            font-size: 15px;
            font-weight: 900;
            cursor: pointer;
            text-decoration: none;
            transition: transform .18s ease, background .18s ease, box-shadow .18s ease;
        }

        .btn:hover { transform: translateY(-2px); }
        .btn-primary {
            color: #fff;
            background: linear-gradient(135deg, var(--mf-blue), var(--mf-sky));
            box-shadow: 0 18px 34px rgba(0,61,165,.22);
        }
        .btn-primary:hover { box-shadow: 0 22px 38px rgba(0,61,165,.28); }
        .btn-ghost { margin-top: 12px; color: #334155; background: #fff; border: 1px solid var(--mf-line); }
        .btn-ghost:hover { background: #F8FBFF; }
        .switch-auth { margin-top: 24px; color: #8A98B0; text-align: center; font-size: 14px; }
        .copyright { margin-top: 34px; color: #94A3B8; text-align: center; font-size: 12px; }

        @keyframes gridDrift { from { background-position: 0 0, 0 0; } to { background-position: 92px 46px, 92px 46px; } }
        @keyframes ringBreathe { 0%, 100% { transform: scale(1); opacity: .85; } 50% { transform: scale(1.05); opacity: .55; } }
        @keyframes logoHop { 0%, 100% { transform: translateY(0) rotate(0deg); } 42% { transform: translateY(-7px) rotate(-2deg); } 58% { transform: translateY(-4px) rotate(2deg); } }
        @keyframes shine { 0%, 62% { left: -55%; } 78%, 100% { left: 118%; } }
        @keyframes floatIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes titleRise { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes markerSweep { from { transform: scaleX(0); } to { transform: scaleX(1); } }
        @keyframes chipFloat { 0%, 100% { transform: translate3d(0,0,0); } 50% { transform: translate3d(0,-14px,0); } }
        @keyframes cardPop { from { opacity: 0; transform: translateY(14px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes growBar { from { transform: scaleX(.18); } to { transform: scaleX(1); } }
        @keyframes badgePulse { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }

        @media (max-width: 1100px) {
            .auth-shell { grid-template-columns: 1fr; }
            .brand-panel { min-height: 560px; padding: 36px 24px; }
            .form-panel { padding: 30px 18px; }
            .orbit { display: none; }
        }

        @media (max-width: 680px) {
            .brand-panel { min-height: auto; align-items: flex-start; }
            .brand-title { font-size: 36px; }
            .mini-board { grid-template-columns: 1fr; }
            .auth-card { padding: 22px; }
            .form-title { font-size: 28px; }
            .row { align-items: flex-start; flex-direction: column; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>
    <main class="auth-shell">
        <section class="brand-panel" aria-label="MobiFone WorkHub">
            <div class="orbit one"><i data-lucide="sparkles"></i> Đồng bộ nhóm</div>
            <div class="orbit two"><i data-lucide="bell-ring"></i> Nhắc deadline</div>
            <div class="orbit three"><i data-lucide="paperclip"></i> Tài liệu theo việc</div>

            <div class="brand-content">
                <div class="brand-lockup">
                    <div>
                        <div class="brand-word"><strong class="blue">mob<span class="brand-i">ı</span></strong><strong class="red">fone</strong></div>
                        <div class="brand-sub">WORKHUB</div>
                    </div>
                </div>

                <h1 class="brand-title">
                    <span>Đăng nhập</span>
                    <span class="accent">vào WorkHub</span>
                    <span>thật nhanh.</span>
                </h1>
                <p class="brand-desc">Một màn hình điều phối gọn gàng cho công việc, phòng ban, tài liệu và thông báo nội bộ.</p>

                <div class="mini-board" aria-label="Tổng quan vận hành">
                    <div class="mini-card"><b>142</b><span>Công việc</span><div class="mini-line"><i style="width: 82%; background:#0B66D8"></i></div></div>
                    <div class="mini-card"><b>58</b><span>Hoàn thành</span><div class="mini-line"><i style="width: 72%; background:#22C55E"></i></div></div>
                    <div class="mini-card"><b>12</b><span>Quá hạn</span><div class="mini-line"><i style="width: 18%; background:#E4002B"></i></div></div>
                </div>
            </div>
        </section>

        <section class="form-panel">
            <div class="auth-card">
                <div class="form-badge"><i data-lucide="log-in" class="w-8 h-8"></i></div>
                <h2 class="form-title">Chào mừng trở lại</h2>
                <p class="form-desc">Đăng nhập để tiếp tục vào MobiFone WorkHub.</p>

                @if(session('error'))
                    <div class="alert error"><i data-lucide="alert-circle" class="w-4 h-4"></i><span>{{ session('error') }}</span></div>
                @endif
                @if(session('success'))
                    <div class="alert success"><i data-lucide="check-circle" class="w-4 h-4"></i><span>{{ session('success') }}</span></div>
                @endif
                @if($errors->any())
                    <div class="alert error">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                        <div>@foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach</div>
                    </div>
                @endif

                <form action="{{ url('/login') }}" method="POST" autocomplete="off">
                    @csrf
                    <div class="field">
                        <label for="email">Email công ty</label>
                        <div class="input-wrap">
                            <i data-lucide="mail"></i>
                            <input id="email" class="input" type="email" name="email" value="{{ old('email') }}" placeholder="ten.nguyen@mobifone.vn" required autocomplete="off">
                        </div>
                    </div>

                    <div class="field">
                        <label for="password">Mật khẩu</label>
                        <div class="input-wrap">
                            <i data-lucide="lock"></i>
                            <input id="password" class="input" type="password" name="password" placeholder="••••••••" required style="padding-right:52px" autocomplete="new-password">
                            <button type="button" class="password-toggle" data-target="password" aria-label="Hiện mật khẩu" title="Hiện mật khẩu"><i data-lucide="eye"></i></button>
                        </div>
                    </div>

                    <div class="row">
                        <label class="remember"><input type="checkbox" name="remember"> Ghi nhớ đăng nhập</label>
                        <a href="{{ route('password.request') }}" class="link">Quên mật khẩu?</a>
                    </div>

                    <button type="submit" class="btn btn-primary">Đăng nhập <i data-lucide="arrow-right"></i></button>
                    <a href="{{ route('landing') }}" class="btn btn-ghost"><i data-lucide="chevron-left"></i> Quay lại trang chủ</a>
                </form>

                <div class="switch-auth">Chưa có tài khoản? <a href="{{ route('register') }}" class="link">Đăng ký ngay</a></div>
                <div class="copyright">© 2026 MobiFone WorkHub · v2.4.1</div>
            </div>
        </section>
    </main>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
        document.querySelectorAll('.password-toggle').forEach((button) => {
            button.addEventListener('click', () => {
                const input = document.getElementById(button.dataset.target);
                const icon = button.querySelector('i');
                const show = input.type === 'password';
                input.type = show ? 'text' : 'password';
                icon.setAttribute('data-lucide', show ? 'eye-off' : 'eye');
                button.setAttribute('aria-label', show ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
                button.setAttribute('title', show ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
                lucide.createIcons();
            });
        });

        // Tự động xóa thông tin đăng nhập tự động điền (autofill) của trình duyệt khi mở trang
        document.addEventListener('DOMContentLoaded', () => {
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            @if(!old('email'))
                if (emailInput) emailInput.value = '';
            @endif
            if (passwordInput) passwordInput.value = '';

            setTimeout(() => {
                @if(!old('email'))
                    if (emailInput) emailInput.value = '';
                @endif
                if (passwordInput) passwordInput.value = '';
            }, 100);
        });
    </script>
</body>
</html>
