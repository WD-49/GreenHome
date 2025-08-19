@extends('layouts.app')
@section('content')
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
    <script>
        AOS.init();
    </script>
    @push('styles')
        <style>
            /* Ảnh mặc định cho Grid View */
            .product-img {
                width: 100%;
                height: 225px;
                object-fit: cover;
                border-radius: 6px;
                display: block;
            }

            /* Cha chứa ảnh - Grid View */
            .cr-left,
            .cr-product-image {
                width: 100%;
                height: 225px;
            }

            /* List View - Kích thước cố định */
            .grid-row-active .cr-left,
            .grid-row-active .cr-product-image {
                width: 350px;
                height: 280px;
                flex-shrink: 0;
            }

            .grid-row-active .product-img {
                width: 350px;
                height: 280px;
                object-fit: cover;
            }

            .cr-blog-image img {
                width: 100%;
                height: 300px;
                /* hoặc chiều cao bạn muốn */
                object-fit: cover;
                display: block;
            }
        </style>
    @endpush

    @php
        $defaultBanner = $banners->where('type', 'slider')->first();
    @endphp

    {{-- Thông báo cho Login Google, Fb --}}
    <div class="toast-container position-fixed top-0 end-0 p-3">
        @if (session('success'))
            <div class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if (session('error'))
            <div class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('error') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="toast align-items-center text-bg-warning border-0" role="alert" aria-live="assertive"
                aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">
                        {{ session('warning') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"
                        aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>
    <script>
        // JS cho phần thông báo Login Google FB
        document.addEventListener('DOMContentLoaded', function() {
            var toastElList = [].slice.call(document.querySelectorAll('.toast'))
            var toastList = toastElList.map(function(toastEl) {
                return new bootstrap.Toast(toastEl, {
                    autohide: true,
                    delay: 5000 // 5 giây
                })
            })
            toastList.forEach(toast => toast.show())
        });
    </script>

    <!-- Hero slider -->
    <section class="section-hero padding-b-100 next">
        <div class="cr-slider swiper-container">
            <div class="swiper-wrapper">

                {{-- Banner có priority 1 --}}
                @foreach ($banners as $banner)
                    @if ($banner->type == 'slider')
                        <div class="swiper-slide">
                            <a href="{{ $banner->link ?? '#' }}">
                                <div class="cr-hero-banner"
                                    style="background-image: url('{{ Storage::url(Str::replaceFirst('storage/', '', $banner->img)) }}');
            background-size: cover;
            background-position: center;">

                                    <div class="container">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <div class="cr-left-side-contain slider-animation text-white">
                                                    <h1>{{ $banner->name ?? '' }}</h1>
                                                    <p>{!! $banner->description ?? '' !!}</p>
                                                    {{-- <div class="cr-last-buttons">
                                                    <a href="{{ $banner->link }}" class="cr-button">Xem Cửa Hàng</a>
                                                </div> --}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endif
                @endforeach
            </div>
            {{-- Swiper pagination --}}
            <div class="swiper-pagination"></div>
        </div>
    </section>

    <!-- Categories -->
    <section class="section-categories padding-b-100">
        <div class="container">
            <div class="row mb-minus-24">
                {{-- Danh sách tab danh mục --}}
                <div class="col-lg-4 col-12 mb-24">
                    <div class="cr-categories">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            @foreach ($topCategories as $index => $category)
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link center-categories-inner {{ $loop->first ? 'active' : '' }}"
                                        id="tab-{{ $category->id }}" data-bs-toggle="tab"
                                        data-bs-target="#content-{{ $category->id }}" type="button" role="tab"
                                        aria-controls="content-{{ $category->id }}"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                        {{ $category->name }} <span>({{ $category->products->count() }} items)</span>
                                    </button>
                                </li>
                            @endforeach
                            <li class="nav-item" role="presentation">
                                <a class="center-categories-inner cr-view-more" href="shop-left-sidebar.html">
                                    Xem thêm
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Nội dung tab --}}
                {{-- filepath: c:\laragon\www\GreenHome-main\resources\views\client\pages\home.blade.php --}}
                <div class="col-lg-8 col-12 mb-24 d-flex align-items-stretch">
                    <div class="tab-content w-100" id="myTabContent">
                        @foreach ($topCategories as $index => $category)
                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                id="content-{{ $category->id }}" role="tabpanel"
                                aria-labelledby="tab-{{ $category->id }}">
                                <div class="row mb-minus-24 h-100">
                                    @foreach ($banners as $banner)
                                        @if ($banner->category_id == $category->id)
                                            {{-- Hiển thị banner nếu có --}}
                                            @php
                                                $imgPath = Str::replaceFirst('storage/', '', $banner->img);
                                            @endphp
                                            <div class="col-6 cr-categories-box mb-4 d-flex align-items-stretch">
                                                <div class="cr-side-categories w-100" style="height:100%;">
                                                    <a href="{{ $banner->link ?? '#' }}"
                                                        style="display:block;height:100%;width:100%;">
                                                        <div
                                                            style="position:relative; height:100%; width:100%; border-radius:8px; overflow:hidden;">
                                                            <img src="{{ asset($banner->img) }}" alt="{{ $banner->name }}"
                                                                style="width:100%;height:100%;object-fit:cover;display:block;position:absolute;top:0;left:0;">
                                                            <div
                                                                style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.25);z-index:1;">
                                                            </div>
                                                            <div class="categories-inner"
                                                                style="position:absolute;top:10px;left:10px;z-index:2;">
                                                            </div>
                                                            <div
                                                                style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.25);border-radius:8px;z-index:1;">
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>

        </div>
        </div>
    </section>





    <!-- Popular product -->
    <section class="section-popular-product-shape padding-b-100">
        <div class="container" data-aos="fade-up" data-aos-duration="2000">
            <div class="row">
                <div class="col-lg-12">
                    <div class="mb-30">
                        <div class="cr-banner">
                            <h2>Sản phẩm nổi bật</h2>
                        </div>
                        <div class="cr-banner-sub-title">
                            <p>Tổng hợp những sản phẩm được yêu thích và đánh giá cao nhất – chất lượng vượt trội, thiết kế
                                ấn tượng và luôn dẫn đầu xu hướng</p>
                        </div>
                    </div>
                </div>
            </div>
            {{-- filepath: c:\laragon\www\GreenHome-main\resources\views\client\pages\home.blade.php --}}

            <div class="product-content row mb-minus-24" id="MixItUpDA2FB7">
                {{-- Cột trái: Danh mục --}}
                <div class="col-xl-3 col-lg-4 col-12 mb-24">
                    <div class="cr-product-tabs">
                        <ul>
                            <li class="active" data-filter="all" onclick="showTab('all')">All</li>
                            @foreach ($categoriesWithTopProducts as $category)
                                <li data-filter=".{{ $category->slug }}" onclick="showTab('cat-{{ $category->id }}')">
                                    {{ $category->name }}
                                </li>
                            @endforeach
                        </ul>
                    </div>

                </div>

                {{-- Cột phải: Sản phẩm --}}
                <div class="col-xl-9 col-lg-8 col-12 mb-24">

                    <div id="tab-all" class="product-tab-content fade-tab show">
                        <div class="row mb-minus-24">
                            @foreach ($popularProducts as $product)
                                <div class="col-xxl-3 col-xl-4 col-6 cr-product-box mb-24">
                                    <div class="cr-product-card">
                                        {{-- Hiển thị ảnh sản phẩm --}}
                                        <div class="cr-product-image">
                                            <div class="cr-image-inner zoom-image-hover">
                                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                                            </div>
                                            <div class="cr-side-view">
                                                <a href="javascript:void(0);" class="wishlist-button"
                                                    data-product-id="{{ $product->id }}">
                                                    @if (in_array($product->id, $wishlistProductIds ?? []))
                                                        <i class="ri-heart-fill text-danger"></i>
                                                    @else
                                                        <i class="ri-heart-line"></i>
                                                    @endif
                                                </a>
                                                <a class="model-oraganic-product" data-bs-toggle="modal" href="#quickview"
                                                    role="button" data-product-id="{{ $product->id }}">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                            </div>

                                        </div>
                                        <div class="cr-product-details">
                                            <div class="cr-brand">
                                                <a
                                                    href="{{ route('shop.index', ['categories[]' => $product->category->id]) }}">{{ $product->category->name ?? '' }}</a>
                                            </div>
                                            <a href="{{ route('productDetail', $product->slug) }}"
                                                class="title">{{ $product->name }}</a>

                                            {{-- ✅ Hiển thị đúng giá biến thể đầu tiên --}}
                                            <p class="cr-price">
                                                @php
                                                    $variant = $product->productVariants->first();
                                                @endphp

                                                @if ($variant)
                                                    <span class="new-price">
                                                        {{ number_format($variant->price, 0, ',', '.') }}₫
                                                    </span>
                                                @else
                                                    <span class="new-price text-muted">Chưa có giá</span>
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>


                    {{-- Tab từng danh mục: 8 sản phẩm mới nhất --}}
                    @foreach ($categories2 as $category)
                        <div id="tab-cat-{{ $category->id }}" class="product-tab-content" style="display:none;">
                            <div class="row mb-minus-24">

                                {{-- 👉 Sản phẩm trong danh mục --}}
                                @foreach ($category->products as $product)
                                    <div class="col-xxl-3 col-xl-4 col-6 cr-product-box mb-24">
                                        <div class="cr-product-card">
                                            <div class="cr-product-image">
                                                <div class="cr-image-inner zoom-image-hover">
                                                    <img src="{{ Storage::url($product->image) }}"
                                                        alt="{{ $product->name }}">
                                                </div>
                                                <div class="cr-side-view">
                                                    <a href="javascript:void(0);" class="wishlist-button"
                                                        data-product-id="{{ $product->id }}">
                                                        @if (in_array($product->id, $wishlistProductIds ?? []))
                                                            <i class="ri-heart-fill text-danger"></i>
                                                        @else
                                                            <i class="ri-heart-line"></i>
                                                        @endif
                                                    </a>
                                                    <a class="model-oraganic-product" data-bs-toggle="modal"
                                                        href="#quickview" role="button"
                                                        data-product-id="{{ $product->id }}">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                </div>

                                            </div>
                                            <div class="cr-product-details">
                                                <div class="cr-brand">
                                                    <a
                                                        href="{{ route('shop.index', ['categories[]' => $category->id]) }}">{{ $category->name }}</a>
                                                </div>
                                                <a href="{{ route('productDetail', $product->slug) }}"
                                                    class="title">{{ $product->name }}</a>

                                                <p class="cr-price">
                                                    @if ($variant = $product->productVariants->first())
                                                        <span class="new-price">
                                                            {{ number_format($variant->price, 0, ',', '.') }}₫
                                                        </span>
                                                    @else
                                                        <span class="new-price text-muted">Chưa có giá</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach


                </div>
            </div>

            <script>
                function showTab(tabId) {
                    document.querySelectorAll('.product-tab-content').forEach(el => el.style.display = 'none');
                    document.getElementById('tab-' + tabId).style.display = 'block';
                    document.querySelectorAll('.cr-product-tabs ul li').forEach(el => el.classList.remove('active'));
                    if (tabId === 'all') {
                        document.querySelector('.cr-product-tabs ul li[data-filter="all"]').classList.add('active');
                    } else {
                        document.querySelector('.cr-product-tabs ul li[data-filter=".cat-' + tabId.split('-')[1] + '"]').classList
                            .add('active');
                    }
                }
                document.addEventListener('DOMContentLoaded', function() {
                    showTab('all');
                });
            </script>
        </div>
    </section>

    <!-- Product banner -->
    @php use Illuminate\Support\Str; @endphp

    <section class="section-product-banner padding-b-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cr-banner-slider swiper-container">
                        <div class="swiper-wrapper">
                            @foreach ($banners as $banner)
                                @if ($banner->type == 'slider' && $banner->status == 1)
                                    @php
                                        $imgPath = Str::replaceFirst('storage/', '', $banner->img);
                                    @endphp
                                    <div class="swiper-slide" data-aos="fade-up" data-aos-duration="1000">
                                        <a href="{{ $banner->link ?? '#' }}">
                                            <div class="position-relative rounded overflow-hidden shadow"
                                                style="height: 300px;">

                                                {{-- Ảnh banner --}}
                                                <img src="{{ Storage::url($imgPath) }}" alt="{{ $banner->name }}"
                                                    class="w-100 h-100 object-cover">

                                                {{-- Chữ và nút chồng lên ảnh --}}
                                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center text-center text-white p-3"
                                                    style="background: rgba(0, 0, 0, 0.3);">
                                                    <h5 class="fw-bold" style="font-size: 24px;">{!! $banner->name ?? '' !!}
                                                    </h5>
                                                    <p>{{ $banner->sub_title ?? '' }}</p>

                                                </div>

                                            </div>
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section class="section-services padding-b-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="cr-services-border" data-aos="fade-up" data-aos-duration="2000">
                        <div class="cr-service-slider swiper-container">
                            <div class="swiper-wrapper">
                                <div class="swiper-slide">
                                    <div class="cr-services">
                                        <div class="cr-services-image">
                                            <i class="ri-red-packet-line"></i>
                                        </div>
                                        <div class="cr-services-contain">
                                            <h4> Đóng gói sản phẩm</h4>
                                            <p>Chúng tôi đóng gói sản phẩm cẩn thận, đảm bảo an toàn tuyệt đối trong quá
                                                trình vận chuyển đến tay bạn..</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="cr-services">
                                        <div class="cr-services-image">
                                            <i class="ri-customer-service-2-line"></i>
                                        </div>
                                        <div class="cr-services-contain">
                                            <h4>Hỗ trợ 24/7</h4>
                                            <p>Đội ngũ chăm sóc khách hàng luôn sẵn sàng hỗ trợ bạn mọi lúc, mọi nơi – kể cả
                                                cuối tuần và ngày lễ.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="cr-services">
                                        <div class="cr-services-image">
                                            <i class="ri-truck-line"></i>
                                        </div>
                                        <div class="cr-services-contain">
                                            <h4> Giao hàng trong 5 ngày</h4>
                                            <p>Cam kết giao hàng nhanh chóng trong vòng 5 ngày làm việc, đúng hẹn và đúng
                                                chất lượng.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="swiper-slide">
                                    <div class="cr-services">
                                        <div class="cr-services-image">
                                            <i class="ri-money-dollar-box-line"></i>
                                        </div>
                                        <div class="cr-services-contain">
                                            <h4> Thanh toán an toàn</h4>
                                            <p>Mọi giao dịch được mã hóa và bảo mật tuyệt đối, giúp bạn yên tâm khi thanh
                                                toán trực tuyến.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Popular product -->
    <section class="section-popular margin-b-100">
        <div class="container">
            <div class="row">
                <div class="col-xxl-7 col-xl-6 col-lg-6 col-md-12" data-aos="fade-up" data-aos-duration="2000">
                    <div class="cr-twocolumns-product">
                        @foreach ($products as $product)
                            <div class="slick-slide">
                                <div class="cr-product-card">
                                    <div class="cr-product-image">
                                        <div class="cr-image-inner zoom-image-hover">
                                            <img src="{{ asset('storage/' . $product->image) }}"
                                                alt="{{ $product->name }}">
                                        </div>
                                        <div class="cr-side-view">
                                            <a href="javascript:void(0);" class="wishlist-button"
                                                data-product-id="{{ $product->id }}">
                                                @if (in_array($product->id, $wishlistProductIds ?? []))
                                                    <i class="ri-heart-fill text-danger"></i>
                                                @else
                                                    <i class="ri-heart-line"></i>
                                                @endif
                                            </a>
                                            <a class="model-oraganic-product" data-bs-toggle="modal" href="#quickview"
                                                role="button" data-product-id="{{ $product->id }}">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                        </div>

                                    </div>
                                    <div class="cr-product-details">
                                        <div class="cr-brand">
                                            <a
                                                href="{{ route('shop.index', ['categories[]' => $product->category->id]) }}">{{ $product->category->name ?? 'Category' }}</a>
                                            <div class="cr-star">
                                                <i class="ri-star-fill"></i>
                                                <i class="ri-star-fill"></i>
                                                <i class="ri-star-fill"></i>
                                                <i class="ri-star-fill"></i>
                                                <i class="ri-star-line"></i>
                                                <p>(4.0)</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('productDetail', $product->slug) }}" class="title">
                                            {{ $product->name }}
                                        </a>
                                        <p class="cr-price">
                                            @if ($variant = $product->productVariants->first())
                                                <span class="new-price">
                                                    {{ number_format($variant->price, 0, ',', '.') }}₫
                                                </span>
                                            @else
                                                <span class="new-price text-muted">Chưa có giá</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="col-xxl-5 col-xl-6 col-lg-6 col-md-12" data-aos="fade-up" data-aos-duration="2000">
                    <div class="cr-products-rightbar position-relative overflow-hidden rounded shadow">
                        @if ($defaultBanner)
                            <a href="{{ $defaultBanner->link ?? '#' }}"></a><img
                                src="{{ asset('storage/' . str_replace('storage/', '', $defaultBanner->img)) }}"
                                alt="{{ $defaultBanner->name }}"></a>

                            <div class="cr-products-rightbar-content position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-start text-white p-4"
                                style="background: rgba(0, 0, 0, 0.3);">
                                <div class="rightbar-buttons mt-2">
                                </div>

                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </section>


    <!-- Blog -->
    <section class="section-blog padding-b-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="mb-30" data-aos="fade-up" data-aos-duration="2000">
                        <div class="cr-banner">

                            <h2>Tin tức mới nhất</h2>

                        </div>
                        <div class="cr-banner-sub-title">
                            <p>Tin tức mới nhất được cập nhật mỗi ngày.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-lg-12">
                    <div class="cr-blog-slider swiper-container">
                        <div class="swiper-wrapper">
                            @foreach ($blogs as $blog)
                                <div class="swiper-slide" data-aos="fade-up" data-aos-duration="2000">
                                    <div class="cr-blog">
                                        <div class="cr-blog-content">
                                            <span>
                                                <code>By {{ $blog->author->name ?? 'Admin' }}</code> |
                                                <a href="#">{{ $blog->category->name ?? 'Uncategorized' }}</a>
                                            </span>
                                            <h5>{{ $blog->title }}</h5>
                                            <a class="read" href="{{ route('blog.show', $blog->slug) }}">Đọc thêm</a>
                                        </div>

                                        <div class="cr-blog-image">
                                            <img src="{{ Storage::url(\Illuminate\Support\Str::replaceFirst('storage/', '', $blog->thumbnail)) }}"
                                                alt="{{ $blog->title }}">
                                            <div class="cr-blog-date">
                                                <span>
                                                    {{ \Carbon\Carbon::parse($blog->created_at)->locale('vi')->translatedFormat('d') }}
                                                    <code>{{ \Carbon\Carbon::parse($blog->created_at)->locale('vi')->translatedFormat('F') }}</code>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            // CSRF token setup
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                xhrFields: {
                    withCredentials: true
                }
            });

            // Wishlist toggle
            $(document).on('click', '.wishlist-button', function(e) {
                e.preventDefault();
                const $btn = $(this);
                const productId = $btn.data('product-id');

                $.post('{{ route('wishlist.toggle') }}', {
                    product_id: productId
                }, function(res) {
                    if (res.added) {
                        $btn.find('i').removeClass('ri-heart-line').addClass(
                            'ri-heart-fill text-danger');
                    } else {
                        $btn.find('i').removeClass('ri-heart-fill text-danger').addClass(
                            'ri-heart-line');
                    }
                    alert(res.message);
                }).fail(function(xhr) {
                    alert(xhr.status === 401 ? 'Vui lòng đăng nhập để thêm vào wishlist' :
                        'Đã có lỗi xảy ra!');
                });
            });
        });
    </script>
@endpush
