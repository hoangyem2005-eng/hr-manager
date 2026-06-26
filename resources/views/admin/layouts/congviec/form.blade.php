@extends('admin.layouts.index')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="text-primary fw-bold mb-1">{{ $title ?? 'Công việc' }}</h3>
                    <p class="text-muted mb-4">Tạo hoặc cập nhật đầu việc và nhân viên phụ trách.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ $action }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tên công việc</label>
                            <input type="text" name="task_name" value="{{ old('task_name', $task->task_name) }}" class="form-control" placeholder="Nhập tên công việc">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Mô tả</label>
                            <textarea name="description" rows="4" class="form-control" placeholder="Mô tả yêu cầu, đầu ra cần có">{{ old('description', $task->description) }}</textarea>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Nhân viên nhận</label>
                                <select name="assigned_to" class="form-select">
                                    <option value="">Chưa gán</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Hạn xử lý</label>
                                <input type="date" name="deadline" value="{{ old('deadline', $task->deadline) }}" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Trạng thái</label>
                                <select name="status" class="form-select">
                                    @foreach(['Todo' => 'Chưa thực hiện', 'In Progress' => 'Đang thực hiện', 'Done' => 'Hoàn thành', 'Overdue' => 'Trễ hạn'] as $value => $label)
                                        <option value="{{ $value }}" {{ old('status', $task->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <button type="submit" class="btn btn-primary">Lưu công việc</button>
                            <a href="{{ route('congviec.danhsach') }}" class="btn btn-outline-secondary">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
