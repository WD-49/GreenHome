<!-- =========================================================
    Item Name: Carrot - Multipurpose eCommerce HTML Template.
    Author: ashishmaraviya
    Version: 2.1
    Copyright 2024
 ============================================================-->
<!DOCTYPE html>
<html lang="en" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="ecommerce, market, shop, mart, cart, deal, multipurpose, marketplace">
    <meta name="description" content="Carrot - Multipurpose eCommerce HTML Template.">
    <meta name="author" content="ashishmaraviya">

    <title>Carrot - Multipurpose eCommerce HTML Template</title>

    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets_client/assets/img/logo/favicon.png') }}">

    <!-- Icon CSS -->
    <link rel="stylesheet" href="{{ asset('assets_client/assets/css/vendor/materialdesignicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets_client/assets/css/vendor/remixicon.css') }}">

    <!-- Vendor -->
    <link rel="stylesheet" href="{{ asset('assets_client/assets/css/vendor/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('assets_client/assets/css/vendor/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets_client/assets/css/vendor/aos.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets_client/assets/css/vendor/range-slider.css') }}">
    <link rel="stylesheet" href="{{ asset('assets_client/assets/css/vendor/swiper-bundle.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets_client/assets/css/vendor/jquery.slick.css') }}">
    <link rel="stylesheet" href="{{ asset('assets_client/assets/css/vendor/slick-theme.css') }}">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('assets_client/assets/css/style.css') }}">
    @stack('styles')
    <!-- CSRF Token cho AJAX -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="body-bg-6">

    <!-- Loader -->
    <div id="cr-overlay">
        <span class="loader"></span>
    </div>

    <!-- Header -->
    <header>
        @include('client.partials.header')
        {{-- @auth
    @if (!Auth::user()->hasVerifiedEmail())
        <div class="email-verify-reminder">
            <a href="{{ route('profile.index') }}" title="Xác minh email để sử dụng đầy đủ tính năng!">
                <i class="ri-error-warning-line"></i>
                <span>Chưa xác minh email</span>
            </a>
        </div>
    @endif
@endauth --}}

    </header>

    <!-- Mobile menu -->
    <div class="cr-sidebar-overlay"></div>
    <div id="cr_mobile_menu" class="cr-side-cart cr-mobile-menu">
        <div class="cr-menu-title">
            <span class="menu-title">My Menu</span>
            <button type="button" class="cr-close">×</button>
        </div>
        <div class="cr-menu-inner">
            <div class="cr-menu-content">
                <ul>
                    <li class="dropdown drop-list">
                        <a href="index.html">Home</a>
                    </li>
                    <li class="dropdown drop-list">
                        <span class="menu-toggle"></span>
                        <a href="javascript:void(0)" class="dropdown-list">Category</a>
                        <ul class="sub-menu">
                            <li><a href="shop-left-sidebar.html">Shop Left sidebar</a></li>
                            <li><a href="shop-right-sidebar.html">Shop Right sidebar</a></li>
                            <li><a href="shop-full-width.html">Full Width</a></li>
                        </ul>
                    </li>
                    <li class="dropdown drop-list">
                        <span class="menu-toggle"></span>
                        <a href="javascript:void(0)" class="dropdown-list">product</a>
                        <ul class="sub-menu">
                            <li><a href="product-left-sidebar.html">product Left sidebar</a></li>
                            <li><a href="product-right-sidebar.html">product Right sidebar</a></li>
                            <li><a href="product-full-width.html">Product Full Width </a></li>
                        </ul>
                    </li>
                    <li class="dropdown drop-list">
                        <span class="menu-toggle"></span>
                        <a href="javascript:void(0)" class="dropdown-list">Pages</a>
                        <ul class="sub-menu">
                            <li><a href="about.html">About Us</a></li>
                            <li><a href="contact-us.html">Contact Us</a></li>
                            <li><a href="cart.html">Cart</a></li>
                            <li><a href="checkout.html">Checkout</a></li>
                            <li><a href="track-order.html">Track Order</a></li>
                            <li><a href="wishlist.html">Wishlist</a></li>
                            <li><a href="faq.html">Faq</a></li>
                            <li><a href="login.html">Login</a></li>
                            <li><a href="register.html">Register</a></li>
                            <li><a href="policy.html">Policy</a></li>
                        </ul>
                    </li>
                    <li class="dropdown drop-list">
                        <span class="menu-toggle"></span>
                        <a href="javascript:void(0)" class="dropdown-list">Blog</a>
                        <ul class="sub-menu">
                            <li><a href="blog-left-sidebar.html">Left Sidebar</a></li>
                            <li><a href="blog-right-sidebar.html">Right Sidebar</a></li>
                            <li><a href="blog-full-width.html">Full Width</a></li>
                            <li><a href="blog-detail-left-sidebar.html">Detail Left Sidebar</a></li>
                            <li><a href="blog-detail-right-sidebar.html">Detail Right Sidebar</a></li>
                            <li><a href="blog-detail-full-width.html">Detail Full Width</a></li>
                        </ul>
                    </li>
                    <li class="dropdown drop-list">
                        <span class="menu-toggle"></span>
                        <a href="javascript:void(0)">Element</a>
                        <ul class="sub-menu">
                            <li><a href="elements-products.html">Products</a></li>
                            <li><a href="elements-typography.html">Typography</a></li>
                            <li><a href="elements-buttons.html">Buttons</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div>


    @yield('content')





    <!-- Footer -->
    @include('client.partials.footer')

    <!-- Tab to top -->
    @include('client.partials.tabToTop')


    @include('client.partials.modalProduct')


    <!-- Cart -->
    @include('client.partials.miniCart')

    <!-- Side-tool -->
    @include('client.partials.sideTool')
    <!-- Voucher Modal -->
    @include('client.partials.voucherModal')

    <!-- Global Notify -->
    <div id="global-notify" style="display:none;position:fixed;z-index:9999;bottom:40px;right:40px;min-width:220px;">
        <div class="cr-cart-notify">
            <p class="compare-note" id="global-notify-message"></p>
        </div>
    </div>

    <!-- Các phần còn lại bạn thay đổi tương tự: -->
    <!-- Đổi tất cả các src="assets/..." thành src="{{ asset('assets_client/assets/...') }}" -->
    <!-- Đổi tất cả các href="assets/..." thành href="{{ asset('assets_client/assets/...') }}" -->

    <!-- Vendor Custom -->
    <script src="{{ asset('assets_client/assets/js/vendor/jquery-3.6.4.min.js') }}"></script>
    <script src="{{ asset('assets_client/assets/js/vendor/jquery.zoom.min.js') }}"></script>
    <script src="{{ asset('assets_client/assets/js/vendor/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets_client/assets/js/vendor/mixitup.min.js') }}"></script>
    <script src="{{ asset('assets_client/assets/js/vendor/range-slider.js') }}"></script>
    <script src="{{ asset('assets_client/assets/js/vendor/aos.min.js') }}"></script>
    <script src="{{ asset('assets_client/assets/js/vendor/swiper-bundle.min.js') }}"></script>
    <script src="{{ asset('assets_client/assets/js/vendor/slick.min.js') }}"></script>

    <!-- Main Custom -->
    <script src="{{ asset('assets_client/assets/js/main.js') }}"></script>

    {{-- js cho thông báo --}}
    {{-- do dòng này --}}
    @Vite(['resources/js/app.js']); 
    @stack('scripts')
</body>

</html>
