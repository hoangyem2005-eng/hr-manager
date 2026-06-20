@extends('admin.layouts.index')

@section('content')

<div class="container" style="margin-top:20px;">

```
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

<form action="them" method="post">
    @csrf

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">
            Mã nhân viên :
        </label>

        <div class="col-sm-10">
            <input type="text"
                   name="manv"
                   class="form-control"
                   placeholder="Nhập mã nhân viên"
                   value="{{ old('manv') }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">
            Họ tên :
        </label>

        <div class="col-sm-10">
            <input type="text"
                   name="hoten"
                   class="form-control"
                   placeholder="Nhập họ tên nhân viên"
                   value="{{ old('hoten') }}">
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
            Ngày sinh :
        </label>

        <div class="col-sm-10">
            <input type="date"
                   name="ngaysinh"
                   class="form-control"
                   value="{{ old('ngaysinh') }}">
        </div>
    </div>

    <div class="form-group row">
        <label class="col-sm-2 col-form-label">
            Phòng ban :
        </label>

        <div class="col-sm-10">

            <select name="id_phongban"
                    class="form-control">

                <option value="">
                    -- Chọn phòng ban --
                </option>

                @foreach($phongban as $item)

                    <option value="{{ $item->ID }}">
                        {{ $item->TENPHONG }}
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
```

</div>
@endsection
