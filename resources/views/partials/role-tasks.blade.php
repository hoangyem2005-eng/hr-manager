@php
    $isAdminPage = request()->routeIs('admin.*');
    $isProgressPage = !$isAdminPage && request('mode') === 'progress';
    $routePrefix = $isAdminPage ? 'admin' : 'manager';
    $taskRoute = $routePrefix . '.tasks';
    $saveRoute = $routePrefix . '.tasks.save';
    $detailRoute = $isAdminPage ? 'congviec.chitiet' : 'manager.tasks.show';
    $routeParams = fn (array $params = []) => $isProgressPage
        ? array_merge(['mode' => 'progress'], $params)
        : $params;
    $detailParams = fn ($taskId) => $isAdminPage
        ? [$taskId]
        : array_filter([
            'task' => $taskId,
            'from' => $isProgressPage ? 'progress' : null,
        ]);
    $totalTasks = isset($globalMetrics) ? $globalMetrics['total'] : count($mappedTasksList);
    $doneTasks = isset($globalMetrics) ? $globalMetrics['done'] : collect($mappedTasksList)->where('status', 'Hoàn thành')->count();
    $doingTasks = isset($globalMetrics) ? $globalMetrics['doing'] : collect($mappedTasksList)->whereIn('status', ['Đang làm', 'Đang review'])->count();
    $pendingTasks = isset($globalMetrics) ? $globalMetrics['pending'] : collect($mappedTasksList)->whereIn('status', ['Chờ xử lý', 'Todo'])->count();
    $overdueTasks = isset($globalMetrics) ? $globalMetrics['overdue'] : collect($mappedTasksList)->where('status', 'Quá hạn')->count();
    $completionRate = $totalTasks > 0 ? round(($doneTasks / $totalTasks) * 100) : 0;
    $avgProgress = $totalTasks > 0 ? round(collect($mappedTasksList)->avg('progress')) : 0;
    $topProgressTasks = collect($mappedTasksList)->sortByDesc('progress')->take(4)->values();
@endphp

