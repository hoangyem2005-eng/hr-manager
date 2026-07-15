@extends('layouts.dashboard')

@section('title', 'Phân quyền Hệ thống - MobiFone WorkHub')
@section('page_title', 'Phân quyền')

@section('content')
<div class="space-y-6">
    <h1 class="text-2xl font-bold text-[#001F5B]">Phân quyền Hệ thống</h1>

    <!-- Roles Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Role 1: Admin -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
            <div class="px-5 py-5 text-white bg-[#001F5B]">
                <div class="flex items-center gap-3 mb-1">
                    <i data-lucide="crown" class="w-5.5 h-5.5"></i>
                    <span class="font-bold text-lg">Quản trị viên</span>
                </div>
                <p class="text-[10px] opacity-60 uppercase tracking-widest">Admin</p>
            </div>
            <div class="p-5 space-y-2.5">
                @php
                    $adminPerms = [
                        ['Toàn quyền hệ thống', true],
                        ['Thêm/xóa thành viên', true],
                        ['Phân quyền người dùng', true],
                        ['Xem báo cáo tổng hợp', true],
                        ['Cấu hình hệ thống', true],
                        ['Xóa vĩnh viễn dữ liệu', true]
                    ];
                @endphp
                @foreach($adminPerms as $p)
                    <div class="flex items-center gap-2.5">
                        <div class="w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0 {{ $p[1] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                            <i data-lucide="{{ $p[1] ? 'check' : 'x' }}" class="w-2.5 h-2.5"></i>
                        </div>
                        <span class="text-sm {{ $p[1] ? 'text-gray-700' : 'text-gray-400' }}">{{ $p[0] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Role 2: Manager -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
            <div class="px-5 py-5 text-white bg-[#003DA5]">
                <div class="flex items-center gap-3 mb-1">
                    <i data-lucide="briefcase" class="w-5.5 h-5.5"></i>
                    <span class="font-bold text-lg">Quản lý / Trưởng nhóm</span>
                </div>
                <p class="text-[10px] opacity-60 uppercase tracking-widest">Manager</p>
            </div>
            <div class="p-5 space-y-2.5">
                @php
                    $managerPerms = [
                        ['Tạo & giao công việc', true],
                        ['Thêm thành viên vào task', true],
                        ['Theo dõi tiến độ toàn nhóm', true],
                        ['Gửi thông báo email', true],
                        ['Xem báo cáo nhóm', true],
                        ['Xóa thành viên hệ thống', false],
                        ['Phân quyền người dùng', false]
                    ];
                @endphp
                @foreach($managerPerms as $p)
                    <div class="flex items-center gap-2.5">
                        <div class="w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0 {{ $p[1] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                            <i data-lucide="{{ $p[1] ? 'check' : 'x' }}" class="w-2.5 h-2.5"></i>
                        </div>
                        <span class="text-sm {{ $p[1] ? 'text-gray-700' : 'text-gray-400' }}">{{ $p[0] }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Role 3: Staff -->
        <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
            <div class="px-5 py-5 text-white bg-[#6B7280]">
                <div class="flex items-center gap-3 mb-1">
                    <i data-lucide="user" class="w-5.5 h-5.5"></i>
                    <span class="font-bold text-lg">Nhân viên</span>
                </div>
                <p class="text-[10px] opacity-60 uppercase tracking-widest">Staff</p>
            </div>
            <div class="p-5 space-y-2.5">
                @php
                    $staffPerms = [
                        ['Xem task được giao', true],
                        ['Cập nhật trạng thái task', true],
                        ['Upload tài liệu đính kèm', true],
                        ['Bình luận trong task', true],
                        ['Tạo task mới', false],
                        ['Xem task của người khác', false],
                        ['Gửi thông báo email', false]
                    ];
                @endphp
                @foreach($staffPerms as $p)
                    <div class="flex items-center gap-2.5">
                        <div class="w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0 {{ $p[1] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                            <i data-lucide="{{ $p[1] ? 'check' : 'x' }}" class="w-2.5 h-2.5"></i>
                        </div>
                        <span class="text-sm {{ $p[1] ? 'text-gray-700' : 'text-gray-400' }}">{{ $p[0] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Details Permission Customize Matrix -->
    <div class="bg-white rounded-2xl border border-gray-100 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-bold text-[#001F5B]">Tuỳ chỉnh phân quyền chi tiết</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-[#F1F3F5] text-gray-400 font-semibold text-xs uppercase tracking-wide">
                        <th class="px-5 py-3">Hành động</th>
                        <th class="px-5 py-3 text-center">Admin</th>
                        <th class="px-5 py-3 text-center">Quản lý</th>
                        <th class="px-5 py-3 text-center">Nhân viên</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $matrix = [
                            ['Tạo công việc', [true, true, false]],
                            ['Xóa công việc', [true, false, false]],
                            ['Giao task cho người khác', [true, true, false]],
                            ['Xem báo cáo nhóm', [true, true, false]],
                            ['Quản lý thành viên', [true, false, false]],
                            ['Cấu hình hệ thống', [true, false, false]],
                        ];
                    @endphp
                    @foreach($matrix as $item)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-5 py-4 text-gray-700 font-semibold text-sm">{{ $item[0] }}</td>
                            @foreach($item[1] as $index => $val)
                                <td class="px-5 py-4 text-center">
                                    <button onclick="toggleSwitch(this, {{ $index }})" 
                                            class="w-10 h-5 rounded-full relative transition-colors duration-200 inline-block focus:outline-none {{ $val ? 'bg-[#003DA5]' : 'bg-gray-300' }} {{ $index === 0 ? 'cursor-not-allowed opacity-80' : 'cursor-pointer' }}"
                                            {{ $index === 0 ? 'disabled' : '' }}>
                                        <div class="absolute top-[2px] w-4 h-4 rounded-full bg-white shadow transition-all duration-200"
                                             style="left: {{ $val ? 'calc(100% - 18px)' : '2px' }}"></div>
                                    </button>
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Client-side visual toggle switch interaction
    window.toggleSwitch = (el, index) => {
        if (index === 0) return; // Admin always on
        
        const knob = el.querySelector('div');
        if (el.classList.contains('bg-[#003DA5]')) {
            el.classList.replace('bg-[#003DA5]', 'bg-gray-300');
            knob.style.left = '2px';
        } else {
            el.classList.replace('bg-gray-300', 'bg-[#003DA5]');
            knob.style.left = 'calc(100% - 18px)';
        }
    };
</script>
@endsection
