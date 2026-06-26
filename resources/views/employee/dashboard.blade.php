<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Nhân viên — MobiFone HR')</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; font-family: 'Inter', sans-serif; }

        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex; align-items: flex-start;
        }

        .emp-wrapper {
            width: 100%; min-height: 100vh;
            display: flex; flex-direction: column;
        }

        /* TOPBAR */
        .emp-topbar {
            background: rgba(255,255,255,.12);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255,255,255,.2);
            padding: 0 28px;
            height: 64px;
            display: flex; align-items: center; gap: 16px;
            position: sticky; top: 0; z-index: 20;
        }
        .emp-brand { display: flex; align-items: center; gap: 10px; }
        .emp-brand-icon {
            width: 36px; height: 36px; border-radius: 10px;
            background: rgba(255,255,255,.25);
            display: flex; align-items: center; justify-content: center;
            font-weight: 800; font-size: 14px; color: #fff;
        }
        .emp-brand-text .brand { font-size: 14px; font-weight: 700; color: #fff; }
        .emp-brand-text .sub   { font-size: 10px; color: rgba(255,255,255,.6); }

        .emp-topbar .spacer { flex: 1; }

        .notif-btn {
            position: relative;
            width: 38px; height: 38px; border-radius: 10px;
            background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; color: #fff; transition: all .18s;
        }
        .notif-btn:hover { background: rgba(255,255,255,.25); }
        .notif-dot {
            position: absolute; top: 6px; right: 6px;
            width: 8px; height: 8px; background: #FCD34D;
            border-radius: 50%; border: 2px solid rgba(103,126,234,1);
        }

        .emp-user-chip {
            display: flex; align-items: center; gap: 8px;
            padding: 6px 12px; border-radius: 99px;
            background: rgba(255,255,255,.2);
            border: 1px solid rgba(255,255,255,.3);
        }
        .emp-avatar {
            width: 28px; height: 28px; border-radius: 8px;
            background: rgba(255,255,255,.4);
            display: flex; align-items: center; justify-content: center;
            font-weight: 700; font-size: 11px; color: #fff;
        }
        .emp-name { font-size: 12px; font-weight: 600; color: #fff; }

        /* MAIN */
        .emp-main {
            flex: 1; padding: 28px;
            overflow-y: auto;
        }

        /* GREETING */
        .greeting-card {
            background: rgba(255,255,255,.15);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255,255,255,.25);
            border-radius: 20px;
            padding: 28px 32px;
            margin-bottom: 24px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .greeting-text .hello { font-size: 13px; color: rgba(255,255,255,.7); margin-bottom: 4px; }
        .greeting-text .name  { font-size: 28px; font-weight: 800; color: #fff; }
        .greeting-text .dept  { font-size: 13px; color: rgba(255,255,255,.65); margin-top: 4px; }

        .completion-ring {
            width: 90px; height: 90px; flex-shrink: 0;
        }

        /* KPI ROW */
        .kpi-row { display: grid; grid-template-columns: repeat(4,1fr); gap: 14px; margin-bottom: 24px; }

        .kpi-mini {
            background: rgba(255,255,255,.15);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,.2);
            border-radius: 14px;
            padding: 16px 18px;
            display: flex; align-items: center; gap: 14px;
            transition: all .2s;
        }
        .kpi-mini:hover { background: rgba(255,255,255,.22); transform: translateY(-2px); }
        .kpi-mini-icon {
            width: 40px; height: 40px; border-radius: 11px;
            background: rgba(255,255,255,.2);
            display: flex; align-items: center; justify-content: center;
            color: #fff; flex-shrink: 0;
        }
        .kpi-mini-val  { font-size: 24px; font-weight: 800; color: #fff; line-height: 1; }
        .kpi-mini-label{ font-size: 11px; color: rgba(255,255,255,.65); margin-top: 2px; }

        /* TASK LIST */
        .tasks-card {
            background: rgba(255,255,255,.95);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0,0,0,.12);
            margin-bottom: 20px;
        }
        .tasks-header {
            padding: 18px 24px;
            display: flex; align-items: center; justify-content: space-between;
            border-bottom: 1px solid #F1F5F9;
            background: #fff;
        }
        .tasks-title { font-size: 15px; font-weight: 700; color: #1E3A5F; }

        /* FILTER TABS */
        .filter-tabs {
            display: flex; gap: 6px;
            background: #F1F5F9; border-radius: 10px; padding: 4px;
        }
        .tab-btn {
            padding: 6px 14px; border-radius: 8px; border: none;
            font-size: 12px; font-weight: 600; cursor: pointer;
            font-family: inherit; color: #64748B; background: none;
            transition: all .18s;
        }
        .tab-btn.active { background: #fff; color: #1E3A5F; box-shadow: 0 1px 4px rgba(0,0,0,.1); }

        /* TASK ITEM */
        .task-item {
            padding: 16px 24px;
            border-bottom: 1px solid #F8FAFC;
            display: flex; align-items: center; gap: 16px;
            transition: background .15s;
        }
        .task-item:hover { background: #FAFBFF; }
        .task-item:last-child { border-bottom: none; }

        .task-check {
            width: 22px; height: 22px; border-radius: 6px;
            border: 2px solid #E2E8F0;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; cursor: pointer;
            transition: all .15s;
        }
        .task-check.done  { background: #16A34A; border-color: #16A34A; color: #fff; }
        .task-check.doing { border-color: #D97706; }

        .task-info { flex: 1; min-width: 0; }
        .task-name {
            font-size: 14px; font-weight: 600; color: #0F172A;
            margin-bottom: 4px;
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .task-name.done-text { text-decoration: line-through; color: #94A3B8; }
        .task-meta { display: flex; align-items: center; gap: 10px; }

        .task-deadline {
            font-size: 11px; color: #94A3B8;
            display: flex; align-items: center; gap: 4px;
        }
        .task-deadline.urgent { color: #E63946; font-weight: 600; }

        /* PROGRESS SLIDER */
        .task-progress-wrap { display: flex; align-items: center; gap: 10px; min-width: 140px; }
        .task-progress-track {
            flex: 1; height: 6px; background: #E2E8F0;
            border-radius: 99px; overflow: hidden;
        }
        .task-progress-fill { height: 100%; border-radius: 99px; transition: width .4s; }

        .status-select {
            padding: 4px 10px; border-radius: 8px;
            border: 1.5px solid #E2E8F0;
            font-size: 11px; font-weight: 600;
            font-family: inherit; cursor: pointer;
            outline: none; background: #fff;
            transition: all .15s;
        }
        .status-select:focus { border-color: #7C3AED; }

        .pill { display:inline-block;padding:3px 10px;border-radius:99px;font-size:11px;font-weight:600; }
        .pill.done    { background:#F0FDF4;color:#16A34A; }
        .pill.doing   { background:#FFFBEB;color:#D97706; }
        .pill.review  { background:#EFF6FF;color:#2563EB; }
        .pill.pending { background:#F9FAFB;color:#6B7280; }
        .pill.overdue { background:#FFF1F2;color:#E63946; }

        /* PROFILE CARD */
        .profile-card {
            background: rgba(255,255,255,.95);
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 8px 32px rgba(0,0,0,.1);
        }
        .profile-title { font-size: 14px; font-weight: 700; color: #1E3A5F; margin-bottom: 18px; }
        .profile-row { display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #F1F5F9; font-size: 13px; }
        .profile-row:last-child { border-bottom: none; }
        .profile-row .label { color: #64748B; }
        .profile-row .value { font-weight: 600; color: #0F172A; }

        .content-grid { display: grid; grid-template-columns: 2fr 1fr; gap: 20px; }

        .btn-logout {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 7px 14px; border-radius: 10px;
            background: rgba(255,255,255,.15);
            border: 1px solid rgba(255,255,255,.25);
            color: #fff; font-size: 12px; font-weight: 600;
            font-family: inherit; cursor: pointer; transition: all .18s;
        }
        .btn-logout:hover { background: rgba(255,255,255,.25); }

        .empty-state { text-align:center; padding:40px 20px; }
        .empty-state svg { margin: 0 auto 12px; display:block; opacity:.4; }
        .empty-state p { font-size:14px; color:#94A3B8; }

        .toast {
            position: fixed; bottom: 24px; right: 24px;
            background: #0F172A; color: #fff; padding: 12px 20px;
            border-radius: 12px; font-size: 13px; font-weight: 500;
            box-shadow: 0 4px 20px rgba(0,0,0,.2);
            transform: translateY(80px); opacity: 0;
            transition: all .3s; z-index: 9999;
        }
        .toast.show { transform: translateY(0); opacity: 1; }

        @media (max-width: 900px) {
            .kpi-row { grid-template-columns: repeat(2,1fr); }
            .content-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 560px) {
            .kpi-row { grid-template-columns: 1fr 1fr; }
            .greeting-card { flex-direction: column; gap: 16px; }
        }
    </style>
</head>
<body>
<div class="emp-wrapper">

    <!-- TOPBAR -->
    <header class="emp-topbar">
        <div class="emp-brand">
            <div class="emp-brand-icon">NV</div>
            <div class="emp-brand-text">
                <div class="brand">MobiFone</div>
                <div class="sub">WorkHub</div>
            </div>
        </div>

        <div class="spacer"></div>

        <a href="{{ route('dashboard.notifications') }}" class="notif-btn">
            <i data-lucide="bell" style="width:17px;height:17px"></i>
            @if($unreadCount > 0)<span class="notif-dot"></span>@endif
        </a>

        <div class="emp-user-chip">
            <div class="emp-avatar">{{ substr(Auth::user()->name ?? 'NV', 0, 2) }}</div>
            <span class="emp-name">{{ Auth::user()->name }}</span>
        </div>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="btn-logout">
                <i data-lucide="log-out" style="width:14px;height:14px"></i>
                Đăng xuất
            </button>
        </form>
    </header>

    <!-- MAIN -->
    <div class="emp-main">
        @if(session('success'))
            <div class="toast show" id="toast">✓ {{ session('success') }}</div>
        @endif

        <!-- GREETING -->
        <div class="greeting-card">
            <div class="greeting-text">
                <div class="hello">👋 Xin chào,</div>
                <div class="name">{{ Auth::user()->name }}</div>
                <div class="dept">
                    <i data-lucide="building-2" style="width:13px;height:13px;vertical-align:middle;margin-right:4px"></i>
                    {{ Auth::user()->department->TENPHONG ?? 'MobiFone' }}
                    &nbsp;·&nbsp;
                    <i data-lucide="briefcase" style="width:13px;height:13px;vertical-align:middle;margin-right:4px"></i>
                    Nhân viên
                </div>
            </div>

            <!-- Completion ring via SVG -->
            <div style="text-align:center">
                <svg width="90" height="90" viewBox="0 0 90 90">
                    <circle cx="45" cy="45" r="36" fill="none" stroke="rgba(255,255,255,.2)" stroke-width="8"/>
                    <circle cx="45" cy="45" r="36" fill="none" stroke="#FCD34D" stroke-width="8"
                        stroke-dasharray="{{ round(2*3.14159*36) }}"
                        stroke-dashoffset="{{ round(2*3.14159*36 * (1 - $completionRate/100)) }}"
                        stroke-linecap="round"
                        transform="rotate(-90 45 45)"/>
                    <text x="45" y="48" text-anchor="middle" font-size="14" font-weight="800" fill="#fff" font-family="Inter">{{ $completionRate }}%</text>
                    <text x="45" y="62" text-anchor="middle" font-size="8" fill="rgba(255,255,255,.6)" font-family="Inter">hoàn thành</text>
                </svg>
            </div>
        </div>

        <!-- KPI ROW -->
        <div class="kpi-row">
            <div class="kpi-mini">
                <div class="kpi-mini-icon"><i data-lucide="clipboard" style="width:18px;height:18px"></i></div>
                <div>
                    <div class="kpi-mini-val">{{ $total }}</div>
                    <div class="kpi-mini-label">Tất cả công việc</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon"><i data-lucide="zap" style="width:18px;height:18px"></i></div>
                <div>
                    <div class="kpi-mini-val" style="color:#FCD34D">{{ $doing }}</div>
                    <div class="kpi-mini-label">Đang thực hiện</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon"><i data-lucide="check-circle" style="width:18px;height:18px"></i></div>
                <div>
                    <div class="kpi-mini-val" style="color:#6EE7B7">{{ $done }}</div>
                    <div class="kpi-mini-label">Đã hoàn thành</div>
                </div>
            </div>
            <div class="kpi-mini">
                <div class="kpi-mini-icon"><i data-lucide="alert-circle" style="width:18px;height:18px"></i></div>
                <div>
                    <div class="kpi-mini-val" style="color:#FCA5A5">{{ $overdue }}</div>
                    <div class="kpi-mini-label">Quá hạn</div>
                </div>
            </div>
        </div>

        <!-- CONTENT GRID -->
        <div class="content-grid">
            <!-- TASK LIST -->
            <div class="tasks-card">
                <div class="tasks-header">
                    <span class="tasks-title">📋 Công việc của tôi</span>
                    <div class="filter-tabs">
                        <button class="tab-btn active" onclick="filterTasks('all', this)">Tất cả</button>
                        <button class="tab-btn" onclick="filterTasks('doing', this)">Đang làm</button>
                        <button class="tab-btn" onclick="filterTasks('done', this)">Xong</button>
                        <button class="tab-btn" onclick="filterTasks('overdue', this)">Quá hạn</button>
                    </div>
                </div>

                <div id="task-list">
                    @forelse($mappedTasks as $t)
                        @php
                            $sc = match($t['status']) {
                                'Hoàn thành' => 'done', 'Đang làm' => 'doing',
                                'Đang review' => 'review', 'Quá hạn' => 'overdue',
                                default => 'pending'
                            };
                            $progressColor = match($t['status']) {
                                'Hoàn thành' => '#16A34A', 'Quá hạn' => '#E63946',
                                'Đang làm' => '#7C3AED', default => '#94A3B8'
                            };
                        @endphp
                        <div class="task-item" data-status="{{ $sc }}" id="task-{{ $t['id'] }}">
                            <!-- Status indicator -->
                            <div class="task-check {{ $t['status'] === 'Hoàn thành' ? 'done' : ($t['status'] === 'Đang làm' ? 'doing' : '') }}">
                                @if($t['status'] === 'Hoàn thành')
                                    <i data-lucide="check" style="width:13px;height:13px"></i>
                                @endif
                            </div>

                            <!-- Info -->
                            <div class="task-info">
                                <div class="task-name {{ $t['status'] === 'Hoàn thành' ? 'done-text' : '' }}">
                                    {{ $t['name'] }}
                                </div>
                                <div class="task-meta">
                                    <span class="pill {{ $sc }}">{{ $t['status'] }}</span>
                                    <span class="task-deadline {{ ($t['days_left'] !== null && $t['days_left'] <= 2 && $t['status'] !== 'Hoàn thành') ? 'urgent' : '' }}">
                                        <i data-lucide="calendar" style="width:11px;height:11px"></i>
                                        {{ $t['deadline'] }}
                                        @if($t['days_left'] !== null && $t['status'] !== 'Hoàn thành')
                                            @if($t['days_left'] < 0)
                                                <span style="color:#E63946">(quá {{ abs($t['days_left']) }} ngày)</span>
                                            @elseif($t['days_left'] <= 2)
                                                <span style="color:#D97706">(còn {{ $t['days_left'] }} ngày)</span>
                                            @endif
                                        @endif
                                    </span>
                                </div>
                            </div>

                            <!-- Progress -->
                            <div class="task-progress-wrap">
                                <div class="task-progress-track">
                                    <div class="task-progress-fill" id="bar-{{ $t['id'] }}"
                                         style="width:{{ $t['progress'] }}%;background:{{ $progressColor }}"></div>
                                </div>
                                <span style="font-size:11px;font-weight:700;color:#374151;min-width:30px" id="pct-{{ $t['id'] }}">{{ $t['progress'] }}%</span>
                            </div>

                            <!-- Status control -->
                            <select class="status-select"
                                    onchange="updateTask({{ $t['id'] }}, this.value)"
                                    style="color:{{ $t['status'] === 'Hoàn thành' ? '#16A34A' : ($t['status'] === 'Đang làm' ? '#D97706' : '#6B7280') }}">
                                <option value="Chờ xử lý"  {{ $t['status'] === 'Chờ xử lý'  ? 'selected' : '' }}>Chờ xử lý</option>
                                <option value="Đang làm"   {{ $t['status'] === 'Đang làm'   ? 'selected' : '' }}>Đang làm</option>
                                <option value="Đang review"{{ $t['status'] === 'Đang review' ? 'selected' : '' }}>Đang review</option>
                                <option value="Hoàn thành" {{ $t['status'] === 'Hoàn thành' ? 'selected' : '' }}>Hoàn thành</option>
                            </select>
                        </div>
                    @empty
                        <div class="empty-state">
                            <i data-lucide="inbox" style="width:48px;height:48px;color:#C4B5FD"></i>
                            <p>Bạn chưa có công việc nào được giao</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- PROFILE + NOTIFS -->
            <div>
                <div class="profile-card" style="margin-bottom:16px">
                    <div class="profile-title">👤 Thông tin cá nhân</div>
                    <div class="profile-row">
                        <span class="label">Họ tên</span>
                        <span class="value">{{ Auth::user()->name }}</span>
                    </div>
                    <div class="profile-row">
                        <span class="label">Email</span>
                        <span class="value" style="font-size:12px">{{ Auth::user()->email }}</span>
                    </div>
                    <div class="profile-row">
                        <span class="label">Phòng ban</span>
                        <span class="value">{{ Auth::user()->department->TENPHONG ?? '—' }}</span>
                    </div>
                    <div class="profile-row">
                        <span class="label">Chức vụ</span>
                        <span class="value">Nhân viên</span>
                    </div>
                    <div class="profile-row">
                        <span class="label">Tổng công việc</span>
                        <span class="value">{{ $total }}</span>
                    </div>
                    <div class="profile-row">
                        <span class="label">Hoàn thành</span>
                        <span class="value" style="color:#16A34A">{{ $completionRate }}%</span>
                    </div>
                </div>

                @if($notifications->count() > 0)
                    <div class="profile-card">
                        <div class="profile-title">🔔 Thông báo mới</div>
                        @foreach($notifications as $n)
                            <div style="padding:10px 0;border-bottom:1px solid #F1F5F9;font-size:13px">
                                <div style="font-weight:600;color:#0F172A;margin-bottom:2px">{{ $n->title }}</div>
                                <div style="color:#94A3B8;font-size:11px">{{ $n->created_at->diffForHumans() }}</div>
                            </div>
                        @endforeach
                        <a href="{{ route('dashboard.notifications') }}" style="display:block;margin-top:12px;font-size:12px;font-weight:600;color:#7C3AED;text-decoration:none">Xem tất cả →</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Toast -->
<div class="toast" id="toast-live"></div>

<script>
    lucide.createIcons();

    // Auto-hide session toast
    setTimeout(() => {
        const t = document.getElementById('toast');
        if (t) t.classList.remove('show');
    }, 3500);

    // Filter tasks
    function filterTasks(status, btn) {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.task-item').forEach(item => {
            item.style.display = (status === 'all' || item.dataset.status === status) ? '' : 'none';
        });
    }

    // Update task status via AJAX
    function updateTask(id, status) {
        const progressMap = { 'Chờ xử lý': 0, 'Đang làm': 50, 'Đang review': 80, 'Hoàn thành': 100 };
        const progress = progressMap[status] ?? 0;

        fetch(`/employee/tasks/${id}/progress`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ status, progress })
        })
        .then(r => r.json())
        .then(data => {
            if (data.ok) {
                // Update progress bar
                const bar = document.getElementById('bar-' + id);
                const pct = document.getElementById('pct-' + id);
                if (bar) { bar.style.width = data.progress + '%'; }
                if (pct) { pct.textContent = data.progress + '%'; }

                // Reinit icons
                lucide.createIcons();

                // Show toast
                showToast('✓ Đã cập nhật: ' + status);
            }
        })
        .catch(() => showToast('⚠ Không thể kết nối máy chủ'));
    }

    function showToast(msg) {
        const t = document.getElementById('toast-live');
        t.textContent = msg; t.classList.add('show');
        setTimeout(() => t.classList.remove('show'), 3000);
    }
</script>
</body>
</html>
