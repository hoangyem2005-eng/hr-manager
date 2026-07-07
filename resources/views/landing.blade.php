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
        <div class="max-w-[1180px] mx-auto px-[18px] min-h-[68px] py-3 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex items-center gap-3">
                <div>
                    <div class="mf-logo-lockup" aria-label="MobiFone">
                        <span class="mf-logo-blue">Mobi</span><span class="mf-logo-red">Fone</span>
                    </div>
                    <div class="mt-1 text-[11px] font-semibold text-[#003DA5] tracking-[0.2em]">WORKHUB</div>
                </div>
            </div>

            <nav class="flex w-full flex-wrap items-center gap-2 sm:w-auto sm:justify-end">
                <a href="#features" class="px-3 py-2 text-sm font-semibold text-gray-700 hover:text-[#003DA5] transition-colors">Tính năng</a>
                <a href="#benefits" class="px-3 py-2 text-sm font-semibold text-gray-700 hover:text-[#003DA5] transition-colors">Lợi ích</a>
                <a href="{{ route('register') }}" class="px-3 py-2 text-sm font-semibold text-gray-700 hover:text-[#003DA5] transition-colors">Đăng ký</a>
                <a href="{{ route('login') }}" class="ml-auto px-5 py-2 rounded-[8px] bg-[#003DA5] text-white hover:bg-[#0057C8] border-none font-semibold text-sm cursor-pointer transition-all sm:ml-0">Đăng nhập</a>
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="max-w-[1180px] mx-auto px-[18px] py-[60px]">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-10 items-center">
            <!-- Left: Text -->
            <div>
                <div class="text-[13px] font-bold text-[#003DA5] tracking-[0.06em] uppercase mb-4">Hệ thống quản lý công việc</div>
                <h1 class="text-4xl md:text-5xl font-bold text-[#001F5B] leading-[1.1] mb-5">Quản lý công việc nội bộ hiệu quả</h1>
                <p class="text-[17px] text-gray-700 leading-relaxed mb-8 max-w-[550px]">
                    MobiFone WorkHub hỗ trợ giao việc, theo dõi tiến độ, tổng hợp báo cáo hiệu suất và quản lý nhân sự trên một nền tảng nội bộ.
                </p>
                <div class="flex flex-col gap-3 sm:flex-row">
                    <a href="{{ route('login') }}" class="px-[28px] py-[14px] bg-[#003DA5] text-white hover:bg-[#0057C8] rounded-[8px] font-semibold text-[15px] cursor-pointer flex items-center justify-center gap-2 transition-all">
                        Vào WorkHub <i data-lucide="arrow-right" class="w-[18px] h-[18px]"></i>
                    </a>
                    <a href="#features" class="px-[28px] py-[14px] border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 rounded-[8px] font-semibold text-[15px] cursor-pointer text-center transition-all">Xem tính năng</a>
                </div>
            </div>

            <!-- Right: Feature Cards -->
            <div class="grid gap-4">
                @php
                    $quickFeatures = [
                        ['icon' => 'layout-dashboard', 'title' => 'Dashboard điều hành', 'desc' => 'Theo dõi công việc và tiến độ theo thời gian thực'],
                        ['icon' => 'columns-3', 'title' => 'Bảng Kanban', 'desc' => 'Quản lý luồng xử lý theo từng trạng thái công việc'],
                        ['icon' => 'users', 'title' => 'Phối hợp nhóm', 'desc' => 'Phân công, trao đổi và cập nhật trách nhiệm rõ ràng'],
                        ['icon' => 'bar-chart-3', 'title' => 'Báo cáo hiệu suất', 'desc' => 'Tổng hợp số liệu phục vụ quản lý phòng ban'],
                    ];
                @endphp

                @foreach($quickFeatures as $item)
                    <div class="bg-white p-5 rounded-[8px] shadow-sm border border-gray-100 flex gap-4 hover:-translate-y-0.5 transition-all">
                        <div class="w-10 h-10 rounded-[8px] bg-[#E8F0FE] text-[#003DA5] flex items-center justify-center flex-shrink-0">
                            <i data-lucide="{{ $item['icon'] }}" class="w-5 h-5"></i>
                        </div>
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
            <h2 class="text-3xl font-bold text-[#001F5B] text-center mb-10">Tính năng chính</h2>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @php
                    $features = [
                        ['icon' => 'check-circle', 'title' => 'Quản lý công việc', 'desc' => 'Tạo, giao và theo dõi công việc với hạn xử lý rõ ràng'],
                        ['icon' => 'users', 'title' => 'Quản lý đội nhóm', 'desc' => 'Quản lý thành viên, phân quyền và phân công công việc'],
                        ['icon' => 'bar-chart-3', 'title' => 'Báo cáo', 'desc' => 'Xem thống kê hiệu suất và tỷ lệ hoàn thành theo thời gian'],
                        ['icon' => 'shield', 'title' => 'Bảo mật', 'desc' => 'Phân quyền truy cập chi tiết theo vai trò người dùng'],
                        ['icon' => 'bell', 'title' => 'Thông báo', 'desc' => 'Nhắc việc và thông báo hệ thống cho các hạn xử lý quan trọng'],
                        ['icon' => 'folder-open', 'title' => 'Tài liệu', 'desc' => 'Đính kèm và quản lý tài liệu cho từng công việc'],
                    ];
                @endphp

                @foreach($features as $item)
                    <div class="p-[28px] bg-white border border-gray-200 rounded-[8px] text-center hover:shadow-md transition-all">
                        <div class="w-[52px] h-[52px] rounded-[8px] bg-[#E8F0FE] flex items-center justify-center mx-auto mb-4 text-[#003DA5]">
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
        <h2 class="text-3xl font-bold text-[#001F5B] text-center mb-10">Lợi ích cho tổ chức</h2>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-center">
            <div>
                @php
                    $benefits = [
                        'Tăng năng suất làm việc lên đến 40%',
                        'Giảm thời gian quản lý công việc thủ công',
                        'Cải thiện giao tiếp nội bộ',
                        'Dễ dàng theo dõi KPI nhóm',
                        'Tiết kiệm thời gian báo cáo hàng tuần',
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

            <div class="bg-[#E8F0FE] p-10 rounded-[8px] relative overflow-hidden">
                <div>
                    <div class="text-6xl font-bold text-[#003DA5] mb-2">98%</div>
                    <p class="text-[15px] text-gray-700 mb-4">Độ hài lòng người dùng</p>
                    <p class="text-[13px] text-gray-400">Được sử dụng bởi các tổ chức lớn tại Việt Nam</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-[#001F5B] text-white py-[60px] px-[18px] text-center">
        <div class="max-w-[600px] mx-auto">
            <h2 class="text-3xl font-bold mb-4">Sẵn sàng nâng cao hiệu quả?</h2>
            <p class="text-[17px] leading-relaxed mb-8 opacity-90">Tham gia ngay để trải nghiệm hệ thống quản lý công việc thế hệ mới của MobiFone.</p>
            <a href="{{ route('login') }}" class="px-[40px] py-[14px] bg-white text-[#003DA5] hover:bg-gray-100 rounded-[8px] font-semibold text-[16px] cursor-pointer inline-flex items-center gap-2 transition-all">
                Đăng nhập WorkHub <i data-lucide="arrow-right" class="w-[18px] h-[18px]"></i>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-[#001F5B] text-white py-10 px-[18px] text-center">
        <div class="max-w-[1180px] mx-auto">
            <p class="text-sm mb-2">© 2026 MobiFone. Tất cả các quyền được bảo lưu.</p>
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
