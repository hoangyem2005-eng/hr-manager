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
            @php
                $isAssignee = ((int)$task->assigned_to === (int)Auth::id()) || ($task->assignees && $task->assignees->contains('id', Auth::id()));
                $isPending = in_array($task->status, ['Chờ xử lý', 'Todo'], true);
            @endphp
            @if($isPending && $isAssignee)
                <form method="POST" action="{{ route('employee.task.accept', $task->id) }}" style="display:inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn tiếp nhận công việc này?')">
                    @csrf
                    <button type="submit" class="detail-btn primary" style="background:#16A34A;border-color:#16A34A">
                        <i data-lucide="check" style="width:18px;height:18px"></i>Nhận việc
                    </button>
                </form>
                <button type="button" class="detail-btn primary" style="background:#DC2626;border-color:#DC2626;cursor:pointer" onclick="openManagerDeclineModal()">
                    <i data-lucide="x" style="width:18px;height:18px"></i>Không nhận
                </button>
            @endif
            <a class="detail-btn" href="{{ $backRoute }}"><i data-lucide="arrow-left" style="width:18px;height:18px"></i>Quay lại</a>
            @if($task->is_proposal)
                <form action="{{ route('dashboard.tasks.approve-proposal', $task->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn phê duyệt đề xuất công việc này?');">
                    @csrf
                    <button type="submit" class="detail-btn primary" style="background:#16A34A;border-color:#16A34A">
                        <i data-lucide="check" style="width:18px;height:18px"></i>Phê duyệt
                    </button>
                </form>
                @if((int) $task->proposal_step === 1)
                    <form action="{{ route('dashboard.tasks.escalate', $task->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn chuyển tiếp đề xuất này lên Ban Giám đốc?');">
                        @csrf
                        <button type="submit" class="detail-btn primary" style="background:#2563EB;border-color:#2563EB">
                            <i data-lucide="share-2" style="width:18px;height:18px"></i>Gửi Ban Giám đốc
                        </button>
                    </form>
                @endif
                <form action="{{ route('dashboard.tasks.reject-proposal', $task->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn từ chối đề xuất này?');">
                    @csrf
                    <button type="submit" class="detail-btn primary" style="background:#DC2626;border-color:#DC2626">
                        <i data-lucide="x-circle" style="width:18px;height:18px"></i>Không đồng ý
                    </button>
                </form>
            @else
                <a class="detail-btn primary" href="{{ route('manager.tasks', ['edit_task_id' => $task->id]) }}" style="background:#003DA5;border-color:#003DA5"><i data-lucide="edit-3" style="width:18px;height:18px"></i>Cập nhật</a>
            @endif
            <a class="detail-btn primary" href="{{ route('manager.tasks') }}"><i data-lucide="send" style="width:18px;height:18px"></i>Giao việc</a>
        </div>
    </section>

    @if($isPending && $isAssignee)
        <div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;padding:16px 20px;display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
            <div>
                <div style="font-weight:900;color:#1E40AF;font-size:14px;display:flex;align-items:center;gap:6px">
                    <i data-lucide="clock" style="width:18px;height:18px"></i> Công việc đang chờ bạn tiếp nhận
                </div>
                <div style="font-size:13px;color:#3B82F6;margin-top:2px">
                    Lãnh đạo đã giao công việc này cho bạn. Vui lòng xác nhận nhận việc để bắt đầu thực hiện hoặc phản hồi không nhận.
                    @if($task->acceptance_deadline)
                        <strong>(Hạn nhận: {{ $task->acceptance_deadline->format('H:i d/m/Y') }})</strong>
                    @endif
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:10px">
                <form method="POST" action="{{ route('employee.task.accept', $task->id) }}" onsubmit="return confirm('Bạn có chắc chắn muốn tiếp nhận công việc này?')">
                    @csrf
                    <button type="submit" style="height:38px;padding:0 18px;background:#16A34A;color:#fff;border:none;border-radius:8px;font-weight:800;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
                        <i data-lucide="check" style="width:16px;height:16px"></i> Nhận việc
                    </button>
                </form>
                <button type="button" onclick="openManagerDeclineModal()" style="height:38px;padding:0 16px;background:#FEF2F2;color:#DC2626;border:1px solid #FECACA;border-radius:8px;font-weight:800;font-size:13px;cursor:pointer;display:inline-flex;align-items:center;gap:6px">
                    <i data-lucide="x" style="width:16px;height:16px"></i> Không nhận
                </button>
            </div>
        </div>
    @endif

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

            @if((int) $task->assigned_to === (int) Auth::id() && isset($myTeam) && $myTeam->isNotEmpty())
                <div style="padding:16px 18px;background:#F0F6FF;border-top:1px solid #D4E0F7;">
                    <div style="font-weight:900;color:#001F5B;font-size:13px;margin-bottom:8px;display:flex;align-items:center;gap:6px">
                        <i data-lucide="user-plus" style="width:16px;height:16px;color:#003DA5"></i> Phân công cho nhân viên trong phòng
                    </div>
                    <form method="POST" action="{{ route('manager.task.delegate', $task->id) }}" style="display:grid;grid-template-columns:1fr auto;gap:10px;align-items:center">
                        @csrf
                        @method('PATCH')
                        <select name="assigned_to" required style="min-height:38px;border:1px solid #B9CDF5;border-radius:8px;padding:0 10px;font-family:inherit;color:#0F172A;background:#fff">
                            <option value="">Chọn nhân viên nhận việc</option>
                            @foreach($myTeam as $m)
                                <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->email }})</option>
                            @endforeach
                        </select>
                        <button type="submit" class="detail-btn primary" style="min-height:38px;padding:0 16px"><i data-lucide="send" style="width:15px;height:15px"></i>Phân công</button>
                    </form>
                </div>
            @endif
        </div>
    </section>

    {{-- ===== ĐOẠN MÔ TẢ NỘP VIỆC CỦA NHÂN VIÊN & DUYỆT ĐÁNH GIÁ ===== --}}
    @if($task->employee_note || $task->status === 'Chờ đánh giá')
        <section class="panel" style="border: 2px solid #2563EB25; background: linear-gradient(135deg, #F8FAFF 0%, #EFF6FF 100%);">
            <div class="panel-head" style="background: linear-gradient(90deg, #001F5B 0%, #003DA5 100%); color: #fff; border-radius: 8px 8px 0 0;">
                <div class="panel-title" style="color: #fff">
                    <i data-lucide="star" style="width:20px;height:20px;color:#FCD34D"></i>
                    Báo cáo nộp việc & Đánh giá công việc
                </div>
                <span class="status-pill" style="background:rgba(255,255,255,.2);color:#fff">{{ $task->status }}</span>
            </div>
            <div style="padding: 22px; display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.05em; color:#001F5B; margin-bottom:8px">
                        📝 Đoạn mô tả khi nộp duyệt của nhân viên:
                    </div>
                    @if($task->employee_note)
                        <div style="background:#fff; border:1px solid #D4E0F7; border-radius:10px; padding:16px; color:#1E293B; line-height:1.7; font-size:14px; white-space:pre-line">
                            {{ $task->employee_note }}
                        </div>
                    @else
                        <div style="background:#F8FAFF; border:1px dashed #CBD5E1; border-radius:10px; padding:16px; color:#94A3B8; font-style:italic; font-size:13px">
                            Nhân viên chưa gửi đoạn mô tả khi nộp việc.
                        </div>
                    @endif
                </div>

                <div>
                    <div style="font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.05em; color:#001F5B; margin-bottom:8px">
                        ⭐ Chấm điểm & Quyết định duyệt:
                    </div>
                    @if($task->status === 'Chờ đánh giá')
                        <form action="{{ route('congviec.chamdiem', $task->id) }}" method="POST" style="background:#fff; border:1px solid #D4E0F7; border-radius:10px; padding:16px;">
                            @csrf
                            @method('PATCH')
                            <div style="margin-bottom:12px">
                                <label style="display:block; font-size:12px; font-weight:700; color:#374151; margin-bottom:6px">Điểm đánh giá *</label>
                                <select name="evaluation_score" required style="width:100%; min-height:36px; border:1px solid #CBD5E1; border-radius:8px; padding:0 10px; font-weight:700; color:#0F172A">
                                    <option value="5">⭐⭐⭐⭐⭐ (5/5 sao - Xuất sắc)</option>
                                    <option value="4">⭐⭐⭐⭐ (4/5 sao - Tốt)</option>
                                    <option value="3">⭐⭐⭐ (3/5 sao - Đạt yêu cầu)</option>
                                    <option value="2">⭐⭐ (2/5 sao - Cần cải thiện)</option>
                                    <option value="1">⭐ (1/5 sao - Chưa đạt)</option>
                                </select>
                            </div>
                            <div style="margin-bottom:12px">
                                <label style="display:block; font-size:12px; font-weight:700; color:#374151; margin-bottom:6px">Nhận xét của cấp trên</label>
                                <textarea name="evaluation_comment" rows="2" placeholder="Nhập nhận xét cho nhân viên..." style="width:100%; border:1px solid #CBD5E1; border-radius:8px; padding:8px 10px; font-family:inherit; font-size:13px; resize:vertical; outline:none; box-sizing:border-box" maxlength="2000"></textarea>
                            </div>
                            <div style="display:flex; gap:10px;">
                                <button type="submit" name="action" value="approve" class="detail-btn primary" style="flex:1; justify-content:center; background:#16A34A; border-color:#16A34A">
                                    <i data-lucide="check-circle" style="width:16px;height:16px"></i> Duyệt Hoàn thành
                                </button>
                                <button type="submit" name="action" value="return" class="detail-btn primary" style="flex:1; justify-content:center; background:#DC2626; border-color:#DC2626">
                                    <i data-lucide="rotate-ccw" style="width:16px;height:16px"></i> Trả lại
                                </button>
                            </div>
                        </form>
                    @elseif($task->evaluated_at)
                        <div style="background:#fff; border:1px solid #BBF7D0; border-radius:10px; padding:16px;">
                            <div style="font-weight:800; color:#15803D; margin-bottom:6px">
                                Đã chấm: {{ $task->evaluation_score }}/5 ⭐
                            </div>
                            @if($task->evaluation_comment)
                                <p style="font-size:13px; color:#374151; line-height:1.6">{{ $task->evaluation_comment }}</p>
                            @endif
                            <div style="font-size:11px; color:#94A3B8; margin-top:8px">
                                Chấm bởi: {{ $task->evaluator->name ?? 'Cấp trên' }} — {{ $task->evaluated_at->format('d/m/Y H:i') }}
                            </div>
                        </div>
                    @else
                        <div style="background:#fff; border:1px solid #D4E0F7; border-radius:10px; padding:16px; color:#64748B; font-size:13px">
                            Trạng thái hiện tại: <strong>{{ $task->status }}</strong>. Khi nhân viên nộp việc, bạn có thể chấm điểm và phê duyệt tại đây.
                        </div>
                    @endif
                </div>
            </div>
        </section>
    @endif

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

