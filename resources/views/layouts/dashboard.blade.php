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

    <!-- Style overrides for premium aesthetic -->
    <style>
        body {
            font-family: 'Be Vietnam Pro', sans-serif;
            background-color: #F8F9FA;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(0, 61, 165, 0.18);
            border-radius: 99px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(0, 61, 165, 0.35);
        }

        /* Sidebar transition */
        .sidebar-transition {
            transition: width 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .collapsed .sidebar-text {
            display: none;
        }
        .collapsed .logo-text {
            display: none;
        }
        .collapsed .user-info {
            display: none;
        }
        .collapsed .logout-btn {
            display: none;
        }
    </style>
</head>
<body class="overflow-hidden h-screen flex">

    <!-- SIDEBAR CONTAINER -->
    <div id="sidebar" class="sidebar-transition flex flex-col h-full bg-[#001F5B] text-white w-60 z-20 flex-shrink-0">
        <!-- Logo -->
        <div class="flex items-center gap-3 px-4 py-5 border-b border-white/10">
            <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
                <span class="text-white font-black text-sm">M</span>
            </div>
            <div class="logo-text">
                <div class="text-white font-bold text-sm leading-tight">MobiFone</div>
                <div class="text-blue-300 text-[10px] font-semibold tracking-[0.2em]">WORKHUB</div>
            </div>
        </div>

        <!-- Navigation Menu -->
        <nav class="flex-1 py-4 space-y-1 px-2 overflow-y-auto custom-scrollbar">
            @php
                $route = Route::currentRouteName();
                $menuItems = [
                    ['route' => 'dashboard.index', 'label' => 'Dashboard', 'icon' => 'layout-dashboard'],
                    ['route' => 'dashboard.tasks', 'label' => 'Công việc', 'icon' => 'check-square'],
                    ['route' => 'dashboard.members', 'label' => 'Thành viên', 'icon' => 'users'],
                    ['route' => 'dashboard.reports', 'label' => 'Báo cáo', 'icon' => 'bar-chart-2'],
                    ['route' => 'dashboard.notifications', 'label' => 'Thông báo', 'icon' => 'bell', 'badge' => 2],
                    ['route' => 'dashboard.roles', 'label' => 'Phân quyền', 'icon' => 'shield'],
                ];
            @endphp

            @foreach($menuItems as $item)
                @php $isA = ($route === $item['route']); @endphp
                <a href="{{ route($item['route']) }}" 
                   title="{{ $item['label'] }}"
                   class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-sm font-semibold transition-all relative {{ $isA ? 'bg-[#003DA5] text-white' : 'text-white/60 hover:bg-white/5 hover:text-white' }}">
                    <i data-lucide="{{ $item['icon'] }}" class="w-[18px] h-[18px] flex-shrink-0"></i>
                    <span class="sidebar-text">{{ $item['label'] }}</span>
                    @if(isset($item['badge']) && $item['badge'] > 0)
                        <span class="sidebar-text ml-auto px-1.5 py-0.5 rounded-full text-[10px] font-bold bg-[#E4002B] text-white">{{ $item['badge'] }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <!-- Sidebar Footer & Collapse Toggle -->
        <div class="border-t border-white/10 p-3 space-y-2">
            <!-- User Profile (Quick View) -->
            <div class="flex items-center gap-3 px-2 py-2 user-card">
                <div class="w-8 h-8 rounded-full bg-[#0057C8] flex items-center justify-center font-semibold text-white text-xs flex-shrink-0">
                    {{ substr(Auth::user()->name ?? 'AD', 0, 2) }}
                </div>
                <div class="flex-1 min-w-0 user-info">
                    <div class="text-white text-xs font-bold truncate">{{ Auth::user()->name ?? 'Hoàng Thị Em' }}</div>
                    <div class="text-blue-300 text-[10px]">{{ Auth::user()->role_id == 1 ? 'Admin' : 'Quản lý' }}</div>
                </div>
                <!-- Logout form link -->
                <form action="{{ route('logout') }}" method="POST" id="logout-form" class="inline">
                    @csrf
                    <button type="submit" title="Đăng xuất" class="logout-btn text-blue-300 hover:text-white transition-colors">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                    </button>
                </form>
            </div>

            <!-- Collapse Button -->
            <button id="toggle-sidebar" class="w-full flex items-center justify-center gap-2 py-2 rounded-xl text-xs font-medium text-white/40 hover:bg-white/10 hover:text-white transition-colors">
                <i data-lucide="chevron-left" id="collapse-icon" class="w-4 h-4"></i>
                <span class="sidebar-text">Thu gọn</span>
            </button>
        </div>
    </div>

    <!-- MAIN APP CONTAINER -->
    <div class="flex flex-col flex-1 min-w-0 overflow-hidden">
        <!-- HEADER -->
        <header class="h-16 bg-white border-b border-gray-200 flex items-center px-6 gap-4 flex-shrink-0">
            <!-- Breadcrumbs -->
            <div class="flex items-center gap-1.5 text-sm flex-1 min-w-0">
                <span class="text-gray-400">Dashboard</span>
                <i data-lucide="chevron-right" class="w-3.5 h-3.5 text-gray-400"></i>
                <span class="text-[#001F5B] font-bold">@yield('page_title', 'Tổng quan')</span>
            </div>

            <!-- Search -->
            <div class="relative hidden md:block">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl outline-none w-60 bg-[#F1F3F5] focus:border-[#003DA5] focus:bg-white transition-all"
                       placeholder="Tìm kiếm công việc, nhân viên..." />
            </div>

            <!-- Header actions -->
            <div class="flex items-center gap-3">
                <a href="{{ route('dashboard.notifications') }}" class="relative w-9 h-9 rounded-xl flex items-center justify-center hover:bg-gray-100 transition-colors">
                    <i data-lucide="bell" class="w-[18px] h-[18px] text-gray-700"></i>
                    <span class="absolute top-1 right-1 w-4 h-4 bg-[#E4002B] text-white flex items-center justify-center font-bold rounded-full text-[9px]">2</span>
                </a>
                <button class="w-9 h-9 rounded-xl flex items-center justify-center hover:bg-gray-100 transition-colors">
                    <i data-lucide="calendar" class="w-[18px] h-[18px] text-gray-700"></i>
                </button>
                <div class="w-9 h-9 rounded-xl bg-[#003DA5] text-white flex items-center justify-center font-semibold text-sm">
                    {{ substr(Auth::user()->name ?? 'AD', 0, 2) }}
                </div>
            </div>
        </header>

        <!-- MAIN CONTENT AREA -->
        <main class="flex-1 overflow-y-auto p-8 bg-[#F8F9FA]">
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
            sidebar.classList.toggle('w-60');
            sidebar.classList.toggle('w-16');
            sidebar.classList.toggle('collapsed');

            if (sidebar.classList.contains('w-16')) {
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
