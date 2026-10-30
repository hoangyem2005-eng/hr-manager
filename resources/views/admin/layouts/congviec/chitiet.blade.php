@extends('admin.layouts.index')

@section('title', 'Chi tiết công việc - Giám đốc')
@section('page_title', 'Chi tiết công việc')

@section('content')
<section class="module-hero">
    <div>
        <div class="module-kicker">Chi tiết công việc</div>
        <h1 class="module-title">{{ $task->task_name }}</h1>
        <p class="module-desc">Mã WH-{{ str_pad($task->id, 3, '0', STR_PAD_LEFT) }} - theo dõi người giao, người nhận, hạn chót, tiến độ và file liên quan.</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a href="{{ route('congviec.danhsach') }}" class="btn secondary"><i data-lucide="arrow-left"></i>Danh sách</a>
        @if($task->is_proposal && (int) $task->proposal_step === 2)
            <a href="{{ route('congviec.sua', $task->id) }}" class="btn primary" style="background:#16A34A;border-color:#16A34A"><i data-lucide="check"></i>Giao việc (Phê duyệt)</a>
            <form action="{{ route('dashboard.tasks.reject-proposal', $task->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Bạn có chắc chắn muốn từ chối đề xuất này?');">
                @csrf
                <button type="submit" class="btn primary" style="background:#DC2626;border-color:#DC2626;cursor:pointer">
                    <i data-lucide="x-circle"></i>Không đồng ý
                </button>
            </form>
        @else
            <a href="{{ route('congviec.sua', $task->id) }}" class="btn primary"><i data-lucide="pencil"></i>Cập nhật</a>
        @endif
    </div>
</section>

<section style="display:grid;grid-template-columns:1.2fr .8fr;gap:18px">
    <div class="panel">
        <div class="panel-head"><div class="panel-title"><i data-lucide="file-text"></i>Mô tả công việc</div></div>
        <div style="padding:18px;color:#374151;line-height:1.7;white-space:pre-line">{{ $task->description ?: 'Chưa có mô tả chi tiết.' }}</div>
    </div>

    <div class="panel">
        <div class="panel-head"><div class="panel-title"><i data-lucide="info"></i>Thông tin vận hành</div></div>
        <table class="table">
            <tbody>
                <tr><td>Người giao</td><td><strong>{{ $task->creator->name ?? 'Hệ thống' }}</strong></td></tr>
                <tr><td>Người cùng làm</td><td><strong>{{ $task->assignees->isNotEmpty() ? $task->assignees->pluck('name')->join(', ') : ($task->assignee->name ?? 'Chưa gán') }}</strong></td></tr>
                <tr><td>Hạn chót</td><td>{{ $task->deadline ? $task->deadline->format('d/m/Y') : 'Không có' }}</td></tr>
                <tr>
                    <td>Trạng thái</td>
                    <td>
                        @php
                            $displayStatus = match($task->status) {
                                'Done', 'Hoàn thành' => 'Hoàn thành',
                                'In Progress', 'Đang làm' => 'Đang làm',
                                'Todo', 'Chờ xử lý' => 'Chờ xử lý',
                                'Overdue', 'Quá hạn' => 'Quá hạn',
                                'Đang review', 'Chờ đánh giá' => 'Chờ đánh giá',
                                default => $task->status,
                            };
                        @endphp
                        <span class="status">{{ $displayStatus }}</span>
                    </td>
                </tr>
                <tr><td>Tiến độ</td><td><strong>{{ $task->progress ?? 0 }}%</strong></td></tr>
            </tbody>
        </table>
    </div>
</section>

