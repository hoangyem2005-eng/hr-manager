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

    <!-- Flatpickr CSS & JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html, body { height: 100%; overflow: hidden; }
        body { font-family: 'Inter', sans-serif; background: #F2F6FC; display: flex; }

        /* SIDEBAR */
        .sidebar {
            width: 230px;
            height: 100vh;
            background: linear-gradient(180deg, #001F5B 0%, #003DA5 100%);
            display: flex; flex-direction: column; flex-shrink: 0;
            z-index: 30;
            transition: width .25s cubic-bezier(.4,0,.2,1);
        }
        .sidebar.collapsed { width: 64px; }
        .sidebar.collapsed .s-label,
        .sidebar.collapsed .s-logo-text,
        .sidebar.collapsed .s-user-info { display: none; }

        .sidebar-logo {
            display: flex; align-items: center; gap: 10px;
            padding: 20px 16px 18px;
            border-bottom: 1px solid rgba(255,255,255,.1);
        }
        .logo-icon {
            width: 38px; height: 38px;
            background: #fff;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 15px; color: #003DA5;
            flex-shrink: 0;
            box-shadow: inset 5px 0 0 #E4002B;
        }
        .mf-logo-word { display: inline-flex; align-items: baseline; background: #fff; border-radius: 7px; padding: 4px 9px; line-height: 1; box-shadow: 0 6px 18px rgba(0,0,0,.12); }
        .mf-logo-word .blue { color: #003DA5; font-size: 17px; font-weight: 900; letter-spacing: -.03em; }
        .mf-logo-word .red { color: #E4002B; font-size: 17px; font-weight: 900; letter-spacing: -.03em; }
        .s-logo-text .sub   { margin-top: 6px; font-size: 10px; color: #BFD8FF; letter-spacing: .12em; font-weight: 700; }

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
        .nav-item.active { background: #fff; color: #003DA5; font-weight: 800; box-shadow: inset 3px 0 0 #E4002B; }

        .sidebar-footer {
            border-top: 1px solid rgba(255,255,255,.1);
            padding: 12px 10px;
        }
        .user-card {
            display: flex; align-items: center; gap: 10px;
            padding: 10px; border-radius: 10px;
            background: rgba(255,255,255,.1);
            text-decoration: none; color: inherit;
        }
        .user-card:hover { background: rgba(255,255,255,.16); }
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
            border-bottom: 1px solid #D8E4F5;
            display: flex; align-items: center;
            padding: 0 24px; gap: 16px; flex-shrink: 0;
        }
        .topbar-title { font-size: 16px; font-weight: 800; color: #001F5B; flex: 1; }
        .role-badge {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 6px 14px; border-radius: 99px;
            background: #E8F0FE; border: 1px solid #B9CDF5;
            font-size: 11px; font-weight: 700; color: #003DA5;
        }
        .dept-chip {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 5px 12px; border-radius: 99px;
            background: #F5F8FE; border: 1px solid #C7D8F8;
            font-size: 11px; font-weight: 700; color: #003DA5;
        }

        main.manager-main {
            flex: 1; overflow-y: auto;
            padding: 24px 28px;
            background: #F2F6FC;
        }
        main.manager-main::-webkit-scrollbar { width: 5px; }
        main.manager-main::-webkit-scrollbar-thumb { background: rgba(37,99,235,.15); border-radius:99px; }

        .flash { display:flex;align-items:center;gap:10px;padding:14px 16px;border-radius:12px;font-size:13px;font-weight:500;margin-bottom:20px;animation:fadeSlide .3s ease; }
        .flash.success { background:#F0FDF4;border:1px solid #BBF7D0;color:#16A34A; }
        .flash.error   { background:#FFF1F2;border:1px solid #FECDD3;color:#E11D48; }
        @keyframes fadeSlide { from{opacity:0;transform:translateY(-8px)}to{opacity:1;transform:translateY(0)} }

        /* Custom Pagination Styling */
        .hidden { display: none !important; }
        .flex { display: flex !important; }
        .items-center { align-items: center !important; }
        .justify-between { justify-content: space-between !important; }
        .flex-1 { flex: 1 1 0% !important; }
        .w-5 { width: 16px !important; }
        .h-5 { height: 16px !important; }
        
        nav[role="navigation"] svg {
            width: 16px !important;
            height: 16px !important;
            display: inline-block;
            vertical-align: middle;
        }
        
        nav[role="navigation"] span.relative,
        nav[role="navigation"] a.relative {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 32px;
            height: 32px;
            padding: 0 10px;
            border: 1px solid #D1D5DB;
            border-radius: 6px;
            background: #fff;
            color: #374151;
            text-decoration: none;
            font-weight: 700;
            transition: all 0.2s;
        }
        nav[role="navigation"] a.relative:hover {
            background: #F3F4F6;
            border-color: #9CA3AF;
        }
        nav[role="navigation"] span.relative[aria-current="page"] {
            background: #003DA5;
            color: #fff;
            border-color: #003DA5;
        }
        nav[role="navigation"] span.relative[aria-disabled="true"] {
            color: #9CA3AF;
            background: #F9FAFB;
            cursor: not-allowed;
            opacity: 0.6;
        }
        
        @media (min-width: 640px) {
            .sm\:hidden { display: none !important; }
            .sm\:flex { display: flex !important; }
            .sm\:flex-1 { flex: 1 1 0% !important; }
            .sm\:items-center { align-items: center !important; }
            .sm\:justify-between { justify-content: space-between !important; }
        }
    </style>
    @yield('head_extra')
</head>
<body>
@php
    $authUser = Auth::user();
    $authRoleName = $authUser->role_display_name ?? 'Trưởng phòng';
@endphp
<aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
        <div class="s-logo-text">
            <div class="mf-logo-word"><span class="blue">mob<span class="mf-logo-i">ı</span></span><span class="red">fone</span></div>
            <div class="sub">GIAO DIỆN TRƯỞNG PHÒNG</div>
        </div>
    </div>

    <nav class="sidebar-nav">
        <span class="nav-section s-label">Tổng quan</span>
        <a href="{{ route('manager.dashboard') }}" class="nav-item {{ request()->routeIs('manager.dashboard') ? 'active' : '' }}">
            <i data-lucide="layout-dashboard" style="width:17px;height:17px"></i>
            <span class="s-label">Tổng quan</span>
        </a>

        <span class="nav-section s-label">Phòng ban</span>
        <a href="{{ route('manager.members') }}" class="nav-item {{ request()->routeIs('manager.members') ? 'active' : '' }}">
            <i data-lucide="users" style="width:17px;height:17px"></i>
            <span class="s-label">Nhân viên phòng</span>
        </a>
        <a href="{{ route('manager.tasks') }}" class="nav-item {{ (request()->routeIs('manager.tasks') && request('mode') !== 'progress' && request('mode') !== 'proposal') || (request()->routeIs('manager.tasks.show') && request('from') !== 'progress' && request('from') !== 'proposal') ? 'active' : '' }}">
            <i data-lucide="clipboard-list" style="width:17px;height:17px"></i>
            <span class="s-label">Giao công việc</span>
        </a>
        
        @php
            $deptMemberIds = \App\Models\User::where('department_id', Auth::user()->department_id)->pluck('id');
            $managerProposalCount = \App\Models\Task::where('is_proposal', true)
                ->where('proposal_step', 1)
                ->whereIn('assigned_to', $deptMemberIds)
                ->count();
        @endphp
        <a href="{{ route('manager.tasks', ['mode' => 'proposal', 'view' => 'list']) }}" class="nav-item {{ request('mode') === 'proposal' ? 'active' : '' }}">
            <i data-lucide="send" style="width:17px;height:17px"></i>
            <span class="s-label" style="position:relative">Đề xuất của NV
                @if($managerProposalCount > 0)
                    <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#E4002B;position:absolute;right:-12px;top:0;"></span>
                @endif
            </span>
        </a>

        <a href="{{ route('manager.tasks', ['mode' => 'progress', 'view' => 'list']) }}" class="nav-item {{ (request()->routeIs('manager.tasks') && request('mode') === 'progress') || (request()->routeIs('manager.tasks.show') && request('from') === 'progress') ? 'active' : '' }}">
            <i data-lucide="kanban" style="width:17px;height:17px"></i>
            <span class="s-label">Tiến độ công việc</span>
        </a>

        <span class="nav-section s-label">Báo cáo</span>
        <a href="{{ route('manager.reports') }}" class="nav-item {{ request()->routeIs('manager.reports') ? 'active' : '' }}">
            <i data-lucide="bar-chart-2" style="width:17px;height:17px"></i>
            <span class="s-label">Báo cáo</span>
        </a>
        <a href="{{ route('manager.notifications') }}" class="nav-item {{ request()->routeIs('manager.notifications') ? 'active' : '' }}">
            <i data-lucide="bell" style="width:17px;height:17px"></i>
            <span class="s-label">Thông báo</span>
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="{{ route('profile.show') }}" class="user-card" title="Trang cá nhân">
            <div class="user-avatar-sm">{{ substr($authUser->name ?? 'QL', 0, 2) }}</div>
            <div class="s-user-info">
                <div class="s-name">{{ $authUser->name ?? 'Trưởng phòng' }}</div>
                <div class="s-role">{{ $authRoleName }}</div>
            </div>
        </a>
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
            Phòng: {{ $authUser->department->TENPHONG ?? 'Chưa xác định' }}
        </div>
        <div class="role-badge">
            <i data-lucide="crown" style="width:12px;height:12px"></i>
            {{ $authRoleName }}
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

<script>
    lucide.createIcons();
    // Khởi tạo Flatpickr cho các input[type="date"]
    document.addEventListener('DOMContentLoaded', function() {
        // Override prototype reset to support programmatical form.reset()
        const originalReset = HTMLFormElement.prototype.reset;
        HTMLFormElement.prototype.reset = function() {
            originalReset.call(this);
            setTimeout(() => {
                this.querySelectorAll('.flatpickr-input').forEach(el => {
                    if (el._flatpickr) {
                        el._flatpickr.setDate(el.value || new Date());
                    }
                });
            }, 0);
        };

        document.querySelectorAll('input[type="date"]').forEach(function(el) {
            flatpickr(el, {
                locale: 'vn',
                altInput: true,
                altFormat: 'd/m/Y',
                dateFormat: 'Y-m-d',
                defaultDate: el.value || new Date(),
                allowInput: true
            });
        });

        // Lắng nghe sự kiện reset form để đồng bộ lại Flatpickr về ngày mặc định
        document.addEventListener('reset', function(e) {
            const form = e.target;
            setTimeout(function() {
                form.querySelectorAll('.flatpickr-input').forEach(function(el) {
                    if (el._flatpickr) {
                        el._flatpickr.setDate(el.value || new Date());
                    }
                });
            }, 0);
        });
    });
</script>
@yield('scripts')
</body>
</html>
