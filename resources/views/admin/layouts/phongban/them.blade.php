@extends('admin.layouts.index')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="text-primary fw-bold mb-1">Thêm phòng ban</h3>
                    <p class="text-muted mb-4">Tạo phòng ban để gán nhân viên vào đúng đơn vị.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('phongban.luu') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tên phòng ban</label>
                            <input type="text" name="TENPHONG" value="{{ old('TENPHONG') }}" class="form-control" placeholder="Ví dụ: Phòng Kinh doanh">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Lưu phòng ban</button>
                            <a href="{{ route('phongban.danhsach') }}" class="btn btn-outline-secondary">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
