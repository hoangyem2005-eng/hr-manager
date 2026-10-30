@extends('admin.layouts.index')

@section('title', 'Công việc - Giám đốc')
@section('page_title', 'Công việc')

@section('content')
<section class="module-hero">
    <div>
        <div class="module-kicker">Quản lý công việc</div>
        <h1 class="module-title">Quản trị công việc</h1>
        <p class="module-desc">Theo dõi toàn bộ đầu việc theo phạm vi quyền hạn, lọc nhanh theo trạng thái và mở chi tiết để kiểm tra tiến độ, deadline, file đính kèm.</p>
    </div>
    <a href="{{ route('congviec.taomoi') }}" class="btn primary"><i data-lucide="plus"></i>Giao công việc</a>
</section>

<section class="panel">
    <div class="panel-head">
        <div class="panel-title"><i data-lucide="list-checks"></i>Danh sách công việc</div>
        <form method="GET" action="{{ route('congviec.danhsach') }}" class="filters">
            <input class="input" name="search" value="{{ $search }}" placeholder="Tìm theo tên công việc">
            <select class="select" name="status">
                <option value="">Tất cả trạng thái</option>
                @foreach(['Todo' => 'Chờ xử lý', 'In Progress' => 'Đang làm', 'Done' => 'Hoàn thành', 'Overdue' => 'Quá hạn'] as $value => $label)
                    <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <button class="btn secondary" type="submit"><i data-lucide="search"></i>Lọc</button>
            <a class="btn secondary" href="{{ route('congviec.danhsach') }}">Xóa lọc</a>
        </form>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead>
                <tr>
                    <th>Mã</th>
                    <th>Công việc</th>
                    <th>Người nhận</th>
                    <th>Người giao</th>
                    <th>Deadline</th>
                    <th>Tiến độ</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tasks as $task)
                    @php
                        $statusClass = match($task->status) {
                            'Done', 'Hoàn thành' => 'done',
                            'In Progress', 'Đang làm' => 'doing',
                            'Overdue', 'Quá hạn' => 'overdue',
                            'Đang review', 'Chờ đánh giá' => 'review',
                            default => '',
                        };
                        $displayStatus = match($task->status) {
                            'Done', 'Hoàn thành' => 'Hoàn thành',
                            'In Progress', 'Đang làm' => 'Đang làm',
                            'Todo', 'Chờ xử lý' => 'Chờ xử lý',
                            'Overdue', 'Quá hạn' => 'Quá hạn',
                            'Đang review', 'Chờ đánh giá' => 'Chờ đánh giá',
                            default => $task->status,
                        };
                        $isOverdue = in_array($task->status, ['Overdue', 'Quá hạn']) || (!in_array($task->status, ['Done', 'Hoàn thành']) && $task->deadline && \Carbon\Carbon::parse($task->deadline)->endOfDay()->isPast());
                        $isDueSoon = false;
                        $daysLeft = null;
                        if (!$isOverdue && !in_array($task->status, ['Done', 'Hoàn thành']) && $task->deadline) {
                            $dlEnd = \Carbon\Carbon::parse($task->deadline)->endOfDay();
                            if ($dlEnd->isFuture()) {
                                $daysLeft = (int) \Carbon\Carbon::now()->startOfDay()->diffInDays($dlEnd->copy()->startOfDay(), false);
                                if ($daysLeft <= 3 && $daysLeft >= 0) {
                                    $isDueSoon = true;
                                }
                            }
                        }
                    @endphp
                    <tr>
                        <td class="code">WH-{{ str_pad($task->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <strong style="color:#111827">{{ $task->task_name }}</strong>
                            <div style="font-size:12px;color:#6B7280;margin-top:4px">{{ \Illuminate\Support\Str::limit($task->description, 90) }}</div>
                        </td>
                        <td>{{ $task->assignee->name ?? 'Chưa gán' }}</td>
                        <td>{{ $task->creator->name ?? 'Hệ thống' }}</td>
                        <td style="{{ $isOverdue ? 'color:#DC2626;font-weight:800;' : ($isDueSoon ? 'color:#D97706;font-weight:800;' : '') }}">
                            {{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d/m/Y') : 'Không có' }}
                            @if($isOverdue)
                                <br><span style="font-size:10px;padding:2px 6px;background:#FEE2E2;color:#DC2626;border-radius:4px;font-weight:900;border:1px solid #FCA5A5;display:inline-block;margin-top:4px;">QUÁ HẠN</span>
                            @elseif($isDueSoon)
                                <br><span style="font-size:10px;padding:2px 6px;background:#FEF3C7;color:#D97706;border-radius:4px;font-weight:900;border:1px solid #FCD34D;display:inline-block;margin-top:4px;">
                                    @if($daysLeft === 0)
                                        HẾT HẠN HÔM NAY
                                    @else
                                        CÒN {{ $daysLeft }} NGÀY
                                    @endif
                                </span>
                            @endif
                        </td>
                        <td><strong>{{ $task->progress ?? 0 }}%</strong></td>
                        <td><span class="status {{ $statusClass }}">{{ $displayStatus }}</span></td>
                        <td>
                            <div style="display:flex;gap:8px;flex-wrap:wrap">
                                <a class="btn secondary" href="{{ route('congviec.chitiet', $task->id) }}">Chi tiết</a>
                                <a class="btn secondary" href="{{ route('congviec.sua', $task->id) }}">Sửa</a>
                                <form method="POST" action="{{ route('congviec.xoa', $task->id) }}" onsubmit="return confirm('Xóa công việc này?')">
                                    @csrf @method('DELETE')
                                    <button class="btn danger" type="submit">Xóa</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8"><div class="empty">Chưa có công việc nào.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 16px;border-top:1px solid #E5E7EB">{{ $tasks->links() }}</div>
</section>
@endsection
