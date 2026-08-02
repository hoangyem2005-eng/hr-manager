<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Công việc của tôi - MobiFone Employee</title>
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
        button, input, select, textarea { font: inherit; }
        .layout { min-height: 100vh; display: grid; grid-template-columns: var(--sidebar-w) 1fr; }
        .sidebar { position: sticky; top: 0; height: 100vh; display: flex; flex-direction: column; overflow-y: auto; color: #fff; background: linear-gradient(180deg, #001F5B 0%, #003DA5 100%); }
        .brand { display: flex; align-items: center; gap: 12px; padding: 22px 20px 18px; border-bottom: 1px solid rgba(255,255,255,.12); }
        .brand-mark { width: 38px; height: 38px; border-radius: 8px; display: grid; place-items: center; background: #fff; color: var(--mf-blue); font-weight: 900; box-shadow: inset 5px 0 0 var(--mf-red); }
        .brand-word { display: inline-flex; align-items: baseline; background: #fff; border-radius: 7px; padding: 4px 9px; line-height: 1; box-shadow: 0 6px 18px rgba(0,0,0,.12); }
        .brand-word .blue { color: var(--mf-blue); font-size: 17px; font-weight: 900; letter-spacing: -.03em; }
        .brand-word .red { color: var(--mf-red); font-size: 17px; font-weight: 900; letter-spacing: -.03em; }
        .brand-sub { margin-top: 6px; color: #BFD8FF; font-size: 10px; letter-spacing: .12em; text-transform: uppercase; font-weight: 700; }
        .profile-card { display:block; color:inherit; text-decoration:none; margin: 18px 14px; padding: 16px; border: 1px solid rgba(255,255,255,.14); border-radius: 12px; background: rgba(255,255,255,.08); }
        .profile-card:hover { background: rgba(255,255,255,.13); }
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
        .hero { position: relative; overflow: hidden; display: flex; justify-content: space-between; gap: 18px; padding: 28px; border: 1px solid #B9CDF5; border-radius: 8px; background: linear-gradient(135deg, #F7FAFF 0%, #EEF5FF 60%, #FFF7FA 100%); box-shadow: inset 4px 0 0 var(--mf-red); }
        .hero::after { content: ''; position: absolute; right: 42px; top: 18px; width: 140px; height: 140px; border-radius: 999px; border: 28px solid rgba(0,61,165,.06); }
        .hero-main { position: relative; z-index: 1; display: flex; gap: 16px; align-items: flex-start; }
        .hero-icon { width: 50px; height: 50px; display: grid; place-items: center; border: 1px solid #B9CDF5; border-radius: 8px; background: #fff; color: var(--mf-blue); }
        .eyebrow { color: var(--mf-blue); font-size: 12px; letter-spacing: .1em; text-transform: uppercase; font-weight: 900; }
        h1 { margin-top: 4px; color: var(--mf-blue-dark); font-size: 34px; line-height: 1.1; letter-spacing: -.03em; font-weight: 900; }
        .hero p { margin-top: 8px; color: #40516B; font-size: 15px; line-height: 1.6; }
        .primary-btn { position: relative; z-index: 1; height: 44px; display: inline-flex; align-items: center; justify-content: center; gap: 9px; padding: 0 18px; border: 0; border-radius: 8px; background: var(--mf-blue); color: #fff; font-weight: 900; cursor: pointer; box-shadow: 0 14px 28px rgba(0,61,165,.18); }
        .toolbar { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; }
        .segmented, .pills { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; }
        .segmented { gap: 0; overflow: hidden; border: 1px solid #D8E2F4; border-radius: 8px; background: #fff; }
        .segmented a { display: inline-flex; align-items: center; gap: 7px; padding: 10px 14px; font-weight: 900; color: #334155; }
        .segmented a.active { background: linear-gradient(135deg, #001F5B, #003DA5); color: #fff; }
        .pill { padding: 9px 16px; border: 1px solid #D8E2F4; border-radius: 999px; background: #fff; color: #334155; font-size: 13px; font-weight: 900; }
        .pill.active { background: var(--mf-blue); border-color: var(--mf-blue); color: #fff; }
        .board { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
        .column { min-height: 430px; display: flex; flex-direction: column; overflow: hidden; border: 1px solid #B9CDF5; border-radius: 8px; background: #F8FAFF; }
        .column-head { display: flex; align-items: center; justify-content: space-between; padding: 14px 16px; border-bottom: 1px solid #D8E2F4; background: rgba(255,255,255,.82); font-weight: 900; }
        .column-head .count { min-width: 28px; height: 24px; display: grid; place-items: center; border-radius: 999px; color: #fff; font-size: 12px; background: var(--mf-blue); }
        .column-body { padding: 14px; display: flex; flex-direction: column; gap: 14px; }
        .empty { min-height: 96px; display: grid; place-items: center; border: 1px dashed #B9CDF5; border-radius: 8px; color: #94A3B8; font-size: 13px; font-weight: 700; background: rgba(255,255,255,.55); }
        .task-card { border: 1px solid #D4E0F7; border-radius: 8px; padding: 14px; background: #fff; box-shadow: 0 16px 30px rgba(0,31,91,.06); }
        .task-top { display: flex; align-items: center; justify-content: space-between; gap: 10px; }
        .priority { padding: 5px 10px; border-radius: 999px; background: #FFF3CD; color: #B45309; font-size: 11px; font-weight: 900; }
        .code { font-size: 10px; color: #94A3B8; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-weight: 800; }
        .task-title { margin-top: 12px; color: var(--mf-blue-dark); font-size: 15px; font-weight: 900; line-height: 1.35; }
        .task-desc { margin-top: 6px; color: var(--muted); font-size: 13px; line-height: 1.45; }
        .progress-row { margin-top: 14px; }
        .progress-meta { display: flex; align-items: center; justify-content: space-between; color: #94A3B8; font-size: 11px; font-weight: 800; }
        .progress-bar { height: 7px; margin-top: 7px; border-radius: 99px; background: #EEF2F7; overflow: hidden; }
        .progress-fill { height: 100%; border-radius: inherit; background: linear-gradient(90deg, var(--mf-red), var(--mf-blue)); }
        .task-foot { margin-top: 13px; display: flex; align-items: center; justify-content: space-between; gap: 10px; color: #94A3B8; font-size: 12px; font-weight: 700; }
        .task-actions { margin-top: 14px; display: grid; grid-template-columns: 1fr auto; gap: 9px; }
        .status-select { min-width: 0; border: 1px solid #B9CDF5; border-radius: 8px; padding: 10px 12px; background: #F8FAFF; color: var(--mf-blue-dark); font-size: 13px; font-weight: 900; outline: none; }
        .detail-btn { border: 1px solid #B9CDF5; border-radius: 8px; padding: 10px 13px; background: #fff; color: var(--mf-blue); font-size: 13px; font-weight: 900; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; }
        .list-table { overflow: hidden; border: 1px solid #D8E2F4; border-radius: 8px; background: #fff; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 14px; text-align: left; border-bottom: 1px solid #EEF2F7; }
        th { background: #F4F8FF; color: #64748B; font-size: 11px; text-transform: uppercase; letter-spacing: .08em; }
        td { font-size: 13px; color: #334155; }
        .modal { position: fixed; inset: 0; z-index: 50; display: none; align-items: center; justify-content: center; padding: 18px; background: rgba(0,20,60,.48); }
        .modal.open { display: flex; }
        .modal-card { width: min(680px, 100%); max-height: 90vh; overflow: auto; border-radius: 10px; background: #fff; box-shadow: 0 24px 70px rgba(0,31,91,.28); }
        .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 18px 20px; border-bottom: 1px solid #E5EAF5; }
        .modal-title { color: var(--mf-blue-dark); font-size: 18px; font-weight: 900; }
        .modal-body { padding: 20px; display: grid; gap: 14px; }
        .close-btn { width: 34px; height: 34px; border: 0; border-radius: 8px; background: #F1F5F9; color: #64748B; cursor: pointer; }
        .field label { display: block; margin-bottom: 6px; color: #64748B; font-size: 12px; font-weight: 900; }
        .field input, .field textarea, .field select { width: 100%; border: 1px solid #D8E2F4; border-radius: 8px; padding: 12px; outline: none; }
        .field textarea { min-height: 110px; resize: vertical; }
        .modal-actions { display: flex; justify-content: flex-end; gap: 10px; padding: 0 20px 20px; }
        .ghost-btn { border: 1px solid #D8E2F4; border-radius: 8px; padding: 11px 16px; background: #fff; color: #334155; font-weight: 900; cursor: pointer; }
        .doc-list { display: grid; gap: 8px; }
        .doc-row { display: flex; align-items: center; justify-content: space-between; gap: 10px; border: 1px solid #E5EAF5; border-radius: 8px; padding: 10px; }
        .doc-actions { display: flex; gap: 8px; align-items: center; }
        .doc-actions a, .doc-actions button { border-radius: 8px; padding: 7px 10px; background: #F4F8FF; color: var(--mf-blue); font-size: 12px; font-weight: 900; border: 0; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; line-height: 1; }
        .doc-actions button.del { background: #FEF2F2; color: #DC2626; }
        .doc-actions button.del:hover { background: #FEE2E2; }
        .upload-panel { border: 1px solid #D8E2F4; border-radius: 8px; padding: 14px; background: #F8FAFF; }
        .upload-label { display: flex; align-items: center; justify-content: center; gap: 9px; min-height: 78px; border: 1px dashed #9DBBF2; border-radius: 8px; color: var(--mf-blue); background: #fff; font-weight: 900; cursor: pointer; }
        .upload-label:hover { background: #F4F8FF; border-color: var(--mf-blue); }
        .upload-label input { position: absolute; width: 1px; height: 1px; opacity: 0; pointer-events: none; }
        .upload-selected { margin-top: 10px; display: none; gap: 8px; align-items: center; color: #40516B; font-size: 12px; font-weight: 800; }
        .upload-selected.open { display: flex; }
        .upload-actions { margin-top: 12px; display: flex; justify-content: flex-end; }
        .upload-submit { border: 0; border-radius: 8px; padding: 10px 14px; background: var(--mf-blue); color: #fff; font-weight: 900; cursor: pointer; display: inline-flex; align-items: center; gap: 7px; }
        .upload-submit:disabled { opacity: .45; cursor: not-allowed; }
        @media (max-width: 1200px) { .board { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
        @media (max-width: 900px) { .layout { grid-template-columns: 1fr; } .sidebar { height: auto; position: static; } .hero { flex-direction: column; } .board { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
@php
    $user = Auth::user();
    $totalTasks = count($mappedTasksList);
    $doneTasks = collect($mappedTasksList)->where('status', 'Hoàn thành')->count();
    $doingTasks = collect($mappedTasksList)->whereIn('status', ['Đang làm', 'Đang review'])->count();
    $overdueTasks = collect($mappedTasksList)->where('status', 'Quá hạn')->count();
    $completionRate = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;
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

        <a href="{{ route('profile.show') }}" class="profile-card" title="Trang cá nhân">
            <div class="profile-row">
                <div class="avatar">{{ mb_strtoupper(mb_substr($user->name ?? 'NV', 0, 2)) }}</div>
                <div>
                    <div class="profile-name">{{ $user->name }}</div>
                    <div class="profile-meta">{{ $user->email }}</div>
                </div>
            </div>
            <div class="profile-dept"><i data-lucide="building-2" style="width:15px;height:15px"></i>{{ $user->department->TENPHONG ?? 'MobiFone' }}</div>
        </a>

        <div class="sidebar-stats">
            <div class="stat-box"><div class="stat-label">Tổng việc</div><div class="stat-val">{{ $totalTasks }}</div></div>
            <div class="stat-box"><div class="stat-label">Đang làm</div><div class="stat-val">{{ $doingTasks }}</div></div>
            <div class="stat-box"><div class="stat-label">Hoàn thành</div><div class="stat-val green">{{ $doneTasks }}</div></div>
            <div class="stat-box"><div class="stat-label">Quá hạn</div><div class="stat-val red">{{ $overdueTasks }}</div></div>
        </div>

        <nav class="sidebar-nav">
            <a href="{{ route('employee.dashboard') }}" class="nav-item">
                <i data-lucide="layout-dashboard" style="width:16px;height:16px"></i>Dashboard
            </a>
            <a href="{{ route('employee.tasks') }}" class="nav-item active">
                <i data-lucide="clipboard-list" style="width:16px;height:16px"></i>Công việc của tôi
                @if($totalTasks > 0)<span class="nav-badge">{{ $totalTasks }}</span>@endif
            </a>
            <a href="{{ route('employee.notifications') }}" class="nav-item">
                <i data-lucide="bell" style="width:16px;height:16px"></i>Thông báo
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
            <div class="topbar-title">Công việc của tôi</div>
            <div class="topbar-actions">
                <a href="{{ route('employee.notifications') }}" class="icon-btn" title="Thông báo"><i data-lucide="bell" style="width:20px;height:20px"></i></a>
                <span class="icon-btn" title="{{ $user->name }}">{{ mb_strtoupper(mb_substr($user->name ?? 'NV', 0, 2)) }}</span>
            </div>
        </header>

        <section class="content">
            @if(session('success'))
                <div style="border:1px solid #BBF7D0;background:#F0FDF4;color:#166534;border-radius:8px;padding:12px 14px;font-weight:800">{{ session('success') }}</div>
            @endif

            <section class="hero">
                <div class="hero-main">
                    <div class="hero-icon"><i data-lucide="clipboard-plus" style="width:24px;height:24px"></i></div>
                    <div>
                        <div class="eyebrow">My Workbench</div>
                        <h1>Việc của tôi</h1>
                        <p>Theo dõi việc được giao, cập nhật trạng thái và gửi đề xuất công việc cho quản lý.</p>
                    </div>
                </div>
                <button type="button" class="primary-btn" data-open-create>
                    <i data-lucide="plus" style="width:18px;height:18px"></i>Gửi đề xuất việc
                </button>
            </section>

            <section class="toolbar">
                <div class="segmented">
                    <a href="{{ route('employee.tasks', ['view' => 'kanban', 'filter' => $filter]) }}" class="{{ $viewType === 'kanban' ? 'active' : '' }}"><i data-lucide="columns-3" style="width:16px;height:16px"></i>Kanban</a>
                    <a href="{{ route('employee.tasks', ['view' => 'list', 'filter' => $filter]) }}" class="{{ $viewType === 'list' ? 'active' : '' }}"><i data-lucide="list" style="width:16px;height:16px"></i>Danh sách</a>
                </div>
                <div class="pills">
                    @foreach(['Tất cả', 'Của tôi', 'Quá hạn'] as $f)
                        <a class="pill {{ $filter === $f ? 'active' : '' }}" href="{{ route('employee.tasks', ['view' => $viewType, 'filter' => $f]) }}">{{ $f }}</a>
                    @endforeach
                </div>
            </section>

            @if($viewType === 'list')
                <section class="list-table">
                    <table>
                        <thead>
                            <tr>
                                <th>Mã</th>
                                <th>Công việc</th>
                                <th>Hạn</th>
                                <th>Tiến độ</th>
                                <th>Trạng thái</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mappedTasksList as $task)
                                <tr>
                                    <td class="code">{{ $task['code'] }}</td>
                                    <td><strong style="color:var(--mf-blue-dark)">{{ $task['name'] }}</strong><br><span style="color:#64748B">{{ $task['description'] ?: 'Chưa có mô tả.' }}</span></td>
                                    <td>{{ $task['deadline'] }}</td>
                                    <td>{{ $task['progress'] }}%</td>
                                    <td>
                                        <select class="status-select" data-task-status="{{ $task['id'] }}" data-previous-status="{{ $task['status'] }}" onchange="updateEmployeeTaskStatus({{ $task['id'] }}, this.value)" {{ ($task['status'] === 'Hoàn thành') ? 'disabled' : '' }}>
                                            <option value="Chờ xử lý" {{ $task['status'] === 'Chờ xử lý' ? 'selected' : '' }}>Chờ xử lý</option>
                                            <option value="Đang làm" {{ $task['status'] === 'Đang làm' ? 'selected' : '' }}>Đang làm</option>
                                            <option value="Đang review" {{ $task['status'] === 'Đang review' ? 'selected' : '' }}>Đang review</option>
                                            @if($task['status'] === 'Hoàn thành')
                                                <option value="Hoàn thành" selected disabled>Hoàn thành</option>
                                            @endif
                                        </select>
                                    </td>
                                    <td><button type="button" class="detail-btn" onclick='openTaskDetail(@json($task))'><i data-lucide="eye" style="width:15px;height:15px"></i>Chi tiết</button></td>
                                </tr>
                            @empty
                                <tr><td colspan="6" style="text-align:center;color:#94A3B8;padding:34px">Chưa có công việc.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </section>
            @else
                <section class="board">
                    @foreach($cols as $key => $col)
                        <div class="column">
                            <div class="column-head">
                                <span>{{ $col['label'] }}</span>
                                <span class="count">{{ count($col['tasks']) }}</span>
                            </div>
                            <div class="column-body">
                                @forelse($col['tasks'] as $task)
                                    <article class="task-card">
                                        <div class="task-top">
                                            <span class="priority">{{ $task['priority'] }}</span>
                                            <span class="code">{{ $task['code'] }}</span>
                                        </div>
                                        <h3 class="task-title">{{ $task['name'] }}</h3>
                                        <p class="task-desc">{{ $task['description'] ?: 'Chưa có mô tả chi tiết.' }}</p>
                                        <div class="progress-row">
                                            <div class="progress-meta"><span>Tiến độ</span><strong data-progress-text="{{ $task['id'] }}">{{ $task['progress'] }}%</strong></div>
                                            <div class="progress-bar"><div class="progress-fill" data-progress-bar="{{ $task['id'] }}" style="width: {{ $task['progress'] }}%"></div></div>
                                        </div>
                                        <div class="task-foot">
                                            <span style="display:flex;align-items:center;gap:5px"><i data-lucide="calendar" style="width:15px;height:15px"></i>{{ $task['deadline'] }}</span>
                                            <span style="display:flex;align-items:center;gap:8px">
                                                @if(($task['documents_count'] ?? 0) > 0)
                                                    <span style="display:flex;align-items:center;gap:4px"><i data-lucide="paperclip" style="width:15px;height:15px"></i>{{ $task['documents_count'] }}</span>
                                                @endif
                                                <strong style="width:28px;height:28px;border-radius:999px;display:grid;place-items:center;background:var(--mf-blue);color:#fff;font-size:11px">{{ $task['avatar'] }}</strong>
                                            </span>
                                        </div>
                                        <div class="task-actions">
                                             <select class="status-select" data-task-status="{{ $task['id'] }}" data-previous-status="{{ $task['status'] }}" onchange="updateEmployeeTaskStatus({{ $task['id'] }}, this.value)" {{ ($task['status'] === 'Hoàn thành') ? 'disabled' : '' }}>
                                                 <option value="Chờ xử lý" {{ $task['status'] === 'Chờ xử lý' ? 'selected' : '' }}>Chờ xử lý</option>
                                                 <option value="Đang làm" {{ $task['status'] === 'Đang làm' ? 'selected' : '' }}>Đang làm</option>
                                                 <option value="Đang review" {{ $task['status'] === 'Đang review' ? 'selected' : '' }}>Đang review</option>
                                                 @if($task['status'] === 'Hoàn thành')
                                                     <option value="Hoàn thành" selected disabled>Hoàn thành</option>
                                                 @endif
                                             </select>
                                            <button type="button" class="detail-btn" onclick='openTaskDetail(@json($task))'><i data-lucide="eye" style="width:15px;height:15px"></i>Chi tiết</button>
                                        </div>
                                    </article>
                                @empty
                                    <div class="empty">Chưa có công việc.</div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                </section>
            @endif
        </section>
    </main>
</div>

<div class="modal" id="create-modal">
    <div class="modal-card">
        <div class="modal-head">
            <div class="modal-title">Gửi đề xuất công việc</div>
            <button type="button" class="close-btn" data-close-create><i data-lucide="x" style="width:18px;height:18px"></i></button>
        </div>
        <form action="{{ route('employee.tasks.save') }}" method="POST">
            @csrf
            <div class="modal-body">
                <div class="field">
                    <label>Tên công việc *</label>
                    <input type="text" name="task_name" required placeholder="Nhập tên công việc cần đề xuất">
                </div>
                <div class="field">
                    <label>Mô tả</label>
                    <textarea name="description" placeholder="Mô tả ngắn nội dung công việc"></textarea>
                </div>
                <div class="field">
                    <label>Deadline *</label>
                    <input type="date" name="deadline" required>
                </div>
                <input type="hidden" name="status" value="Chờ xử lý">
            </div>
            <div class="modal-actions">
                <button type="button" class="ghost-btn" data-close-create>Hủy</button>
                <button type="submit" class="primary-btn">Gửi đề xuất</button>
            </div>
        </form>
    </div>
</div>

<div class="modal" id="detail-modal">
    <div class="modal-card">
        <div class="modal-head">
            <div>
                <div class="code" id="detail-code"></div>
                <div class="modal-title" id="detail-name"></div>
            </div>
            <button type="button" class="close-btn" data-close-detail><i data-lucide="x" style="width:18px;height:18px"></i></button>
        </div>
        <div class="modal-body">
            <p id="detail-desc" style="padding:14px;border-radius:8px;background:#F8FAFF;color:#40516B;line-height:1.6"></p>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                <div class="field"><label>Deadline</label><div id="detail-deadline"></div></div>
                <div class="field"><label>Tiến độ</label><div id="detail-progress"></div></div>
            </div>
            <div>
                <div style="font-size:12px;font-weight:900;color:#64748B;margin-bottom:8px">Tài liệu đính kèm</div>
                <div class="doc-list" id="detail-documents"></div>
            </div>
            <form method="POST" action="#" enctype="multipart/form-data" id="detail-upload-form" class="upload-panel">
                @csrf
                <input type="hidden" name="redirect_to" value="employee.tasks">
                <div style="font-size:12px;font-weight:900;color:#64748B;margin-bottom:8px">Tải file cho công việc này</div>
                <label class="upload-label" for="detail-attachments">
                    <i data-lucide="upload-cloud" style="width:22px;height:22px"></i>
                    <span>Nhấn để chọn file đính kèm</span>
                    <input id="detail-attachments" name="attachments[]" type="file" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar,.txt">
                </label>
                <div class="upload-selected" id="detail-selected-files">
                    <i data-lucide="paperclip" style="width:15px;height:15px"></i>
                    <span id="detail-selected-text"></span>
                </div>
                <div class="upload-actions">
                    <button class="upload-submit" id="detail-upload-submit" type="submit" disabled>
                        <i data-lucide="upload" style="width:15px;height:15px"></i>
                        Tải lên file
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    lucide.createIcons();

    const createModal = document.getElementById('create-modal');
    const detailModal = document.getElementById('detail-modal');
    const detailUploadForm = document.getElementById('detail-upload-form');
    const detailAttachments = document.getElementById('detail-attachments');
    const detailSelectedFiles = document.getElementById('detail-selected-files');
    const detailSelectedText = document.getElementById('detail-selected-text');
    const detailUploadSubmit = document.getElementById('detail-upload-submit');
    document.querySelectorAll('[data-open-create]').forEach(btn => btn.addEventListener('click', () => createModal.classList.add('open')));
    document.querySelectorAll('[data-close-create]').forEach(btn => btn.addEventListener('click', () => createModal.classList.remove('open')));
    document.querySelectorAll('[data-close-detail]').forEach(btn => btn.addEventListener('click', () => detailModal.classList.remove('open')));

    detailAttachments?.addEventListener('change', () => {
        const files = Array.from(detailAttachments.files || []);
        detailUploadSubmit.disabled = files.length === 0;
        detailSelectedFiles.classList.toggle('open', files.length > 0);
        detailSelectedText.textContent = files.length ? files.map(file => file.name).join(', ') : '';
    });

    const employeeProgressByStatus = {
        'Chờ xử lý': 0,
        'Đang làm': 50,
        'Đang review': 80,
        'Hoàn thành': 100,
    };

    function updateEmployeeTaskStatus(taskId, status) {
        const selects = document.querySelectorAll(`[data-task-status="${taskId}"]`);
        const previous = Array.from(selects).find(select => select.dataset.previousStatus)?.dataset.previousStatus || status;
        const progress = employeeProgressByStatus[status] ?? 0;
        selects.forEach(select => select.disabled = true);

        fetch(`{{ url('/employee/tasks') }}/${taskId}/progress`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ status, progress }),
        })
            .then(response => response.json().then(payload => ({ ok: response.ok, payload })))
            .then(({ ok, payload }) => {
                if (!ok || !payload.ok) throw new Error(payload.message || 'Update failed');
                const nextProgress = payload.progress ?? progress;
                document.querySelectorAll(`[data-progress-bar="${taskId}"]`).forEach(bar => bar.style.width = `${nextProgress}%`);
                document.querySelectorAll(`[data-progress-text="${taskId}"]`).forEach(text => text.textContent = `${nextProgress}%`);
                selects.forEach(select => {
                    select.value = payload.status || status;
                    select.dataset.previousStatus = payload.status || status;
                });
            })
            .catch(() => {
                selects.forEach(select => select.value = select.dataset.previousStatus || previous);
                alert('Không cập nhật được trạng thái công việc. Vui lòng thử lại.');
            })
            .finally(() => selects.forEach(select => select.disabled = false));
    }

    function openTaskDetail(task) {
        document.getElementById('detail-code').textContent = task.code || `WH-${String(task.id).padStart(3, '0')}`;
        document.getElementById('detail-name').textContent = task.name || '';
        document.getElementById('detail-desc').textContent = task.description || 'Không có mô tả chi tiết.';
        document.getElementById('detail-deadline').textContent = task.deadline || 'Không có';
        document.getElementById('detail-progress').textContent = `${task.progress || 0}%`;

        detailUploadForm.action = `{{ url('/employee/tasks') }}/${task.id}/upload`;
        detailUploadForm.reset();
        detailUploadSubmit.disabled = true;
        detailSelectedFiles.classList.remove('open');
        detailSelectedText.textContent = '';

        const docs = document.getElementById('detail-documents');
        docs.innerHTML = '';
        const documents = task.documents || [];
        if (!documents.length) {
            docs.innerHTML = '<div class="empty" style="min-height:70px">Chưa có tài liệu đính kèm.</div>';
        } else {
            documents.forEach(file => {
                const row = document.createElement('div');
                row.className = 'doc-row';
                const canDelete = file.can_delete !== undefined ? file.can_delete : true;
                const deleteBtnHtml = canDelete ? `<button type="button" class="del" onclick="deleteDocumentItem(${file.id}, this)">Xóa</button>` : '';
                row.innerHTML = `
                    <div style="min-width:0">
                        <strong style="display:block;color:var(--mf-blue-dark);white-space:nowrap;overflow:hidden;text-overflow:ellipsis">${file.file_name || 'Tài liệu'}</strong>
                        <span style="font-size:12px;color:#94A3B8">${[file.file_type, file.uploader, file.uploaded_at].filter(Boolean).join(' · ')}</span>
                    </div>
                    <div class="doc-actions">
                        <a href="${file.preview_url}" target="_blank" rel="noopener">Xem</a>
                        <a href="${file.download_url}">Tải</a>
                        ${deleteBtnHtml}
                    </div>
                `;
                docs.appendChild(row);
            });
        }
        detailModal.classList.add('open');
        lucide.createIcons();
    }

    function deleteDocumentItem(documentId, btnElement) {
        if (!confirm('Bạn có chắc chắn muốn xóa tệp này không?')) return;

        fetch(`{{ url('/tai-lieu') }}/${documentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const row = btnElement.closest('.doc-row');
                if (row) {
                    row.remove();
                    const docsContainer = document.getElementById('detail-documents');
                    if (docsContainer && docsContainer.children.length === 0) {
                        docsContainer.innerHTML = '<div class="empty" style="min-height:70px">Chưa có tài liệu đính kèm.</div>';
                    }
                }
                alert(data.message || 'Đã xóa tệp đính kèm.');
            } else {
                alert(data.message || 'Không thể xóa tệp.');
            }
        })
        .catch(err => {
            console.error(err);
            alert('Có lỗi xảy ra khi xóa tệp đính kèm.');
        });
    }
</script>
</body>
</html>
