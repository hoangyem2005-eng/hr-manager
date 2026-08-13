<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang cá nhân - MobiFone WorkHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { min-height: 100vh; font-family: "Be Vietnam Pro", sans-serif; color: #001F5B; background: #F2F6FC; }
        .shell { min-height: 100vh; display: grid; grid-template-columns: 360px minmax(0, 1fr); }
        .side { position: relative; overflow: hidden; padding: 32px; color: #fff; background: linear-gradient(180deg, #001F5B 0%, #003DA5 100%); }
        .side:after { content: ""; position: absolute; right: -90px; bottom: -120px; width: 310px; height: 310px; border: 46px solid rgba(255,255,255,.08); border-radius: 999px; }
        .brand { position: relative; z-index: 1; display: flex; align-items: center; gap: 12px; }
        .mark { width: 48px; height: 48px; display: grid; place-items: center; border-radius: 10px; background: #fff; color: #003DA5; font-weight: 900; box-shadow: inset 6px 0 0 #E4002B; }
        .word {
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

        .word::after {
            content: "";
            position: absolute;
            inset: -45% auto -45% -55%;
            width: 42%;
            transform: rotate(18deg);
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.9), transparent);
            animation: shine 4.4s ease-in-out infinite;
        }

        .word strong { 
            font-family: Arial, Helvetica, sans-serif !important;
            font-weight: 700 !important;
            letter-spacing: -0.05em !important;
            font-size: 25px; 
            line-height: 1;
        }
        
        .word .blue { color: #003DA5; }
        .word .red { color: #E4002B; }

        /* Custom styling for the official MobiFone logo (lowercase, red dot on 'i') */
        .brand-i {
            position: relative;
            display: inline-block;
            color: inherit;
            font-style: normal;
            line-height: inherit;
            margin-right: -0.06em !important;
        }

        .brand-i::after {
            content: "";
            position: absolute;
            bottom: 0.66em;
            left: 50%;
            transform: translateX(-50%);
            width: 0.15em;
            height: 0.15em;
            background-color: #E4002B !important;
            border-radius: 0;
            display: block;
            z-index: 10;
        }

        @keyframes shine { 0%, 62% { left: -55%; } 78%, 100% { left: 118%; } }
        .sub { margin-top: 8px; color: #BFD8FF; font-size: 11px; font-weight: 800; letter-spacing: .14em; }
        .identity { position: relative; z-index: 1; margin-top: 56px; }
        .avatar { width: 108px; height: 108px; display: grid; place-items: center; overflow: hidden; border-radius: 18px; background: #fff; color: #003DA5; font-size: 34px; font-weight: 900; box-shadow: inset 10px 0 0 #E4002B, 0 24px 50px rgba(0,0,0,.18); }
        a.avatar { text-decoration: none; transition: transform .18s ease, box-shadow .18s ease; }
        a.avatar:hover { transform: translateY(-2px); box-shadow: inset 10px 0 0 #E4002B, 0 28px 56px rgba(0,0,0,.22); }
        .avatar img { width: 100%; height: 100%; object-fit: cover; }
        .identity h1 { margin-top: 22px; font-size: 30px; line-height: 1.15; }
        .identity p { margin-top: 8px; color: #D8E7FF; line-height: 1.6; }
        .pill { margin-top: 22px; display: inline-flex; align-items: center; gap: 8px; padding: 9px 12px; border-radius: 999px; background: rgba(255,255,255,.12); color: #fff; font-weight: 800; }
        .actions { position: relative; z-index: 1; display: grid; gap: 10px; margin-top: 34px; }
        .side-btn { min-height: 46px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: 1px solid rgba(255,255,255,.22); border-radius: 8px; color: #fff; text-decoration: none; font-weight: 900; }
        .side-btn.primary { border-color: #E4002B; background: #E4002B; }
        .main { padding: 34px; overflow: auto; }
        .hero { display: grid; grid-template-columns: 1fr auto; gap: 20px; align-items: center; padding: 26px; border: 1px solid #B9CDF5; border-radius: 10px; background: linear-gradient(135deg, #fff 0%, #F4F8FF 66%, #FFF3F6 100%); box-shadow: inset 5px 0 0 #E4002B; }
        .kicker { color: #E4002B; font-size: 12px; font-weight: 900; letter-spacing: .16em; text-transform: uppercase; }
        .hero h2 { margin-top: 8px; font-size: 34px; line-height: 1.15; }
        .hero p { margin-top: 8px; max-width: 760px; color: #52637A; line-height: 1.65; }
        .hero-actions { display: flex; flex-wrap: wrap; gap: 10px; justify-content: flex-end; }
        .btn { min-height: 44px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; border-radius: 8px; padding: 0 16px; font-weight: 900; text-decoration: none; border: 1px solid #B9CDF5; color: #003DA5; background: #fff; }
        .btn.primary { color: #fff; border-color: #003DA5; background: #003DA5; }
        .flash { margin-top: 18px; padding: 13px 16px; border: 1px solid #B6E7C9; border-radius: 8px; color: #087A35; background: #ECFDF3; font-weight: 800; }
        .grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin-top: 18px; }
        .stat { padding: 18px; border: 1px solid #D4E0F7; border-radius: 8px; background: #fff; }
        .stat span { color: #6B7890; font-size: 11px; font-weight: 900; letter-spacing: .09em; text-transform: uppercase; }
        .stat strong { display: block; margin-top: 9px; color: #003DA5; font-size: 32px; line-height: 1; }
        .stat.alert strong { color: #E4002B; }
        .content { display: grid; grid-template-columns: minmax(0, 1fr) minmax(320px, .58fr); gap: 18px; margin-top: 18px; }
        .panel { overflow: hidden; border: 1px solid #D4E0F7; border-radius: 8px; background: #fff; }
        .panel-head { display: flex; align-items: center; gap: 10px; padding: 16px 18px; border-bottom: 1px solid #E5EDF8; font-size: 18px; font-weight: 900; }
        .info-row { display: grid; grid-template-columns: 160px 1fr; gap: 14px; padding: 15px 18px; border-bottom: 1px solid #EEF2F7; }
        .info-row:last-child { border-bottom: 0; }
        .label { color: #64748B; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .06em; }
        .value { color: #001F5B; font-weight: 900; }
        .progress-box { padding: 20px; }
        .progress-number { font-size: 56px; font-weight: 900; color: #003DA5; line-height: 1; }
        .track { height: 10px; margin-top: 14px; overflow: hidden; border-radius: 999px; background: #E9EEF8; }
        .fill { height: 100%; border-radius: inherit; background: linear-gradient(90deg, #E4002B, #003DA5); }
        @media (max-width: 1000px) { .shell { grid-template-columns: 1fr; } .grid, .content, .hero { grid-template-columns: 1fr; } .hero-actions { justify-content: flex-start; } }
    </style>
</head>
<body>
@php
    $initials = mb_strtoupper(mb_substr($user->name ?? 'NV', 0, 2));
    $avatarUrl = $user->avatar_path ? asset('storage/'.$user->avatar_path) : null;
@endphp
<div class="shell">
    <aside class="side">
        <div class="brand">
            <div>
                <div class="word"><strong class="blue">mob<span class="brand-i">ı</span></strong><strong class="red">fone</strong></div>
                <div class="sub">WORKHUB PROFILE</div>
            </div>
        </div>

        <div class="identity">
            <a class="avatar" href="{{ route('profile.edit') }}" title="Đổi ảnh đại diện">@if($avatarUrl)<img src="{{ $avatarUrl }}" alt="Ảnh đại diện của {{ $user->name }}">@else{{ $initials }}@endif</a>
            <h1>{{ $user->name }}</h1>
            <p>{{ $user->email }}</p>
            <div class="pill"><i data-lucide="briefcase" style="width:16px;height:16px"></i>{{ $user->role_display_name }}</div>
        </div>

        <div class="actions">
            <a class="side-btn primary" href="{{ $dashboardRoute }}"><i data-lucide="layout-dashboard" style="width:18px;height:18px"></i>Quay lại dashboard</a>
            <a class="side-btn" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('profileLogout').submit();"><i data-lucide="log-out" style="width:18px;height:18px"></i>Đăng xuất</a>
        </div>
        <form id="profileLogout" method="POST" action="{{ route('logout') }}" style="display:none">@csrf</form>
    </aside>

    <main class="main">
        <section class="hero">
            <div>
                <div class="kicker">Không gian cá nhân</div>
                <h2>Trang cá nhân của tôi</h2>
                <p>Xem nhanh thông tin tài khoản, phòng ban, chức vụ và trạng thái công việc đang gắn với bạn trong MobiFone WorkHub.</p>
            </div>
            <div class="hero-actions">
                <a class="btn primary" href="{{ route('profile.edit') }}"><i data-lucide="square-pen" style="width:18px;height:18px"></i>Chỉnh sửa thông tin</a>
                <a class="btn" href="{{ route('profile.password.edit') }}"><i data-lucide="key-round" style="width:18px;height:18px"></i>Đổi mật khẩu</a>
            </div>
        </section>

        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif

        <section class="grid">
            <div class="stat"><span>Việc được giao</span><strong>{{ $totalAssigned }}</strong></div>
            <div class="stat"><span>Đang xử lý</span><strong>{{ $inProgress }}</strong></div>
            <div class="stat"><span>Hoàn thành</span><strong>{{ $completed }}</strong></div>
            <div class="stat alert"><span>Quá hạn</span><strong>{{ $overdue }}</strong></div>
        </section>

        <section class="content">
            <div class="panel">
                <div class="panel-head"><i data-lucide="id-card" style="width:22px;height:22px"></i>Thông tin tài khoản</div>
                <div>
                    <div class="info-row"><div class="label">Họ tên</div><div class="value">{{ $user->name }}</div></div>
                    <div class="info-row"><div class="label">Email</div><div class="value">{{ $user->email }}</div></div>
                    <div class="info-row"><div class="label">Phòng ban</div><div class="value">{{ $user->department->TENPHONG ?? $user->department->name ?? 'Chưa xếp phòng' }}</div></div>
                    <div class="info-row"><div class="label">Chức vụ</div><div class="value">{{ $user->role_display_name }}</div></div>
                    <div class="info-row"><div class="label">Ngày tham gia</div><div class="value">{{ $user->created_at?->format('d/m/Y') ?? 'Chưa có dữ liệu' }}</div></div>
                    <div class="info-row"><div class="label">Việc đã giao</div><div class="value">{{ $createdTasks }}</div></div>
                </div>
            </div>

            <div class="panel">
                <div class="panel-head"><i data-lucide="target" style="width:22px;height:22px"></i>Hiệu suất cá nhân</div>
                <div class="progress-box">
                    <div class="progress-number">{{ $completionRate }}%</div>
                    <p style="margin-top:10px;color:#64748B;line-height:1.6">Tỷ lệ hoàn thành dựa trên các công việc được giao cho tài khoản này.</p>
                    <div class="track"><div class="fill" style="width:{{ $completionRate }}%"></div></div>
                </div>
            </div>
        </section>
    </main>
</div>
<script>lucide.createIcons();</script>
</body>
</html>
