<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>WorkHub Nhân viên - MobiFone HR</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --mf-blue: #003DA5;
            --mf-blue-dark: #001F5B;
            --mf-red: #E4002B;
            --mf-red-light: #FEF2F2;
            --mf-light: #EEF3FC;
            --mf-border: #D4E0F7;
            --bg: #F0F4FB;
            --white: #ffffff;
            --text: #0D1B3E;
            --text-muted: #64748B;
            --sidebar-w: 272px;
        }
        body { min-height: 100vh; font-family: 'Be Vietnam Pro', sans-serif; background: var(--bg); color: var(--text); }

        /* ======= LAYOUT ======= */
        .layout { min-height: 100vh; display: grid; grid-template-columns: var(--sidebar-w) 1fr; }

        /* ======= SIDEBAR ======= */
        .sidebar {
            background: linear-gradient(180deg, #001F5B 0%, #003DA5 100%);
            color: #fff;
            display: flex;
            flex-direction: column;
            position: sticky;
            top: 0;
            height: 100vh;
            overflow-y: auto;
        }
        .sidebar-brand {
            padding: 22px 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,.12);
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .brand-logo {
            width: 38px; height: 38px; border-radius: 8px;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; font-weight: 900; letter-spacing: -.02em; color: var(--mf-blue);
            flex-shrink: 0;
            box-shadow: inset 5px 0 0 var(--mf-red);
        }
        .mf-logo-word { display: inline-flex; align-items: baseline; background: #fff; border-radius: 7px; padding: 4px 9px; line-height: 1; box-shadow: 0 6px 18px rgba(0,0,0,.12); }
        .mf-logo-word .blue { color: var(--mf-blue); font-size: 17px; font-weight: 900; letter-spacing: -.03em; }
        .mf-logo-word .red { color: var(--mf-red); font-size: 17px; font-weight: 900; letter-spacing: -.03em; }
        .brand-sub { color: #BFD8FF; font-size: 10px; margin-top: 6px; letter-spacing: .12em; text-transform: uppercase; font-weight: 700; }

        /* Profile Card */
        .profile-card {
            margin: 18px 14px;
            background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.14);
            border-radius: 12px;
            padding: 16px;
            backdrop-filter: blur(8px);
            display: block;
            color: inherit;
            text-decoration: none;
        }
        .profile-card:hover { background: rgba(255,255,255,.13); }
        .profile-row { display: flex; align-items: center; gap: 12px; }
        .avatar {
            width: 46px; height: 46px; border-radius: 10px;
            background: linear-gradient(135deg, #E4002B, #FF5A7A);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 15px; flex-shrink: 0;
        }
        .profile-info .name { font-size: 14px; font-weight: 800; }
        .profile-info .meta { font-size: 11px; color: rgba(255,255,255,.6); margin-top: 3px; }
        .profile-dept {
            margin-top: 12px;
            display: flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,.1);
            border-radius: 8px; padding: 7px 10px;
            font-size: 12px; font-weight: 600; color: rgba(255,255,255,.85);
        }

        /* Stats */
        .sidebar-stats {
            padding: 0 14px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .stat-box {
            background: rgba(255,255,255,.06);
            border: 1px solid rgba(255,255,255,.1);
            border-radius: 10px;
            padding: 12px;
        }
        .stat-box.full { grid-column: 1/-1; }
        .stat-label { font-size: 10px; text-transform: uppercase; letter-spacing: .1em; color: rgba(255,255,255,.5); font-weight: 700; }
        .stat-val { font-size: 26px; font-weight: 900; margin-top: 4px; line-height: 1; }
        .stat-val.red { color: #FCA5A5; }
        .stat-val.green { color: #6EE7B7; }
        .stat-val.blue { color: #93C5FD; }

        /* Progress Ring */
        .progress-ring-wrap {
            display: flex; align-items: center; gap: 14px;
        }
        .ring-svg { transform: rotate(-90deg); }
        .ring-track { fill: none; stroke: rgba(255,255,255,.15); stroke-width: 5; }
        .ring-fill { fill: none; stroke: #6EE7B7; stroke-width: 5; stroke-linecap: round; transition: stroke-dashoffset .6s ease; }
        .ring-center { text-align: right; }
        .ring-pct { font-size: 22px; font-weight: 900; color: #6EE7B7; }
        .ring-sub { font-size: 10px; color: rgba(255,255,255,.5); margin-top: 2px; }

        /* Sidebar Nav */
        .sidebar-nav {
            margin: 14px 14px 0;
            display: flex;
            flex-direction: column;
            gap: 4px;
        }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            color: rgba(255,255,255,.7);
            text-decoration: none;
            font-size: 13px; font-weight: 600;
            border: none; background: transparent; cursor: pointer;
            font-family: inherit;
            width: 100%;
            transition: background .15s, color .15s;
            position: relative;
        }
        .nav-item:hover { background: rgba(255,255,255,.08); color: #fff; }
        .nav-item.active { background: #fff; color: var(--mf-blue); font-weight: 800; box-shadow: inset 3px 0 0 var(--mf-red); }
        .nav-badge {
            margin-left: auto;
            min-width: 20px; height: 20px; border-radius: 10px;
            background: var(--mf-red);
            color: #fff; font-size: 10px; font-weight: 800;
            display: flex; align-items: center; justify-content: center;
            padding: 0 5px;
        }

        /* Sidebar Footer */
        .sidebar-footer {
            margin-top: auto;
            padding: 14px;
            border-top: 1px solid rgba(255,255,255,.1);
        }
        .logout-btn {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            color: rgba(255,255,255,.55);
            border: none; background: transparent; cursor: pointer;
            font-family: inherit; font-size: 13px; font-weight: 600;
            width: 100%;
            transition: background .15s, color .15s;
        }
        .logout-btn:hover { background: rgba(228,0,43,.15); color: #FCA5A5; }

        /* ======= MAIN CONTENT ======= */
        .main { min-width: 0; display: flex; flex-direction: column; }

        /* Topbar */
        .topbar {
            position: sticky; top: 0; z-index: 30;
            background: rgba(240,244,251,.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid #D4E0F7;
            height: 60px;
            display: flex; align-items: center;
            padding: 0 24px;
            gap: 16px;
        }
        .topbar-title { font-size: 14px; font-weight: 800; color: var(--mf-blue-dark); flex: 1; }
        .topbar-date { font-size: 12px; color: var(--text-muted); font-weight: 500; }
        .topbar-bell {
            position: relative;
            width: 36px; height: 36px; border-radius: 8px;
            border: 1px solid var(--mf-border);
            background: var(--white);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; text-decoration: none; color: var(--text);
            transition: border-color .15s;
        }
        .topbar-bell:hover { border-color: var(--mf-blue); }
        .bell-dot {
            position: absolute; top: 6px; right: 6px;
            width: 8px; height: 8px; border-radius: 50%;
            background: var(--mf-red); border: 2px solid var(--bg);
        }

        /* Content Area */
        .content { padding: 22px 24px; display: flex; flex-direction: column; gap: 20px; flex: 1; }

        /* Hero Banner */
        .hero {
            background: linear-gradient(135deg, var(--mf-blue-dark) 0%, var(--mf-blue) 60%, #1a56c4 100%);
            border-radius: 14px;
            padding: 24px 28px;
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 20px;
            align-items: center;
            overflow: hidden;
            position: relative;
        }
        .hero::before {
            content: '';
            position: absolute; inset: 0;
            background-image: radial-gradient(circle at 80% 50%, rgba(255,255,255,.06) 0%, transparent 60%);
            pointer-events: none;
        }
        .hero-kicker { font-size: 10px; letter-spacing: .2em; text-transform: uppercase; color: rgba(255,255,255,.55); font-weight: 700; }
        .hero-title { font-size: 22px; font-weight: 900; color: #fff; margin-top: 6px; line-height: 1.3; }
        .hero-sub { font-size: 13px; color: rgba(255,255,255,.6); margin-top: 6px; line-height: 1.6; }
        .hero-actions { display: flex; gap: 10px; margin-top: 16px; flex-wrap: wrap; }
        .hero-btn {
            height: 36px; border-radius: 8px; padding: 0 14px;
            display: inline-flex; align-items: center; gap: 7px;
            font-size: 12px; font-weight: 800; cursor: pointer;
            font-family: inherit; text-decoration: none; border: 1px solid transparent;
            transition: opacity .15s;
        }
        .hero-btn:hover { opacity: .85; }
        .hero-btn.primary { background: var(--mf-red); color: #fff; border-color: var(--mf-red); }
        .hero-btn.ghost { background: rgba(255,255,255,.1); color: #fff; border-color: rgba(255,255,255,.2); }
        .hero-stats { display: flex; gap: 20px; }
        .hero-stat { text-align: center; }
        .hero-stat-val { font-size: 28px; font-weight: 900; color: #fff; line-height: 1; }
        .hero-stat-label { font-size: 10px; color: rgba(255,255,255,.55); margin-top: 4px; text-transform: uppercase; letter-spacing: .08em; font-weight: 700; }

        /* Deadline Alert */
        .deadline-alert {
            background: linear-gradient(135deg, #FFF7ED, #FEF3C7);
            border: 1px solid #FCD34D;
            border-radius: 12px;
            padding: 14px 18px;
            display: flex; align-items: center; gap: 12px;
            font-size: 13px; font-weight: 600; color: #92400E;
        }
        .deadline-alert i { color: #D97706; flex-shrink: 0; }

        /* Content Grid */
        .content-grid { display: grid; grid-template-columns: 1fr 340px; gap: 20px; align-items: start; }

        .dashboard-overview {
            display: grid;
            grid-template-columns: minmax(0, 1.15fr) minmax(320px, .85fr);
            gap: 20px;
            align-items: stretch;
        }
        .overview-panel { min-height: 220px; }
        .overview-date {
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 800;
            background: #F1F5FD;
            border-radius: 999px;
            padding: 5px 10px;
        }
        .overview-body {
            padding: 20px;
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 16px;
            height: calc(100% - 58px);
        }
        .focus-card {
            border-radius: 12px;
            padding: 18px;
            display: flex;
            gap: 14px;
            align-items: flex-start;
            border: 1px solid #D4E0F7;
            background:
                linear-gradient(135deg, rgba(0,61,165,.08), rgba(255,255,255,.96)),
                #fff;
        }
        .focus-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: var(--mf-blue);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: inset 4px 0 0 var(--mf-red);
        }
        .focus-icon i,
        .priority-item i,
        .shortcut-icon i,
        .shortcut-card > i,
        .priority-empty i { width: 18px; height: 18px; }
        .focus-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: var(--text-muted);
            font-weight: 800;
        }
        .focus-card h3 {
            margin-top: 4px;
            font-size: 42px;
            line-height: 1;
            color: var(--mf-blue-dark);
            font-weight: 900;
        }
        .focus-desc {
            margin-top: 10px;
            color: var(--text-muted);
            font-size: 13px;
            line-height: 1.6;
        }
        .mini-metrics {
            display: grid;
            gap: 10px;
        }
        .mini-metric {
            border-radius: 12px;
            border: 1px solid #E5EDF8;
            background: #F8FAFF;
            padding: 14px;
        }
        .mini-metric span {
            display: block;
            font-size: 11px;
            color: var(--text-muted);
            font-weight: 800;
        }
        .mini-metric strong {
            display: block;
            margin-top: 4px;
            font-size: 24px;
            line-height: 1;
            color: var(--mf-blue);
            font-weight: 900;
        }
        .mini-metric.danger strong { color: var(--mf-red); }
        .priority-body {
            padding: 14px;
            display: grid;
            gap: 10px;
        }
        .priority-item {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 12px;
            align-items: center;
            min-height: 64px;
            padding: 12px;
            border-radius: 12px;
            border: 1px solid #F4C2CC;
            background: #FFF7F9;
            color: var(--text);
            text-decoration: none;
        }
        .priority-item.calm {
            border-color: #D4E0F7;
            background: #F8FAFF;
        }
        .priority-item:hover {
            border-color: var(--mf-blue);
            background: #fff;
        }
        .priority-dot {
            width: 10px;
            height: 10px;
            border-radius: 999px;
            background: var(--mf-red);
            box-shadow: 0 0 0 5px rgba(228,0,43,.1);
        }
        .priority-item.calm .priority-dot {
            background: var(--mf-blue);
            box-shadow: 0 0 0 5px rgba(0,61,165,.1);
        }
        .priority-item strong {
            display: block;
            font-size: 13px;
            color: var(--mf-blue-dark);
            line-height: 1.35;
        }
        .priority-item small {
            display: block;
            margin-top: 4px;
            color: var(--text-muted);
            font-size: 11px;
            font-weight: 600;
        }
        .priority-item > i {
            color: var(--mf-blue);
        }
        .priority-empty {
            min-height: 138px;
            border-radius: 12px;
            border: 1px dashed #D4E0F7;
            background: #F8FAFF;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 18px;
            color: var(--text-muted);
            gap: 8px;
        }
        .priority-empty i { color: #16A34A; width: 28px; height: 28px; }
        .priority-empty strong { color: var(--mf-blue-dark); font-size: 14px; }
        .priority-empty span { font-size: 12px; line-height: 1.5; }
        .shortcut-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
        }
        .shortcut-card {
            min-height: 108px;
            border-radius: 14px;
            border: 1px solid #E5EDF8;
            background: #fff;
            color: var(--text);
            text-decoration: none;
            padding: 18px;
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 14px;
            align-items: center;
            transition: border-color .15s, transform .15s, box-shadow .15s;
        }
        .shortcut-card:hover {
            border-color: var(--mf-blue);
            transform: translateY(-2px);
            box-shadow: 0 18px 36px rgba(0,31,91,.08);
        }
        .shortcut-card.static {
            grid-template-columns: auto 1fr;
            pointer-events: none;
        }
        .shortcut-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        .shortcut-icon.blue { background: var(--mf-blue); }
        .shortcut-icon.red { background: var(--mf-red); }
        .shortcut-icon.navy { background: var(--mf-blue-dark); }
        .shortcut-card strong {
            display: block;
            color: var(--mf-blue-dark);
            font-size: 14px;
            line-height: 1.35;
        }
        .shortcut-card small {
            display: block;
            margin-top: 4px;
            color: var(--text-muted);
            font-size: 12px;
            line-height: 1.5;
        }
        .shortcut-card > i { color: var(--mf-blue); }
        .shortcut-card em {
            min-width: 22px;
            height: 22px;
            border-radius: 999px;
            background: var(--mf-red);
            color: #fff;
            font-style: normal;
            font-size: 11px;
            font-weight: 900;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 7px;
        }

        /* Panel */
        .panel {
            background: var(--white);
            border: 1px solid #E5EDF8;
            border-radius: 14px;
            overflow: hidden;
        }
        .panel-head {
            padding: 16px 20px;
            border-bottom: 1px solid #EFF4FD;
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
        }
        .panel-title {
            font-size: 14px; font-weight: 800; color: var(--mf-blue-dark);
            display: flex; align-items: center; gap: 8px;
        }
        .panel-title i { color: var(--mf-blue); }

        /* Tab Filter */
        .tab-group {
            display: flex; gap: 4px;
            background: var(--mf-light);
            border-radius: 8px; padding: 3px;
        }
        .tab-btn {
            height: 28px; border-radius: 6px; padding: 0 12px;
            background: transparent; border: none; cursor: pointer;
            font-family: inherit; font-size: 12px; font-weight: 700;
            color: var(--text-muted); transition: all .15s;
        }
        .tab-btn.active { background: #fff; color: var(--mf-blue); box-shadow: 0 1px 3px rgba(0,61,165,.12); }

        /* Task Card */
        .task-card {
            padding: 16px 20px;
            border-top: 1px solid #F1F5FD;
            display: flex; flex-direction: column; gap: 10px;
            transition: background .12s;
            position: relative;
        }
        .task-card:hover { background: #F8FAFF; }
        .task-card.overdue { border-left: 3px solid var(--mf-red); }
        .task-card.done { border-left: 3px solid #10B981; }
        .task-card.doing { border-left: 3px solid #3B82F6; }
        .task-card.pending { border-left: 3px solid #9CA3AF; }

        .task-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 12px; }
        .task-name { font-size: 14px; font-weight: 800; color: var(--mf-blue-dark); line-height: 1.4; }
        .task-code { font-family: monospace; font-size: 11px; color: var(--text-muted); background: #F1F5FD; border-radius: 5px; padding: 2px 6px; flex-shrink: 0; }
        .task-meta-row { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .task-chip {
            display: inline-flex; align-items: center; gap: 4px;
            height: 22px; padding: 0 8px; border-radius: 999px;
            font-size: 11px; font-weight: 700;
        }
        .chip-pending { background: #F3F4F6; color: #6B7280; }
        .chip-doing { background: #EFF6FF; color: #1D4ED8; }
        .chip-review { background: #F5F3FF; color: #6D28D9; }
        .chip-done { background: #F0FDF4; color: #15803D; }
        .chip-overdue { background: var(--mf-red-light); color: var(--mf-red); }
        .deadline-chip { font-size: 11px; color: var(--text-muted); display: flex; align-items: center; gap: 4px; }
        .deadline-chip.urgent { color: var(--mf-red); font-weight: 700; }

        /* Progress bar */
        .prog-wrap { display: flex; align-items: center; gap: 10px; }
        .prog-bar { flex: 1; height: 6px; background: #E8EFF9; border-radius: 999px; overflow: hidden; }
        .prog-fill { height: 100%; border-radius: 999px; transition: width .4s ease; }
        .prog-fill.blue { background: linear-gradient(90deg, #3B82F6, #003DA5); }
        .prog-fill.green { background: linear-gradient(90deg, #10B981, #059669); }
        .prog-fill.red { background: linear-gradient(90deg, #F87171, #E4002B); }
        .prog-pct { font-size: 12px; font-weight: 800; color: var(--mf-blue); min-width: 34px; text-align: right; }

        /* Task actions row */
        .task-actions { display: flex; align-items: center; gap: 10px; }
        .status-select {
            flex: 1;
            height: 34px;
            border: 1px solid #D4E0F7;
            border-radius: 8px;
            padding: 0 10px;
            background: #F8FAFF;
            font-family: inherit; font-size: 12px; font-weight: 700;
            color: var(--mf-blue-dark);
            cursor: pointer;
            outline: none;
            transition: border-color .15s;
        }
        .status-select:focus { border-color: var(--mf-blue); background: #fff; }
        .view-btn {
            height: 34px; padding: 0 12px; border-radius: 8px;
            border: 1px solid var(--mf-border);
            background: var(--white);
            color: var(--mf-blue); font-family: inherit; font-size: 12px; font-weight: 700;
            cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 6px;
            transition: background .12s, border-color .12s;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .view-btn:hover { background: var(--mf-light); border-color: var(--mf-blue); }

        .empty-state {
            padding: 40px 20px;
            text-align: center;
            color: var(--text-muted);
        }
        .empty-state i { opacity: .3; margin-bottom: 12px; }
        .empty-state p { font-size: 14px; }

        /* ======= NOTIFICATIONS PANEL ======= */
        .notif-item {
            display: flex; gap: 12px;
            padding: 14px 20px;
            border-top: 1px solid #F1F5FD;
            width: 100%; text-align: left;
            background: transparent; border-left: none; border-right: none; border-bottom: none;
            cursor: pointer; font-family: inherit;
            transition: background .12s;
        }
        .notif-item:hover { background: #F8FAFF; }
        .notif-item.unread { background: #F0F4FF; }
        .notif-icon {
            width: 36px; height: 36px; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .notif-icon.task { background: #EEF3FC; color: var(--mf-blue); }
        .notif-icon.alert { background: var(--mf-red-light); color: var(--mf-red); }
        .notif-icon.general { background: #F0FDF4; color: #15803D; }
        .notif-title { font-size: 13px; font-weight: 800; color: var(--mf-blue-dark); line-height: 1.3; }
        .notif-desc { font-size: 11px; color: var(--text-muted); margin-top: 3px; line-height: 1.5; }
        .notif-time { font-size: 10px; color: #9CA3AF; margin-top: 5px; }
        .notif-dot {
            width: 7px; height: 7px; border-radius: 50%;
            background: var(--mf-blue); flex-shrink: 0; margin-top: 5px;
        }

        /* Mark all read link */
        .mark-read-form { padding: 12px 20px; border-top: 1px solid #F1F5FD; }
        .mark-read-btn {
            font-size: 12px; font-weight: 700; color: var(--mf-blue);
            background: none; border: none; cursor: pointer; font-family: inherit;
            padding: 0; text-decoration: underline; text-underline-offset: 3px;
        }

        /* Toast */
        .toast {
            position: fixed; right: 20px; bottom: 20px; z-index: 9999;
            background: var(--mf-blue-dark); color: #fff;
            border-radius: 10px; padding: 12px 18px;
            font-size: 13px; font-weight: 700;
            display: flex; align-items: center; gap: 8px;
            box-shadow: 0 8px 30px rgba(0,31,91,.3);
            transform: translateY(80px); opacity: 0; transition: all .25s cubic-bezier(.34,1.56,.64,1);
        }
        .toast.show { transform: translateY(0); opacity: 1; }
        .toast.success { background: #065F46; }
        .toast.error { background: #991B1B; }

        /* Responsive */
        @media (max-width: 1200px) { .content-grid, .dashboard-overview { grid-template-columns: 1fr; } .shortcut-grid { grid-template-columns: 1fr; } }
        @media (max-width: 960px) { .layout { grid-template-columns: 1fr; } .sidebar { height: auto; position: static; } }
        @media (max-width: 640px) { .hero, .overview-body { grid-template-columns: 1fr; } .hero-stats { display: none; } .shortcut-card { grid-template-columns: auto 1fr; } .shortcut-card > i { display: none; } }
    </style>
</head>
<body>
<div class="layout">

    {{-- ======= SIDEBAR ======= --}}
    <aside class="sidebar">
        <div class="sidebar-brand">
            <div class="brand-logo">M</div>
            <div>
                <div class="mf-logo-word"><span class="blue">Mobi</span><span class="red">Fone</span></div>
                <div class="brand-sub">EMPLOYEE WORKHUB</div>
            </div>
        </div>

        <a href="{{ route('profile.show') }}" class="profile-card" title="Trang cá nhân">
            <div class="profile-row">
                <div class="avatar">{{ mb_strtoupper(mb_substr(Auth::user()->name ?? 'NV', 0, 2)) }}</div>
                <div class="profile-info">
                    <div class="name">{{ Auth::user()->name }}</div>
                    <div class="meta">{{ Auth::user()->email }}</div>
                </div>
            </div>
            <div class="profile-dept">
                <i data-lucide="building-2" style="width:13px;height:13px;flex-shrink:0"></i>
                {{ Auth::user()->department->TENPHONG ?? 'MobiFone' }}
            </div>
        </a>

        <div class="sidebar-stats">
            <div class="stat-box">
                <div class="stat-label">Tổng việc</div>
                <div class="stat-val">{{ $total }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Đang làm</div>
                <div class="stat-val blue">{{ $doing }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Hoàn thành</div>
                <div class="stat-val green">{{ $done }}</div>
            </div>
            <div class="stat-box">
                <div class="stat-label">Quá hạn</div>
                <div class="stat-val red">{{ $overdue }}</div>
            </div>
            {{-- Progress Ring --}}
            <div class="stat-box full" style="display:flex;align-items:center;gap:16px">
                <svg width="56" height="56" class="ring-svg">
                    @php $circumference = 2 * pi() * 22; $offset = $circumference - ($completionRate / 100) * $circumference; @endphp
                    <circle class="ring-track" cx="28" cy="28" r="22"/>
                    <circle class="ring-fill" cx="28" cy="28" r="22"
                        stroke-dasharray="{{ $circumference }}"
                        stroke-dashoffset="{{ $offset }}"
                    />
                </svg>
                <div>
                    <div class="ring-pct">{{ $completionRate }}%</div>
                    <div class="ring-sub">Tỉ lệ hoàn thành</div>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav" style="margin-top:16px">
            <a href="{{ route('employee.dashboard') }}" class="nav-item active">
                <i data-lucide="layout-dashboard" style="width:16px;height:16px;flex-shrink:0"></i>
                Dashboard
            </a>
            <a href="{{ route('employee.tasks') }}" class="nav-item">
                <i data-lucide="clipboard-list" style="width:16px;height:16px;flex-shrink:0"></i>
                Công việc của tôi
                @if($total > 0)<span class="nav-badge">{{ $total }}</span>@endif
            </a>
            <a href="{{ route('employee.notifications') }}" class="nav-item">
                <i data-lucide="bell" style="width:16px;height:16px;flex-shrink:0"></i>
                Thông báo
                @if($unreadCount > 0)<span class="nav-badge">{{ $unreadCount }}</span>@endif
            </a>
        </nav>

        <div class="sidebar-footer">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="logout-btn">
                    <i data-lucide="log-out" style="width:16px;height:16px;flex-shrink:0"></i>
                    Đăng xuất
                </button>
            </form>
        </div>
    </aside>

    {{-- ======= MAIN ======= --}}
    <div class="main">
        {{-- Topbar --}}
        <div class="topbar">
            <div class="topbar-title">
                Xin chào, {{ explode(' ', Auth::user()->name)[count(explode(' ', Auth::user()->name)) - 1] }}! 👋
            </div>
            <div class="topbar-date" id="topbar-date"></div>
            <a href="{{ route('employee.notifications') }}" class="topbar-bell" title="Thông báo">
                <i data-lucide="bell" style="width:17px;height:17px"></i>
                @if($unreadCount > 0)<span class="bell-dot"></span>@endif
            </a>
        </div>

        <div class="content">

            {{-- Hero --}}
            <div class="hero">
                <div>
                    <div class="hero-kicker">Personal Execution · MobiFone WorkHub</div>
                    <div class="hero-title">Việc của tôi, tiến độ của tôi.</div>
                    <div class="hero-sub">Cập nhật trạng thái công việc và theo dõi tiến độ cá nhân của bạn tại đây.</div>
                    <div class="hero-actions">
                        <a href="{{ route('employee.tasks') }}" class="hero-btn primary">
                            <i data-lucide="zap" style="width:14px;height:14px"></i>
                            Công việc ngay
                        </a>
                        <a href="{{ route('employee.notifications') }}" class="hero-btn ghost">
                            <i data-lucide="bell" style="width:14px;height:14px"></i>
                            Thông báo @if($unreadCount > 0)({{ $unreadCount }})@endif
                        </a>
                    </div>
                </div>
                <div class="hero-stats">
                    <div class="hero-stat">
                        <div class="hero-stat-val">{{ $total }}</div>
                        <div class="hero-stat-label">Tổng việc</div>
                    </div>
                    <div class="hero-stat" style="border-left:1px solid rgba(255,255,255,.15);padding-left:20px">
                        <div class="hero-stat-val" style="color:#6EE7B7">{{ $completionRate }}%</div>
                        <div class="hero-stat-label">Hoàn thành</div>
                    </div>
                    <div class="hero-stat" style="border-left:1px solid rgba(255,255,255,.15);padding-left:20px">
                        <div class="hero-stat-val" style="color:#FCA5A5">{{ $overdue }}</div>
                        <div class="hero-stat-label">Quá hạn</div>
                    </div>
                </div>
            </div>

            {{-- Deadline Alert --}}
            @php
                $urgentTasks = collect($mappedTasks)->filter(fn($t) => isset($t['days_left']) && $t['days_left'] !== null && $t['days_left'] <= 2 && $t['days_left'] >= 0 && !str_contains($t['status'], 'Hoàn'));
            @endphp
            @if($urgentTasks->isNotEmpty())
            <div class="deadline-alert">
                <i data-lucide="alert-triangle" style="width:18px;height:18px"></i>
                <span>
                    <strong>{{ $urgentTasks->count() }} công việc sắp hết hạn trong 2 ngày tới.</strong>
                    Hãy ưu tiên xử lý ngay để không bị quá hạn.
                </span>
            </div>
            @endif

            {{-- Overview Grid --}}
            @php
                $activeTasks = collect($mappedTasks)->filter(fn($t) => !str_contains($t['status'], 'Hoàn'));
                $nextTask = $activeTasks
                    ->sortBy(fn($t) => $t['deadline_raw'] ?: '9999-12-31')
                    ->first();
                $todayFocus = $urgentTasks->take(3);
            @endphp
            <div class="dashboard-overview">
                <section class="panel overview-panel">
                    <div class="panel-head">
                        <div class="panel-title">
                            <i data-lucide="activity"></i>
                            Tổng quan hôm nay
                        </div>
                        <span class="overview-date">{{ now()->format('d/m/Y') }}</span>
                    </div>
                    <div class="overview-body">
                        <div class="focus-card primary">
                            <div class="focus-icon"><i data-lucide="target"></i></div>
                            <div>
                                <p class="focus-label">Tiến độ cá nhân</p>
                                <h3>{{ $completionRate }}%</h3>
                                <p class="focus-desc">Bạn đã hoàn thành {{ $done }}/{{ $total }} công việc được giao.</p>
                            </div>
                        </div>
                        <div class="mini-metrics">
                            <div class="mini-metric">
                                <span>Chờ xử lý</span>
                                <strong>{{ $pending }}</strong>
                            </div>
                            <div class="mini-metric">
                                <span>Đang làm</span>
                                <strong>{{ $doing }}</strong>
                            </div>
                            <div class="mini-metric danger">
                                <span>Quá hạn</span>
                                <strong>{{ $overdue }}</strong>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="panel overview-panel">
                    <div class="panel-head">
                        <div class="panel-title">
                            <i data-lucide="flag"></i>
                            Ưu tiên cần chú ý
                        </div>
                    </div>
                    <div class="priority-body">
                        @if($todayFocus->isNotEmpty())
                            @foreach($todayFocus as $task)
                                <a href="{{ route('employee.task.detail', $task['id']) }}" class="priority-item">
                                    <span class="priority-dot"></span>
                                    <span>
                                        <strong>{{ $task['name'] }}</strong>
                                        <small>Deadline {{ $task['deadline'] }} · còn {{ $task['days_left'] }} ngày</small>
                                    </span>
                                    <i data-lucide="chevron-right"></i>
                                </a>
                            @endforeach
                        @elseif($nextTask)
                            <a href="{{ route('employee.task.detail', $nextTask['id']) }}" class="priority-item calm">
                                <span class="priority-dot"></span>
                                <span>
                                    <strong>{{ $nextTask['name'] }}</strong>
                                    <small>Việc tiếp theo · deadline {{ $nextTask['deadline'] }}</small>
                                </span>
                                <i data-lucide="chevron-right"></i>
                            </a>
                        @else
                            <div class="priority-empty">
                                <i data-lucide="check-circle-2"></i>
                                <strong>Không có việc cần xử lý ngay</strong>
                                <span>Trang công việc sẽ hiển thị khi bạn được giao nhiệm vụ mới.</span>
                            </div>
                        @endif
                    </div>
                </section>
            </div>

            <div class="shortcut-grid">
                <a href="{{ route('employee.tasks') }}" class="shortcut-card">
                    <span class="shortcut-icon blue"><i data-lucide="clipboard-list"></i></span>
                    <span>
                        <strong>Mở trang công việc</strong>
                        <small>Cập nhật trạng thái, xem chi tiết và upload tài liệu.</small>
                    </span>
                    <i data-lucide="arrow-right"></i>
                </a>
                <a href="{{ route('employee.notifications') }}" class="shortcut-card">
                    <span class="shortcut-icon red"><i data-lucide="bell"></i></span>
                    <span>
                        <strong>Mở hộp thông báo</strong>
                        <small>Theo dõi nhắc deadline và thông báo từ quản lý.</small>
                    </span>
                    @if($unreadCount > 0)<em>{{ $unreadCount }}</em>@endif
                    <i data-lucide="arrow-right"></i>
                </a>
                <div class="shortcut-card static">
                    <span class="shortcut-icon navy"><i data-lucide="shield-check"></i></span>
                    <span>
                        <strong>Không gian cá nhân</strong>
                        <small>Dashboard chỉ giữ phần tổng quan, các tác vụ nằm ở trang riêng.</small>
                    </span>
                </div>
            </div>
        </div>{{-- /content --}}
    </div>{{-- /main --}}
</div>

<div class="toast" id="toast-live"><i data-lucide="check-circle" style="width:15px;height:15px;flex-shrink:0"></i><span id="toast-msg"></span></div>

<script>
    lucide.createIcons();

    // Topbar date
    const d = new Date();
    const opts = { weekday:'long', year:'numeric', month:'long', day:'numeric' };
    document.getElementById('topbar-date').textContent = d.toLocaleDateString('vi-VN', opts);

    // Scroll to anchor
    function scrollTo(id) {
        const el = document.getElementById(id);
        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const id = a.getAttribute('href').slice(1);
            const el = document.getElementById(id);
            if (el) { e.preventDefault(); el.scrollIntoView({ behavior: 'smooth', block: 'start' }); }
        });
    });

    // Filter tasks
    function filterTasks(status, btn) {
        document.querySelectorAll('.tab-btn').forEach(t => t.classList.remove('active'));
        btn.classList.add('active');
        document.querySelectorAll('.task-card').forEach(card => {
            card.style.display = (status === 'all' || card.dataset.status === status) ? 'flex' : 'none';
        });
    }
    // Set task-card display to flex initially
    document.querySelectorAll('.task-card').forEach(c => c.style.display = 'flex');
    document.querySelectorAll('.task-card').forEach(c => c.style.flexDirection = 'column');

    // Update task status
    function updateTask(id, status) {
        const progressMap = { 'Chờ xử lý': 0, 'Đang làm': 50, 'Đang review': 80, 'Hoàn thành': 100 };
        const progress = progressMap[status] ?? 0;

        fetch(`/employee/tasks/${id}/progress`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ status, progress })
        })
        .then(r => r.json())
        .then(data => {
            if (!data.ok) { showToast('Lỗi cập nhật trạng thái', 'error'); return; }

            // Update progress bar
            const bar = document.getElementById('bar-' + id);
            const pct = document.getElementById('pct-' + id);
            if (bar) { bar.style.width = data.progress + '%'; }
            if (pct) { pct.textContent = data.progress + '%'; }

            // Update card style
            const card = document.getElementById('task-' + id);
            if (card) {
                card.classList.remove('pending', 'doing', 'done', 'overdue');
                const stateMap = { 'Hoàn thành': 'done', 'Đang làm': 'doing', 'Đang review': 'doing', 'Chờ xử lý': 'pending' };
                const newState = stateMap[status] || 'pending';
                card.classList.add(newState);
                card.dataset.status = newState;

                // Update bar color
                if (bar) {
                    bar.className = 'prog-fill ' + (newState === 'done' ? 'green' : 'blue');
                }
            }

            showToast('Đã cập nhật: ' + status, 'success');
        })
        .catch(() => showToast('Không thể kết nối máy chủ', 'error'));
    }

    function showToast(message, type = 'success') {
        const toast = document.getElementById('toast-live');
        const msg = document.getElementById('toast-msg');
        msg.textContent = message;
        toast.className = 'toast ' + type;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 2800);
    }
</script>
</body>
</html>
