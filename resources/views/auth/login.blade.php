<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - MobiFone WorkHub</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Be Vietnam Pro', sans-serif;
        }

        @keyframes floatOrb {
            from {
                transform: translateY(0);
            }
            to {
                transform: translateY(-18px);
            }
        }

        .orb {
            animation: floatOrb 3.5s ease-in-out infinite alternate;
        }
    </style>
</head>
<body class="bg-gray-50 flex h-screen w-full overflow-hidden">

    <!-- LEFT PANEL: Brand Info & Visual Elements -->
    <div class="relative hidden lg:flex flex-col items-center justify-center w-[55%] h-full overflow-hidden bg-gradient-to-br from-[#001F5B] to-[#0057C8]">
        <!-- Radial Dot Matrix -->
        <div class="absolute inset-0 opacity-[0.04]" style="background-image: radial-gradient(circle, white 1.5px, transparent 1.5px); background-size: 30px 30px;"></div>

        <!-- Floating Orbs -->
        <div class="orb absolute rounded-full bg-white/5" style="width: 80px; height: 80px; top: 8%; left: 6%; animation-duration: 3s;"></div>
        <div class="orb absolute rounded-full bg-white/5" style="width: 50px; height: 50px; top: 20%; left: 80%; animation-duration: 4s;"></div>
        <div class="orb absolute rounded-full bg-white/5" style="width: 30px; height: 30px; top: 60%; left: 5%; animation-duration: 3.5s;"></div>
        <div class="orb absolute rounded-full bg-white/5" style="width: 60px; height: 60px; top: 75%; left: 75%; animation-duration: 5s;"></div>
        <div class="orb absolute rounded-full bg-white/5" style="width: 20px; height: 20px; top: 45%; left: 90%; animation-duration: 2.5s;"></div>

        <!-- Main Card Brand -->
        <div class="relative z-10 flex flex-col items-center gap-6 px-16 text-center">
            <div class="flex items-center gap-4 mb-2">
                <div class="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur-sm flex items-center justify-center border border-white/20">
                    <span class="text-white font-black text-2xl">M</span>
                </div>
                <div class="text-left">
                    <div class="text-white text-2xl font-bold tracking-tight">MobiFone</div>
                    <div class="text-blue-300 text-xs font-semibold tracking-[0.2em] uppercase">WorkHub</div>
                </div>
            </div>

            <p class="text-blue-200 text-lg font-light leading-relaxed max-w-xs">
                Hệ thống Quản lý Công việc Nội bộ
            </p>

            <!-- Dashboard Preview Widget -->
            <div class="w-80 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/15 p-5 text-left mt-2 shadow-2xl">
                <p class="text-blue-300 text-[10px] font-semibold mb-3 uppercase tracking-wider">Dashboard Overview</p>
                <div class="grid grid-cols-2 gap-2 mb-4">
                    @php
                        $kpis = [
                            ['Tổng CV', '142', '#003DA5'],
                            ['Hoàn thành', '58', '#16A34A'],
                            ['Đang làm', '42', '#D97706'],
                            ['Quá hạn', '12', '#DC2626']
                        ];
                    @endphp
                    @foreach($kpis as $kpi)
                        <div class="rounded-xl p-3 bg-white/5">
                            <div class="text-xl font-bold text-white">{{ $kpi[1] }}</div>
                            <div class="text-[10px] text-blue-200 mt-0.5">{{ $kpi[0] }}</div>
                            <div class="h-1 rounded-full mt-2 bg-white/10">
                                <div class="h-1 rounded-full" style="width: {{ intval($kpi[1]) / 1.42 }}%; background-color: {{ $kpi[2] }};"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="text-xs text-blue-200 flex items-center justify-between">
                    <span>Tiến độ tháng 6/2025</span>
                    <span class="font-semibold text-white">72%</span>
                </div>
                <div class="h-1.5 rounded-full mt-1.5 bg-white/20">
                    <div class="h-1.5 rounded-full bg-[#16A34A]" style="width: 72%;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL: Login Form -->
    <div class="flex items-center justify-center w-full lg:w-[45%] h-full bg-white px-8 md:px-16">
        <div class="w-full max-w-sm">
            <!-- Header Form -->
            <div class="flex flex-col items-center mb-8">
                <div class="p-3.5 rounded-2xl flex items-center justify-center mb-4 shadow-sm border border-[#003DA5]/20 bg-[#003DA5]/5 text-[#003DA5]">
                    <i data-lucide="users" class="w-8 h-8"></i>
                </div>
                <h1 class="text-2xl md:text-3xl font-bold mb-2 tracking-tight text-[#001F5B]">
                    Quản lý nhân sự
                </h1>
                <p class="text-sm text-gray-400 text-center">
                    Đăng nhập để tiếp tục vào MobiFone WorkHub
                </p>
            </div>

            <!-- Notification Messages -->
            @if(session('error'))
                <div class="p-3 mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-xl flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4 flex-shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            @if(session('success'))
                <div class="p-3 mb-4 text-sm text-green-600 bg-green-50 border border-green-200 rounded-xl flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 flex-shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            @if(count($errors) > 0)
                <div class="p-3 mb-4 text-sm text-red-600 bg-red-50 border border-red-200 rounded-xl space-y-1">
                    @foreach($errors->all() as $err)
                        <div class="flex items-center gap-2">
                            <i data-lucide="x" class="w-3.5 h-3.5 flex-shrink-0"></i>
                            <span>{{ $err }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Form -->
            <form action="{{ url('/login') }}" method="POST" class="space-y-4">
                @csrf
                <!-- Email -->
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">Email công ty</label>
                    <div class="relative">
                        <i data-lucide="mail" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input
                            type="email" name="email" value="{{ old('email') }}"
                            placeholder="ten.nguyen@mobifone.vn" required
                            class="w-full pl-10 pr-4 py-3 border border-gray-200 rounded-xl text-sm outline-none transition-all focus:border-[#003DA5] focus:ring-1 focus:ring-[#003DA5]"
                        />
                    </div>
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-semibold mb-1.5 text-gray-700">Mật khẩu</label>
                    <div class="relative">
                        <i data-lucide="lock" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input
                            type="password" name="password" id="password" placeholder="••••••••" required
                            class="w-full pl-10 pr-10 py-3 border border-gray-200 rounded-xl text-sm outline-none transition-all focus:border-[#003DA5] focus:ring-1 focus:ring-[#003DA5]"
                        />
                        <button type="button" id="toggle-password" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition-colors">
                            <i data-lucide="eye" id="eye-icon" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>

                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 text-sm cursor-pointer text-gray-700">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-[#003DA5] focus:ring-[#003DA5]" /> Ghi nhớ đăng nhập
                    </label>
                    <a href="{{ route('password.request') }}" class="text-sm font-semibold text-[#003DA5] hover:text-[#0057C8] transition-colors">Quên mật khẩu?</a>
                </div>

                <!-- Submit buttons -->
                <button
                    type="submit"
                    class="w-full py-3.5 rounded-xl text-white font-semibold flex items-center justify-center gap-2 bg-gradient-to-r from-[#003DA5] to-[#0057C8] hover:shadow-lg hover:shadow-[#003DA5]/20 active:scale-[0.98] transition-all"
                >
                    Đăng nhập <i data-lucide="arrow-right" class="w-[18px] h-[18px]"></i>
                </button>

                <a
                    href="{{ route('landing') }}"
                    class="w-full py-3 rounded-xl border border-gray-200 text-sm font-medium flex items-center justify-center gap-2 text-gray-700 hover:bg-gray-50 active:scale-[0.98] transition-all mt-2"
                >
                    <i data-lucide="chevron-left" class="w-4 h-4"></i>
                    Quay lại trang chủ
                </a>
            </form>

            <div class="text-center mt-6">
                <span class="text-sm text-gray-400">Chưa có tài khoản? </span>
                <a href="{{ route('register') }}" class="text-sm font-bold text-[#003DA5] hover:underline">Đăng ký ngay</a>
            </div>

            <p class="text-center text-xs text-gray-400 mt-10">
                © 2025 MobiFone. All rights reserved. · v2.4.1
            </p>
        </div>
    </div>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        lucide.createIcons();

        // Toggle show password
        const togglePassBtn = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        togglePassBtn.addEventListener('click', () => {
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.setAttribute('data-lucide', 'eye-off');
            } else {
                passwordInput.type = 'password';
                eyeIcon.setAttribute('data-lucide', 'eye');
            }
            lucide.createIcons();
        });
    </script>
</body>
</html>
