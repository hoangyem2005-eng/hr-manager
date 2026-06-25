<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MobiFone WorkHub - Hệ Thống Quản Lý Công Việc</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Be Vietnam Pro', sans-serif;
            background-color: #F8F9FA;
        }
    </style>
</head>
<body>

    <!-- Navigation Header -->
    <header class="bg-white border-b border-gray-200 sticky top-0 z-30">
        <div class="max-w-[1180px] mx-auto px-[18px] h-[68px] flex items-center justify-between">
            <div class="flex items-center gap-3 cursor-pointer">
                <div class="w-[42px] h-[42px] rounded-lg bg-gradient-to-br from-[#001F5B] to-[#003DA5] flex items-center justify-center text-white text-xl font-bold">M</div>
                <div>
                    <div class="text-[18px] font-bold text-[#001F5B]">MobiFone</div>
                    <div class="text-[11px] font-semibold text-[#003DA5] tracking-[0.2em]">WORKHUB</div>
                </div>
            </div>

            <nav class="flex items-center gap-3">
                <a href="#features" class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-[#003DA5] transition-colors">Tính năng</a>
                <a href="#benefits" class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-[#003DA5] transition-colors">Lợi ích</a>
                <a href="{{ route('register') }}" class="px-4 py-2 text-sm font-semibold text-gray-700 hover:text-[#003DA5] transition-colors">Đăng ký</a>
                <a href="{{ route('login') }}" class="px-5 py-2 rounded-lg bg-[#003DA5] text-white hover:bg-[#0057C8] border-none font-semibold text-sm cursor-pointer transition-all">Đăng nhập</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="max-w-[1180px] mx-auto px-[18px] py-[60px]">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
            <!-- Left: Text -->
            <div>
                <div class="text-[13px] font-bold text-[#003DA5] tracking-[0.06em] uppercase mb-4">Hệ Thống Quản Lý Công Việc</div>
                <h1 class="text-4xl md:text-5xl font-bold text-[#001F5B] leading-[1.1] mb-5">Quản lý công việc nội bộ hiệu quả</h1>
                <p class="text-[17px] text-gray-700 leading-relaxed mb-8 max-w-[550px]">
                    MobiFone WorkHub cung cấp giải pháp quản lý công việc tích hợp. Giao task, theo dõi tiến độ, báo cáo hiệu suất và quản lý nhân viên tất cả trong một nền tảng.
                </p>
                <div class="flex gap-3">
                    <a href="{{ route('login') }}" class="px-[28px] py-[14px] bg-gradient-to-r from-[#003DA5] to-[#0057C8] text-white hover:shadow-lg rounded-lg font-semibold text-[15px] cursor-pointer flex items-center gap-2 transition-all">
                        Khám phá ngay <i data-lucide="arrow-right" class="w-[18px] h-[18px]"></i>
                    </a>
                    <a href="{{ route('login') }}" class="px-[28px] py-[14px] border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 rounded-lg font-semibold text-[15px] cursor-pointer transition-all">Xem demo</a>
                </div>
            </div>

            <!-- Right: Feature Cards -->
            <div class="grid gap-4">
                @php
                    $quickFeatures = [
                        ['icon' => '📊', 'title' => 'Dashboard Thông Minh', 'desc' => 'Visualize công việc theo thời gian thực'],
                        ['icon' => '⚡', 'title' => 'Kanban Board', 'desc' => 'Quản lý workflow với drag-and-drop'],
                        ['icon' => '👥', 'title' => 'Team Collaboration', 'desc' => 'Cộng tác nhóm và giao tiếp tức thì'],
                        ['icon' => '📈', 'title' => 'Analytics', 'desc' => 'Báo cáo chi tiết về hiệu suất nhóm'],
                    ];
                @endphp

                @foreach($quickFeatures as $item)
                    <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 flex gap-4 hover:-translate-y-0.5 transition-all">
                        <div class="text-[28px]">{{ $item['icon'] }}</div>
                        <div>
                            <h3 class="text-[16px] font-semibold text-[#001F5B] mb-1">{{ $item['title'] }}</h3>
                            <p class="text-[13px] text-gray-400">{{ $item['desc'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="bg-white py-[60px] border-t border-b border-gray-200">
        <div class="max-w-[1180px] mx-auto px-[18px]">
            <h2 class="text-3xl font-bold text-[#001F5B] text-center mb-10">Tính Năng Chính</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $features = [
                        ['icon' => 'check-circle', 'title' => 'Quản Lý Task', 'desc' => 'Tạo, giao, theo dõi công việc với deadline rõ ràng'],
                        ['icon' => 'users', 'title' => 'Quản Lý Team', 'desc' => 'Quản lý thành viên, phân quyền và phân công công việc'],
                        ['icon' => 'bar-chart-3', 'title' => 'Báo Cáo', 'desc' => 'Xem thống kê hiệu suất, hoàn thành công việc theo thời gian'],
                        ['icon' => 'shield', 'title' => 'Bảo Mật', 'desc' => 'Các quyền truy cập chi tiết theo vai trò người dùng'],
                        ['icon' => 'zap', 'title' => 'Thông Báo', 'desc' => 'Email và thông báo hệ thống tức thì cho deadline'],
                        ['icon' => 'briefcase', 'title' => 'Tài Liệu', 'desc' => 'Đính kèm và quản lý tài liệu cho từng task'],
                    ];
                @endphp

                @foreach($features as $item)
                    <div class="p-[28px] bg-white border border-gray-200 rounded-xl text-center hover:shadow-md transition-all">
                        <div class="w-[52px] h-[52px] rounded-xl bg-[#E8F0FE] flex items-center justify-center mx-auto mb-4 text-[#003DA5]">
                            <i data-lucide="{{ $item['icon'] }}" class="w-[28px] h-[28px]"></i>
                        </div>
                        <h3 class="text-[18px] font-semibold text-[#001F5B] mb-2">{{ $item['title'] }}</h3>
                        <p class="text-[14px] text-gray-400 leading-relaxed">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="benefits" class="max-w-[1180px] mx-auto px-[18px] py-[60px]">
        <h2 class="text-3xl font-bold text-[#001F5B] text-center mb-10">Lợi Ích Cho Tổ Chức</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                @php
                    $benefits = [
                        'Tăng năng suất làm việc lên đến 40%',
                        'Giảm thời gian quản lý công việc thủ công',
                        'Cải thiện giao tiếp nội bộ',
                        'Dễ dàng theo dõi KPI nhóm',
                        'Tiết kiệm thời gian report hàng tuần',
                        'Quản lý nhân viên tập trung',
                    ];
                @endphp

                @foreach($benefits as $benefit)
                    <div class="flex gap-3 mb-4 items-start">
                        <i data-lucide="check-circle" class="w-5 h-5 text-[#16A34A] flex-shrink-0 mt-0.5"></i>
                        <span class="text-[15px] text-gray-700">{{ $benefit }}</span>
                    </div>
                @endforeach
            </div>

            <div class="bg-[#E8F0FE] p-10 rounded-2xl relative overflow-hidden">
                <div class="absolute inset-0 opacity-10" style="background-image: radial-gradient(circle, white 1.5px, transparent 1.5px); background-size: 30px 30px;"></div>
                <div class="relative z-10">
                    <div class="text-6xl font-bold text-[#003DA5] mb-2">98%</div>
                    <p class="text-[15px] text-gray-700 mb-4">Độ hài lòng người dùng</p>
                    <p class="text-[13px] text-gray-400">Được sử dụng bởi các tổ chức lớn tại Việt Nam</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-r from-[#001F5B] to-[#0057C8] text-white py-[60px] px-[18px] text-center">
        <div class="max-w-[600px] mx-auto">
            <h2 class="text-3xl font-bold mb-4">Sẵn sàng nâng cao hiệu quả?</h2>
            <p class="text-[17px] leading-relaxed mb-8 opacity-90">Tham gia ngay để trải nghiệm hệ thống quản lý công việc thế hệ mới của MobiFone.</p>
            <a href="{{ route('login') }}" class="px-[40px] py-[14px] bg-white text-[#003DA5] hover:bg-gray-100 rounded-lg font-semibold text-[16px] cursor-pointer inline-flex items-center gap-2 transition-all">
                Đăng nhập WorkHub <i data-lucide="arrow-right" class="w-[18px] h-[18px]"></i>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#001F5B] text-white py-10 px-[18px] text-center">
        <div class="max-w-[1180px] mx-auto">
            <p class="text-sm mb-2">© 2025 MobiFone. Tất cả các quyền được bảo lưu.</p>
            <p class="text-[12px] opacity-60">MobiFone WorkHub v2.4.1</p>
        </div>
    </footer>

    <!-- Lucide Script -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();
    </script>
</body>
</html>
