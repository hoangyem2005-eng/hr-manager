@extends('layouts.dashboard')

@section('title', 'Tổng quan Dashboard - MobiFone WorkHub')
@section('page_title', 'Tổng quan')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-[#001F5B]">Dashboard</h1>
            <p class="text-sm mt-0.5 text-gray-400">Tổng quan hệ thống — Tháng 6/2025</p>
        </div>
        <a href="{{ route('dashboard.tasks') }}" class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-[#003DA5] hover:bg-[#0057C8] hover:shadow-lg transition-all active:scale-95">
            <i data-lucide="plus" class="w-4 h-4"></i> Tạo công việc mới
        </a>
    </div>

    <!-- KPI Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Card 1 -->
        <div class="bg-white rounded-2xl p-5 border-l-4 border-[#003DA5] shadow-sm flex items-start justify-between">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider mb-2 text-gray-400">Tổng công việc</p>
                <p class="text-4xl font-bold text-[#001F5B]">{{ $totalTasks }}</p>
                <p class="text-[11px] mt-1.5 text-gray-400">Tháng 6/2025</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-[#003DA5]/10 flex items-center justify-center text-[#003DA5]">
                <i data-lucide="clipboard" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-2xl p-5 border-l-4 border-[#D97706] shadow-sm flex items-start justify-between">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider mb-2 text-gray-400">Đang thực hiện</p>
                <p class="text-4xl font-bold text-[#001F5B]">{{ $doingTasks }}</p>
                <p class="text-[11px] mt-1.5 text-gray-400">Đang tiến hành</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-[#D97706]/10 flex items-center justify-center text-[#D97706]">
                <i data-lucide="play-circle" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-2xl p-5 border-l-4 border-[#16A34A] shadow-sm flex items-start justify-between">
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider mb-2 text-gray-400">Hoàn thành</p>
                <p class="text-4xl font-bold text-[#001F5B]">{{ $doneTasks }}</p>
                <p class="text-[11px] mt-1.5 text-gray-400">Đã xong</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-[#16A34A]/10 flex items-center justify-center text-[#16A34A]">
                <i data-lucide="check-circle" class="w-5 h-5"></i>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white rounded-2xl p-5 border-l-4 border-[#DC2626] shadow-sm relative overflow-hidden flex items-start justify-between">
            <span class="absolute top-3.5 right-3.5 w-2.5 h-2.5 rounded-full bg-[#DC2626] animate-ping opacity-75"></span>
            <div>
                <p class="text-[10px] font-semibold uppercase tracking-wider mb-2 text-gray-400">Quá hạn</p>
                <p class="text-4xl font-bold text-[#001F5B]">{{ $overdueTasks }}</p>
                <p class="text-[11px] mt-1.5 text-gray-400 text-[#DC2626] font-medium">Cần xử lý ngay</p>
            </div>
            <div class="w-11 h-11 rounded-xl bg-[#DC2626]/10 flex items-center justify-center text-[#DC2626]">
                <i data-lucide="alert-circle" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Main Grid Content -->
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
        <!-- Recent Tasks Table -->
        <div class="lg:col-span-3 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col justify-between">
            <div>
                <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-[#001F5B]">Công việc gần đây</h3>
                    <a href="{{ route('dashboard.tasks') }}" class="text-xs font-semibold text-[#003DA5] hover:underline">Xem tất cả →</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead>
                            <tr class="bg-[#F1F3F5] text-gray-400 font-semibold text-xs uppercase tracking-wide">
                                <th class="px-4 py-3">Mã CV</th>
                                <th class="px-4 py-3">Tên công việc</th>
                                <th class="px-4 py-3">Người thực hiện</th>
                                <th class="px-4 py-3">Ưu tiên</th>
                                <th class="px-4 py-3">Deadline</th>
                                <th class="px-4 py-3">Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($mappedTasks as $task)
                                <tr class="hover:bg-[#E8F0FE]/30 transition-colors cursor-pointer" onclick="window.location.href='{{ route('dashboard.tasks') }}'">
                                    <td class="px-4 py-3.5 text-xs font-mono text-gray-400">{{ $task['id'] }}</td>
                                    <td class="px-4 py-3.5 font-medium text-gray-700 truncate max-w-[150px]">{{ $task['name'] }}</td>
                                    <td class="px-4 py-3.5">
                                        <div class="flex items-center gap-2">
                                            <div class="w-7 h-7 rounded-full bg-[#0057C8] text-white flex items-center justify-center font-bold text-[10px]">
                                                {{ $task['avatar'] }}
                                            </div>
                                            <span class="text-xs text-gray-700">{{ $task['assignee'] }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3.5">
                                        @if($task['priority'] == 'Cao')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Cao</span>
                                        @elseif($task['priority'] == 'Trung bình')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">Trung bình</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">Thấp</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3.5 text-xs font-medium {{ $task['status'] == 'Quá hạn' ? 'text-[#DC2626]' : 'text-gray-400' }}">{{ $task['deadline'] }}</td>
                                    <td class="px-4 py-3.5">
                                        @if($task['status'] == 'Hoàn thành')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700">Hoàn thành</span>
                                        @elseif($task['status'] == 'Đang làm')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700">Đang làm</span>
                                        @elseif($task['status'] == 'Đang review')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-700">Đang review</span>
                                        @elseif($task['status'] == 'Quá hạn')
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700">Quá hạn</span>
                                        @else
                                            <span class="px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-500">Chờ xử lý</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-8 text-gray-400">Không có công việc nào gần đây.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right Side Widgets -->
        <div class="lg:col-span-2 flex flex-col gap-4">
            <!-- Donut Chart -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
                <h3 class="font-bold mb-4 text-[#001F5B]">Phân bổ công việc</h3>
                <div class="flex items-center gap-5">
                    <!-- Target element for ApexCharts -->
                    <div class="w-32 h-32 flex-shrink-0 flex items-center justify-center">
                        <div id="chart-pie"></div>
                    </div>
                    <!-- Legend List -->
                    <div class="space-y-2 flex-1">
                        @foreach($statusDistribution as $index => $item)
                            @php
                                $colors = ['#16A34A', '#D97706', '#003DA5', '#6B7280'];
                                $color = $colors[$index] ?? '#6B7280';
                            @endphp
                            <div class="flex items-center justify-between text-xs">
                                <div class="flex items-center gap-2">
                                    <div class="w-2.5 h-2.5 rounded-full flex-shrink-0" style="background-color: {{ $color }};"></div>
                                    <span class="text-gray-600">{{ $item['name'] }}</span>
                                </div>
                                <span class="font-bold text-[#001F5B]">{{ $item['value'] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Active Members List -->
            <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 flex-1">
                <h3 class="font-bold mb-4 text-[#001F5B]">Thành viên hoạt động</h3>
                <div class="space-y-3.5">
                    @forelse($members as $m)
                        <div class="flex items-center gap-3">
                            <div class="relative flex-shrink-0">
                                <div class="w-9 h-9 rounded-full text-white flex items-center justify-center font-bold text-xs" style="background-color: {{ $m['color'] }};">
                                    {{ $m['avatar'] }}
                                </div>
                                <div class="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-green-500 border-2 border-white"></div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-gray-700 truncate">{{ $m['name'] }}</div>
                                <div class="text-xs text-gray-400">{{ $m['dept'] }}</div>
                            </div>
                            <span class="text-xs px-2.5 py-0.5 rounded-full font-semibold bg-[#E8F0FE] text-[#003DA5]">{{ $m['tasks'] }} tasks</span>
                        </div>
                    @empty
                        <div class="text-center py-6 text-gray-400 text-xs">Không có thành viên hoạt động.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var chartData = @json($statusDistribution);
        
        var options = {
            series: chartData.map(d => d.value),
            labels: chartData.map(d => d.name),
            colors: ['#16A34A', '#D97706', '#003DA5', '#6B7280'],
            chart: {
                type: 'donut',
                height: 130,
                sparkline: {
                    enabled: true
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        background: 'transparent'
                    }
                }
            },
            stroke: {
                show: false
            },
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            },
            tooltip: {
                style: {
                    fontSize: '11px',
                    fontFamily: 'Be Vietnam Pro, sans-serif'
                }
            }
        };

        var chart = new ApexCharts(document.querySelector("#chart-pie"), options);
        chart.render();
    });
</script>
@endsection
