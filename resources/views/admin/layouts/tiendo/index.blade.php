@extends('admin.layouts.index')

@section('title', 'Tiến độ - Giám đốc')
@section('page_title', 'Tiến độ')

@section('content')
<section class="module-hero">
    <div>
        <div class="module-kicker">Progress operations</div>
        <h1 class="module-title">Theo dõi tiến độ công việc</h1>
        <p class="module-desc">Kiểm soát phần trăm hoàn thành, trạng thái vận hành và các việc quá hạn theo phạm vi quyền hạn.</p>
    </div>
    <div style="text-align:right">
        <div style="font-size:12px;color:#6B7280;font-weight:900">Tiến độ trung bình</div>
        <div style="font-size:30px;font-weight:900;color:#E4002B">{{ round($avgProgress) }}%</div>
    </div>
</section>

<section style="display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:12px">
    @foreach([
        'all' => ['Tất cả', $summary['all']],
        'todo' => ['Chờ xử lý', $summary['todo']],
        'doing' => ['Đang làm', $summary['doing']],
        'done' => ['Hoàn thành', $summary['done']],
        'overdue' => ['Quá hạn', $summary['overdue']],
    ] as $key => [$label, $value])
        <a href="{{ route('tiendo.index', ['filter' => $key]) }}" class="panel" style="padding:16px;text-decoration:none;border-color:{{ $filter === $key ? '#E4002B' : '#E5E7EB' }}">
            <div style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:#6B7280;font-weight:900">{{ $label }}</div>
            <div style="font-size:28px;font-weight:900;color:#111827;margin-top:6px">{{ $value }}</div>
        </a>
    @endforeach
</section>

<section class="panel">
    <div class="panel-head">
        <div class="panel-title"><i data-lucide="activity"></i>Bảng tiến độ</div>
        <form method="GET" action="{{ route('tiendo.index') }}" class="filters">
            <input type="hidden" name="filter" value="{{ $filter }}">
            <input class="input" name="search" value="{{ $search }}" placeholder="Tìm công việc">
            <button class="btn secondary" type="submit"><i data-lucide="search"></i>Tìm</button>
        </form>
    </div>
    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Mã</th><th>Công việc</th><th>Người nhận</th><th>Deadline</th><th>Tiến độ</th><th>Trạng thái</th><th>Cập nhật nhanh</th></tr></thead>
            <tbody>
                @forelse($tasks as $task)
                    @php
                        $statusClass = match($task->status) {
                            'Done', 'Hoàn thành' => 'done',
                            'In Progress', 'Đang làm' => 'doing',
                            'Overdue', 'Quá hạn' => 'overdue',
                            'Đang review' => 'review',
                            default => '',
                        };
                    @endphp
                    <tr>
                        <td class="code">WH-{{ str_pad($task->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td><strong style="color:#111827">{{ $task->task_name }}</strong></td>
                        <td>{{ $task->assignee->name ?? 'Chưa gán' }}</td>
                        <td>{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d/m/Y') : 'Không có' }}</td>
                        <td>
                            <div style="display:flex;align-items:center;gap:10px;min-width:150px">
                                <div style="height:7px;background:#E5E7EB;border-radius:99px;overflow:hidden;flex:1">
                                    <div style="height:100%;width:{{ $task->progress ?? 0 }}%;background:#E4002B;border-radius:99px"></div>
                                </div>
                                <strong>{{ $task->progress ?? 0 }}%</strong>
                            </div>
                        </td>
                        <td><span class="status {{ $statusClass }}">{{ $task->status }}</span></td>
                        <td>
                            <form method="POST" action="{{ route('tiendo.capnhat', $task->id) }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
                                @csrf @method('PATCH')
                                <input class="input" style="width:82px" type="number" min="0" max="100" name="progress" value="{{ $task->progress ?? 0 }}">
                                <select class="select" name="status">
                                    @foreach(['Todo' => 'Chờ xử lý', 'In Progress' => 'Đang làm', 'Done' => 'Hoàn thành', 'Overdue' => 'Quá hạn'] as $value => $label)
                                        <option value="{{ $value }}" {{ $task->status === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                <button class="btn secondary" type="submit">Lưu</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7"><div class="empty">Không có công việc phù hợp.</div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="padding:14px 16px;border-top:1px solid #E5E7EB">{{ $tasks->links() }}</div>
</section>
@endsection
