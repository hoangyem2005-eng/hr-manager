<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MobiFone WorkHub - Hệ thống quản lý công việc')</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Font Awesome (for some extra styling if needed) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

</head>
<body class="mf-app">

    <!-- SIDEBAR CONTAINER -->
    <div id="sidebar" class="mf-sidebar">
        <!-- Logo -->
        <div class="mf-brand">
            <div class="mf-logo-mark" aria-hidden="true">M</div>
            <div class="logo-text">
                <div class="mf-logo-lockup" aria-label="MobiFone">
                    <span class="mf-logo-blue">Mobi</span><span class="mf-logo-red">Fone</span>
                </div>
                <div class="mf-brand-subtitle">WORKHUB</div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="mf-nav custom-scrollbar">
            @php
                $authUser = Auth::user();
                $authRoleName = $authUser->role_display_name ?? 'Chưa có chức vụ';
                $route = Route::currentRouteName();
                $menuItems = [
                    ['route' => 'dashboard.index', 'label' => 'Dashboard', 'icon' => 'layout-dashboard'],
                    ['route' => 'dashboard.tasks', 'label' => 'Công việc', 'icon' => 'check-square'],
                ];

                // Giám đốc thấy menu quản trị nhân sự và phân quyền.
                if ($authUser && $authUser->isDirector()) {
                    $menuItems[] = ['route' => 'dashboard.members', 'label' => 'Thành viên', 'icon' => 'users'];
                }

                $menuItems[] = ['route' => 'dashboard.reports', 'label' => 'Báo cáo', 'icon' => 'bar-chart-2'];
                $menuItems[] = ['route' => 'dashboard.notifications', 'label' => 'Thông báo', 'icon' => 'bell', 'badge' => $unreadNotificationsCount ?? 0];

                if ($authUser && $authUser->isDirector()) {
                    $menuItems[] = ['route' => 'dashboard.roles', 'label' => 'Phân quyền', 'icon' => 'shield'];
                }
            @endphp

            @foreach($menuItems as $item)
                @php $isA = ($route === $item['route']); @endphp
                <a href="{{ route($item['route']) }}" 
                   title="{{ $item['label'] }}"
                   class="mf-nav-item {{ $isA ? 'active' : '' }}">
                    <i data-lucide="{{ $item['icon'] }}" class="w-[18px] h-[18px] flex-shrink-0"></i>
                    <span class="sidebar-text">{{ $item['label'] }}</span>
                    @if(isset($item['badge']) && $item['badge'] > 0)
                        <span class="sidebar-text mf-badge">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <!-- Sidebar Footer & Collapse Toggle -->
        <div class="mf-sidebar-footer space-y-2">
            <!-- User Profile (Quick View) -->
            <div class="mf-user-card">
                <div class="mf-avatar">
                    {{ substr($authUser->name ?? 'AD', 0, 2) }}
                </div>
                <div class="flex-1 min-w-0 user-info">
                    <div class="mf-user-name">{{ $authUser->name ?? 'Hoàng Thị Em' }}</div>
                    <div class="mf-user-role">{{ $authRoleName }}</div>
                </div>
                <!-- Logout form link -->
                <form action="{{ route('logout') }}" method="POST" id="logout-form" class="inline">
                    @csrf
                    <button type="submit" title="Đăng xuất" class="logout-btn" style="color:#a9c7ff">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>

            <!-- Collapse Button -->
            <button id="toggle-sidebar" class="mf-nav-item" style="justify-content:center;background:transparent;border:0;cursor:pointer;color:rgba(255,255,255,.6)">
                <i data-lucide="chevron-left" id="collapse-icon" class="w-4 h-4"></i>
                <span class="sidebar-text">Thu gọn</span>
            </button>
        </div>
    </div>

    <!-- MAIN APP CONTAINER -->
    <div class="mf-main-shell">
        <!-- HEADER -->
        <header class="mf-topbar">
            <!-- Breadcrumbs -->
            <div class="mf-breadcrumb">
                <span>WorkHub</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                <strong>@yield('page_title', 'Tổng quan')</strong>
            </div>

            <!-- Search -->
            <div class="mf-search">
                <i data-lucide="search" class="w-4 h-4"></i>
                <input placeholder="Tìm kiếm công việc, nhân viên..." />
            </div>

            <!-- Header actions -->
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard.notifications') }}" class="mf-icon-btn" aria-label="Thông báo">
                    <i data-lucide="bell" class="w-[18px] h-[18px]"></i>
                    @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                        <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-[#E4002B] text-white flex items-center justify-center font-bold rounded-full text-[9px]">{{ $unreadNotificationsCount }}</span>
                    @endif
                </a>
                <button class="mf-icon-btn" aria-label="Lịch làm việc">
                    <i data-lucide="calendar" class="w-[18px] h-[18px]"></i>
                </button>
                <div class="mf-avatar">
                    {{ substr($authUser->name ?? 'AD', 0, 2) }}
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT AREA -->
        <main class="mf-content">
            @if(session('success'))
                <div class="mf-flash success">
                    <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                    <span class="font-medium">{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mf-flash error">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                    <span class="font-medium">{{ session('error') }}</span>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Lucide Icons CDN Script -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        // Khởi tạo icons Lucide
        lucide.createIcons();

        // Xử lý collapse Sidebar
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('toggle-sidebar');
        const collapseIcon = document.getElementById('collapse-icon');
        const sidebarText = document.querySelectorAll('.sidebar-text');

        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');

            if (sidebar.classList.contains('collapsed')) {
                // Đổi icon sang chevron-right và đổi chữ thu gọn
                collapseIcon.setAttribute('data-lucide', 'chevron-right');
                toggleBtn.setAttribute('title', 'Mở rộng');
            } else {
                collapseIcon.setAttribute('data-lucide', 'chevron-left');
                toggleBtn.removeAttribute('title');
            }
            lucide.createIcons();
        });
    </script>
    @yield('scripts')
</body>
</html>
