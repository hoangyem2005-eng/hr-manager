<!-- NAVBAR -->

<div class="navbar">
    <div class="nav-container">

```
    <!-- Logo -->
    <div class="logo">📱 MobiFone HRM</div>

    <!-- Menu -->
    <div class="menu">

        <a href="{{ url('/') }}" class="nav-link">
            Trang chủ
        </a>

        <!-- Nhân viên -->
        <div class="dropdown-click">

            <div class="dropdown-toggle" onclick="toggleNhanVien()">
                Nhân viên
                <span id="arrow-nhanvien">▼</span>
            </div>

            <div id="menu-nhanvien" class="dropdown-menu-click">

                <a href="{{ url('admin/nhanvien/danhsach') }}">
                    Danh sách nhân viên
                </a>

                <a href="{{ url('admin/nhanvien/them') }}">
                    Thêm nhân viên
                </a>

            </div>

        </div>

        <!-- Phòng ban -->
        <div class="dropdown-click">

            <div class="dropdown-toggle" onclick="togglePhongBan()">
                Phòng ban
                <span id="arrow-phongban">▼</span>
            </div>

            <div id="menu-phongban" class="dropdown-menu-click">

                <a href="{{ url('admin/phongban/danhsach') }}">
                    Danh sách phòng ban
                </a>

                <a href="{{ url('admin/phongban/them') }}">
                    Thêm phòng ban
                </a>

            </div>

        </div>

        <!-- Công việc -->
        <div class="dropdown-click">

            <div class="dropdown-toggle" onclick="toggleCongViec()">
                Công việc
                <span id="arrow-congviec">▼</span>
            </div>

            <div id="menu-congviec" class="dropdown-menu-click">

                <a href="{{ url('admin/congviec/danhsach') }}">
                    Danh sách công việc
                </a>

                <a href="{{ url('admin/congviec/them') }}">
                    Giao công việc
                </a>

            </div>

        </div>

        <!-- Tiến độ -->
        <div class="dropdown-click">

            <div class="dropdown-toggle" onclick="toggleTienDo()">
                Tiến độ
                <span id="arrow-tiendo">▼</span>
            </div>

            <div id="menu-tiendo" class="dropdown-menu-click">

                <a href="#">
                    Tất cả công việc
                </a>

                <a href="#">
                    Đang thực hiện
                </a>

                <a href="#">
                    Chưa hoàn thành
                </a>

                <a href="#">
                    Hoàn thành đúng hạn
                </a>

                <a href="#">
                    Hoàn thành sớm
                </a>

                <a href="#">
                    Trễ hạn
                </a>

            </div>

        </div>

        <a href="#" class="nav-link">
            Chấm công
        </a>

        <a href="#" class="nav-link">
            Nghỉ phép
        </a>

        <a href="#" class="nav-link">
            Báo cáo
        </a>

        <!-- Right -->
        <div class="menu-right">

            @guest

                <a href="{{ url('admin/dangnhap') }}" class="nav-link">
                    Đăng nhập
                </a>

            @endguest

            @auth

                <span class="nav-link">
                    👤 {{ Auth::user()->name }}
                </span>

                <a href="{{ url('dangxuat') }}" class="nav-link">
                    Đăng xuất
                </a>

            @endauth

        </div>

    </div>

</div>
```

</div>

<!-- CSS -->

<style>

.navbar{
    background:linear-gradient(90deg,#005BAC,#00AEEF);
    box-shadow:0 2px 10px rgba(0,0,0,0.2);
    font-family:Arial;
}

.nav-container{
    display:flex;
    justify-content:space-between;
    align-items:center;
    max-width:1400px;
    margin:auto;
    padding:0 20px;
    height:65px;
}

.logo{
    color:white;
    font-size:20px;
    font-weight:bold;
}

.menu{
    display:flex;
    align-items:center;
    gap:15px;
}

.nav-link{
    color:white;
    text-decoration:none;
    padding:8px 12px;
    border-radius:6px;
    transition:0.25s;
}

.nav-link:hover{
    background:rgba(255,255,255,0.15);
}

.dropdown-click{
    position:relative;
}

.dropdown-toggle{
    color:white;
    cursor:pointer;
    padding:8px 12px;
    border-radius:6px;
}

.dropdown-toggle:hover{
    background:rgba(255,255,255,0.15);
}

#arrow-nhanvien,
#arrow-phongban,
#arrow-congviec,
#arrow-tiendo{
    font-size:12px;
    margin-left:5px;
    transition:0.3s;
}

.dropdown-menu-click{
    display:none;
    position:absolute;
    top:42px;
    left:0;
    background:white;
    border-radius:8px;
    min-width:220px;
    overflow:hidden;
    box-shadow:0 5px 15px rgba(0,0,0,0.2);
}

.dropdown-menu-click a{
    display:block;
    padding:12px;
    color:#005BAC;
    text-decoration:none;
}

.dropdown-menu-click a:hover{
    background:#EAF5FF;
}

.show{
    display:block;
}

.rotate{
    transform:rotate(180deg);
}

</style>

<!-- JS -->

<script>

function toggleNhanVien() {
    document.getElementById("menu-nhanvien").classList.toggle("show");
    document.getElementById("arrow-nhanvien").classList.toggle("rotate");
}

function togglePhongBan() {
    document.getElementById("menu-phongban").classList.toggle("show");
    document.getElementById("arrow-phongban").classList.toggle("rotate");
}

function toggleCongViec() {
    document.getElementById("menu-congviec").classList.toggle("show");
    document.getElementById("arrow-congviec").classList.toggle("rotate");
}

function toggleTienDo() {
    document.getElementById("menu-tiendo").classList.toggle("show");
    document.getElementById("arrow-tiendo").classList.toggle("rotate");
}

document.addEventListener("click", function(e) {

    const dropdowns = document.querySelectorAll(".dropdown-click");

    dropdowns.forEach(dropdown => {

        if (!dropdown.contains(e.target)) {

            dropdown.querySelectorAll(".dropdown-menu-click")
            .forEach(menu => {
                menu.classList.remove("show");
            });

            dropdown.querySelectorAll("span")
            .forEach(arrow => {
                arrow.classList.remove("rotate");
            });

        }

    });

});

</script>
