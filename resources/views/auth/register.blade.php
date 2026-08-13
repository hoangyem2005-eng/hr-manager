<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - MobiFone WorkHub</title>

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
            grid-template-columns: minmax(0, .92fr) minmax(560px, 1.08fr);
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
                radial-gradient(circle at 22% 18%, rgba(255,255,255,.16), transparent 27%),
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
            width: 620px;
            height: 620px;
            left: -250px;
            bottom: -240px;
            border-radius: 50%;
            border: 90px solid rgba(255,255,255,.08);
            animation: ringBreathe 8s ease-in-out infinite;
        }

        .brand-content { position: relative; z-index: 2; width: min(540px, 100%); }
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
        .brand-title { margin: 38px 0 0; font-size: clamp(38px, 4.8vw, 62px); line-height: 1.14; font-weight: 900; letter-spacing: 0; }
        .brand-title span { position: relative; display: block; padding: .08em 0 .18em; overflow: visible; animation: titleRise 800ms ease both; }
        .brand-title span:nth-child(2) { animation-delay: 90ms; }
        .brand-title .accent::after {
            content: "";
            position: absolute;
            left: 0;
            right: 18%;
            bottom: .24em;
            height: .23em;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--mf-red), rgba(11,102,216,.96));
            z-index: -1;
            transform-origin: left;
            animation: markerSweep 1.2s .35s ease both;
        }

        .brand-desc { margin-top: 18px; color: #DDEBFF; font-size: 15px; line-height: 1.75; }
        .steps { display: grid; gap: 12px; margin-top: 28px; }
        .step {
            display: grid;
            grid-template-columns: 42px 1fr;
            gap: 12px;
            align-items: center;
            padding: 14px;
            border-radius: 8px;
            background: rgba(255,255,255,.12);
            border: 1px solid rgba(255,255,255,.18);
            backdrop-filter: blur(12px);
            animation: cardPop 650ms ease both;
        }
        .step:nth-child(2) { animation-delay: 90ms; }
        .step:nth-child(3) { animation-delay: 180ms; }
        .step b { width: 42px; height: 42px; border-radius: 8px; display: grid; place-items: center; color: #fff; background: var(--mf-red); }
        .step strong { display: block; font-size: 13px; }
        .step span { display: block; margin-top: 4px; color: #CFE2FF; font-size: 11px; line-height: 1.5; }

        .form-panel {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 42px;
            background:
                radial-gradient(circle at 84% 12%, #EAF2FF, transparent 30%),
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
            width: min(720px, 100%);
            padding: 28px;
            border-radius: 8px;
            background: rgba(255,255,255,.84);
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
            margin-bottom: 18px;
            animation: badgePulse 4s ease-in-out infinite;
        }

        .form-title { margin: 0; color: var(--mf-navy); font-size: 34px; line-height: 1.2; font-weight: 900; }
        .form-desc { margin: 10px 0 24px; color: #8290AA; font-size: 14px; }
        .alert { display: flex; gap: 9px; align-items: flex-start; padding: 12px; border-radius: 8px; margin-bottom: 14px; color: #B91C1C; background: #FEF2F2; border: 1px solid #FECACA; font-size: 13px; font-weight: 700; }
        .grid-2 { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .field { margin-bottom: 15px; }
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
        select.input { appearance: none; }
        .input:focus { background: #fff; border-color: var(--mf-blue); box-shadow: 0 0 0 4px rgba(0,61,165,.11); transform: translateY(-1px); }
        .password-toggle { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); width: 36px; height: 36px; border: 0; border-radius: 8px; background: transparent; color: #8CA0BD; display: grid; place-items: center; cursor: pointer; }
        .password-toggle:hover { color: var(--mf-blue); background: var(--mf-soft); }
        .role-note { display: flex; align-items: center; gap: 9px; min-height: 54px; border: 1px solid var(--mf-line); border-radius: 8px; background: #F8FBFF; padding: 0 14px; color: #334155; font-size: 14px; font-weight: 800; }
        .role-note i { color: var(--mf-blue); }
        .btn { width: 100%; min-height: 56px; border: 0; border-radius: 8px; display: inline-flex; align-items: center; justify-content: center; gap: 10px; color: #fff; background: linear-gradient(135deg, var(--mf-blue), var(--mf-sky)); font-size: 15px; font-weight: 900; cursor: pointer; transition: transform .18s ease, box-shadow .18s ease; box-shadow: 0 18px 34px rgba(0,61,165,.22); }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 22px 38px rgba(0,61,165,.28); }
        .switch-auth { margin-top: 22px; color: #8A98B0; text-align: center; font-size: 14px; }
        .link { color: var(--mf-blue); font-weight: 900; text-decoration: none; }
        .link:hover { color: var(--mf-sky); text-decoration: underline; }
        .back-home { display: inline-flex; align-items: center; gap: 7px; margin-top: 12px; color: #64748B; font-size: 13px; font-weight: 800; text-decoration: none; }
        .back-home:hover { color: var(--mf-blue); }

        @keyframes gridDrift { from { background-position: 0 0, 0 0; } to { background-position: 92px 46px, 92px 46px; } }
        @keyframes ringBreathe { 0%, 100% { transform: scale(1); opacity: .85; } 50% { transform: scale(1.05); opacity: .55; } }
        @keyframes logoHop { 0%, 100% { transform: translateY(0) rotate(0deg); } 42% { transform: translateY(-7px) rotate(-2deg); } 58% { transform: translateY(-4px) rotate(2deg); } }
        @keyframes shine { 0%, 62% { left: -55%; } 78%, 100% { left: 118%; } }
        @keyframes floatIn { from { opacity: 0; transform: translateY(12px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes titleRise { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes markerSweep { from { transform: scaleX(0); } to { transform: scaleX(1); } }
        @keyframes cardPop { from { opacity: 0; transform: translateY(14px) scale(.98); } to { opacity: 1; transform: translateY(0) scale(1); } }
        @keyframes badgePulse { 0%, 100% { transform: translateY(0); } 50% { transform: translateY(-4px); } }

        @media (max-width: 1100px) {
            .auth-shell { grid-template-columns: 1fr; }
            .brand-panel { min-height: 520px; padding: 36px 24px; }
            .form-panel { padding: 30px 18px; }
        }

        @media (max-width: 720px) {
            .brand-panel { min-height: auto; align-items: flex-start; }
            .brand-title { font-size: 34px; }
            .steps { display: none; }
            .auth-card { padding: 22px; }
            .grid-2 { grid-template-columns: 1fr; }
            .form-title { font-size: 28px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; transition-duration: .01ms !important; }
        }
    </style>
</head>
<body>
    <main class="auth-shell">
        <section class="brand-panel" aria-label="MobiFone WorkHub">
            <div class="brand-content">
                <div class="brand-lockup">
                    <div>
                        <div class="brand-word"><strong class="blue">mob<span class="brand-i">ı</span></strong><strong class="red">fone</strong></div>
                        <div class="brand-sub">WORKHUB</div>
                    </div>
                </div>

                <h1 class="brand-title">
                    <span>Tạo tài khoản</span>
                    <span class="accent">để vào đội.</span>
                </h1>
                <p class="brand-desc">Tài khoản đăng ký công khai mặc định là Nhân viên. Trưởng phòng hoặc Giám đốc sẽ được phân quyền bởi quản trị nội bộ.</p>

                <div class="steps">
                    <div class="step"><b>1</b><div><strong>Nhập thông tin</strong><span>Họ tên, email công ty và phòng ban</span></div></div>
                    <div class="step"><b>2</b><div><strong>Tạo mật khẩu</strong><span>Mật khẩu cá nhân cho tài khoản WorkHub</span></div></div>
                    <div class="step"><b>3</b><div><strong>Bắt đầu phối hợp</strong><span>Theo dõi công việc được giao ngay sau đăng nhập</span></div></div>
                </div>
            </div>
        </section>

        <section class="form-panel">
            <div class="auth-card">
                <div class="form-badge"><i data-lucide="user-plus" class="w-8 h-8"></i></div>
                <h2 class="form-title">Đăng ký tài khoản</h2>
                <p class="form-desc">Tạo tài khoản Nhân viên để tham gia MobiFone WorkHub.</p>

                @if ($errors->any())
                    <div class="alert">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                        <div>@foreach($errors->all() as $error)<div>{{ $error }}</div>@endforeach</div>
                    </div>
                @endif

                <form method="POST" action="{{ url('/register') }}">
                    @csrf
                    <div class="grid-2">
                        <div class="field">
                            <label for="name">Họ và tên</label>
                            <div class="input-wrap"><i data-lucide="user"></i><input id="name" class="input" type="text" name="name" value="{{ old('name') }}" required placeholder="Nguyễn Văn An"></div>
                        </div>
                        <div class="field">
                            <label for="email">Email công ty</label>
                            <div class="input-wrap"><i data-lucide="mail"></i><input id="email" class="input" type="email" name="email" value="{{ old('email') }}" required placeholder="an.nguyen@mobifone.vn"></div>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label for="department_id">Phòng ban</label>
                            <div class="input-wrap">
                                <i data-lucide="building-2"></i>
                                <select id="department_id" name="department_id" required class="input">
                                    <option value="">Chọn phòng ban</option>
                                    @foreach($departments as $d)
                                        <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->TENPHONG ?? $d->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="field">
                            <label>Chức vụ</label>
                            <input type="hidden" name="role_id" value="{{ \App\Models\User::ROLE_EMPLOYEE }}">
                            <div class="role-note"><i data-lucide="badge-check"></i> Nhân viên</div>
                        </div>
                    </div>

                    <div class="grid-2">
                        <div class="field">
                            <label for="password">Mật khẩu</label>
                            <div class="input-wrap">
                                <i data-lucide="lock"></i>
                                <input id="password" class="input" type="password" name="password" required placeholder="••••••••" style="padding-right:52px">
                                <button type="button" class="password-toggle" data-target="password" aria-label="Hiện mật khẩu" title="Hiện mật khẩu"><i data-lucide="eye"></i></button>
                            </div>
                        </div>
                        <div class="field">
                            <label for="password_confirmation">Xác nhận mật khẩu</label>
                            <div class="input-wrap">
                                <i data-lucide="lock-keyhole"></i>
                                <input id="password_confirmation" class="input" type="password" name="password_confirmation" required placeholder="••••••••" style="padding-right:52px">
                                <button type="button" class="password-toggle" data-target="password_confirmation" aria-label="Hiện mật khẩu" title="Hiện mật khẩu"><i data-lucide="eye"></i></button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn">Đăng ký tài khoản <i data-lucide="arrow-right"></i></button>
                </form>

                <div class="switch-auth">Đã có tài khoản? <a href="{{ route('login') }}" class="link">Đăng nhập ngay</a></div>
                <a href="{{ route('landing') }}" class="back-home"><i data-lucide="chevron-left" class="w-4 h-4"></i> Quay lại trang chủ</a>
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
    </script>
</body>
</html>
