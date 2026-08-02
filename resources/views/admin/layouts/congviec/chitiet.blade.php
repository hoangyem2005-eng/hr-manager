@extends('admin.layouts.index')

@section('title', 'Chi tiết công việc - Giám đốc')
@section('page_title', 'Chi tiết công việc')

@section('content')
<section class="module-hero">
    <div>
        <div class="module-kicker">Task detail</div>
        <h1 class="module-title">{{ $task->task_name }}</h1>
        <p class="module-desc">Mã WH-{{ str_pad($task->id, 3, '0', STR_PAD_LEFT) }} - theo dõi người giao, người nhận, deadline, tiến độ và file liên quan.</p>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap">
        <a href="{{ route('congviec.danhsach') }}" class="btn secondary"><i data-lucide="arrow-left"></i>Danh sách</a>
        @if($task->is_proposal && (int) $task->proposal_step === 2)
            <a href="{{ route('congviec.sua', $task->id) }}" class="btn primary" style="background:#16A34A;border-color:#16A34A"><i data-lucide="check"></i>Giao việc (Phê duyệt)</a>
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
                <tr><td>Deadline</td><td>{{ $task->deadline ? $task->deadline->format('d/m/Y') : 'Không có' }}</td></tr>
                <tr>
                    <td>Trạng thái</td>
                    <td>
                        @php
                            $displayStatus = match($task->status) {
                                'Done', 'Hoàn thành' => 'Hoàn thành',
                                'In Progress', 'Đang làm' => 'Đang làm',
                                'Todo', 'Chờ xử lý' => 'Chờ xử lý',
                                'Overdue', 'Quá hạn' => 'Quá hạn',
                                'Đang review' => 'Đang review',
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
                                @if(Auth::user()->isLeader() && !Auth::user()->isDirector() && $document->review_status === \App\Models\Document::STATUS_MANAGER_REVIEW)
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
@endsection
