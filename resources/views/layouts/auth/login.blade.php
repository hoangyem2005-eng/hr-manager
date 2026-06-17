<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-blue-100">

<div class="min-h-screen flex items-center justify-center">

    <div class="w-full max-w-md bg-white shadow-2xl rounded-2xl p-8">

        <!-- TITLE -->
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-blue-700">ĐĂNG NHẬP</h1>
            <p class="text-gray-500 mt-2">Hệ thống quản lý công việc</p>
        </div>

        <!-- ERROR LOGIN -->
        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- VALIDATION ERROR -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- FORM -->
        <form method="POST" action="/login">
            @csrf

            <!-- EMAIL -->
            <div class="mb-4">
                <label class="block text-blue-700 font-semibold mb-2">Email</label>
                <input type="email"
                       name="email"
                       class="w-full border border-blue-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Nhập email">
            </div>

            <!-- PASSWORD -->
            <div class="mb-4">
                <label class="block text-blue-700 font-semibold mb-2">Mật khẩu</label>
                <input type="password"
                       name="password"
                       class="w-full border border-blue-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       placeholder="Nhập mật khẩu">
            </div>

            <!-- FORGOT PASSWORD -->
            <div class="flex justify-end mb-5">
                <a href="/forgot-password" class="text-sm text-blue-600 hover:underline">
                    Quên mật khẩu?
                </a>
            </div>

            <!-- BUTTON -->
            <button type="submit"
                    class="w-full bg-blue-700 hover:bg-blue-800 text-white font-bold py-3 rounded-lg">
                Đăng nhập
            </button>
        </form>

        <!-- REGISTER -->
        <div class="text-center mt-6">
            <p class="text-gray-600">Chưa có tài khoản?</p>
            <a href="/register" class="text-blue-700 font-semibold hover:underline">
                Đăng ký ngay
            </a>
        </div>

    </div>

</div>

</body>
</html>