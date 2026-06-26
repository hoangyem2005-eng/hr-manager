@extends('admin.layouts.app')

@section('title', 'Dashboard Giám đốc — MobiFone HR')
@section('page_title', '🏢 Tổng quan Giám đốc')

@section('head_extra')
<style>
    /* ======= GRID LAYOUT ======= */
    .kpi-grid   { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    .main-grid  { display: grid; grid-template-columns: 3fr 2fr; gap: 20px; margin-bottom: 24px; }
    .dept-grid  { display: grid; grid-template-columns: repeat(3, 1fr); gap: 16px; margin-bottom: 24px; }

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

    .avatar-badge {
        width: 34px; height: 34px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 12px; color: #fff;
    }

    /* ======= ROLE PILL ======= */
    .pill {
        display: inline-block;
        padding: 3px 10px; border-radius: 99px;
        font-size: 11px; font-weight: 600;
    }
    .pill.director { background: rgba(230,57,70,.1);  color: #E63946; }
    .pill.leader   { background: rgba(37,99,235,.1);  color: #2563EB; }
    .pill.employee { background: rgba(107,114,128,.1); color: #374151; }

    .status-pill {
        display: inline-block;
        padding: 3px 10px; border-radius: 99px;
        font-size: 11px; font-weight: 600;
    }
    .status-pill.done    { background: #F0FDF4; color: #16A34A; }
    .status-pill.doing   { background: #FFFBEB; color: #D97706; }
    .status-pill.review  { background: #EFF6FF; color: #2563EB; }
    .status-pill.pending { background: #F9FAFB; color: #6B7280; }
    .status-pill.overdue { background: #FFF1F2; color: #E63946; }

    /* ======= DEPT CARD ======= */
    .dept-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #F1F5F9;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,.05);
    }
    .dept-name   { font-size: 14px; font-weight: 700; color: #0F172A; margin-bottom: 12px; }
    .dept-stats  { display: flex; gap: 20px; margin-bottom: 14px; }
    .dept-stat   { font-size: 11px; color: #64748B; }
    .dept-stat strong { font-size: 18px; font-weight: 800; color: #0F172A; display: block; }
    .progress-bar {
        height: 6px; background: #E2E8F0; border-radius: 99px; overflow: hidden;
    }
    .progress-fill { height: 100%; border-radius: 99px; transition: width .6s ease; }

    /* ======= SECTION HEADER ======= */
    .section-hdr {
        display: flex; align-items: center; justify-content: space-between;
        margin-bottom: 16px;
    }
    .section-title {
        font-size: 16px; font-weight: 700; color: #0F172A;
        display: flex; align-items: center; gap: 8px;
    }

    /* ======= BTN ======= */
    .btn-primary {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 9px 18px; border-radius: 10px;
        background: linear-gradient(135deg, #E63946, #C0392B);
        color: #fff; font-size: 13px; font-weight: 600;
        text-decoration: none; border: none; cursor: pointer;
        font-family: inherit;
        box-shadow: 0 2px 8px rgba(230,57,70,.3);
        transition: all .18s;
    }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 14px rgba(230,57,70,.4); }

    .btn-ghost {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 7px 14px; border-radius: 9px;
        background: #F1F5F9; color: #374151;
        font-size: 12px; font-weight: 600;
        border: none; cursor: pointer; font-family: inherit;
        transition: all .15s; text-decoration: none;
    }
    .btn-ghost:hover { background: #E2E8F0; }
    .btn-danger {
        display: inline-flex; align-items: center; gap: 4px;
        padding: 5px 10px; border-radius: 7px;
        background: rgba(230,57,70,.08); color: #E63946;
        font-size: 11px; font-weight: 600;
        border: 1px solid rgba(230,57,70,.15); cursor: pointer; font-family: inherit;
        transition: all .15s;
    }
    .btn-danger:hover { background: rgba(230,57,70,.15); }

    /* ======= MODAL ======= */
    .modal-overlay {
        position: fixed; inset: 0; background: rgba(0,0,0,.45);
        display: flex; align-items: center; justify-content: center;
        z-index: 9999; opacity: 0; pointer-events: none;
        transition: opacity .2s;
    }
    .modal-overlay.open { opacity: 1; pointer-events: all; }
    .modal {
        background: #fff; border-radius: 20px;
        width: 520px; max-width: 95vw;
        padding: 28px; position: relative;
        box-shadow: 0 20px 60px rgba(0,0,0,.2);
        transform: translateY(16px);
        transition: transform .2s;
        max-height: 90vh; overflow-y: auto;
    }
    .modal-overlay.open .modal { transform: translateY(0); }
    .modal-title { font-size: 18px; font-weight: 700; color: #0F172A; margin-bottom: 20px; }

    .form-group { margin-bottom: 16px; }
    .form-label { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 6px; display: block; }
    .form-input {
        width: 100%; height: 40px; border: 1.5px solid #E2E8F0;
        border-radius: 10px; padding: 0 12px;
        font-size: 13px; color: #0F172A; outline: none;
        font-family: inherit; transition: border-color .2s;
    }
    .form-input:focus { border-color: #E63946; }
    .form-select { appearance: none; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 20px; }

    /* ======= COMPLETION CIRCLE ======= */
    .rate-circle {
        width: 50px; height: 50px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 13px; font-weight: 700; color: #0F172A;
        background: conic-gradient(#16A34A var(--pct), #E2E8F0 0);
        position: relative;
    }
    .rate-circle::before {
        content: ''; position: absolute; inset: 5px;
        border-radius: 50%; background: #fff;
    }
    .rate-circle span { position: relative; z-index: 1; font-size: 11px; }

    @media (max-width: 1200px) {
        .kpi-grid  { grid-template-columns: repeat(2, 1fr); }
        .main-grid { grid-template-columns: 1fr; }
        .dept-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 700px) {
        .kpi-grid  { grid-template-columns: 1fr; }
        .dept-grid { grid-template-columns: 1fr; }
    }
</style>
@endsection

@section('content')

<!-- ===== KPI CARDS ===== -->
<div class="kpi-grid">
    <div class="kpi-card blue">
        <div>
            <div class="kpi-label">Tổng nhân sự</div>
            <div class="kpi-value">{{ $totalUsers }}</div>
            <div class="kpi-sub">{{ $totalDepts }} phòng ban</div>
        </div>
        <div class="kpi-icon blue">
            <i data-lucide="users" style="width:22px;height:22px"></i>
        </div>
    </div>

    <div class="kpi-card green">
        <div>
            <div class="kpi-label">Hoàn thành</div>
            <div class="kpi-value">{{ $completionRate }}%</div>
            <div class="kpi-sub">{{ $doneTasks }}/{{ $totalTasks }} công việc</div>
        </div>
        <div class="kpi-icon green">
            <i data-lucide="check-circle-2" style="width:22px;height:22px"></i>
        </div>
    </div>

    <div class="kpi-card amber">
        <div>
            <div class="kpi-label">Đang thực hiện</div>
            <div class="kpi-value">{{ $doingTasks }}</div>
            <div class="kpi-sub">Công việc đang làm</div>
        </div>
        <div class="kpi-icon amber">
            <i data-lucide="zap" style="width:22px;height:22px"></i>
        </div>
    </div>

    <div class="kpi-card red">
        <div>
            <div class="kpi-label">Quá hạn</div>
            <div class="kpi-value">{{ $overdueTasks }}</div>
            <div class="kpi-sub" style="color:#E63946;font-weight:600">Cần xử lý ngay</div>
        </div>
        <div class="kpi-icon red">
            <i data-lucide="alert-circle" style="width:22px;height:22px"></i>
        </div>
        @if($overdueTasks > 0)
            <span style="position:absolute;top:12px;right:12px;width:8px;height:8px;border-radius:50%;background:#E63946;animation:ping 1.2s infinite"></span>
        @endif
    </div>
</div>

<!-- ===== PHÒNG BAN ===== -->
<div id="phong-ban" class="section-hdr">
    <div class="section-title">
        <i data-lucide="building-2" style="width:18px;height:18px;color:#E63946"></i>
        Thống kê phòng ban
    </div>
</div>
<div class="dept-grid" style="margin-bottom:28px">
    @forelse($deptStats as $d)
        <div class="dept-card">
            <div class="dept-name">{{ $d['name'] }}</div>
            <div class="dept-stats">
                <div class="dept-stat"><strong>{{ $d['users'] }}</strong>Nhân viên</div>
                <div class="dept-stat"><strong>{{ $d['tasks'] }}</strong>Công việc</div>
                <div class="dept-stat"><strong>{{ $d['rate'] }}%</strong>Hoàn thành</div>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" style="width:{{ $d['rate'] }}%; background: {{ $d['rate'] >= 70 ? '#16A34A' : ($d['rate'] >= 40 ? '#D97706' : '#E63946') }}"></div>
            </div>
        </div>
    @empty
        <div class="dept-card" style="grid-column:1/-1;text-align:center;color:#94A3B8;padding:40px">
            Chưa có phòng ban nào
        </div>
    @endforelse
</div>

<!-- ===== MAIN GRID: Công việc + Chart ===== -->
<div class="main-grid">
    <!-- Công việc gần đây -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Công việc gần đây — Toàn công ty</span>
            <a href="{{ route('dashboard.tasks') }}" class="card-link">Xem tất cả →</a>
        </div>
        <div style="overflow-x:auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Tên công việc</th>
                        <th>Người thực hiện</th>
                        <th>Phòng ban</th>
                        <th>Deadline</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentTasks as $t)
                        <tr>
                            <td style="font-size:11px;font-family:monospace;color:#94A3B8">{{ $t['id'] }}</td>
                            <td style="font-weight:600;max-width:180px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $t['name'] }}</td>
                            <td>{{ $t['assignee'] }}</td>
                            <td style="color:#64748B;font-size:12px">{{ $t['dept'] }}</td>
                            <td style="font-size:12px;{{ $t['is_overdue'] ? 'color:#E63946;font-weight:600' : 'color:#94A3B8' }}">{{ $t['deadline'] }}</td>
                            <td>
                                @php
                                    $sc = match($t['status']) {
                                        'Hoàn thành' => 'done', 'Đang làm' => 'doing',
                                        'Đang review' => 'review', 'Quá hạn' => 'overdue',
                                        default => 'pending'
                                    };
                                @endphp
                                <span class="status-pill {{ $sc }}">{{ $t['status'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;padding:30px;color:#94A3B8">Chưa có công việc nào</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Chart -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">Phân bổ công việc</span>
        </div>
        <div class="card-body">
            <div id="admin-donut-chart"></div>
            <div style="margin-top:16px;space-y:8px">
                @foreach($statusChart as $s)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:6px 0;border-bottom:1px solid #F1F5F9">
                        <div style="display:flex;align-items:center;gap:8px">
                            <span style="width:10px;height:10px;border-radius:50%;background:{{ $s['color'] }};display:inline-block"></span>
                            <span style="font-size:13px;color:#374151">{{ $s['name'] }}</span>
                        </div>
                        <span style="font-weight:700;font-size:14px;color:#0F172A">{{ $s['value'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- ===== BẢNG NHÂN SỰ (FULL CRUD) ===== -->
<div id="nhan-su" class="section-hdr">
    <div class="section-title">
        <i data-lucide="users" style="width:18px;height:18px;color:#E63946"></i>
        Quản lý nhân sự — Toàn công ty
    </div>
    <button class="btn-primary" onclick="openModal('addUserModal')">
        <i data-lucide="user-plus" style="width:15px;height:15px"></i>
        Thêm nhân sự
    </button>
</div>

<div class="card" style="margin-bottom:32px">
    <div style="overflow-x:auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Mã NV</th>
                    <th>Họ tên</th>
                    <th>Email</th>
                    <th>Phòng ban</th>
                    <th>Vai trò</th>
                    <th>Công việc</th>
                    <th>Ngày tham gia</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allUsers as $u)
                    <tr>
                        <td style="font-family:monospace;font-size:11px;color:#94A3B8">{{ $u['code'] }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px">
                                <div class="avatar-badge" style="background: {{ $u['role_id'] == 3 ? 'linear-gradient(135deg,#E63946,#C0392B)' : ($u['role_id'] == 1 ? 'linear-gradient(135deg,#2563EB,#1E40AF)' : 'linear-gradient(135deg,#7C3AED,#5B21B6)') }}">
                                    {{ $u['avatar'] }}
                                </div>
                                <span style="font-weight:600;font-size:13px">{{ $u['name'] }}</span>
                            </div>
                        </td>
                        <td style="color:#64748B;font-size:12px">{{ $u['email'] }}</td>
                        <td style="font-size:12px">{{ $u['dept'] }}</td>
                        <td>
                            <span class="pill {{ $u['role_id'] == 3 ? 'director' : ($u['role_id'] == 1 ? 'leader' : 'employee') }}">
                                {{ $u['role_name'] }}
                            </span>
                        </td>
                        <td style="font-weight:600;color:#0F172A">{{ $u['tasks'] }}</td>
                        <td style="font-size:12px;color:#94A3B8">{{ $u['joined'] }}</td>
                        <td>
                            <div style="display:flex;gap:6px">
                                <button class="btn-ghost" style="padding:5px 10px;font-size:11px"
                                    onclick="openEditModal({{ json_encode($u) }})">
                                    <i data-lucide="pencil" style="width:13px;height:13px"></i> Sửa
                                </button>
                                <form action="{{ route('admin.user.destroy', $u['id']) }}" method="POST"
                                      onsubmit="return confirm('Xóa nhân sự {{ $u['name'] }}?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-danger">
                                        <i data-lucide="trash-2" style="width:12px;height:12px"></i> Xóa
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" style="text-align:center;padding:40px;color:#94A3B8">Chưa có nhân sự</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- ===== MODAL: Thêm nhân sự ===== -->
<div class="modal-overlay" id="addUserModal">
    <div class="modal">
        <div class="modal-title">
            <i data-lucide="user-plus" style="width:18px;height:18px;color:#E63946;vertical-align:middle;margin-right:8px"></i>
            Thêm nhân sự mới
        </div>
        <form action="{{ route('admin.user.store') }}" method="POST">
            @csrf
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Họ và tên *</label>
                    <input type="text" name="name" class="form-input" placeholder="Nguyễn Văn A" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-input" placeholder="nva@mobifone.vn" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Mật khẩu *</label>
                    <input type="password" name="password" class="form-input" placeholder="Tối thiểu 6 ký tự" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Phòng ban *</label>
                    <select name="department_id" class="form-input form-select" required>
                        <option value="">Chọn phòng ban</option>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}">{{ $d->TENPHONG ?? $d->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Vai trò *</label>
                <select name="role_id" class="form-input form-select" required>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-ghost" onclick="closeModal('addUserModal')">Hủy</button>
                <button type="submit" class="btn-primary">
                    <i data-lucide="check" style="width:14px;height:14px"></i> Tạo tài khoản
                </button>
            </div>
        </form>
        <button onclick="closeModal('addUserModal')" style="position:absolute;top:16px;right:16px;background:none;border:none;cursor:pointer;color:#94A3B8">
            <i data-lucide="x" style="width:20px;height:20px"></i>
        </button>
    </div>
</div>

<!-- ===== MODAL: Sửa nhân sự ===== -->
<div class="modal-overlay" id="editUserModal">
    <div class="modal">
        <div class="modal-title">
            <i data-lucide="pencil" style="width:18px;height:18px;color:#2563EB;vertical-align:middle;margin-right:8px"></i>
            Chỉnh sửa nhân sự
        </div>
        <form id="editUserForm" method="POST">
            @csrf @method('PUT')
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Họ và tên *</label>
                    <input type="text" name="name" id="edit_name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" id="edit_email" class="form-input" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Mật khẩu mới (để trống nếu không đổi)</label>
                    <input type="password" name="password" class="form-input" placeholder="Để trống nếu không đổi">
                </div>
                <div class="form-group">
                    <label class="form-label">Phòng ban *</label>
                    <select name="department_id" id="edit_dept" class="form-input form-select" required>
                        @foreach($departments as $d)
                            <option value="{{ $d->id }}">{{ $d->TENPHONG ?? $d->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Vai trò *</label>
                <select name="role_id" id="edit_role" class="form-input form-select" required>
                    @foreach($roles as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-ghost" onclick="closeModal('editUserModal')">Hủy</button>
                <button type="submit" class="btn-primary">
                    <i data-lucide="save" style="width:14px;height:14px"></i> Lưu thay đổi
                </button>
            </div>
        </form>
        <button onclick="closeModal('editUserModal')" style="position:absolute;top:16px;right:16px;background:none;border:none;cursor:pointer;color:#94A3B8">
            <i data-lucide="x" style="width:20px;height:20px"></i>
        </button>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Chart
    const chartData = @json($statusChart);
    new ApexCharts(document.querySelector('#admin-donut-chart'), {
        series: chartData.map(d => d.value),
        labels: chartData.map(d => d.name),
        colors: chartData.map(d => d.color),
        chart: { type: 'donut', height: 200, sparkline: { enabled: true } },
        plotOptions: { pie: { donut: { size: '65%', labels: { show: true, total: { show: true, label: 'Tổng', color: '#0F172A', fontSize: '13px', fontWeight: 700 } } } } },
        dataLabels: { enabled: false },
        legend: { show: false },
        stroke: { show: false },
        tooltip: { style: { fontFamily: 'Inter, sans-serif', fontSize: '12px' } }
    }).render();

    // Modal helpers
    function openModal(id) {
        document.getElementById(id).classList.add('open');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        document.getElementById(id).classList.remove('open');
        document.body.style.overflow = '';
    }
    document.querySelectorAll('.modal-overlay').forEach(o => {
        o.addEventListener('click', e => { if (e.target === o) closeModal(o.id); });
    });

    function openEditModal(u) {
        document.getElementById('editUserForm').action = '/admin/users/' + u.id;
        document.getElementById('edit_name').value  = u.name;
        document.getElementById('edit_email').value = u.email;
        document.getElementById('edit_dept').value  = u.dept_id;
        document.getElementById('edit_role').value  = u.role_id;
        openModal('editUserModal');
        lucide.createIcons();
    }

    // Ping animation
    const style = document.createElement('style');
    style.textContent = '@keyframes ping { 0%,100%{transform:scale(1);opacity:.75} 50%{transform:scale(1.5);opacity:0} }';
    document.head.appendChild(style);
</script>
@endsection
