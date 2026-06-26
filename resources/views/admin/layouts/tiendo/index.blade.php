@extends('admin.layouts.index')

@section('content')
<div class="container py-4">
    <div class="mb-3">
        <h3 class="mb-1 text-primary fw-bold">Tiến độ công việc</h3>
        <p class="text-muted mb-0">Tổng quan trạng thái xử lý và các đầu việc cần chú ý.</p>
    </div>

    <div class="row g-3 mb-4">
        @foreach([
            'all' => 'Tất cả',
            'todo' => 'Chưa làm',
            'doing' => 'Đang làm',
            'done' => 'Hoàn thành',
            'overdue' => 'Trễ hạn',
        ] as $key => $label)
            <div class="col-md">
                <a href="{{ route('tiendo.index', ['filter' => $key]) }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 {{ $filter === $key ? 'border border-primary' : '' }}">
                        <div class="card-body">
                            <div class="text-muted small">{{ $label }}</div>
                            <div class="fs-3 fw-bold text-primary">{{ $summary[$key] }}</div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>Công việc</th>
                        <th>Người nhận</th>
                        <th>Hạn xử lý</th>
                        <th>Tiến độ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        @php
                            $isLate = $task->deadline && \Carbon\Carbon::parse($task->deadline)->isPast() && $task->status !== 'Done';
                            $percent = ['Todo' => 20, 'In Progress' => 60, 'Done' => 100, 'Overdue' => 35][$task->status] ?? 20;
                            $bar = $isLate ? 'danger' : ($task->status === 'Done' ? 'success' : 'primary');
                        @endphp
                        <tr>
                            <td class="fw-semibold">{{ $task->task_name }}</td>
                            <td>{{ $task->assignee->name ?? 'Chưa gán' }}</td>
                            <td>{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d/m/Y') : 'Không có' }}</td>
                            <td style="min-width: 220px;">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>{{ $isLate ? 'Trễ hạn' : $task->status }}</span>
                                    <span>{{ $percent }}%</span>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-{{ $bar }}" style="width: {{ $percent }}%"></div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">Chưa có công việc để hiển thị.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $tasks->links() }}</div>
</div>
@endsection