<style>
    .role-page { display:grid; gap:20px; color:#001F5B; }
    .mobifone-panel { border:1px solid #D4E0F7; border-radius:8px; background:#fff; box-shadow:0 14px 34px rgba(0,31,91,.06); }
    .dispatch-hero, .progress-hero { position:relative; overflow:hidden; border-radius:8px; }
    .dispatch-hero { display:grid; grid-template-columns:minmax(0,1fr) 360px; gap:22px; padding:28px; border:1px solid #B9CDF5; background:linear-gradient(135deg,#FFFFFF 0%,#F3F7FF 58%,#FFF3F6 100%); box-shadow:inset 5px 0 0 #E4002B; }
    .dispatch-hero:after { content:""; position:absolute; right:-46px; top:-58px; width:210px; height:210px; border:34px solid rgba(0,61,165,.06); border-radius:999px; }
    .hero-kicker { display:inline-flex; align-items:center; gap:8px; color:#003DA5; font-size:12px; font-weight:900; letter-spacing:.16em; text-transform:uppercase; }
    .hero-kicker.red { color:#E4002B; }
    .dispatch-hero h2, .progress-hero h2 { margin:8px 0 8px; font-size:36px; line-height:1.08; font-weight:900; letter-spacing:-.03em; }
    .dispatch-hero p, .progress-hero p { max-width:760px; color:#52637A; font-size:15px; line-height:1.65; }
    .dispatch-rail { position:relative; z-index:1; display:grid; gap:12px; align-content:start; border:1px solid #C8D8F6; border-radius:8px; padding:16px; background:rgba(255,255,255,.82); }
    .rail-title { display:flex; align-items:center; justify-content:space-between; gap:10px; color:#001F5B; font-weight:900; }
    .rail-people { display:grid; gap:10px; }
    .rail-person { display:grid; grid-template-columns:38px 1fr auto; align-items:center; gap:10px; padding:10px; border:1px solid #E2EAF8; border-radius:8px; background:#F8FBFF; }
    .avatar-chip { width:38px; height:38px; display:grid; place-items:center; border-radius:8px; background:#003DA5; color:#fff; font-size:12px; font-weight:900; box-shadow:inset 4px 0 0 #E4002B; }
    .person-name { font-weight:900; }
    .person-meta { color:#64748B; font-size:12px; font-weight:700; }
    .mini-count { border-radius:999px; padding:5px 9px; background:#E8F0FE; color:#003DA5; font-size:12px; font-weight:900; }
    .progress-hero { display:grid; grid-template-columns:minmax(0,1fr) 390px; gap:22px; padding:28px; border:1px solid #174EA6; background:#002B7A; color:#fff; }
    .progress-hero:before { content:""; position:absolute; inset:0; background-image:linear-gradient(rgba(255,255,255,.07) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.07) 1px,transparent 1px); background-size:54px 54px; opacity:.6; }
    .progress-hero:after { content:""; position:absolute; right:-72px; bottom:-90px; width:280px; height:280px; border:42px solid rgba(255,255,255,.09); border-radius:999px; }
    .progress-hero > * { position:relative; z-index:1; }
    .progress-hero h2 { color:#fff; }
    .progress-hero p { color:#D8E7FF; }
    .control-board { display:grid; gap:12px; }
    .control-card { display:grid; grid-template-columns:1fr auto; gap:12px; align-items:center; padding:14px; border:1px solid rgba(255,255,255,.22); border-radius:8px; background:rgba(255,255,255,.11); }
    .control-card span { color:#BCD1F5; font-size:11px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; }
    .control-card strong { display:block; margin-top:5px; color:#fff; font-size:28px; line-height:1; }
    .control-dot { width:38px; height:38px; display:grid; place-items:center; border-radius:8px; background:#fff; color:#003DA5; }
    .metric-strip { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:12px; }
    .metric-card { padding:16px; border:1px solid #D4E0F7; border-radius:8px; background:#fff; }
    .metric-card span { display:block; color:#6B7890; font-size:11px; font-weight:900; letter-spacing:.09em; text-transform:uppercase; }
    .metric-card strong { display:block; margin-top:8px; color:#003DA5; font-size:30px; line-height:1; font-weight:900; }
    .metric-card.alert { border-color:#FFB7C3; background:#FFF6F8; }
    .metric-card.alert strong { color:#E4002B; }
    .project-map { display:grid; gap:14px; }
    .project-map-head { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:16px 18px; border-bottom:1px solid #E5EDF8; }
    .project-map-title { display:flex; align-items:center; gap:10px; color:#001F5B; font-size:18px; font-weight:900; }
    .project-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:14px; padding:16px; }
    .project-card { overflow:hidden; border:1px solid #D4E0F7; border-radius:8px; background:linear-gradient(180deg,#fff 0%,#F8FBFF 100%); box-shadow:0 14px 28px rgba(0,31,91,.06); }
    .project-card-top { padding:15px; border-bottom:1px solid #E7EEF9; background:linear-gradient(135deg,#001F5B,#003DA5); color:#fff; }
    .project-code { display:inline-flex; align-items:center; gap:6px; border-radius:999px; padding:5px 9px; background:rgba(255,255,255,.14); font-size:11px; font-weight:900; letter-spacing:.08em; }
    .project-name { margin-top:10px; font-size:16px; line-height:1.35; font-weight:900; }
    .project-summary { margin-top:10px; display:flex; gap:8px; flex-wrap:wrap; color:#D8E7FF; font-size:11px; font-weight:800; }
    .project-progress { margin-top:12px; height:7px; overflow:hidden; border-radius:999px; background:rgba(255,255,255,.22); }
    .project-progress span { display:block; height:100%; border-radius:inherit; background:linear-gradient(90deg,#E4002B,#fff); }
    .subtask-list { display:grid; align-content:start; gap:10px; padding:12px; max-height:290px; overflow-y:auto; scrollbar-width:thin; scrollbar-color:#B9CDF5 transparent; }
    .subtask-list::-webkit-scrollbar { width:8px; }
    .subtask-list::-webkit-scrollbar-thumb { background:#B9CDF5; border-radius:999px; }
    .subtask-item { display:grid; gap:9px; padding:12px; border:1px solid #E2EAF8; border-radius:8px; background:#fff; }
    .subtask-main { display:flex; justify-content:space-between; gap:10px; align-items:flex-start; }
    .subtask-title { color:#001F5B; font-size:13px; font-weight:900; line-height:1.35; }
    .subtask-step { flex:0 0 auto; border-radius:999px; padding:4px 8px; background:#FFF1F4; color:#E4002B; font-size:10px; font-weight:900; }
    .people-line { display:grid; gap:5px; color:#52637A; font-size:11px; line-height:1.45; }
    .people-line b { color:#003DA5; }
    .task-people { margin-top:10px; display:grid; gap:5px; padding:9px; border-radius:8px; background:#F8FBFF; color:#52637A; font-size:11px; line-height:1.45; }
    .task-people b { color:#003DA5; }
    .action-row { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; }
    .left-tools, .filters { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }
    .segmented { display:inline-flex; overflow:hidden; border:1px solid #D4E0F7; border-radius:8px; background:#fff; }
    .segmented a { display:inline-flex; align-items:center; gap:8px; padding:10px 15px; color:#334155; font-weight:900; text-decoration:none; }
    .segmented a.active { background:#003DA5; color:#fff; }
    .progress-page .segmented a.active { background:#E4002B; color:#fff; }
    .filter-pill { border:1px solid #D4E0F7; border-radius:999px; padding:9px 16px; background:#fff; color:#334155; font-size:13px; font-weight:900; text-decoration:none; }
    .filter-pill.active { border-color:#003DA5; background:#003DA5; color:#fff; }
    .progress-page .filter-pill.active { border-color:#E4002B; background:#E4002B; color:#fff; }
    .primary-action { position:relative; z-index:1; display:inline-flex; align-items:center; justify-content:center; gap:9px; min-height:46px; border:0; border-radius:8px; padding:0 18px; background:#003DA5; color:#fff; font-weight:900; cursor:pointer; box-shadow:0 16px 28px rgba(0,61,165,.18); text-decoration:none; }
    .primary-action.red { background:#E4002B; box-shadow:0 16px 28px rgba(228,0,43,.18); }
    .dispatch-layout { display:grid; grid-template-columns:340px minmax(0,1fr); gap:16px; align-items:start; }
    .assign-card { padding:18px; }
    .assign-title { display:flex; align-items:center; gap:10px; margin-bottom:14px; font-size:18px; font-weight:900; }
    .assign-steps { display:grid; gap:10px; }
    .assign-step { display:grid; grid-template-columns:32px 1fr; gap:10px; align-items:start; padding:12px; border:1px solid #E2EAF8; border-radius:8px; background:#F8FBFF; }
    .step-number { width:32px; height:32px; display:grid; place-items:center; border-radius:8px; background:#003DA5; color:#fff; font-weight:900; }
    .step-title { font-weight:900; }
    .step-copy { margin-top:3px; color:#64748B; font-size:12px; line-height:1.45; }
    .board-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; align-items:start; }
    .work-column { height:min(560px,calc(100vh - 300px)); min-height:430px; overflow:hidden; display:flex; flex-direction:column; border:1px solid #BFD1F5; border-top:4px solid #003DA5; border-radius:8px; background:#F8FAFF; }
    .work-column:nth-child(1) { border-top-color:#64748B; }
    .work-column:nth-child(2) { border-top-color:#E4002B; }
    .work-column:nth-child(3) { border-top-color:#2563EB; }
    .work-column:nth-child(4) { border-top-color:#001F5B; }
    .column-head { display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid #E4ECFA; background:#fff; color:#001F5B; font-weight:900; }
    .count-badge { min-width:27px; height:24px; display:grid; place-items:center; border-radius:999px; background:#003DA5; color:#fff; font-size:12px; }
    .column-body { display:grid; align-content:start; gap:10px; padding:12px; overflow-y:auto; min-height:0; scrollbar-width:thin; scrollbar-color:#B9CDF5 transparent; }
    .column-body::-webkit-scrollbar { width:8px; }
    .column-body::-webkit-scrollbar-thumb { background:#B9CDF5; border-radius:999px; }
    .empty-card { min-height:110px; display:grid; place-items:center; border:1px dashed #BFD1F5; border-radius:8px; color:#94A3B8; background:rgba(255,255,255,.62); font-weight:700; }
    .task-card { border:1px solid #D4E0F7; border-radius:8px; padding:12px; background:#fff; box-shadow:0 10px 22px rgba(0,31,91,.06); }
    .task-top { display:flex; justify-content:space-between; gap:10px; align-items:center; }
    .priority-chip { border-radius:999px; padding:5px 10px; background:#FFF3CD; color:#B45309; font-size:11px; font-weight:900; }
    .task-code { color:#94A3B8; font-size:10px; font-weight:900; font-family:ui-monospace,SFMono-Regular,Menlo,monospace; }
    .task-name { margin-top:9px; color:#001F5B; font-size:14px; font-weight:900; line-height:1.3; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .task-desc { margin-top:5px; color:#64748B; font-size:11px; line-height:1.4; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; overflow:hidden; }
    .progress-meta { margin-top:10px; display:flex; justify-content:space-between; color:#64748B; font-size:11px; font-weight:900; }
    .progress-track { margin-top:7px; height:7px; overflow:hidden; border-radius:999px; background:#E9EEF8; }
    .progress-fill { height:100%; border-radius:inherit; background:linear-gradient(90deg,#E4002B,#003DA5); }
    .task-foot { margin-top:10px; display:flex; justify-content:space-between; gap:10px; align-items:center; color:#64748B; font-size:11px; font-weight:800; }
    .detail-link { display:inline-flex; align-items:center; gap:7px; border:1px solid #B9CDF5; border-radius:8px; padding:9px 12px; color:#003DA5; background:#fff; font-size:12px; font-weight:900; text-decoration:none; }
    .progress-shell { display:grid; grid-template-columns:minmax(0,1.35fr) minmax(320px,.65fr); gap:16px; align-items:start; }
    .progress-table-box { overflow:hidden; max-height:min(640px,calc(100vh - 260px)); display:flex; flex-direction:column; }
    .progress-table-box > div:last-child { overflow:auto; min-height:0; scrollbar-width:thin; scrollbar-color:#B9CDF5 transparent; }
    .section-head { display:flex; align-items:center; justify-content:space-between; gap:12px; padding:16px 18px; border-bottom:1px solid #E5EDF8; }
    .section-title { display:flex; align-items:center; gap:10px; font-size:18px; font-weight:900; }
    .role-table { width:100%; border-collapse:collapse; }
    .role-table th, .role-table td { padding:14px 16px; border-bottom:1px solid #EEF2F7; text-align:left; }
    .role-table th { background:#F4F8FF; color:#64748B; font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:.08em; }
    .role-table td { color:#334155; }
    .row-progress { min-width:160px; }
    .progress-percent { display:flex; justify-content:space-between; gap:8px; margin-bottom:7px; color:#001F5B; font-size:12px; font-weight:900; }
    .focus-list { display:grid; gap:12px; padding:16px; }
    .focus-card { border:1px solid #D4E0F7; border-radius:8px; padding:13px; background:#F8FBFF; }
    .focus-line { display:flex; justify-content:space-between; gap:12px; align-items:center; }
    .focus-name { color:#001F5B; font-weight:900; }
    .focus-meta { margin-top:6px; color:#64748B; font-size:12px; }
    .list-box { overflow:auto; max-height:min(640px,calc(100vh - 260px)); border:1px solid #D4E0F7; border-radius:8px; background:#fff; scrollbar-width:thin; scrollbar-color:#B9CDF5 transparent; }
    .list-box::-webkit-scrollbar, .progress-table-box > div:last-child::-webkit-scrollbar { width:8px; height:8px; }
    .list-box::-webkit-scrollbar-thumb, .progress-table-box > div:last-child::-webkit-scrollbar-thumb { background:#B9CDF5; border-radius:999px; }
    .modal-backdrop { position:fixed; inset:0; z-index:100; display:none; align-items:center; justify-content:center; padding:18px; background:rgba(0,20,60,.46); overflow:hidden; }
    .modal-backdrop.open { display:flex; }
    .task-modal { width:min(680px,100%); max-height:calc(100vh - 36px); display:flex; flex-direction:column; overflow:hidden; border-radius:10px; background:#fff; box-shadow:0 26px 70px rgba(0,31,91,.28); }
    .task-modal form { display:flex; flex-direction:column; flex:1; min-height:0; overflow:hidden; }
    .modal-head { display:flex; justify-content:space-between; align-items:center; padding:18px 20px; border-bottom:1px solid #E5EAF5; color:#001F5B; font-weight:900; flex-shrink:0; }
    .modal-body { display:grid; gap:14px; padding:20px; overflow-y:auto; flex:1; min-height:0; }
    .field label { display:block; margin-bottom:6px; color:#64748B; font-size:12px; font-weight:900; }
    .field input, .field textarea, .field select { width:100%; border:1px solid #D4E0F7; border-radius:8px; padding:12px; outline:none; }
    .field textarea { min-height:110px; resize:vertical; }
    .assignee-check-grid { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:10px; max-height:230px; overflow:auto; padding:10px; border:1px solid #D4E0F7; border-radius:8px; background:#F8FBFF; }
    .assignee-check { display:flex; align-items:center; gap:10px; padding:10px; border:1px solid #E2EAF8; border-radius:8px; background:#fff; cursor:pointer; font-weight:900; color:#001F5B; }
    .assignee-check input { width:16px; height:16px; accent-color:#003DA5; }
    .assignee-check span { display:block; }
    .assignee-check small { display:block; margin-top:2px; color:#64748B; font-size:11px; font-weight:700; }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .ghost-btn { border:1px solid #D4E0F7; border-radius:8px; padding:11px 16px; background:#fff; color:#334155; font-weight:900; cursor:pointer; }
    .modal-actions { display:flex; justify-content:flex-end; gap:10px; padding:20px; border-top:1px solid #E5EAF5; flex-shrink:0; }
    @media (max-width:1200px) { .dispatch-hero, .progress-hero, .dispatch-layout, .progress-shell { grid-template-columns:1fr; } .board-grid, .metric-strip, .project-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width:760px) { .board-grid, .metric-strip, .project-grid, .form-grid { grid-template-columns:1fr; } .role-table { min-width:760px; } .dispatch-hero, .progress-hero { padding:20px; } .dispatch-hero h2, .progress-hero h2 { font-size:30px; } }
</style>

<div class="role-page {{ $isProgressPage ? 'progress-page' : 'dispatch-page' }}">
    @if($isProgressPage)
        <section class="progress-hero">
            <div>
                <div class="hero-kicker red"><i data-lucide="activity" style="width:16px;height:16px"></i> Theo dõi tiến độ</div>
                <h2>Tiến độ công việc trong phòng</h2>
                <p>Theo dõi tốc độ xử lý, trạng thái review, file đính kèm và các việc cần nhắc ngay trong không gian trưởng phòng.</p>
            </div>
            <div class="control-board">
                <div class="control-card"><div><span>Tỷ lệ hoàn thành</span><strong>{{ $completionRate }}%</strong></div><div class="control-dot"><i data-lucide="target" style="width:20px;height:20px"></i></div></div>
                <div class="control-card"><div><span>Tiến độ trung bình</span><strong>{{ $avgProgress }}%</strong></div><div class="control-dot"><i data-lucide="gauge" style="width:20px;height:20px"></i></div></div>
                <div class="control-card"><div><span>Việc cần chú ý</span><strong>{{ $overdueTasks + $pendingTasks }}</strong></div><div class="control-dot"><i data-lucide="bell-ring" style="width:20px;height:20px"></i></div></div>
            </div>
        </section>
    @else
        <section class="dispatch-hero">
            <div>
                <div class="hero-kicker"><i data-lucide="send" style="width:16px;height:16px"></i>{{ $isAdminPage ? 'Điều hành công việc cấp công ty' : 'Bàn giao việc trong phòng' }}</div>
                <h2>{{ $isAdminPage ? 'Giao việc toàn công ty' : 'Bàn giao việc cho đội nhóm' }}</h2>
                <p>{{ $isAdminPage ? 'Phân công việc theo phòng ban, theo dõi người nhận và giữ mọi đầu việc nằm trong một luồng rõ ràng.' : 'Chọn đúng nhân sự trong phòng, giao việc nhanh và nhìn ngay đội nào đang nhận thêm đầu việc mới.' }}</p>
                <button type="button" class="primary-action red" style="margin-top:18px" onclick="openCreateNormalTaskModal()">
                    <i data-lucide="plus" style="width:18px;height:18px"></i>{{ $isAdminPage ? 'Tạo giao việc' : 'Giao việc cho đội' }}
                </button>
            </div>
            <aside class="dispatch-rail">
                <div class="rail-title"><span>Đội nhận việc</span><span class="mini-count">{{ $allUsers->count() }} người</span></div>
                <div class="rail-people">
                    @forelse($allUsers->take(4) as $assignee)
                        <div class="rail-person">
                            <div class="avatar-chip">{{ strtoupper(mb_substr($assignee->name ?? 'NV', 0, 2)) }}</div>
                            <div>
                                <div class="person-name">{{ $assignee->name }}</div>
                                <div class="person-meta">{{ $assignee->department->TENPHONG ?? 'Chưa xếp phòng' }}</div>
                            </div>
                            <span class="mini-count">{{ $assignee->tasks_count ?? 0 }}</span>
                        </div>
                    @empty
                        <div class="empty-card">Chưa có nhân viên để giao việc.</div>
                    @endforelse
                </div>
            </aside>
        </section>
    @endif

    <section class="metric-strip">
        <div class="metric-card"><span>Tổng việc</span><strong>{{ $totalTasks }}</strong></div>
        <div class="metric-card"><span>Đang xử lý</span><strong>{{ $doingTasks }}</strong></div>
        <div class="metric-card"><span>Hoàn thành</span><strong>{{ $doneTasks }}</strong></div>
        <div class="metric-card alert"><span>Quá hạn</span><strong>{{ $overdueTasks }}</strong></div>
    </section>

    @if(($projectGroups ?? collect())->isNotEmpty())
        <section class="mobifone-panel project-map">
            <div class="project-map-head">
                <div class="project-map-title"><i data-lucide="workflow" style="width:22px;height:22px"></i>Cấu trúc công việc lớn</div>
                <span class="mini-count">{{ $projectGroups->count() }} nhóm</span>
            </div>
            <div class="project-grid">
                @foreach($projectGroups as $project)
                    <article class="project-card">
                        <div class="project-card-top">
                            <span class="project-code"><i data-lucide="folder-kanban" style="width:14px;height:14px"></i>{{ $project['code'] }}</span>
                            <div class="project-name">{{ $project['name'] }}</div>
                            <div class="project-summary">
                                <span>{{ $project['total'] }} việc nhỏ</span>
                                <span>{{ $project['done'] }} hoàn thành</span>
                                <span>{{ $project['progress'] }}%</span>
                            </div>
                            <div class="project-progress"><span style="width:{{ $project['progress'] }}%"></span></div>
                        </div>
                        <div class="subtask-list">
                            @foreach($project['tasks'] as $task)
                                <div class="subtask-item">
                                    <div class="subtask-main">
                                        <div class="subtask-title">{{ $task['name'] }}</div>
                                        <span class="subtask-step">Bước {{ $task['project_step'] ?? $loop->iteration }}</span>
                                    </div>
                                    <div class="people-line">
                                        <span><b>Chủ trì:</b> {{ $task['lead'] }}</span>
                                        <span><b>Cùng nhận:</b> {{ !empty($task['collaborators']) ? implode(', ', $task['collaborators']) : 'Không có' }}</span>
                                        <span><b>Hạn:</b> {{ $task['deadline'] }} - <b>{{ $task['status'] }}</b></span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </article>
                @endforeach
            </div>
        </section>
    @endif

    <div class="action-row">
        <div class="left-tools">
            <div class="segmented">
                <a href="{{ route($taskRoute, $routeParams(['view' => 'kanban', 'filter' => $filter])) }}" class="{{ $viewType === 'kanban' ? 'active' : '' }}"><i data-lucide="columns-3" style="width:16px;height:16px"></i>Kanban</a>
                <a href="{{ route($taskRoute, $routeParams(['view' => 'list', 'filter' => $filter])) }}" class="{{ $viewType === 'list' ? 'active' : '' }}"><i data-lucide="list" style="width:16px;height:16px"></i>Danh sách</a>
            </div>
            <div class="filters">
                @foreach(['Tất cả', 'Của tôi', 'Quá hạn'] as $item)
                    <a href="{{ route($taskRoute, $routeParams(['view' => $viewType, 'filter' => $item])) }}" class="filter-pill {{ $filter === $item ? 'active' : '' }}">{{ $item }}</a>
                @endforeach
            </div>
        </div>
        @if($isProgressPage)
            <a class="primary-action" href="{{ route($taskRoute) }}"><i data-lucide="send" style="width:18px;height:18px"></i>Sang giao việc</a>
        @else
        <div class="left-tools">
            <div class="segmented">
                <a href="{{ route($taskRoute, $routeParams(['view' => 'kanban', 'filter' => $filter])) }}" class="{{ $viewType === 'kanban' ? 'active' : '' }}"><i data-lucide="columns-3" style="width:16px;height:16px"></i>Kanban</a>
                <a href="{{ route($taskRoute, $routeParams(['view' => 'list', 'filter' => $filter])) }}" class="{{ $viewType === 'list' ? 'active' : '' }}"><i data-lucide="list" style="width:16px;height:16px"></i>Danh sách</a>
            </div>
            <div class="filters">
                @foreach(['Tất cả', 'Của tôi', 'Quá hạn'] as $item)
                    <a href="{{ route($taskRoute, $routeParams(['view' => $viewType, 'filter' => $item])) }}" class="filter-pill {{ $filter === $item ? 'active' : '' }}">{{ $item }}</a>
                @endforeach
            </div>
        </div>
        @if($isProgressPage)
            <a class="primary-action" href="{{ route($taskRoute) }}"><i data-lucide="send" style="width:18px;height:18px"></i>Sang giao việc</a>
        @else
            <div style="display:flex; gap:8px; align-items:center">
                <button type="button" class="primary-action" onclick="openCreateNormalTaskModal()">
                    <i data-lucide="plus" style="width:18px;height:18px"></i>{{ $isAdminPage ? 'Giao việc' : 'Giao việc cho đội' }}
                </button>
                @if(!$isAdminPage)
                    <button type="button" class="primary-action" style="background:#E8F0FE;color:#003DA5;box-shadow:none;border:1px solid #B9CDF5" onclick="openCreateProposalTaskModal()">
                        <i data-lucide="send" style="width:18px;height:18px"></i>Gửi đề xuất việc
                    </button>
                @endif
            </div>
        @endif
    </div>

    @if($isProgressPage)
        <section class="progress-shell">
            <div class="mobifone-panel progress-table-box">
                <div class="section-head">
                    <div class="section-title"><i data-lucide="line-chart" style="width:22px;height:22px"></i>Bảng theo dõi tiến độ</div>
                    <span class="mini-count">{{ $totalTasks }} việc</span>
                </div>
                <div style="overflow-x:auto">
                    <table class="role-table">
                        <thead>
                            <tr>
                                <th>Mã</th>
                                <th>Công việc</th>
                                <th>Nhân viên</th>
                                <th>Hạn</th>
                                <th>Tiến độ</th>
                                <th>File</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mappedTasksList as $task)
                                <tr>
                                    <td class="task-code">{{ $task['code'] }}</td>
                                    <td>
                                        <strong style="color:#001F5B">{{ $task['name'] }}</strong><br>
                                        <span style="color:#64748B">{{ $task['status'] }}</span>
                                    </td>
                                    <td>
                                        <strong style="color:#003DA5">{{ $task['lead'] }}</strong>
                                        <br><span style="color:#64748B;font-size:12px">Cùng nhận: {{ !empty($task['collaborators']) ? implode(', ', $task['collaborators']) : 'Không có' }}</span>
                                    </td>
                                    <td>
                                        {{ $task['deadline'] }}
                                        @if(!empty($task['acceptance_deadline']) && in_array($task['status'], ['Chờ xử lý', 'Todo'], true))
                                            <br><span style="font-size:10px; padding:2px 6px; background:#FEF3C7; color:#D97706; border-radius:4px; font-weight:800; border:1px solid #FCD34D; display:inline-block; margin-top:4px;">HẠN NHẬN: {{ $task['acceptance_deadline'] }}</span>
                                        @endif
                                    </td>
                                    <td class="row-progress">
                                        <div class="progress-percent"><span>{{ $task['progress'] }}%</span></div>
                                        <div class="progress-track"><div class="progress-fill" style="width:{{ $task['progress'] }}%"></div></div>
                                    </td>
                                    <td>{{ $task['documents_count'] }}</td>
                                    <td>
                                        <a class="detail-link" href="{{ route($detailRoute, $detailParams($task['id'])) }}"><i data-lucide="eye" style="width:15px;height:15px"></i>Chi tiết</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" style="text-align:center;color:#94A3B8;padding:34px">Chưa có công việc để theo dõi.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <aside class="mobifone-panel">
                <div class="section-head">
                    <div class="section-title"><i data-lucide="radar" style="width:22px;height:22px"></i>Radar tiến độ</div>
                </div>
                <div class="focus-list">
                    @forelse($topProgressTasks as $task)
                        <article class="focus-card">
                            <div class="focus-line">
                                <div class="focus-name">{{ $task['name'] }}</div>
                                <span class="mini-count">{{ $task['progress'] }}%</span>
                            </div>
                            <div class="focus-meta">{{ $task['assignee'] }} · {{ $task['deadline'] }}</div>
                            <div class="progress-track"><div class="progress-fill" style="width:{{ $task['progress'] }}%"></div></div>
                        </article>
                    @empty
                        <div class="empty-card">Chưa có dữ liệu tiến độ.</div>
                    @endforelse
                </div>
            </aside>
        </section>
    @else
        <section class="dispatch-layout">
            <aside class="mobifone-panel assign-card">
                <div class="assign-title"><i data-lucide="workflow" style="width:22px;height:22px"></i>Luồng giao việc</div>
                <div class="assign-steps">
                    <div class="assign-step"><div class="step-number">1</div><div><div class="step-title">Chọn người nhận</div><div class="step-copy">Giao cho một hoặc nhiều nhân viên trong đúng phạm vi quyền.</div></div></div>
                    <div class="assign-step"><div class="step-number">2</div><div><div class="step-title">Đặt deadline</div><div class="step-copy">Deadline rõ ràng giúp hệ thống nhắc hạn và báo quá hạn chính xác.</div></div></div>
                    <div class="assign-step"><div class="step-number">3</div><div><div class="step-title">Theo dõi sau khi giao</div><div class="step-copy">Chuyển sang Tiến độ công việc để xem trạng thái xử lý.</div></div></div>
                </div>
            </aside>

            <div>
                @if($viewType === 'list')
                    <section class="list-box">
                        <table class="role-table">
                            <thead>
                                <tr>
                                    <th>Mã</th>
                                    <th>Công việc</th>
                                    <th>Nhân viên</th>
                                    <th>Hạn</th>
                                    <th>Tiến độ</th>
                                    <th>File</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mappedTasksList as $task)
                                    <tr>
                                        <td class="task-code">{{ $task['code'] }}</td>
                                        <td><strong style="color:#001F5B">{{ $task['name'] }}</strong><br><span style="color:#64748B">{{ $task['description'] ?: 'Chưa có mô tả.' }}</span></td>
                                        <td>
                                            <strong style="color:#003DA5">{{ $task['lead'] }}</strong>
                                            <br><span style="color:#64748B;font-size:12px">Cùng nhận: {{ !empty($task['collaborators']) ? implode(', ', $task['collaborators']) : 'Không có' }}</span>
                                        </td>
                                        <td style="{{ $task['is_overdue'] ? 'color:#E4002B; font-weight:800;' : '' }}">
                                             {{ $task['deadline'] }}
                                             @if($task['is_overdue'])
                                                 <br><span style="font-size:10px; padding:2px 6px; background:#FFEBEB; color:#E4002B; border-radius:4px; font-weight:900; border:1px solid #FFCDCD; display:inline-block; margin-top:4px;">QUÁ HẠN</span>
                                             @elseif(!empty($task['acceptance_deadline']) && in_array($task['status'], ['Chờ xử lý', 'Todo'], true))
                                                 <br><span style="font-size:10px; padding:2px 6px; background:#FEF3C7; color:#D97706; border-radius:4px; font-weight:800; border:1px solid #FCD34D; display:inline-block; margin-top:4px;">HẠN NHẬN: {{ $task['acceptance_deadline'] }}</span>
                                             @endif
                                         </td>
                                        <td>{{ $task['progress'] }}%</td>
                                        <td>{{ $task['documents_count'] }}</td>
                                        <td>
                                             <div style="display:flex; gap:8px; align-items:center; justify-content:flex-start">
                                                 <a class="detail-link" href="{{ route($detailRoute, $detailParams($task['id'])) }}"><i data-lucide="eye" style="width:15px;height:15px"></i>Chi tiết</a>
                                                 @if(Auth::user()->isDirector() || Auth::user()->isLeader())
                                                     <button type="button" class="btn primary" style="background:#003DA5;border-color:#003DA5;padding:4px 8px;min-height:auto;font-size:12px;cursor:pointer;color:#fff;border-radius:4px" onclick="openEditTaskModal({{ json_encode($task) }})">
                                                         <i data-lucide="edit-3" style="width:14px;height:14px"></i>Sửa
                                                     </button>
                                                 @endif
                                                 @if(request('mode') === 'proposal' && Auth::user()->isDirector() && $task['proposal_step'] === 2)
                                                     <button type="button" class="btn primary" style="background:#16A34A;border-color:#16A34A;padding:4px 8px;min-height:auto;font-size:12px;cursor:pointer" onclick="openApproveProposalModal({{ json_encode($task) }})">
                                                         <i data-lucide="check" style="width:14px;height:14px"></i>Phê duyệt
                                                     </button>
                                                     <button type="button" class="btn primary" style="background:#DC2626;border-color:#DC2626;padding:4px 8px;min-height:auto;font-size:12px;cursor:pointer" onclick="rejectProposal({{ $task['id'] }})">
                                                         <i data-lucide="x-circle" style="width:14px;height:14px"></i>Không đồng ý
                                                     </button>
                                                 @endif
                                             </div>
                                         </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" style="text-align:center;color:#94A3B8;padding:34px">Chưa có công việc.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </section>
                @else
                    <section class="board-grid">
                        @foreach($cols as $col)
                            <div class="work-column">
                                <div class="column-head">
                                    <span>{{ $col['label'] }}</span>
                                    <span class="count-badge">{{ count($col['tasks']) }}</span>
                                </div>
                                <div class="column-body">
                                    @forelse($col['tasks'] as $task)
                                        <article class="task-card">
                                            <div class="task-top">
                                                <span class="priority-chip">{{ $task['priority'] }}</span>
                                                @if($task['is_overdue'])
                                                    <span class="priority-chip" style="background:#FEF2F2; color:#B91C1C; margin-left:6px; border:1px solid #FCA5A5">Quá hạn</span>
                                                @elseif(!empty($task['acceptance_deadline']) && in_array($task['status'], ['Chờ xử lý', 'Todo'], true))
                                                    <span class="priority-chip" style="background:#FEF3C7; color:#D97706; margin-left:6px; border:1px solid #FCD34D" title="Chờ nhận trong 2h (hoặc 4h sau 17h)">Hạn nhận: {{ $task['acceptance_deadline'] }}</span>
                                                @endif
                                                <span class="task-code">{{ $task['code'] }}</span>
                                            </div>
                                            <div class="task-name">{{ $task['name'] }}</div>
                                            <div class="task-desc">{{ $task['description'] ?: 'Chưa có mô tả.' }}</div>
                                            <div class="task-people">
                                                <span><b>Chủ trì:</b> {{ $task['lead'] }}</span>
                                                <span><b>Cùng nhận:</b> {{ !empty($task['collaborators']) ? implode(', ', $task['collaborators']) : 'Không có' }}</span>
                                            </div>
                                            <div class="progress-meta"><span>Tiến độ</span><span>{{ $task['progress'] }}%</span></div>
                                            <div class="progress-track"><div class="progress-fill" style="width:{{ $task['progress'] }}%"></div></div>
                                            <div class="task-foot">
                                                <span style="{{ $task['is_overdue'] ? 'color:#E4002B; font-weight:800;' : '' }}">
                                                    <i data-lucide="calendar" style="width:14px;height:14px;vertical-align:-2px; {{ $task['is_overdue'] ? 'color:#E4002B;' : '' }}"></i> 
                                                     {{ $task['deadline'] }}
                                                </span>
                                                <span><i data-lucide="paperclip" style="width:14px;height:14px;vertical-align:-2px"></i> {{ $task['documents_count'] }}</span>
                                            </div>
                                            <div style="margin-top:13px;display:flex;justify-content:space-between;gap:10px;align-items:center;flex-wrap:wrap">
                                                 <strong style="color:#001F5B;font-size:12px">{{ $task['assignee_count'] }} người</strong>
                                                 <div style="display:flex; gap:6px; align-items:center">
                                                     <a class="detail-link" href="{{ route($detailRoute, $detailParams($task['id'])) }}"><i data-lucide="eye" style="width:15px;height:15px"></i>Chi tiết</a>
                                                     @if(Auth::user()->isDirector() || Auth::user()->isLeader())
                                                         <button type="button" class="btn primary" style="background:#003DA5;border-color:#003DA5;padding:4px 8px;min-height:auto;font-size:12px;cursor:pointer;color:#fff;border-radius:4px" onclick="openEditTaskModal({{ json_encode($task) }})">
                                                             <i data-lucide="edit-3" style="width:14px;height:14px"></i>Sửa
                                                         </button>
                                                     @endif
                                                     @if(request('mode') === 'proposal' && Auth::user()->isDirector() && $task['proposal_step'] === 2)
                                                         <button type="button" class="btn primary" style="background:#16A34A;border-color:#16A34A;padding:4px 8px;min-height:auto;font-size:12px;cursor:pointer" onclick="openApproveProposalModal({{ json_encode($task) }})">
                                                             <i data-lucide="check" style="width:14px;height:14px"></i>Phê duyệt
                                                         </button>
                                                         <button type="button" class="btn primary" style="background:#DC2626;border-color:#DC2626;padding:4px 8px;min-height:auto;font-size:12px;cursor:pointer" onclick="rejectProposal({{ $task['id'] }})">
                                                             <i data-lucide="x-circle" style="width:14px;height:14px"></i>Không đồng ý
                                                         </button>
                                                     @endif
                                                 </div>
                                             </div>
                                        </article>
                                    @empty
                                        <div class="empty-card">Chưa có công việc.</div>
                                    @endforelse
                                </div>
                            </div>
                        @endforeach
                    </section>
                @endif
            </div>
        </section>
    @endif
</div>

<div class="modal-backdrop" id="createTaskModal">
    <div class="task-modal">
        <div class="modal-head">
            <span>{{ $isAdminPage ? 'Giao việc cấp công ty' : 'Giao việc trong phòng' }}</span>
            <button type="button" class="ghost-btn" onclick="document.getElementById('createTaskModal').classList.remove('open')">Đóng</button>
        </div>
        <form method="POST" action="{{ route($saveRoute) }}">
            @csrf
            <input type="hidden" name="is_proposal" id="partial-is-proposal" value="0">
            <div class="modal-body">
                <div class="field">
                    <label>Tên công việc *</label>
                    <input name="task_name" required placeholder="Nhập tên công việc">
                </div>
                <div class="field">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px">
                        <label style="margin:0">Người cùng làm *</label>
                        <label style="display:inline-flex;align-items:center;gap:6px;font-size:12px;font-weight:900;color:#003DA5;cursor:pointer;user-select:none;margin:0">
                            <input type="checkbox" id="select-all-assignees" style="cursor:pointer;width:14px;height:14px;margin:0;accent-color:#003DA5">
                            Chọn tất cả
                        </label>
                    </div>
                    @php
                        $groupedUsers = $allUsers->groupBy(fn($u) => $u->department->TENPHONG ?? 'Chưa xếp phòng');
                    @endphp
                    <div class="assignee-check-grid" style="display:flex; flex-direction:column; gap:16px;">
                        @foreach($groupedUsers as $deptName => $usersInDept)
                            <div class="dept-group" style="display:flex; flex-direction:column; gap:8px;">
                                <div style="font-weight:800; font-size:12px; color:#003DA5; margin-bottom:4px; border-bottom:1px solid #E2EAF8; padding-bottom:4px; display:flex; justify-content:space-between; align-items:center;">
                                    <span>{{ $deptName }}</span>
                                    <label style="font-size:10px; font-weight:700; color:#64748B; cursor:pointer; display:inline-flex; align-items:center; gap:4px; margin:0;">
                                        <input type="checkbox" class="select-dept-all" style="width:12px; height:12px; margin:0;" onclick="toggleDeptAll(this, '{{ addslashes($deptName) }}')"> Chọn tất cả
                                    </label>
                                </div>
                                <div style="display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:8px;">
                                    @foreach($usersInDept as $assignee)
                                        <label class="assignee-check" data-dept="{{ $deptName }}" style="padding:8px 10px; min-height:44px; display:flex; align-items:center; gap:8px; border:1px solid #E2EAF8; border-radius:8px; background:#fff; cursor:pointer;">
                                            <input type="checkbox" name="assigned_to[]" value="{{ $assignee->id }}" style="width:14px; height:14px; margin:0;">
                                            <span style="display:block; text-align:left; font-size:12px; line-height:1.2; font-weight:700; color:#001F5B;">
                                                {{ $assignee->name }}
                                                <small style="display:block; font-size:10px; color:#64748B; font-weight:500; margin-top:2px;">{{ $assignee->role->name ?? 'Nhân viên' }}</small>
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div style="margin-top:6px;color:#64748B;font-size:12px;font-weight:700">Tích nhiều nhân viên để cùng làm chung một công việc.</div>
                </div>
                <div class="form-grid">
                    <div class="field">
                        <label>Deadline *</label>
                        <input type="date" name="deadline" required>
                    </div>
                    <div class="field">
                        <label>Trạng thái</label>
                        <select name="status">
                            <option>Chờ xử lý</option>
                            <option>Đang làm</option>
                            <option>Đang review</option>
                            <option>Hoàn thành</option>
                        </select>
                    </div>
                </div>
                <div class="field">
                    <label>Mô tả</label>
                    <textarea name="description" placeholder="Ghi chú yêu cầu, phạm vi, tài liệu cần chuẩn bị..."></textarea>
                </div>
            </div>
            <div class="modal-actions">
                <button type="button" class="ghost-btn" onclick="document.getElementById('createTaskModal').classList.remove('open')">Hủy</button>
                <button type="submit" class="primary-action">Lưu công việc</button>
            </div>
        </form>
    </div>
</div>

<script>
    window.rejectProposal = (taskId) => {
        if (!confirm('Bạn có chắc chắn muốn từ chối đề xuất công việc này?')) return;
        
        const f = document.createElement('form');
        f.method = 'POST';
        f.action = "/dashboard/tasks/" + taskId + "/reject-proposal";
        f.innerHTML = '@csrf';
        document.body.appendChild(f);
        f.submit();
    };
    window.openCreateNormalTaskModal = () => {
        const m = document.getElementById('createTaskModal');
        if (m) {
            const form = m.querySelector('form');
            if (form) {
                form.reset();
                form.action = "{{ route($saveRoute) }}";
            }
            const modalTitle = m.querySelector('.modal-head span');
            if (modalTitle) modalTitle.innerText = "{{ $isAdminPage ? 'Giao việc cấp công ty' : 'Giao việc trong phòng' }}";
            const isProposalField = document.getElementById('partial-is-proposal');
            if (isProposalField) isProposalField.value = '0';
            const assigneeField = m.querySelector('input[name="assigned_to[]"]')?.closest('.field');
            if (assigneeField) assigneeField.style.display = 'block';
            const submitBtn = m.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.innerText = 'Lưu công việc';
            m.classList.add('open');
        }
    };

    window.openCreateProposalTaskModal = () => {
        const m = document.getElementById('createTaskModal');
        if (m) {
            const form = m.querySelector('form');
            if (form) {
                form.reset();
                form.action = "{{ route($saveRoute) }}";
            }
            const modalTitle = m.querySelector('.modal-head span');
            if (modalTitle) modalTitle.innerText = "Gửi đề xuất công việc lên cấp trên";
            const isProposalField = document.getElementById('partial-is-proposal');
            if (isProposalField) isProposalField.value = '1';
            const assigneeField = m.querySelector('input[name="assigned_to[]"]')?.closest('.field');
            if (assigneeField) assigneeField.style.display = 'none';
            const submitBtn = m.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.innerText = 'Gửi đề xuất';
            m.classList.add('open');
        }
    };

    window.openApproveProposalModal = (task) => {
        const m = document.getElementById('createTaskModal');
        if (m) {
            const form = m.querySelector('form');
            if (form) {
                form.reset();
                form.action = "/dashboard/tasks/" + task.id + "/update";
            }
            const modalTitle = m.querySelector('.modal-head span');
            if (modalTitle) modalTitle.innerText = "Giao việc (Phê duyệt đề xuất)";
            const isProposalField = document.getElementById('partial-is-proposal');
            if (isProposalField) isProposalField.value = '0';
            
            m.querySelector('[name="task_name"]').value = task.name;
            m.querySelector('[name="description"]').value = task.description || '';
            m.querySelector('[name="deadline"]').value = task.deadline_raw;
            
            const statusSelect = m.querySelector('[name="status"]');
            if (statusSelect) statusSelect.value = 'Chờ xử lý';
            
            const assigneeField = m.querySelector('input[name="assigned_to[]"]')?.closest('.field');
            if (assigneeField) assigneeField.style.display = 'block';
            
            const proposerId = task.assigned_by;
            const checkboxes = m.querySelectorAll('input[name="assigned_to[]"]');
            checkboxes.forEach(cb => {
                cb.checked = (parseInt(cb.value) === parseInt(proposerId));
            });
            
            const submitBtn = m.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.innerText = 'Giao việc';
            m.classList.add('open');
        }
    };

    window.openEditTaskModal = (task) => {
        const m = document.getElementById('createTaskModal');
        if (m) {
            const form = m.querySelector('form');
            if (form) {
                form.reset();
                form.action = "/dashboard/tasks/" + task.id + "/update";
            }
            const modalTitle = m.querySelector('.modal-head span');
            if (modalTitle) modalTitle.innerText = "Chỉnh sửa công việc";
            const isProposalField = document.getElementById('partial-is-proposal');
            if (isProposalField) isProposalField.value = task.is_proposal ? '1' : '0';
            
            m.querySelector('[name="task_name"]').value = task.name;
            m.querySelector('[name="description"]').value = task.description || '';
            m.querySelector('[name="deadline"]').value = task.deadline_raw;
            
            const statusSelect = m.querySelector('[name="status"]');
            if (statusSelect) {
                statusSelect.value = task.status;
            }
            
            const assigneeField = m.querySelector('input[name="assigned_to[]"]')?.closest('.field');
            if (assigneeField) assigneeField.style.display = 'block';
            
            const checkboxes = m.querySelectorAll('input[name="assigned_to[]"]');
            checkboxes.forEach(cb => {
                cb.checked = task.assignee_ids && task.assignee_ids.includes(parseInt(cb.value));
            });
            
            const submitBtn = m.querySelector('button[type="submit"]');
            if (submitBtn) submitBtn.innerText = 'Lưu thay đổi';
            
            if (window.updateSelectAllState) {
                window.updateSelectAllState();
            }
            
            m.classList.add('open');
        }
    };

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            document.getElementById('createTaskModal')?.classList.remove('open');
        }
    });

    window.toggleDeptAll = (cb, deptName) => {
        const checkboxes = document.querySelectorAll(`.assignee-check[data-dept="${deptName}"] input[name="assigned_to[]"]`);
        checkboxes.forEach(child => {
            child.checked = cb.checked;
        });
        if (window.updateSelectAllState) {
            window.updateSelectAllState();
        }
    };

    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('select-all-assignees');
        if (selectAllCheckbox) {
            const individualCheckboxes = document.querySelectorAll('#createTaskModal input[name="assigned_to[]"]');
            
            const updateSelectAllState = () => {
                const checkedCount = document.querySelectorAll('#createTaskModal input[name="assigned_to[]"]:checked').length;
                selectAllCheckbox.checked = checkedCount === individualCheckboxes.length && individualCheckboxes.length > 0;
                
                // Update each department's "Select all" state
                const depts = document.querySelectorAll('.dept-group');
                depts.forEach(dept => {
                    const deptCheckbox = dept.querySelector('.select-dept-all');
                    if (deptCheckbox) {
                        const onclickAttr = deptCheckbox.getAttribute('onclick') || '';
                        const match = onclickAttr.match(/'([^']+)'/);
                        if (match) {
                            const deptName = match[1];
                            const deptChildren = dept.querySelectorAll(`.assignee-check[data-dept="${deptName}"] input[name="assigned_to[]"]`);
                            const deptChecked = dept.querySelectorAll(`.assignee-check[data-dept="${deptName}"] input[name="assigned_to[]"]:checked`);
                            deptCheckbox.checked = deptChildren.length > 0 && deptChildren.length === deptChecked.length;
                        }
                    }
                });
            };

            selectAllCheckbox.addEventListener('change', function() {
                individualCheckboxes.forEach(cb => {
                    cb.checked = selectAllCheckbox.checked;
                });
                updateSelectAllState();
            });

            individualCheckboxes.forEach(cb => {
                cb.addEventListener('change', updateSelectAllState);
            });

            const form = document.querySelector('#createTaskModal form');
            if (form) {
                form.addEventListener('reset', function() {
                    setTimeout(updateSelectAllState, 0);
                });
            }
            
            window.updateSelectAllState = updateSelectAllState;
            updateSelectAllState();
        }
    });

    @if(request('edit_task_id'))
        @php
            $editTaskId = (int) request('edit_task_id');
            $taskToEdit = null;
            foreach ($cols as $colKey => $colData) {
                foreach ($colData['tasks'] as $t) {
                    if ((int)$t['id'] === $editTaskId) {
                        $taskToEdit = $t;
                        break 2;
                    }
                }
            }
        @endphp
        @if($taskToEdit)
            document.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    if (window.openEditTaskModal) {
                        window.openEditTaskModal(@json($taskToEdit));
                    }
                }, 300);
            });
        @endif
    @endif
</script>
