<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Giám đốc - MobiFone HR')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --mf-navy: #001F5B;
            --mf-blue: #003DA5;
            --mf-blue-light: #E8F0FE;
            --mf-red: #E4002B;
        }
        body { min-height: 100vh; font-family: Inter, sans-serif; background: #F5F7FB; color: #111827; }
        .admin-shell { min-height: 100vh; display: grid; grid-template-columns: 248px minmax(0,1fr); }
        .admin-side { background: linear-gradient(180deg, var(--mf-navy), var(--mf-blue)); color: #fff; padding: 18px; display: flex; flex-direction: column; gap: 18px; }
        .brand { display: flex; align-items: center; gap: 10px; padding: 8px 6px 16px; border-bottom: 1px solid rgba(255,255,255,.1); }
        .brand-mark { width: 38px; height: 38px; border-radius: 8px; background: var(--mf-red); display: grid; place-items: center; font-weight: 900; }
        .brand strong { display: block; font-size: 14px; }
        .brand span { color: #B9D3FF; font-size: 10px; letter-spacing: .16em; text-transform: uppercase; }
        .nav { display: grid; gap: 6px; }
        .nav a { position: relative; height: 40px; border-radius: 8px; display: flex; align-items: center; gap: 10px; color: #D8E7FF; text-decoration: none; padding: 0 10px; font-size: 13px; font-weight: 800; border: 1px solid transparent; }
        .nav a:hover { color: #fff; background: rgba(255,255,255,.1); border-color: rgba(255,255,255,.12); }
        .nav a.active { color: var(--mf-navy); background: #fff; border-color: rgba(255,255,255,.24); box-shadow: 0 10px 24px rgba(0,31,91,.18); }
        .nav a.active::before { content: ""; position: absolute; left: 0; top: 9px; bottom: 9px; width: 3px; border-radius: 0 4px 4px 0; background: var(--mf-red); }
        .nav i { width: 17px; height: 17px; }
        .side-foot { margin-top: auto; border-top: 1px solid rgba(255,255,255,.1); padding-top: 14px; color: #B9D3FF; font-size: 12px; line-height: 1.5; }
        .admin-main { min-width: 0; display: flex; flex-direction: column; }
        .topbar { height: 64px; background: #fff; border-bottom: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: space-between; padding: 0 24px; }
        .crumb { color: #6B7280; font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: .08em; }
        .top-title { color: #111827; font-size: 18px; font-weight: 900; margin-top: 3px; }
        .top-actions { display: flex; align-items: center; gap: 10px; }
        .icon-btn { width: 38px; height: 38px; border-radius: 8px; border: 1px solid #E5E7EB; background: #fff; color: #374151; display: grid; place-items: center; cursor: pointer; }
        .content { padding: 24px; display: grid; gap: 18px; }
        .module-hero { background: #fff; border: 1px solid #E5E7EB; border-radius: 8px; padding: 20px; display: flex; justify-content: space-between; align-items: center; gap: 18px; }
        .module-kicker { color: #E4002B; font-size: 11px; font-weight: 900; letter-spacing: .16em; text-transform: uppercase; }
        .module-title { color: #111827; font-size: 26px; font-weight: 900; margin-top: 6px; }
        .module-desc { color: #6B7280; font-size: 13px; line-height: 1.6; margin-top: 6px; max-width: 720px; }
        .btn { min-height: 38px; border-radius: 8px; border: 1px solid transparent; display: inline-flex; align-items: center; justify-content: center; gap: 8px; padding: 0 13px; text-decoration: none; font-family: inherit; font-size: 13px; font-weight: 900; cursor: pointer; }
        .btn.primary { background: #E4002B; color: #fff; }
        .btn.secondary { background: #fff; border-color: #D1D5DB; color: #374151; }
        .btn.danger { background: #FEF2F2; border-color: #FECACA; color: #B91C1C; }
        .panel { background: #fff; border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden; }
        .panel-head { min-height: 54px; padding: 12px 16px; border-bottom: 1px solid #E5E7EB; display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .panel-title { font-size: 14px; font-weight: 900; color: #111827; display: flex; align-items: center; gap: 8px; }
        .filters { display: flex; gap: 8px; flex-wrap: wrap; }
        .input, .select, .textarea { border: 1px solid #D1D5DB; border-radius: 8px; padding: 0 11px; min-height: 40px; font-family: inherit; font-size: 13px; background: #fff; color: #111827; }
        .textarea { min-height: 96px; padding-top: 10px; resize: vertical; }
        .table-wrap { overflow-x: auto; }
        .table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .table th { background: #F9FAFB; color: #6B7280; text-align: left; padding: 12px 14px; font-size: 10px; text-transform: uppercase; letter-spacing: .08em; }
        .table td { padding: 14px; border-top: 1px solid #F3F4F6; vertical-align: middle; color: #374151; }
        .code { font-family: ui-monospace, SFMono-Regular, Consolas, monospace; color: #9CA3AF; font-size: 12px; }
        .status { display: inline-flex; align-items: center; min-height: 22px; border-radius: 999px; padding: 2px 9px; font-size: 11px; font-weight: 900; background: #F3F4F6; color: #4B5563; }
        .status.done { background: #F0FDF4; color: #15803D; }
        .status.doing { background: #FFFBEB; color: #B45309; }
        .status.overdue { background: #FEF2F2; color: #B91C1C; }
        .status.review { background: #EFF6FF; color: #1D4ED8; }
        .grid-form { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 14px; padding: 18px; }
        .field.full { grid-column: 1 / -1; }
        .field label { display: block; color: #374151; font-size: 12px; font-weight: 900; margin-bottom: 6px; }
        .form-actions { grid-column: 1 / -1; display: flex; justify-content: flex-end; gap: 10px; border-top: 1px solid #E5E7EB; padding-top: 16px; }
        .flash { border-radius: 8px; padding: 12px 14px; font-size: 13px; font-weight: 800; }
        .flash.success { background: #F0FDF4; color: #15803D; border: 1px solid #BBF7D0; }
        .flash.error { background: #FEF2F2; color: #B91C1C; border: 1px solid #FECACA; }
        .empty { padding: 28px; color: #9CA3AF; text-align: center; }
        @media (max-width: 900px) { .admin-shell { grid-template-columns: 1fr; } .admin-side { position: static; } .module-hero { align-items: flex-start; flex-direction: column; } .grid-form { grid-template-columns: 1fr; } }

        /* MobiFone Brand Logo Styles */
        .mf-logo-i {
            position: relative;
            display: inline-block;
            color: inherit;
            font-style: normal;
            line-height: inherit;
            margin-right: -0.06em !important;
        }
        .mf-logo-i::after {
            content: "";
            position: absolute;
            bottom: 0.66em;
            left: 50%;
            transform: translateX(-50%);
            width: 0.15em;
            height: 0.15em;
            background-color: #e4002b !important;
            border-radius: 0;
            display: block;
            z-index: 10;
        }
        .mf-logo-lockup {
            font-family: Arial, Helvetica, sans-serif !important;
            letter-spacing: -0.05em !important;
        }
        .mf-logo-lockup span {
            font-family: Arial, Helvetica, sans-serif !important;
            font-weight: 700 !important;
            letter-spacing: -0.05em !important;
        }
    </style>
    @yield('head_extra')
</head>
<body>
@php $routeName = Route::currentRouteName(); @endphp
<div class="admin-shell">
    <aside class="admin-side">
        <div class="brand">
            <div class="brand-mark">GĐ</div>
            <div><strong>MobiFone HR</strong><span>Director console</span></div>
        </div>
        <nav class="nav">
            <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}"><i data-lucide="layout-dashboard"></i>Dashboard</a>
            <a class="{{ request()->routeIs('congviec.*') && request('mode') !== 'proposal' ? 'active' : '' }}" href="{{ route('congviec.danhsach') }}"><i data-lucide="clipboard-list"></i>Công việc</a>
            
            @php
                $directorProposalCount = \App\Models\Task::where('is_proposal', true)->where('proposal_step', 2)->count();
            @endphp
            <a class="{{ request('mode') === 'proposal' ? 'active' : '' }}" href="{{ route('dashboard.tasks', ['mode' => 'proposal', 'view' => 'list']) }}">
                <i data-lucide="send"></i>Đề xuất của NV
                @if($directorProposalCount > 0)
                    <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#E4002B;position:absolute;right:12px;top:16px;"></span>
                @endif
            </a>

            <a class="{{ request()->routeIs('phongban.*') ? 'active' : '' }}" href="{{ route('phongban.danhsach') }}"><i data-lucide="building-2"></i>Phòng ban</a>
            <a class="{{ request()->routeIs('tiendo.*') ? 'active' : '' }}" href="{{ route('tiendo.index') }}"><i data-lucide="activity"></i>Tiến độ</a>
            <a href="{{ route('admin.notifications') }}"><i data-lucide="bell"></i>Thông báo</a>
        </nav>
        <a class="side-foot" href="{{ route('profile.show') }}" title="Trang cá nhân" style="display:block;text-decoration:none;color:inherit">
            <strong>{{ Auth::user()->name ?? 'Giám đốc' }}</strong><br>
            Phiên điều hành Giám đốc
        </a>
    </aside>
    <section class="admin-main">
        <header class="topbar">
            <div><div class="crumb">Giám đốc / Điều hành</div><div class="top-title">@yield('page_title', 'Quản trị')</div></div>
            <div class="top-actions">
                <a class="icon-btn" href="{{ route('admin.notifications') }}" title="Thông báo"><i data-lucide="bell"></i></a>
                <form action="{{ route('logout') }}" method="POST">@csrf<button class="icon-btn" type="submit" title="Đăng xuất"><i data-lucide="log-out"></i></button></form>
            </div>
        </header>
        <main class="content">
            @if(session('thongbao') || session('success'))
                <div class="flash success">{{ session('thongbao') ?? session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="flash error">{{ session('error') }}</div>
            @endif
            @yield('content')
        </main>
    </section>
</div>
<script>lucide.createIcons();</script>
@yield('scripts')
</body>
</html>
