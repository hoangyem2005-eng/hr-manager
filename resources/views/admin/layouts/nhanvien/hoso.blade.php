@extends('admin.layouts.index')

@section('content')
<style>
    .profile-page {
        max-width: 1180px;
        margin: 24px auto;
        padding: 0 16px;
    }

    .profile-hero {
        background: #fff;
        border: 1px solid #dde8ef;
        border-radius: 8px;
        box-shadow: 0 10px 28px rgba(15, 45, 80, .08);
        overflow: hidden;
    }

    .profile-hero-bar {
        height: 8px;
        background: linear-gradient(90deg, #005bac, #00aeef, #16a34a);
    }

    .profile-hero-body {
        display: grid;
        grid-template-columns: minmax(0, 1.3fr) minmax(280px, .7fr);
        gap: 24px;
        padding: 28px;
    }

    .page-label {
        color: #005bac;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 8px;
    }

    .page-title {
        color: #13294b;
        font-size: 32px;
        font-weight: 700;
        margin-bottom: 10px;
    }

    .page-subtitle {
        color: #617083;
        margin-bottom: 0;
        max-width: 620px;
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-top: 24px;
    }

    .summary-item {
        border: 1px solid #e7eef4;
        border-radius: 8px;
        padding: 14px;
        background: #f8fbfd;
    }

    .summary-item strong {
        display: block;
        color: #13294b;
        font-size: 22px;
    }

    .summary-item span {
        color: #6c7a89;
        font-size: 13px;
    }

    .attach-panel {
        border: 1px dashed #8ab7df;
        border-radius: 8px;
        background: #f5fbff;
        padding: 18px;
    }

    .attach-icon {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        display: grid;
        place-items: center;
        background: #005bac;
        color: #fff;
        font-size: 22px;
        margin-bottom: 12px;
    }

    .attach-title {
        color: #13294b;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .attach-note {
        color: #667789;
        font-size: 14px;
        margin-bottom: 14px;
    }

    .file-name {
        color: #005bac;
        font-size: 14px;
        margin-top: 10px;
        min-height: 20px;
        word-break: break-word;
    }

    .content-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 340px;
        gap: 18px;
        margin-top: 18px;
    }

    .section-card {
        background: #fff;
        border: 1px solid #dde8ef;
        border-radius: 8px;
        box-shadow: 0 8px 22px rgba(15, 45, 80, .06);
        padding: 22px;
    }

    .section-title {
        color: #13294b;
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 16px;
    }

    .employee-table {
        margin-bottom: 0;
    }

    .employee-table thead {
        background: #005bac;
        color: #fff;
    }

    .employee-table td,
    .employee-table th {
        vertical-align: middle;
    }

    .status-pill {
        display: inline-block;
        border-radius: 999px;
        padding: 5px 10px;
        background: #e8f6ef;
        color: #157347;
        font-size: 13px;
        font-weight: 600;
    }

    .doc-list {
        display: grid;
        gap: 10px;
    }

    .doc-item {
        display: flex;
        gap: 12px;
        align-items: center;
        border: 1px solid #e8eef3;
        border-radius: 8px;
        padding: 12px;
        background: #fbfdff;
    }

    .doc-type {
        width: 42px;
        height: 42px;
        border-radius: 8px;
        display: grid;
        place-items: center;
        background: #eef6ff;
        color: #005bac;
        font-weight: 700;
        font-size: 13px;
        flex: 0 0 auto;
    }

    .doc-name {
        color: #17202a;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .doc-meta {
        color: #738293;
        font-size: 13px;
    }

    .doc-actions {
        margin-left: auto;
        flex: 0 0 auto;
    }

    @media (max-width: 900px) {
        .profile-hero-body,
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 640px) {
        .summary-grid {
            grid-template-columns: 1fr;
        }

        .profile-hero-body,
        .section-card {
            padding: 18px;
        }

        .page-title {
            font-size: 26px;
        }
    }
</style>

