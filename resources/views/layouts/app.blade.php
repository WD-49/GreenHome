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
</head>
@stack('scripts')
<body class="body-bg-6">

    <!-- Loader -->
    <div id="cr-overlay">
        <span class="loader"></span>
    </div>

    <!-- Header -->
    <header>
        @include('client.partials.header')
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

    <!-- Modal -->
    <div class="modal fade quickview-modal" id="quickview" aria-hidden="true" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered cr-modal-dialog">
            <div class="modal-content">
                <button type="button" class="cr-close-model btn-close" data-bs-dismiss="modal"
                    aria-label="Close"></button>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5 col-sm-12 col-xs-12">
                            <div class="zoom-image-hover modal-border-image">
                                <img src="{{ asset('assets_client/assets/img/product/tab-1.jpg') }}"
                                    alt="product-tab-2" class="product-image">
                            </div>
                        </div>
                        <div class="col-md-7 col-sm-12 col-xs-12">
                            <div class="cr-size-and-weight-contain">
                                <h2 class="heading">Peach Seeds Of Change Oraganic Quinoa, Brown fruit</h2>
                                <p>Lorem Ipsum is simply dummy text of the printing and typesetting industry...</p>
                            </div>
                            <div class="cr-size-and-weight">
                                <div class="cr-review-star">
                                    <div class="cr-star">
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                        <i class="ri-star-fill"></i>
                                    </div>
                                    <p>( 75 Review )</p>
                                </div>
                                <div class="cr-product-price">
                                    <span class="new-price">$120.25</span>
                                    <span class="old-price">$123.25</span>
                                </div>
                                <div class="cr-size-weight">
                                    <h5><span>Size</span>/<span>Weight</span> :</h5>
                                    <div class="cr-kg">
                                        <ul>
                                            <li class="active-color">500gm</li>
                                            <li>1kg</li>
                                            <li>2kg</li>
                                            <li>5kg</li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="cr-add-card">
                                    <div class="cr-qty-main">
                                        <input type="text" value="1" class="quantity">
                                        <button type="button" id="add_model" class="plus">+</button>
                                        <button type="button" id="sub_model" class="minus">-</button>
                                    </div>
                                    <div class="cr-add-button">
                                        <button type="button" class="cr-button">Add to cart</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <!-- Cart -->
    @include('client.partials.miniCart')

    <!-- Side-tool -->
    @include('client.partials.sideTool')


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
    @stack('scripts')
</body>

</html>
