@extends('layouts.dashboard')

@section('title', 'Báo cáo toàn WorkHub - MobiFone')
@section('page_title', 'Báo cáo')

@section('content')
@php
    $change = $summary['monthly_task_change'] ?? 0;
    $changeText = $change > 0 ? '+' . $change . '%' : $change . '%';
    $changeColor = $change >= 0 ? 'text-[#16A34A]' : 'text-[#E4002B]';
@endphp

<div class="space-y-5">
    <section class="relative overflow-hidden rounded-[8px] border border-[#B9CDF5] bg-[#001F5B] text-white">
        <div class="absolute inset-0 opacity-20" style="background-image: linear-gradient(rgba(255,255,255,.18) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.18) 1px, transparent 1px); background-size: 42px 42px;"></div>
        <div class="absolute -right-16 -top-20 h-64 w-64 rounded-full border-[34px] border-white/10"></div>
        <div class="relative grid gap-6 p-6 lg:grid-cols-[1.35fr_.65fr] lg:p-7">
            <div>
                <div class="mb-3 inline-flex items-center gap-2 rounded-full bg-white/10 px-3 py-1 text-[11px] font-black uppercase tracking-[0.22em] text-[#B9CDF5]">
                    <span class="h-2 w-2 rounded-full bg-[#E4002B]"></span>
                    MobiFone WorkHub Report
                </div>
                <h1 class="max-w-3xl text-3xl font-black leading-tight lg:text-4xl">Báo cáo vận hành toàn WorkHub</h1>
                <p class="mt-3 max-w-2xl text-sm leading-6 text-white/75">
                    Dữ liệu được tổng hợp trực tiếp từ công việc, phòng ban, nhân viên và tài liệu trong hệ thống.
                </p>
            </div>
            <div class="grid grid-cols-2 gap-3 self-end">
                <div class="rounded-[8px] border border-white/15 bg-white/10 p-4">
                    <p class="text-[10px] font-black uppercase tracking-wider text-white/55">Nhân sự hoạt động</p>
                    <strong class="mt-1 block text-3xl font-black">{{ $summary['active_users'] }}</strong>
                </div>
                <div class="rounded-[8px] border border-white/15 bg-white/10 p-4">
                    <p class="text-[10px] font-black uppercase tracking-wider text-white/55">File nhân viên tải lên</p>
                    <strong class="mt-1 block text-3xl font-black">{{ $summary['documents'] }}</strong>
                </div>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-[8px] border border-[#D9E5F7] bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between">
                <span class="rounded-[8px] bg-[#E8F0FE] p-2 text-[#003DA5]"><i data-lucide="briefcase" class="h-5 w-5"></i></span>
                <span class="text-xs font-black {{ $changeColor }}">{{ $changeText }} tháng này</span>
            </div>
            <p class="mt-4 text-[11px] font-black uppercase tracking-wider text-slate-400">Tổng công việc</p>
            <strong class="mt-1 block text-4xl font-black text-[#001F5B]">{{ $summary['total'] }}</strong>
        </div>

        <div class="rounded-[8px] border border-[#D9E5F7] bg-white p-5 shadow-sm">
            <span class="inline-flex rounded-[8px] bg-[#ECFDF3] p-2 text-[#16A34A]"><i data-lucide="check-circle-2" class="h-5 w-5"></i></span>
            <p class="mt-4 text-[11px] font-black uppercase tracking-wider text-slate-400">Tỷ lệ hoàn thành</p>
            <div class="mt-1 flex items-end gap-2">
                <strong class="text-4xl font-black text-[#001F5B]">{{ $summary['done_rate'] }}%</strong>
                <span class="pb-1 text-sm font-bold text-slate-400">{{ $summary['completed'] }} việc</span>
            </div>
        </div>

        <div class="rounded-[8px] border border-[#D9E5F7] bg-white p-5 shadow-sm">
            <span class="inline-flex rounded-[8px] bg-[#F4F8FF] p-2 text-[#003DA5]"><i data-lucide="timer" class="h-5 w-5"></i></span>
            <p class="mt-4 text-[11px] font-black uppercase tracking-wider text-slate-400">Thời gian TB hoàn thành</p>
            <div class="mt-1 flex items-end gap-2">
                <strong class="text-4xl font-black text-[#001F5B]">{{ $summary['avg_completion_days'] }}</strong>
                <span class="pb-1 text-sm font-bold text-slate-400">ngày</span>
            </div>
        </div>

        <div class="rounded-[8px] border border-[#F7B7C3] bg-white p-5 shadow-sm">
            <span class="inline-flex rounded-[8px] bg-[#FFF0F3] p-2 text-[#E4002B]"><i data-lucide="alert-triangle" class="h-5 w-5"></i></span>
            <p class="mt-4 text-[11px] font-black uppercase tracking-wider text-slate-400">Quá hạn</p>
            <div class="mt-1 flex items-end gap-2">
                <strong class="text-4xl font-black text-[#E4002B]">{{ $summary['overdue_rate'] }}%</strong>
                <span class="pb-1 text-sm font-bold text-slate-400">{{ $summary['overdue'] }} việc</span>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-[.85fr_1.15fr]">
        <div class="rounded-[8px] border border-[#D9E5F7] bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-black text-[#001F5B]">Tài liệu nhân viên tải lên</h2>
                    <p class="mt-1 text-sm text-slate-500">Tổng hợp file do nhân viên gửi kèm trong các công việc.</p>
                </div>
                <span class="rounded-full bg-[#E8F0FE] px-3 py-1 text-xs font-black text-[#003DA5]">{{ $summary['documents'] }} file</span>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div class="rounded-[8px] bg-[#F4F8FF] p-3">
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Tổng file</p>
                    <strong class="mt-1 block text-2xl font-black text-[#001F5B]">{{ $summary['documents'] }}</strong>
                </div>
                <div class="rounded-[8px] bg-[#FFF7ED] p-3">
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Chờ duyệt</p>
                    <strong class="mt-1 block text-2xl font-black text-[#D97706]">{{ $summary['documents_waiting'] }}</strong>
                </div>
                <div class="rounded-[8px] bg-[#ECFDF3] p-3">
                    <p class="text-[10px] font-black uppercase tracking-wider text-slate-400">Đã chuyển</p>
                    <strong class="mt-1 block text-2xl font-black text-[#16A34A]">{{ $summary['documents_forwarded'] }}</strong>
                </div>
            </div>

            <div class="mt-4 space-y-3">
                @forelse($documentUploaderData as $uploader)
                    <div class="rounded-[8px] border border-slate-100 bg-[#F8FAFF] p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-black text-[#001F5B]">{{ $uploader['name'] }}</p>
                                <p class="mt-0.5 text-xs font-semibold text-slate-500">{{ $uploader['department'] }} · mới nhất {{ $uploader['latest_at'] }}</p>
                            </div>
                            <strong class="rounded-full bg-white px-2.5 py-1 text-xs font-black text-[#003DA5]">{{ $uploader['total'] }} file</strong>
                        </div>
                        <div class="mt-3 grid grid-cols-2 gap-2 text-xs font-bold">
                            <span class="rounded-[8px] bg-white px-2.5 py-2 text-[#D97706]">Chờ duyệt: {{ $uploader['waiting'] }}</span>
                            <span class="rounded-[8px] bg-white px-2.5 py-2 text-[#16A34A]">Đã chuyển: {{ $uploader['forwarded'] }}</span>
                        </div>
                    </div>
                @empty
                    <div class="rounded-[8px] border border-dashed border-slate-200 p-8 text-center text-sm text-slate-400">
                        Chưa có file nào do nhân viên tải lên.
                    </div>
                @endforelse
            </div>
        </div>

        <div class="rounded-[8px] border border-[#D9E5F7] bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-black text-[#001F5B]">File mới nhất</h2>
                    <p class="mt-1 text-sm text-slate-500">Các tài liệu nhân viên vừa upload gần đây.</p>
                </div>
                <i data-lucide="files" class="h-6 w-6 text-[#003DA5]"></i>
            </div>

            <div class="overflow-hidden rounded-[8px] border border-slate-100">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#F4F8FF] text-[11px] font-black uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Tài liệu</th>
                            <th class="px-4 py-3">Người tải</th>
                            <th class="px-4 py-3">Công việc</th>
                            <th class="px-4 py-3">Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentEmployeeDocuments as $document)
                            <tr class="hover:bg-[#F8FAFF]">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <span class="inline-flex h-9 w-9 shrink-0 items-center justify-center rounded-[8px] bg-[#E8F0FE] text-[#003DA5]">
                                            <i data-lucide="paperclip" class="h-4 w-4"></i>
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate font-black text-[#001F5B]">{{ $document['file_name'] }}</p>
                                            <p class="text-xs font-semibold text-slate-400">{{ $document['file_type'] }} · {{ $document['uploaded_at'] }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-bold text-slate-700">{{ $document['uploader'] }}</p>
                                    <p class="text-xs text-slate-400">{{ $document['department'] }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-mono text-xs font-black text-[#003DA5]">{{ $document['task_code'] }}</p>
                                    <p class="max-w-[220px] truncate text-xs font-semibold text-slate-500">{{ $document['task_name'] }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-black {{ str_contains($document['status'], 'Đã chuyển') ? 'bg-[#ECFDF3] text-[#16A34A]' : 'bg-[#FFF7ED] text-[#D97706]' }}">
                                        {{ $document['status'] }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-sm text-slate-400">Chưa có file nhân viên upload để hiển thị.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-[1.35fr_.65fr]">
        <div class="rounded-[8px] border border-[#D9E5F7] bg-white p-5 shadow-sm">
            <div class="mb-5 flex items-start justify-between gap-4">
                <div>
                    <h2 class="text-lg font-black text-[#001F5B]">Nhịp vận hành 7 ngày</h2>
                    <p class="mt-1 text-sm text-slate-500">So sánh việc được tạo và việc hoàn thành theo ngày.</p>
                </div>
                <button type="button" id="export-report-csv" class="inline-flex items-center gap-2 rounded-[8px] border border-[#B9CDF5] bg-[#F4F8FF] px-3 py-2 text-xs font-black text-[#003DA5] hover:bg-white">
                    <i data-lucide="download" class="h-4 w-4"></i>Xuất CSV
                </button>
            </div>
            <div id="chart-line-weekly" class="h-72 w-full"></div>
        </div>

        <div class="rounded-[8px] border border-[#D9E5F7] bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black text-[#001F5B]">Trạng thái toàn hệ thống</h2>
            <p class="mt-1 text-sm text-slate-500">Tỷ trọng công việc theo trạng thái hiện tại.</p>
            <div id="chart-donut-status" class="mt-4 h-72 w-full"></div>
        </div>
    </section>

    <section class="grid grid-cols-1 gap-4 xl:grid-cols-[.9fr_1.1fr]">
        <div class="rounded-[8px] border border-[#D9E5F7] bg-white p-5 shadow-sm">
            <h2 class="text-lg font-black text-[#001F5B]">Khối lượng theo phòng ban</h2>
            <p class="mt-1 text-sm text-slate-500">Tính theo phòng ban của người nhận việc.</p>
            <div id="chart-department-workload" class="mt-4 h-72 w-full"></div>
        </div>

        <div class="rounded-[8px] border border-[#D9E5F7] bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-black text-[#001F5B]">Hiệu suất thành viên</h2>
                    <p class="mt-1 text-sm text-slate-500">Top nhân sự có công việc trong WorkHub.</p>
                </div>
                <span class="rounded-full bg-[#E8F0FE] px-3 py-1 text-xs font-black text-[#003DA5]">{{ $perfData->count() }} người</span>
            </div>

            <div class="overflow-hidden rounded-[8px] border border-slate-100">
                <table class="w-full text-left text-sm">
                    <thead class="bg-[#F4F8FF] text-[11px] font-black uppercase tracking-wider text-slate-500">
                        <tr>
                            <th class="px-4 py-3">Nhân viên</th>
                            <th class="px-4 py-3">Phòng ban</th>
                            <th class="px-4 py-3 text-center">Tổng</th>
                            <th class="px-4 py-3">Hoàn thành</th>
                            <th class="px-4 py-3 text-center">Quá hạn</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($perfData as $member)
                            <tr class="hover:bg-[#F8FAFF]">
                                <td class="px-4 py-3 font-bold text-[#001F5B]">{{ $member['name'] }}</td>
                                <td class="px-4 py-3 text-slate-500">{{ $member['department'] }}</td>
                                <td class="px-4 py-3 text-center font-black text-[#003DA5]">{{ $member['total'] }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="h-2 flex-1 rounded-full bg-slate-100">
                                            <div class="h-2 rounded-full bg-[#003DA5]" style="width: {{ $member['completion'] }}%"></div>
                                        </div>
                                        <span class="w-10 text-right text-xs font-black text-[#001F5B]">{{ $member['completion'] }}%</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center font-black {{ $member['overdue'] > 0 ? 'text-[#E4002B]' : 'text-slate-400' }}">{{ $member['overdue'] }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-10 text-center text-sm text-slate-400">Chưa có dữ liệu nhân sự để thống kê.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="rounded-[8px] border border-[#D9E5F7] bg-white p-5 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-black text-[#001F5B]">Bảng phòng ban WorkHub</h2>
                <p class="mt-1 text-sm text-slate-500">Tổng hợp tiến độ theo từng đơn vị trong hệ thống.</p>
            </div>
            <span class="rounded-full bg-[#FFF0F3] px-3 py-1 text-xs font-black text-[#E4002B]">{{ $departmentData->count() }} phòng có việc</span>
        </div>

        <div class="grid grid-cols-1 gap-3 lg:grid-cols-3">
            @forelse($departmentData as $department)
                <div class="rounded-[8px] border border-slate-100 bg-[#F8FAFF] p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h3 class="font-black text-[#001F5B]">{{ $department['name'] }}</h3>
                            <p class="mt-1 text-xs font-semibold text-slate-500">{{ $department['total'] }} việc · {{ $department['completed'] }} hoàn thành</p>
                        </div>
                        <strong class="text-2xl font-black text-[#003DA5]">{{ $department['completion_rate'] }}%</strong>
                    </div>
                    <div class="mt-4 h-2 rounded-full bg-white">
                        <div class="h-2 rounded-full bg-gradient-to-r from-[#E4002B] to-[#003DA5]" style="width: {{ $department['completion_rate'] }}%"></div>
                    </div>
                    <div class="mt-3 flex items-center justify-between text-xs font-bold text-slate-500">
                        <span>Quá hạn</span>
                        <span class="{{ $department['overdue'] > 0 ? 'text-[#E4002B]' : 'text-slate-400' }}">{{ $department['overdue'] }}</span>
                    </div>
                </div>
            @empty
                <div class="rounded-[8px] border border-dashed border-slate-200 p-8 text-center text-sm text-slate-400 lg:col-span-3">
                    Chưa có phòng ban nào phát sinh công việc.
                </div>
            @endforelse
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const lineData = @json($lineData);
        const departmentData = @json($departmentData);
        const statusData = @json($pieData);
        const summary = @json($summary);

        const fontFamily = 'Be Vietnam Pro, sans-serif';
        const grid = { borderColor: '#E8EEF8', strokeDashArray: 4 };
        const labelStyle = { colors: '#94A3B8', fontFamily };

        new ApexCharts(document.querySelector("#chart-line-weekly"), {
            series: [
                { name: 'Được tạo', data: lineData.map(item => item.assigned) },
                { name: 'Hoàn thành', data: lineData.map(item => item.completed) }
            ],
            chart: { type: 'area', height: 288, toolbar: { show: false }, zoom: { enabled: false } },
            colors: ['#003DA5', '#E4002B'],
            dataLabels: { enabled: false },
            stroke: { curve: 'smooth', width: 3 },
            fill: {
                type: 'gradient',
                gradient: { shadeIntensity: 0.2, opacityFrom: 0.28, opacityTo: 0.04, stops: [0, 100] }
            },
            xaxis: { categories: lineData.map(item => item.label), labels: { style: labelStyle } },
            yaxis: { labels: { style: labelStyle, formatter: value => Math.round(value) } },
            grid,
            legend: { position: 'top', horizontalAlign: 'right', fontFamily, fontWeight: 700 },
            tooltip: { theme: 'light' }
        }).render();

        new ApexCharts(document.querySelector("#chart-donut-status"), {
            series: statusData.map(item => item.value),
            labels: statusData.map(item => item.name),
            colors: statusData.map(item => item.color),
            chart: { type: 'donut', height: 288 },
            stroke: { width: 0 },
            dataLabels: { enabled: false },
            legend: { position: 'bottom', fontFamily, fontWeight: 700 },
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Tổng việc',
                                color: '#64748B',
                                fontFamily,
                                formatter: () => summary.total
                            }
                        }
                    }
                }
            }
        }).render();

        new ApexCharts(document.querySelector("#chart-department-workload"), {
            series: [
                { name: 'Tổng việc', data: departmentData.map(item => item.total) },
                { name: 'Hoàn thành', data: departmentData.map(item => item.completed) },
                { name: 'Quá hạn', data: departmentData.map(item => item.overdue) }
            ],
            chart: { type: 'bar', height: 288, toolbar: { show: false } },
            colors: ['#003DA5', '#16A34A', '#E4002B'],
            plotOptions: { bar: { horizontal: false, columnWidth: '42%', borderRadius: 4 } },
            xaxis: { categories: departmentData.map(item => item.name), labels: { style: labelStyle, trim: true } },
            yaxis: { labels: { style: labelStyle, formatter: value => Math.round(value) } },
            grid,
            legend: { position: 'top', horizontalAlign: 'right', fontFamily, fontWeight: 700 },
            dataLabels: { enabled: false }
        }).render();

        document.getElementById('export-report-csv')?.addEventListener('click', () => {
            const rows = [
                ['Chi so', 'Gia tri'],
                ['Tong cong viec', summary.total],
                ['Hoan thanh', summary.completed],
                ['Dang lam', summary.in_progress],
                ['Cho xu ly', summary.pending],
                ['Qua han', summary.overdue],
                ['Ty le hoan thanh', summary.done_rate + '%'],
                ['Ty le qua han', summary.overdue_rate + '%'],
                ['Nhan su hoat dong', summary.active_users],
                ['Phong ban', summary.departments],
                ['File nhan vien tai len', summary.documents],
                ['File cho truong phong duyet', summary.documents_waiting],
                ['File da chuyen len giam doc', summary.documents_forwarded],
            ];
            const csv = rows.map(row => row.map(cell => `"${String(cell).replaceAll('"', '""')}"`).join(',')).join('\n');
            const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');
            link.href = url;
            link.download = 'mobifone-workhub-report.csv';
            link.click();
            URL.revokeObjectURL(url);
        });
    });
</script>
@endsection
