@extends('employee.layouts.app')

@section('title', 'Nhân viên — MobiFone HR')
@section('page_title', '🏢 Tổng quan Nhân viên')

@section('head_extra')
<style>
    /* ======= GRID LAYOUT ======= */
    .kpi-grid   { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .main-grid  { display: grid; grid-template-columns: 3fr 2fr; gap: 20px; margin-bottom: 24px; }

    /* ======= KPI CARD ======= */
    .kpi-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        display: flex; align-items: flex-start; justify-content: space-between;
        box-shadow: 0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.04);
        border: 1px solid #F1F5F9;
        transition: box-shadow .2s, transform .2s;
        position: relative; overflow: hidden;
    }
    .kpi-card:hover { box-shadow: 0 4px 20px rgba(0,0,0,.1); transform: translateY(-2px); }
    .kpi-card::after {
        content: '';
        position: absolute; top: 0; left: 0;
        width: 4px; height: 100%;
        border-radius: 4px 0 0 4px;
    }
    .kpi-card.blue::after   { background: #2563EB; }
    .kpi-card.green::after  { background: #16A34A; }
    .kpi-card.amber::after  { background: #D97706; }
    .kpi-card.red::after    { background: #E63946; }

    .kpi-label  { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: #94A3B8; margin-bottom: 6px; }
    .kpi-value  { font-size: 36px; font-weight: 800; color: #0F172A; line-height: 1; }
    .kpi-sub    { font-size: 11px; color: #94A3B8; margin-top: 6px; }

    .kpi-icon {
        width: 46px; height: 46px; border-radius: 13px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }
    .kpi-icon.blue  { background: rgba(37,99,235,.1);  color: #2563EB; }
    .kpi-icon.green { background: rgba(22,163,74,.1);  color: #16A34A; }
    .kpi-icon.amber { background: rgba(217,119,6,.1);  color: #D97706; }
    .kpi-icon.red   { background: rgba(230,57,70,.1);  color: #E63946; }

    /* ======= CARD BASE ======= */
    .card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #F1F5F9;
        box-shadow: 0 1px 3px rgba(0,0,0,.05);
        overflow: hidden;
        margin-bottom: 20px;
    }
    .card-header {
        padding: 18px 20px;
        border-bottom: 1px solid #F1F5F9;
        display: flex; align-items: center; justify-content: space-between;
    }
    .card-title { font-size: 14px; font-weight: 700; color: #0F172A; }
    .card-body  { padding: 20px; }
    .card-link  { font-size: 12px; font-weight: 600; color: #2563EB; text-decoration: none; }
    .card-link:hover { text-decoration: underline; }

    /* ======= TABLE ======= */
    .data-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .data-table thead th {
        background: #F8FAFC; padding: 11px 16px;
        text-align: left; font-size: 10px; font-weight: 700;
        text-transform: uppercase; letter-spacing: .08em; color: #64748B;
        border-bottom: 1px solid #F1F5F9;
    }
    .data-table tbody tr {
        border-bottom: 1px solid #F8FAFC;
        transition: background .15s;
    }
    .data-table tbody tr:hover { background: #F8FAFF; }
    .data-table tbody td { padding: 12px 16px; color: #374151; vertical-align: middle; }

    /* ======= FILTER TABS ======= */
    .filter-tabs {
        display: flex; gap: 4px;
        background: #F1F5F9; border-radius: 8px; padding: 3px;
    }
    .tab-btn {
        padding: 5px 12px; border-radius: 6px; border: none;
        font-size: 11px; font-weight: 600; cursor: pointer;
        font-family: inherit; color: #64748B; background: none;
        transition: all .15s;
    }
    .tab-btn.active { background: #fff; color: #2563EB; box-shadow: 0 1px 3px rgba(0,0,0,.08); }

    /* ======= STATUS PILLS ======= */
    .status-pill {
        display: inline-block;
        padding: 3px 10px; border-radius: 99px;
        font-size: 11px; font-weight: 600;
        text-align: center;
        white-space: nowrap;
    }
    .status-pill.done    { background: #F0FDF4; color: #16A34A; }
    .status-pill.doing   { background: #FFFBEB; color: #D97706; }
    .status-pill.review  { background: #EFF6FF; color: #2563EB; }
    .status-pill.pending { background: #F9FAFB; color: #6B7280; }
    .status-pill.overdue { background: #FFF1F2; color: #E63946; }

    /* ======= PROGRESS BAR ======= */
    .progress-container {
        display: flex; align-items: center; gap: 8px;
        min-width: 110px;
    }
    .progress-track {
        flex: 1; height: 6px; background: #E2E8F0;
        border-radius: 99px; overflow: hidden;
    }
    .progress-fill { height: 100%; border-radius: 99px; transition: width .4s ease; }
    .progress-text { font-size: 11px; font-weight: 700; color: #475569; min-width: 28px; text-align: right; }

    /* ======= SELECT CONTROL ======= */
    .status-select {
        padding: 4px 8px; border-radius: 8px;
        border: 1.5px solid #E2E8F0;
        font-size: 11px; font-weight: 600;
        font-family: inherit; cursor: pointer;
        outline: none; background: #fff;
        transition: all .15s;
        color: #475569;
    }
    .status-select:focus { border-color: #2563EB; }

    /* ======= ACTION BUTTONS ======= */
    .btn-ghost {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 12px; border-radius: 8px;
        background: #F1F5F9; color: #374151;
        font-size: 12px; font-weight: 600;
        border: none; cursor: pointer; font-family: inherit;
        transition: all .15s; text-decoration: none;
    }
    .btn-ghost:hover { background: #E2E8F0; }

    .btn-primary-sm {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 5px 10px; border-radius: 7px;
        background: linear-gradient(135deg, #2563EB, #1D4ED8);
        color: #fff; font-size: 11px; font-weight: 600;
        border: none; cursor: pointer; font-family: inherit;
        box-shadow: 0 2px 6px rgba(37,99,235,.2);
        transition: all .15s; text-decoration: none;
    }
    .btn-primary-sm:hover { transform: translateY(-1px); box-shadow: 0 4px 10px rgba(37,99,235,.3); }

    /* ======= SIDE PANEL LISTS ======= */
    .todo-list, .notif-list {
        display: flex; flex-direction: column; gap: 0;
    }
    .todo-item, .notif-item {
        padding: 12px 0;
        border-bottom: 1px solid #F1F5F9;
        display: flex; align-items: center; justify-content: space-between;
        gap: 12px;
    }
    .todo-item:last-child, .notif-item:last-child { border-bottom: none; }
    
    .todo-details { flex: 1; min-width: 0; }
    .todo-name { font-size: 13px; font-weight: 600; color: #0F172A; margin-bottom: 3px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .todo-meta { font-size: 11px; color: #94A3B8; display: flex; align-items: center; gap: 6px; }

    .notif-details { flex: 1; min-width: 0; }
    .notif-title { font-size: 13px; font-weight: 500; color: #334155; margin-bottom: 3px; }
    .notif-time { font-size: 10px; color: #94A3B8; }

    .empty-panel-state {
        text-align: center; padding: 24px 12px; color: #94A3B8; font-size: 13px;
    }
    .empty-panel-state svg { display: block; margin: 0 auto 10px; opacity: .4; }

    /* PROFILE TABLE */
    .profile-table { width: 100%; border-collapse: collapse; font-size: 13px; margin-top: 10px; }
    .profile-table td { padding: 8px 0; border-bottom: 1px solid #F1F5F9; }
    .profile-table tr:last-child td { border-bottom: none; }
    .profile-table .label { color: #64748B; }
    .profile-table .value { font-weight: 600; color: #0F172A; text-align: right; }

    @media (max-width: 1200px) {
        .kpi-grid  { grid-template-columns: repeat(2, 1fr); }
        .main-grid { grid-template-columns: 1fr; }
    }
    @media (max-width: 700px) {
        .kpi-grid  { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')

<!-- ===== KPI CARDS ===== -->
<div class="kpi-grid">
    <div class="kpi-card blue">
        <div>
            <div class="kpi-label">Tổng công việc</div>
            <div class="kpi-value" id="kpi-total">{{ $total }}</div>
            <div class="kpi-sub" id="kpi-pending-sub">{{ $pending }} công việc chờ xử lý</div>
        </div>
        <div class="kpi-icon blue">
            <i data-lucide="clipboard" style="width:22px;height:22px"></i>
        </div>
    </div>

    <div class="kpi-card green">
        <div>
            <div class="kpi-label">Hoàn thành</div>
            <div class="kpi-value" id="kpi-rate">{{ $completionRate }}%</div>
            <div class="kpi-sub" id="kpi-done-sub">{{ $done }}/{{ $total }} đã hoàn thành</div>
        </div>
        <div class="kpi-icon green">
            <i data-lucide="check-circle-2" style="width:22px;height:22px"></i>
        </div>
    </div>

    <div class="kpi-card amber">
        <div>
            <div class="kpi-label">Đang thực hiện</div>
            <div class="kpi-value" id="kpi-doing">{{ $doing }}</div>
            <div class="kpi-sub">Công việc đang làm</div>
        </div>
        <div class="kpi-icon amber">
            <i data-lucide="zap" style="width:22px;height:22px"></i>
        </div>
    </div>

    <div class="kpi-card red">
        <div>
            <div class="kpi-label">Quá hạn</div>
            <div class="kpi-value" id="kpi-overdue">{{ $overdue }}</div>
            <div class="kpi-sub" id="kpi-overdue-sub" style="color:{{ $overdue > 0 ? '#E63946' : '#94A3B8' }};font-weight:{{ $overdue > 0 ? '600' : 'normal' }}">
                {{ $overdue > 0 ? 'Cần xử lý ngay' : 'Không có công việc trễ' }}
            </div>
        </div>
        <div class="kpi-icon red">
            <i data-lucide="alert-circle" style="width:22px;height:22px"></i>
        </div>
        <span id="kpi-ping" style="position:absolute;top:12px;right:12px;width:8px;height:8px;border-radius:50%;background:#E63946;animation:ping 1.2s infinite;display:{{ $overdue > 0 ? 'block' : 'none' }}"></span>
    </div>
</div>

<!-- ===== MAIN GRID: Tasks + Notification/Accept Side Panel ===== -->
<div class="main-grid">
    <!-- Left Column: My Tasks -->
    <div class="card" id="cong-viec">
        <div class="card-header">
            <span class="card-title">📋 Danh sách công việc của tôi</span>
            <div class="filter-tabs">
                <button class="tab-btn active" onclick="filterTasks('all', this)">Tất cả</button>
                <button class="tab-btn" onclick="filterTasks('doing', this)">Đang làm</button>
                <button class="tab-btn" onclick="filterTasks('done', this)">Xong</button>
                <button class="tab-btn" onclick="filterTasks('overdue', this)">Quá hạn</button>
            </div>
        </div>
        <div style="overflow-x:auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Tên công việc</th>
                        <th style="width: 140px">Tiến độ</th>
                        <th>Hạn chót</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="task-table-body">
                    @forelse($mappedTasks as $t)
                        @php
                            $sc = match($t['status']) {
                                'Hoàn thành' => 'done', 'Đang làm' => 'doing',
                                'Đang review' => 'review', 'Quá hạn' => 'overdue',
                                default => 'pending'
                            };
                            $progressColor = match($t['status']) {
                                'Hoàn thành' => '#16A34A', 'Quá hạn' => '#E63946',
                                'Đang làm' => '#2563EB', 'Đang review' => '#7C3AED',
                                default => '#94A3B8'
                            };
                        @endphp
                        <tr class="task-row" data-status="{{ $sc }}" id="task-row-{{ $t['id'] }}">
                            <td style="font-size:11px;font-family:monospace;color:#94A3B8">WH-{{ str_pad($t['id'], 3, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div style="font-weight:600;max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" id="task-name-text-{{ $t['id'] }}" class="{{ $t['status'] === 'Hoàn thành' ? 'done-text' : '' }}">
                                    {{ $t['name'] }}
                                </div>
                                <div style="font-size:11px;color:#94A3B8;max-width:240px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">
                                    {{ $t['description'] ?? 'Không có mô tả' }}
                                </div>
                            </td>
                            <td>
                                <div class="progress-container">
                                    <div class="progress-track">
                                        <div class="progress-fill" id="bar-{{ $t['id'] }}" style="width:{{ $t['progress'] }}%;background:{{ $progressColor }}"></div>
                                    </div>
                                    <span class="progress-text" id="pct-{{ $t['id'] }}">{{ $t['progress'] }}%</span>
                                </div>
                            </td>
                            <td>
                                <div style="font-size:12px;{{ ($t['days_left'] !== null && $t['days_left'] <= 2 && $t['status'] !== 'Hoàn thành') ? 'color:#E63946;font-weight:600' : 'color:#64748B' }}" id="task-deadline-{{ $t['id'] }}">
                                    {{ $t['deadline'] }}
                                    @if($t['days_left'] !== null && $t['status'] !== 'Hoàn thành')
                                        <div style="font-size:10px;font-weight:normal">
                                            @if($t['days_left'] < 0)
                                                <span style="color:#E63946">(trễ {{ abs($t['days_left']) }} ngày)</span>
                                            @elseif($t['days_left'] <= 2)
                                                <span style="color:#D97706">(còn {{ $t['days_left'] }} ngày)</span>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <span class="status-pill {{ $sc }}" id="pill-{{ $t['id'] }}">{{ $t['status'] }}</span>
                            </td>
                            <td>
                                <select class="status-select" id="select-{{ $t['id'] }}" onchange="updateTask({{ $t['id'] }}, this.value)">
                                    <option value="Chờ xử lý"   {{ $t['status'] === 'Chờ xử lý'   ? 'selected' : '' }}>Chờ xử lý</option>
                                    <option value="Đang làm"    {{ $t['status'] === 'Đang làm'    ? 'selected' : '' }}>Đang làm</option>
                                    <option value="Đang review" {{ $t['status'] === 'Đang review' ? 'selected' : '' }}>Đang review</option>
                                    <option value="Hoàn thành"  {{ $t['status'] === 'Hoàn thành'  ? 'selected' : '' }}>Hoàn thành</option>
                                </select>
                            </td>
                        </tr>
                    @empty
                        <tr id="task-empty-row"><td colspan="6" style="text-align:center;padding:40px;color:#94A3B8">Chưa có công việc nào được giao</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Column: Notifications & New Task Receipt Panel -->
    <div>
        <!-- Block 1: Nhận việc mới (Tasks with status 'Chờ xử lý') -->
        <div class="card">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:6px">
                    <i data-lucide="zap" style="width:16px;height:16px;color:#D97706"></i>
                    Nhận công việc mới
                </span>
            </div>
            <div class="card-body" style="padding-top: 10px; padding-bottom: 10px;">
                <div class="todo-list" id="todo-receipt-list">
                    @php $todoTasksCount = 0; @endphp
                    @foreach($mappedTasks as $t)
                        @if($t['status'] === 'Chờ xử lý')
                            @php $todoTasksCount++; @endphp
                            <div class="todo-item" id="todo-receipt-item-{{ $t['id'] }}">
                                <div class="todo-details">
                                    <div class="todo-name" title="{{ $t['name'] }}">{{ $t['name'] }}</div>
                                    <div class="todo-meta">
                                        <span style="font-family:monospace;font-size:10px">WH-{{ str_pad($t['id'], 3, '0', STR_PAD_LEFT) }}</span>
                                        <span>·</span>
                                        <span>Hạn: {{ $t['deadline'] }}</span>
                                    </div>
                                </div>
                                <button class="btn-primary-sm" onclick="acceptTask({{ $t['id'] }})">
                                    <i data-lucide="play" style="width:11px;height:11px"></i> Nhận việc
                                </button>
                            </div>
                        @endif
                    @endforeach

                    @if($todoTasksCount === 0)
                        <div class="empty-panel-state" id="todo-receipt-empty">
                            <i data-lucide="check-circle" style="width:32px;height:32px;color:#10B981"></i>
                            Chưa có công việc mới cần nhận
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Block 2: Thông báo mới -->
        <div class="card">
            <div class="card-header">
                <span class="card-title" style="display:flex;align-items:center;gap:6px">
                    <i data-lucide="bell" style="width:16px;height:16px;color:#2563EB"></i>
                    Thông báo mới
                </span>
                <a href="{{ route('dashboard.notifications') }}" class="card-link">Xem tất cả</a>
            </div>
            <div class="card-body" style="padding-top: 10px; padding-bottom: 10px;">
                <div class="notif-list">
                    @forelse($notifications as $n)
                        <div class="notif-item hover:bg-gray-50 transition-colors cursor-pointer" onclick="location.href='{{ route('dashboard.notifications.read', $n->id) }}'">
                            <div class="notif-details">
                                <div class="notif-title">{{ $n->title }}</div>
                                <div class="notif-time">
                                    <i data-lucide="clock" style="width:10px;height:10px;vertical-align:middle;margin-right:2px"></i>
                                    {{ $n->created_at->diffForHumans() }}
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-panel-state">
                            <i data-lucide="message-square" style="width:32px;height:32px;color:#94A3B8"></i>
                            Không có thông báo mới
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Block 3: Phân bổ công việc (Chart + Profile) -->
        <div class="card">
            <div class="card-header">
                <span class="card-title">📊 Phân bổ công việc cá nhân</span>
            </div>
            <div class="card-body">
                <div id="employee-donut-chart" style="margin-bottom: 15px;"></div>

                <div class="profile-card">
                    <table class="profile-table">
                        <tr>
                            <td class="label">Phòng ban</td>
                            <td class="value">{{ Auth::user()->department->TENPHONG ?? 'Chưa xác định' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Email liên hệ</td>
                            <td class="value" style="font-size:12px">{{ Auth::user()->email }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast Live Notification -->
<div id="toast-live" style="position:fixed;bottom:24px;right:24px;background:#0F172A;color:#fff;padding:12px 20px;border-radius:12px;font-size:13px;font-weight:500;box-shadow:0 4px 20px rgba(0,0,0,.2);transform:translateY(80px);opacity:0;transition:all .3s;z-index:9999;"></div>

@endsection

@section('scripts')
<script>
    // Task numbers used to update graphs and KPIs
    let taskStats = {
        total: {{ $total }},
        done: {{ $done }},
        doing: {{ $doing }},
        overdue: {{ $overdue }},
        pending: {{ $pending }}
    };

    // Render Apex Donut Chart
    let chartObj = null;
    function renderDonutChart() {
        const data = [
            { name: 'Hoàn thành', value: taskStats.done, color: '#16A34A' },
            { name: 'Đang thực hiện', value: taskStats.doing, color: '#2563EB' },
            { name: 'Quá hạn', value: taskStats.overdue, color: '#E63946' },
            { name: 'Chờ xử lý', value: taskStats.pending, color: '#94A3B8' }
        ];

        const filteredData = data.filter(d => d.value > 0);
        const series = filteredData.length > 0 ? filteredData.map(d => d.value) : [1];
        const labels = filteredData.length > 0 ? filteredData.map(d => d.name) : ['Không có công việc'];
        const colors = filteredData.length > 0 ? filteredData.map(d => d.color) : ['#E2E8F0'];

        const options = {
            series: series,
            labels: labels,
            colors: colors,
            chart: { type: 'donut', height: 180, sparkline: { enabled: true } },
            plotOptions: {
                pie: {
                    donut: {
                        size: '70%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Tổng số',
                                color: '#0F172A',
                                fontSize: '12px',
                                fontWeight: 700,
                                formatter: function () {
                                    return taskStats.total;
                                }
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            legend: { show: true, position: 'bottom', horizontalAlign: 'center', fontSize: '11px', fontFamily: 'Inter' },
            stroke: { show: false },
            tooltip: { style: { fontFamily: 'Inter, sans-serif', fontSize: '11px' } }
        };

        if (chartObj) {
            chartObj.destroy();
        }
        chartObj = new ApexCharts(document.querySelector('#employee-donut-chart'), options);
        chartObj.render();
    }

    // Run on startup
    document.addEventListener("DOMContentLoaded", function() {
        renderDonutChart();
        lucide.createIcons();
    });

    // Helper to filter tasks based on tabs
    let currentFilter = 'all';
    function filterTasks(status, btn) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        currentFilter = status;
        
        const rows = document.querySelectorAll('.task-row');
        let visibleCount = 0;
        rows.forEach(item => {
            const isVisible = (status === 'all' || item.dataset.status === status);
            item.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });

        // Toggle empty row placeholder if no rows visible
        let emptyRow = document.getElementById('task-table-empty-row');
        if (visibleCount === 0) {
            if (!emptyRow) {
                emptyRow = document.createElement('tr');
                emptyRow.id = 'task-table-empty-row';
                emptyRow.innerHTML = `<td colspan="6" style="text-align:center;padding:30px;color:#94A3B8">Không có công việc nào trong mục này</td>`;
                document.getElementById('task-table-body').appendChild(emptyRow);
            } else {
                emptyRow.style.display = '';
            }
        } else if (emptyRow) {
            emptyRow.style.display = 'none';
        }
    }

    // Accept task click handler on right panel
    function acceptTask(id) {
        // Find select dropdown for this task and set to 'Đang làm'
        const selectEl = document.getElementById('select-' + id);
        if (selectEl) {
            selectEl.value = 'Đang làm';
            updateTask(id, 'Đang làm');
        }
    }

    // Update task status via Fetch API
    function updateTask(id, status) {
        const progressMap = { 'Chờ xử lý': 0, 'Đang làm': 50, 'Đang review': 80, 'Hoàn thành': 100 };
        const progress = progressMap[status] ?? 0;

        fetch(`/employee/tasks/${id}/progress`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status, progress })
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                // Update table row styling, progress and data-status
                const row = document.getElementById('task-row-' + id);
                if (row) {
                    const statusClassMap = {
                        'Chờ xử lý': 'pending',
                        'Đang làm': 'doing',
                        'Đang review': 'review',
                        'Hoàn thành': 'done',
                        'Quá hạn': 'overdue'
                    };
                    const sc = statusClassMap[status] ?? 'pending';
                    row.dataset.status = sc;
                    
                    // Update task text decoration
                    const nameText = document.getElementById('task-name-text-' + id);
                    if (nameText) {
                        if (status === 'Hoàn thành') {
                            nameText.classList.add('done-text');
                            nameText.style.textDecoration = 'line-through';
                            nameText.style.color = '#94A3B8';
                        } else {
                            nameText.classList.remove('done-text');
                            nameText.style.textDecoration = '';
                            nameText.style.color = '';
                        }
                    }

                    // Update progress bar
                    const bar = document.getElementById('bar-' + id);
                    const pct = document.getElementById('pct-' + id);
                    if (bar) {
                        bar.style.width = data.progress + '%';
                        const progressColor = status === 'Hoàn thành' ? '#16A34A' : (status === 'Quá hạn' ? '#E63946' : (status === 'Đang làm' ? '#2563EB' : '#7C3AED'));
                        bar.style.backgroundColor = progressColor;
                    }
                    if (pct) { pct.textContent = data.progress + '%'; }

                    // Update status pill
                    const pill = document.getElementById('pill-' + id);
                    if (pill) {
                        pill.className = 'status-pill ' + sc;
                        pill.textContent = status;
                    }
                }

                // If task status changed from 'Chờ xử lý' to something else, remove from right-side accept list
                if (status !== 'Chờ xử lý') {
                    const receiptItem = document.getElementById('todo-receipt-item-' + id);
                    if (receiptItem) {
                        receiptItem.remove();
                        // Check if no items left
                        const items = document.querySelectorAll('#todo-receipt-list .todo-item');
                        if (items.length === 0) {
                            let emptyReceipt = document.getElementById('todo-receipt-empty');
                            if (!emptyReceipt) {
                                emptyReceipt = document.createElement('div');
                                emptyReceipt.id = 'todo-receipt-empty';
                                emptyReceipt.className = 'empty-panel-state';
                                emptyReceipt.innerHTML = `<i data-lucide="check-circle" style="width:32px;height:32px;color:#10B981"></i> Chưa có công việc mới cần nhận`;
                                document.getElementById('todo-receipt-list').appendChild(emptyReceipt);
                                lucide.createIcons();
                            } else {
                                emptyReceipt.style.display = '';
                            }
                        }
                    }
                } else {
                    // If it changed back to 'Chờ xử lý', we could rebuild the item, but simple page reload or dynamic element insertion is needed.
                    // For safety, if user goes back to pending, reload page or create the receipt item again.
                    // Let's just update the list to avoid complex insertion since employees rarely transition a task BACK to pending.
                }

                // Re-apply tab filtering in case the row should now be hidden
                const currentTabBtn = document.querySelector('.tab-btn.active');
                if (currentTabBtn) {
                    filterTasks(currentFilter, currentTabBtn);
                }

                // Dynamically recalculate taskStats
                recalculateStats();

                // Show toast notification
                showToast('✓ Đã cập nhật công việc: ' + status);
            } else {
                showToast('⚠ Cập nhật thất bại');
            }
        })
        .catch(() => showToast('⚠ Không thể kết nối máy chủ'));
    }

    function recalculateStats() {
        const rows = document.querySelectorAll('.task-row');
        let total = rows.length;
        let done = 0;
        let doing = 0;
        let overdue = 0;
        let pending = 0;

        rows.forEach(row => {
            const status = row.dataset.status;
            if (status === 'done') done++;
            else if (status === 'doing' || status === 'review') doing++;
            else if (status === 'overdue') overdue++;
            else if (status === 'pending') pending++;
        });

        taskStats.total = total;
        taskStats.done = done;
        taskStats.doing = doing;
        taskStats.overdue = overdue;
        taskStats.pending = pending;

        const rate = total > 0 ? Math.round((done / total) * 100) : 0;

        // Update KPI values
        document.getElementById('kpi-total').textContent = total;
        document.getElementById('kpi-pending-sub').textContent = pending + ' công việc chờ xử lý';
        document.getElementById('kpi-rate').textContent = rate + '%';
        document.getElementById('kpi-done-sub').textContent = done + '/' + total + ' đã hoàn thành';
        document.getElementById('kpi-doing').textContent = doing;
        document.getElementById('kpi-overdue').textContent = overdue;
        
        const overdueSub = document.getElementById('kpi-overdue-sub');
        const pingDot = document.getElementById('kpi-ping');
        if (overdue > 0) {
            overdueSub.textContent = 'Cần xử lý ngay';
            overdueSub.style.color = '#E63946';
            overdueSub.style.fontWeight = '600';
            if (pingDot) pingDot.style.display = 'block';
        } else {
            overdueSub.textContent = 'Không có công việc trễ';
            overdueSub.style.color = '#94A3B8';
            overdueSub.style.fontWeight = 'normal';
            if (pingDot) pingDot.style.display = 'none';
        }

        // Re-render chart
        renderDonutChart();
    }

    function showToast(msg) {
        const t = document.getElementById('toast-live');
        t.textContent = msg; 
        t.style.transform = 'translateY(0)';
        t.style.opacity = '1';
        setTimeout(() => {
            t.style.transform = 'translateY(80px)';
            t.style.opacity = '0';
        }, 3000);
    }

    // Ping animation style inject
    const style = document.createElement('style');
    style.textContent = '@keyframes ping { 0%,100%{transform:scale(1);opacity:.75} 50%{transform:scale(1.5);opacity:0} }';
    document.head.appendChild(style);
</script>
@endsection
