@extends('admin.layouts.index')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1 text-primary fw-bold">Phòng ban</h3>
            <p class="text-muted mb-0">Quản lý danh sách phòng ban và số nhân viên trực thuộc.</p>
        </div>
        <a href="{{ route('phongban.them') }}" class="btn btn-primary">Thêm phòng ban</a>
    </div>

    @if(session('thongbao'))
        <div class="alert alert-success">{{ session('thongbao') }}</div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-primary">
                    <tr>
                        <th>ID</th>
                        <th>Tên phòng ban</th>
                        <th>Nhân viên</th>
                        <th>Ngày tạo</th>
                        <th class="text-end">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($departments as $department)
                        <tr>
                            <td>{{ $department->id }}</td>
                            <td class="fw-semibold">{{ $department->TENPHONG }}</td>
                            <td>{{ $department->users_count }}</td>
                            <td>{{ optional($department->created_at)->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <a href="{{ route('phongban.sua', $department->id) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                                <form method="POST" action="{{ route('phongban.xoa', $department->id) }}" onsubmit="return confirm('Xóa phòng ban này?')" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Chưa có phòng ban.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $departments->links() }}</div>
</div>
@endsection
