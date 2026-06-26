@extends('manager.layouts.app')

@section('title', 'Dashboard Trưởng phòng — MobiFone HR')
@section('page_title', '👔 Tổng quan phòng ban')

@section('head_extra')
<style>
    .kpi-grid  { display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px; }
    .team-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:28px; }
    .main-grid { display:grid;grid-template-columns:3fr 2fr;gap:20px;margin-bottom:28px; }

    .kpi-card {
        background:#fff; border-radius:16px;
        padding:20px; display:flex;align-items:flex-start;justify-content:space-between;
        box-shadow:0 1px 3px rgba(0,0,0,.06),0 4px 16px rgba(37,99,235,.06);
        border:1px solid #DBEAFE; transition:all .2s; position:relative; overflow:hidden;
    }
    .kpi-card:hover { transform:translateY(-2px);box-shadow:0 4px 20px rgba(37,99,235,.12); }
    .kpi-card::after { content:'';position:absolute;top:0;left:0;width:4px;height:100%;border-radius:4px 0 0 4px; }
    .kpi-card.blue::after  { background:#2563EB; }
    .kpi-card.green::after { background:#16A34A; }
    .kpi-card.amber::after { background:#D97706; }
    .kpi-card.red::after   { background:#E63946; }

    .kpi-label { font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.1em;color:#94A3B8;margin-bottom:6px; }
    .kpi-value { font-size:34px;font-weight:800;color:#1E3A5F;line-height:1; }
    .kpi-sub   { font-size:11px;color:#94A3B8;margin-top:6px; }

    .kpi-icon { width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
    .kpi-icon.blue  { background:rgba(37,99,235,.1);color:#2563EB; }
    .kpi-icon.green { background:rgba(22,163,74,.1);color:#16A34A; }
    .kpi-icon.amber { background:rgba(217,119,6,.1);color:#D97706; }
    .kpi-icon.red   { background:rgba(230,57,70,.1);color:#E63946; }

    /* TEAM MEMBER CARD */
    .member-card {
        background:#fff; border-radius:16px;
        border:1px solid #DBEAFE; padding:20px;
        box-shadow:0 1px 3px rgba(0,0,0,.04);
        transition:all .2s;
    }
    .member-card:hover { box-shadow:0 4px 16px rgba(37,99,235,.1);transform:translateY(-2px); }
    .member-avatar {
        width:52px; height:52px; border-radius:14px;
        display:flex;align-items:center;justify-content:center;
        font-weight:700;font-size:18px;color:#fff;margin-bottom:12px;
        background:linear-gradient(135deg,#2563EB,#7C3AED);
    }
    .member-name { font-size:14px;font-weight:700;color:#1E3A5F;margin-bottom:2px; }
    .member-role { font-size:11px;color:#94A3B8;margin-bottom:14px; }
    .member-stats { display:flex;gap:16px; }
    .member-stat strong { font-size:18px;font-weight:800;color:#1E3A5F;display:block; }
    .member-stat span { font-size:10px;color:#94A3B8; }

    .progress-wrap { margin-top:12px; }
    .progress-label { display:flex;justify-content:space-between;font-size:11px;color:#64748B;margin-bottom:4px; }
    .progress-bar { height:5px;background:#DBEAFE;border-radius:99px;overflow:hidden; }
    .progress-fill { height:100%;border-radius:99px;transition:width .6s ease; }

    /* CARD */
    .card { background:#fff;border-radius:16px;border:1px solid #DBEAFE;box-shadow:0 1px 3px rgba(0,0,0,.04);overflow:hidden; }
    .card-header { padding:16px 20px;border-bottom:1px solid #DBEAFE;display:flex;align-items:center;justify-content:space-between; }
    .card-title { font-size:14px;font-weight:700;color:#1E3A5F; }
    .card-body { padding:20px; }

    /* TABLE */
    .data-table { width:100%;border-collapse:collapse;font-size:13px; }
    .data-table thead th { background:#EFF6FF;padding:10px 14px;text-align:left;font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:#2563EB;border-bottom:1px solid #DBEAFE; }
    .data-table tbody tr { border-bottom:1px solid #EFF6FF;transition:background .15s; }
    .data-table tbody tr:hover { background:#F5F8FF; }
    .data-table tbody td { padding:12px 14px;color:#374151;vertical-align:middle; }

    .status-pill { display:inline-block;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600; }
    .status-pill.done    { background:#F0FDF4;color:#16A34A; }
    .status-pill.doing   { background:#FFFBEB;color:#D97706; }
    .status-pill.review  { background:#EFF6FF;color:#2563EB; }
    .status-pill.pending { background:#F9FAFB;color:#6B7280; }
    .status-pill.overdue { background:#FFF1F2;color:#E63946; }

    .section-hdr { display:flex;align-items:center;justify-content:space-between;margin-bottom:16px; }
    .section-title { font-size:16px;font-weight:700;color:#1E3A5F;display:flex;align-items:center;gap:8px; }

    /* BUTTONS */
    .btn-primary {
        display:inline-flex;align-items:center;gap:6px;padding:9px 18px;border-radius:10px;
        background:linear-gradient(135deg,#2563EB,#1E40AF);color:#fff;
        font-size:13px;font-weight:600;border:none;cursor:pointer;font-family:inherit;
        box-shadow:0 2px 8px rgba(37,99,235,.3);transition:all .18s;text-decoration:none;
    }
    .btn-primary:hover { transform:translateY(-1px);box-shadow:0 4px 14px rgba(37,99,235,.4); }
    .btn-ghost { display:inline-flex;align-items:center;gap:6px;padding:7px 14px;border-radius:9px;background:#EFF6FF;color:#2563EB;font-size:12px;font-weight:600;border:none;cursor:pointer;font-family:inherit;transition:all .15s;text-decoration:none; }
    .btn-ghost:hover { background:#DBEAFE; }
    .btn-sm { padding:5px 12px;font-size:11px; }

    /* MODAL */
    .modal-overlay { position:fixed;inset:0;background:rgba(0,0,0,.4);display:flex;align-items:center;justify-content:center;z-index:9999;opacity:0;pointer-events:none;transition:opacity .2s; }
    .modal-overlay.open { opacity:1;pointer-events:all; }
    .modal { background:#fff;border-radius:20px;width:480px;max-width:95vw;padding:28px;position:relative;box-shadow:0 20px 60px rgba(0,0,0,.15);transform:translateY(16px);transition:transform .2s;max-height:90vh;overflow-y:auto; }
    .modal-overlay.open .modal { transform:translateY(0); }
    .modal-title { font-size:17px;font-weight:700;color:#1E3A5F;margin-bottom:20px; }
    .form-group { margin-bottom:14px; }
    .form-label { font-size:12px;font-weight:600;color:#374151;margin-bottom:5px;display:block; }
    .form-input { width:100%;height:40px;border:1.5px solid #DBEAFE;border-radius:10px;padding:0 12px;font-size:13px;color:#1E3A5F;outline:none;font-family:inherit;transition:border-color .2s;background:#FAFCFF; }
    .form-input:focus { border-color:#2563EB; }
    .form-row { display:grid;grid-template-columns:1fr 1fr;gap:12px; }
    .modal-actions { display:flex;gap:10px;justify-content:flex-end;margin-top:20px; }
    .lock-info { background:#EFF6FF;border:1px solid #BFDBFE;border-radius:10px;padding:10px 14px;font-size:12px;color:#1D4ED8;display:flex;align-items:center;gap:8px;margin-bottom:14px; }

    @media (max-width:1100px) { .kpi-grid{grid-template-columns:repeat(2,1fr)} .team-grid{grid-template-columns:repeat(2,1fr)} .main-grid{grid-template-columns:1fr} }
    @media (max-width:640px) { .kpi-grid{grid-template-columns:1fr} .team-grid{grid-template-columns:1fr} }
</style>
@endsection

@section('content')

<!-- KPI CARDS -->
<div class="kpi-grid">
    <div class="kpi-card blue">
        <div>
            <div class="kpi-label">Nhân viên phòng</div>
            <div class="kpi-value">{{ $myTeam->count() }}</div>
            <div class="kpi-sub">Trong phòng của bạn</div>
        </div>
        <div class="kpi-icon blue"><i data-lucide="users" style="width:22px;height:22px"></i></div>
    </div>
    <div class="kpi-card green">
        <div>
            <div class="kpi-label">Hoàn thành</div>
            <div class="kpi-value">{{ $doneCount }}</div>
            <div class="kpi-sub">Công việc xong</div>
        </div>
        <div class="kpi-icon green"><i data-lucide="check-circle-2" style="width:22px;height:22px"></i></div>
    </div>
    <div class="kpi-card amber">
        <div>
            <div class="kpi-label">Đang thực hiện</div>
            <div class="kpi-value">{{ $doingCount }}</div>
            <div class="kpi-sub">Công việc đang làm</div>
        </div>
        <div class="kpi-icon amber"><i data-lucide="zap" style="width:22px;height:22px"></i></div>
    </div>
    <div class="kpi-card red">
        <div>
            <div class="kpi-label">Quá hạn</div>
            <div class="kpi-value">{{ $overdueCount }}</div>
            <div class="kpi-sub" style="{{ $overdueCount > 0 ? 'color:#E63946;font-weight:600' : '' }}">
                {{ $overdueCount > 0 ? 'Cần xử lý ngay!' : 'Không có' }}
            </div>
        </div>
        <div class="kpi-icon red"><i data-lucide="alert-circle" style="width:22px;height:22px"></i></div>
    </div>
</div>

<!-- THÀNH VIÊN PHÒNG -->
<div id="team" class="section-hdr">
    <div class="section-title">
        <i data-lucide="users" style="width:18px;height:18px;color:#2563EB"></i>
        Nhân viên trong phòng: {{ $department->TENPHONG ?? '—' }}
    </div>
    <button class="btn-primary" onclick="openModal('addEmpModal')">
        <i data-lucide="user-plus" style="width:15px;height:15px"></i>
        Thêm nhân viên
    </button>
</div>

<div class="team-grid">
    @forelse($myTeam as $m)
        <div class="member-card">
            <div class="member-avatar">{{ substr($m['name'], 0, 2) }}</div>
            <div class="member-name">{{ $m['name'] }}</div>
            <div class="member-role">{{ $m['role_name'] }} · {{ $department->TENPHONG ?? '—' }}</div>
            <div class="member-stats">
                <div class="member-stat"><strong>{{ $m['total'] }}</strong><span>Tổng CV</span></div>
                <div class="member-stat"><strong style="color:#16A34A">{{ $m['done'] }}</strong><span>Hoàn thành</span></div>
                <div class="member-stat"><strong style="color:#E63946">{{ $m['overdue'] }}</strong><span>Quá hạn</span></div>
            </div>
            <div class="progress-wrap">
                <div class="progress-label">
                    <span>Tiến độ tổng</span>
                    <span style="font-weight:700;color:#2563EB">{{ $m['rate'] }}%</span>
                </div>
                <div class="progress-bar">
                    <div class="progress-fill" style="width:{{ $m['rate'] }}%;background:{{ $m['rate'] >= 70 ? '#16A34A' : ($m['rate'] >= 40 ? '#D97706' : '#E63946') }}"></div>
                </div>
            </div>
        </div>
    @empty
        <div class="card" style="grid-column:1/-1;text-align:center;padding:40px">
            <i data-lucide="users" style="width:40px;height:40px;color:#BFDBFE;margin:0 auto 12px;display:block"></i>
            <p style="color:#94A3B8;font-size:14px">Chưa có nhân viên nào trong phòng</p>
            <button class="btn-primary" style="margin-top:12px" onclick="openModal('addEmpModal')">
                <i data-lucide="user-plus" style="width:15px;height:15px"></i> Thêm ngay
            </button>
        </div>
    @endforelse
</div>

<!-- GIAO VIỆC + TIẾN ĐỘ -->
<div class="main-grid">
    <!-- Công việc đã giao -->
    <div id="cong-viec" class="card">
        <div class="card-header">
            <span class="card-title">📋 Công việc đã giao</span>
            <button class="btn-ghost btn-sm" onclick="openModal('assignTaskModal')">
                <i data-lucide="plus" style="width:13px;height:13px"></i> Giao việc mới
            </button>
        </div>
        <div style="overflow-x:auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Công việc</th>
                        <th>Giao cho</th>
                        <th>Deadline</th>
                        <th>Tiến độ</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($myAssignedTasks as $t)
                        <tr>
                            <td style="font-family:monospace;font-size:11px;color:#94A3B8">{{ $t['code'] }}</td>
                            <td style="font-weight:600;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">{{ $t['name'] }}</td>
                            <td style="font-size:12px">{{ $t['assignee'] }}</td>
                            <td style="font-size:12px;color:#64748B">{{ $t['deadline'] }}</td>
                            <td>
                                <div style="display:flex;align-items:center;gap:8px;min-width:80px">
                                    <div style="flex:1;height:5px;background:#DBEAFE;border-radius:99px;overflow:hidden">
                                        <div style="height:100%;width:{{ $t['progress'] }}%;background:{{ $t['progress'] >= 100 ? '#16A34A' : '#2563EB' }};border-radius:99px"></div>
                                    </div>
                                    <span style="font-size:11px;font-weight:700;color:#1E3A5F;min-width:28px">{{ $t['progress'] }}%</span>
                                </div>
                            </td>
                            <td>
                                @php $sc = match($t['status']) { 'Hoàn thành' => 'done', 'Đang làm' => 'doing', 'Đang review' => 'review', 'Quá hạn' => 'overdue', default => 'pending' }; @endphp
                                <span class="status-pill {{ $sc }}">{{ $t['status'] }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;padding:30px;color:#94A3B8">Chưa có công việc được giao</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Thống kê nhanh -->
    <div class="card">
        <div class="card-header">
            <span class="card-title">📊 Tổng quan phòng</span>
        </div>
        <div class="card-body">
            @php
                $total = $doneCount + $doingCount + $pendingCount + $overdueCount;
            @endphp
            <div id="manager-chart"></div>
            <div style="margin-top:16px">
                @foreach([['Hoàn thành',$doneCount,'#16A34A'],['Đang làm',$doingCount,'#D97706'],['Chờ xử lý',$pendingCount,'#6B7280'],['Quá hạn',$overdueCount,'#E63946']] as [$label,$val,$color])
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:8px 0;border-bottom:1px solid #EFF6FF">
                        <div style="display:flex;align-items:center;gap:8px">
                            <span style="width:9px;height:9px;border-radius:50%;background:{{ $color }};display:inline-block"></span>
                            <span style="font-size:13px;color:#374151">{{ $label }}</span>
                        </div>
                        <span style="font-weight:700;font-size:14px;color:#1E3A5F">{{ $val }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- ===== MODAL: Thêm nhân viên (Department LOCKED) ===== -->
<div class="modal-overlay" id="addEmpModal">
    <div class="modal">
        <div class="modal-title">
            <i data-lucide="user-plus" style="width:18px;height:18px;color:#2563EB;vertical-align:middle;margin-right:8px"></i>
            Thêm nhân viên vào phòng
        </div>
        <div class="lock-info">
            <i data-lucide="lock" style="width:15px;height:15px"></i>
            <span>Nhân viên mới sẽ được tự động xếp vào <strong>Phòng {{ $department->TENPHONG ?? Auth::user()->department->TENPHONG ?? '' }}</strong> của bạn</span>
        </div>
        <form action="{{ route('manager.employee.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Họ và tên *</label>
                <input type="text" name="name" class="form-input" placeholder="Nguyễn Văn B" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email *</label>
                <input type="email" name="email" class="form-input" placeholder="nvb@mobifone.vn" required>
            </div>
            <div class="form-group">
                <label class="form-label">Mật khẩu *</label>
                <input type="password" name="password" class="form-input" placeholder="Tối thiểu 6 ký tự" required>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-ghost" onclick="closeModal('addEmpModal')">Hủy</button>
                <button type="submit" class="btn-primary">
                    <i data-lucide="check" style="width:14px;height:14px"></i> Thêm nhân viên
                </button>
            </div>
        </form>
        <button onclick="closeModal('addEmpModal')" style="position:absolute;top:16px;right:16px;background:none;border:none;cursor:pointer;color:#94A3B8">
            <i data-lucide="x" style="width:20px;height:20px"></i>
        </button>
    </div>
</div>

<!-- ===== MODAL: Giao công việc ===== -->
<div id="giao-viec" class="modal-overlay" id="assignTaskModal">
    <div class="modal">
        <div class="modal-title">
            <i data-lucide="clipboard-list" style="width:18px;height:18px;color:#2563EB;vertical-align:middle;margin-right:8px"></i>
            Giao công việc mới
        </div>
        <form action="{{ route('manager.task.assign') }}" method="POST">
            @csrf
            <div class="form-group">
                <label class="form-label">Tên công việc *</label>
                <input type="text" name="task_name" class="form-input" placeholder="Mô tả ngắn công việc..." required>
            </div>
            <div class="form-group">
                <label class="form-label">Giao cho *</label>
                <select name="assigned_to" class="form-input" style="appearance:none" required>
                    <option value="">Chọn nhân viên trong phòng</option>
                    @foreach($allTeamMembers as $m)
                        <option value="{{ $m->id }}">{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Deadline *</label>
                    <input type="date" name="deadline" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-input" style="appearance:none">
                        <option value="Chờ xử lý">Chờ xử lý</option>
                        <option value="Đang làm">Đang làm</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Mô tả thêm</label>
                <textarea name="description" class="form-input" style="height:80px;padding-top:10px;resize:vertical" placeholder="Chi tiết yêu cầu..."></textarea>
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-ghost" onclick="closeModal('assignTaskModal')">Hủy</button>
                <button type="submit" class="btn-primary">
                    <i data-lucide="send" style="width:14px;height:14px"></i> Giao việc
                </button>
            </div>
        </form>
        <button onclick="closeModal('assignTaskModal')" style="position:absolute;top:16px;right:16px;background:none;border:none;cursor:pointer;color:#94A3B8">
            <i data-lucide="x" style="width:20px;height:20px"></i>
        </button>
    </div>
</div>

@endsection

@section('scripts')
<script>
    const data = [{{ $doneCount }}, {{ $doingCount }}, {{ $pendingCount }}, {{ $overdueCount }}];
    new ApexCharts(document.querySelector('#manager-chart'), {
        series: data,
        labels: ['Hoàn thành','Đang làm','Chờ xử lý','Quá hạn'],
        colors: ['#16A34A','#D97706','#6B7280','#E63946'],
        chart: { type:'donut', height:180, sparkline:{ enabled:true } },
        plotOptions: { pie: { donut: { size:'62%', labels:{ show:true, total:{ show:true, label:'Tổng', fontSize:'12px', fontWeight:700, color:'#1E3A5F' } } } } },
        dataLabels: { enabled:false },
        legend: { show:false },
        stroke: { show:false }
    }).render();

    function openModal(id)  { document.getElementById(id).classList.add('open'); document.body.style.overflow='hidden'; }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow=''; }
    document.querySelectorAll('.modal-overlay').forEach(o => {
        o.addEventListener('click', e => { if(e.target===o) closeModal(o.id); });
    });
</script>
@endsection
