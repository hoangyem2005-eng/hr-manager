@extends('admin.layouts.app')

@section('title', 'Trung tâm điều hành - MobiFone HR')
@section('page_title', 'Trung tâm điều hành')

@section('head_extra')
<style>
    .exec-shell { display: grid; gap: 20px; }
    .exec-hero { position: relative; overflow: hidden; background: linear-gradient(135deg, #001F5B 0%, #003DA5 100%); color: #fff; border: 1px solid rgba(0,61,165,.18); border-radius: 8px; padding: 28px; display: grid; grid-template-columns: 1.2fr .8fr; gap: 24px; }
    .exec-hero::before { content: ''; position: absolute; inset: 0; opacity: .11; background-image: linear-gradient(rgba(255,255,255,.24) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.24) 1px, transparent 1px); background-size: 34px 34px; }
    .exec-hero > * { position: relative; z-index: 1; }
    .exec-kicker { font-size: 11px; letter-spacing: .18em; text-transform: uppercase; color: #BFDBFE; font-weight: 800; }
    .exec-title { font-size: 32px; line-height: 1.15; font-weight: 900; margin-top: 10px; max-width: 720px; }
    .exec-copy { color: #D7E7FF; font-size: 14px; line-height: 1.7; margin-top: 12px; max-width: 700px; }
    .exec-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 22px; }
    .exec-btn { height: 40px; border-radius: 8px; padding: 0 14px; border: 1px solid transparent; display: inline-flex; align-items: center; gap: 8px; font-size: 13px; font-weight: 800; text-decoration: none; cursor: pointer; font-family: inherit; }
    .exec-btn.primary { background: #E4002B; color: #fff; }
    .exec-btn.secondary { background: rgba(255,255,255,.08); border-color: rgba(255,255,255,.22); color: #EAF2FF; }
    .exec-scoreboard { display: grid; grid-template-columns: repeat(2, minmax(0,1fr)); gap: 12px; }
    .exec-metric { border: 1px solid rgba(255,255,255,.16); background: rgba(255,255,255,.1); border-radius: 8px; padding: 16px; backdrop-filter: blur(10px); }
    .exec-metric span { display: block; font-size: 11px; color: #BFDBFE; text-transform: uppercase; letter-spacing: .08em; font-weight: 800; }
    .exec-metric strong { display: block; margin-top: 8px; font-size: 30px; line-height: 1; }
    .exec-grid { display: grid; grid-template-columns: 1.35fr .65fr; gap: 20px; }
    .exec-panel { background: #fff; border: 1px solid #DBE4EF; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 2px rgba(15,23,42,.04); }
    .exec-panel-header { height: 56px; padding: 0 18px; display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid #E5E7EB; }
    .exec-panel-title { font-size: 14px; font-weight: 900; color: #111827; display: flex; align-items: center; gap: 8px; }
    .exec-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .exec-table th { background: #F9FAFB; color: #6B7280; text-transform: uppercase; letter-spacing: .08em; font-size: 10px; text-align: left; padding: 12px 14px; }
    .exec-table td { padding: 13px 14px; border-top: 1px solid #F3F4F6; color: #374151; vertical-align: middle; }
    .exec-chip { display: inline-flex; align-items: center; height: 22px; padding: 0 8px; border-radius: 999px; font-size: 11px; font-weight: 800; }
    .exec-chip.red { background: #FEF2F2; color: #B91C1C; }
    .exec-chip.green { background: #F0FDF4; color: #15803D; }
    .exec-chip.blue { background: #EFF6FF; color: #1D4ED8; }
    .exec-chip.gray { background: #F3F4F6; color: #4B5563; }
    .dept-row { padding: 16px 18px; border-top: 1px solid #F3F4F6; }
    .dept-line { display: flex; justify-content: space-between; gap: 12px; font-size: 13px; font-weight: 800; color: #111827; }
    .dept-meta { display: flex; gap: 16px; margin-top: 8px; color: #6B7280; font-size: 12px; }
    .dept-track { height: 6px; background: #E5E7EB; border-radius: 99px; overflow: hidden; margin-top: 12px; }
    .dept-fill { height: 100%; background: linear-gradient(90deg, #003DA5, #23A6D5); border-radius: 99px; }
    .people-grid { display: grid; grid-template-columns: repeat(3, minmax(0,1fr)); gap: 12px; padding: 18px; }
    .person-card { border: 1px solid #E5E7EB; border-radius: 8px; padding: 14px; background: #fff; }
    .person-top { display: flex; gap: 10px; align-items: center; }
    .avatar { width: 34px; height: 34px; border-radius: 8px; background: #003DA5; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 11px; font-weight: 900; }
    .person-name { font-size: 13px; font-weight: 900; color: #111827; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .person-sub { font-size: 11px; color: #6B7280; margin-top: 2px; }
    .person-actions { display: flex; gap: 8px; margin-top: 12px; }
    .mini-btn { border: 1px solid #E5E7EB; background: #F9FAFB; color: #374151; border-radius: 7px; height: 30px; padding: 0 10px; font-size: 11px; font-weight: 800; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 5px; }
    .modal-overlay { position: fixed; inset: 0; display: none; align-items: center; justify-content: center; background: rgba(0,0,0,.55); z-index: 9999; padding: 16px; }
    .modal-overlay.open { display: flex; }
    .modal { width: min(560px, 100%); background: #fff; border-radius: 8px; overflow: hidden; }
    .modal-head { padding: 18px; background: #001F5B; color: #fff; display: flex; justify-content: space-between; align-items: center; }
    .modal-body { padding: 18px; display: grid; gap: 14px; }
    .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .field label { display: block; font-size: 12px; font-weight: 800; color: #374151; margin-bottom: 6px; }
    .field input, .field select { width: 100%; height: 40px; border: 1px solid #D1D5DB; border-radius: 8px; padding: 0 11px; font-family: inherit; font-size: 13px; }
    .password-field { position: relative; }
    .password-field input { padding-right: 42px; }
    .password-toggle { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); width: 28px; height: 28px; border: 0; background: transparent; color: #6B7280; border-radius: 7px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; }
    .password-toggle:hover { background: #F3F4F6; color: #003DA5; }
    .password-toggle:focus-visible { outline: 2px solid #93C5FD; outline-offset: 2px; }
    .modal-actions { padding: 16px 18px; border-top: 1px solid #E5E7EB; display: flex; justify-content: flex-end; gap: 10px; }
    @media (max-width: 1100px) { .exec-hero, .exec-grid { grid-template-columns: 1fr; } .people-grid { grid-template-columns: repeat(2, minmax(0,1fr)); } }
    @media (max-width: 680px) { .exec-scoreboard, .people-grid, .form-grid { grid-template-columns: 1fr; } .exec-title { font-size: 26px; } }
</style>
@endsection

@section('content')
<div class="exec-shell">
    <section class="exec-hero">
        <div>
            <div class="exec-kicker">Hoạt động nhân sự MobiFone</div>
            <h1 class="exec-title">Điều hành nhân sự, phòng ban và tiến độ công việc trên một màn hình.</h1>
            <p class="exec-copy">Theo dõi năng lực vận hành theo phòng ban, nắm nhanh việc quá hạn và xử lý các điểm nghẽn ảnh hưởng đến mục tiêu chung.</p>
            <div class="exec-actions">
                <button class="exec-btn primary" onclick="openModal('addUserModal')"><i data-lucide="user-plus"></i> Thêm nhân sự</button>
                <a class="exec-btn secondary" href="{{ route('admin.tasks') }}"><i data-lucide="send"></i> Giao mục tiêu</a>
                <a class="exec-btn secondary" href="{{ route('admin.reports') }}"><i data-lucide="bar-chart-3"></i> Báo cáo</a>
            </div>
        </div>
        <div class="exec-scoreboard">
            <div class="exec-metric"><span>Nhân sự</span><strong>{{ $totalUsers }}</strong></div>
            <div class="exec-metric"><span>Phòng ban</span><strong>{{ $totalDepts }}</strong></div>
            <div class="exec-metric"><span>Hoàn thành</span><strong>{{ $completionRate }}%</strong></div>
            <div class="exec-metric"><span>Quá hạn</span><strong style="color:#FCA5A5">{{ $overdueTasks }}</strong></div>
        </div>
    </section>

    <section class="exec-grid" id="phong-ban">
        <div class="exec-panel">
            <div class="exec-panel-header">
                <div class="exec-panel-title"><i data-lucide="radar"></i> Việc nóng toàn công ty</div>
                <a class="mini-btn" href="{{ route('admin.tasks') }}">Xem tất cả</a>
            </div>
            <table class="exec-table">
                <thead><tr><th>Mã</th><th>Công việc</th><th>Phụ trách</th><th>Phòng</th><th>Hạn</th><th>Trạng thái</th></tr></thead>
                <tbody>
                    @forelse($recentTasks as $t)
                        @php
                            $class = $t['is_overdue'] ? 'red' : (str_contains($t['status'], 'Hoàn') ? 'green' : (str_contains($t['status'], 'review') ? 'blue' : 'gray'));
                        @endphp
                        <tr>
                            <td style="font-family:monospace;color:#9CA3AF">{{ $t['id'] }}</td>
                            <td style="font-weight:800;color:#111827">{{ $t['name'] }}</td>
                            <td>{{ $t['assignee'] }}</td>
                            <td>{{ $t['dept'] }}</td>
                            <td style="{{ $t['is_overdue'] ? 'color:#B91C1C;font-weight:900' : '' }}">{{ $t['deadline'] }}</td>
                            <td><span class="exec-chip {{ $class }}">{{ $t['status'] }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" style="text-align:center;color:#9CA3AF;padding:28px">Chưa có công việc.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="exec-panel">
            <div class="exec-panel-header"><div class="exec-panel-title"><i data-lucide="building-2"></i> Phòng ban</div></div>
            @forelse($deptStats as $d)
                <div class="dept-row">
                    <div class="dept-line"><span>{{ $d['name'] }}</span><span>{{ $d['rate'] }}%</span></div>
                    <div class="dept-meta"><span>{{ $d['users'] }} nhân sự</span><span>{{ $d['tasks'] }} việc</span></div>
                    <div class="dept-track"><div class="dept-fill" style="width: {{ $d['rate'] }}%"></div></div>
                </div>
            @empty
                <div class="dept-row" style="color:#9CA3AF">Chưa có phòng ban.</div>
            @endforelse
        </div>
    </section>

    @if(false)
    <section class="exec-panel" id="nhan-su">
        <div class="exec-panel-header">
            <div class="exec-panel-title"><i data-lucide="id-card"></i> Nhân sự toàn công ty</div>
            <button class="mini-btn" onclick="openModal('addUserModal')"><i data-lucide="plus"></i> Thêm</button>
        </div>
        <div class="people-grid">
            @forelse($allUsers as $u)
                <div class="person-card">
                    <div class="person-top">
                        <div class="avatar">{{ $u['avatar'] }}</div>
                        <div style="min-width:0">
                            <div class="person-name">{{ $u['name'] }}</div>
                            <div class="person-sub">{{ $u['role_name'] }} - {{ $u['dept'] }}</div>
                        </div>
                    </div>
                    <div class="person-sub" style="margin-top:10px">{{ $u['email'] }}</div>
                    <div class="person-actions">
                        <button class="mini-btn" onclick="openEditModal({{ json_encode($u) }})"><i data-lucide="pencil"></i> Sửa</button>
                        <form action="{{ route('admin.user.destroy', $u['id']) }}" method="POST" onsubmit="return confirm('Xóa nhân sự {{ $u['name'] }}?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="mini-btn" style="color:#B91C1C"><i data-lucide="trash-2"></i> Xóa</button>
                        </form>
                    </div>
                </div>
            @empty
                <div style="color:#9CA3AF">Chưa có nhân sự.</div>
            @endforelse
        </div>
    </section>
    @endif
</div>

<div class="modal-overlay" id="addUserModal">
    <div class="modal">
        <div class="modal-head"><strong>Thêm nhân sự cấp công ty</strong><button class="mini-btn" onclick="closeModal('addUserModal')">Đóng</button></div>
        <form action="{{ route('admin.user.store') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-grid">
                    <div class="field"><label>Họ tên</label><input name="name" required></div>
                    <div class="field"><label>Email</label><input type="email" name="email" required></div>
                    <div class="field"><label>Mật khẩu</label><div class="password-field"><input type="password" name="password" required><button type="button" class="password-toggle" aria-label="Hiện mật khẩu" title="Hiện mật khẩu"><i data-lucide="eye"></i></button></div></div>
                    <div class="field"><label>Phòng ban</label><select name="department_id" required>@foreach($departments as $d)<option value="{{ $d->id }}">{{ $d->TENPHONG ?? $d->name }}</option>@endforeach</select></div>
                    <div class="field"><label>Vai trò</label><select name="role_id" required>@foreach($roles as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select></div>
                </div>
            </div>
            <div class="modal-actions"><button type="button" class="mini-btn" onclick="closeModal('addUserModal')">Hủy</button><button class="exec-btn primary" type="submit">Tạo tài khoản</button></div>
        </form>
    </div>
</div>

<div class="modal-overlay" id="editUserModal">
    <div class="modal">
        <div class="modal-head"><strong>Chỉnh sửa nhân sự</strong><button class="mini-btn" onclick="closeModal('editUserModal')">Đóng</button></div>
        <form id="editUserForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-body">
                <div class="form-grid">
                    <div class="field"><label>Họ tên</label><input name="name" id="edit_name" required></div>
                    <div class="field"><label>Email</label><input type="email" name="email" id="edit_email" required></div>
                    <div class="field"><label>Mật khẩu mới</label><div class="password-field"><input type="password" name="password"><button type="button" class="password-toggle" aria-label="Hiện mật khẩu" title="Hiện mật khẩu"><i data-lucide="eye"></i></button></div></div>
                    <div class="field"><label>Phòng ban</label><select name="department_id" id="edit_dept" required>@foreach($departments as $d)<option value="{{ $d->id }}">{{ $d->TENPHONG ?? $d->name }}</option>@endforeach</select></div>
                    <div class="field"><label>Vai trò</label><select name="role_id" id="edit_role" required>@foreach($roles as $r)<option value="{{ $r->id }}">{{ $r->name }}</option>@endforeach</select></div>
                </div>
            </div>
            <div class="modal-actions"><button type="button" class="mini-btn" onclick="closeModal('editUserModal')">Hủy</button><button class="exec-btn primary" type="submit">Lưu</button></div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function openModal(id) { document.getElementById(id).classList.add('open'); document.body.style.overflow = 'hidden'; }
    function closeModal(id) { document.getElementById(id).classList.remove('open'); document.body.style.overflow = ''; }
    function openEditModal(u) {
        document.getElementById('editUserForm').action = '/admin/users/' + u.id;
        document.getElementById('edit_name').value = u.name;
        document.getElementById('edit_email').value = u.email;
        document.getElementById('edit_dept').value = u.dept_id;
        document.getElementById('edit_role').value = u.role_id;
        openModal('editUserModal');
        lucide.createIcons();
    }
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
