@extends('layouts.dashboard')

@section('title', 'Báo cáo & Thống kê - MobiFone WorkHub')
@section('page_title', 'Báo cáo')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <h1 class="text-2xl font-bold text-[#001F5B]">Báo cáo & Thống kê</h1>
        <div class="flex items-center gap-3">
            <div class="flex rounded-xl border border-gray-200 bg-white overflow-hidden">
                <button class="px-3.5 py-2 text-xs font-semibold bg-[#003DA5] text-white">Tháng này</button>
                <button class="px-3.5 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">Tuần này</button>
            </div>
            <button class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs font-semibold border border-gray-200 bg-white hover:bg-gray-50 text-gray-700 transition-all">
                <i data-lucide="download" class="w-3.5 h-3.5"></i> Xuất Excel
            </button>
        </div>
    </div>

    <!-- Mini KPIs Row -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- KPI 1 -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-[10px] font-semibold uppercase tracking-wider mb-2 text-gray-400">Tổng công việc</p>
            <p class="text-3xl font-bold text-[#001F5B]">{{ $total }}</p>
            <p class="text-xs mt-1.5 text-green-600 font-semibold">↑ +12% tháng trước</p>
        </div>
        <!-- KPI 2 -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-[10px] font-semibold uppercase tracking-wider mb-2 text-gray-400">Tỷ lệ hoàn thành</p>
            <p class="text-3xl font-bold text-[#001F5B]">{{ $doneRate }}%</p>
            <p class="text-xs mt-1.5 text-green-600 font-semibold">↑ +5% tháng trước</p>
        </div>
        <!-- KPI 3 -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-[10px] font-semibold uppercase tracking-wider mb-2 text-gray-400">Thời gian TB hoàn thành</p>
            <p class="text-3xl font-bold text-[#001F5B]">4.2 ngày</p>
            <p class="text-xs mt-1.5 text-green-600 font-semibold">↓ cải thiện 0.8 ngày</p>
        </div>
        <!-- KPI 4 -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <p class="text-[10px] font-semibold uppercase tracking-wider mb-2 text-gray-400">Tỷ lệ quá hạn</p>
            <p class="text-3xl font-bold text-[#001F5B]">{{ $overdueRate }}%</p>
            <p class="text-xs mt-1.5 text-green-600 font-semibold">↓ -2% tháng trước</p>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Left: Line Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-bold mb-4 text-[#001F5B]">Tiến độ công việc theo tuần</h3>
            <div id="chart-line-weekly" class="w-full h-56"></div>
        </div>

        <!-- Right: Bar Priority Chart -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-bold mb-4 text-[#001F5B]">Phân bổ theo ưu tiên</h3>
            <div id="chart-bar-priority" class="w-full h-56"></div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Left: Performance by member stacked bar -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-bold mb-4 text-[#001F5B]">Hiệu suất theo thành viên</h3>
            <div id="chart-bar-member" class="w-full h-56"></div>
        </div>

        <!-- Right: Donut Chart Status -->
        <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100">
            <h3 class="font-bold mb-4 text-[#001F5B]">Trạng thái công việc</h3>
            <div class="flex items-center justify-center h-56">
                <div id="chart-donut-status" class="w-64"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        // 1. Line Chart: Tiến độ công việc theo tuần
        var lineData = @json($lineData);
        var lineOptions = {
            series: [
                { name: 'Được giao', data: lineData.map(d => d.assigned) },
                { name: 'Hoàn thành', data: lineData.map(d => d.completed) }
            ],
            chart: {
                type: 'line',
                height: 220,
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: ['#003DA5', '#16A34A'],
            stroke: { curve: 'smooth', width: 3 },
            xaxis: {
                categories: lineData.map(d => d.week),
                labels: { style: { colors: '#9CA3AF', fontFamily: 'Be Vietnam Pro' } }
            },
            yaxis: {
                labels: { style: { colors: '#9CA3AF', fontFamily: 'Be Vietnam Pro' } }
            },
            grid: { borderColor: '#F3F4F6' },
            legend: { position: 'top', horizontalAlign: 'right', fontFamily: 'Be Vietnam Pro' }
        };
        new ApexCharts(document.querySelector("#chart-line-weekly"), lineOptions).render();

        // 2. Bar Chart: Phân bổ theo ưu tiên (Nằm ngang)
        var priorityData = @json($priorityDistribution);
        var priOptions = {
            series: [{
                name: 'Số lượng',
                data: priorityData.map(d => d.value)
            }],
            chart: {
                type: 'bar',
                height: 220,
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    barHeight: '40%',
                    distributed: true,
                    horizontal: true,
                    borderRadius: 4
                }
            },
            colors: ['#DC2626', '#D97706', '#003DA5'], // Cao, Trung bình, Thấp
            xaxis: {
                categories: priorityData.map(d => d.priority),
                labels: { style: { colors: '#9CA3AF', fontFamily: 'Be Vietnam Pro' } }
            },
            yaxis: {
                labels: { style: { colors: '#9CA3AF', fontFamily: 'Be Vietnam Pro' } }
            },
            grid: { show: false },
            legend: { show: false }
        };
        new ApexCharts(document.querySelector("#chart-bar-priority"), priOptions).render();

        // 3. Stacked Bar Chart: Hiệu suất theo thành viên
        var perfData = @json($perfData);
        var memberOptions = {
            series: [
                { name: 'Hoàn thành %', data: perfData.map(d => d.completion) },
                { name: 'Quá hạn %', data: perfData.map(d => d.overdue) }
            ],
            chart: {
                type: 'bar',
                height: 220,
                stacked: true,
                toolbar: { show: false }
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    barHeight: '35%',
                    borderRadius: 4
                }
            },
            colors: ['#003DA5', '#DC2626'],
            xaxis: {
                categories: perfData.map(d => d.name),
                labels: { style: { colors: '#9CA3AF', fontFamily: 'Be Vietnam Pro' } },
                max: 100
            },
            yaxis: {
                labels: { style: { colors: '#9CA3AF', fontFamily: 'Be Vietnam Pro' } }
            },
            legend: { position: 'top', horizontalAlign: 'right', fontFamily: 'Be Vietnam Pro' }
        };
        new ApexCharts(document.querySelector("#chart-bar-member"), memberOptions).render();

        // 4. Donut Chart: Trạng thái công việc
        var pieData = @json($pieData);
        var donutOptions = {
            series: pieData.map(d => d.value),
            labels: pieData.map(d => d.name),
            colors: pieData.map(d => d.color),
            chart: {
                type: 'donut',
                height: 200
            },
            plotOptions: {
                pie: {
                    donut: { size: '65%' }
                }
            },
            stroke: { show: false },
            dataLabels: { enabled: false },
            legend: { position: 'bottom', fontFamily: 'Be Vietnam Pro' }
        };
        new ApexCharts(document.querySelector("#chart-donut-status"), donutOptions).render();
    });
</script>
@endsection
