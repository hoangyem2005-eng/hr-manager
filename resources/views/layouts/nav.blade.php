<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom px-4 py-2 mb-4 shadow-sm d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <a class="navbar-brand fw-bold text-primary fs-4" href="/dashboard">
            <i class="fas fa-user-shield me-2"></i>HR Manager
        </a>
    </div>

    <div class="d-flex align-items-center gap-3">
        
        <div class="dropdown">
            <button type="button" class="btn btn-light position-relative rounded-circle p-2" id="page-header-notifications-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="width: 40px; height: 40px;">
                <i class="fas fa-bell text-secondary"></i>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; padding: 0.25em 0.5em;">
                    3
                </span>
            </button>
            
            <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg border-0 mt-2" aria-labelledby="page-header-notifications-dropdown" style="width: 320px;">
                <div class="p-3 bg-primary text-white rounded-top">
                    <h6 class="m-0 text-white fw-bold"><i class="fas fa-envelope-open-text me-2"></i>Thông báo mới nhất</h6>
                </div>
                <div style="max-height: 250px; overflow-y: auto;">
                    <a href="#" class="dropdown-item text-wrap p-3 border-bottom d-block">
                        <div class="d-flex align-items-start">
                            <div class="bg-light p-2 rounded-circle me-3 text-success">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fs-6 fw-bold text-dark">📌 Công việc mới được giao</h6>
                                <p class="mb-0 small text-muted">Bạn vừa được giao task: "Thiết kế giao diện tĩnh tuần 2".</p>
                            </div>
                        </div>
                    </a>
                    <a href="#" class="dropdown-item text-wrap p-3 border-bottom d-block">
                        <div class="d-flex align-items-start">
                            <div class="bg-light p-2 rounded-circle me-3 text-primary">
                                <i class="fas fa-file-alt"></i>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fs-6 fw-bold text-dark">📁 Tài liệu mới cập nhật</h6>
                                <p class="mb-0 small text-muted">Long vừa tải lên tệp tài liệu: "CV_UngVien_NguyenVanA.pdf".</p>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="p-2 text-center border-top">
                    <a class="btn btn-sm btn-link text-primary fw-bold text-decoration-none" href="javascript:void(0)">
                        Xem tất cả thông báo
                    </a>
                </div>
            </div>
        </div>
        <div class="dropdown">
            <button class="btn btn-light dropdown-toggle d-flex align-items-center gap-2 border-0" type="button" id="userMenuDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="fas fa-user"></i>
                </div>
                <span class="fw-semibold text-dark small">HOANG YEM</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2" aria-labelledby="userMenuDropdown">
                <li><a class="dropdown-item small" href="#"><i class="fas fa-user-cog me-2 text-muted"></i>Hồ sơ cá nhân</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="/logout" method="POST" class="d-inline">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <button type="submit" class="dropdown-item small text-danger"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</button>
                    </form>
                </li>
            </ul>
        </div>

    </div>
</nav>