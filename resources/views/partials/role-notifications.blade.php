@php
    $isAdminPage = request()->routeIs('admin.*');
    $routePrefix = $isAdminPage ? 'admin' : 'manager';
    $notificationRoute = $routePrefix . '.notifications';
    $markAllRoute = $routePrefix . '.notifications.markAllRead';
    $broadcastRoute = $routePrefix . '.notifications.broadcast';
    $openRoute = $routePrefix . '.notifications.open';
    $totalNotifications = $notifications->count();
    $taskCount = $notificationTypeCounts['task'] ?? 0;
    $deadlineCount = $notificationTypeCounts['deadline'] ?? 0;
@endphp

<style>
    .notif-page { display:grid; grid-template-columns:minmax(0,1fr) 360px; gap:18px; align-items:start; }
    .notif-hero { grid-column:1 / -1; display:flex; justify-content:space-between; gap:18px; padding:26px; border:1px solid #B9CDF5; border-radius:8px; background:linear-gradient(135deg,#F8FBFF 0%,#EEF5FF 66%,#FFF6F8 100%); box-shadow:inset 5px 0 0 #E4002B; }
    .notif-hero h2 { margin:6px 0; color:#001F5B; font-size:34px; line-height:1.08; font-weight:900; }
    .kicker { color:#003DA5; font-size:12px; font-weight:900; letter-spacing:.14em; text-transform:uppercase; }
    .notif-hero p { color:#52637A; line-height:1.6; }
    .hero-counts { display:grid; grid-template-columns:repeat(2,130px); gap:10px; }
    .hero-count { border:1px solid #D4E0F7; border-radius:8px; padding:14px; background:#fff; }
    .hero-count span { color:#64748B; font-size:10px; font-weight:900; letter-spacing:.1em; text-transform:uppercase; }
    .hero-count strong { display:block; margin-top:8px; color:#003DA5; font-size:30px; line-height:1; font-weight:900; }
    .panel { overflow:hidden; border:1px solid #D4E0F7; border-radius:8px; background:#fff; box-shadow:0 16px 30px rgba(0,31,91,.05); }
    .panel-head { display:flex; justify-content:space-between; align-items:center; gap:12px; padding:16px 18px; border-bottom:1px solid #E5EAF5; }
    .panel-title { display:flex; align-items:center; gap:10px; color:#001F5B; font-size:17px; font-weight:900; }
    .small-action { border:0; background:transparent; color:#003DA5; font-size:12px; font-weight:900; cursor:pointer; }
    .notif-list { display:grid; }
    .notif-item { width:100%; display:grid; grid-template-columns:42px minmax(0,1fr) auto; gap:14px; align-items:start; border:0; border-bottom:1px solid #EEF2F7; padding:16px 18px; background:#fff; text-align:left; cursor:pointer; }
    .notif-item:hover { background:#F7FAFF; }
    .notif-item.unread { background:linear-gradient(90deg,rgba(0,61,165,.08),#fff 45%); }
    .notif-icon { width:42px; height:42px; display:grid; place-items:center; border-radius:10px; background:#E8F0FE; color:#003DA5; box-shadow:inset 4px 0 0 #E4002B; }
    .notif-icon.overdue { background:#FFF0F3; color:#E4002B; }
    .notif-icon.deadline { background:#FFFBEB; color:#B45309; }
    .notif-title { color:#001F5B; font-size:14px; font-weight:900; line-height:1.35; }
    .notif-desc { margin-top:4px; color:#64748B; font-size:12px; line-height:1.45; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .notif-time { margin-top:8px; color:#94A3B8; font-size:11px; font-weight:800; }
    .unread-dot { width:9px; height:9px; border-radius:999px; background:#E4002B; margin-top:8px; }
    .empty-state { padding:48px 18px; text-align:center; color:#64748B; }
    .broadcast { padding:18px; display:grid; gap:12px; }
    .broadcast label { display:block; margin-bottom:6px; color:#64748B; font-size:12px; font-weight:900; }
    .broadcast input, .broadcast textarea { width:100%; border:1px solid #D4E0F7; border-radius:8px; padding:12px; outline:none; }
    .broadcast textarea { min-height:130px; resize:vertical; }
    .primary-action { display:inline-flex; align-items:center; justify-content:center; gap:8px; border:0; border-radius:8px; padding:12px 16px; background:#003DA5; color:#fff; font-weight:900; cursor:pointer; }
    .metric-grid { display:grid; grid-template-columns:1fr 1fr; gap:10px; padding:18px; }
    .metric { border-radius:8px; padding:14px; background:#F4F8FF; }
    .metric span { color:#64748B; font-size:10px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; }
    .metric strong { display:block; margin-top:7px; color:#001F5B; font-size:24px; font-weight:900; }
    @media (max-width:1050px) { .notif-page { grid-template-columns:1fr; } .notif-hero { flex-direction:column; } }
</style>

<div class="notif-page">
    <section class="notif-hero">
        <div>
            <div class="kicker">{{ $isAdminPage ? 'Hộp thư điều hành' : 'Hộp thư phòng ban' }}</div>
            <h2>Thông báo nội bộ</h2>
            <p>Nhận thông báo giao việc, nhắc deadline và gửi thông báo hệ thống trong đúng không gian {{ $isAdminPage ? 'Giám đốc' : 'Trưởng phòng' }}.</p>
        </div>
        <div class="hero-counts">
            <div class="hero-count"><span>Chưa đọc</span><strong>{{ $unreadCount }}</strong></div>
            <div class="hero-count"><span>Tổng số</span><strong>{{ $totalNotifications }}</strong></div>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <div class="panel-title"><i data-lucide="inbox" style="width:20px;height:20px"></i>Hộp thông báo</div>
            @if($notifications->isNotEmpty())
                <form method="POST" action="{{ route($markAllRoute) }}">
                    @csrf
                    <button type="submit" class="small-action">Đánh dấu tất cả đã đọc</button>
                </form>
            @endif
        </div>
        <div class="notif-list">
            @forelse($notifications as $n)
                @php
                    $icon = match($n['type']) {
                        'overdue' => 'alert-triangle',
                        'deadline' => 'clock',
                        'complete' => 'check-circle-2',
                        default => 'bell',
                    };
                @endphp
                <form method="POST" action="{{ route($openRoute, $n['id']) }}" id="role-notif-form-{{ $n['id'] }}">
                    @csrf
                    <div class="notif-item {{ !$n['read'] ? 'unread' : '' }}" onclick="document.getElementById('role-notif-form-{{ $n['id'] }}').submit()">
                        <span class="notif-icon {{ $n['type'] }}"><i data-lucide="{{ $icon }}" style="width:20px;height:20px"></i></span>
                        <span>
                            <span class="notif-title">{{ $n['title'] }}</span>
                            <span class="notif-desc">{{ $n['desc'] }}</span>
                            <span class="notif-time">{{ $n['time'] }}</span>

                            @if(!empty($n['can_accept']) && !empty($n['task_id']))
                                <div style="display:flex;align-items:center;gap:8px;margin-top:8px" onclick="event.stopPropagation()">
                                    <button type="button"
                                        onclick="roleAcceptTask({{ $n['task_id'] }}, '{{ addslashes($n['task_name'] ?? '') }}')"
                                        style="display:inline-flex;align-items:center;gap:4px;padding:6px 12px;border-radius:6px;background:#16A34A;color:#fff;border:none;font-size:12px;font-weight:800;cursor:pointer">
                                        <i data-lucide="check" style="width:13px;height:13px"></i>
                                        Nhận việc
                                    </button>
                                    <button type="button"
                                        onclick="roleOpenDeclineModal({{ $n['task_id'] }}, '{{ addslashes($n['task_name'] ?? '') }}')"
                                        style="display:inline-flex;align-items:center;gap:4px;padding:5px 11px;border-radius:6px;background:#FEF2F2;color:#DC2626;border:1px solid #FECACA;font-size:12px;font-weight:800;cursor:pointer">
                                        <i data-lucide="x" style="width:13px;height:13px"></i>
                                        Không nhận
                                    </button>
                                </div>
                            @endif
                        </span>
                        @if(!$n['read'])<span class="unread-dot"></span>@endif
                    </div>
                </form>
            @empty
                <div class="empty-state">
                    <i data-lucide="bell-off" style="width:44px;height:44px"></i>
                    <div style="margin-top:12px;font-weight:900;color:#001F5B">Chưa có thông báo nào.</div>
                </div>
            @endforelse
        </div>
    </section>

    {{-- Decline Modal for Role Notifications --}}
    <div id="role-decline-modal" style="display:none;position:fixed;inset:0;background:rgba(0,31,91,.45);backdrop-filter:blur(4px);z-index:999;place-items:center;padding:20px">
        <div style="width:min(460px,100%);background:#fff;border-radius:12px;box-shadow:0 24px 48px rgba(0,0,0,.2);overflow:hidden">
            <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid #EFF4FD;background:#F8FAFF">
                <h3 style="font-size:15px;font-weight:900;color:#001F5B;margin:0">Từ chối nhận công việc</h3>
                <button type="button" onclick="roleCloseDeclineModal()" style="background:none;border:none;cursor:pointer;color:#64748B"><i data-lucide="x" style="width:18px;height:18px"></i></button>
            </div>
            <form id="role-decline-form" method="POST" action="">
                @csrf
                <div style="padding:20px">
                    <p id="role-decline-task-name" style="font-weight:800;color:#001F5B;margin-bottom:12px;font-size:13px"></p>
                    <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:700;color:#475569">Lý do từ chối (tùy chọn):</label>
                    <textarea id="role-reason" name="reason" rows="3" style="width:100%;border:1px solid #D4E0F7;border-radius:8px;padding:8px 10px;font-family:inherit;font-size:13px;resize:vertical;outline:none;box-sizing:border-box" placeholder="Nhập lý do không thể nhận việc..."></textarea>
                </div>
                <div style="padding:12px 20px;border-top:1px solid #EFF4FD;background:#F8FAFF;display:flex;justify-content:flex-end;gap:10px">
                    <button type="button" onclick="roleCloseDeclineModal()" style="padding:7px 14px;border-radius:6px;background:#fff;border:1px solid #D4E0F7;color:#64748B;font-weight:700;font-size:12px;cursor:pointer">Hủy</button>
                    <button type="submit" style="padding:7px 14px;border-radius:6px;background:#DC2626;border:none;color:#fff;font-weight:800;font-size:12px;cursor:pointer">Xác nhận không nhận</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function roleAcceptTask(taskId, taskName) {
            if (!confirm(`Bạn có chắc chắn muốn nhận công việc "${taskName || 'này'}"?`)) return;

            fetch(`{{ url('/employee/tasks') }}/${taskId}/accept`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(res => res.json())
            .then(data => {
                if (data.ok) {
                    alert(data.message || 'Đã tiếp nhận công việc!');
                    window.location.reload();
                } else {
                    alert(data.message || 'Có lỗi xảy ra.');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Không thể tiếp nhận công việc. Vui lòng thử lại.');
            });
        }

        function roleOpenDeclineModal(taskId, taskName) {
            const modal = document.getElementById('role-decline-modal');
            const form = document.getElementById('role-decline-form');
            const nameEl = document.getElementById('role-decline-task-name');
            if (nameEl) nameEl.textContent = taskName ? `Công việc: ${taskName}` : '';
            if (form) form.action = `{{ url('/employee/tasks') }}/${taskId}/decline`;
            if (modal) modal.style.display = 'grid';
        }

        function roleCloseDeclineModal() {
            const modal = document.getElementById('role-decline-modal');
            if (modal) modal.style.display = 'none';
        }
    </script>

    <aside class="panel">
        @if($canBroadcastNotifications)
            <div class="panel-head">
                <div class="panel-title"><i data-lucide="send" style="width:20px;height:20px"></i>Gửi thông báo</div>
            </div>
            <form class="broadcast" method="POST" action="{{ route($broadcastRoute) }}">
                @csrf
                <div>
                    <label>Tiêu đề</label>
                    <input name="title" required placeholder="VD: Lịch bảo trì hệ thống">
                </div>
                <div>
                    <label>Nội dung</label>
                    <textarea name="message" required placeholder="Nhập nội dung cần thông báo..."></textarea>
                </div>
                <button type="submit" class="primary-action"><i data-lucide="send" style="width:16px;height:16px"></i>Gửi đến toàn bộ nhân viên</button>
            </form>
        @endif
        <div class="panel-head">
            <div class="panel-title"><i data-lucide="bar-chart-3" style="width:20px;height:20px"></i>Thống kê</div>
        </div>
        <div class="metric-grid">
            <div class="metric"><span>Công việc</span><strong>{{ $taskCount }}</strong></div>
            <div class="metric"><span>Deadline</span><strong>{{ $deadlineCount }}</strong></div>
        </div>
    </aside>
</div>
