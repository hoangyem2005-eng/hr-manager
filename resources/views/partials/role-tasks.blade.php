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
    $totalTasks = count($mappedTasksList);
    $doneTasks = collect($mappedTasksList)->where('status', 'Hoàn thành')->count();
    $doingTasks = collect($mappedTasksList)->whereIn('status', ['Đang làm', 'Đang review'])->count();
    $pendingTasks = collect($mappedTasksList)->whereIn('status', ['Chờ xử lý', 'Todo'])->count();
    $overdueTasks = collect($mappedTasksList)->where('status', 'Quá hạn')->count();
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
    .board-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; }
    .work-column { min-height:430px; overflow:hidden; border:1px solid #BFD1F5; border-top:4px solid #003DA5; border-radius:8px; background:#F8FAFF; }
    .work-column:nth-child(1) { border-top-color:#64748B; }
    .work-column:nth-child(2) { border-top-color:#E4002B; }
    .work-column:nth-child(3) { border-top-color:#2563EB; }
    .work-column:nth-child(4) { border-top-color:#001F5B; }
    .column-head { display:flex; align-items:center; justify-content:space-between; padding:14px 16px; border-bottom:1px solid #E4ECFA; background:#fff; color:#001F5B; font-weight:900; }
    .count-badge { min-width:27px; height:24px; display:grid; place-items:center; border-radius:999px; background:#003DA5; color:#fff; font-size:12px; }
    .column-body { display:grid; gap:13px; padding:14px; }
    .empty-card { min-height:110px; display:grid; place-items:center; border:1px dashed #BFD1F5; border-radius:8px; color:#94A3B8; background:rgba(255,255,255,.62); font-weight:700; }
    .task-card { border:1px solid #D4E0F7; border-radius:8px; padding:15px; background:#fff; box-shadow:0 14px 30px rgba(0,31,91,.08); }
    .task-top { display:flex; justify-content:space-between; gap:10px; align-items:center; }
    .priority-chip { border-radius:999px; padding:5px 10px; background:#FFF3CD; color:#B45309; font-size:11px; font-weight:900; }
    .task-code { color:#94A3B8; font-size:10px; font-weight:900; font-family:ui-monospace,SFMono-Regular,Menlo,monospace; }
    .task-name { margin-top:12px; color:#001F5B; font-size:15px; font-weight:900; line-height:1.35; }
    .task-desc { margin-top:6px; color:#64748B; font-size:12px; line-height:1.45; }
    .progress-meta { margin-top:13px; display:flex; justify-content:space-between; color:#64748B; font-size:11px; font-weight:900; }
    .progress-track { margin-top:7px; height:7px; overflow:hidden; border-radius:999px; background:#E9EEF8; }
    .progress-fill { height:100%; border-radius:inherit; background:linear-gradient(90deg,#E4002B,#003DA5); }
    .task-foot { margin-top:13px; display:flex; justify-content:space-between; gap:10px; align-items:center; color:#64748B; font-size:12px; font-weight:800; }
    .detail-link { display:inline-flex; align-items:center; gap:7px; border:1px solid #B9CDF5; border-radius:8px; padding:9px 12px; color:#003DA5; background:#fff; font-size:12px; font-weight:900; text-decoration:none; }
    .progress-shell { display:grid; grid-template-columns:minmax(0,1.35fr) minmax(320px,.65fr); gap:16px; align-items:start; }
    .progress-table-box { overflow:hidden; }
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
    .list-box { overflow:hidden; border:1px solid #D4E0F7; border-radius:8px; background:#fff; }
    .modal-backdrop { position:fixed; inset:0; z-index:100; display:none; align-items:center; justify-content:center; padding:18px; background:rgba(0,20,60,.46); }
    .modal-backdrop.open { display:flex; }
    .task-modal { width:min(680px,100%); overflow:hidden; border-radius:10px; background:#fff; box-shadow:0 26px 70px rgba(0,31,91,.28); }
    .modal-head { display:flex; justify-content:space-between; align-items:center; padding:18px 20px; border-bottom:1px solid #E5EAF5; color:#001F5B; font-weight:900; }
    .modal-body { display:grid; gap:14px; padding:20px; }
    .field label { display:block; margin-bottom:6px; color:#64748B; font-size:12px; font-weight:900; }
    .field input, .field textarea, .field select { width:100%; border:1px solid #D4E0F7; border-radius:8px; padding:12px; outline:none; }
    .field textarea { min-height:110px; resize:vertical; }
    .form-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .ghost-btn { border:1px solid #D4E0F7; border-radius:8px; padding:11px 16px; background:#fff; color:#334155; font-weight:900; cursor:pointer; }
    .modal-actions { display:flex; justify-content:flex-end; gap:10px; padding:0 20px 20px; }
    @media (max-width:1200px) { .dispatch-hero, .progress-hero, .dispatch-layout, .progress-shell { grid-template-columns:1fr; } .board-grid, .metric-strip { grid-template-columns:repeat(2,minmax(0,1fr)); } }
    @media (max-width:760px) { .board-grid, .metric-strip, .form-grid { grid-template-columns:1fr; } .role-table { min-width:760px; } .dispatch-hero, .progress-hero { padding:20px; } .dispatch-hero h2, .progress-hero h2 { font-size:30px; } }
</style>

<div class="role-page {{ $isProgressPage ? 'progress-page' : 'dispatch-page' }}">
    @if($isProgressPage)
        <section class="progress-hero">
            <div>
                <div class="hero-kicker red"><i data-lucide="activity" style="width:16px;height:16px"></i> Progress Control</div>
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
                <div class="hero-kicker"><i data-lucide="send" style="width:16px;height:16px"></i>{{ $isAdminPage ? 'Executive Dispatch' : 'Department Dispatch' }}</div>
                <h2>{{ $isAdminPage ? 'Giao việc toàn công ty' : 'Bàn giao việc cho đội nhóm' }}</h2>
                <p>{{ $isAdminPage ? 'Phân công việc theo phòng ban, theo dõi người nhận và giữ mọi đầu việc nằm trong một luồng rõ ràng.' : 'Chọn đúng nhân sự trong phòng, giao việc nhanh và nhìn ngay đội nào đang nhận thêm đầu việc mới.' }}</p>
                <button type="button" class="primary-action red" style="margin-top:18px" onclick="document.getElementById('createTaskModal').classList.add('open')">
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
            <button type="button" class="primary-action" onclick="document.getElementById('createTaskModal').classList.add('open')">
                <i data-lucide="send" style="width:18px;height:18px"></i>{{ $isAdminPage ? 'Giao việc' : 'Giao việc cho đội' }}
            </button>
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
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($mappedTasksList as $task)
                                <tr>
                                    <td class="task-code">{{ $task['code'] }}</td>
                                    <td><strong style="color:#001F5B">{{ $task['name'] }}</strong><br><span style="color:#64748B">{{ $task['status'] }}</span></td>
                                    <td>{{ $task['assignee'] }}</td>
                                    <td>{{ $task['deadline'] }}</td>
                                    <td class="row-progress">
                                        <div class="progress-percent"><span>{{ $task['progress'] }}%</span><span>{{ $task['status'] }}</span></div>
                                        <div class="progress-track"><div class="progress-fill" style="width:{{ $task['progress'] }}%"></div></div>
                                    </td>
                                    <td>{{ $task['documents_count'] }}</td>
                                    <td><a class="detail-link" href="{{ route($detailRoute, $detailParams($task['id'])) }}"><i data-lucide="eye" style="width:15px;height:15px"></i>Chi tiết</a></td>
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
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mappedTasksList as $task)
                                    <tr>
                                        <td class="task-code">{{ $task['code'] }}</td>
                                        <td><strong style="color:#001F5B">{{ $task['name'] }}</strong><br><span style="color:#64748B">{{ $task['description'] ?: 'Chưa có mô tả.' }}</span></td>
                                        <td>{{ $task['assignee'] }}</td>
                                        <td>{{ $task['deadline'] }}</td>
                                        <td>{{ $task['progress'] }}%</td>
                                        <td>{{ $task['documents_count'] }}</td>
                                        <td><a class="detail-link" href="{{ route($detailRoute, $detailParams($task['id'])) }}"><i data-lucide="eye" style="width:15px;height:15px"></i>Chi tiết</a></td>
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
                                                <span class="task-code">{{ $task['code'] }}</span>
                                            </div>
                                            <div class="task-name">{{ $task['name'] }}</div>
                                            <div class="task-desc">{{ $task['description'] ?: 'Chưa có mô tả.' }}</div>
                                            <div class="progress-meta"><span>Tiến độ</span><span>{{ $task['progress'] }}%</span></div>
                                            <div class="progress-track"><div class="progress-fill" style="width:{{ $task['progress'] }}%"></div></div>
                                            <div class="task-foot">
                                                <span><i data-lucide="calendar" style="width:14px;height:14px;vertical-align:-2px"></i> {{ $task['deadline'] }}</span>
                                                <span><i data-lucide="paperclip" style="width:14px;height:14px;vertical-align:-2px"></i> {{ $task['documents_count'] }}</span>
                                            </div>
                                            <div style="margin-top:13px;display:flex;justify-content:space-between;gap:10px;align-items:center">
                                                <strong style="color:#001F5B;font-size:12px">{{ $task['assignee'] }}</strong>
                                                <a class="detail-link" href="{{ route($detailRoute, $detailParams($task['id'])) }}"><i data-lucide="eye" style="width:15px;height:15px"></i>Chi tiết</a>
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
            <div class="modal-body">
                <div class="field">
                    <label>Tên công việc *</label>
                    <input name="task_name" required placeholder="Nhập tên công việc">
                </div>
                <div class="field">
                    <label>Người nhận việc *</label>
                    <select name="assigned_to[]" multiple required size="6">
                        @foreach($allUsers as $assignee)
                            <option value="{{ $assignee->id }}">
                                {{ $assignee->name }} - {{ $assignee->department->TENPHONG ?? 'Chưa xếp phòng' }} / {{ $assignee->role->name ?? 'Nhân viên' }}
                            </option>
                        @endforeach
                    </select>
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
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            document.getElementById('createTaskModal')?.classList.remove('open');
        }
    });
</script>