{{-- Modal từ chối nhận việc --}}
<div id="manager-decline-modal" style="display:none;position:fixed;inset:0;background:rgba(0,31,91,.45);backdrop-filter:blur(4px);z-index:999;place-items:center;padding:20px">
    <div style="width:min(480px,100%);background:#fff;border-radius:12px;box-shadow:0 24px 48px rgba(0,0,0,.2);overflow:hidden">
        <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid #EFF4FD;background:#F8FAFF">
            <h3 style="font-size:15px;font-weight:900;color:#001F5B;margin:0">Từ chối nhận công việc</h3>
            <button type="button" onclick="closeManagerDeclineModal()" style="background:none;border:none;cursor:pointer;color:#64748B"><i data-lucide="x" style="width:18px;height:18px"></i></button>
        </div>
        <form method="POST" action="{{ route('employee.task.decline', $task->id) }}">
            @csrf
            <div style="padding:20px">
                <p style="font-weight:800;color:#001F5B;margin-bottom:12px;font-size:14px">
                    Công việc: {{ $task->task_name }}
                </p>
                <label style="display:block;margin-bottom:6px;font-size:12px;font-weight:700;color:#475569">Lý do từ chối (tùy chọn / gửi cho cấp trên):</label>
                <textarea name="reason" rows="3" style="width:100%;border:1px solid #D4E0F7;border-radius:8px;padding:8px 10px;font-family:inherit;font-size:13px;resize:vertical;outline:none;box-sizing:border-box" placeholder="Nhập lý do bạn không thể nhận công việc này..."></textarea>
            </div>
            <div style="padding:12px 20px;border-top:1px solid #EFF4FD;background:#F8FAFF;display:flex;justify-content:flex-end;gap:10px">
                <button type="button" onclick="closeManagerDeclineModal()" style="padding:8px 16px;border-radius:7px;background:#fff;border:1px solid #D4E0F7;color:#64748B;font-weight:700;font-size:13px;cursor:pointer">Hủy bỏ</button>
                <button type="submit" style="padding:8px 16px;border-radius:7px;background:#DC2626;border:none;color:#fff;font-weight:800;font-size:13px;cursor:pointer">Xác nhận không nhận</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openManagerDeclineModal() {
        const modal = document.getElementById('manager-decline-modal');
        if (modal) modal.style.display = 'grid';
    }
    function closeManagerDeclineModal() {
        const modal = document.getElementById('manager-decline-modal');
        if (modal) modal.style.display = 'none';
    }
</script>
@endsection
