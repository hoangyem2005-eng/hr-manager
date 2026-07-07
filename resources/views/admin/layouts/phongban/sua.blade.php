@extends('admin.layouts.app')

@section('title', 'Sửa phòng ban - Giám đốc')
@section('page_title', 'Sửa phòng ban')

@section('head_extra')
<style>
    .pb-hero { background: #fff; border: 1px solid #E5E7EB; border-radius: 8px; padding: 20px; display: flex; justify-content: space-between; align-items: center; gap: 18px; margin-bottom: 18px; }
    .pb-kicker { color: #E4002B; font-size: 11px; font-weight: 900; letter-spacing: .16em; text-transform: uppercase; }
    .pb-title { color: #111827; font-size: 24px; font-weight: 900; margin-top: 6px; }
    .pb-desc { color: #6B7280; font-size: 13px; line-height: 1.6; margin-top: 6px; }
    .pb-panel { background: #fff; border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden; }
    .pb-panel-head { min-height: 54px; padding: 12px 16px; border-bottom: 1px solid #E5E7EB; display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 900; color: #111827; }
    .pb-btn { min-height: 36px; border-radius: 8px; border: 1px solid transparent; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 0 12px; text-decoration: none; font-family: inherit; font-size: 13px; font-weight: 800; cursor: pointer; }
    .pb-btn-primary { background: #E4002B; color: #fff; border-color: #E4002B; }
    .pb-btn-secondary { background: #fff; border-color: #D1D5DB; color: #374151; }
    .pb-form { display: grid; gap: 16px; padding: 20px; }
    .pb-field label { display: block; font-size: 12px; font-weight: 900; color: #374151; margin-bottom: 6px; }
    .pb-input { width: 100%; border: 1px solid #D1D5DB; border-radius: 8px; padding: 0 11px; min-height: 40px; font-family: inherit; font-size: 13px; background: #fff; color: #111827; }
    .pb-actions { display: flex; justify-content: flex-end; gap: 10px; padding-top: 16px; border-top: 1px solid #E5E7EB; }
    .pb-error { background: #FEF2F2; color: #B91C1C; border: 1px solid #FECACA; border-radius: 8px; padding: 12px 14px; font-size: 13px; font-weight: 800; margin-bottom: 16px; }
</style>
@endsection

@section('content')
<div class="space-y-5">
    <div class="pb-hero">
        <div>
            <div class="pb-kicker">Edit department</div>
            <h1 class="pb-title">{{ $department->TENPHONG ?? $department->name }}</h1>
            <p class="pb-desc">Cập nhật tên phòng ban. Thay đổi này sẽ phản ánh ở dashboard, nhân sự và các bộ lọc công việc.</p>
        </div>
        <a href="{{ route('phongban.danhsach') }}" class="pb-btn pb-btn-secondary"><i data-lucide="arrow-left"></i> Quay lại</a>
    </div>

    <div class="pb-panel">
        <div class="pb-panel-head"><i data-lucide="pencil"></i> Thông tin phòng ban</div>
        <form method="POST" action="{{ route('phongban.capnhat', $department->id) }}" class="pb-form">
            @csrf @method('PUT')
            @if ($errors->any())
                <div class="pb-error">
                    @foreach ($errors->all() as $error)<div>{{ $error }}</div>@endforeach
                </div>
            @endif
            <div class="pb-field">
                <label>Tên phòng ban</label>
                <input class="pb-input" name="TENPHONG" value="{{ old('TENPHONG', $department->TENPHONG) }}" required>
            </div>
            <div class="pb-actions">
                <a href="{{ route('phongban.danhsach') }}" class="pb-btn pb-btn-secondary">Hủy</a>
                <button class="pb-btn pb-btn-primary" type="submit"><i data-lucide="save"></i> Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>
@endsection
