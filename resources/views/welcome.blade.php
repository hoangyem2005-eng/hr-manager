<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MobiFone Cà Mau - Trang chủ HRM</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-blue: #005bac;
            --brand-cyan: #00aeef;
            --brand-green: #16a34a;
            --ink: #14213d;
            --muted: #667085;
            --line: #dce8f1;
            --page: #f3f8fb;
            --text-contrast: #556075;
        }
        .dropdown-menu .dropdown-item {
            transition: background-color 0.15s ease-in-out; /* Giúp hiệu ứng chuyển màu mượt mà */
        }

        .dropdown-menu .dropdown-item:hover {
            background-color: #f8f9fa !important; /* Đổi sang màu xám nhạt khi rà chuột */
            color: inherit; /* Giữ nguyên màu chữ gốc */
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            background: var(--page);
            color: var(--ink);
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid var(--line);
        }

        .topbar-inner {
            max-width: 1180px;
            height: 68px;
            margin: 0 auto;
            padding: 0 18px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 700;
            color: var(--brand-blue);
            text-decoration: none;
        }

        .brand-mark {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            background: linear-gradient(135deg, var(--brand-blue), var(--brand-cyan));
            color: #fff;
            font-size: 20px;
        }

        .top-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }

        /* ==================== HIỆU ỨNG HOVER CHUÔNG THÔNG BÁO TỐI ƯU ==================== */
        .btn-bell-hover {
            transition: all 0.2s ease-in-out;
            cursor: pointer !important;
            border-radius: 8px;
        }

        .btn-bell-hover:hover {
            background-color: #f0f7ff !important;
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(0, 91, 172, 0.15);
        }

        .btn-bell-hover:hover .brand-mark-bell {
            background: var(--brand-blue) !important;
            color: #fff !important;
        }
        /* ============================================================================== */

        .home-wrap {
            max-width: 1180px;
            margin: 0 auto;
            padding: 34px 18px 46px;
        }

        .hero {
            display: grid;
            grid-template-columns: minmax(0, 1.08fr) minmax(320px, .92fr);
            gap: 20px;
            align-items: stretch;
        }

        .hero-main,
        .hero-side,
        .quick-card,
        .info-card {
            background: #fff;
            border: 1px solid var(--line);
            border-radius: 8px;
            box-shadow: 0 12px 30px rgba(20, 33, 61, .07);
        }

        .hero-main {
            padding: 34px;
            position: relative;
            overflow: hidden;
        }

        .hero-main::before {
            content: "";
            position: absolute;
            inset: 0 0 auto;
            height: 8px;
            background: linear-gradient(90deg, var(--brand-blue), var(--brand-cyan), var(--brand-green));
        }

        .eyebrow {
            color: var(--brand-blue);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .hero-title {
            max-width: 680px;
            margin: 0 0 14px;
            color: var(--ink);
            font-size: clamp(30px, 5vw, 52px);
            line-height: 1.08;
            font-weight: 750;
        }

        .hero-text {
            max-width: 650px;
            color: var(--muted);
            font-size: 17px;
            line-height: 1.65;
            margin-bottom: 24px;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .hero-side {
            padding: 24px;
            display: grid;
            gap: 14px;
        }

        .metric {
            display: grid;
            grid-template-columns: 52px minmax(0, 1fr);
            gap: 14px;
            align-items: center;
            padding: 14px;
            border: 1px solid #e8f0f6;
            border-radius: 8px;
            background: #fbfdff;
        }

        .metric-icon {
            width: 52px;
            height: 52px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            background: #eef7ff;
            color: var(--brand-blue);
            font-size: 24px;
        }

        .metric strong {
            display: block;
            font-size: 24px;
            line-height: 1.1;
        }

        .metric span {
            color: var(--muted);
            font-size: 14px;
        }

        .section-title {
            margin: 28px 0 14px;
            color: var(--ink);
            font-size: 22px;
            font-weight: 700;
        }

        .quick-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 14px;
        }

        .quick-card {
            min-height: 168px;
            padding: 20px;
            color: var(--ink);
            text-decoration: none;
            transition: transform .15s ease, border-color .15s ease, box-shadow .15s ease;
        }

        .quick-card:hover {
            transform: translateY(-3px);
            border-color: var(--brand-cyan);
            box-shadow: 0 16px 34px rgba(0, 91, 172, .12);
            color: var(--ink);
        }

        .quick-icon {
            width: 42px;
            height: 42px;
            border-radius: 8px;
            display: grid;
            place-items: center;
            margin-bottom: 14px;
            background: #eef7ff;
            color: var(--brand-blue);
            font-size: 21px;
        }

        .quick-card strong {
            display: block;
            font-size: 17px;
            margin-bottom: 8px;
        }

        .quick-card span {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.5;
        }

        .info-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
            margin-top: 14px;
        }

        .info-card {
            padding: 20px;
        }

        .info-card h2 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .info-card p {
            color: var(--muted);
            margin-bottom: 0;
            line-height: 1.55;
        }

        @media (max-width: 980px) {
            .hero,
            .info-row {
                grid-template-columns: 1fr;
            }

            .quick-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 620px) {
            .topbar-inner {
                height: auto;
                padding-top: 14px;
                padding-bottom: 14px;
                align-items: flex-start;
                flex-direction: column;
            }

            .top-actions {
                width: 100%;
                justify-content: stretch;
            }

            .top-actions .btn,
            .hero-actions .btn {
                width: 100%;
            }

            .hero-main,
            .hero-side {
                padding: 22px;
            }

            .quick-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="topbar-inner">
            <a class="brand" href="{{ url('/') }}">
                <span class="brand-mark">M</span>
                <span>MobiFone Cà Mau HRM</span>
            </a>

            <nav class="top-actions" aria-label="Liên kết nhanh">
                <!-- ==================== CỤM CHUÔNG THÔNG BÁO (TUẦN 2) ==================== -->
                <div class="dropdown d-inline-block me-2">
                    <button type="button" class="btn btn-link position-relative text-decoration-none p-2 border-0 btn-bell-hover" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="color: var(--brand-blue); font-size: 1.2rem;">
                        <span class="brand-mark" style="width: 36px; height: 36px; font-size: 16px; background: #eef7ff; color: var(--brand-blue); transition: all 0.2s;">🔔</span>
                        <!-- Ký hiệu số lượng thông báo mới -->
                        {{-- <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 0.25em 0.5em; margin-top: 5px; margin-left: -5px;">
                            3
                        </span> --}}
                    </button>
                    
                    <!-- Menu danh sách thông báo xổ xuống khi click -->
                    <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0 mt-2" aria-labelledby="page-header-notifications-dropdown" style="width: 320px; z-index: 1050;">
                        <div class="p-3 text-white rounded-top" style="background: var(--brand-blue);">
                            <h6 class="m-0 text-white fw-bold">Thông báo mới nhất</h6>
                        </div>
                        <div style="max-height: 250px; overflow-y: auto;">
                            <!-- Thông báo mẫu 1 -->
                            <<a href="#" class="dropdown-item text-wrap p-3 border-bottom d-block">
                                <h6 class="mb-1 small fw-bold text-success" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                    📌 THONG_BAO_SIEU_DAI_KHOONG_KHOANG_TRANG_ABC_XYZ_123456789_A_B_C_D_E_F_G_H_I_J_K_L_M_N_O_P_Q_R_S_T_U_V_W_X_Y_Z_MOBI_FONE_CA_MAU_HRM_PROJECT_TEST_CASE_NOTI_17_TRUNCATION_TESTING_LONG_STRING_WITHOUT_SPACE
                                </h6>
                                <p class="mb-0 text-muted" style="font-size: 13px;">Bạn vừa được giao task: "Thiết kế giao diện tĩnh tuần 2".</p>
                            </a>
                            <!-- Thông báo mẫu 2 -->
                            <a href="#" class="dropdown-item text-wrap p-3 border-bottom d-block">
                                <h6 class="mb-1 small fw-bold text-primary">📁 Tài liệu mới cập nhật</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px;">Long vừa tải lên tệp tài liệu: "CV_UngVien.pdf".</p>
                            </a>
                            <!-- Copy đoạn này và dán liên tiếp dưới nhau 8 lần -->
                            <a href="#" class="dropdown-item text-wrap p-3 border-bottom d-block">
                                <h6 class="mb-1 small fw-bold text-primary">📁 Tài liệu mới cập nhật</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px;">Long vừa tải lên tệp tài liệu: "CV_UngVien.pdf".</p>
                            </a>
                            <!-- Copy đoạn này và dán liên tiếp dưới nhau 8 lần -->
                            <a href="#" class="dropdown-item text-wrap p-3 border-bottom d-block">
                                <h6 class="mb-1 small fw-bold text-primary">📁 Tài liệu mới cập nhật</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px;">Long vừa tải lên tệp tài liệu: "CV_UngVien.pdf".</p>
                            </a>

                            <!-- Copy đoạn này và dán liên tiếp dưới nhau 8 lần -->
                            <a href="#" class="dropdown-item text-wrap p-3 border-bottom d-block">
                                <h6 class="mb-1 small fw-bold text-primary">📁 Tài liệu mới cập nhật</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px;">Long vừa tải lên tệp tài liệu: "CV_UngVien.pdf".</p>
                            </a>
                            <a href="#" class="dropdown-item text-wrap p-3 border-bottom d-block">
                                <h6 class="mb-1 small fw-bold text-primary">📁 Tài liệu mới cập nhật</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px;">Long vừa tải lên tệp tài liệu: "CV_UngVien.pdf".</p>
                            </a>
                            <a href="#" class="dropdown-item text-wrap p-3 border-bottom d-block">
                                <h6 class="mb-1 small fw-bold text-primary">📁 Tài liệu mới cập nhật</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px;">Long vừa tải lên tệp tài liệu: "CV_UngVien.pdf".</p>
                            </a>
                            <a href="#" class="dropdown-item text-wrap p-3 border-bottom d-block">
                                <h6 class="mb-1 small fw-bold text-primary">📁 Tài liệu mới cập nhật</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px;">Long vừa tải lên tệp tài liệu: "CV_UngVien.pdf".</p>
                            </a>
                            <a href="#" class="dropdown-item text-wrap p-3 border-bottom d-block">
                                <h6 class="mb-1 small fw-bold text-primary">📁 Tài liệu mới cập nhật</h6>
                                <p class="mb-0 text-muted" style="font-size: 13px;">Long vừa tải lên tệp tài liệu: "CV_UngVien.pdf".</p>
                            </a>
                        </div>
                        <div class="p-2 text-center border-top bg-light">
                            <a class="text-primary fw-bold text-decoration-none small" href="#thong-bao-he-thong">Xem tất cả</a>
                        </div>
                    </div>
                </div>
                <!-- ======================================================================= -->

                <a class="btn btn-outline-primary" href="{{ url('admin/nhanvien/hoso') }}">Hồ sơ nhân sự</a>
                <a class="btn btn-primary" href="{{ url('admin/nhanvien/danhsach') }}">Vào quản lý</a>
            </nav>
        </div>
    </header>

    <main class="home-wrap">
        <section class="hero">
            <div class="hero-main">
                <div class="eyebrow">Hệ thống quản lý nhân viên</div>
                <h1 class="hero-title">Trang chủ điều hành nhân sự MobiFone Cà Mau</h1>
                <p class="hero-text">
                    Theo dõi hồ sơ, cập nhật danh sách nhân viên, thêm nhân sự mới và xem thống kê nhanh trong cùng một giao diện làm việc.
                </p>

                <div class="hero-actions">
                    <a class="btn btn-primary btn-lg" href="{{ url('admin/nhanvien/hoso') }}">Mở hồ sơ nhân sự</a>
                    <a class="btn btn-outline-secondary btn-lg" href="{{ url('admin/nhanvien/them') }}">Thêm nhân viên</a>
                </div>
            </div>

            <aside class="hero-side">
                <div class="metric">
                    <div class="metric-icon">HS</div>
                    <div>
                        <strong>Hồ sơ</strong>
                        <span>Quản lý tài liệu và thông tin nhân sự</span>
                    </div>
                </div>
                <div class="metric">
                    <div class="metric-icon">NV</div>
                    <div>
                        <strong>Nhân viên</strong>
                        <span>Tra cứu, chỉnh sửa và cập nhật danh sách</span>
                    </div>
                </div>
                <div class="metric">
                    <div class="metric-icon">TK</div>
                    <div>
                        <strong>Thống kê</strong>
                        <span>Theo dõi dữ liệu nhân sự theo phòng ban</span>
                    </div>
                </div>
            </aside>
        </section>

        <h2 class="section-title">Thao tác nhanh</h2>
        <section class="quick-grid" aria-label="Thao tác nhanh">
            <a class="quick-card" href="{{ url('admin/nhanvien/hoso') }}">
                <div class="quick-icon">HS</div>
                <strong>Hồ sơ nhân sự</strong>
                <span>Xem hồ sơ, trạng thái và khu vực đính kèm tài liệu.</span>
            </a>
            <a class="quick-card" href="{{ url('admin/nhanvien/danhsach') }}">
                <div class="quick-icon">DS</div>
                <strong>Danh sách nhân viên</strong>
                <span>Tìm kiếm, xem và cập nhật thông tin nhân viên.</span>
            </a>
            <a class="quick-card" href="{{ url('admin/nhanvien/them') }}">
                <div class="quick-icon">+</div>
                <strong>Thêm nhân viên</strong>
                <span>Tạo thông tin nhân viên mới cho hệ thống.</span>
            </a>
            <a class="quick-card" href="{{ url('admin/nhanvien/thongke') }}">
                <div class="quick-icon">TK</div>
                <strong>Thống kê</strong>
                <span>Xem số liệu nhân sự và báo cáo tổng quan.</span>
            </a>
        </section>

        <!-- ==================== BẢNG TRUNG TÂM THÔNG BÁO TĨNH (TUẦN 2) ==================== -->
        <h2 class="section-title">Thông báo hệ thống</h2>
        <section id="thong-bao-he-thong" class="card shadow-sm border-0 mb-4 rounded-3 overflow-hidden">
            <div class="card-header bg-white py-3 border-bottom" style="border-color: var(--line) !important;">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-dark fs-5">💬 Cập nhật hoạt động mới nhất</span>
                    <span class="badge bg-danger rounded-pill px-2.5 py-1" style="font-size: 12px;">2 thông báo chưa đọc</span>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <!-- Item 1 -->
                    <div class="list-group-item p-3 border-bottom" style="background: #fbfdff;">
                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                            <h6 class="mb-0 text-success fw-bold">📌 Kế hoạch kiểm thử Tuần 2</h6>
                            <small class="text-muted">Vừa xong</small>
                        </div>
                        <p class="mb-0 text-secondary small">QA Leader đã tạo khung Master Test Plan trên Notion. Thành viên vui lòng vào bổ sung 20 Test Cases trước Chủ Nhật.</p>
                    </div>
                    <!-- Item 2 -->
                    <div class="list-group-item p-3">
                        <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                            <h6 class="mb-0 text-primary fw-bold">📁 Đồng bộ Cơ sở dữ liệu thành công</h6>
                            <small class="text-muted">15 phút trước</small>
                        </div>
                        <p class="mb-0 text-secondary small">Nhánh dữ liệu `long/db-profiles-documents` đã được tích hợp thành công vào hệ thống chính quản lý nhân sự.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="info-row">
            <div class="info-card">
                <h2>Giao diện tập trung</h2>
                <p>Trang chủ gom các lối vào quan trọng để giảm thao tác chuyển trang khi quản lý nhân sự hằng ngày.</p>
            </div>
            <div class="info-card">
                <h2>Sẵn sàng mở rộng</h2>
                <p>Các khu vực hồ sơ, file đính kèm và thống kê có thể nối thêm dữ liệu thật khi hoàn thiện backend.</p>
            </div>
        </section>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
