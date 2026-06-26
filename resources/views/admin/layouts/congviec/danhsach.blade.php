@extends('admin.layouts.index')

@section('content')
<div class="container py-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
        <div>
            <h3 class="mb-1 text-primary fw-bold">Công việc</h3>
            <p class="text-muted mb-0">Theo dõi danh sách công việc đã giao và trạng thái xử lý.</p>
        </div>
        <a href="{{ route('congviec.them') }}" class="btn btn-primary">Giao công việc</a>
    </div>

    @if(session('thongbao'))
        <div class="alert alert-success">{{ session('thongbao') }}</div>
    @endif

    <form method="GET" action="{{ route('congviec.danhsach') }}" class="card border-0 shadow-sm mb-3">
        <div class="card-body d-flex flex-wrap gap-2 align-items-end">
            <div>
                <label class="form-label fw-semibold">Lọc trạng thái</label>
                <select name="status" class="form-select" style="min-width: 220px;">
                    <option value="">Tất cả</option>
                    @foreach(['Todo' => 'Chưa thực hiện', 'In Progress' => 'Đang thực hiện', 'Done' => 'Hoàn thành', 'Overdue' => 'Trễ hạn'] as $value => $label)
                        <option value="{{ $value }}" {{ $status === $value ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-outline-primary">Lọc</button>
            <a href="{{ route('congviec.danhsach') }}" class="btn btn-outline-secondary">Xóa lọc</a>
        </div>
    </form>

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>Công việc</th>
                        <th>Người nhận</th>
                        <th>Người giao</th>
                        <th>Hạn xử lý</th>
                        <th>Trạng thái</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($tasks as $task)
                        <tr>
                            <td>
                                <div class="fw-semibold">{{ $task->task_name }}</div>
                                <div class="text-muted small">{{ \Illuminate\Support\Str::limit($task->description, 80) }}</div>
                            </td>
                            <td>{{ $task->assignee->name ?? 'Chưa gán' }}</td>
                            <td>{{ $task->assigner->name ?? 'Hệ thống' }}</td>
                            <td>{{ $task->deadline ? \Carbon\Carbon::parse($task->deadline)->format('d/m/Y') : 'Không có' }}</td>
                            <td>
                                @php
                                    $classes = [
                                        'Todo' => 'secondary',
                                        'In Progress' => 'primary',
                                        'Done' => 'success',
                                        'Overdue' => 'danger',
                                    ];
                                @endphp
                                <span class="badge bg-{{ $classes[$task->status] ?? 'secondary' }}">{{ $task->status }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('congviec.sua', $task->id) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                                <form method="POST" action="{{ route('congviec.xoa', $task->id) }}" onsubmit="return confirm('Xóa công việc này?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Chưa có công việc.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $tasks->links() }}</div>
</div>
@endsection
