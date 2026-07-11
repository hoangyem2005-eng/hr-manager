@extends('admin.layouts.index')

@section('title', ($title ?? 'Công việc') . ' - Giám đốc')
@section('page_title', $title ?? 'Công việc')

@section('content')
<section class="module-hero">
    <div>
        <div class="module-kicker">Task form</div>
        <h1 class="module-title">{{ $title ?? 'Công việc' }}</h1>
        <p class="module-desc">Nhập rõ người phụ trách, deadline, trạng thái và tiến độ để dashboard, tiến độ và chuông thông báo đồng bộ chính xác.</p>
    </div>
    <a href="{{ route('congviec.danhsach') }}" class="btn secondary"><i data-lucide="arrow-left"></i>Quay lại</a>
</section>

<section class="panel">
    <div class="panel-head">
        <div class="panel-title"><i data-lucide="clipboard-edit"></i>Thông tin công việc</div>
    </div>
    @if ($errors->any())
        <div class="flash error" style="margin:18px">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif
    <form method="POST" action="{{ $action }}" enctype="multipart/form-data" class="grid-form">
        @csrf
        @if($task->exists)
            @method('PUT')
        @endif

        <div class="field full">
            <label>Tên công việc</label>
            <input class="input" type="text" name="task_name" value="{{ old('task_name', $task->task_name) }}" required placeholder="VD: Hoàn thiện báo cáo KPI phòng Nhân sự">
        </div>

        <div class="field full">
            <label>Mô tả</label>
            <textarea class="textarea" name="description" placeholder="Yêu cầu, đầu ra cần có, ghi chú phối hợp...">{{ old('description', $task->description) }}</textarea>
        </div>

        <div class="field">
            <label>Người nhận</label>
            <select class="select" name="assigned_to">
                <option value="">Chưa gán</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('assigned_to', $task->assigned_to) == $user->id ? 'selected' : '' }}>{{ $user->name }} - {{ $user->email }}</option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label>Deadline</label>
            <input class="input" type="date" name="deadline" value="{{ old('deadline', optional($task->deadline)->format('Y-m-d') ?? $task->deadline) }}">
        </div>

        <div class="field">
            <label>Trạng thái</label>
            <select class="select" name="status" required>
                @foreach(['Todo' => 'Chờ xử lý', 'In Progress' => 'Đang làm', 'Done' => 'Hoàn thành', 'Overdue' => 'Quá hạn'] as $value => $label)
                    <option value="{{ $value }}" {{ old('status', $task->status) === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="field">
            <label>Tiến độ (%)</label>
            <input class="input" type="number" min="0" max="100" name="progress" value="{{ old('progress', $task->progress ?? 0) }}">
        </div>

        <div class="field full">
            <label>File đính kèm</label>
            <input class="input" type="file" name="attachments[]" multiple>
        </div>

        <div class="form-actions">
            <a class="btn secondary" href="{{ route('congviec.danhsach') }}">Hủy</a>
            <button class="btn primary" type="submit"><i data-lucide="save"></i>Lưu công việc</button>
        </div>
    </form>
</section>
@endsection
