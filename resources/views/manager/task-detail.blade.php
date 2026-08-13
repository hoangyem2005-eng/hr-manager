@extends('manager.layouts.app')

@section('title', 'Chi tiết công việc - Trưởng phòng')
@section('page_title', 'Chi tiết công việc')

@section('content')
@php
    $fromProgress = request('from') === 'progress';
    $backRoute = $fromProgress
        ? route('manager.tasks', ['mode' => 'progress', 'view' => 'list'])
        : route('manager.tasks');
    $progress = (int) ($task->progress ?? 0);
    $deadline = $task->deadline ? $task->deadline->format('d/m/Y') : 'Không có';
@endphp

<style>
    .detail-page { display:grid; gap:20px; }
    .detail-hero { position:relative; overflow:hidden; display:grid; grid-template-columns:1fr auto; gap:18px; align-items:center; padding:26px; border:1px solid #B9CDF5; border-radius:8px; background:linear-gradient(135deg,#F8FBFF 0%,#EEF5FF 66%,#FFF5F7 100%); box-shadow:inset 5px 0 0 #E4002B; }
    .detail-hero:after { content:""; position:absolute; right:-42px; top:-62px; width:220px; height:220px; border:34px solid rgba(0,61,165,.06); border-radius:999px; }
    .detail-kicker { color:#003DA5; font-size:12px; font-weight:900; letter-spacing:.16em; text-transform:uppercase; }
    .detail-title { margin:8px 0 8px; color:#001F5B; font-size:34px; line-height:1.08; font-weight:900; letter-spacing:-.03em; }
    .detail-desc { color:#52637A; font-size:15px; line-height:1.6; max-width:760px; }
    .hero-actions { position:relative; z-index:1; display:flex; gap:10px; flex-wrap:wrap; justify-content:flex-end; }
    .detail-btn { min-height:42px; display:inline-flex; align-items:center; justify-content:center; gap:8px; border:1px solid #B9CDF5; border-radius:8px; padding:0 15px; background:#fff; color:#003DA5; font-weight:900; text-decoration:none; }
    .detail-btn.primary { border-color:#003DA5; background:#003DA5; color:#fff; }
    .detail-grid { display:grid; grid-template-columns:minmax(0,1.2fr) minmax(330px,.8fr); gap:18px; align-items:start; }
    .panel { overflow:hidden; border:1px solid #D4E0F7; border-radius:8px; background:#fff; box-shadow:0 16px 34px rgba(0,31,91,.06); }
    .panel-head { min-height:58px; display:flex; align-items:center; justify-content:space-between; gap:12px; padding:0 18px; border-bottom:1px solid #E5EDF8; }
    .panel-title { display:flex; align-items:center; gap:10px; color:#001F5B; font-size:17px; font-weight:900; }
    .description-box { padding:20px; color:#334155; font-size:15px; line-height:1.75; white-space:pre-line; min-height:220px; }
    .info-list { display:grid; }
    .info-row { display:grid; grid-template-columns:150px 1fr; gap:14px; padding:15px 18px; border-bottom:1px solid #EEF2F7; align-items:center; }
    .info-row:last-child { border-bottom:0; }
    .info-label { color:#64748B; font-size:12px; font-weight:900; text-transform:uppercase; letter-spacing:.06em; }
    .info-value { color:#001F5B; font-weight:900; }
    .status-pill { display:inline-flex; align-items:center; min-height:26px; border-radius:999px; padding:0 10px; background:#E8F0FE; color:#003DA5; font-size:12px; font-weight:900; }
    .progress-track { height:9px; overflow:hidden; border-radius:999px; background:#E9EEF8; }
    .progress-fill { height:100%; border-radius:inherit; background:linear-gradient(90deg,#E4002B,#003DA5); }
    .docs-table { width:100%; border-collapse:collapse; }
    .docs-table th, .docs-table td { padding:14px 16px; border-bottom:1px solid #EEF2F7; text-align:left; }
    .docs-table th { background:#F4F8FF; color:#64748B; font-size:11px; font-weight:900; text-transform:uppercase; letter-spacing:.08em; }
    .docs-table td { color:#334155; font-size:13px; }
    .doc-actions { display:flex; gap:8px; flex-wrap:wrap; }
    .empty { padding:34px 18px; color:#94A3B8; text-align:center; font-weight:800; }
    @media (max-width:1000px) { .detail-hero, .detail-grid { grid-template-columns:1fr; } .hero-actions { justify-content:flex-start; } }
</style>

<div class="detail-page">
    <section class="detail-hero">
        <div>
            <div class="detail-kicker">{{ $fromProgress ? 'Chi tiết tiến độ' : 'Chi tiết công việc' }}</div>
            <h1 class="detail-title">{{ $task->task_name }}</h1>
            <p class="detail-desc">
                Mã WH-{{ str_pad($task->id, 3, '0', STR_PAD_LEFT) }} - xem người giao, người nhận, hạn chót, tiến độ và tài liệu trong không gian Trưởng phòng.
            </p>
        </div>
        <div class="hero-actions">
            <a class="detail-btn" href="{{ $backRoute }}"><i data-lucide="arrow-left" style="width:18px;height:18px"></i>Quay lại</a>
            @if($task->is_proposal && (int) $task->proposal_step === 1)
                <form action="{{ route('dashboard.tasks.escalate', $task->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn duyệt và chuyển đề xuất này lên Ban Giám đốc?');">
                    @csrf
                    <button type="submit" class="detail-btn primary" style="background:#2563EB;border-color:#2563EB">
                        <i data-lucide="share-2" style="width:18px;height:18px"></i>Duyệt & Gửi lên Ban Giám đốc
                    </button>
                </form>
            @endif
            @if(!$task->is_proposal)
                <a class="detail-btn primary" href="{{ route('manager.tasks', ['edit_task_id' => $task->id]) }}" style="background:#003DA5;border-color:#003DA5"><i data-lucide="edit-3" style="width:18px;height:18px"></i>Cập nhật</a>
            @endif
            <a class="detail-btn primary" href="{{ route('manager.tasks') }}"><i data-lucide="send" style="width:18px;height:18px"></i>Giao việc</a>
        </div>
    </section>

    <section class="detail-grid">
        <div class="panel">
            <div class="panel-head">
                <div class="panel-title"><i data-lucide="file-text" style="width:22px;height:22px"></i>Mô tả công việc</div>
            </div>
            <div class="description-box">{{ $task->description ?: 'Chưa có mô tả chi tiết.' }}</div>
        </div>

        <div class="panel">
            <div class="panel-head">
                <div class="panel-title"><i data-lucide="info" style="width:22px;height:22px"></i>Thông tin vận hành</div>
            </div>
            <div class="info-list">
                <div class="info-row"><div class="info-label">Người giao</div><div class="info-value">{{ $task->creator->name ?? 'Hệ thống' }}</div></div>
                <div class="info-row"><div class="info-label">Người cùng làm</div><div class="info-value">{{ $task->assignees->isNotEmpty() ? $task->assignees->pluck('name')->join(', ') : ($task->assignee->name ?? 'Chưa gán') }}</div></div>
                <div class="info-row"><div class="info-label">Hạn chót</div><div class="info-value">{{ $deadline }}</div></div>
                <div class="info-row"><div class="info-label">Trạng thái</div><div class="info-value"><span class="status-pill">{{ $task->status }}</span></div></div>
                <div class="info-row">
                    <div class="info-label">Tiến độ</div>
                    <div class="info-value" style="display:grid;gap:8px">
                        <span>{{ $progress }}%</span>
                        <div class="progress-track"><div class="progress-fill" style="width:{{ $progress }}%"></div></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="panel">
        <div class="panel-head">
            <div class="panel-title"><i data-lucide="paperclip" style="width:22px;height:22px"></i>File đính kèm</div>
            <span class="status-pill">{{ $task->documents->count() }} file</span>
        </div>
        <div style="overflow-x:auto">
            <table class="docs-table">
                <thead>
                    <tr>
                        <th>Tên file</th>
                        <th>Người tải</th>
                        <th>Loại</th>
                        <th>Luồng duyệt</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($task->documents as $document)
                        <tr>
                            <td><strong style="color:#001F5B">{{ $document->file_name }}</strong></td>
                            <td>{{ $document->uploader->name ?? 'Không rõ' }}</td>
                            <td>{{ strtoupper($document->file_type ?? 'FILE') }}</td>
                            <td>
                                @if($document->review_status === \App\Models\Document::STATUS_MANAGER_REVIEW)
                                    <span class="status-pill">Chờ trưởng phòng</span>
                                @else
                                    <span class="status-pill">Đã gửi Giám đốc</span>
                                @endif
                            </td>
                            <td>
                                <div class="doc-actions">
                                    <a class="detail-btn" href="{{ route('congviec.file.preview', $document->id) }}" target="_blank">Xem</a>
                                    <a class="detail-btn primary" href="{{ route('congviec.file.download', $document->id) }}">Tải xuống</a>
                                    @if($document->review_status === \App\Models\Document::STATUS_MANAGER_REVIEW)
                                        <form method="POST" action="{{ route('manager.file.forward', $document->id) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="detail-btn primary">Gửi Giám đốc</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5"><div class="empty">Chưa có file đính kèm.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
@endsection
