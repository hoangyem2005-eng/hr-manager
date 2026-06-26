<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Manager — MobiFone HR')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body { height: 100%; overflow: hidden; }
        body { font-family: 'Inter', sans-serif; background: #EFF6FF; display: flex; }

        /* SIDEBAR */
        .sidebar {
            width: 230px;
            height: 100vh;
            background: linear-gradient(180deg, #1E3A5F 0%, #2563EB 100%);
            display: flex; flex-direction: column; flex-shrink: 0;
            z-index: 30;
            transition: width .25s cubic-bezier(.4,0,.2,1);
        }
        .sidebar.collapsed { width: 64px; }
        .sidebar.collapsed .s-label,
        .sidebar.collapsed .s-logo-text,
        .sidebar.collapsed .s-user-info { display: none; }

        .sidebar-logo {
            display: flex; align-items: center; gap: 12px;
            padding: 20px 16px 18px;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .logo-icon {
            width: 38px; height: 38px;
            background: rgba(255,255,255,.2);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 14px; color: #fff;
            flex-shrink: 0;
        }
        .s-logo-text .brand { font-size: 14px; font-weight: 700; color: #fff; }
        .s-logo-text .sub   { font-size: 10px; color: rgba(255,255,255,.5); letter-spacing: .12em; font-weight: 600; }

        nav.sidebar-nav { flex: 1; padding: 12px 10px; overflow-y: auto; }
        nav.sidebar-nav::-webkit-scrollbar { width: 3px; }
        nav.sidebar-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius:99px; }

        .nav-section { font-size: 9px; font-weight: 700; letter-spacing: .12em; color: rgba(255,255,255,.35); padding: 14px 10px 6px; text-transform: uppercase; }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            color: rgba(255,255,255,.6);
            font-size: 13px; font-weight: 500;
            text-decoration: none; transition: all .18s; margin-bottom: 2px;
        }
        .nav-item:hover { background: rgba(255,255,255,.1); color: #fff; }
        .nav-item.active { background: rgba(255,255,255,.2); color: #fff; font-weight: 600; }

        .sidebar-footer {
            border-top: 1px solid rgba(255,255,255,.1);
            padding: 12px 10px;
        }
        .user-card {
            display: flex; align-items: center; gap: 10px;
            padding: 10px; border-radius: 10px;
            background: rgba(255,255,255,.1);
        }
        .user-avatar-sm {
            width: 34px; height: 34px; border-radius: 10px;
            background: rgba(255,255,255,.3);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 12px; color: #fff; flex-shrink: 0;
        }
        .s-user-info .s-name { font-size: 12px; font-weight: 600; color: #fff; }
        .s-user-info .s-role { font-size: 10px; color: rgba(255,255,255,.5); }

        /* LAYOUT */
        .app-body { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

        .topbar {
            height: 64px; background: #fff;
            border-bottom: 1px solid #DBEAFE;
            display: flex; align-items: center;
            padding: 0 24px; gap: 16px; flex-shrink: 0;
        }
        .topbar-title { font-size: 16px; font-weight: 700; color: #1E3A5F; flex: 1; }
        .role-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 14px; border-radius: 99px;
            background: #DBEAFE; border: 1px solid #BFDBFE;
            font-size: 11px; font-weight: 600; color: #1D4ED8;
        }
        .dept-chip {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 12px; border-radius: 99px;
            background: #EFF6FF; border: 1px solid #BFDBFE;
            font-size: 11px; font-weight: 600; color: #2563EB;
        }

        main.manager-main {
            flex: 1; overflow-y: auto;
            padding: 24px 28px;
            background: #EFF6FF;
        }
        main.manager-main::-webkit-scrollbar { width: 5px; }
        main.manager-main::-webkit-scrollbar-thumb { background: rgba(37,99,235,.15); border-radius:99px; }

        .flash { display:flex;align-items:center;gap:10px;padding:14px 16px;border-radius:12px;font-size:13px;font-weight:500;margin-bottom:20px;animation:fadeSlide .3s ease; }
        .flash.success { background:#F0FDF4;border:1px solid #BBF7D0;color:#16A34A; }
        .flash.error   { background:#FFF1F2;border:1px solid #FECDD3;color:#E11D48; }
        @keyframes fadeSlide { from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)} }
    </style>
    @yield('head_extra')
</head>
<body>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="logo-icon">TP</div>
        <div class="s-logo-text">
            <div class="brand">MobiFone</div>
            <div class="sub">TRƯỞNG PHÒNG</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <span class="nav-section s-label">Tổng quan</span>
        <a href="{{ route('manager.dashboard') }}" class="nav-item {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
            <i data-lucide="layout-dashboard" style="width:17px;height:17px"></i>
            <span class="s-label">Dashboard</span>
        </a>

        <span class="nav-section s-label">Phòng ban</span>
        <a href="{{ route('manager.dashboard') }}#team" class="nav-item">
            <i data-lucide="users" style="width:17px;height:17px"></i>
            <span class="s-label">Nhân viên phòng</span>
        </a>
        <a href="{{ route('manager.dashboard') }}#giao-viec" class="nav-item">
            <i data-lucide="clipboard-list" style="width:17px;height:17px"></i>
            <span class="s-label">Giao công việc</span>
        </a>
        <a href="{{ route('manager.dashboard') }}#cong-viec" class="nav-item">
            <i data-lucide="kanban" style="width:17px;height:17px"></i>
            <span class="s-label">Tiến độ công việc</span>
        </a>

        <span class="nav-section s-label">Báo cáo</span>
        <a href="{{ route('dashboard.reports') }}" class="nav-item">
            <i data-lucide="bar-chart-2" style="width:17px;height:17px"></i>
            <span class="s-label">Báo cáo</span>
        </a>
        <a href="{{ route('dashboard.notifications') }}" class="nav-item">
            <i data-lucide="bell" style="width:17px;height:17px"></i>
            <span class="s-label">Thông báo</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar-sm">{{ substr(Auth::user()->name ?? 'TP', 0, 2) }}</div>
            <div class="s-user-info">
                <div class="s-name">{{ Auth::user()->name ?? 'Trưởng phòng' }}</div>
                <div class="s-role">Trưởng phòng</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="margin-top:8px">
            @csrf
            <button type="submit" class="nav-item" style="width:100%;border:none;background:none;cursor:pointer;font-family:inherit">
                <i data-lucide="log-out" style="width:16px;height:16px"></i>
                <span class="s-label">Đăng xuất</span>
            </button>
        </form>
    </div>
</aside>

<div class="app-body">
    <header class="topbar">
        <button onclick="document.getElementById('sidebar').classList.toggle('collapsed')" style="background:none;border:none;cursor:pointer;color:#64748B;display:flex;align-items:center">
            <i data-lucide="menu" style="width:20px;height:20px"></i>
        </button>
        <h1 class="topbar-title">@yield('page_title', 'Dashboard Trưởng phòng')</h1>
        <div class="dept-chip">
            <i data-lucide="building-2" style="width:12px;height:12px"></i>
            Phòng: {{ Auth::user()->department->TENPHONG ?? 'Chưa xác định' }}
        </div>
        <div class="role-badge">
            <i data-lucide="crown" style="width:12px;height:12px"></i>
            Trưởng phòng
        </div>
    </header>

    <main class="manager-main">
        @if(session('success'))
            <div class="flash success"><i data-lucide="check-circle" style="width:16px;height:16px"></i> {{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="flash error"><i data-lucide="alert-triangle" style="width:16px;height:16px"></i> {{ session('error') }}</div>
        @endif
        @yield('content')
    </main>
</div>

<script>lucide.createIcons();</script>
@yield('scripts')
</body>
</html>
