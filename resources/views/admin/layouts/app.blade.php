<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Giám đốc - MobiFone HR')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @yield('head_extra')
</head>
<body class="mf-app">
@php
    $authUser = Auth::user();
    $authRoleName = $authUser->role_display_name ?? 'Giám đốc';
@endphp

<!-- ============ SIDEBAR ============ -->
<aside class="mf-sidebar" id="sidebar">
    <div class="mf-brand">
        <div class="mf-logo-mark" aria-hidden="true">M</div>
        <div class="s-logo-text logo-text">
            <div class="mf-logo-lockup" aria-label="MobiFone">
                <span class="mf-logo-blue">Mobi</span><span class="mf-logo-red">Fone</span>
            </div>
            <div class="mf-brand-subtitle">ADMIN CONSOLE</div>
        </div>
    </div>

    <nav class="mf-nav">
        <span class="mf-nav-section s-label">Tổng quan</span>
        <a href="{{ route('admin.dashboard') }}" class="mf-nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i data-lucide="layout-dashboard" style="width:17px;height:17px"></i>
            <span class="s-label">Dashboard</span>
        </a>

        <span class="mf-nav-section s-label">Nhân sự</span>
        @if(false)
        <a href="{{ route('admin.dashboard') }}#nhan-su" class="mf-nav-item">
            <i data-lucide="users" style="width:17px;height:17px"></i>
            <span class="s-label">Tất cả nhân viên</span>
        </a>
        @endif
        <a href="{{ route('phongban.danhsach') }}" class="mf-nav-item {{ request()->routeIs('phongban.*') ? 'active' : '' }}">
            <i data-lucide="building-2" style="width:17px;height:17px"></i>
            <span class="s-label">Phòng ban</span>
        </a>

        <span class="mf-nav-section s-label">Vận hành</span>
        <a href="{{ route('dashboard.tasks') }}" class="mf-nav-item {{ request()->routeIs('dashboard.tasks') ? 'active' : '' }}">
            <i data-lucide="kanban" style="width:17px;height:17px"></i>
            <span class="s-label">Công việc</span>
        </a>
        <a href="{{ route('dashboard.reports') }}" class="mf-nav-item">
            <i data-lucide="bar-chart-2" style="width:17px;height:17px"></i>
            <span class="s-label">Báo cáo</span>
        </a>
        <a href="{{ route('dashboard.notifications') }}" class="mf-nav-item">
            <i data-lucide="bell" style="width:17px;height:17px"></i>
            <span class="s-label">Thông báo</span>
            @if(($unreadCount ?? 0) > 0)
                <span class="mf-badge badge-count">{{ $unreadCount }}</span>
            @endif
        </a>
    </nav>

    <div class="mf-sidebar-footer">
        <div class="mf-user-card">
            <div class="mf-avatar">{{ substr($authUser->name ?? 'GĐ', 0, 2) }}</div>
            <div class="s-user-info">
                <div class="mf-user-name">{{ $authUser->name ?? 'Giám đốc' }}</div>
                <div class="mf-user-role">{{ $authRoleName }}</div>
            </div>
        </div>
        <form action="{{ route('logout') }}" method="POST" style="margin-top:8px">
            @csrf
            <button type="submit" class="mf-nav-item" style="border:none;background:transparent;cursor:pointer;color:rgba(255,255,255,.62);font-family:inherit">
                <i data-lucide="log-out" style="width:16px;height:16px"></i>
                <span class="s-label">Đăng xuất</span>
            </button>
        </form>
    </div>
</aside>

<!-- ============ APP BODY ============ -->
<div class="mf-main-shell">
    <!-- TOPBAR -->
    <header class="mf-topbar">
        <button id="sidebarToggle" class="mf-icon-btn" onclick="document.getElementById('sidebar').classList.toggle('collapsed')" aria-label="Thu gọn menu">
            <i data-lucide="menu" style="width:20px;height:20px"></i>
        </button>

        <h1 class="mf-topbar-title">@yield('page_title', 'Dashboard Giám đốc')</h1>

        <div class="mf-search">
            <i data-lucide="search" style="width:15px;height:15px"></i>
            <input type="text" placeholder="Tìm kiếm nhân viên, phòng ban...">
        </div>

        <div style="display:flex;align-items:center;gap:8px">
            <a href="{{ route('dashboard.notifications') }}" class="mf-icon-btn" aria-label="Thông báo">
                <i data-lucide="bell" style="width:17px;height:17px"></i>
                @if(($unreadCount ?? 0) > 0)<span class="mf-dot"></span>@endif
            </a>
            <div class="mf-role-pill">
                <i data-lucide="shield-check" style="width:13px;height:13px"></i>
                {{ $authRoleName }}
            </div>
        </div>
    </header>

    <!-- MAIN -->
    <main class="mf-content">
        @if(session('success') || session('thongbao'))
            <div class="mf-flash success">
                <i data-lucide="check-circle" style="width:16px;height:16px"></i>
                {{ session('success') ?? session('thongbao') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mf-flash error">
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
