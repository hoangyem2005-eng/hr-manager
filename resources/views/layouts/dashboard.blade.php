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
<<<<<<< HEAD
                <div class="relative">
                    <button id="bell-dropdown-btn" class="relative w-9 h-9 rounded-xl flex items-center justify-center hover:bg-gray-100 transition-colors focus:outline-none">
                        <i data-lucide="bell" class="w-[18px] h-[18px] text-gray-700"></i>
                        @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                            <span id="bell-badge" class="absolute top-1 right-1 w-4 h-4 bg-[#E4002B] text-white flex items-center justify-center font-bold rounded-full text-[9px]">{{ $unreadNotificationsCount }}</span>
                        @endif
                    </button>
                    <!-- Bell Dropdown Panel -->
                    <div id="bell-dropdown-panel" class="absolute right-0 mt-2 w-80 bg-white rounded-2xl border border-gray-100 shadow-xl hidden z-50 overflow-hidden">
                        <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                            <span class="text-xs font-bold text-[#001F5B]">Thông báo gần đây</span>
                            <a href="{{ route('dashboard.notifications') }}" class="text-[10px] text-[#003DA5] hover:underline font-semibold">Xem tất cả</a>
                        </div>
                        <div class="divide-y divide-gray-50 max-h-64 overflow-y-auto custom-scrollbar">
                            @forelse($recentNotifications ?? [] as $rn)
                                <a href="{{ route('dashboard.notifications.read', $rn->id) }}" class="block px-4 py-3 hover:bg-[#E8F0FE]/20 transition-colors {{ !$rn->is_read ? 'bg-[#E8F0FE]/40' : '' }}">
                                    <div class="flex gap-2.5 items-start">
                                        <div class="w-1.5 h-1.5 rounded-full mt-1.5 {{ !$rn->is_read ? 'bg-[#003DA5]' : 'bg-transparent' }}"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-gray-800 truncate">{{ $rn->title }}</p>
                                            <p class="text-[10px] text-gray-400 mt-0.5 truncate">{{ $rn->message }}</p>
                                            <p class="text-[9px] text-gray-400 mt-1 font-mono">{{ $rn->created_at ? $rn->created_at->diffForHumans() : 'Vừa xong' }}</p>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-xs text-gray-400">Không có thông báo mới</div>
                            @endforelse
                        </div>
                    </div>
                </div>
                <button class="w-9 h-9 rounded-xl flex items-center justify-center hover:bg-gray-100 transition-colors">
                    <i data-lucide="calendar" class="w-[18px] h-[18px] text-gray-700"></i>
=======
                <a href="{{ route('dashboard.notifications') }}" class="mf-icon-btn" aria-label="Thông báo">
                    <i data-lucide="bell" class="w-[18px] h-[18px]"></i>
                    @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                        <span class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-[#E4002B] text-white flex items-center justify-center font-bold rounded-full text-[9px]">{{ $unreadNotificationsCount }}</span>
                    @endif
                </a>
                <button class="mf-icon-btn" aria-label="Lịch làm việc">
                    <i data-lucide="calendar" class="w-[18px] h-[18px]"></i>
>>>>>>> 7cc2df640476108373fb6ec7676acf2bec9b0ebc
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

    <!-- Global Toast Container -->
    <div id="global-toast" class="fixed bottom-5 right-5 bg-[#001F5B] text-white px-5 py-3.5 rounded-2xl text-xs font-semibold shadow-2xl transition-all duration-300 transform translate-y-20 opacity-0 z-50 flex items-center gap-2 border border-blue-900">
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

        // Bell dropdown toggle
        const bellBtn = document.getElementById('bell-dropdown-btn');
        const bellPanel = document.getElementById('bell-dropdown-panel');
        
        if (bellBtn && bellPanel) {
            bellBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                bellPanel.classList.toggle('hidden');
            });

            document.addEventListener('click', (e) => {
                if (!bellPanel.contains(e.target) && !bellBtn.contains(e.target)) {
                    bellPanel.classList.add('hidden');
                }
            });
        }

        // Global Toast alert logic
        window.showGlobalToast = (message, type = 'success') => {
            const toast = document.getElementById('global-toast');
            if (!toast) return;
            
            let icon = '🔔';
            if (type === 'success') icon = '✅';
            if (type === 'error') icon = '❌';
            
            toast.innerHTML = `<span class="text-sm">${icon}</span> <span>${message}</span>`;
            toast.classList.remove('translate-y-20', 'opacity-0');
            
            setTimeout(() => {
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 4000);
        };

        // AJAX Polling for real-time notifications
        function pollNotifications() {
            fetch('{{ route("dashboard.notifications.poll") }}')
                .then(res => res.json())
                .then(data => {
                    // Update bell badge
                    let badge = document.getElementById('bell-badge');
                    if (data.unread_count > 0) {
                        if (badge) {
                            badge.innerText = data.unread_count;
                            badge.classList.remove('hidden');
                        } else {
                            const newBadge = document.createElement('span');
                            newBadge.id = 'bell-badge';
                            newBadge.className = 'absolute top-1 right-1 w-4 h-4 bg-[#E4002B] text-white flex items-center justify-center font-bold rounded-full text-[9px]';
                            newBadge.innerText = data.unread_count;
                            bellBtn.appendChild(newBadge);
                        }
                    } else if (badge) {
                        badge.remove();
                    }

                    // Display toasts & inject into recent notifications dropdown dynamically
                    if (data.new_notifications && data.new_notifications.length > 0) {
                        if (!window.notifiedIds) window.notifiedIds = [];
                        
                        data.new_notifications.forEach(notif => {
                            if (window.notifiedIds.includes(notif.id)) return;
                            window.notifiedIds.push(notif.id);

                            // Trigger real-time visual popup
                            window.showGlobalToast(notif.title + ": " + notif.message);

                            // Prepend item to bell dropdown menu
                            const list = document.querySelector('#bell-dropdown-panel .divide-y');
                            if (list) {
                                const emptyState = list.querySelector('.text-center');
                                if (emptyState) emptyState.remove();

                                const item = document.createElement('a');
                                item.href = `/dashboard/notifications/${notif.id}/read`;
                                item.className = 'block px-4 py-3 hover:bg-[#E8F0FE]/20 transition-colors bg-[#E8F0FE]/40';
                                item.innerHTML = `
                                    <div class="flex gap-2.5 items-start">
                                        <div class="w-1.5 h-1.5 rounded-full mt-1.5 bg-[#003DA5]"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-bold text-gray-800 truncate">${notif.title}</p>
                                            <p class="text-[10px] text-gray-400 mt-0.5 truncate">${notif.message}</p>
                                            <p class="text-[9px] text-gray-400 mt-1 font-mono">Vừa xong</p>
                                        </div>
                                    </div>
                                `;
                                list.insertBefore(item, list.firstChild);
                            }
                        });
                    }
                })
                .catch(err => console.error('Lỗi kiểm tra thông báo:', err));
        }

        // Start polling every 4 seconds if authenticated
        @auth
            setInterval(pollNotifications, 4000);
        @endauth
    @yield('scripts')
</body>
</html>