<section class="panel">
    <div class="panel-head"><div class="panel-title"><i data-lucide="paperclip"></i>File đính kèm</div></div>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Tên file</th><th>Người tải</th><th>Loại</th><th>Luồng duyệt</th><th>Thao tác</th></tr></thead>
            <tbody>
                @forelse($task->documents as $document)
                    <tr>
                        <td><strong>{{ $document->file_name }}</strong></td>
                        <td>{{ $document->uploader->name ?? 'Không rõ' }}</td>
                        <td>{{ strtoupper($document->file_type ?? 'FILE') }}</td>
                        <td>
                            @if($document->review_status === \App\Models\Document::STATUS_MANAGER_REVIEW)
                                <span class="status">Chờ trưởng phòng</span>
                            @else
                                <span class="status">Đã gửi Giám đốc</span>
                            @endif
                        </td>
                        <td>
                            <div style="display:flex;gap:8px;flex-wrap:wrap">
                                <a class="btn secondary" href="{{ route('congviec.file.preview', $document->id) }}" target="_blank">Xem</a>
                                <a class="btn primary" href="{{ route('congviec.file.download', $document->id) }}">Tải xuống</a>
                                @if((Auth::user()->isLeader() || (int)Auth::user()->role_id === \App\Models\User::ROLE_MANAGER) && (int)Auth::user()->role_id !== \App\Models\User::ROLE_ADMIN && $document->review_status === \App\Models\Document::STATUS_MANAGER_REVIEW)
                                    <form method="POST" action="{{ route('manager.file.forward', $document->id) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn primary">Gửi Giám đốc</button>
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

