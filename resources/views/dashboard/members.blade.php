@extends('layouts.dashboard')

@section('title', 'Quản lý Thành viên - MobiFone WorkHub')
@section('page_title', 'Thành viên')

@section('content')
<div class="space-y-5 relative">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <h1 class="text-2xl font-bold text-[#001F5B]">Quản lý Thành viên</h1>
            <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#E8F0FE] text-[#003DA5]">{{ count($usersList) }} người</span>
        </div>
        <button id="open-member-panel"
                class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-[#003DA5] hover:bg-[#0057C8] hover:shadow-lg transition-all active:scale-95">
            <i data-lucide="plus" class="w-4 h-4"></i> Thêm thành viên
        </button>
    </div>

    <!-- Filters & Messages -->
    <div class="flex items-center gap-3 flex-wrap justify-between">
        <form method="GET" action="{{ route('dashboard.members') }}" class="flex gap-3 flex-wrap items-center">
            <!-- Search -->
            <div class="relative">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                <input name="search" value="{{ $search }}"
                       class="pl-9 pr-4 py-2 text-sm border border-gray-200 rounded-xl outline-none w-64 bg-white focus:border-[#003DA5]"
                       placeholder="Tìm kiếm thành viên..." />
            </div>

            <!-- Dept Selector -->
            <select name="dept" onchange="this.form.submit()" class="px-3 py-2 text-sm border border-gray-200 rounded-xl outline-none bg-white text-gray-700 focus:border-[#003DA5]">
                <option value="">Tất cả phòng ban</option>
                @foreach($departments as $d)
                    <option value="{{ $d->TENPHONG }}" {{ $deptFilter == $d->TENPHONG ? 'selected' : '' }}>{{ $d->TENPHONG }}</option>
                @endforeach
            </select>

            <button type="submit" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-semibold rounded-xl transition-all">Lọc</button>
            @if(!empty($search) || !empty($deptFilter))
                <a href="{{ route('dashboard.members') }}" class="text-xs text-red-600 hover:underline">Xóa lọc</a>
            @endif
        </form>

        @if(session('success'))
            <div class="px-4 py-2 rounded-xl text-sm bg-green-50 border border-green-200 text-green-700 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif
    </div>

    <!-- Members Table -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-[#F1F3F5] text-gray-400 font-semibold text-xs uppercase tracking-wide">
                        <th class="px-4 py-3">Nhân viên</th>
                        <th class="px-4 py-3">Mã NV</th>
                        <th class="px-4 py-3">Phòng ban</th>
                        <th class="px-4 py-3">Chức vụ</th>
                        <th class="px-4 py-3">Vai trò</th>
                        <th class="px-4 py-3">CV đang giao</th>
                        <th class="px-4 py-3">Ngày tham gia</th>
                        <th class="px-4 py-3">Trạng thái</th>
                        <th class="px-4 py-3">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($usersList as $m)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="relative flex-shrink-0">
                                        <div class="w-10 h-10 rounded-full text-white flex items-center justify-center font-bold text-sm" style="background-color: {{ $m['color'] }};">
                                            {{ $m['avatar'] }}
                                        </div>
                                        <div class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-green-500 border-2 border-white"></div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-700">{{ $m['name'] }}</div>
                                        <div class="text-xs text-gray-400">{{ $m['email'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4 text-xs font-mono text-gray-400">{{ $m['id'] }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $m['dept'] }}</td>
                            <td class="px-4 py-4 text-sm text-gray-700">{{ $m['position'] }}</td>
                            <td class="px-4 py-4">
                                @if($m['role'] == 'Admin')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#001F5B] text-white border border-[#001F5B]">{{ $m['role'] }}</span>
                                @elseif($m['role'] == 'Quản lý')
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-[#003DA5] text-white border border-[#003DA5]">{{ $m['role'] }}</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-700 border border-gray-200">{{ $m['role'] }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-[#E8F0FE] text-[#003DA5]">{{ $m['tasks'] }}</span>
                            </td>
                            <td class="px-4 py-4 text-xs text-gray-400">{{ $m['joined'] }}</td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-1.5">
                                    <div class="w-2 h-2 rounded-full bg-green-500"></div>
                                    <span class="text-xs font-semibold text-green-600">Hoạt động</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center gap-1">
                                    <button class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-blue-50 text-[#003DA5]" title="Chỉnh sửa"><i data-lucide="edit" class="w-4 h-4"></i></button>
                                    <button class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-blue-50 text-[#003DA5]" title="Phân quyền"><i data-lucide="shield" class="w-4 h-4"></i></button>
                                    <button class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-red-50 text-red-600" title="Vô hiệu hóa"><i data-lucide="alert-circle" class="w-4 h-4"></i></button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-8 text-gray-400 text-sm">Không tìm thấy thành viên phù hợp.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Pagination UI Mock -->
        <div class="px-5 py-4 border-t border-gray-100 flex items-center justify-between">
            <div class="flex items-center gap-2 text-sm text-gray-400">
                Hiển thị <select class="px-2 py-1 border rounded-lg text-xs"><option>10</option><option>25</option><option>50</option></select> mục
            </div>
            <div class="flex gap-1">
                <button class="w-8 h-8 rounded-lg text-sm font-semibold bg-[#003DA5] text-white">1</button>
                <button class="w-8 h-8 rounded-lg text-sm font-semibold text-gray-400 hover:bg-gray-100">2</button>
                <button class="w-8 h-8 rounded-lg text-sm font-semibold text-gray-400 hover:bg-gray-100">3</button>
            </div>
        </div>
    </div>
</div>

<!-- ==================== POPUP: SLIDE MEMBER PANEL ==================== -->
<div id="member-panel" class="fixed inset-0 z-50 flex justify-end bg-black/30 hidden">
    <div class="w-96 bg-white h-full shadow-2xl flex flex-col justify-between" id="panel-content">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-[#001F5B]">Thêm thành viên mới</h2>
            <button id="close-member-panel"><i data-lucide="x" class="w-5 h-5 text-gray-400"></i></button>
        </div>

        <form action="{{ route('dashboard.members.save') }}" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1 custom-scrollbar">
            @csrf
            <!-- Name -->
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700 font-medium">Họ và tên *</label>
                <input name="name" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]" placeholder="Nhập họ và tên..." />
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700 font-medium font-medium">Email công ty *</label>
                <input type="email" name="email" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]" placeholder="vi_du@mobifone.vn" />
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700 font-medium">Mật khẩu khởi tạo *</label>
                <input type="password" name="password" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]" placeholder="••••••••" />
            </div>

            <!-- Dept -->
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700 font-medium">Phòng ban *</label>
                <select name="department_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none bg-white focus:border-[#003DA5]">
                    <option value="">— Chọn phòng ban —</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->TENPHONG }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Role Selector -->
            <div>
                <label class="block text-sm font-semibold mb-2 text-gray-700 font-medium">Vai trò hệ thống *</label>
                @foreach($roles as $r)
                    @php
                        $desc = $r->name == 'Admin' ? 'Toàn quyền cấu hình hệ thống' : ($r->name == 'Quản lý' ? 'Tạo và giao task, quản lý nhóm' : 'Nhận và cập nhật task được giao');
                    @endphp
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-200 mb-2 cursor-pointer hover:bg-gray-50/50">
                        <input type="radio" name="role_id" value="{{ $r->id }}" class="mt-1 text-[#003DA5] focus:ring-[#003DA5]" required {{ $r->name == 'Nhân viên' ? 'checked' : '' }} />
                        <div>
                            <div class="text-sm font-semibold text-gray-700">{{ $r->name }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $desc }}</div>
                        </div>
                    </label>
                @endforeach
            </div>

            <!-- Onboarding email option -->
            <div class="flex items-center justify-between p-4 rounded-xl bg-[#E8F0FE]">
                <div>
                    <p class="text-sm font-semibold text-[#001F5B]">Gửi email mời tham gia</p>
                    <p class="text-xs text-gray-400 mt-0.5">Email onboarding tự động</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="send_email" value="1" class="sr-only peer" checked>
                    <div class="w-10 h-5 bg-gray-300 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:height-4 after:w-4 after:transition-all peer-checked:bg-[#003DA5]"></div>
                </label>
            </div>

            <!-- Submit -->
            <div class="pt-4">
                <button type="submit" class="w-full py-3 rounded-xl text-sm font-bold text-white bg-[#003DA5] hover:bg-[#0057C8] transition-all">Gửi lời mời thành viên</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const panel = document.getElementById('member-panel');
        const openBtn = document.getElementById('open-member-panel');
        const closeBtn = document.getElementById('close-member-panel');

        const togglePanel = () => panel.classList.toggle('hidden');

        openBtn.addEventListener('click', togglePanel);
        closeBtn.addEventListener('click', togglePanel);

        // Click outside panel to close it
        panel.addEventListener('click', (e) => {
            if (e.target === panel) togglePanel();
        });
    });
</script>
@endsection
