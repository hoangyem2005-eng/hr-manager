<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-100">

<div class="min-h-screen flex items-center justify-center p-6">

    <div class="w-full max-w-5xl bg-white rounded-3xl shadow-2xl overflow-hidden">

        <div class="grid md:grid-cols-2">

            <!-- LEFT -->
            <div class="bg-blue-700 text-white p-10 flex flex-col justify-center">

                <h1 class="text-5xl font-bold mb-5">MOBIFONE</h1>

                <h2 class="text-3xl font-bold mb-4">
                    Hệ thống quản lý công việc
                </h2>

                <p class="text-blue-100 leading-7">
                    Quản lý nhân viên, công việc và phân quyền
                    dành cho phòng kinh doanh.
                </p>

                <div class="mt-10">
                    <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png"
                         class="w-52">
                </div>

            </div>

            <!-- RIGHT -->
            <div class="p-10">

                <div class="mb-8">
                    <h2 class="text-3xl font-bold text-blue-700">
                        Đăng ký tài khoản
                    </h2>

                    <p class="text-gray-500 mt-2">
                        Tạo tài khoản để sử dụng hệ thống
                    </p>
                </div>

                <!-- ERROR ALL -->
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-xl">
                        <ul class="list-disc pl-5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- SUCCESS -->
                @if(session('success'))
                    <div class="mb-4 p-3 bg-green-100 text-green-700 border border-green-400 rounded-xl">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST" action="/register">
                    @csrf

                    <!-- NAME -->
                    <div class="mb-4">
                        <label class="block text-blue-700 font-semibold mb-2">Họ tên</label>

                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               placeholder="Nhập họ tên"
                               class="w-full border border-blue-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">

                        @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- EMAIL -->
                    <div class="mb-4">
                        <label class="block text-blue-700 font-semibold mb-2">Email</label>

                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               placeholder="Nhập email"
                               class="w-full border border-blue-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">

                        @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- PASSWORD -->
                    <div class="grid md:grid-cols-2 gap-4 mb-4">

                        <div>
                            <label class="block text-blue-700 font-semibold mb-2">Mật khẩu</label>

                            <input type="password"
                                   name="password"
                                   required
                                   placeholder="Nhập mật khẩu"
                                   class="w-full border border-blue-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">

                            @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-blue-700 font-semibold mb-2">Xác nhận mật khẩu</label>

                            <input type="password"
                                   name="password_confirmation"
                                   required
                                   placeholder="Nhập lại mật khẩu"
                                   class="w-full border border-blue-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                    </div>

                    <!-- ROLE -->
                    <div class="mb-6">

                        <label class="block text-blue-700 font-semibold mb-2">Chức vụ</label>

                        <select name="role_id"
                                required
                                class="w-full border border-blue-300 rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500">

                            <option value="">-- Chọn chức vụ --</option>
                            <option value="3" {{ old('role_id') == 3 ? 'selected' : '' }}>Tổ trưởng</option>
                            <option value="4" {{ old('role_id') == 4 ? 'selected' : '' }}>Nhân viên</option>

                        </select>

                        @error('role_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror


                        <label class="block text-blue-700 font-semibold mb-2 mt-4">
                            Phòng ban
                        </label>

                        <input type="text"
                               value="Phòng Kinh doanh"
                               readonly
                               class="w-full border border-blue-300 rounded-xl px-4 py-3 bg-gray-100">

                        <input type="hidden" name="department_id" value="1">

                    </div>

                    <!-- BUTTON -->
                    <button type="submit"
                            class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-xl transition duration-300">
                        Đăng ký tài khoản
                    </button>

                </form>

                <!-- LOGIN -->
                <div class="text-center mt-6">
                    <p class="text-gray-600">Đã có tài khoản?</p>

                    <a href="/login" class="text-blue-700 font-bold hover:underline">
                        Đăng nhập ngay
                    </a>
                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>