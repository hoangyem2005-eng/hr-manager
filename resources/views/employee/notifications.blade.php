<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Thông báo của tôi - MobiFone Employee</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --mf-blue: #003DA5;
            --mf-blue-dark: #001F5B;
            --mf-red: #E4002B;
            --mf-border: #D4E0F7;
            --bg: #F0F4FB;
            --text: #0D1B3E;
            --muted: #64748B;
            --sidebar-w: 272px;
        }
        body { min-height: 100vh; font-family: 'Be Vietnam Pro', sans-serif; background: var(--bg); color: var(--text); }
        a { color: inherit; text-decoration: none; }
        button { font: inherit; }
        .layout { min-height: 100vh; display: grid; grid-template-columns: var(--sidebar-w) 1fr; }
        .sidebar { position: sticky; top: 0; height: 100vh; display: flex; flex-direction: column; overflow-y: auto; color: #fff; background: linear-gradient(180deg, #001F5B 0%, #003DA5 100%); }
        .brand { display: flex; align-items: center; gap: 12px; padding: 22px 20px 18px; border-bottom: 1px solid rgba(255,255,255,.12); }
        .brand-mark { width: 38px; height: 38px; border-radius: 8px; display: grid; place-items: center; background: #fff; color: var(--mf-blue); font-weight: 900; box-shadow: inset 5px 0 0 var(--mf-red); }
        .brand-word { display: inline-flex; align-items: baseline; background: #fff; border-radius: 7px; padding: 4px 9px; line-height: 1; box-shadow: 0 6px 18px rgba(0,0,0,.12); }
        .brand-word .blue { color: var(--mf-blue); font-size: 17px; font-weight: 900; letter-spacing: -.03em; }
        .brand-word .red { color: var(--mf-red); font-size: 17px; font-weight: 900; letter-spacing: -.03em; }
        .brand-sub { margin-top: 6px; color: #BFD8FF; font-size: 10px; letter-spacing: .12em; text-transform: uppercase; font-weight: 700; }
        .profile-card { margin: 18px 14px; padding: 16px; border: 1px solid rgba(255,255,255,.14); border-radius: 12px; background: rgba(255,255,255,.08); }
        .profile-row { display: flex; align-items: center; gap: 12px; }
        .avatar { width: 46px; height: 46px; border-radius: 10px; display: grid; place-items: center; background: linear-gradient(135deg, #E4002B, #FF5A7A); color: #fff; font-weight: 900; font-size: 15px; }
        .profile-name { font-size: 14px; font-weight: 900; }
        .profile-meta { margin-top: 3px; font-size: 11px; color: rgba(255,255,255,.68); }
        .profile-dept { margin-top: 12px; display: flex; align-items: center; gap: 6px; border-radius: 8px; padding: 7px 10px; background: rgba(255,255,255,.1); font-size: 12px; font-weight: 700; color: rgba(255,255,255,.88); }
        .sidebar-stats { padding: 0 14px; display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .stat-box { border: 1px solid rgba(255,255,255,.1); border-radius: 10px; padding: 12px; background: rgba(255,255,255,.06); }
        .stat-label { font-size: 10px; text-transform: uppercase; letter-spacing: .1em; color: rgba(255,255,255,.56); font-weight: 800; }
        .stat-val { margin-top: 4px; font-size: 26px; line-height: 1; font-weight: 900; }
        .green { color: #6EE7B7; } .red { color: #FCA5A5; }
        .sidebar-nav { margin: 16px 14px 0; display: flex; flex-direction: column; gap: 6px; }
        .nav-item { min-height: 44px; display: flex; align-items: center; gap: 10px; padding: 10px 14px; border-radius: 8px; color: rgba(255,255,255,.78); font-weight: 800; font-size: 14px; }
        .nav-item:hover { background: rgba(255,255,255,.08); color: #fff; }
        .nav-item.active { background: #fff; color: var(--mf-blue); box-shadow: inset 3px 0 0 var(--mf-red); }
        .nav-badge { margin-left: auto; min-width: 24px; height: 24px; display: grid; place-items: center; border-radius: 99px; background: var(--mf-red); color: #fff; font-size: 11px; }
        .sidebar-footer { margin-top: auto; padding: 16px 14px 20px; border-top: 1px solid rgba(255,255,255,.1); }
        .logout-btn { width: 100%; border: 0; background: transparent; color: rgba(255,255,255,.72); display: flex; align-items: center; gap: 10px; padding: 11px 14px; border-radius: 8px; font-weight: 800; cursor: pointer; }
        .logout-btn:hover { background: rgba(255,255,255,.08); color: #fff; }
        .main { min-width: 0; display: flex; flex-direction: column; }
        .topbar { height: 72px; display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 0 24px; background: #fff; border-bottom: 1px solid var(--mf-border); }
        .topbar-title { font-weight: 900; color: var(--mf-blue-dark); }
        .topbar-actions { display: flex; align-items: center; gap: 10px; }
        .icon-btn { width: 42px; height: 42px; display: grid; place-items: center; border: 1px solid var(--mf-border); border-radius: 8px; background: #fff; color: var(--mf-blue-dark); }
        .content { padding: 24px; display: flex; flex-direction: column; gap: 20px; }
        .hero { position: relative; overflow: hidden; display: flex; justify-content: space-between; gap: 18px; padding: 28px; border: 1px solid #B9CDF5; border-radius: 8px; background: linear-gradient(135deg, #F7FAFF 0%, #EEF5FF 62%, #FFF7FA 100%); box-shadow: inset 4px 0 0 var(--mf-red); }
        .hero::after { content: ''; position: absolute; right: 44px; top: -34px; width: 190px; height: 190px; border-radius: 999px; border: 34px solid rgba(0,61,165,.06); }
        .hero-main { position: relative; z-index: 1; display: flex; gap: 16px; align-items: flex-start; }
        .hero-icon { width: 50px; height: 50px; display: grid; place-items: center; border: 1px solid #B9CDF5; border-radius: 8px; background: #fff; color: var(--mf-blue); }
        .eyebrow { color: var(--mf-blue); font-size: 12px; letter-spacing: .1em; text-transform: uppercase; font-weight: 900; }
        h1 { margin-top: 4px; color: var(--mf-blue-dark); font-size: 34px; line-height: 1.1; letter-spacing: -.03em; font-weight: 900; }
        .hero p { margin-top: 8px; color: #40516B; font-size: 15px; line-height: 1.6; }
        .summary { position: relative; z-index: 1; display: grid; grid-template-columns: repeat(2, 126px); gap: 10px; align-self: center; }
        .summary-card { border: 1px solid #C7D7F6; border-radius: 8px; padding: 14px; background: rgba(255,255,255,.8); }
        .summary-card span { display: block; color: #64748B; font-size: 10px; letter-spacing: .08em; text-transform: uppercase; font-weight: 900; }
        .summary-card strong { display: block; margin-top: 8px; color: var(--mf-blue-dark); font-size: 28px; line-height: 1; font-weight: 900; }
        .notice { border: 1px solid #BBF7D0; background: #F0FDF4; color: #166534; border-radius: 8px; padding: 12px 14px; font-weight: 800; }
        .workspace { display: grid; grid-template-columns: minmax(0, 1fr) 330px; gap: 18px; align-items: start; }
        .panel { overflow: hidden; border: 1px solid #D4E0F7; border-radius: 8px; background: #fff; box-shadow: 0 16px 30px rgba(0,31,91,.05); }
        .panel-head { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 16px 18px; border-bottom: 1px solid #E5EAF5; }
        .panel-title { display: flex; align-items: center; gap: 10px; color: var(--mf-blue-dark); font-size: 17px; font-weight: 900; }
        .tabs { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .tab-btn { border: 1px solid #D8E2F4; border-radius: 999px; padding: 8px 12px; background: #fff; color: #64748B; font-size: 12px; font-weight: 900; cursor: pointer; }
        .tab-btn.active { border-color: var(--mf-blue); background: var(--mf-blue); color: #fff; }
        .mark-form button { border: 0; background: transparent; color: var(--mf-blue); font-size: 12px; font-weight: 900; cursor: pointer; }
        .notif-list { display: grid; }
        .notif-form { display: block; }
        .notif-item { width: 100%; display: grid; grid-template-columns: 46px minmax(0, 1fr) auto; gap: 14px; align-items: start; border: 0; border-bottom: 1px solid #EEF2F7; padding: 16px 18px; background: #fff; text-align: left; cursor: pointer; transition: background .18s ease, transform .18s ease; }
        .notif-item:hover { background: #F7FAFF; transform: translateX(2px); }
        .notif-item.unread { background: linear-gradient(90deg, rgba(0,61,165,.08), #fff 42%); }
        .notif-icon { width: 42px; height: 42px; display: grid; place-items: center; border-radius: 10px; color: var(--mf-blue); background: #E8F0FE; box-shadow: inset 4px 0 0 var(--mf-red); }
        .notif-icon.overdue { color: #DC2626; background: #FEF2F2; }
        .notif-icon.deadline { color: #B45309; background: #FFFBEB; }
        .notif-icon.complete { color: #15803D; background: #F0FDF4; }
        .notif-title { color: var(--mf-blue-dark); font-size: 14px; line-height: 1.35; font-weight: 900; }
        .notif-desc { margin-top: 4px; color: #64748B; font-size: 12px; line-height: 1.45; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .notif-meta { margin-top: 8px; display: flex; align-items: center; gap: 8px; color: #94A3B8; font-size: 11px; font-weight: 800; }
        .unread-dot { width: 9px; height: 9px; border-radius: 99px; background: var(--mf-red); margin-top: 8px; }
        .empty-state { padding: 48px 20px; text-align: center; color: #64748B; }
        .empty-icon { width: 58px; height: 58px; margin: 0 auto 14px; display: grid; place-items: center; border-radius: 14px; background: #E8F0FE; color: var(--mf-blue); }
        .empty-state strong { display: block; color: var(--mf-blue-dark); font-size: 15px; }
        .empty-state span { display: block; margin-top: 6px; font-size: 13px; }
        .side-panel { display: grid; gap: 14px; }
        .info-card { border: 1px solid #D4E0F7; border-radius: 8px; padding: 18px; background: #fff; }
        .info-card h2 { color: var(--mf-blue-dark); font-size: 16px; font-weight: 900; }
        .metric-grid { margin-top: 14px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .metric { border-radius: 8px; padding: 14px; background: #F4F8FF; }
        .metric span { color: #64748B; font-size: 10px; text-transform: uppercase; letter-spacing: .08em; font-weight: 900; }
        .metric strong { display: block; margin-top: 7px; color: var(--mf-blue-dark); font-size: 24px; font-weight: 900; }
        .quick-link { margin-top: 14px; display: flex; align-items: center; justify-content: space-between; gap: 12px; border: 1px solid #D4E0F7; border-radius: 8px; padding: 13px; background: #F8FAFF; color: var(--mf-blue-dark); font-size: 13px; font-weight: 900; }
        .hidden { display: none !important; }
        @media (max-width: 1100px) { .workspace { grid-template-columns: 1fr; } .summary { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 900px) { .layout { grid-template-columns: 1fr; } .sidebar { height: auto; position: static; } .hero { flex-direction: column; } .notif-item { grid-template-columns: 42px minmax(0, 1fr); } .unread-dot { display: none; } }
    </style>
</head>
<body>
@php
    $user = Auth::user();
    $totalNotifications = $notifications->count();
    $taskCount = $notificationTypeCounts['task'] ?? 0;
    $deadlineCount = $notificationTypeCounts['deadline'] ?? 0;
@endphp
<div class="layout">
    <aside class="sidebar">
        <div class="brand">
            <div class="brand-mark">M</div>
            <div>
                <div class="brand-word"><span class="blue">Mobi</span><span class="red">Fone</span></div>
                <div class="brand-sub">Employee WorkHub</div>
            </div>
        </div>

        <div class="profile-card">
            <div class="profile-row">
                <div class="avatar">{{ mb_strtoupper(mb_substr($user->name ?? 'NV', 0, 2)) }}</div>
                <div>
                    <div class="profile-name">{{ $user->name }}</div>
                    <div class="profile-meta">{{ $user->email }}</div>
                </div>
            </div>
            <div class="profile-dept"><i data-lucide="building-2" style="width:15px;height:15px"></i>{{ $user->department->TENPHONG ?? 'MobiFone' }}</div>
        </div>

        <div class="sidebar-stats">
            <div class="stat-box"><div class="stat-label">Tổng báo</div><div class="stat-val">{{ $totalNotifications }}</div></div>
            <div class="stat-box"><div class="stat-label">Chưa đọc</div><div class="stat-val red">{{ $unreadCount }}</div></div>
            <div class="stat-box"><div class="stat-label">Công việc</div><div class="stat-val">{{ $taskCount }}</div></div>
            <div class="stat-box"><div class="stat-label">Deadline</div><div class="stat-val green">{{ $deadlineCount }}</div></div>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('employee.dashboard') }}" class="nav-item">
                <i data-lucide="layout-dashboard" style="width:16px;height:16px"></i>Dashboard
            </a>
            <a href="{{ route('employee.tasks') }}" class="nav-item">
                <i data-lucide="clipboard-list" style="width:16px;height:16px"></i>Công việc của tôi
            </a>
            <a href="{{ route('employee.notifications') }}" class="nav-item active">
                <i data-lucide="bell" style="width:16px;height:16px"></i>Thông báo
                @if($unreadCount > 0)<span class="nav-badge">{{ $unreadCount }}</span>@endif
            </a>
        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn"><i data-lucide="log-out" style="width:16px;height:16px"></i>Đăng xuất</button>
            </form>
        </div>
    </aside>

    <main class="main">
        <header class="topbar">
            <div class="topbar-title">Thông báo của tôi</div>
            <div class="topbar-actions">
                <a href="{{ route('employee.tasks') }}" class="icon-btn" title="Công việc"><i data-lucide="clipboard-list" style="width:20px;height:20px"></i></a>
                <span class="icon-btn" title="{{ $user->name }}">{{ mb_strtoupper(mb_substr($user->name ?? 'NV', 0, 2)) }}</span>
            </div>
        </header>

        <section class="content">
            @if(session('success'))
                <div class="notice">{{ session('success') }}</div>
            @endif

            <section class="hero">
                <div class="hero-main">
                    <div class="hero-icon"><i data-lucide="bell-ring" style="width:24px;height:24px"></i></div>
                    <div>
                        <div class="eyebrow">Personal Inbox</div>
                        <h1>Thông báo của tôi</h1>
                        <p>Theo dõi giao việc, nhắc deadline và các cập nhật dành riêng cho bạn trong không gian nhân viên.</p>
                    </div>
                </div>
                <div class="summary">
                    <div class="summary-card"><span>Chưa đọc</span><strong>{{ $unreadCount }}</strong></div>
                    <div class="summary-card"><span>Tổng số</span><strong>{{ $totalNotifications }}</strong></div>
                </div>
            </section>

            <section class="workspace">
                <div class="panel">
                    <div class="panel-head">
                        <div class="panel-title">
                            <i data-lucide="inbox" style="width:21px;height:21px"></i>
                            Hộp thông báo
                        </div>
                        @if($notifications->isNotEmpty())
                            <form method="POST" action="{{ route('employee.notifications.markAllRead') }}" class="mark-form">
                                @csrf
                                <button type="submit">Đánh dấu tất cả đã đọc</button>
                            </form>
                        @endif
                    </div>

                    <div class="panel-head">
                        <div class="tabs" id="notif-tabs">
                            <button type="button" class="tab-btn active" data-tab="all">Tất cả</button>
                            <button type="button" class="tab-btn" data-tab="unread">Chưa đọc {{ $unreadCount }}</button>
                            <button type="button" class="tab-btn" data-tab="task">Công việc {{ $taskCount }}</button>
                            <button type="button" class="tab-btn" data-tab="deadline">Deadline {{ $deadlineCount }}</button>
                        </div>
                    </div>

                    <div class="notif-list" id="notif-list">
                        @forelse($notifications as $n)
                            @php
                                $iconMap = [
                                    'general' => 'megaphone',
                                    'task' => 'check-square',
                                    'overdue' => 'alert-circle',
                                    'deadline' => 'clock',
                                    'complete' => 'check-circle',
                                ];
                                $icon = $iconMap[$n['type']] ?? 'bell';
                            @endphp
                            <form method="POST" action="{{ route('employee.notifications.open', $n['id']) }}" class="notif-form">
                                @csrf
                                <button type="submit"
                                    class="notif-item {{ !$n['read'] ? 'unread' : '' }}"
                                    data-type="{{ $n['type'] }}"
                                    data-read="{{ $n['read'] ? 'true' : 'false' }}">
                                    <span class="notif-icon {{ $n['type'] }}">
                                        <i data-lucide="{{ $icon }}" style="width:20px;height:20px"></i>
                                    </span>
                                    <span>
                                        <span class="notif-title">{{ $n['title'] }}</span>
                                        <span class="notif-desc">{{ $n['desc'] }}</span>
                                        <span class="notif-meta">
                                            <i data-lucide="clock-3" style="width:13px;height:13px"></i>
                                            {{ $n['time'] }}
                                        </span>
                                    </span>
                                    <span class="{{ !$n['read'] ? 'unread-dot' : '' }}"></span>
                                </button>
                            </form>
                        @empty
                            <div class="empty-state">
                                <div class="empty-icon"><i data-lucide="bell-off" style="width:28px;height:28px"></i></div>
                                <strong>Chưa có thông báo nào</strong>
                                <span>Khi có giao việc hoặc nhắc deadline, thông báo sẽ xuất hiện tại đây.</span>
                            </div>
                        @endforelse

                        <div id="filter-empty" class="empty-state hidden">
                            <div class="empty-icon"><i data-lucide="search-x" style="width:28px;height:28px"></i></div>
                            <strong id="filter-empty-title">Không có thông báo phù hợp</strong>
                            <span id="filter-empty-desc">Thử chọn tab khác để xem thêm thông báo.</span>
                        </div>
                    </div>
                </div>

                <aside class="side-panel">
                    <div class="info-card">
                        <h2>Trạng thái hộp thư</h2>
                        <div class="metric-grid">
                            <div class="metric"><span>Chưa đọc</span><strong>{{ $unreadCount }}</strong></div>
                            <div class="metric"><span>Tổng số</span><strong>{{ $totalNotifications }}</strong></div>
                            <div class="metric"><span>Công việc</span><strong>{{ $taskCount }}</strong></div>
                            <div class="metric"><span>Deadline</span><strong>{{ $deadlineCount }}</strong></div>
                        </div>
                    </div>

                    <div class="info-card">
                        <h2>Lối tắt</h2>
                        <a href="{{ route('employee.tasks') }}" class="quick-link">
                            <span>Mở công việc của tôi</span>
                            <i data-lucide="arrow-right" style="width:18px;height:18px"></i>
                        </a>
                        <a href="{{ route('employee.dashboard') }}" class="quick-link">
                            <span>Quay về dashboard</span>
                            <i data-lucide="arrow-right" style="width:18px;height:18px"></i>
                        </a>
                    </div>
                </aside>
            </section>
        </section>
    </main>
</div>

<script>
    lucide.createIcons();

    const emptyState = document.getElementById('filter-empty');
    const emptyTitle = document.getElementById('filter-empty-title');
    const emptyDesc = document.getElementById('filter-empty-desc');
    const emptyCopy = {
        all: ['Chưa có thông báo nào', 'Các thông báo mới sẽ xuất hiện tại đây.'],
        unread: ['Không còn thông báo chưa đọc', 'Bạn đã xử lý xong các thông báo mới.'],
        task: ['Chưa có thông báo công việc', 'Khi có giao việc mới, thông báo sẽ xuất hiện trong tab này.'],
        deadline: ['Chưa có nhắc deadline', 'Các việc sắp hết hạn hoặc quá hạn sẽ được nhắc tại đây.'],
    };

    document.querySelectorAll('[data-tab]').forEach((button) => {
        button.addEventListener('click', () => {
            const tab = button.dataset.tab;
            let visibleCount = 0;

            document.querySelectorAll('[data-tab]').forEach((item) => item.classList.remove('active'));
            button.classList.add('active');

            document.querySelectorAll('.notif-form').forEach((form) => {
                const item = form.querySelector('.notif-item');
                let visible = tab === 'all';

                if (tab === 'unread') {
                    visible = item.dataset.read === 'false';
                } else if (tab !== 'all') {
                    visible = item.dataset.type === tab;
                }

                form.style.display = visible ? 'block' : 'none';
                visibleCount += visible ? 1 : 0;
            });

            if (emptyState) {
                const copy = emptyCopy[tab] || emptyCopy.all;
                emptyTitle.innerText = copy[0];
                emptyDesc.innerText = copy[1];
                emptyState.classList.toggle('hidden', visibleCount !== 0);
            }
        });
    });
</script>
</body>
</html>
