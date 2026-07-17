<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chi tiết công việc - MobiFone HR</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --mf-blue: #003DA5; --mf-blue-dark: #001F5B; --mf-red: #E4002B;
            --mf-light: #EEF3FC; --mf-border: #D4E0F7;
            --bg: #F0F4FB; --white: #fff; --text: #0D1B3E; --text-muted: #64748B;
            --sidebar-w: 260px;
        }
        body { min-height: 100vh; font-family: 'Be Vietnam Pro', sans-serif; background: var(--bg); color: var(--text); }
        .layout { min-height: 100vh; display: grid; grid-template-columns: var(--sidebar-w) 1fr; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            background: linear-gradient(180deg, #001F5B 0%, #003DA5 100%);
            color: #fff; display: flex; flex-direction: column;
            position: sticky; top: 0; height: 100vh; overflow-y: auto;
        }
        .sidebar-brand {
            padding: 22px 20px 18px; border-bottom: 1px solid rgba(255,255,255,.12);
            display: flex; align-items: center; gap: 12px;
        }
        .brand-logo {
            width: 38px; height: 38px; border-radius: 8px;
            background: #fff;
            display: flex; align-items: center; justify-content: center;
            font-size: 15px; font-weight: 900; color: var(--mf-blue); flex-shrink: 0;
            letter-spacing: -.02em;
            box-shadow: inset 5px 0 0 var(--mf-red);
        }
        .mf-logo-word { display: inline-flex; align-items: baseline; background: #fff; border-radius: 7px; padding: 4px 9px; line-height: 1; box-shadow: 0 6px 18px rgba(0,0,0,.12); }
        .mf-logo-word .blue { color: var(--mf-blue); font-size: 17px; font-weight: 900; letter-spacing: -.03em; }
        .mf-logo-word .red { color: var(--mf-red); font-size: 17px; font-weight: 900; letter-spacing: -.03em; }
        .brand-sub { font-size: 10px; color: #BFD8FF; letter-spacing: .12em; text-transform: uppercase; margin-top: 6px; font-weight: 700; }

        .profile-card {
            margin: 16px 14px; background: rgba(255,255,255,.08);
            border: 1px solid rgba(255,255,255,.14); border-radius: 12px; padding: 14px;
            display: block; color: inherit; text-decoration: none;
        }
        .profile-card:hover { background: rgba(255,255,255,.13); }
        .profile-row { display: flex; align-items: center; gap: 10px; }
        .avatar {
            width: 42px; height: 42px; border-radius: 10px;
            background: linear-gradient(135deg, #E4002B, #FF5A7A);
            color: #fff; display: flex; align-items: center; justify-content: center;
            font-weight: 900; font-size: 14px; flex-shrink: 0;
        }
        .profile-name { font-size: 14px; font-weight: 800; }
        .profile-meta { font-size: 11px; color: rgba(255,255,255,.55); margin-top: 2px; }

        .sidebar-nav { padding: 14px; display: flex; flex-direction: column; gap: 4px; }
        .nav-item {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            color: rgba(255,255,255,.7); text-decoration: none;
            font-size: 13px; font-weight: 600;
            border: none; background: transparent; cursor: pointer; font-family: inherit;
            width: 100%; transition: background .15s, color .15s;
        }
        .nav-item:hover { background: rgba(255,255,255,.08); color: #fff; }
        .nav-item.active { background: #fff; color: var(--mf-blue); font-weight: 800; box-shadow: inset 3px 0 0 var(--mf-red); }

        .sidebar-footer { margin-top: auto; padding: 14px; border-top: 1px solid rgba(255,255,255,.1); }
        .logout-btn {
            display: flex; align-items: center; gap: 10px;
            padding: 10px 12px; border-radius: 10px;
            color: rgba(255,255,255,.55); border: none; background: transparent;
            cursor: pointer; font-family: inherit; font-size: 13px; font-weight: 600; width: 100%;
            transition: background .15s;
        }
        .logout-btn:hover { background: rgba(228,0,43,.15); color: #FCA5A5; }

        /* ===== MAIN ===== */
        .main { min-width: 0; }
        .topbar {
            position: sticky; top: 0; z-index: 30;
            background: rgba(240,244,251,.92); backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--mf-border);
            height: 60px; display: flex; align-items: center; padding: 0 24px; gap: 16px;
        }
        .crumb { font-size: 12px; color: var(--text-muted); }
        .crumb a { color: var(--mf-blue); text-decoration: none; font-weight: 600; }
        .crumb a:hover { text-decoration: underline; }

        .content { padding: 24px; display: flex; flex-direction: column; gap: 20px; }

        /* Flash */
        .flash-success {
            display: flex; align-items: center; gap: 10px;
            background: #F0FDF4; border: 1px solid #BBF7D0; border-radius: 10px;
            padding: 12px 16px; font-size: 13px; font-weight: 700; color: #15803D;
        }
        .flash-error {
            display: flex; align-items: center; gap: 10px;
            background: #FEF2F2; border: 1px solid #FECACA; border-radius: 10px;
            padding: 12px 16px; font-size: 13px; font-weight: 700; color: #B91C1C;
        }

        /* Task Hero */
        .task-hero {
            background: linear-gradient(135deg, #001F5B 0%, #003DA5 100%);
            border-radius: 14px; padding: 28px 32px;
            display: flex; justify-content: space-between; align-items: flex-start; gap: 20px;
        }
        .task-kicker { font-size: 10px; letter-spacing: .2em; text-transform: uppercase; color: rgba(255,255,255,.5); font-weight: 700; }
        .task-title { font-size: 22px; font-weight: 900; color: #fff; margin-top: 8px; line-height: 1.35; max-width: 680px; }
        .task-code-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2);
            border-radius: 8px; padding: 5px 12px;
            font-size: 12px; font-weight: 700; color: rgba(255,255,255,.75); margin-top: 12px;
        }
        .back-btn {
            height: 38px; padding: 0 16px; border-radius: 8px;
            background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.2);
            color: #fff; font-family: inherit; font-size: 13px; font-weight: 700;
            cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 8px; flex-shrink: 0;
            transition: background .15s;
        }
        .back-btn:hover { background: rgba(255,255,255,.2); }

        /* Detail grid */
        .detail-grid { display: grid; grid-template-columns: 1.3fr 1fr; gap: 20px; }
        .panel { background: var(--white); border: 1px solid #E5EDF8; border-radius: 14px; overflow: hidden; }
        .panel-head {
            padding: 16px 20px; border-bottom: 1px solid #EFF4FD;
            display: flex; align-items: center; gap: 8px;
            font-size: 14px; font-weight: 800; color: var(--mf-blue-dark);
        }
        .panel-head i { color: var(--mf-blue); }
        .panel-body { padding: 20px; color: #374151; line-height: 1.8; white-space: pre-line; font-size: 14px; }

        .info-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 13px 20px; border-top: 1px solid #F1F5FD; font-size: 13px;
        }
        .info-label { color: var(--text-muted); font-weight: 600; display: flex; align-items: center; gap: 6px; }
        .info-val { font-weight: 800; color: var(--mf-blue-dark); text-align: right; }

        .status-badge { display: inline-flex; align-items: center; gap: 5px; height: 24px; padding: 0 10px; border-radius: 999px; font-size: 11px; font-weight: 700; }
        .badge-pending { background: #F3F4F6; color: #6B7280; }
        .badge-doing   { background: #EFF6FF; color: #1D4ED8; }
        .badge-review  { background: #F5F3FF; color: #6D28D9; }
        .badge-done    { background: #F0FDF4; color: #15803D; }
        .badge-overdue { background: #FEF2F2; color: var(--mf-red); }

        .prog-section { padding: 16px 20px; border-top: 1px solid #F1F5FD; }
        .prog-label { font-size: 12px; font-weight: 700; color: var(--text-muted); margin-bottom: 8px; display: flex; justify-content: space-between; }
        .prog-bar { height: 8px; background: #E8EFF9; border-radius: 999px; overflow: hidden; }
        .prog-fill { height: 100%; border-radius: 999px; transition: width .5s ease; }
        .prog-fill.blue  { background: linear-gradient(90deg, #3B82F6, #003DA5); }
        .prog-fill.green { background: linear-gradient(90deg, #10B981, #059669); }

        /* ===== FILE SECTION ===== */
        .file-panel-head {
            padding: 16px 20px; border-bottom: 1px solid #EFF4FD;
            display: flex; align-items: center; justify-content: space-between; gap: 12px;
        }
        .file-panel-title { display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 800; color: var(--mf-blue-dark); }
        .file-panel-title i { color: var(--mf-blue); }

        /* Upload zone */
        .upload-zone {
            margin: 20px; border: 2px dashed var(--mf-border);
            border-radius: 12px; padding: 24px;
            display: flex; flex-direction: column; align-items: center; gap: 10px;
            background: var(--mf-light); cursor: pointer;
            transition: border-color .2s, background .2s;
        }
        .upload-zone:hover, .upload-zone.drag-over {
            border-color: var(--mf-blue); background: #E4EDFC;
        }
        .upload-zone i { color: var(--mf-blue); opacity: .65; }
        .upload-zone-text { font-size: 13px; font-weight: 700; color: var(--mf-blue); }
        .upload-zone-sub  { font-size: 11px; color: var(--text-muted); text-align: center; }
        #file-input { display: none; }

        /* Selected files */
        .selected-files { margin: 0 20px 16px; display: flex; flex-direction: column; gap: 6px; }
        .selected-file-item {
            display: flex; align-items: center; gap: 8px;
            background: var(--mf-light); border: 1px solid var(--mf-border);
            border-radius: 8px; padding: 8px 12px;
            font-size: 12px; font-weight: 600; color: var(--mf-blue-dark);
        }
        .selected-file-item i { color: var(--mf-blue); flex-shrink: 0; }
        .selected-file-size { margin-left: auto; color: var(--text-muted); font-size: 11px; }
        .remove-selected { background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 0; display: flex; align-items: center; margin-left: 4px; }
        .remove-selected:hover { color: var(--mf-red); }

        /* Upload actions */
        .upload-actions { padding: 0 20px 20px; display: flex; align-items: center; gap: 12px; }
        .upload-btn {
            height: 38px; padding: 0 18px; border-radius: 8px;
            background: var(--mf-blue); border: none; color: #fff;
            font-family: inherit; font-size: 13px; font-weight: 700;
            cursor: pointer; display: flex; align-items: center; gap: 7px;
            transition: opacity .15s;
        }
        .upload-btn:hover { opacity: .88; }
        .upload-btn:disabled { opacity: .4; cursor: not-allowed; }
        .upload-hint { font-size: 11px; color: var(--text-muted); line-height: 1.5; }

        /* Validation errors */
        .validation-error {
            margin: 0 20px 16px; background: #FEF2F2; border: 1px solid #FECACA;
            border-radius: 10px; padding: 12px 16px;
            font-size: 12px; font-weight: 700; color: #B91C1C;
            display: flex; flex-direction: column; gap: 4px;
        }

        /* File table */
        .file-table { width: 100%; border-collapse: collapse; font-size: 13px; }
        .file-table th {
            background: #F8FAFF; color: var(--text-muted);
            text-align: left; padding: 10px 20px;
            font-size: 10px; text-transform: uppercase; letter-spacing: .08em; font-weight: 700;
            border-top: 1px solid #F1F5FD;
        }
        .file-table td { padding: 12px 20px; border-top: 1px solid #F1F5FD; vertical-align: middle; }
        .file-name { font-weight: 700; color: var(--mf-blue-dark); display: flex; align-items: center; gap: 8px; }
        .file-ext {
            display: inline-block; padding: 2px 7px; border-radius: 5px;
            background: var(--mf-light); color: var(--mf-blue);
            font-size: 10px; font-weight: 800; text-transform: uppercase;
        }
        .file-mine { color: var(--mf-blue); font-size: 12px; font-weight: 700; }
        .file-uploader { color: var(--text-muted); font-size: 12px; }
        .file-btn {
            height: 30px; padding: 0 10px; border-radius: 7px;
            border: 1px solid var(--mf-border); background: var(--white);
            color: var(--mf-blue); font-family: inherit; font-size: 11px; font-weight: 700;
            cursor: pointer; text-decoration: none;
            display: inline-flex; align-items: center; gap: 5px;
            transition: background .12s;
        }
        .file-btn:hover { background: var(--mf-light); }
        .file-btn.danger { border-color: #FECACA; color: var(--mf-red); background: #FEF2F2; }
        .file-btn.danger:hover { background: #FEE2E2; }
        .file-actions { display: flex; gap: 6px; flex-wrap: wrap; }
        .no-files {
            padding: 32px; text-align: center; color: var(--text-muted);
            font-size: 13px; display: flex; flex-direction: column; align-items: center; gap: 8px;
        }
        .no-files i { opacity: .3; }

        @media (max-width: 960px) {
            .layout { grid-template-columns: 1fr; }
            .sidebar { height: auto; position: static; }
            .detail-grid { grid-template-columns: 1fr; }
            .task-hero { flex-direction: column; }
        }
    </style>
</head>
<body>
<div class="layout">

    {{-- ===== SIDEBAR ===== --}}
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
                <div>
                    <div class="profile-name">{{ Auth::user()->name }}</div>
                    <div class="profile-meta">{{ Auth::user()->department->TENPHONG ?? 'MobiFone' }}</div>
                </div>
            </div>
        </a>

        <nav class="sidebar-nav">
            <a href="{{ route('employee.dashboard') }}" class="nav-item">
                <i data-lucide="layout-dashboard" style="width:16px;height:16px;flex-shrink:0"></i>
                Dashboard
            </a>
            <a href="{{ route('employee.tasks') }}" class="nav-item active">
                <i data-lucide="clipboard-list" style="width:16px;height:16px;flex-shrink:0"></i>
                Công việc của tôi
            </a>
            <a href="{{ route('employee.notifications') }}" class="nav-item">
                <i data-lucide="bell" style="width:16px;height:16px;flex-shrink:0"></i>
                Thông báo
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

    {{-- ===== MAIN ===== --}}
    <div class="main">
        <div class="topbar">
            <div class="crumb">
                <a href="{{ route('employee.dashboard') }}">Dashboard</a>
                &rsaquo; Chi tiết công việc
            </div>
        </div>

        <div class="content">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="flash-success">
                    <i data-lucide="check-circle" style="width:16px;height:16px;flex-shrink:0"></i>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="flash-error">
                    <i data-lucide="alert-circle" style="width:16px;height:16px;flex-shrink:0"></i>
                    {{ session('error') }}
                </div>
            @endif

            {{-- Task Hero --}}
            <div class="task-hero">
                <div>
                    <div class="task-kicker">Task detail · MobiFone WorkHub</div>
                    <div class="task-title">{{ $task->task_name }}</div>
                    <div class="task-code-badge">
                        <i data-lucide="hash" style="width:12px;height:12px"></i>
                        WH-{{ str_pad($task->id, 3, '0', STR_PAD_LEFT) }}
                    </div>
                </div>
                <a href="{{ route('employee.dashboard') }}" class="back-btn">
                    <i data-lucide="arrow-left" style="width:14px;height:14px"></i>
                    Quay lại
                </a>
            </div>

            {{-- Detail Grid --}}
            <div class="detail-grid">
                <div class="panel">
                    <div class="panel-head">
                        <i data-lucide="file-text"></i>
                        Mô tả công việc
                    </div>
                    <div class="panel-body">{{ $task->description ?: 'Chưa có mô tả chi tiết.' }}</div>
                </div>

                <div class="panel">
                    <div class="panel-head">
                        <i data-lucide="info"></i>
                        Thông tin vận hành
                    </div>
                    <div class="info-row">
                        <div class="info-label"><i data-lucide="user" style="width:13px;height:13px"></i>Người giao</div>
                        <div class="info-val">{{ $task->creator->name ?? 'Trưởng phòng' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><i data-lucide="user-check" style="width:13px;height:13px"></i>Người nhận</div>
                        <div class="info-val">{{ $task->assignees->isNotEmpty() ? $task->assignees->pluck('name')->join(', ') : ($task->assignee->name ?? 'Bạn') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><i data-lucide="calendar" style="width:13px;height:13px"></i>Deadline</div>
                        <div class="info-val">{{ $task->deadline ? $task->deadline->format('d/m/Y') : 'Không có' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label"><i data-lucide="activity" style="width:13px;height:13px"></i>Trạng thái</div>
                        <div class="info-val">
                            @php
                                $s  = $task->status ?? '';
                                $bc = str_contains($s, 'Hoàn') ? 'badge-done'
                                    : (str_contains($s, 'Quá') ? 'badge-overdue'
                                    : (str_contains($s, 'review') ? 'badge-review'
                                    : (str_contains($s, 'Đang') ? 'badge-doing' : 'badge-pending')));
                            @endphp
                            <span class="status-badge {{ $bc }}">{{ $s ?: 'Chờ xử lý' }}</span>
                        </div>
                    </div>
                    <div class="prog-section">
                        @php $prog = $task->progress ?? 0; @endphp
                        <div class="prog-label">
                            <span>Tiến độ</span>
                            <span style="color:var(--mf-blue);font-weight:900">{{ $prog }}%</span>
                        </div>
                        <div class="prog-bar">
                            <div class="prog-fill {{ $prog >= 100 ? 'green' : 'blue' }}" style="width:{{ $prog }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ===== FILE SECTION ===== --}}
            <div class="panel">
                <div class="file-panel-head">
                    <div class="file-panel-title">
                        <i data-lucide="paperclip"></i>
                        File đính kèm
                        @if($task->documents->count() > 0)
                            <span style="background:var(--mf-light);color:var(--mf-blue);border-radius:999px;padding:2px 9px;font-size:11px;font-weight:800">{{ $task->documents->count() }}</span>
                        @endif
                    </div>
                    <span style="font-size:11px;color:var(--text-muted);font-weight:500">Tối đa 5 file · 20 MB/file</span>
                </div>

                {{-- Upload Form --}}
                <form method="POST" action="{{ route('employee.task.upload', $task->id) }}"
                      enctype="multipart/form-data" id="upload-form">
                    @csrf

                    @if($errors->any())
                        <div class="validation-error">
                            @foreach($errors->all() as $err)<div>{{ $err }}</div>@endforeach
                        </div>
                    @endif

                    <div class="upload-zone" id="drop-zone" onclick="document.getElementById('file-input').click()">
                        <i data-lucide="upload-cloud" style="width:32px;height:32px"></i>
                        <div class="upload-zone-text">Nhấn hoặc kéo thả file vào đây</div>
                        <div class="upload-zone-sub">PDF, Word, Excel, ảnh, ZIP · Tối đa 20 MB/file</div>
                    </div>
                    <input type="file" id="file-input" name="attachments[]"
                           multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip,.rar,.txt">

                    <div class="selected-files" id="selected-files"></div>

                    <div class="upload-actions">
                        <button type="submit" class="upload-btn" id="upload-btn" disabled>
                            <i data-lucide="upload" style="width:14px;height:14px"></i>
                            Tải lên
                        </button>
                        <span class="upload-hint">File sẽ hiển thị cho cấp trên xem xét.</span>
                    </div>
                </form>

                {{-- Existing files table --}}
                @if($task->documents->isEmpty())
                    <div class="no-files">
                        <i data-lucide="folder-open" style="width:36px;height:36px"></i>
                        Chưa có file đính kèm. Tải file lên ở trên để gửi cho cấp trên.
                    </div>
                @else
                    <table class="file-table">
                        <thead>
                            <tr>
                                <th>Tên file</th>
                                <th>Người tải</th>
                                <th>Loại</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($task->documents as $doc)
                                @php
                                    $ext = strtolower($doc->file_type ?? '');
                                    $icon = in_array($ext, ['jpg','jpeg','png','gif','webp']) ? 'image'
                                          : ($ext === 'pdf' ? 'file-text'
                                          : (in_array($ext, ['doc','docx']) ? 'file-text'
                                          : (in_array($ext, ['zip','rar']) ? 'archive'
                                          : 'file')));
                                @endphp
                                <tr>
                                    <td>
                                        <div class="file-name">
                                            <i data-lucide="{{ $icon }}" style="width:15px;height:15px;flex-shrink:0;color:var(--mf-blue)"></i>
                                            {{ $doc->file_name }}
                                        </div>
                                    </td>
                                    <td>
                                        @if($doc->user_id === Auth::id())
                                            <span class="file-mine">
                                                <i data-lucide="user" style="width:11px;height:11px"></i> Bạn
                                            </span>
                                        @else
                                            <span class="file-uploader">{{ $doc->uploader->name ?? 'Không rõ' }}</span>
                                        @endif
                                    </td>
                                    <td><span class="file-ext">{{ $doc->file_type ?? 'file' }}</span></td>
                                    <td>
                                        <div class="file-actions">
                                            <a href="{{ route('congviec.file.preview', $doc->id) }}" target="_blank" class="file-btn">
                                                <i data-lucide="eye" style="width:12px;height:12px"></i> Xem
                                            </a>
                                            <a href="{{ route('congviec.file.download', $doc->id) }}" class="file-btn">
                                                <i data-lucide="download" style="width:12px;height:12px"></i> Tải
                                            </a>
                                            @if($doc->user_id === Auth::id())
                                                <form method="POST" action="{{ route('employee.file.delete', $doc->id) }}"
                                                      onsubmit="return confirm('Xóa file \'{{ addslashes($doc->file_name) }}\'?')"
                                                      style="display:inline">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="file-btn danger">
                                                        <i data-lucide="trash-2" style="width:12px;height:12px"></i> Xóa
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>

        </div>{{-- /content --}}
    </div>{{-- /main --}}
</div>

<script>
lucide.createIcons();

const dropZone   = document.getElementById('drop-zone');
const fileInput  = document.getElementById('file-input');
const uploadBtn  = document.getElementById('upload-btn');
const selectedEl = document.getElementById('selected-files');
let selectedFiles = [];

function formatSize(bytes) {
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

function renderSelectedFiles() {
    selectedEl.innerHTML = '';
    uploadBtn.disabled = selectedFiles.length === 0;
    selectedFiles.forEach((file, idx) => {
        const div = document.createElement('div');
        div.className = 'selected-file-item';
        div.innerHTML = `
            <i data-lucide="file" style="width:14px;height:14px"></i>
            <span style="flex:1;min-width:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap">${file.name}</span>
            <span class="selected-file-size">${formatSize(file.size)}</span>
            <button type="button" class="remove-selected" data-idx="${idx}" title="Bỏ chọn">
                <i data-lucide="x" style="width:13px;height:13px"></i>
            </button>`;
        selectedEl.appendChild(div);
    });
    lucide.createIcons();
    selectedEl.querySelectorAll('.remove-selected').forEach(btn => {
        btn.addEventListener('click', () => {
            selectedFiles.splice(parseInt(btn.dataset.idx), 1);
            syncInputFiles();
            renderSelectedFiles();
        });
    });
}

function syncInputFiles() {
    const dt = new DataTransfer();
    selectedFiles.forEach(f => dt.items.add(f));
    fileInput.files = dt.files;
}

function addFiles(newFiles) {
    newFiles.forEach(f => {
        if (!selectedFiles.find(x => x.name === f.name && x.size === f.size))
            selectedFiles.push(f);
    });
    if (selectedFiles.length > 5) {
        selectedFiles = selectedFiles.slice(0, 5);
        alert('Tối đa 5 file mỗi lần tải lên.');
    }
    syncInputFiles();
    renderSelectedFiles();
}

fileInput.addEventListener('change', () => addFiles(Array.from(fileInput.files)));

dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('drag-over'); });
dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
dropZone.addEventListener('drop', e => {
    e.preventDefault();
    dropZone.classList.remove('drag-over');
    addFiles(Array.from(e.dataTransfer.files));
});
</script>
</body>
</html>