{{-- ===== PANEL ĐÁNH GIÁ: hiện khi trạng thái là "Chờ đánh giá" ===== --}}
@if($task->status === 'Chờ đánh giá')
<section class="panel" id="evaluation-panel" style="border:2px solid #2563EB20;background:linear-gradient(135deg,#EFF6FF 0%,#F8FAFF 100%)">
    <div class="panel-head" style="background:linear-gradient(90deg,#1D4ED8 0%,#2563EB 100%);border-radius:10px 10px 0 0">
        <div class="panel-title" style="color:#fff">
            <i data-lucide="star" style="width:20px;height:20px;color:#FCD34D"></i>
            Đánh giá & Duyệt công việc
        </div>
    </div>

    <div style="padding:24px;display:grid;grid-template-columns:1fr 1fr;gap:24px">

        {{-- Cột trái: Ghi chú của nhân viên --}}
        <div>
            <div style="font-size:.78rem;font-weight:700;color:#6B7280;letter-spacing:.05em;text-transform:uppercase;margin-bottom:8px">
                📝 Ghi chú tự đánh giá của nhân viên
            </div>
            @if($task->employee_note)
                <div style="background:#fff;border:1px solid #E0ECFF;border-radius:10px;padding:14px 16px;color:#1E3A5F;line-height:1.7;white-space:pre-line;font-size:.93rem">
                    {{ $task->employee_note }}
                </div>
            @else
                <div style="background:#F9FAFB;border:1px dashed #CBD5E1;border-radius:10px;padding:14px 16px;color:#9CA3AF;font-size:.9rem;font-style:italic">
                    Nhân viên chưa gửi ghi chú tự đánh giá.
                </div>
            @endif
        </div>

        {{-- Cột phải: Form chấm điểm của cấp trên --}}
        <div>
            @if($task->evaluated_at)
                {{-- Đã chấm điểm rồi - hiển thị kết quả --}}
                <div style="font-size:.78rem;font-weight:700;color:#6B7280;letter-spacing:.05em;text-transform:uppercase;margin-bottom:8px">
                    ✅ Đã chấm điểm
                </div>
                <div style="background:#fff;border:1px solid #BBF7D0;border-radius:10px;padding:16px">
                    <div style="display:flex;gap:4px;margin-bottom:10px">
                        @for($s = 1; $s <= 5; $s++)
                            <span style="font-size:1.8rem;color:{{ $s <= $task->evaluation_score ? '#F59E0B' : '#D1D5DB' }}">★</span>
                        @endfor
                        <span style="margin-left:8px;font-size:.9rem;color:#374151;align-self:center">{{ $task->evaluation_score }}/5 sao</span>
                    </div>
                    @if($task->evaluation_comment)
                        <div style="color:#374151;font-size:.92rem;line-height:1.6;white-space:pre-line">{{ $task->evaluation_comment }}</div>
                    @endif
                    <div style="margin-top:10px;font-size:.8rem;color:#9CA3AF">
                        Chấm bởi: <strong>{{ $task->evaluator->name ?? 'N/A' }}</strong> — {{ $task->evaluated_at->format('d/m/Y H:i') }}
                    </div>
                </div>
            @else
                {{-- Form chấm điểm --}}
                <div style="font-size:.78rem;font-weight:700;color:#6B7280;letter-spacing:.05em;text-transform:uppercase;margin-bottom:8px">
                    ⭐ Chấm điểm & Quyết định
                </div>
                <form id="eval-form" action="{{ route('congviec.chamdiem', $task->id) }}" method="POST" style="background:#fff;border:1px solid #DBEAFE;border-radius:10px;padding:18px">
                    @csrf
                    @method('PATCH')

                    {{-- Chọn sao --}}
                    <div style="margin-bottom:16px">
                        <label style="font-size:.85rem;font-weight:600;color:#374151;display:block;margin-bottom:8px">Điểm đánh giá *</label>
                        <div class="star-rating" style="display:flex;gap:6px;flex-direction:row-reverse;width:fit-content">
                            @for($s = 5; $s >= 1; $s--)
                                <input type="radio" name="evaluation_score" id="star{{ $s }}" value="{{ $s }}" style="display:none">
                                <label for="star{{ $s }}" style="font-size:2.2rem;color:#D1D5DB;cursor:pointer;transition:color .15s" onmouseover="highlightStars({{ $s }})" onmouseout="resetStars()" onclick="selectStar({{ $s }})">★</label>
                            @endfor
                        </div>
                        <div id="star-label" style="font-size:.82rem;color:#6B7280;margin-top:4px">Chưa chọn</div>
                    </div>

                    {{-- Nhận xét --}}
                    <div style="margin-bottom:16px">
                        <label style="font-size:.85rem;font-weight:600;color:#374151;display:block;margin-bottom:6px">Nhận xét của cấp trên</label>
                        <textarea name="evaluation_comment" rows="3" placeholder="Nhập nhận xét, góp ý…" style="width:100%;border:1px solid #D1D5DB;border-radius:8px;padding:10px 12px;font-size:.9rem;resize:vertical;outline:none;box-sizing:border-box;font-family:inherit" maxlength="2000"></textarea>
                    </div>

                    {{-- Nút hành động --}}
                    <div style="display:flex;gap:10px">
                        <button type="submit" name="action" value="approve"
                            style="flex:1;padding:10px;background:linear-gradient(135deg,#16A34A,#22C55E);color:#fff;border:none;border-radius:8px;font-size:.9rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px"
                            onclick="return confirmEval('approve')">
                            <i data-lucide="check-circle" style="width:16px;height:16px"></i> Duyệt Hoàn thành
                        </button>
                        <button type="submit" name="action" value="return"
                            style="flex:1;padding:10px;background:linear-gradient(135deg,#DC2626,#EF4444);color:#fff;border:none;border-radius:8px;font-size:.9rem;font-weight:600;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:6px"
                            onclick="return confirmEval('return')">
                            <i data-lucide="rotate-ccw" style="width:16px;height:16px"></i> Trả lại
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</section>
@endif

<script>
const starLabels = ['', 'Cần cải thiện', 'Dưới trung bình', 'Trung bình', 'Tốt', 'Xuất sắc'];
let selectedStar = 0;

function highlightStars(n) {
    document.querySelectorAll('.star-rating label').forEach((el, i) => {
        const val = 5 - i;
        el.style.color = val <= n ? '#F59E0B' : '#D1D5DB';
    });
}
function resetStars() {
    document.querySelectorAll('.star-rating label').forEach((el, i) => {
        const val = 5 - i;
        el.style.color = val <= selectedStar ? '#F59E0B' : '#D1D5DB';
    });
}
function selectStar(n) {
    selectedStar = n;
    const lbl = document.getElementById('star-label');
    if (lbl) lbl.textContent = n + ' sao — ' + (starLabels[n] || '');
}
function confirmEval(action) {
    if (!selectedStar) { alert('Vui lòng chọn số sao đánh giá.'); return false; }
    const msg = action === 'approve'
        ? 'Xác nhận DUYỆT HOÀN THÀNH công việc với ' + selectedStar + ' sao?'
        : 'Xác nhận TRẢ LẠI công việc để nhân viên tiếp tục?';
    return confirm(msg);
}
</script>
@endsection
