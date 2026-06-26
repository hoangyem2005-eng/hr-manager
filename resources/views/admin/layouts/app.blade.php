<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin — MobiFone HR')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-w: 240px;
            --header-h: 64px;
            --primary:   #1A1A2E;
            --accent:    #E63946;
            --accent2:   #F4A261;
            --surface:   #16213E;
            --surface2:  #0F3460;
            --text:      #E8ECEF;
            --text-muted:#94A3B8;
            --border:    rgba(255,255,255,0.07);
            --card-bg:   #FFFFFF;
            --page-bg:   #F0F4FF;
        }

        html, body { height: 100%; overflow: hidden; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--page-bg);
            display: flex;
        }

        /* ======= SIDEBAR ======= */
        .sidebar {
            width: var(--sidebar-w);
            height: 100vh;
            background: linear-gradient(180deg, #1A1A2E 0%, #0F3460 100%);
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            position: relative;
            z-index: 30;
            transition: width .25s cubic-bezier(.4,0,.2,1);
        }

        .sidebar.collapsed { width: 68px; }
        .sidebar.collapsed .s-label,
        .sidebar.collapsed .s-logo-text,
        .sidebar.collapsed .s-user-info,
        .sidebar.collapsed .badge-count { display: none; }

        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 20px 16px 18px;
            border-bottom: 1px solid var(--border);
        }
        .logo-icon {
            width: 38px; height: 38px;
            background: linear-gradient(135deg, #E63946, #F4A261);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 16px; color: #fff;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(230,57,70,.35);
        }
        .s-logo-text .brand { font-size: 14px; font-weight: 700; color: #fff; line-height: 1.2; }
        .s-logo-text .sub   { font-size: 10px; color: #64748B; letter-spacing: .12em; font-weight: 600; }

        .sidebar-badge {
            display: inline-block;
            background: #E63946;
            color: #fff;
            font-size: 9px; font-weight: 700;
            border-radius: 99px;
            padding: 1px 6px;
            min-width: 18px; text-align: center;
            margin-left: auto;
        }

        nav.sidebar-nav {
            flex: 1;
            padding: 12px 10px;
            overflow-y: auto;
        }
        nav.sidebar-nav::-webkit-scrollbar { width: 3px; }
        nav.sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.1); border-radius: 99px; }

        .nav-section {
            font-size: 9px; font-weight: 700; letter-spacing: .12em;
            color: rgba(255,255,255,.3);
            padding: 14px 10px 6px;
            text-transform: uppercase;
        }

        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            color: rgba(255,255,255,.55);
            font-size: 13px; font-weight: 500;
            text-decoration: none;
            transition: all .18s;
            margin-bottom: 2px;
            position: relative;
        }
        .nav-item:hover { background: rgba(255,255,255,.07); color: #fff; }
        .nav-item.active {
            background: linear-gradient(135deg, rgba(230,57,70,.25), rgba(244,162,97,.15));
            color: #fff;
            box-shadow: inset 0 0 0 1px rgba(230,57,70,.3);
        }
        .nav-item.active::before {
            content: '';
            position: absolute; left: 0; top: 50%;
            transform: translateY(-50%);
            width: 3px; height: 60%; background: #E63946;
            border-radius: 0 3px 3px 0;
        }
        .nav-item svg { flex-shrink: 0; }

        .sidebar-footer {
            border-top: 1px solid var(--border);
            padding: 12px 10px;
        }
        .user-card {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 10px;
            border-radius: 10px;
            background: rgba(255,255,255,.05);
        }
        .user-avatar-sm {
            width: 34px; height: 34px; border-radius: 10px;
            background: linear-gradient(135deg, #E63946, #F4A261);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 12px; color: #fff;
            flex-shrink: 0;
        }
        .s-user-info .s-name  { font-size: 12px; font-weight: 600; color: #fff; }
        .s-user-info .s-role  { font-size: 10px; color: var(--text-muted); }

        /* ======= LAYOUT ======= */
        .app-body { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

        .topbar {
            height: var(--header-h);
            background: #fff;
            border-bottom: 1px solid #E8ECF0;
            display: flex; align-items: center;
            padding: 0 24px; gap: 16px;
            flex-shrink: 0;
            box-shadow: 0 1px 0 rgba(0,0,0,.05);
        }
        .topbar-title { font-size: 16px; font-weight: 700; color: #0F172A; flex: 1; }
        .topbar-search {
            position: relative;
        }
        .topbar-search input {
            height: 38px; width: 240px;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            padding: 0 12px 0 36px;
            font-size: 13px; color: #0F172A;
            outline: none; background: #F8FAFC;
            font-family: inherit;
            transition: border-color .2s;
        }
        .topbar-search input:focus { border-color: #E63946; background: #fff; }
        .topbar-search svg {
            position: absolute; left: 10px; top: 50%; transform: translateY(-50%);
            color: #94A3B8;
        }

        .topbar-actions { display: flex; align-items: center; gap: 8px; }
        .icon-btn {
            width: 38px; height: 38px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            background: #F8FAFC; border: 1.5px solid #E2E8F0;
            cursor: pointer; color: #64748B;
            transition: all .18s; position: relative;
        }
        .icon-btn:hover { background: #fff; border-color: #E63946; color: #E63946; }

        .notif-dot {
            position: absolute; top: 6px; right: 6px;
            width: 8px; height: 8px; border-radius: 50%;
            background: #E63946; border: 2px solid #fff;
        }

        .role-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 12px; border-radius: 99px;
            background: linear-gradient(135deg, rgba(230,57,70,.1), rgba(244,162,97,.08));
            border: 1px solid rgba(230,57,70,.2);
            font-size: 11px; font-weight: 600; color: #E63946;
        }

        /* ======= MAIN ======= */
        main.admin-main {
            flex: 1;
            overflow-y: auto;
            padding: 28px 28px;
            background: var(--page-bg);
        }
        main.admin-main::-webkit-scrollbar { width: 5px; }
        main.admin-main::-webkit-scrollbar-thumb { background: rgba(0,0,0,.1); border-radius: 99px; }

        /* ======= FLASH ======= */
        .flash {
            display: flex; align-items: center; gap: 10px;
            padding: 14px 16px; border-radius: 12px;
            font-size: 13px; font-weight: 500;
            margin-bottom: 20px;
            animation: fadeSlide .3s ease;
        }
        .flash.success { background: #F0FDF4; border: 1px solid #BBF7D0; color: #16A34A; }
        .flash.error   { background: #FFF1F2; border: 1px solid #FECDD3; color: #E11D48; }
        @keyframes fadeSlide { from { opacity:0; transform: translateY(-8px); } to { opacity:1; transform: translateY(0); } }
    </style>
    @yield('head_extra')
</head>
<body>

<!-- ============ SIDEBAR ============ -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">GĐ</div>
        <div class="s-logo-text">
            <div class="brand">MobiFone</div>
            <div class="sub">HR — ADMIN</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <span class="nav-section s-label">Tổng quan</span>
        <a href="{{ route('admin.dashboard') }}" class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i data-lucide="layout-dashboard" style="width:17px;height:17px"></i>
            <span class="s-label">Dashboard</span>
        </a>

        <span class="nav-section s-label" style="margin-top:4px">Nhân sự</span>
        <a href="{{ route('admin.dashboard') }}#nhan-su" class="nav-item">
            <i data-lucide="users" style="width:17px;height:17px"></i>
            <span class="s-label">Tất cả nhân viên</span>
        </a>
        <a href="{{ route('admin.dashboard') }}#phong-ban" class="nav-item">
            <i data-lucide="building-2" style="width:17px;height:17px"></i>
            <span class="s-label">Phòng ban</span>
        </a>

        <span class="nav-section s-label" style="margin-top:4px">Vận hành</span>
        <a href="{{ route('dashboard.index') }}" class="nav-item">
            <i data-lucide="kanban" style="width:17px;height:17px"></i>
            <span class="s-label">Công việc</span>
        </a>
        <a href="{{ route('dashboard.reports') }}" class="nav-item">
            <i data-lucide="bar-chart-2" style="width:17px;height:17px"></i>
            <span class="s-label">Báo cáo</span>
        </a>
        <a href="{{ route('dashboard.notifications') }}" class="nav-item">
            <i data-lucide="bell" style="width:17px;height:17px"></i>
            <span class="s-label">Thông báo</span>
            @if(($unreadCount ?? 0) > 0)
                <span class="sidebar-badge badge-count">{{ $unreadCount }}</span>
            @endif
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar-sm">{{ substr(Auth::user()->name ?? 'GĐ', 0, 2) }}</div>
            <div class="s-user-info">
                <div class="s-name">{{ Auth::user()->name ?? 'Giám đốc' }}</div>
                <div class="s-role">Giám đốc</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="margin-top:8px">
            @csrf
            <button type="submit" class="nav-item" style="width:100%;border:none;background:none;cursor:pointer;color:rgba(255,255,255,.45);font-family:inherit">
                <i data-lucide="log-out" style="width:16px;height:16px"></i>
                <span class="s-label">Đăng xuất</span>
            </button>
        </form>
    </div>
</aside>

<!-- ============ APP BODY ============ -->
<div class="app-body">
    <!-- TOPBAR -->
    <header class="topbar">
        <button id="sidebarToggle" onclick="document.getElementById('sidebar').classList.toggle('collapsed')" style="background:none;border:none;cursor:pointer;color:#64748B;display:flex;align-items:center">
            <i data-lucide="menu" style="width:20px;height:20px"></i>
        </button>

        <h1 class="topbar-title">@yield('page_title', 'Dashboard Giám đốc')</h1>

        <div class="topbar-search">
            <i data-lucide="search" style="width:15px;height:15px"></i>
            <input type="text" placeholder="Tìm kiếm nhân viên, phòng ban...">
        </div>

        <div class="topbar-actions">
            <a href="{{ route('dashboard.notifications') }}" class="icon-btn">
                <i data-lucide="bell" style="width:17px;height:17px"></i>
                @if(($unreadCount ?? 0) > 0)<span class="notif-dot"></span>@endif
            </a>
            <div class="role-badge">
                <i data-lucide="shield-check" style="width:13px;height:13px"></i>
                Giám đốc
            </div>
        </div>
    </header>

    <!-- MAIN -->
    <main class="admin-main">
        @if(session('success'))
            <div class="flash success">
                <i data-lucide="check-circle" style="width:16px;height:16px"></i>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="flash error">
                <i data-lucide="alert-triangle" style="width:16px;height:16px"></i>
                {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </main>
</div>

<script>
    lucide.createIcons();
</script>
@yield('scripts')
</body>
</html>