<div class="profile-page">
    <section class="profile-hero">
        <div class="profile-hero-bar"></div>
        <div class="profile-hero-body">
            <div>
                <div class="page-label">Hồ sơ nhân sự</div>
                <h1 class="page-title">Quản lý hồ sơ và tài liệu nhân viên</h1>
                <p class="page-subtitle">
                    Theo dõi thông tin nhân sự, tình trạng hồ sơ và đính kèm các tệp cần thiết cho quá trình quản lý.
                </p>

                <div class="summary-grid">
                    <div class="summary-item">
                        <strong>{{ $totalUsers }}</strong>
                        <span>Nhân viên</span>
                    </div>
                    <div class="summary-item">
                        <strong>12</strong>
                        <span>Hồ sơ cần duyệt</span>
                    </div>
                    <div class="summary-item">
                        <strong>{{ $totalDocuments }}</strong>
                        <span>Tệp mới trong tháng</span>
                    </div>
                </div>
            </div>

            <form class="attach-panel" action="{{ route('nhanvien.hoso.upload') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="attach-icon">+</div>
                <div class="attach-title">Đính kèm file hồ sơ</div>
                <p class="attach-note">
                    Hỗ trợ PDF, DOCX, XLSX, PNG, JPG. Dung lượng đề xuất tối đa 10MB mỗi tệp.
                </p>
                <select name="user_id" class="form-select mb-2">
                    <option value="">Chọn nhân viên liên quan</option>
                    @foreach($allUsers as $employee)
                        <option value="{{ $employee->id }}" @selected(old('user_id') == $employee->id)>
                            {{ $employee->name }} - {{ $employee->email }}
                        </option>
                    @endforeach
                </select>
                <label class="btn btn-primary w-100" for="profile_file">Chọn file đính kèm</label>
                <input id="profile_file" name="profile_file" type="file" class="d-none" accept=".pdf,.doc,.docx,.xls,.xlsx,.png,.jpg,.jpeg">
                <div id="selected-file-name" class="file-name">Chưa chọn file nào</div>
                @error('profile_file')
                    <div class="text-danger small mt-2">{{ $message }}</div>
                @enderror
                <button type="submit" class="btn btn-outline-secondary w-100 mt-2">Lưu file</button>
            </form>
        </div>
    </section>

    @if(session('thongbao'))
        <div class="alert alert-success mt-3 mb-0">
            {{ session('thongbao') }}
        </div>
    @endif

    <div class="content-grid">
        <section class="section-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <h2 class="section-title mb-0">Danh sách hồ sơ</h2>
                <a href="{{ url('admin/nhanvien/them') }}" class="btn btn-success">Thêm nhân viên</a>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover employee-table">
                    <thead>
                        <tr>
                            <th>Mã HS</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Trạng thái</th>
                            <th>Cập nhật</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>HS-{{ str_pad($user->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="status-pill">Đã tạo hồ sơ</span></td>
                                <td>{{ optional($user->updated_at)->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Chưa có nhân viên nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </section>

        <aside class="section-card">
            <h2 class="section-title">Tài liệu gần đây</h2>
            <div class="doc-list">
                @forelse($documents as $document)
                    <div class="doc-item">
                        <div class="doc-type">{{ $document['type'] }}</div>
                        <div>
                            <div class="doc-name">{{ $document['name'] }}</div>
                            <div class="doc-meta">
                                {{ number_format($document['size'] / 1024, 1) }} KB -
                                {{ \Carbon\Carbon::createFromTimestamp($document['modified'])->format('d/m/Y H:i') }}
                            </div>
                        </div>
                        <div class="doc-actions">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('nhanvien.hoso.download', ['filename' => $document['name']]) }}">
                                Tải
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-muted small">Chưa có file hồ sơ nào được lưu.</div>
                @endforelse
            </div>
        </aside>
    </div>
</div>

<script>
    const profileFileInput = document.getElementById('profile_file');
    const selectedFileName = document.getElementById('selected-file-name');

    profileFileInput.addEventListener('change', function () {
        selectedFileName.textContent = this.files.length
            ? this.files[0].name
            : 'Chưa chọn file nào';
    });
</script>
@endsection
