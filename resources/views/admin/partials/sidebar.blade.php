<!--- Sidemenu -->
<div id="sidebar-menu">

    {{-- logo --}}
    <div class="logo-box">
        <a class='logo logo-light' href='index.html'>
            <span class="logo-sm">
                <img src="assets/images/logo-sm.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="assets/images/logo-light.png" alt="" height="24">
            </span>
        </a>
        <a class='logo logo-dark' href='index.html'>
            <span class="logo-sm">
                <img src="assets/images/logo-sm.png" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="assets/images/logo-dark.png" alt="" height="24">
            </span>
        </a>
    </div>
    {{-- end logo --}}

    <ul id="side-menu">

        <li class="menu-title">Menu</li>

        <li>
            <a href="#sidebarDashboards" data-bs-toggle="collapse">
                <i data-feather="home"></i>
                <span> Dashboard </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="sidebarDashboards">
                <ul class="nav-second-level">
                    <li>
                        <a class='tp-link' href='index.html'>Analytical</a>
                    </li>
                    <li>
                        <a class='tp-link' href='ecommerce.html'>E-commerce</a>
                    </li>
                </ul>
            </div>
        </li>

        {{-- <li class="menu-title">Pages</li> --}}
        <li>
            <a href="#sidebarAuth" data-bs-toggle="collapse">
                <i data-feather="users"></i>
                <span> Tài khoản </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="sidebarAuth">
                <ul class="nav-second-level">
                    <li>
                        <a class='tp-link' href='auth-register.html'>Người dùng</a>
                    </li>
                    <li>
                        <a class='tp-link' href='auth-register.html'>Quản trị</a>
                    </li>
                </ul>
            </div>
        </li>

        <li>
    <a href="#sidebarError" data-bs-toggle="collapse">
        <i data-feather="package"></i>
        <span> Sản phẩm </span>
        <span class="menu-arrow"></span>
    </a>
    <div class="collapse" id="sidebarError">
        <ul class="nav-second-level">
            <li>
                <a class="tp-link" href="{{ route('admin.products.index') }}">Danh sách sản phẩm</a>
            </li>
            <li>
                <a class="tp-link" href="{{ route('admin.categories.index') }}">Danh mục</a>
            </li>
            <li>
                <a class="tp-link" href="{{ route('admin.brands.index') }}">Thương hiệu</a>
            </li>
            <li>
                <a class="tp-link" href="{{ route('admin.attribute.index') }}">Thuộc tính</a>
            </li>
        </ul>
    </div>
</li>


        <li>
            <a href="#sidebarExpages" data-bs-toggle="collapse">
                <i data-feather="calendar"></i>
                <span> Đơn hàng </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="sidebarExpages">
                <ul class="nav-second-level">
                    <li>
                        <a class='tp-link' href='pages-starter.html'>Danh sách đơn hàng</a>
                    </li>
                    <li>
                        <a class='tp-link' href='pages-profile.html'>Phương thức thanh toán</a>
                    </li>
                </ul>
            </div>
        </li>

        <li>
            <a class='tp-link' href='calendar.html'>
                <i data-feather="calendar"></i>
                <span> Bình luận </span>
            </a>
        </li>
        <li>
            <a class='tp-link' href='{{route('admin.blogs.index')}}'>
                <i data-feather="calendar"></i>
                <span> Bài viết </span>
            </a>
        </li>
        <li>
            <a class='tp-link' href='calendar.html'>
                <i data-feather="calendar"></i>
                <span> Đánh giá </span>
            </a>
        </li>

        {{-- <li class="menu-title mt-2">General</li> --}}

        <li>
            <a href="#sidebarBaseui" data-bs-toggle="collapse">
                <i data-feather="package"></i>
                <span> Khuyến mãi </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="sidebarBaseui">
                <ul class="nav-second-level">
                    <li>
                        <a class='tp-link' href='ui-accordions.html'>danh sách</a>
                    </li>
                    <li>
                        <a class='tp-link' href='ui-alerts.html'>lịch sử sử dụng</a>
                    </li>
                </ul>
            </div>
        </li>

        <li>
            <a class='tp-link' href='widgets.html'>
                <i data-feather="aperture"></i>
                <span> Banner </span>
            </a>
        </li>

        <li>
            <a href="#sidebarMaps" data-bs-toggle="collapse">
                <i data-feather="map"></i>
                <span> Maps </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="sidebarMaps">
                <ul class="nav-second-level">
                    <li>
                        <a class='tp-link' href='maps-google.html'>Google Maps</a>
                    </li>
                    <li>
                        <a class='tp-link' href='maps-vector.html'>Vector Maps</a>
                    </li>
                </ul>
            </div>
        </li>

    </ul>

</div>
<!-- End Sidebar -->
<div class="clearfix"></div>
