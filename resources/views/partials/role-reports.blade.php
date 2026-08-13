@php
    $change = $summary['monthly_task_change'] ?? 0;
    $changeText = $change > 0 ? '+' . $change . '%' : $change . '%';
    $lineMax = max(1, collect($lineData)->flatMap(fn ($row) => [$row['assigned'], $row['completed']])->max() ?? 1);
    $statusTotal = max(1, $summary['total']);
@endphp

<style>
    .report-page { display:grid; gap:20px; }
    .report-hero { position:relative; overflow:hidden; display:grid; grid-template-columns:1fr auto; gap:22px; align-items:end; padding:28px; border:1px solid #B9CDF5; border-radius:8px; background:#001F5B; color:#fff; }
    .report-hero:before { content:""; position:absolute; inset:0; opacity:.18; background-image:linear-gradient(rgba(255,255,255,.22) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.22) 1px,transparent 1px); background-size:42px 42px; }
    .report-hero:after { content:""; position:absolute; right:-56px; top:-76px; width:250px; height:250px; border:34px solid rgba(255,255,255,.11); border-radius:999px; }
    .report-hero > * { position:relative; z-index:1; }
    .kicker { display:inline-flex; align-items:center; gap:8px; border-radius:999px; padding:7px 12px; background:rgba(255,255,255,.1); color:#B9CDF5; font-size:11px; font-weight:900; letter-spacing:.18em; text-transform:uppercase; }
    .kicker:before { content:""; width:8px; height:8px; border-radius:999px; background:#E4002B; }
    .report-hero h2 { margin:14px 0 10px; font-size:36px; line-height:1.08; font-weight:900; letter-spacing:-.03em; }
    .report-hero p { color:rgba(255,255,255,.74); line-height:1.65; }
    .hero-mini { display:grid; grid-template-columns:repeat(2,160px); gap:12px; }
    .hero-mini-card { border:1px solid rgba(255,255,255,.18); border-radius:8px; padding:16px; background:rgba(255,255,255,.1); }
    .hero-mini-card span { display:block; color:rgba(255,255,255,.56); font-size:10px; font-weight:900; letter-spacing:.1em; text-transform:uppercase; }
    .hero-mini-card strong { display:block; margin-top:8px; font-size:30px; line-height:1; font-weight:900; }
    .metric-grid { display:grid; grid-template-columns:repeat(4,minmax(0,1fr)); gap:16px; }
    .metric-card { border:1px solid #D9E5F7; border-radius:8px; background:#fff; padding:20px; box-shadow:0 12px 26px rgba(0,31,91,.05); }
    .metric-card.danger { border-color:#F7B7C3; }
    .metric-top { display:flex; justify-content:space-between; align-items:center; color:#003DA5; }
    .metric-card .icon { width:44px; height:44px; display:grid; place-items:center; border-radius:8px; background:#E8F0FE; }
    .metric-card.danger .icon { background:#FFF0F3; color:#E4002B; }
    .metric-card label { display:block; margin-top:18px; color:#94A3B8; font-size:11px; font-weight:900; letter-spacing:.08em; text-transform:uppercase; }
    .metric-card strong { display:block; margin-top:6px; color:#001F5B; font-size:38px; line-height:1; font-weight:900; }
    .metric-card.danger strong { color:#E4002B; }
    .panel-grid { display:grid; grid-template-columns:minmax(0,1fr) 420px; gap:18px; align-items:start; }
    .panel { border:1px solid #D9E5F7; border-radius:8px; background:#fff; padding:22px; box-shadow:0 12px 26px rgba(0,31,91,.05); }
    .panel h3 { color:#001F5B; font-size:20px; font-weight:900; }
    .panel p { margin-top:6px; color:#64748B; }
    .bars { margin-top:24px; display:grid; gap:14px; }
    .bar-row { display:grid; grid-template-columns:120px 1fr 44px; gap:12px; align-items:center; color:#64748B; font-size:13px; font-weight:800; }
    .bar-track { height:11px; border-radius:999px; background:#EDF2FB; overflow:hidden; }
    .bar-fill { height:100%; border-radius:inherit; background:#003DA5; }
    .bar-fill.red { background:#E4002B; }
    .bar-fill.green { background:#16A34A; }
    .daily-chart { margin-top:26px; display:flex; align-items:end; gap:10px; min-height:260px; border-bottom:1px solid #D9E5F7; }
    .day-col { flex:1; display:grid; grid-template-rows:1fr auto; gap:10px; height:260px; }
    .day-bars { display:flex; align-items:end; justify-content:center; gap:5px; height:220px; }
    .day-bar { width:16px; min-height:4px; border-radius:999px 999px 0 0; background:#003DA5; }
    .day-bar.done { background:#E4002B; }
    .day-label { text-align:center; color:#94A3B8; font-size:11px; font-weight:900; }
    .doc-list { margin-top:18px; display:grid; gap:10px; }
    .doc-item { display:grid; grid-template-columns:1fr auto; gap:10px; border:1px solid #E5EAF5; border-radius:8px; padding:12px; }
    .doc-item strong { color:#001F5B; font-size:13px; }
    .doc-item span { display:block; margin-top:4px; color:#64748B; font-size:12px; }
    .doc-pill { align-self:start; border-radius:999px; padding:5px 10px; background:#E8F0FE; color:#003DA5; font-size:11px; font-weight:900; }
    @media (max-width:1180px) { .metric-grid { grid-template-columns:repeat(2,minmax(0,1fr)); } .panel-grid { grid-template-columns:1fr; } }
    @media (max-width:760px) { .report-hero { grid-template-columns:1fr; } .hero-mini { grid-template-columns:1fr 1fr; } .metric-grid { grid-template-columns:1fr; } }
</style>

<div class="report-page">
    <section class="report-hero">
        <div>
            <div class="kicker">Báo cáo MobiFone WorkHub</div>
            <h2>Báo cáo vận hành toàn WorkHub</h2>
            <p>Dữ liệu được tổng hợp trực tiếp từ công việc, phòng ban, nhân viên và tài liệu trong hệ thống.</p>
        </div>
        <div class="hero-mini">
            <div class="hero-mini-card"><span>Nhân sự hoạt động</span><strong>{{ $summary['active_users'] }}</strong></div>
            <div class="hero-mini-card"><span>File nhân viên tải lên</span><strong>{{ $summary['documents'] }}</strong></div>
        </div>
    </section>

    <section class="metric-grid">
        <div class="metric-card">
            <div class="metric-top"><span class="icon"><i data-lucide="briefcase"></i></span><span style="font-weight:900;color:{{ $change >= 0 ? '#16A34A' : '#E4002B' }}">{{ $changeText }} tháng này</span></div>
            <label>Tổng công việc</label>
            <strong>{{ $summary['total'] }}</strong>
        </div>
        <div class="metric-card">
            <div class="metric-top"><span class="icon"><i data-lucide="check-circle-2"></i></span></div>
            <label>Tỷ lệ hoàn thành</label>
            <strong>{{ $summary['done_rate'] }}%</strong>
        </div>
        <div class="metric-card">
            <div class="metric-top"><span class="icon"><i data-lucide="timer"></i></span></div>
            <label>Thời gian TB hoàn thành</label>
            <strong>{{ $summary['avg_completion_days'] }}<span style="font-size:16px;color:#94A3B8"> ngày</span></strong>
        </div>
        <div class="metric-card danger">
            <div class="metric-top"><span class="icon"><i data-lucide="alert-triangle"></i></span></div>
            <label>Quá hạn</label>
            <strong>{{ $summary['overdue_rate'] }}%</strong>
        </div>
    </section>

    <section class="panel-grid">
        <div class="panel">
            <h3>Nhịp vận hành 7 ngày</h3>
            <p>So sánh số việc được tạo và số việc hoàn thành theo ngày.</p>
            <div class="daily-chart">
                @foreach($lineData as $day)
                    <div class="day-col">
                        <div class="day-bars">
                            <div class="day-bar" style="height:{{ max(4, ($day['assigned'] / $lineMax) * 210) }}px"></div>
                            <div class="day-bar done" style="height:{{ max(4, ($day['completed'] / $lineMax) * 210) }}px"></div>
                        </div>
                        <div class="day-label">{{ $day['label'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="panel">
            <h3>Trạng thái toàn hệ thống</h3>
            <p>Tỷ trọng công việc theo trạng thái hiện tại.</p>
            <div class="bars">
                <div class="bar-row"><span>Chờ xử lý</span><div class="bar-track"><div class="bar-fill" style="width:{{ ($summary['pending'] / $statusTotal) * 100 }}%"></div></div><strong>{{ $summary['pending'] }}</strong></div>
                <div class="bar-row"><span>Đang làm</span><div class="bar-track"><div class="bar-fill" style="width:{{ ($summary['in_progress'] / $statusTotal) * 100 }}%"></div></div><strong>{{ $summary['in_progress'] }}</strong></div>
                <div class="bar-row"><span>Hoàn thành</span><div class="bar-track"><div class="bar-fill green" style="width:{{ ($summary['completed'] / $statusTotal) * 100 }}%"></div></div><strong>{{ $summary['completed'] }}</strong></div>
                <div class="bar-row"><span>Quá hạn</span><div class="bar-track"><div class="bar-fill red" style="width:{{ ($summary['overdue'] / $statusTotal) * 100 }}%"></div></div><strong>{{ $summary['overdue'] }}</strong></div>
            </div>
        </div>
    </section>

    <section class="panel-grid">
        <div class="panel">
            <h3>Hiệu suất theo thành viên</h3>
            <div class="bars">
                @forelse($perfData as $person)
                    @php($completionRate = $person['completion'] ?? $person['done_rate'] ?? 0)
                    <div class="bar-row">
                        <span>{{ $person['name'] }}</span>
                        <div class="bar-track"><div class="bar-fill green" style="width:{{ $completionRate }}%"></div></div>
                        <strong>{{ $completionRate }}%</strong>
                    </div>
                @empty
                    <p>Chưa có dữ liệu thành viên.</p>
                @endforelse
            </div>
        </div>

        <div class="panel">
            <h3>Tài liệu nhân viên tải lên</h3>
            <p>Tổng hợp các file đang gắn với công việc trong WorkHub.</p>
            <div class="doc-list">
                @forelse($recentEmployeeDocuments as $doc)
                    <div class="doc-item">
                        <div>
                            <strong>{{ $doc['file_name'] }}</strong>
                            <span>{{ $doc['uploader'] }} - {{ $doc['department'] }} - {{ $doc['task_code'] }}</span>
                        </div>
                        <span class="doc-pill">{{ $doc['file_type'] }}</span>
                    </div>
                @empty
                    <p>Chưa có tài liệu nhân viên tải lên.</p>
                @endforelse
            </div>
        </div>
    </section>
</div>
