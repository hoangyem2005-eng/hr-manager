@extends('admin.layouts.index')

@section('title', 'Công việc - Giám đốc')
@section('page_title', 'Công việc')

@section('content')
<section class="module-hero">
    <div>
        <div class="module-kicker">Task control</div>
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
                            'Đang review' => 'review',
                            default => '',
                        };
                    @endphp
                    <tr>
                        <td class="code">WH-{{ str_pad($task->id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <strong style="color:#111827">{{ $task->task_name }}</strong>
                            <div style="font-size:12px;color:#6B7280;margin-top:4px">{{ \Illuminate\Support\Str::limit($task->description, 90) }}</div>
                        </td>
                        <td>{{ $task->assignee->name ?? 'Chưa gán' }}</td>
                        <td>{{ $task->creator->name ?? 'Hệ thống' }}</td>
                        <td>{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d/m/Y') : 'Không có' }}</td>
                        <td><strong>{{ $task->progress ?? 0 }}%</strong></td>
                        <td><span class="status {{ $statusClass }}">{{ $task->status }}</span></td>
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
