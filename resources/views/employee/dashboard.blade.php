<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WorkHub Nhân viên - MobiFone HR</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --mf-blue: #003DA5;
            --mf-blue-dark: #001F5B;
            --mf-red: #E4002B;
            --mf-red-light: #FEF2F2;
            --mf-light: #EEF3FC;
            --mf-border: #D4E0F7;
            --bg: #F0F4FB;
            --white: #ffffff;
            --text: #0D1B3E;
            --text-muted: #64748B;
            --sidebar-w: 272px;
        }
        body { min-height: 100vh; font-family: 'Be Vietnam Pro', sans-serif; background: var(--bg); color: var(--text); }

        /* ======= LAYOUT ======= */
        .layout { min-height: 100vh; display: grid; grid-template-columns: var(--sidebar-w) 1fr; }

        /* ======= SIDEBAR ======= */
        .sidebar {
            background: linear-gradient(180deg, #001F5B 0%, #003DA5 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 22px 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,.12);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-logo {
            width: 38px; height: 38px; border-radius: 8px;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; font-weight: 900; letter-spacing: -.02em; color: var(--mf-blue);
            flex-shrink: 0;
            box-shadow: inset 5px 0 0 var(--mf-red);
        }
        .mf-logo-word { display: inline-flex; align-items: baseline; background: #fff; border-radius: 7px; padding: 4px 9px; line-height: 1; box-shadow: 0 6px 18px rgba(0,0,0,.12); }
        .mf-logo-word .blue { color: var(--mf-blue); font-size: 17px; font-weight: 900; letter-spacing: -.03em; }
        .mf-logo-word .red { color: var(--mf-red); font-size: 17px; font-weight: 900; letter-spacing: -.03em; }
        .brand-sub { color: #BFD8FF; font-size: 10px; margin-top: 6px; letter-spacing: .12em; text-transform: uppercase; font-weight: 700; }

        /* Profile Card */
        .profile-card {
            margin: 18px 14px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 12px;
            padding: 16px;
            backdrop-filter: blur(8px);
        }
        .profile-row { display: flex; align-items: center; gap: 12px; }
        .avatar {
            width: 46px; height: 46px; border-radius: 10px;
            background: linear-gradient(135deg, #E4002B, #FF5A7A);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 15px; flex-shrink: 0;
        }
        .profile-info .name { font-size: 14px; font-weight: 800; }
        .profile-info .meta { font-size: 11px; color: rgba(255,255,255,.6); margin-top: 3px; }
        .profile-dept {
            margin-top: 12px;
            display: flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,.1);
            border-radius: 8px; padding: 7px 10px;
            font-size: 12px; font-weight: 600; color: rgba(255,255,255,.85);
        }

        /* Stats */
        .sidebar-stats {
            padding: 0 14px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .stat-box {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 10px;
            padding: 12px;
        }
        .stat-box.full { grid-column: 1/-1; }
        .stat-label { font-size: 10px; text-transform: uppercase; letter-spacing: .1em; color: rgba(255,255,255,.5); font-weight: 700; }
        .stat-val { font-size: 26px; font-weight: 900; margin-top: 4px; line-height: 1; }
        .stat-val.red { color: #FCA5A5; }
        .stat-val.green { color: #6EE7B7; }
        .stat-val.blue { color: #93C5FD; }

        /* Progress Ring */
        .progress-ring-wrap {
            display: flex; align-items: center; gap: 14px;
        }
        .ring-svg { transform: rotate(-90deg); }
        .ring-track { fill: none; stroke: rgba(255,255,255,.15); stroke-width: 5; }
        .ring-fill { fill: none; stroke: #6EE7B7; stroke-width: 5; stroke-linecap: round; transition: stroke-dashoffset .6s ease; }
        .ring-center { text-align: right; }
        .ring-pct { font-size: 22px; font-weight: 900; color: #6EE7B7; }
        .ring-sub { font-size: 10px; color: rgba(255,255,255,.5); margin-top: 2px; }

        /* Sidebar Nav */
        .sidebar-nav {
            margin: 14px 14px 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            color: rgba(255,255,255,.7);
            text-decoration: none;
            font-size: 13px; font-weight: 600;
            border: none; background: transparent; cursor: pointer;
            font-family: inherit;
            width: 100%;
            transition: background .15s, color .15s;
            position: relative;
        }
        .nav-item:hover { background: rgba(255,255,255,.08); color: #fff; }
        .nav-item.active { background: #fff; color: var(--mf-blue); font-weight: 800; box-shadow: inset 3px 0 0 var(--mf-red); }
        .nav-badge {
            margin-left: auto;
            min-width: 20px; height: 20px; border-radius: 10px;
            background: var(--mf-red);
            color: #fff; font-size: 10px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            padding: 0 5px;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            margin-top: auto;
            padding: 14px;
            border-top: 1px solid rgba(255,255,255,.1);
        }
        .logout-btn {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            color: rgba(255,255,255,.55);
            border: none; background: transparent; cursor: pointer;
            font-family: inherit; font-size: 13px; font-weight: 600;
            width: 100%;
            transition: background .15s, color .15s;
        }
        .logout-btn:hover { background: rgba(228,0,43,.15); color: #FCA5A5; }

        /* ======= MAIN CONTENT ======= */
        .main { min-width: 0; display: flex; flex-direction: column; }

        /* Topbar */
        .topbar {
            position: sticky; top: 0; z-index: 30;
            background: rgba(240,244,251,.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #D4E0F7;
            height: 60px;
            display: flex; align-items: center;
            padding: 0 24px;
            gap: 16px;
        }
        .topbar-title { font-size: 14px; font-weight: 800; color: var(--mf-blue-dark); flex: 1; }
        .topbar-date { font-size: 12px; color: var(--text-muted); font-weight: 500; }
        .topbar-bell {
            position: relative;
            width: 36px; height: 36px; border-radius: 8px;
            border: 1px solid var(--mf-border);
            background: var(--white);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; text-decoration: none; color: var(--text);
            transition: border-color .15s;
        }
        .topbar-bell:hover { border-color: var(--mf-blue); }
        .bell-dot {
            position: absolute; top: 6px; right: 6px;
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--mf-red); border: 2px solid var(--bg);
        }

        /* Content Area */
        .content { padding: 22px 24px; display: flex; flex-direction: column; gap: 20px; flex: 1; }

        /* Hero Banner */
        .hero {
            background: linear-gradient(135deg, var(--mf-blue-dark) 0%, var(--mf-blue) 60%, #1a56c4 100%);
            border-radius: 14px;
            padding: 24px 28px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 20px;
            align-items: center;
            overflow: hidden;
            position: relative;
        }
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background-image: radial-gradient(circle at 80% 50%, rgba(255,255,255,.06) 0%, transparent 60%);
            pointer-events: none;
        }
        .hero-kicker { font-size: 10px; letter-spacing: .2em; text-transform: uppercase; color: rgba(255,255,255,.55); font-weight: 700; }
        .hero-title { font-size: 22px; font-weight: 900; color: #fff; margin-top: 6px; line-height: 1.3; }
        .hero-sub { font-size: 13px; color: rgba(255,255,255,.6); margin-top: 6px; line-height: 1.6; }
        .hero-actions { display: flex; gap: 10px; margin-top: 16px; flex-wrap: wrap; }
        .hero-btn {
            height: 36px; border-radius: 8px; padding: 0 14px;
            display: inline-flex; align-items: center; gap: 7px;
            font-size: 12px; font-weight: 800; cursor: pointer;
            font-family: inherit; text-decoration: none; border: 1px solid transparent;
            transition: opacity .15s;
        }
        .hero-btn:hover { opacity: .85; }
        .hero-btn.primary { background: var(--mf-red); color: #fff; border-color: var(--mf-red); }
        .hero-btn.ghost { background: rgba(255,255,255,.1); color: #fff; border-color: rgba(255,255,255,.2); }
        .hero-stats { display: flex; gap: 20px; }
        .hero-stat { text-align: center; }
        .hero-stat-val { font-size: 28px; font-weight: 900; color: #fff; line-height: 1; }
        .hero-stat-label { font-size: 10px; color: rgba(255,255,255,.55); margin-top: 4px; text-transform: uppercase; letter-spacing: .08em; font-weight: 700; }

        /* Deadline Alert */
        .deadline-alert {
            background: linear-gradient(135deg, #FFF7ED, #FEF3C7);
            border: 1px solid #FCD34D;
            border-radius: 12px;
            padding: 14px 18px;
            display: flex; align-items: center; gap: 12px;
            font-size: 13px; font-weight: 600; color: #92400E;
        }
        .deadline-alert i { color: #D97706; flex-shrink: 0; }

        /* Content Grid */
        .content-grid { display: grid; grid-template-columns: 1fr 340px; gap: 20px; align-items: start; }

        /* Panel */
        .panel {
            background: var(--white);
            border: 1px solid #E5EDF8;
            border-radius: 14px;
            overflow: hidden;
        }
        .panel-head {
            padding: 16px 20px;
            border-bottom: 1px solid #EFF4FD;
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
        }
        .panel-title {
            font-size: 14px; font-weight: 800; color: var(--mf-blue-dark);
            display: flex; align-items: center; gap: 8px;
        }
        .panel-title i { color: var(--mf-blue); }

        /* Tab Filter */
        .tab-group {
            display: flex; gap: 4px;
            background: var(--mf-light);
            border-radius: 8px; padding: 3px;
        }
        .tab-btn {
            height: 28px; border-radius: 6px; padding: 0 12px;
            background: transparent; border: none; cursor: pointer;
            font-family: inherit; font-size: 12px; font-weight: 700;
            color: var(--text-muted); transition: all .15s;
        }
        .tab-btn.active { background: #fff; color: var(--mf-blue); box-shadow: 0 1px 3px rgba(0,61,165,.12); }

        /* Task Card */
        .task-card {
            padding: 16px 20px;
            border-top: 1px solid #F1F5FD;
            display: flex; flex-direction: column; gap: 10px;
            transition: background .12s;
            position: relative;
        }
        .task-card:hover { background: #F8FAFF; }
        .task-card.overdue { border-left: 3px solid var(--mf-red); }
        .task-card.done { border-left: 3px solid #10B981; }
        .task-card.doing { border-left: 3px solid #3B82F6; }
        .task-card.pending { border-left: 3px solid #9CA3AF; }

        .task-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
        .task-name { font-size: 14px; font-weight: 800; color: var(--mf-blue-dark); line-height: 1.4; }
        .task-code { font-family: monospace; font-size: 11px; color: var(--text-muted); background: #F1F5FD; border-radius: 5px; padding: 2px 6px; flex-shrink: 0; }
        .task-meta-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .task-chip {
            display: inline-flex; align-items: center; gap: 4px;
            height: 22px; padding: 0 8px; border-radius: 999px;
            font-size: 11px; font-weight: 700;
        }
        .chip-pending { background: #F3F4F6; color: #6B7280; }
        .chip-doing { background: #EFF6FF; color: #1D4ED8; }
        .chip-review { background: #F5F3FF; color: #6D28D9; }
        .chip-done { background: #F0FDF4; color: #15803D; }
        .chip-overdue { background: var(--mf-red-light); color: var(--mf-red); }
        .deadline-chip { font-size: 11px; color: var(--text-muted); display: flex; align-items: center; gap: 4px; }
        .deadline-chip.urgent { color: var(--mf-red); font-weight: 700; }

        /* Progress bar */
        .prog-wrap { display: flex; align-items: center; gap: 10px; }
        .prog-bar { flex: 1; height: 6px; background: #E8EFF9; border-radius: 999px; overflow: hidden; }
        .prog-fill { height: 100%; border-radius: 999px; transition: width .4s ease; }
        .prog-fill.blue { background: linear-gradient(90deg, #3B82F6, #003DA5); }
        .prog-fill.green { background: linear-gradient(90deg, #10B981, #059669); }
        .prog-fill.red { background: linear-gradient(90deg, #F87171, #E4002B); }
        .prog-pct { font-size: 12px; font-weight: 800; color: var(--mf-blue); min-width: 34px; text-align: right; }

        /* Task actions row */
        .task-actions { display: flex; align-items: center; gap: 10px; }
        .status-select {
            flex: 1;
            height: 34px;
            border: 1px solid #D4E0F7;
            border-radius: 8px;
            padding: 0 10px;
            background: #F8FAFF;
            font-family: inherit; font-size: 12px; font-weight: 700;
            color: var(--mf-blue-dark);
            cursor: pointer;
            outline: none;
            transition: border-color .15s;
        }
        .status-select:focus { border-color: var(--mf-blue); background: #fff; }
        .view-btn {
            height: 34px; padding: 0 12px; border-radius: 8px;
            border: 1px solid var(--mf-border);
            background: var(--white);
            color: var(--mf-blue); font-family: inherit; font-size: 12px; font-weight: 700;
            cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
            transition: background .12s, border-color .12s;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .view-btn:hover { background: var(--mf-light); border-color: var(--mf-blue); }

        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: var(--text-muted);
        }
        .empty-state i { opacity: .3; margin-bottom: 12px; }
        .empty-state p { font-size: 14px; }

        /* ======= NOTIFICATIONS PANEL ======= */
        .notif-item {
            display: flex; gap: 12px;
            padding: 14px 20px;
            border-top: 1px solid #F1F5FD;
            width: 100%; text-align: left;
            background: transparent; border-left: none; border-right: none; border-bottom: none;
            cursor: pointer; font-family: inherit;
            transition: background .12s;
        }
        .notif-item:hover { background: #F8FAFF; }
        .notif-item.unread { background: #F0F4FF; }
        .notif-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .notif-icon.task { background: #EEF3FC; color: var(--mf-blue); }
        .notif-icon.alert { background: var(--mf-red-light); color: var(--mf-red); }
        .notif-icon.general { background: #F0FDF4; color: #15803D; }
        .notif-title { font-size: 13px; font-weight: 800; color: var(--mf-blue-dark); line-height: 1.3; }
        .notif-desc { font-size: 11px; color: var(--text-muted); margin-top: 3px; line-height: 1.5; }
        .notif-time { font-size: 10px; color: #9CA3AF; margin-top: 5px; }
        .notif-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--mf-blue); flex-shrink: 0; margin-top: 5px;
        }

        /* Mark all read link */
        .mark-read-form { padding: 12px 20px; border-top: 1px solid #F1F5FD; }
        .mark-read-btn {
            font-size: 12px; font-weight: 700; color: var(--mf-blue);
            background: none; border: none; cursor: pointer; font-family: inherit;
            padding: 0; text-decoration: underline; text-underline-offset: 3px;
        }

        /* Toast */
        .toast {
            position: fixed; right: 20px; bottom: 20px; z-index: 9999;
            background: var(--mf-blue-dark); color: #fff;
            border-radius: 10px; padding: 12px 18px;
            font-size: 13px; font-weight: 700;
            display: flex; align-items: center; gap: 8px;
            box-shadow: 0 8px 30px rgba(0,31,91,.3);
            transform: translateY(80px); opacity: 0; transition: all .25s cubic-bezier(.34,1.56,.64,1);
        }
        .toast.show { transform: translateY(0); opacity: 1; }
        .toast.success { background: #065F46; }
        .toast.error { background: #991B1B; }

        /* Responsive */
        @media (max-width: 1200px) { .content-grid { grid-template-columns: 1fr; } }
        @media (max-width: 960px) { .layout { grid-template-columns: 1fr; } .sidebar { height: auto; position: static; } }
        @media (max-width: 640px) { .hero { grid-template-columns: 1fr; } .hero-stats { display: none; } }
    </style>
</head>
<body>
<div class="layout">

    {{-- ======= SIDEBAR ======= --}}
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-logo">M</div>
            <div>
                <div class="mf-logo-word"><span class="blue">Mobi</span><span class="red">Fone</span></div>
                <div class="brand-sub">EMPLOYEE WORKHUB</div>
            </div>
        </div>

        <div class="profile-card">
            <div class="profile-row">
                <div class="avatar">{{ mb_strtoupper(mb_substr(Auth::user()->name ?? 'NV', 0, 2)) }}</div>
                <div class="profile-info">
                    <div class="name">{{ Auth::user()->name }}</div>
                    <div class="meta">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="profile-dept">
                <i data-lucide="building-2" style="width:13px;height:13px;flex-shrink:0"></i>
                {{ Auth::user()->department->TENPHONG ?? 'MobiFone' }}
            </div>
        </div>

        <div class="sidebar-stats">
            <div class="stat-box">
                <div class="stat-label">Tổng việc</div>
                <div class="stat-val">{{ $total }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Đang làm</div>
                <div class="stat-val blue">{{ $doing }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Hoàn thành</div>
                <div class="stat-val green">{{ $done }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Quá hạn</div>
                <div class="stat-val red">{{ $overdue }}</div>
            </div>
            {{-- Progress Ring --}}
            <div class="stat-box full" style="display:flex;align-items:center;gap:16px">
                <svg width="56" height="56" class="ring-svg">
                    @php $circumference = 2 * pi() * 22; $offset = $circumference - ($completionRate / 100) * $circumference; @endphp
                    <circle class="ring-track" cx="28" cy="28" r="22"/>
                    <circle class="ring-fill" cx="28" cy="28" r="22"
                        stroke-dasharray="{{ $circumference }}"
                        stroke-dashoffset="{{ $offset }}"
                    />
                </svg>
                <div>
                    <div class="ring-pct">{{ $completionRate }}%</div>
                    <div class="ring-sub">Tỉ lệ hoàn thành</div>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav" style="margin-top:16px">
            <a href="{{ route('employee.dashboard') }}" class="nav-item active">
                <i data-lucide="layout-dashboard" style="width:16px;height:16px;flex-shrink:0"></i>
                Dashboard
            </a>
            <a href="#tasks-panel" class="nav-item" onclick="scrollTo('tasks-panel')">
                <i data-lucide="clipboard-list" style="width:16px;height:16px;flex-shrink:0"></i>
                Công việc của tôi
                @if($total > 0)<span class="nav-badge">{{ $total }}</span>@endif
            </a>
            <a href="#notifications-panel" class="nav-item" onclick="scrollTo('notifications-panel')">
                <i data-lucide="bell" style="width:16px;height:16px;flex-shrink:0"></i>
                Thông báo
                @if($unreadCount > 0)<span class="nav-badge">{{ $unreadCount }}</span>@endif
            </a>
        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <i data-lucide="log-out" style="width:16px;height:16px;flex-shrink:0"></i>
                    Đăng xuất
                </button>
            </form>
        </div>
    </aside>

    {{-- ======= MAIN ======= --}}
    <div class="main">
        {{-- Topbar --}}
        <div class="topbar">
            <div class="topbar-title">
                Xin chào, {{ explode(' ', Auth::user()->name)[count(explode(' ', Auth::user()->name)) - 1] }}! 👋
            </div>
            <div class="topbar-date" id="topbar-date"></div>
            <a href="#notifications-panel" class="topbar-bell" title="Thông báo">
                <i data-lucide="bell" style="width:17px;height:17px"></i>
                @if($unreadCount > 0)<span class="bell-dot"></span>@endif
            </a>
        </div>

        <div class="content">

            {{-- Hero --}}
            <div class="hero">
                <div>
                    <div class="hero-kicker">Personal Execution · MobiFone WorkHub</div>
                    <div class="hero-title">Việc của tôi, tiến độ của tôi.</div>
                    <div class="hero-sub">Cập nhật trạng thái công việc và theo dõi tiến độ cá nhân của bạn tại đây.</div>
                    <div class="hero-actions">
                        <a href="#tasks-panel" class="hero-btn primary">
                            <i data-lucide="zap" style="width:14px;height:14px"></i>
                            Công việc ngay
                        </a>
                        <a href="#notifications-panel" class="hero-btn ghost">
                            <i data-lucide="bell" style="width:14px;height:14px"></i>
                            Thông báo @if($unreadCount > 0)({{ $unreadCount }})@endif
                        </a>
                    </div>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-val">{{ $total }}</div>
                        <div class="hero-stat-label">Tổng việc</div>
                    </div>
                    <div class="hero-stat" style="border-left:1px solid rgba(255,255,255,.15);padding-left:20px">
                        <div class="hero-stat-val" style="color:#6EE7B7">{{ $completionRate }}%</div>
                        <div class="hero-stat-label">Hoàn thành</div>
                    </div>
                    <div class="hero-stat" style="border-left:1px solid rgba(255,255,255,.15);padding-left:20px">
                        <div class="hero-stat-val" style="color:#FCA5A5">{{ $overdue }}</div>
                        <div class="hero-stat-label">Quá hạn</div>
                    </div>
                </div>
            </div>

            {{-- Deadline Alert --}}
            @php
                $urgentTasks = collect($mappedTasks)->filter(fn($t) => isset($t['days_left']) && $t['days_left'] !== null && $t['days_left'] <= 2 && $t['days_left'] >= 0 && !str_contains($t['status'], 'Hoàn'));
            @endphp
            @if($urgentTasks->isNotEmpty())
            <div class="deadline-alert">
                <i data-lucide="alert-triangle" style="width:18px;height:18px"></i>
                <span>
                    <strong>{{ $urgentTasks->count() }} công việc sắp hết hạn trong 2 ngày tới.</strong>
                    Hãy ưu tiên xử lý ngay để không bị quá hạn.
                </span>
            </div>
            @endif

            {{-- Main Grid --}}
            <div class="content-grid">

                {{-- Task Panel --}}
                <div class="panel" id="tasks-panel">
                    <div class="panel-head">
                        <div class="panel-title">
                            <i data-lucide="clipboard-list"></i>
                            Danh sách công việc cá nhân
                        </div>
                        <div class="tab-group">
                            <button class="tab-btn active" onclick="filterTasks('all', this)">Tất cả</button>
                            <button class="tab-btn" onclick="filterTasks('doing', this)">Đang làm</button>
                            <button class="tab-btn" onclick="filterTasks('pending', this)">Chờ xử lý</button>
                            <button class="tab-btn" onclick="filterTasks('done', this)">Xong</button>
                        </div>
                    </div>

                    @forelse($mappedTasks as $t)
                        @php
                            $state = str_contains($t['status'], 'Hoàn') ? 'done'
                                   : (str_contains($t['status'], 'Quá') ? 'overdue'
                                   : (str_contains($t['status'], 'Đang') ? 'doing' : 'pending'));
                            $chipClass = match($state) {
                                'done'    => 'chip-done',
                                'overdue' => 'chip-overdue',
                                'doing'   => 'chip-doing',
                                default   => 'chip-pending'
                            };
                            $barClass = match($state) {
                                'done'    => 'green',
                                'overdue' => 'red',
                                default   => 'blue'
                            };
                            $daysLeft = $t['days_left'] ?? null;
                            $isUrgent = $daysLeft !== null && $daysLeft <= 2 && $daysLeft >= 0 && $state !== 'done';
                        @endphp
                        <div class="task-card {{ $state }}" data-status="{{ $state }}" id="task-{{ $t['id'] }}">
                            <div class="task-top">
                                <div class="task-name">{{ $t['name'] }}</div>
                                <span class="task-code">{{ $t['code'] }}</span>
                            </div>

                            <div class="task-meta-row">
                                <span class="task-chip {{ $chipClass }}">
                                    <i data-lucide="{{ $state === 'done' ? 'check-circle' : ($state === 'overdue' ? 'alert-circle' : ($state === 'doing' ? 'play-circle' : 'clock')) }}" style="width:11px;height:11px"></i>
                                    {{ $t['status'] }}
                                </span>
                                <span class="deadline-chip {{ $isUrgent ? 'urgent' : '' }}">
                                    <i data-lucide="calendar" style="width:11px;height:11px"></i>
                                    Deadline: {{ $t['deadline'] }}
                                    @if($daysLeft !== null && $state !== 'done')
                                        @if($daysLeft < 0)
                                            · <span style="color:var(--mf-red);font-weight:700">Quá hạn {{ abs((int)$daysLeft) }} ngày</span>
                                        @elseif($daysLeft === 0)
                                            · <span style="color:var(--mf-red);font-weight:700">Hôm nay!</span>
                                        @elseif($daysLeft <= 2)
                                            · <span style="color:#D97706;font-weight:700">Còn {{ $daysLeft }} ngày</span>
                                        @endif
                                    @endif
                                </span>
                            </div>

                            <div class="prog-wrap">
                                <div class="prog-bar">
                                    <div class="prog-fill {{ $barClass }}" id="bar-{{ $t['id'] }}" style="width:{{ $t['progress'] }}%"></div>
                                </div>
                                <span class="prog-pct" id="pct-{{ $t['id'] }}">{{ $t['progress'] }}%</span>
                            </div>

                            <div class="task-actions">
                                <select class="status-select" id="sel-{{ $t['id'] }}" onchange="updateTask({{ $t['id'] }}, this.value)">
                                    <option value="Chờ xử lý" {{ $t['status'] === 'Chờ xử lý' ? 'selected' : '' }}>⏳ Chờ xử lý</option>
                                    <option value="Đang làm"  {{ $t['status'] === 'Đang làm'  ? 'selected' : '' }}>▶️ Đang làm</option>
                                    <option value="Đang review" {{ $t['status'] === 'Đang review' ? 'selected' : '' }}>🔍 Đang review</option>
                                    <option value="Hoàn thành" {{ $t['status'] === 'Hoàn thành' ? 'selected' : '' }}>✅ Hoàn thành</option>
                                </select>
                                <a href="{{ route('employee.task.detail', $t['id']) }}" class="view-btn">
                                    <i data-lucide="eye" style="width:13px;height:13px"></i>
                                    Chi tiết
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="empty-state">
                            <div><i data-lucide="inbox" style="width:40px;height:40px"></i></div>
                            <p>Bạn chưa có công việc nào được giao.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Notifications Panel --}}
                <div class="panel" id="notifications-panel">
                    <div class="panel-head">
                        <div class="panel-title">
                            <i data-lucide="bell"></i>
                            Thông báo
                            @if($unreadCount > 0)
                                <span style="min-width:20px;height:20px;border-radius:10px;background:var(--mf-red);color:#fff;font-size:10px;font-weight:800;display:inline-flex;align-items:center;justify-content:center;padding:0 5px">{{ $unreadCount }}</span>
                            @endif
                        </div>
                    </div>

                    @forelse($notifications as $n)
                        @php
                            $titleLower = mb_strtolower($n->title ?? '');
                            $isTask = str_contains($titleLower, 'giao') || str_contains($titleLower, 'công việc') || str_contains($titleLower, 'phân công');
                            $isAlert = str_contains($titleLower, 'quá hạn') || str_contains($titleLower, 'deadline') || str_contains($titleLower, 'nhắc');
                            $iconClass = $isAlert ? 'alert' : ($isTask ? 'task' : 'general');
                            $iconName  = $isAlert ? 'alert-triangle' : ($isTask ? 'check-square' : 'bell');
                        @endphp
                        <form method="POST" action="{{ route('dashboard.notifications.open', $n->id) }}" style="display:block">
                            @csrf
                            <button type="submit" class="notif-item {{ !$n->is_read ? 'unread' : '' }}">
                                <div class="notif-icon {{ $iconClass }}">
                                    <i data-lucide="{{ $iconName }}" style="width:16px;height:16px"></i>
                                </div>
                                <div style="flex:1;min-width:0;text-align:left">
                                    <div class="notif-title">{{ $n->title }}</div>
                                    <div class="notif-desc">{{ $n->message }}</div>
                                    <div class="notif-time">{{ $n->created_at->diffForHumans() }}</div>
                                </div>
                                @if(!$n->is_read)
                                    <div class="notif-dot"></div>
                                @endif
                            </button>
                        </form>
                    @empty
                        <div class="empty-state">
                            <div><i data-lucide="bell-off" style="width:36px;height:36px"></i></div>
                            <p>Chưa có thông báo nào.</p>
                        </div>
                    @endforelse

                    @if($notifications->isNotEmpty())
                        <div class="mark-read-form">
                            <form method="POST" action="{{ route('dashboard.notifications.markAllRead') }}">
                                @csrf
                                <button type="submit" class="mark-read-btn">Đánh dấu tất cả đã đọc</button>
                            </form>
                        </div>
                    @endif
                </div>

            </div>{{-- /content-grid --}}
        </div>{{-- /content --}}
    </div>{{-- /main --}}
</div>

<div class="toast" id="toast-live"><i data-lucide="check-circle" style="width:15px;height:15px;flex-shrink:0"></i><span id="toast-msg"></span></div>

<script>
    lucide.createIcons();

    // Topbar date
    const d = new Date();
    const opts = { weekday:'long', year:'numeric', month:'long', day:'numeric' };
    document.getElementById('topbar-date').textContent = d.toLocaleDateString('vi-VN', opts);

    // Scroll to anchor
    function scrollTo(id) {
        const el = document.getElementById(id);
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const id = a.getAttribute('href').slice(1);
            const el = document.getElementById(id);
            if (el) { e.preventDefault(); el.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        });
    });

    // Filter tasks
    function filterTasks(status, btn) {
        document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.task-card').forEach(card => {
            card.style.display = (status === 'all' || card.dataset.status === status) ? 'flex' : 'none';
        });
    }
    // Set task-card display to flex initially
    document.querySelectorAll('.task-card').forEach(c => c.style.display = 'flex');
    document.querySelectorAll('.task-card').forEach(c => c.style.flexDirection = 'column');

    // Update task status
    function updateTask(id, status) {
        const progressMap = { 'Chờ xử lý': 0, 'Đang làm': 50, 'Đang review': 80, 'Hoàn thành': 100 };
        const progress = progressMap[status] ?? 0;

        fetch(`/employee/tasks/${id}/progress`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status, progress })
        })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { showToast('Lỗi cập nhật trạng thái', 'error'); return; }

            // Update progress bar
            const bar = document.getElementById('bar-' + id);
            const pct = document.getElementById('pct-' + id);
            if (bar) { bar.style.width = data.progress + '%'; }
            if (pct) { pct.textContent = data.progress + '%'; }

            // Update card style
            const card = document.getElementById('task-' + id);
            if (card) {
                card.classList.remove('pending', 'doing', 'done', 'overdue');
                const stateMap = { 'Hoàn thành': 'done', 'Đang làm': 'doing', 'Đang review': 'doing', 'Chờ xử lý': 'pending' };
                const newState = stateMap[status] || 'pending';
                card.classList.add(newState);
                card.dataset.status = newState;

                // Update bar color
                if (bar) {
                    bar.className = 'prog-fill ' + (newState === 'done' ? 'green' : 'blue');
                }
            }

            showToast('Đã cập nhật: ' + status, 'success');
        })
        .catch(() => showToast('Không thể kết nối máy chủ', 'error'));
    }

    function recalculateStats() {
        const rows = document.querySelectorAll('.task-row');
        let total = rows.length;
        let done = 0;
        let doing = 0;
        let overdue = 0;
        let pending = 0;

        rows.forEach(row => {
            const status = row.dataset.status;
            if (status === 'done') done++;
            else if (status === 'doing' || status === 'review') doing++;
            else if (status === 'overdue') overdue++;
            else if (status === 'pending') pending++;
        });

        taskStats.total = total;
        taskStats.done = done;
        taskStats.doing = doing;
        taskStats.overdue = overdue;
        taskStats.pending = pending;

        const rate = total > 0 ? Math.round((done / total) * 100) : 0;

        // Update KPI values
        document.getElementById('kpi-total').textContent = total;
        document.getElementById('kpi-pending-sub').textContent = pending + ' công việc chờ xử lý';
        document.getElementById('kpi-rate').textContent = rate + '%';
        document.getElementById('kpi-done-sub').textContent = done + '/' + total + ' đã hoàn thành';
        document.getElementById('kpi-doing').textContent = doing;
        document.getElementById('kpi-overdue').textContent = overdue;
        
        const overdueSub = document.getElementById('kpi-overdue-sub');
        const pingDot = document.getElementById('kpi-ping');
        if (overdue > 0) {
            overdueSub.textContent = 'Cần xử lý ngay';
            overdueSub.style.color = '#E63946';
            overdueSub.style.fontWeight = '600';
            if (pingDot) pingDot.style.display = 'block';
        } else {
            overdueSub.textContent = 'Không có công việc trễ';
            overdueSub.style.color = '#94A3B8';
            overdueSub.style.fontWeight = 'normal';
            if (pingDot) pingDot.style.display = 'none';
        }

        // Re-render chart
        renderDonutChart();

        // Auto-scroll and highlight task row if task_id is present in URL
        const urlParams = new URLSearchParams(window.location.search);
        const taskIdParam = urlParams.get('task_id');
        if (taskIdParam) {
            const row = document.getElementById('task-row-' + taskIdParam);
            if (row) {
                row.scrollIntoView({ behavior: 'smooth', block: 'center' });
                row.style.transition = 'background-color 0.5s ease';
                row.style.backgroundColor = '#E8F0FE';
                setTimeout(() => {
                    row.style.backgroundColor = '#EFF6FF';
                    setTimeout(() => {
                        row.style.backgroundColor = '';
                    }, 1000);
                }, 2000);
            }
        }
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast-live');
        const msg = document.getElementById('toast-msg');
        if (msg) msg.textContent = message;
        if (toast) {
            toast.className = 'toast ' + type;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 2800);
        }
    }
</script>
</body>
</html>
