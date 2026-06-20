@extends('admin.layouts.index')

@section('content')

<div class="container" style="margin-top:20px;">

<h3>NHÂN VIÊN <small class="text-muted">Thêm</small></h3>
<hr>

@if(session('thongbao'))
    <div class="alert alert-success">
        {{ session('thongbao') }}
    </div>
@endif

@if(count($errors) > 0)
    <div class="alert alert-danger">
        @foreach($errors->all() as $err)
            {{ $err }} <br>
        @endforeach
    </div>
@endif

<form action="{{ route('nhanvien.luu') }}" method="post">
    @csrf

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">
            Họ tên :
        </label>

        <div class="col-sm-10">
            <input type="text"
                   name="name"
                   class="form-control"
                   placeholder="Nhập họ tên nhân viên"
                   value="{{ old('name') }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">
            Email :
        </label>

        <div class="col-sm-10">
            <input type="text"
                   name="email"
                   class="form-control"
                   placeholder="Nhập email nhân viên"
                   value="{{ old('email') }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">
            Giới tính :
        </label>

        <div class="col-sm-10">
            <select name="gioitinh"
                    class="form-control">

                <option value="Nam">
                    Nam
                </option>

                <option value="Nữ">
                    Nữ
                </option>

            </select>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">
            Mật khẩu :
        </label>

        <div class="col-sm-10">
            <input type="password"
                   name="password"
                   class="form-control"
                   placeholder="Nhập mật khẩu">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">
            Phòng ban :
        </label>

        <div class="col-sm-10">

            <select name="department_id"
                    class="form-control">

                <option value="">
                    -- Chọn phòng ban --
                </option>

                @foreach($departments as $item)

                    <option value="{{ $item->id }}">
                        {{ $item->TENPHONG ?? $item->name ?? ('Phong ban #' . $item->id) }}
                    </option>

                @endforeach

            </select>

        </div>
    </div>

    <div class="text-center" style="margin-top:20px;">

        <button type="submit"
                class="btn btn-success">
            Lưu nhân viên
        </button>

        <button type="reset"
                class="btn btn-danger">
            Nhập lại
        </button>

        <a href="danhsach"
           class="btn btn-primary">
            Xem danh sách
        </a>

    </div>

</form>

</div>
@endsection
