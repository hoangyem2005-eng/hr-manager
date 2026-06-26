@extends('admin.layouts.index')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h3 class="text-primary fw-bold mb-1">Sửa phòng ban</h3>
                    <p class="text-muted mb-4">Cập nhật tên phòng ban trong hệ thống.</p>

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            @foreach ($errors->all() as $error)
                                <div>{{ $error }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form method="POST" action="{{ route('phongban.capnhat', $department->id) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tên phòng ban</label>
                            <input type="text" name="TENPHONG" value="{{ old('TENPHONG', $department->TENPHONG) }}" class="form-control">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                            <a href="{{ route('phongban.danhsach') }}" class="btn btn-outline-secondary">Quay lại</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
