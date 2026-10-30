@extends('admin.layouts.app')

@section('title', 'Hồ sơ nhân viên - Lãnh đạo')
@section('page_title', 'Hồ sơ nhân viên')

@section('head_extra')
<style>
    /* ========== Page Layout ========== */
    .emp-page { display:grid; gap:20px; }

    /* ========== Hero ========== */
    .emp-hero { position:relative; overflow:hidden; display:grid; grid-template-columns:1fr auto; gap:22px; align-items:end; padding:28px; border:1px solid #B9CDF5; border-radius:8px; background:linear-gradient(135deg,#001F5B 0%,#003DA5 60%,#1D4ED8 100%); color:#fff; }
    .emp-hero:before { content:""; position:absolute; inset:0; opacity:.12; background-image:linear-gradient(rgba(255,255,255,.22) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.22) 1px,transparent 1px); background-size:40px 40px; }
    .emp-hero:after { content:""; position:absolute; right:-56px; bottom:-70px; width:240px; height:240px; border:36px solid rgba(255,255,255,.08); border-radius:999px; }
    .emp-hero > * { position:relative; z-index:1; }
    .emp-kicker { display:inline-flex; align-items:center; gap:7px; border-radius:999px; padding:6px 12px; background:rgba(255,255,255,.1); color:#BFDBFE; font-size:11px; font-weight:900; letter-spacing:.16em; text-transform:uppercase; }
    .emp-hero h2 { margin:12px 0 10px; font-size:34px; line-height:1.1; font-weight:900; letter-spacing:-.03em; }
    .emp-hero p { color:rgba(255,255,255,.72); font-size:14px; line-height:1.65; }
    .hero-stats { display:grid; grid-template-columns:repeat(2,140px); gap:12px; }
    .hero-stat { border:1px solid rgba(255,255,255,.18); border-radius:8px; padding:15px; background:rgba(255,255,255,.1); backdrop-filter:blur(10px); }
    .hero-stat span { display:block; color:rgba(255,255,255,.6); font-size:10px; font-weight:900; letter-spacing:.1em; text-transform:uppercase; }
    .hero-stat strong { display:block; margin-top:8px; font-size:28px; font-weight:900; line-height:1; }

    /* ========== Summary Cards ========== */
    .emp-summary { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; }
    .sum-card { border:1px solid #D4E0F7; border-radius:8px; background:#fff; padding:18px; box-shadow:0 10px 26px rgba(0,31,91,.05); }
    .sum-card-top { display:flex; justify-content:space-between; align-items:center; }
    .sum-icon { width:42px; height:42px; display:grid; place-items:center; border-radius:8px; }
    .sum-icon.blue { background:#EEF4FF; color:#2563EB; }
    .sum-icon.green { background:#F0FDF4; color:#16A34A; }
    .sum-icon.red { background:#FEF2F2; color:#DC2626; }
    .sum-icon.orange { background:#FFF7ED; color:#EA580C; }
    .sum-card label { display:block; margin-top:14px; color:#94A3B8; font-size:11px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; }
    .sum-card strong { display:block; margin-top:5px; color:#001F5B; font-size:32px; font-weight:900; line-height:1; }

    /* ========== Toolbar ========== */
    .emp-toolbar { display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; border:1px solid #D4E0F7; border-radius:8px; background:#fff; padding:14px 16px; }
    .toolbar-filters { display:flex; align-items:center; gap:10px; flex-wrap:wrap; flex:1; }
    .search-wrap { position:relative; flex:1; min-width:220px; max-width:360px; }
    .search-wrap i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#8AA0BD; width:16px; height:16px; }
    .emp-input { height:40px; border:1px solid #D4E0F7; border-radius:8px; background:#F8FAFF; color:#001F5B; font-size:13px; outline:none; padding:0 12px 0 38px; width:100%; }
    .emp-select { height:40px; border:1px solid #D4E0F7; border-radius:8px; background:#F8FAFF; color:#001F5B; font-size:13px; outline:none; padding:0 12px; cursor:pointer; }
    .emp-input:focus, .emp-select:focus { border-color:#003DA5; background:#fff; box-shadow:0 0 0 3px rgba(0,61,165,.09); }
    .toolbar-btn { height:40px; border:0; border-radius:8px; padding:0 16px; background:#003DA5; color:#fff; font-size:13px; font-weight:900; cursor:pointer; display:inline-flex; align-items:center; gap:7px; font-family:inherit; }
    .clear-link { color:#E4002B; font-size:13px; font-weight:900; text-decoration:none; white-space:nowrap; }

    /* ========== Table ========== */
    .emp-board { overflow:hidden; border:1px solid #D4E0F7; border-radius:8px; background:#fff; box-shadow:0 14px 32px rgba(0,31,91,.06); }
    .board-head { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:16px 18px; border-bottom:1px solid #E5EDF8; }
    .board-title { display:flex; align-items:center; gap:10px; color:#001F5B; font-size:18px; font-weight:900; }
    .board-count { height:24px; padding:0 10px; border-radius:999px; background:#003DA5; color:#fff; font-size:12px; font-weight:900; display:inline-flex; align-items:center; }
    .emp-table-wrap { overflow-x:auto; }
    .emp-table { width:100%; border-collapse:collapse; font-size:13px; }
    .emp-table th { background:#F4F8FF; color:#64748B; text-transform:uppercase; letter-spacing:.08em; font-size:10px; text-align:left; padding:12px 16px; border-bottom:1px solid #E5EDF8; white-space:nowrap; }
    .emp-table th.sortable { cursor:pointer; user-select:none; }
    .emp-table th.sortable:hover { color:#003DA5; }
    .emp-table td { padding:14px 16px; border-bottom:1px solid #F1F5FB; vertical-align:middle; }
    .emp-table tr:last-child td { border-bottom:none; }
    .emp-table tr:hover td { background:#FAFCFF; }

    /* Employee info cell */
    .emp-info { display:flex; align-items:center; gap:12px; }
    .emp-avatar { width:40px; height:40px; flex:0 0 40px; display:grid; place-items:center; border-radius:8px; background:#003DA5; color:#fff; font-size:12px; font-weight:900; box-shadow:inset 3px 0 0 #E4002B; }
    .emp-name { font-weight:900; color:#001F5B; font-size:13px; white-space:nowrap; }
    .emp-email { margin-top:3px; color:#94A3B8; font-size:11px; white-space:nowrap; }
    .emp-code { color:#CBD5E1; font-size:10px; font-family:ui-monospace,SFMono-Regular,Menlo,monospace; margin-top:2px; }

    /* Pills */
    .role-pill { display:inline-flex; align-items:center; height:24px; padding:0 9px; border-radius:999px; font-size:11px; font-weight:900; white-space:nowrap; }
    .role-pill.director { background:#001F5B; color:#fff; }
    .role-pill.manager { background:#1D4ED8; color:#fff; }
    .role-pill.leader { background:#7C3AED; color:#fff; }
    .role-pill.employee { background:#F1F5F9; color:#475569; border:1px solid #E2E8F0; }
    .status-dot { display:inline-flex; align-items:center; gap:6px; font-size:12px; font-weight:800; }
    .status-dot:before { content:""; width:8px; height:8px; border-radius:999px; flex:0 0 8px; }
    .status-dot.active { color:#15803D; }
    .status-dot.active:before { background:#22C55E; }
    .status-dot.inactive { color:#64748B; }
    .status-dot.inactive:before { background:#CBD5E1; }

    /* Task mini-bar */
    .task-bar-wrap { min-width:130px; }
    .task-bar-nums { display:flex; justify-content:space-between; margin-bottom:5px; font-size:11px; font-weight:900; color:#001F5B; }
    .task-bar-track { height:6px; background:#EEF2F7; border-radius:999px; overflow:hidden; }
    .task-bar-fill { height:100%; border-radius:inherit; background:linear-gradient(90deg,#2563EB,#16A34A); }
    .task-chips { display:flex; gap:5px; margin-top:6px; flex-wrap:wrap; }
    .t-chip { display:inline-flex; align-items:center; gap:3px; height:20px; padding:0 7px; border-radius:999px; font-size:10px; font-weight:900; }
    .t-chip.done { background:#F0FDF4; color:#15803D; }
    .t-chip.doing { background:#EFF6FF; color:#1D4ED8; }
    .t-chip.over { background:#FEF2F2; color:#DC2626; }

    /* Action btn */
    .view-btn { display:inline-flex; align-items:center; gap:6px; height:32px; padding:0 12px; border:1px solid #D4E0F7; border-radius:8px; background:#fff; color:#003DA5; font-size:12px; font-weight:900; text-decoration:none; white-space:nowrap; cursor:pointer; font-family:inherit; transition:background .14s,border-color .14s; }
    .view-btn:hover { background:#EEF4FF; border-color:#003DA5; }

    /* Detail modal */
    .emp-modal-backdrop { position:fixed; inset:0; z-index:200; display:none; align-items:center; justify-content:center; padding:16px; background:rgba(0,20,60,.48); }
    .emp-modal-backdrop.open { display:flex; }
    .emp-modal { width:min(700px,100%); max-height:calc(100vh - 32px); display:flex; flex-direction:column; overflow:hidden; border-radius:10px; background:#fff; box-shadow:0 30px 80px rgba(0,31,91,.28); }
    .modal-hd { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:20px 22px; border-bottom:1px solid #E5EDF8; flex-shrink:0; }
    .modal-hd-title { font-size:18px; font-weight:900; color:#001F5B; }
    .modal-close { width:34px; height:34px; display:grid; place-items:center; border:1px solid #D4E0F7; border-radius:8px; background:#fff; cursor:pointer; color:#64748B; }
    .modal-close:hover { background:#F1F5FB; color:#001F5B; }
    .modal-bd { flex:1; overflow-y:auto; padding:22px; display:grid; gap:18px; }
    .detail-profile { display:flex; align-items:center; gap:16px; padding:18px; border:1px solid #D4E0F7; border-radius:8px; background:linear-gradient(135deg,#F8FAFF,#F0F6FF); }
    .detail-avatar { width:64px; height:64px; flex:0 0 64px; display:grid; place-items:center; border-radius:10px; background:#003DA5; color:#fff; font-size:20px; font-weight:900; box-shadow:inset 4px 0 0 #E4002B; }
    .detail-name { font-size:20px; font-weight:900; color:#001F5B; }
    .detail-sub { margin-top:4px; color:#64748B; font-size:13px; }
    .detail-badges { display:flex; gap:8px; margin-top:10px; flex-wrap:wrap; }
    .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .info-field { border:1px solid #E5EDF8; border-radius:8px; padding:13px; background:#FAFCFF; }
    .info-field label { display:block; color:#94A3B8; font-size:10px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; }
    .info-field span { display:block; margin-top:5px; color:#001F5B; font-size:13px; font-weight:800; }
    .stat-row { display:grid; grid-template-columns:repeat(4,1fr); gap:10px; }
    .stat-box { border:1px solid #E5EDF8; border-radius:8px; padding:13px; text-align:center; }
    .stat-box .num { font-size:24px; font-weight:900; color:#001F5B; }
    .stat-box .lbl { margin-top:4px; font-size:10px; font-weight:900; color:#94A3B8; text-transform:uppercase; letter-spacing:.06em; }
    .stat-box.green .num { color:#16A34A; }
    .stat-box.red .num { color:#DC2626; }
    .stat-box.blue .num { color:#2563EB; }

    /* Footer */
    .board-footer { display:flex; align-items:center; justify-content:space-between; padding:14px 18px; border-top:1px solid #E5EDF8; background:#FAFCFF; color:#64748B; font-size:13px; }

    /* Empty */
    .emp-empty { padding:52px 20px; text-align:center; color:#94A3B8; font-weight:800; }
    .emp-empty i { display:block; margin:0 auto 14px; color:#CBD5E1; }

    /* Responsive */
    @media (max-width:1100px) { .emp-summary { grid-template-columns:repeat(2,1fr); } .hero-stats { grid-template-columns:1fr 1fr; } }
    @media (max-width:900px) { .emp-hero { grid-template-columns:1fr; } .info-grid { grid-template-columns:1fr; } .stat-row { grid-template-columns:repeat(2,1fr); } }
    @media (max-width:640px) { .emp-summary { grid-template-columns:1fr 1fr; } }
</style>
@endsection

@section('content')
<div class="emp-page">

    {{-- Hero --}}
    <section class="emp-hero">
        <div>
            <div class="emp-kicker"><i data-lucide="folder-open" style="width:14px;height:14px"></i>Lưu trữ hồ sơ nhân viên</div>
            <h2>Hồ sơ nhân sự toàn công ty</h2>
            <p>Tổng hợp đầy đủ thông tin hồ sơ, hiệu suất công việc và tài liệu của tất cả nhân viên trong hệ thống.</p>
        </div>
        <div class="hero-stats">
            <div class="hero-stat">
                <span>Tổng nhân viên</span>
                <strong>{{ $totalEmployees }}</strong>
            </div>
            <div class="hero-stat">
                <span>Đang hoạt động</span>
                <strong>{{ $activeEmployees }}</strong>
            </div>
            <div class="hero-stat">
                <span>Tổng công việc</span>
                <strong>{{ $totalTasksSum }}</strong>
            </div>
            <div class="hero-stat">
                <span>Hiệu suất TB</span>
                <strong>{{ $avgDoneRate }}%</strong>
            </div>
        </div>
    </section>

    {{-- Summary Cards --}}
    <section class="emp-summary">
        <div class="sum-card">
            <div class="sum-card-top">
                <div class="sum-icon blue"><i data-lucide="users" style="width:20px;height:20px"></i></div>
            </div>
            <label>Tổng nhân viên</label>
            <strong>{{ $totalEmployees }}</strong>
        </div>
        <div class="sum-card">
            <div class="sum-card-top">
                <div class="sum-icon green"><i data-lucide="user-check" style="width:20px;height:20px"></i></div>
            </div>
            <label>Đang hoạt động</label>
            <strong>{{ $activeEmployees }}</strong>
        </div>
        <div class="sum-card">
            <div class="sum-card-top">
                <div class="sum-icon orange"><i data-lucide="briefcase" style="width:20px;height:20px"></i></div>
            </div>
            <label>Tổng công việc</label>
            <strong>{{ $totalTasksSum }}</strong>
        </div>
        <div class="sum-card">
            <div class="sum-card-top">
                <div class="sum-icon red"><i data-lucide="gauge" style="width:20px;height:20px"></i></div>
            </div>
            <label>Hiệu suất trung bình</label>
            <strong>{{ $avgDoneRate }}%</strong>
        </div>
    </section>

    {{-- Toolbar / Filters --}}
    <form method="GET" action="{{ route('admin.employees') }}" class="emp-toolbar">
        <div class="toolbar-filters">
            <div class="search-wrap">
                <i data-lucide="search"></i>
                <input class="emp-input" type="text" name="search" value="{{ $search }}" placeholder="Tìm tên, email hoặc mã NV (VD: NV001, 1)...">
            </div>
            <select class="emp-select" name="dept" onchange="this.form.submit()">
                <option value="">Tất cả phòng ban</option>
                @foreach($departments as $dept)
                    <option value="{{ $dept->id }}" {{ ($deptId == $dept->id || $deptId === $dept->TENPHONG || $deptId === $dept->name) ? 'selected' : '' }}>
                        {{ $dept->TENPHONG ?? $dept->name }}
                    </option>
                @endforeach
            </select>
            <select class="emp-select" name="role" onchange="this.form.submit()">
                <option value="">Tất cả vai trò</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ $roleId == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            <select class="emp-select" name="status" onchange="this.form.submit()">
                <option value="">Tất cả trạng thái</option>
                <option value="active" {{ $statusF === 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                <option value="inactive" {{ $statusF === 'inactive' ? 'selected' : '' }}>Ngừng hoạt động</option>
            </select>
            <select class="emp-select" name="sort" onchange="this.form.submit()">
                <option value="name" {{ $sortBy === 'name' ? 'selected' : '' }}>Sắp xếp: Tên A-Z</option>
                <option value="tasks" {{ $sortBy === 'tasks' ? 'selected' : '' }}>Sắp xếp: Nhiều việc nhất</option>
                <option value="joined" {{ $sortBy === 'joined' ? 'selected' : '' }}>Sắp xếp: Mới nhất</option>
                <option value="dept" {{ $sortBy === 'dept' ? 'selected' : '' }}>Sắp xếp: Phòng ban</option>
            </select>
            <button type="submit" class="toolbar-btn">
                <i data-lucide="filter" style="width:14px;height:14px"></i>Lọc
            </button>
            @if($search || $deptId || $roleId || $statusF || ($sortBy && $sortBy !== 'name'))
                <a href="{{ route('admin.employees') }}" class="clear-link">✕ Xóa lọc</a>
            @endif
        </div>
    </form>

    {{-- Table --}}
    <div class="emp-board">
        <div class="board-head">
            <div class="board-title">
                <i data-lucide="database" style="width:20px;height:20px"></i>
                Danh sách hồ sơ
                <span class="board-count">{{ $employees->count() }} hồ sơ</span>
            </div>
        </div>
        <div class="emp-table-wrap">
            <table class="emp-table">
                <thead>
                    <tr>
                        <th>Mã NV</th>
                        <th>Thông tin nhân viên</th>
                        <th>Phòng ban</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Công việc / Hiệu suất</th>
                        <th>Tài liệu</th>
                        <th>Ngày vào</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                        <tr>
                            <td>
                                <span style="font-family:ui-monospace,monospace;font-size:11px;color:#94A3B8;font-weight:900">{{ $emp['code'] }}</span>
                            </td>
                            <td>
                                <div class="emp-info">
                                    <div class="emp-avatar">{{ $emp['avatar'] }}</div>
                                    <div>
                                        <div class="emp-name">{{ $emp['name'] }}</div>
                                        <div class="emp-email">{{ $emp['email'] }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="color:#334155;font-weight:800;font-size:12px">{{ $emp['dept'] }}</span>
                            </td>
                            <td>
                                @php
                                    $rClass = match((int)$emp['role_id']) {
                                        1 => 'director', 2 => 'manager',
                                        4,5,6,7 => 'leader', default => 'employee'
                                    };
                                @endphp
                                <span class="role-pill {{ $rClass }}">{{ $emp['role_name'] }}</span>
                            </td>
                            <td>
                                <span class="status-dot {{ $emp['is_active'] ? 'active' : 'inactive' }}">
                                    {{ $emp['is_active'] ? 'Hoạt động' : 'Ngừng' }}
                                </span>
                            </td>
                            <td>
                                <div class="task-bar-wrap">
                                    <div class="task-bar-nums">
                                        <span>{{ $emp['done_rate'] }}%</span>
                                        <span style="color:#94A3B8">{{ $emp['total_tasks'] }} việc</span>
                                    </div>
                                    <div class="task-bar-track">
                                        <div class="task-bar-fill" style="width:{{ $emp['done_rate'] }}%"></div>
                                    </div>
                                    <div class="task-chips">
                                        @if($emp['done_tasks'] > 0)
                                            <span class="t-chip done"><i data-lucide="check" style="width:9px;height:9px"></i>{{ $emp['done_tasks'] }}</span>
                                        @endif
                                        @if($emp['doing_tasks'] > 0)
                                            <span class="t-chip doing"><i data-lucide="clock" style="width:9px;height:9px"></i>{{ $emp['doing_tasks'] }}</span>
                                        @endif
                                        @if($emp['overdue_tasks'] > 0)
                                            <span class="t-chip over"><i data-lucide="alert-triangle" style="width:9px;height:9px"></i>{{ $emp['overdue_tasks'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span style="font-weight:900;color:#334155">{{ $emp['doc_count'] }}</span>
                                <span style="color:#94A3B8;font-size:11px"> file</span>
                            </td>
                            <td>
                                <span style="font-size:12px;color:#64748B;font-weight:800">{{ $emp['joined'] }}</span>
                            </td>
                            <td>
                                <button type="button" class="view-btn" onclick="openEmpDetail({{ json_encode($emp) }})">
                                    <i data-lucide="eye" style="width:13px;height:13px"></i>Chi tiết
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9">
                                <div class="emp-empty">
                                    <i data-lucide="users" style="width:40px;height:40px"></i>
                                    Không tìm thấy nhân viên phù hợp.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="board-footer">
            <span>Hiển thị {{ $employees->count() }} / {{ $totalEmployees }} hồ sơ</span>
            <span style="color:#001F5B;font-weight:900">Hiệu suất TB: {{ $avgDoneRate }}%</span>
        </div>
    </div>

</div>

{{-- Detail Modal --}}
<div class="emp-modal-backdrop" id="empDetailModal">
    <div class="emp-modal">
        <div class="modal-hd">
            <div class="modal-hd-title">Hồ sơ chi tiết nhân viên</div>
            <button class="modal-close" onclick="closeEmpDetail()">
                <i data-lucide="x" style="width:16px;height:16px"></i>
            </button>
        </div>
        <div class="modal-bd" id="empDetailBody">
            {{-- filled by JS --}}
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function openEmpDetail(emp) {
    const rClass = { 1:'director', 2:'manager', 4:'leader', 5:'leader', 6:'leader', 7:'leader' }[emp.role_id] || 'employee';
    const rLabel = { director:'Giám đốc', manager:'Trưởng phòng cấp cao', leader:'Trưởng phòng', employee:'Nhân viên' }[rClass];
    const doneRate = emp.done_rate || 0;

    document.getElementById('empDetailBody').innerHTML = `
        <div class="detail-profile">
            <div class="detail-avatar">${emp.avatar}</div>
            <div style="flex:1;min-width:0">
                <div class="detail-name">${emp.name}</div>
                <div class="detail-sub">${emp.email}</div>
                <div class="detail-badges">
                    <span class="role-pill ${rClass}">${emp.role_name}</span>
                    <span class="status-dot ${emp.is_active ? 'active' : 'inactive'}" style="margin-left:4px">
                        ${emp.is_active ? 'Đang hoạt động' : 'Ngừng hoạt động'}
                    </span>
                </div>
            </div>
        </div>

        <div class="info-grid">
            <div class="info-field">
                <label>Mã nhân viên</label>
                <span style="font-family:monospace">${emp.code}</span>
            </div>
            <div class="info-field">
                <label>Phòng ban</label>
                <span>${emp.dept}</span>
            </div>
            <div class="info-field">
                <label>Email</label>
                <span>${emp.email}</span>
            </div>
            <div class="info-field">
                <label>Ngày tham gia</label>
                <span>${emp.joined}</span>
            </div>
            <div class="info-field">
                <label>Cập nhật lần cuối</label>
                <span>${emp.last_active}</span>
            </div>
            <div class="info-field">
                <label>Tài liệu tải lên</label>
                <span>${emp.doc_count} file</span>
            </div>
        </div>

        <div>
            <div style="font-size:14px;font-weight:900;color:#001F5B;margin-bottom:12px;display:flex;align-items:center;gap:8px">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#003DA5" stroke-width="2.5"><rect x="2" y="3" width="20" height="14" rx="2"/><path d="M8 21h8M12 17v4"/></svg>
                Thống kê công việc
            </div>
            <div class="stat-row">
                <div class="stat-box">
                    <div class="num">${emp.total_tasks}</div>
                    <div class="lbl">Tổng cộng</div>
                </div>
                <div class="stat-box green">
                    <div class="num">${emp.done_tasks}</div>
                    <div class="lbl">Hoàn thành</div>
                </div>
                <div class="stat-box blue">
                    <div class="num">${emp.doing_tasks}</div>
                    <div class="lbl">Đang làm</div>
                </div>
                <div class="stat-box red">
                    <div class="num">${emp.overdue_tasks}</div>
                    <div class="lbl">Quá hạn</div>
                </div>
            </div>
            <div style="margin-top:14px">
                <div style="display:flex;justify-content:space-between;font-size:12px;font-weight:900;color:#001F5B;margin-bottom:6px">
                    <span>Hiệu suất hoàn thành</span><span>${doneRate}%</span>
                </div>
                <div style="height:10px;background:#EEF2F7;border-radius:999px;overflow:hidden">
                    <div style="height:100%;width:${doneRate}%;background:linear-gradient(90deg,#2563EB,#16A34A);border-radius:inherit;transition:width .4s"></div>
                </div>
            </div>
        </div>
    `;

    document.getElementById('empDetailModal').classList.add('open');
    lucide.createIcons();
}

function closeEmpDetail() {
    document.getElementById('empDetailModal').classList.remove('open');
}

document.getElementById('empDetailModal').addEventListener('click', function(e) {
    if (e.target === this) closeEmpDetail();
});
</script>
@endsection
