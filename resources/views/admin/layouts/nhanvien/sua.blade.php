@extends('admin.layouts.index')

@section('content')

<div class="container mt-4">
    <div class="card shadow">

```
    <div class="card-header bg-primary text-white">
        <h4 class="mb-0">
            Nhân viên
            <small class="text-light">| Sửa nhân viên</small>
        </h4>
    </div>

    <div class="card-body">

        {{-- Thông báo thành công --}}
        @if(session('thongbao'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('thongbao') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Hiển thị lỗi --}}
        @if(count($errors) > 0)
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ $nhanvien->ID }}" method="POST">

            @csrf

            <div class="mb-3">
                <label class="form-label fw-bold">
                    Mã nhân viên
                </label>

                <input type="text"
                       class="form-control"
                       value="{{ $nhanvien->MANV }}"
                       disabled>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">
                    Họ tên nhân viên
                </label>

                <input type="text"
                       class="form-control"
                       name="hoten"
                       value="{{ $nhanvien->HOTEN }}">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">
                    Giới tính
                </label>

                <select class="form-control"
                        name="gioitinh">

                    <option value="Nam"
                        {{ $nhanvien->GIOITINH == 'Nam' ? 'selected' : '' }}>
                        Nam
                    </option>

                    <option value="Nữ"
                        {{ $nhanvien->GIOITINH == 'Nữ' ? 'selected' : '' }}>
                        Nữ
                    </option>

                </select>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">
                    Ngày sinh
                </label>

                <input type="date"
                       class="form-control"
                       name="ngaysinh"
                       value="{{ $nhanvien->NGAYSINH }}">
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">
                    Phòng ban
                </label>

                <input type="text"
                       class="form-control"
                       name="phongban"
                       value="{{ $nhanvien->PHONGBAN }}">
            </div>

            <div class="d-flex justify-content-between">

                <div>

                    <button type="submit"
                            class="btn btn-success">
                        💾 Lưu cập nhật
                    </button>

                    <button type="reset"
                            class="btn btn-secondary">
                        🔄 Nhập lại
                    </button>

                </div>

                <a href="../danhsach"
                   class="btn btn-outline-primary">
                    📋 Xem danh sách
                </a>

            </div>

        </form>

    </div>
</div>
```

</div>
@endsection
