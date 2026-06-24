<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - MobiFone WorkHub</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Tailwind CSS (via Vite) -->
    @vite(['resources/css/app.css'])

    <style>
        body {
            font-family: 'Be Vietnam Pro', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
        <div class="grid grid-cols-1 lg:grid-cols-2">

            <!-- LEFT PANEL -->
            <div class="bg-gradient-to-br from-[#001F5B] to-[#003DA5] text-white p-10 flex flex-col justify-center relative overflow-hidden">
                <div class="absolute inset-0 opacity-5" style="background-image: radial-gradient(circle, white 1.5px, transparent 1.5px); background-size: 25px 25px;"></div>
                
                <div class="relative z-10">
                    <h1 class="text-4xl font-black mb-4">MOBIFONE</h1>
                    <h2 class="text-2xl font-bold mb-4">Hệ thống quản lý công việc</h2>
                    <p class="text-blue-100 leading-relaxed mb-10">
                        Đăng ký tài khoản để cộng tác nhóm, quản lý công việc, theo dõi tiến độ và tối ưu hóa hiệu suất làm việc nội bộ.
                    </p>

                    <div class="flex items-center justify-center">
                        <div class="w-48 h-48 rounded-2xl bg-white/10 flex items-center justify-center border border-white/20">
                            <span class="text-white font-black text-7xl">M</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT PANEL -->
            <div class="p-10">
                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-[#001F5B]">Đăng ký tài khoản</h2>
                    <p class="text-gray-400 mt-2">Tạo tài khoản để tham gia MobiFone WorkHub</p>
                </div>

                <!-- Errors -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <div class="flex items-center gap-2">
                                <i data-lucide="x" class="w-4 h-4 flex-shrink-0"></i>
                                <span>{{ $error }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ url('/register') }}" class="space-y-4">
                    @csrf

                    <!-- Họ tên -->
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700">Họ và tên</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Nguyễn Văn An"
                               class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#003DA5] focus:ring-1 focus:ring-[#003DA5]">
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-sm font-semibold mb-1.5 text-gray-700">Email công ty</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="an.nguyen@mobifone.vn"
                               class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#003DA5] focus:ring-1 focus:ring-[#003DA5]">
                    </div>

                    <!-- Password -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Mật khẩu</label>
                            <div class="relative">
                                <input type="password" id="password" name="password" required placeholder="••••••••"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-3 pr-10 text-sm focus:outline-none focus:border-[#003DA5] focus:ring-1 focus:ring-[#003DA5]">
                                <button type="button" onclick="togglePassword('password', 'eye-icon-1')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i data-lucide="eye" id="eye-icon-1" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Xác nhận mật khẩu</label>
                            <div class="relative">
                                <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="••••••••"
                                       class="w-full border border-gray-200 rounded-xl px-4 py-3 pr-10 text-sm focus:outline-none focus:border-[#003DA5] focus:ring-1 focus:ring-[#003DA5]">
                                <button type="button" onclick="togglePassword('password_confirmation', 'eye-icon-2')" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <i data-lucide="eye" id="eye-icon-2" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Role & Dept -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Chức vụ mong muốn</label>
                            <select name="role_id" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#003DA5] focus:ring-1 focus:ring-[#003DA5] bg-white">
                                <option value="">-- Chọn chức vụ --</option>
                                @foreach($roles as $r)
                                    <option value="{{ $r->id }}" {{ old('role_id') == $r->id ? 'selected' : '' }}>{{ $r->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold mb-1.5 text-gray-700">Phòng ban</label>
                            <select name="department_id" required
                                    class="w-full border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-[#003DA5] focus:ring-1 focus:ring-[#003DA5] bg-white">
                                <option value="">-- Chọn phòng ban --</option>
                                @foreach($departments as $d)
                                    <option value="{{ $d->id }}" {{ old('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Submit -->
                    <button type="submit"
                            class="w-full bg-[#003DA5] hover:bg-[#0057C8] hover:shadow-lg text-white font-bold py-3.5 rounded-xl transition duration-300 active:scale-[0.98]">
                        Đăng ký tài khoản
                    </button>
                </form>

                <div class="text-center mt-6">
                    <p class="text-sm text-gray-400">Đã có tài khoản? <a href="{{ route('login') }}" class="text-[#003DA5] font-bold hover:underline">Đăng nhập ngay</a></p>
                </div>
            </div>

        </div>
    </div>

    <!-- Lucide Script -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>
        // Hàm xử lý bật/tắt hiển thị mật khẩu
        function togglePassword(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            // Kiểm tra trạng thái hiện tại
            if (input.type === 'password') {
                input.type = 'text'; // Hiển thị chữ
                icon.setAttribute('data-lucide', 'eye-off'); // Đổi icon thành mắt nhắm
            } else {
                input.type = 'password'; // Ẩn chữ thành dấu chấm
                icon.setAttribute('data-lucide', 'eye'); // Đổi icon thành mắt mở
            }
            
            // Yêu cầu thư viện Lucide vẽ lại icon mới
            lucide.createIcons();
        }
    </script>
</body>
</html>
