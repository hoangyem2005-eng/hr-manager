@extends('manager.layouts.app')

@section('title', 'Nhân viên phòng - Trưởng phòng')
@section('page_title', 'Nhân viên phòng')

@section('content')
<style>
    .team-page { display: grid; gap: 20px; }
    .team-hero { position: relative; overflow: hidden; display: grid; grid-template-columns: 1fr auto; gap: 18px; align-items: end; padding: 26px; border: 1px solid #B9CDF5; border-radius: 8px; background: linear-gradient(135deg, #F8FBFF 0%, #EEF5FF 64%, #FFF5F7 100%); box-shadow: inset 5px 0 0 #E4002B; }
    .team-hero:after { content: ""; position: absolute; right: -46px; top: -66px; width: 220px; height: 220px; border: 34px solid rgba(0,61,165,.06); border-radius: 999px; }
    .team-kicker { color: #003DA5; font-size: 12px; font-weight: 900; letter-spacing: .16em; text-transform: uppercase; }
    .team-hero h2 { margin: 8px 0; color: #001F5B; font-size: 34px; line-height: 1.08; font-weight: 900; letter-spacing: -.03em; }
    .team-hero p { color: #52637A; font-size: 15px; line-height: 1.6; max-width: 760px; }
    .hero-card { position: relative; z-index: 1; min-width: 180px; border: 1px solid #D4E0F7; border-radius: 8px; background: #fff; padding: 16px; }
    .hero-card span { display: block; color: #64748B; font-size: 10px; font-weight: 900; letter-spacing: .1em; text-transform: uppercase; }
    .hero-card strong { display: block; margin-top: 8px; color: #003DA5; font-size: 34px; line-height: 1; font-weight: 900; }
    .team-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; border: 1px solid #D4E0F7; border-radius: 8px; background: #fff; padding: 16px; }
    .team-search { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .search-field { position: relative; }
    .search-field i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 16px; height: 16px; color: #8AA0BD; }
    .team-input { width: 340px; height: 42px; border: 1px solid #D4E0F7; border-radius: 8px; background: #F8FAFF; color: #001F5B; padding: 0 12px 0 38px; outline: none; }
    .team-input:focus { border-color: #003DA5; background: #fff; box-shadow: 0 0 0 3px rgba(0,61,165,.1); }
    .team-btn { min-height: 42px; display: inline-flex; align-items: center; justify-content: center; gap: 8px; border: 0; border-radius: 8px; padding: 0 16px; background: #003DA5; color: #fff; font-weight: 900; cursor: pointer; text-decoration: none; }
    .team-btn.secondary { border: 1px solid #B9CDF5; background: #fff; color: #003DA5; }
    .clear-link { color: #E4002B; font-size: 13px; font-weight: 900; text-decoration: none; }
    .team-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
    .team-stat { border: 1px solid #D4E0F7; border-radius: 8px; background: #fff; padding: 16px; }
    .team-stat span { display: block; color: #64748B; font-size: 10px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; }
    .team-stat strong { display: block; margin-top: 9px; color: #001F5B; font-size: 28px; line-height: 1; font-weight: 900; }
    .team-board { overflow: hidden; border: 1px solid #D4E0F7; border-radius: 8px; background: #fff; box-shadow: 0 16px 34px rgba(0,31,91,.06); }
    .board-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 18px; border-bottom: 1px solid #E5EDF8; }
    .board-title { display: flex; align-items: center; gap: 10px; color: #001F5B; font-size: 18px; font-weight: 900; }
    .team-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; padding: 18px; background: linear-gradient(180deg, #fff 0%, #F8FAFF 100%); }
    .member-card { position: relative; overflow: hidden; min-width: 0; border: 1px solid #D4E0F7; border-radius: 8px; background: #fff; transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease; }
    .member-card:before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 5px; background: #E4002B; }
    .member-card:hover { transform: translateY(-2px); border-color: #B9CDF5; box-shadow: 0 14px 30px rgba(0,31,91,.08); }
    .member-main { display: flex; align-items: flex-start; gap: 12px; padding: 16px 16px 14px 20px; }
    .avatar { width: 52px; height: 52px; flex: 0 0 52px; display: grid; place-items: center; border-radius: 8px; background: #003DA5; color: #fff; font-size: 15px; font-weight: 900; box-shadow: inset 5px 0 0 #E4002B; }
    .member-name { color: #001F5B; font-size: 15px; line-height: 1.25; font-weight: 900; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .member-email { margin-top: 4px; color: #64748B; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .member-code { margin-top: 8px; color: #94A3B8; font-size: 11px; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-weight: 900; }
    .metric-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; padding: 0 16px 16px 20px; }
    .metric { min-width: 0; border: 1px solid #EEF2F7; border-radius: 8px; background: #F8FAFF; padding: 10px; }
    .metric span { color: #7B8BA5; font-size: 10px; font-weight: 900; letter-spacing: .06em; text-transform: uppercase; }
    .metric strong { display: block; margin-top: 5px; color: #001F5B; font-size: 13px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .member-foot { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 16px 14px 20px; border-top: 1px solid #EEF2F7; background: #FBFDFF; }
    .rate-pill, .status-pill { display: inline-flex; align-items: center; min-height: 28px; border-radius: 999px; padding: 0 10px; font-size: 12px; font-weight: 900; }
    .rate-pill { background: #E8F0FE; color: #003DA5; }
    .status-pill.active { background: #ECFDF5; color: #15803D; }
    .status-pill.inactive { background: #F1F5F9; color: #64748B; }
    .empty-state { grid-column: 1 / -1; padding: 46px 20px; border: 1px dashed #B9CDF5; border-radius: 8px; background: #F6FAFF; color: #64748B; text-align: center; font-weight: 800; }
    .team-footer { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 14px 18px; border-top: 1px solid #E5EDF8; background: #fff; color: #64748B; font-size: 13px; }
    @media (max-width: 1280px) { .team-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 920px) { .team-hero { grid-template-columns: 1fr; } .team-stats { grid-template-columns: repeat(2, minmax(0,1fr)); } .team-input, .search-field, .team-search { width: 100%; } .team-btn { width: 100%; } }
    @media (max-width: 620px) { .team-grid { grid-template-columns: 1fr; } .team-stats { grid-template-columns: 1fr; } .team-footer { align-items: stretch; flex-direction: column; } }
</style>

<div class="team-page">
    <section class="team-hero">
        <div>
            <div class="team-kicker">Department People</div>
            <h2>Nhân viên phòng {{ $department->TENPHONG ?? $department->name ?? 'của bạn' }}</h2>
            <p>Quản lý danh sách nhân viên trong phạm vi phòng ban, theo dõi số việc, tiến độ và các việc quá hạn của từng người.</p>
        </div>
        <div class="hero-card">
            <span>Tổng nhân viên</span>
            <strong>{{ $summary['total'] }}</strong>
        </div>
    </section>

    <section class="team-toolbar">
        <form method="GET" action="{{ route('manager.members') }}" class="team-search">
            <div class="search-field">
                <i data-lucide="search"></i>
                <input class="team-input" name="search" value="{{ $search }}" placeholder="Tìm tên hoặc email nhân viên..." />
            </div>
            <button type="submit" class="team-btn">Lọc</button>
            @if($search !== '')
                <a class="clear-link" href="{{ route('manager.members') }}">Xóa lọc</a>
            @endif
        </form>
        <a href="{{ route('manager.tasks') }}" class="team-btn secondary"><i data-lucide="send" style="width:17px;height:17px"></i>Giao việc cho đội</a>
    </section>

    <section class="team-stats">
        <div class="team-stat"><span>Đang hiển thị</span><strong>{{ $summary['visible'] }}</strong></div>
        <div class="team-stat"><span>Hoạt động</span><strong>{{ $summary['active'] }}</strong></div>
        <div class="team-stat"><span>Tổng việc</span><strong>{{ $summary['tasks'] }}</strong></div>
        <div class="team-stat"><span>Quá hạn</span><strong style="color:#E4002B">{{ $summary['overdue'] }}</strong></div>
    </section>

    <section class="team-board">
        <div class="board-head">
            <div class="board-title"><i data-lucide="users" style="width:22px;height:22px"></i>Đội của tôi</div>
            <span class="rate-pill">{{ $teamMembers->firstItem() ?? 0 }}-{{ $teamMembers->lastItem() ?? 0 }} / {{ $teamMembers->total() }}</span>
        </div>

        <div class="team-grid">
            @forelse($teamMembers as $member)
                <article class="member-card">
                    <div class="member-main">
                        <div class="avatar">{{ $member['avatar'] }}</div>
                        <div style="min-width:0">
                            <div class="member-name">{{ $member['name'] }}</div>
                            <div class="member-email">{{ $member['email'] }}</div>
                            <div class="member-code">{{ $member['code'] }}</div>
                        </div>
                    </div>
                    <div class="metric-grid">
                        <div class="metric"><span>Chức vụ</span><strong>{{ $member['role_name'] }}</strong></div>
                        <div class="metric"><span>Ngày tham gia</span><strong>{{ $member['joined'] }}</strong></div>
                        <div class="metric"><span>Đang xử lý</span><strong>{{ $member['doing'] }} việc</strong></div>
                        <div class="metric"><span>Quá hạn</span><strong>{{ $member['overdue'] }} việc</strong></div>
                    </div>
                    <div class="member-foot">
                        <span class="rate-pill">{{ $member['rate'] }}% hoàn thành</span>
                        <span class="status-pill {{ $member['status'] }}">{{ $member['status'] === 'active' ? 'Hoạt động' : 'Tạm khóa' }}</span>
                    </div>
                </article>
            @empty
                <div class="empty-state">
                    <i data-lucide="user-x" style="width:34px;height:34px"></i>
                    <div style="margin-top:10px">Không tìm thấy nhân viên trong phòng.</div>
                </div>
            @endforelse
        </div>

        <div class="team-footer">
            <span>Hiển thị {{ $teamMembers->firstItem() ?? 0 }}-{{ $teamMembers->lastItem() ?? 0 }} / {{ $teamMembers->total() }} nhân viên</span>
            <div>{{ $teamMembers->links() }}</div>
        </div>
    </section>
</div>
@endsection
