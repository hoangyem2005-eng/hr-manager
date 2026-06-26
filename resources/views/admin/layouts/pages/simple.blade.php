@extends('admin.layouts.index')

@section('content')
<div class="container py-4">
    <div class="card border-0 shadow-sm overflow-hidden">
        <div class="p-4 text-white" style="background: linear-gradient(90deg, #005bac, #00aeef);">
            <h3 class="fw-bold mb-2">{{ $title }}</h3>
            <p class="mb-0">{{ $subtitle }}</p>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                @foreach($items as $item)
                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100">
                            <div class="text-muted small">{{ $item['label'] }}</div>
                            <div class="fs-4 fw-bold text-primary">{{ $item['value'] }}</div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="alert alert-info mt-4 mb-0">
                Giao diện chức năng đã sẵn sàng. Có thể nối thêm bảng dữ liệu và form xử lý nghiệp vụ ở bước tiếp theo.
            </div>
        </div>
    </div>
</div>
@endsection
