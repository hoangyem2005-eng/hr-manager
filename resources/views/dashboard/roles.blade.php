@extends('layouts.dashboard')

@section('title', 'Phân quyền hệ thống - MobiFone WorkHub')
@section('page_title', 'Phân quyền')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-bold text-[#001F5B]">Phân quyền hệ thống</h1>
        <p class="mt-1 text-sm text-slate-500">Hệ thống hiện chỉ dùng 3 chức vụ: Giám đốc, Trưởng phòng và Nhân viên.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        @php
            $roleCards = [
                [
                    'name' => 'Giám đốc',
                    'code' => 'Director',
                    'icon' => 'crown',
                    'color' => '#001F5B',
                    'items' => [
                        ['Toàn quyền hệ thống', true],
                        ['Thêm/xóa thành viên', true],
                        ['Phân quyền người dùng', true],
                        ['Xem báo cáo tổng hợp', true],
                        ['Cấu hình hệ thống', true],
                        ['Xóa vĩnh viễn dữ liệu', true],
                    ],
                ],
                [
                    'name' => 'Trưởng phòng',
                    'code' => 'Manager',
                    'icon' => 'briefcase',
                    'color' => '#003DA5',
                    'items' => [
                        ['Tạo và giao công việc', true],
                        ['Thêm thành viên trong phòng', true],
                        ['Theo dõi tiến độ phòng ban', true],
                        ['Gửi thông báo công việc', true],
                        ['Xem báo cáo phòng ban', true],
                        ['Xóa thành viên hệ thống', false],
                        ['Phân quyền người dùng', false],
                    ],
                ],
                [
                    'name' => 'Nhân viên',
                    'code' => 'Staff',
                    'icon' => 'user',
                    'color' => '#64748B',
                    'items' => [
                        ['Xem công việc được giao', true],
                        ['Cập nhật trạng thái công việc', true],
                        ['Upload tài liệu đính kèm', true],
                        ['Xem thông báo cá nhân', true],
                        ['Tạo công việc cho người khác', false],
                        ['Xem công việc của người khác', false],
                        ['Gửi thông báo toàn hệ thống', false],
                    ],
                ],
            ];
        @endphp

        @foreach($roleCards as $card)
            <div class="bg-white rounded-lg border border-gray-100 overflow-hidden shadow-sm">
                <div class="px-5 py-5 text-white" style="background: {{ $card['color'] }}">
                    <div class="flex items-center gap-3 mb-1">
                        <i data-lucide="{{ $card['icon'] }}" class="w-5 h-5"></i>
                        <span class="font-bold text-lg">{{ $card['name'] }}</span>
                    </div>
                    <p class="text-[10px] opacity-70 uppercase tracking-widest">{{ $card['code'] }}</p>
                </div>
                <div class="p-5 space-y-2.5">
                    @foreach($card['items'] as $item)
                        <div class="flex items-center gap-2.5">
                            <div class="w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0 {{ $item[1] ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                <i data-lucide="{{ $item[1] ? 'check' : 'x' }}" class="w-2.5 h-2.5"></i>
                            </div>
                            <span class="text-sm {{ $item[1] ? 'text-gray-700' : 'text-gray-400' }}">{{ $item[0] }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <div class="bg-white rounded-lg border border-gray-100 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-bold text-[#001F5B]">Ma trận quyền chi tiết</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-[#F1F3F5] text-gray-500 font-semibold text-xs uppercase tracking-wide">
                        <th class="px-5 py-3">Hành động</th>
                        <th class="px-5 py-3 text-center">Giám đốc</th>
                        <th class="px-5 py-3 text-center">Trưởng phòng</th>
                        <th class="px-5 py-3 text-center">Nhân viên</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        $matrix = [
                            ['Tạo công việc', [true, true, false]],
                            ['Xóa công việc', [true, false, false]],
                            ['Giao việc cho người khác', [true, true, false]],
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
    window.toggleSwitch = (el, index) => {
        if (index === 0) return;

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
