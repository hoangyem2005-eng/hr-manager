@extends('admin.layouts.index')

@section('content')

<style>
    .card {
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .table thead {
        background: #005BAC;
        color: #fff;
    }

    .table tbody tr:hover {
        background: #f1f7ff;
        transition: 0.3s;
    }

    .btn-sm {
        padding: 5px 10px;
        border-radius: 5px;
    }

    .btn-danger {
        background: #d32f2f;
        border: none;
    }

    .btn-warning {
        background: #f9a825;
        border: none;
        color: #fff;
    }

    .header-title {
        color: #005BAC;
        font-weight: bold;
        margin-bottom: 15px;
    }
</style>

<div class="container" style="margin-top:20px;">

```
<div class="card">

    <!-- Title -->
    <h3 class="header-title">
        👨‍💼 NHÂN VIÊN
        <small style="color:#777;">| Danh sách</small>
    </h3>

    <!-- Nút -->
    <div style="display:flex;justify-content:space-between;margin-bottom:15px;">

        <a href="{{ url('admin/nhanvien/them') }}"
           class="btn btn-primary">
            ➕ Thêm nhân viên
        </a>

        <a href="{{ url('admin/nhanvien/thongke') }}"
           class="btn btn-success">
            📊 Xem thống kê
        </a>

    </div>

    <!-- Tìm kiếm -->
    <form method="GET"
          action="{{ url('admin/nhanvien/danhsach') }}"
          style="margin-bottom:15px;">

        <input type="text"
               name="keyword"
               class="form-control"
               style="width:250px;display:inline-block;"
               placeholder="Nhập tên nhân viên">

        <button type="submit"
                class="btn btn-primary">
            🔍 Tìm
        </button>

    </form>

    <!-- Thông báo -->
    @if(session('thongbao'))

        <div class="alert alert-success">
            {{ session('thongbao') }}
        </div>

    @endif

    <!-- Bảng -->
    <table class="table table-bordered table-hover text-center">

        <thead>
            <tr>
                <th>ID</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Ngày tạo</th>
                <th>Xóa</th>
                <th>Sửa</th>
            </tr>
        </thead>

        <tbody>

            @foreach($users as $user)

            <tr>

                <td>{{ $user->id }}</td>

                <td>{{ $user->name }}</td>

                <td>{{ $user->email }}</td>

                <td>{{ $user->created_at }}</td>

                <td>
                    <a href="{{ url('admin/nhanvien/xoa/'.$user->id) }}"
                       class="btn btn-danger btn-sm"
                       onclick="return confirm('Bạn có chắc muốn xóa?')">
                        Xóa
                    </a>
                </td>

                <td>
                    <a href="{{ url('admin/nhanvien/sua/'.$user->id) }}"
                       class="btn btn-warning btn-sm">
                        Sửa
                    </a>
                </td>

            </tr>

            @endforeach

        </tbody>

    </table>

</div>
```

</div>

@endsection
