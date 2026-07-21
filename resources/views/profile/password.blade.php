<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đổi mật khẩu - MobiFone WorkHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { min-height: 100vh; font-family: "Be Vietnam Pro", sans-serif; color: #001F5B; background: #F2F6FC; }
        .page { min-height: 100vh; display: grid; place-items: center; padding: 34px; }
        .card { width: min(720px, 100%); overflow: hidden; border: 1px solid #B9CDF5; border-radius: 10px; background: #fff; box-shadow: inset 5px 0 0 #E4002B; }
        .head { padding: 26px; border-bottom: 1px solid #E5EDF8; background: linear-gradient(135deg, #fff 0%, #F4F8FF 68%, #FFF3F6 100%); }
        .brand { display: flex; align-items: center; gap: 12px; margin-bottom: 22px; }
        .mark { width: 46px; height: 46px; display: grid; place-items: center; border-radius: 10px; background: #fff; color: #003DA5; font-weight: 900; box-shadow: inset 6px 0 0 #E4002B; border: 1px solid #D4E0F7; }
        .word { display: inline-flex; align-items: baseline; border-radius: 8px; padding: 5px 10px; background: #fff; line-height: 1; border: 1px solid #D4E0F7; }
        .word .blue { color: #003DA5; font-size: 20px; font-weight: 900; }
        .word .red { color: #E4002B; font-size: 20px; font-weight: 900; }
        .sub { margin-top: 5px; color: #64748B; font-size: 11px; font-weight: 900; letter-spacing: .14em; }
        h1 { display: flex; align-items: center; gap: 10px; font-size: 30px; line-height: 1.15; }
        .head p { margin-top: 8px; color: #52637A; line-height: 1.6; }
        form { display: grid; gap: 16px; padding: 24px 26px 26px; }
        .field { display: grid; gap: 8px; }
        label { color: #334155; font-size: 12px; font-weight: 900; letter-spacing: .06em; text-transform: uppercase; }
        input { width: 100%; min-height: 48px; border: 1px solid #C9D8F2; border-radius: 8px; padding: 0 14px; color: #001F5B; font: inherit; font-weight: 800; background: #F8FBFF; outline: none; }
        input:focus { border-color: #003DA5; background: #fff; box-shadow: 0 0 0 4px rgba(0,61,165,.12); }
        .errors { padding: 12px 14px; border: 1px solid #FFC2CC; border-radius: 8px; color: #C9002B; background: #FFF1F3; font-weight: 700; }
        .errors ul { margin-left: 18px; }
        .actions { display: flex; justify-content: flex-end; gap: 10px; padding-top: 4px; }
        .btn { min-height: 46px; border-radius: 8px; padding: 0 18px; font: inherit; font-weight: 900; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn.ghost { border: 1px solid #B9CDF5; background: #fff; color: #003DA5; }
        .btn.primary { border: 0; background: #003DA5; color: #fff; }
        @media (max-width: 560px) { .page { padding: 20px; } .actions { flex-direction: column; } .btn { justify-content: center; } }
    </style>
</head>
<body>
<div class="page">
    <section class="card">
        <div class="head">
            <div class="brand">
                <div class="mark">M</div>
                <div>
                    <div class="word"><span class="blue">Mobi</span><span class="red">Fone</span></div>
                    <div class="sub">WORKHUB SECURITY</div>
                </div>
            </div>
            <h1><i data-lucide="key-round" style="width:30px;height:30px"></i>Đổi mật khẩu</h1>
            <p>Nhập mật khẩu hiện tại để xác nhận, sau đó đặt mật khẩu mới cho tài khoản {{ $user->email }}.</p>
        </div>

        <form method="POST" action="{{ route('profile.password.update') }}">
            @csrf
            @method('PATCH')

            @if ($errors->any())
                <div class="errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="field">
                <label for="currentPassword">Mật khẩu hiện tại</label>
                <input id="currentPassword" name="current_password" type="password" required autocomplete="current-password">
            </div>

            <div class="field">
                <label for="newPassword">Mật khẩu mới</label>
                <input id="newPassword" name="password" type="password" required minlength="8" autocomplete="new-password" placeholder="Tối thiểu 8 ký tự">
            </div>

            <div class="field">
                <label for="newPasswordConfirm">Xác nhận mật khẩu mới</label>
                <input id="newPasswordConfirm" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password">
            </div>

            <div class="actions">
                <a class="btn ghost" href="{{ route('profile.show') }}"><i data-lucide="arrow-left" style="width:18px;height:18px"></i>Quay lại hồ sơ</a>
                <button class="btn primary" type="submit"><i data-lucide="shield-check" style="width:18px;height:18px"></i>Cập nhật mật khẩu</button>
            </div>
        </form>
    </section>
</div>
<script>lucide.createIcons();</script>
</body>
</html>
