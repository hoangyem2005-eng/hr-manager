@extends('admin.layouts.app')

@section('title', 'Tất cả nhân viên - Giám đốc')
@section('page_title', 'Tất cả nhân viên')

@section('content')
@php
    $visibleMembers = collect($usersList->items());
    $activeVisibleMembers = $visibleMembers->where('status', 'active')->count();
    $managerVisibleMembers = $visibleMembers->filter(fn ($member) => in_array($member['role'], ['Giám đốc', 'Trưởng phòng'], true))->count();
    $visibleTaskCount = $visibleMembers->sum('tasks');
@endphp

<style>
    .people-page { display: grid; gap: 20px; }
    .people-hero { position: relative; overflow: hidden; display: grid; grid-template-columns: 1fr auto; gap: 18px; align-items: end; padding: 26px; border: 1px solid #B9CDF5; border-radius: 8px; background: linear-gradient(135deg, #F8FBFF 0%, #EEF5FF 62%, #FFF5F7 100%); box-shadow: inset 5px 0 0 #E4002B; }
    .people-hero:after { content: ""; position: absolute; right: -44px; top: -60px; width: 210px; height: 210px; border: 34px solid rgba(0,61,165,.06); border-radius: 999px; }
    .people-kicker { color: #E4002B; font-size: 12px; font-weight: 900; letter-spacing: .16em; text-transform: uppercase; }
    .people-hero h2 { margin: 8px 0 8px; color: #001F5B; font-size: 34px; line-height: 1.08; font-weight: 900; letter-spacing: -.03em; }
    .people-hero p { color: #52637A; font-size: 15px; line-height: 1.6; max-width: 760px; }
    .hero-count { position: relative; z-index: 1; min-width: 180px; border: 1px solid #D4E0F7; border-radius: 8px; background: rgba(255,255,255,.86); padding: 16px; }
    .hero-count span { display: block; color: #64748B; font-size: 10px; font-weight: 900; letter-spacing: .1em; text-transform: uppercase; }
    .hero-count strong { display: block; margin-top: 8px; color: #003DA5; font-size: 34px; line-height: 1; font-weight: 900; }
    .people-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; border: 1px solid #D4E0F7; border-radius: 8px; background: #fff; padding: 16px; }
    .people-filter { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .filter-field { position: relative; }
    .filter-field i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #8AA0BD; width: 16px; height: 16px; }
    .people-input, .people-select { height: 42px; border: 1px solid #D4E0F7; border-radius: 8px; background: #F8FAFF; color: #001F5B; font-size: 13px; outline: none; }
    .people-input { width: 320px; padding: 0 12px 0 38px; }
    .people-select { padding: 0 34px 0 12px; }
    .people-input:focus, .people-select:focus { border-color: #003DA5; background: #fff; box-shadow: 0 0 0 3px rgba(0,61,165,.1); }
    .filter-btn { height: 42px; border: 0; border-radius: 8px; padding: 0 16px; background: #003DA5; color: #fff; font-size: 13px; font-weight: 900; cursor: pointer; }
    .clear-link { color: #E4002B; font-size: 13px; font-weight: 900; text-decoration: none; }
    .people-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
    .people-stat { border: 1px solid #D4E0F7; border-radius: 8px; background: #fff; padding: 16px; }
    .people-stat span { display: block; color: #64748B; font-size: 10px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; }
    .people-stat strong { display: block; margin-top: 9px; color: #001F5B; font-size: 28px; line-height: 1; font-weight: 900; }
    .people-board { overflow: hidden; border: 1px solid #D4E0F7; border-radius: 8px; background: #fff; box-shadow: 0 16px 34px rgba(0,31,91,.06); }
    .board-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 16px 18px; border-bottom: 1px solid #E5EDF8; }
    .board-title { display: flex; align-items: center; gap: 10px; color: #001F5B; font-size: 18px; font-weight: 900; }
    .people-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; padding: 18px; background: linear-gradient(180deg, #fff 0%, #F8FAFF 100%); }
    .person-card { position: relative; overflow: hidden; min-width: 0; border: 1px solid #D4E0F7; border-radius: 8px; background: #fff; transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease; }
    .person-card:before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 5px; background: #E4002B; }
    .person-card:hover { transform: translateY(-2px); border-color: #B9CDF5; box-shadow: 0 14px 30px rgba(0,31,91,.08); }
    .person-main { display: flex; align-items: flex-start; gap: 12px; padding: 16px 16px 14px 20px; }
    .person-avatar { width: 52px; height: 52px; flex: 0 0 52px; display: grid; place-items: center; border-radius: 8px; background: #003DA5; color: #fff; font-size: 15px; font-weight: 900; box-shadow: inset 5px 0 0 #E4002B; }
    .person-name { color: #001F5B; font-size: 15px; line-height: 1.25; font-weight: 900; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .person-email { margin-top: 4px; color: #64748B; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .person-code { margin-top: 8px; color: #94A3B8; font-size: 11px; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-weight: 900; }
    .person-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding: 0 16px 16px 20px; }
    .meta-box { min-width: 0; border: 1px solid #EEF2F7; border-radius: 8px; background: #F8FAFF; padding: 10px; }
    .meta-box span { color: #7B8BA5; font-size: 10px; font-weight: 900; letter-spacing: .06em; text-transform: uppercase; }
    .meta-box strong { display: block; margin-top: 5px; color: #001F5B; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .person-foot { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 16px 14px 20px; border-top: 1px solid #EEF2F7; background: #FBFDFF; }
    .role-pill, .status-pill { display: inline-flex; align-items: center; gap: 6px; min-height: 28px; border-radius: 999px; padding: 0 10px; font-size: 12px; font-weight: 900; }
    .role-pill.admin { background: #001F5B; color: #fff; }
    .role-pill.manager { background: #003DA5; color: #fff; }
    .role-pill.employee { border: 1px solid #E2E8F0; background: #F1F5F9; color: #334155; }
    .status-pill.active { background: #ECFDF5; color: #15803D; }
    .status-pill.inactive { background: #F1F5F9; color: #64748B; }
    .empty-state { grid-column: 1 / -1; padding: 46px 20px; border: 1px dashed #B9CDF5; border-radius: 8px; background: #F6FAFF; color: #64748B; text-align: center; font-weight: 800; }
    .people-footer { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 14px 18px; border-top: 1px solid #E5EDF8; background: #fff; color: #64748B; font-size: 13px; }
    .pagination-wrap :where(nav) { display: flex; justify-content: flex-end; }
    @media (max-width: 1280px) { .people-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 920px) { .people-hero { grid-template-columns: 1fr; } .people-stats { grid-template-columns: repeat(2, minmax(0,1fr)); } .people-input { width: 100%; } .people-filter, .filter-field { width: 100%; } .people-select, .filter-btn { width: 100%; } }
    @media (max-width: 620px) { .people-grid { grid-template-columns: 1fr; } .people-stats { grid-template-columns: 1fr; } .people-footer { align-items: stretch; flex-direction: column; } }
</style>

<div class="people-page">
    <section class="people-hero">
        <div>
            <div class="people-kicker">People Directory</div>
            <h2>Toàn bộ nhân viên MobiFone</h2>
            <p>Xem nhanh nhân sự theo phòng ban, chức vụ, trạng thái tài khoản và số công việc đang được giao trên toàn WorkHub.</p>
        </div>
        <div class="hero-count">
            <span>Tổng nhân viên</span>
            <strong>{{ $usersList->total() }}</strong>
        </div>
    </section>

    <section class="people-toolbar">
        <form method="GET" action="{{ route('admin.members') }}" class="people-filter">
            <div class="filter-field">
                <i data-lucide="search"></i>
                <input name="search" value="{{ $search }}" class="people-input" placeholder="Tìm tên, email hoặc mã NV..." />
            </div>
            <select name="dept" onchange="this.form.submit()" class="people-select">
                <option value="">Tất cả phòng ban</option>
                @foreach($departments as $d)
                    <option value="{{ $d->TENPHONG }}" {{ $deptFilter == $d->TENPHONG ? 'selected' : '' }}>{{ $d->TENPHONG }}</option>
                @endforeach
            </select>
            <button type="submit" class="filter-btn">Lọc</button>
            @if(!empty($search) || !empty($deptFilter))
                <a href="{{ route('admin.members') }}" class="clear-link">Xóa lọc</a>
            @endif
        </form>
    </section>

    <section class="people-stats">
        <div class="people-stat"><span>Đang hiển thị</span><strong>{{ $visibleMembers->count() }}</strong></div>
        <div class="people-stat"><span>Hoạt động</span><strong>{{ $activeVisibleMembers }}</strong></div>
        <div class="people-stat"><span>Quản lý</span><strong>{{ $managerVisibleMembers }}</strong></div>
        <div class="people-stat"><span>Việc được giao</span><strong>{{ $visibleTaskCount }}</strong></div>
    </section>

    <section class="people-board">
        <div class="board-head">
            <div class="board-title"><i data-lucide="users" style="width:22px;height:22px"></i>Danh sách nhân viên</div>
            <span class="status-pill active">{{ $usersList->firstItem() ?? 0 }}-{{ $usersList->lastItem() ?? 0 }} / {{ $usersList->total() }}</span>
        </div>

        <div class="people-grid">
            @forelse($usersList as $member)
                @php
                    $roleClass = match($member['role_id']) {
                        \App\Models\User::ROLE_ADMIN => 'admin',
                        \App\Models\User::ROLE_MANAGER => 'manager',
                        default => 'employee',
                    };
                @endphp
                <article class="person-card">
                    <div class="person-main">
                        <div class="person-avatar">{{ $member['avatar'] }}</div>
                        <div style="min-width:0">
                            <div class="person-name">{{ $member['name'] }}</div>
                            <div class="person-email">{{ $member['email'] }}</div>
                            <div class="person-code">{{ $member['id'] }}</div>
                        </div>
                    </div>
                    <div class="person-meta">
                        <div class="meta-box"><span>Phòng ban</span><strong>{{ $member['dept'] }}</strong></div>
                        <div class="meta-box"><span>Công việc</span><strong>{{ $member['tasks'] }} việc</strong></div>
                        <div class="meta-box"><span>Ngày tham gia</span><strong>{{ $member['joined'] }}</strong></div>
                        <div class="meta-box"><span>Trạng thái</span><strong>{{ $member['status'] === 'active' ? 'Hoạt động' : 'Tạm khóa' }}</strong></div>
                    </div>
                    <div class="person-foot">
                        <span class="role-pill {{ $roleClass }}">{{ $member['role'] }}</span>
                        <span class="status-pill {{ $member['status'] }}">{{ $member['status'] === 'active' ? 'Hoạt động' : 'Tạm khóa' }}</span>
                    </div>
                </article>
            @empty
                <div class="empty-state">
                    <i data-lucide="user-x" style="width:34px;height:34px"></i>
                    <div style="margin-top:10px">Không tìm thấy nhân viên phù hợp.</div>
                </div>
            @endforelse
        </div>

        <div class="people-footer">
            <span>Hiển thị {{ $usersList->firstItem() ?? 0 }}-{{ $usersList->lastItem() ?? 0 }} / {{ $usersList->total() }} nhân viên</span>
            <div class="pagination-wrap">{{ $usersList->links() }}</div>
        </div>
    </section>
</div>
@endsection
