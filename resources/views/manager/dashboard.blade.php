@extends('manager.layouts.app')

@section('title', 'Team Dispatch - MobiFone HR')
@section('page_title', 'Team Dispatch')

@section('head_extra')
<style>
    .team-shell { display: grid; gap: 18px; }
    .team-hero { background: linear-gradient(135deg, #F6FAFF 0%, #E8F0FE 100%); border: 1px solid #B9CDF5; border-radius: 8px; padding: 24px; display: grid; grid-template-columns: 1fr auto; gap: 20px; align-items: center; box-shadow: inset 4px 0 0 #E4002B; }
    .team-kicker { color: #003DA5; text-transform: uppercase; letter-spacing: .16em; font-size: 11px; font-weight: 900; }
    .team-title { color: #001F5B; font-size: 30px; line-height: 1.1; font-weight: 900; margin-top: 8px; }
    .team-copy { color: #475569; font-size: 14px; margin-top: 10px; max-width: 680px; line-height: 1.7; }
    .team-actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 18px; }
    .team-btn { height: 40px; border-radius: 8px; border: 1px solid #003DA5; background: #003DA5; color: #fff; padding: 0 14px; display: inline-flex; align-items: center; gap: 8px; font-family: inherit; font-size: 13px; font-weight: 900; cursor: pointer; text-decoration: none; }
    .team-btn:hover { background: #0057C8; border-color: #0057C8; }
    .team-btn.light { background: #fff; color: #003DA5; border-color: #B9CDF5; }
    .team-btn.light:hover { background: #E8F0FE; border-color: #003DA5; }
    .team-meter { width: 190px; background: #fff; border: 1px solid #D8E4F5; border-radius: 8px; padding: 16px; }
    .team-meter strong { display: block; font-size: 34px; color: #003DA5; line-height: 1; }
    .team-meter span { display: block; color: #64748B; font-size: 12px; margin-top: 8px; }
    .ops-grid { display: grid; grid-template-columns: repeat(4, minmax(0,1fr)); gap: 12px; }
    .ops-card { background: #fff; border: 1px solid #D8E4F5; border-radius: 8px; padding: 16px; }
    .ops-card span { display: block; color: #64748B; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; font-weight: 900; }
    .ops-card strong { display: block; color: #001F5B; font-size: 28px; margin-top: 8px; line-height: 1; }
    .workbench { display: grid; grid-template-columns: .95fr 1.05fr; gap: 18px; }
    .team-panel { background: #fff; border: 1px solid #D8E4F5; border-radius: 8px; overflow: hidden; }
    .panel-head { height: 54px; padding: 0 16px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #E6EDF8; }
    .panel-title { color: #001F5B; font-size: 14px; font-weight: 900; display: flex; align-items: center; gap: 8px; }
    .member-list { display: grid; gap: 0; }
    .member-row { display: grid; grid-template-columns: 44px 1fr auto; gap: 12px; align-items: center; padding: 14px 16px; border-top: 1px solid #F1F5F9; }
    .member-avatar { width: 40px; height: 40px; border-radius: 8px; background: #003DA5; color: #fff; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 12px; box-shadow: inset 4px 0 0 #E4002B; }
    .member-name { color: #001F5B; font-size: 13px; font-weight: 900; }
    .member-sub { color: #64748B; font-size: 11px; margin-top: 3px; }
    .rate { width: 54px; text-align: right; color: #003DA5; font-weight: 900; }
    .task-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .task-table th { background: #F8FAFC; text-align: left; color: #64748B; font-size: 10px; text-transform: uppercase; letter-spacing: .08em; padding: 12px; }
    .task-table td { padding: 13px 12px; border-top: 1px solid #F1F5F9; color: #334155; }
    .status-pill { display: inline-flex; align-items: center; height: 22px; border-radius: 999px; padding: 0 8px; font-size: 11px; font-weight: 800; background: #F1F5F9; color: #475569; }
    .incoming-list { display: grid; gap: 10px; padding: 14px; }
    .incoming-card { border: 1px solid #B9CDF5; background: #F6FAFF; border-radius: 8px; padding: 14px; display: grid; gap: 12px; }
    .incoming-top { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; }
    .incoming-code { color: #003DA5; font-size: 11px; font-weight: 900; font-family: monospace; }
    .incoming-title { color: #001F5B; font-size: 14px; font-weight: 900; margin-top: 4px; }
    .incoming-meta { color: #64748B; font-size: 12px; margin-top: 5px; }
    .delegate-form { display: grid; grid-template-columns: 1fr auto; gap: 10px; align-items: center; }
    .delegate-form select { min-height: 38px; border: 1px solid #B9CDF5; border-radius: 8px; padding: 0 10px; font-family: inherit; color: #0F172A; background: #fff; }
    .modal-overlay { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(15,23,42,.45); z-index: 9999; padding: 16px; }
    .modal-overlay.open { display: flex; }
    .modal { width: min(520px, 100%); border-radius: 8px; background: #fff; overflow: hidden; }
    .modal-head { padding: 18px; background: #001F5B; color: #fff; display: flex; justify-content: space-between; align-items: center; }
    .modal-body { padding: 18px; display: grid; gap: 14px; }
    .field label { display: block; margin-bottom: 6px; color: #334155; font-size: 12px; font-weight: 900; }
    .field input, .field select, .field textarea { width: 100%; border: 1px solid #CBD5E1; border-radius: 8px; padding: 0 11px; min-height: 40px; font-family: inherit; font-size: 13px; }
    .password-field { position: relative; }
    .password-field input { padding-right: 42px; }
    .password-toggle { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); width: 28px; height: 28px; border: 0; background: transparent; color: #64748B; border-radius: 7px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; }
    .password-toggle:hover { background: #E8F0FE; color: #003DA5; }
    .password-toggle:focus-visible { outline: 2px solid #5EEAD4; outline-offset: 2px; }
    .field textarea { min-height: 82px; padding-top: 10px; resize: vertical; }
    .assignee-check-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; max-height:220px; overflow:auto; padding:10px; border:1px solid #D4E0F7; border-radius:8px; background:#F8FBFF; }
    .assignee-check { display:flex; align-items:center; gap:10px; padding:10px; border:1px solid #E2EAF8; border-radius:8px; background:#fff; cursor:pointer; font-weight:900; color:#001F5B; }
    .assignee-check input { width:16px; height:16px; accent-color:#003DA5; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .modal-actions { border-top: 1px solid #E2E8F0; padding: 16px 18px; display: flex; justify-content: flex-end; gap: 10px; }
    .note { border: 1px solid #B9CDF5; background: #F6FAFF; color: #003DA5; padding: 12px; border-radius: 8px; font-size: 12px; line-height: 1.5; }
    @media (max-width: 1100px) { .team-hero, .workbench { grid-template-columns: 1fr; } .ops-grid { grid-template-columns: repeat(2, minmax(0,1fr)); } .team-meter { width: auto; } }
    @media (max-width: 640px) { .ops-grid, .form-grid, .delegate-form { grid-template-columns: 1fr; } .team-title { font-size: 24px; } }
</style>
@endsection

@section('content')
<div class="team-shell">
    <section class="team-hero">
        <div>
            <div class="team-kicker">Department dispatch</div>
            <h1 class="team-title">Điều phối phòng {{ $department->TENPHONG ?? $department->name ?? 'của bạn' }}</h1>
            <p class="team-copy">Không còn giao diện chung chung: quản lý chỉ nhìn đội của mình, thêm nhân viên vào đúng phòng, và giao việc trong phạm vi phòng ban.</p>
            <div class="team-actions">
                <button class="team-btn" onclick="openModal('assignTaskModal')"><i data-lucide="send"></i> Giao việc</button>
                <button class="team-btn light" onclick="openModal('addEmpModal')"><i data-lucide="user-plus"></i> Thêm nhân viên</button>
            </div>
        </div>
        <div class="team-meter"><strong>{{ $myTeam->count() }}</strong><span>nhân viên trong phòng đang được quản lý</span></div>
    </section>

    <section class="ops-grid">
        <div class="ops-card"><span>Chờ xử lý</span><strong>{{ $pendingCount }}</strong></div>
        <div class="ops-card"><span>Đang làm</span><strong>{{ $doingCount }}</strong></div>
        <div class="ops-card"><span>Hoàn thành</span><strong>{{ $doneCount }}</strong></div>
        <div class="ops-card"><span>Quá hạn</span><strong style="color:#DC2626">{{ $overdueCount }}</strong></div>
    </section>

    <section class="workbench">
        <div class="team-panel">
            <div class="panel-head"><div class="panel-title"><i data-lucide="users"></i> Đội của tôi</div><button class="team-btn light" onclick="openModal('addEmpModal')" style="height:32px">Thêm</button></div>
            <div class="member-list">
                @forelse($myTeam as $m)
                    <div class="member-row">
                        <div class="member-avatar">{{ $m['avatar'] }}</div>
                        <div>
                            <div class="member-name">{{ $m['name'] }}</div>
                            <div class="member-sub">{{ $m['email'] }} - {{ $m['total'] }} việc, {{ $m['overdue'] }} quá hạn</div>
                        </div>
                        <div class="rate">{{ $m['rate'] }}%</div>
                    </div>
                @empty
                    <div style="padding:24px;color:#94A3B8">Chưa có nhân viên trong phòng.</div>
                @endforelse
            </div>
        </div>

        <div style="display:grid;gap:18px">
            <div class="team-panel">
                <div class="panel-head"><div class="panel-title"><i data-lucide="inbox"></i> Việc cấp trên giao</div><span class="status-pill">{{ $incomingTasks->count() }} việc</span></div>
                <div class="incoming-list">
                    @forelse($incomingTasks as $t)
                        <div class="incoming-card">
                            <div class="incoming-top">
                                <div>
                                    <div class="incoming-code">{{ $t['code'] }}</div>
                                    <div class="incoming-title">{{ $t['name'] }}</div>
                                    <div class="incoming-meta">Người giao: {{ $t['assigner'] }} · Hạn: {{ $t['deadline'] }} · Trạng thái: {{ $t['status'] }}</div>
                                </div>
                                <span class="status-pill">Chờ phân công</span>
                            </div>
                            <form method="POST" action="{{ route('manager.task.delegate', $t['id']) }}" class="delegate-form">
                                @csrf
                                @method('PATCH')
                                <select name="assigned_to" required aria-label="Chọn nhân viên nhận việc {{ $t['code'] }}">
                                    <option value="">Chọn nhân viên trong phòng</option>
                                    @foreach($myTeam as $m)
                                        <option value="{{ $m['id'] }}">{{ $m['name'] }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="team-btn" style="height:38px"><i data-lucide="send"></i> Phân công</button>
                            </form>
                        </div>
                    @empty
                        <div style="padding:24px;color:#94A3B8;text-align:center">Chưa có công việc cấp trên giao cần phân công.</div>
                    @endforelse
                </div>
            </div>

            <div class="team-panel">
                <div class="panel-head"><div class="panel-title"><i data-lucide="list-checks"></i> Việc đã giao</div><button class="team-btn" onclick="openModal('assignTaskModal')" style="height:32px">Giao mới</button></div>
                <div style="overflow-x:auto">
                    <table class="task-table">
                        <thead><tr><th>Mã</th><th>Công việc</th><th>Nhân viên</th><th>Hạn</th><th>Tiến độ</th><th>File</th><th>Trạng thái</th><th></th></tr></thead>
                        <tbody>
                            @forelse($myAssignedTasks as $t)
                                <tr>
                                    <td style="font-family:monospace;color:#94A3B8">{{ $t['code'] }}</td>
                                    <td style="font-weight:900;color:#0F172A">{{ $t['name'] }}</td>
                                    <td>{{ $t['assignee'] }}</td>
                                    <td>{{ $t['deadline'] }}</td>
                                    <td><strong>{{ $t['progress'] }}%</strong></td>
                                    <td><strong>{{ $t['documents_count'] }}</strong></td>
                                    <td><span class="status-pill">{{ $t['status'] }}</span></td>
                                    <td><a class="team-btn light" style="height:32px" href="{{ route('manager.tasks.show', $t['id']) }}">Chi tiết</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="8" style="padding:24px;text-align:center;color:#94A3B8">Chưa giao công việc nào.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal-overlay" id="addEmpModal">
    <div class="modal">
        <div class="modal-head"><strong>Thêm nhân viên vào phòng</strong><button class="team-btn light" onclick="closeModal('addEmpModal')" style="height:30px">Đóng</button></div>
        <form action="{{ route('manager.employee.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="note">Nhân viên mới tự động thuộc phòng {{ $department->TENPHONG ?? $department->name ?? 'hiện tại' }} và có vai trò Nhân viên.</div>
                <div class="field"><label>Họ tên</label><input name="name" required></div>
                <div class="field"><label>Email</label><input type="email" name="email" required></div>
                <div class="field"><label>Mật khẩu</label><div class="password-field"><input type="password" name="password" required><button type="button" class="password-toggle" aria-label="Hiện mật khẩu" title="Hiện mật khẩu"><i data-lucide="eye"></i></button></div></div>
            </div>
            <div class="modal-actions"><button type="button" class="team-btn light" onclick="closeModal('addEmpModal')">Hủy</button><button type="submit" class="team-btn">Thêm nhân viên</button></div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="assignTaskModal">
    <div class="modal">
        <div class="modal-head"><strong>Giao việc trong phòng</strong><button class="team-btn light" onclick="closeModal('assignTaskModal')" style="height:30px">Đóng</button></div>
        <form action="{{ route('manager.task.assign') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="field"><label>Tên công việc</label><input name="task_name" required></div>
                <div class="field">
                    <label>Người cùng làm</label>
                    <div class="assignee-check-grid">
                        @foreach($allTeamMembers as $m)
                            <label class="assignee-check">
                                <input type="checkbox" name="assigned_to[]" value="{{ $m->id }}">
                                <span>{{ $m->name }}</span>
                            </label>
                        @endforeach
                    </div>
                    <div style="font-size:11px;color:#64748B;margin-top:6px">Tích nhiều nhân viên để cùng làm chung một công việc.</div>
                </div>
                <div class="form-grid">
                    <div class="field"><label>Deadline</label><input type="date" name="deadline" required></div>
                    <div class="field"><label>Trạng thái</label><select name="status"><option value="Chờ xử lý">Chờ xử lý</option><option value="Đang làm">Đang làm</option></select></div>
                </div>
                <div class="field"><label>Mô tả</label><textarea name="description"></textarea></div>
            </div>
                <div class="field">
                    <label>Tài liệu đính kèm (Tối đa 5 file, &lt; 20MB/file)</label>
                    <input type="file" name="attachments[]" multiple style="padding-top:8px">
                </div>
            </div>
            <div class="modal-actions"><button type="button" class="team-btn light" onclick="closeModal('assignTaskModal')">Hủy</button><button type="submit" class="team-btn">Giao việc</button></div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.add('open'); document.body.style.overflow = 'hidden'; }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = ''; }
    document.querySelectorAll('.password-toggle').forEach(button => {
        button.addEventListener('click', () => {
            const input = button.closest('.password-field').querySelector('input');
            const icon = button.querySelector('i');
            const shouldShow = input.type === 'password';

            input.type = shouldShow ? 'text' : 'password';
            icon.setAttribute('data-lucide', shouldShow ? 'eye-off' : 'eye');
            button.setAttribute('aria-label', shouldShow ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
            button.setAttribute('title', shouldShow ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
            lucide.createIcons();
        });
    });
    document.querySelectorAll('.modal-overlay').forEach(el => el.addEventListener('click', e => { if (e.target === el) closeModal(el.id); }));
    lucide.createIcons();
</script>
@endsection

