<!--- Sidemenu -->
<div id="sidebar-menu">

    {{-- logo --}}
    {{-- <div class="logo-box">
        <a class='logo logo-light' href="{{ url('index.html') }}">
            <span class="logo-sm">
                <img src="{{ asset('public/assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('public/assets/images/logo-light.png') }}" alt="" height="24">
            </span>
        </a>
        <a class='logo logo-dark' href="{{ url('index.html') }}">
            <span class="logo-sm">
                <img src="{{ asset('public/assets/images/logo-sm.png') }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('public/assets/images/logo-dark.png') }}" alt="" height="24">
            </span>
        </a>
    </div> --}}
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
                        <a class='tp-link' href="{{ url('index.html') }}">Analytical</a>
                    </li>
                    <li>
                        <a class='tp-link' href="{{ url('ecommerce.html') }}">E-commerce</a>
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
                        <a class='tp-link' href="{{ route('admin.account.listUsers') }}">Người dùng</a>
                    </li>
                    <li>
                        <a class='tp-link' href="{{ route('admin.account.listAdmins') }}">Quản trị</a>

                    </li>
                </ul>
            </div>
        </li>

        <li>
            <a href="#sidebarProduct" data-bs-toggle="collapse">
                <i data-feather="package"></i>

                <span> Sản phẩm </span>
                <span class="menu-arrow"></span>
            </a>
            <div class="collapse" id="sidebarProduct">
                <ul class="nav-second-level">
                    <li>
                        <a class='tp-link' href="{{ route('admin.products.index') }}">Danh sách sản phẩm</a>
                    </li>
                    <li>
                        <a class='tp-link' href="{{ route('admin.categories.index') }}">Danh mục</a>
                    </li>
                    <li>
                        <a class='tp-link' href="{{ route('admin.brands.index') }}">Thương hiệu</a>
                    </li>
                    <li>
                        <a class='tp-link' href="{{ route('admin.attribute.index') }}">Thuộc tính</a>
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
                        <a class='tp-link' href="{{ route('admin.orders.index') }}">Danh sách đơn hàng</a>

                    </li>
                    <li>
                        <a class='tp-link' href="{{ route('admin.paymentMethods.index') }}">Phương thức thanh toán</a>
                    </li>
                </ul>
            </div>
        </li>
        <li>
            <a class='tp-link' href='{{ route('admin.comments.index') }}'>
                <i data-feather="message-square"></i>
                <span> Bình luận </span>
            </a>
        </li>
        <li>
            <a class='tp-link' href='{{ route('admin.blogs.index') }}'>
                <i data-feather="calendar"></i>
                <span> Bài viết </span>
            </a>
        </li>
        <li>
            <a class='tp-link' href='
            {{ route('admin.reviews.index') }}'>
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
                        <a class='tp-link' href='{{ route('admin.discount.index') }}'>Danh sách</a>
                    </li>
                    <li>
                        <a class='tp-link' href='{{ route('admin.discount.history') }}'>Lịch sử dùng mã</a>
                    </li>
                </ul>
            </div>
        </li>

        <li>
            <a class='tp-link' href="{{ route('admin.banners.index') }}">
                <i data-feather="aperture"></i>
                <span> Banner </span>
            </a>
        </li>
        <li>
            <a class='tp-link' href="{{ route('admin.blog_categories.index') }}">
                <i data-feather="file-text"></i>
                <span> Blog </span>
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
                        <a class='tp-link' href="{{ url('maps-google.html') }}">Google Maps</a>
                    </li>
                    <li>
                        <a class='tp-link' href="{{ url('maps-vector.html') }}">Vector Maps</a>
                    </li>
                </ul>
            </div>
        </li>

    </ul>

</div>
<!-- End Sidebar -->
<div class="clearfix"></div>
