```html
<style>
    .navbar-custom {
        background: #ffffff;
        border-bottom: 1px solid #e9ecef;
        box-shadow: 0 2px 12px rgba(0,0,0,0.08);
    }

    .mobifone-logo {
        text-decoration: none;
    }

    .mobifone-logo .mobi {
        color: #0066cc;
        font-size: 32px;
        font-weight: 800;
        letter-spacing: -1px;
    }

    .mobifone-logo .fone {
        color: #e60012;
        font-size: 32px;
        font-weight: 800;
        letter-spacing: -1px;
    }

    .mobifone-logo .sub-title {
        font-size: 12px;
        color: #6c757d;
        margin-top: -5px;
    }

    .notification-btn {
        width: 42px;
        height: 42px;
        border-radius: 50%;
        border: none;
        background: #f8f9fa;
        transition: all 0.3s;
    }

    .notification-btn:hover {
        background: #e9ecef;
    }

    .user-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0066cc, #0099ff);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .dropdown-menu {
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }

    .notification-header {
        background: linear-gradient(135deg, #0066cc, #0099ff);
    }
</style>

<nav class="navbar navbar-expand-lg navbar-custom px-4 py-3 mb-4">

    
    <a class="navbar-brand mobifone-logo d-flex align-items-center" href="/dashboard">

        <div>
            <div>
                <span class="mobi">Mobi</span><span class="fone">Fone</span>
            </div>

            <div class="sub-title">
                Human Resource Management System
            </div>
        </div>
    </a>

    <!-- RIGHT -->
    <div class="d-flex align-items-center gap-3">

        
        <div class="dropdown">
            <button type="button"
                    class="notification-btn position-relative"
                    data-bs-toggle="dropdown">

                <i class="fas fa-bell text-secondary"></i>

                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    3
                </span>
            </button>

            <div class="dropdown-menu dropdown-menu-end shadow-lg mt-2"
                 style="width:350px;">

                <div class="p-3 text-white notification-header">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-bell me-2"></i>
                        Thông báo mới nhất
                    </h6>
                </div>

                <div style="max-height:300px; overflow-y:auto;">

                    <a href="#" class="dropdown-item p-3 border-bottom">

                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-tasks text-success fs-5"></i>
                            </div>

                            <div>
                                <strong>Công việc mới được giao</strong>
                                <div class="small text-muted">
                                    Bạn vừa được giao task:
                                    "Thiết kế giao diện tuần 2"
                                </div>
                            </div>
                        </div>

                    </a>

                    <a href="#" class="dropdown-item p-3 border-bottom">

                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-file-alt text-primary fs-5"></i>
                            </div>

                            <div>
                                <strong>Tài liệu mới cập nhật</strong>
                                <div class="small text-muted">
                                    Long vừa tải lên:
                                    CV_UngVien_NguyenVanA.pdf
                                </div>
                            </div>
                        </div>

                    </a>

                </div>

                <div class="text-center p-2">
                    <a href="#" class="text-decoration-none fw-bold">
                        Xem tất cả thông báo
                    </a>
                </div>

            </div>
        </div>

        <!-- USER -->
        <div class="dropdown">

            <button class="btn btn-light d-flex align-items-center gap-2 border-0 shadow-sm"
                    data-bs-toggle="dropdown">

                <div class="user-avatar">
                    <i class="fas fa-user"></i>
                </div>

                <span class="fw-semibold">
                    HOANG YEM
                </span>

                <i class="fas fa-chevron-down small"></i>

            </button>

            <ul class="dropdown-menu dropdown-menu-end shadow-lg mt-2">

                <li>
                    <a class="dropdown-item" href="#">
                        <i class="fas fa-user-cog me-2"></i>
                        Hồ sơ cá nhân
                    </a>
                </li>

                <li>
                    <hr class="dropdown-divider">
                </li>

                <li>
                    <form action="/logout" method="POST">
                        @csrf

                        <button type="submit"
                                class="dropdown-item text-danger">

                            <i class="fas fa-sign-out-alt me-2"></i>
                            Đăng xuất

                        </button>
                    </form>
                </li>

            </ul>

        </div>

    </div>

</nav>
```
