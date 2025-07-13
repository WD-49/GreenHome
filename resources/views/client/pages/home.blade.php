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
        </style>
    @endpush

    <!-- Hero slider -->
    <section class="section-hero padding-b-100 next">
        <div class="cr-slider swiper-container">
            <div class="swiper-wrapper">

                {{-- Banner có priority 1 --}}
                @foreach ($banner1 as $item)
                    <div class="swiper-slide">
                        <div class="cr-hero-banner"
                            style="background-image: url('{{ Storage::url(Str::replaceFirst('storage/', '', $item->img)) }}');
            background-size: cover;
            background-position: center;">

                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="cr-left-side-contain slider-animation text-white">
                                            <h1>{{ $item->name }}</h1>
                                            <p>{!! $item->description !!}</p>
                                            <div class="cr-last-buttons">
                                                <a href="{{ $item->link }}" class="cr-button">Xem Cửa Hàng</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Banner có priority 2 --}}
                @foreach ($banner2 as $item)
                    <div class="swiper-slide">
                        <div class="cr-hero-banner"
                            style="background-image: url('{{ Storage::url(Str::replaceFirst('storage/', '', $item->img)) }}');
            background-size: cover;
            background-position: center;">

                            <div class="container">
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="cr-left-side-contain slider-animation text-white">
                                            <h1>{{ $item->name }}</h1>
                                            <p>{!! $item->description !!}</p>
                                            <div class="cr-last-buttons">
                                                <a href="{{ $item->link }}" class="cr-button">Xem Cửa Hàng</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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
                        @foreach ($categories as $index => $category)
                            @php
                                $start = $index * 2;
                                $categoryBanners = $banners->slice($start, 2);
                            @endphp

                            <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                id="content-{{ $category->id }}" role="tabpanel"
                                aria-labelledby="tab-{{ $category->id }}">
                                <div class="row mb-minus-24 h-100">
                                    @foreach ($categoryBanners as $banner)
                                        <div class="col-6 cr-categories-box mb-4 d-flex align-items-stretch">
                                            <div class="cr-side-categories w-100" style="height:100%;">
                                                <div
                                                    style="position:relative; height:100%; width:100%; border-radius:8px; overflow:hidden;">
                                                    <img src="{{ asset($banner->img) }}" alt="{{ $banner->name }}"
                                                        style="width:100%;height:100%;object-fit:cover;display:block;position:absolute;top:0;left:0;">
                                                    <div
                                                        style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.25);z-index:1;">
                                                    </div>
                                                    <div class="categories-inner"
                                                        style="position:absolute;top:10px;left:10px;z-index:2;">
                                                        <h4>{{ $category->discount ?? '0' }}
                                                            <span>
                                                                <small>%</small>
                                                                <small>Off</small>
                                                            </span>
                                                        </h4>
                                                    </div>
                                                    <div class="categories-contain"
                                                        style="position:absolute;bottom:20px;left:0;width:100%;z-index:2;text-align:center;">
                                                        <div class="categories-text">
                                                            <h5 style="color:#fff;text-shadow:0 1px 4px #000;">
                                                                {{ $category->name }}</h5>
                                                        </div>
                                                        <div class="categories-button">
                                                            <a href="{{ route('shop.category', $category->slug ?? $category->id) }}"
                                                                class="cr-button">Xem Cửa Hàng</a>
                                                        </div>
                                                    </div>
                                                    <div
                                                        style="position:absolute;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.25);border-radius:8px;z-index:1;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                            <p>Tổng hợp những sản phẩm được yêu thích và đánh giá cao nhất – chất lượng vượt trội, thiết kế ấn tượng và luôn dẫn đầu xu hướng</p>
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
                    @foreach ($banner11 as $item)
                        <div class="cr-ice-cubes mt-4">

                            <img src="{{ Storage::url(Str::replaceFirst('storage/', '', $item->img)) }}"
                                alt="{{ $item->name }}" style="width: 100%;">

                            <div class="cr-ice-cubes-contain">
                                <h4 class="title">{{ $item->name }}</h4>
                                <h5 class="sub-title">{{ $item->sub_title ?? 'Subtitle' }}</h5>
                                <span>{{ $item->description }}</span>
                                <a href="{{ $item->link }}" class="cr-button">Xem Cửa Hàng</a>

                            </div>
                        </div>
                    @endforeach

                </div>

                {{-- Cột phải: Sản phẩm --}}
                <div class="col-xl-9 col-lg-8 col-12 mb-24">
                    {{-- Tab All: 8 sản phẩm ngẫu nhiên --}}
                    <div id="tab-all" class="product-tab-content fade-tab show">
                        <div class="row mb-minus-24">
                            @foreach ($randomProducts as $product)
                                <div class="col-xxl-3 col-xl-4 col-6 cr-product-box mb-24">
                                    <div class="cr-product-card">
                                        <div class="cr-product-image">
                                            <div class="cr-image-inner zoom-image-hover">
                                                <img src="{{ Storage::url($product->image) }}"
                                                    alt="{{ $product->name }}">
                                            </div>
                                            <div class="cr-side-view">
                                                <a href="javascript:void(0)" class="wishlist">
                                                    <i class="ri-heart-line"></i>
                                                </a>
                                                <a class="model-oraganic-product" data-bs-toggle="modal"
                                                    href="#quickview" role="button">
                                                    <i class="ri-eye-line"></i>
                                                </a>
                                            </div>
                                            <a class="cr-shopping-bag" href="javascript:void(0)">
                                                <i class="ri-shopping-bag-line"></i>
                                            </a>
                                        </div>
                                        <div class="cr-product-details">
                                            <div class="cr-brand">
                                                <a href="#">{{ $product->category->name ?? '' }}</a>
                                            </div>
                                            <a href="#" class="title">{{ $product->name }}</a>

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
                                @foreach ($category->products as $product)
                                    <div class="col-xxl-3 col-xl-4 col-6 cr-product-box mb-24">
                                        <div class="cr-product-card">
                                            <div class="cr-product-image">
                                                <div class="cr-image-inner zoom-image-hover">
                                                    <img src="{{ Storage::url($product->image) }}"
                                                        alt="{{ $product->name }}">
                                                </div>
                                                <div class="cr-side-view">
                                                    <a href="javascript:void(0)" class="wishlist">
                                                        <i class="ri-heart-line"></i>
                                                    </a>
                                                    <a class="model-oraganic-product" data-bs-toggle="modal"
                                                        href="#quickview" role="button">
                                                        <i class="ri-eye-line"></i>
                                                    </a>
                                                </div>
                                                <a class="cr-shopping-bag" href="javascript:void(0)">
                                                    <i class="ri-shopping-bag-line"></i>
                                                </a>
                                            </div>
                                            <div class="cr-product-details">
                                                <div class="cr-brand">
                                                    <a href="#">{{ $category->name }}</a>
                                                </div>
                                                <a href="#" class="title">{{ $product->name }}</a>

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
                            @foreach ($banners_mix as $banner)
                                @php
                                    $imgPath = Str::replaceFirst('storage/', '', $banner->img);
                                @endphp
                                <div class="swiper-slide" data-aos="fade-up" data-aos-duration="1000">
                                    <div class="position-relative rounded overflow-hidden shadow" style="height: 300px;">

                                        {{-- Ảnh banner --}}
                                        <img src="{{ Storage::url($imgPath) }}" alt="{{ $banner->name }}"
                                            class="w-100 h-100 object-cover">

                                        {{-- Chữ và nút chồng lên ảnh --}}
                                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center text-center text-white p-3"
                                            style="background: rgba(0, 0, 0, 0.3);">
                                            <h5 class="fw-bold" style="font-size: 24px;">{!! $banner->name !!}</h5>
                                            <p>{{ $banner->sub_title ?? '' }}</p>
                                            <a href="{{ $banner->link ?? '#' }}" class="cr-button mt-2">Xem Cửa Hàng</a>
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
                                            <a href="javascript:void(0)" class="wishlist"><i
                                                    class="ri-heart-line"></i></a>
                                            <a class="model-oraganic-product" data-bs-toggle="modal" href="#quickview"
                                                role="button">
                                                <i class="ri-eye-line"></i>
                                            </a>
                                        </div>
                                        <a class="cr-shopping-bag" href="javascript:void(0)">
                                            <i class="ri-shopping-bag-line"></i>
                                        </a>
                                    </div>
                                    <div class="cr-product-details">
                                        <div class="cr-brand">
                                            <a
                                                href="shop-left-sidebar.html">{{ $product->category->name ?? 'Category' }}</a>
                                            <div class="cr-star">
                                                <i class="ri-star-fill"></i>
                                                <i class="ri-star-fill"></i>
                                                <i class="ri-star-fill"></i>
                                                <i class="ri-star-fill"></i>
                                                <i class="ri-star-line"></i>
                                                <p>(4.0)</p>
                                            </div>
                                        </div>
                                        <a href="product-left-sidebar.html" class="title">
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
                        @if ($banner12)
                            {{-- Ảnh banner từ DB --}}
                            <img src="{{ asset('storage/' . str_replace('storage/', '', $imgPath)) }}"
                                alt="{{ $banner12->name }}">

                            {{-- Nội dung chèn lên ảnh --}}
                            <div class="cr-products-rightbar-content position-absolute top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-start text-white p-4"
                                style="background: rgba(0, 0, 0, 0.3);">
                                {{-- <h4>{!! $banner12->name !!}</h4>
                                <div class="rightbar-buttons mt-2">
                                    <a href="{{ $banner12->link ?? '#' }}" class="cr-button">Xem Cửa Hàng</a>
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
                                            <a class="read" href="">Đọc thêm</a>
                                        </div>

                                        <div class="cr-blog-image">
                                            <img src="{{ Storage::url(\Illuminate\Support\Str::replaceFirst('storage/', '', $blog->thumbnail)) }}"
                                                alt="{{ $blog->title }}">
                                            <div class="cr-blog-date">
                                                <span>
                                                    {{ optional($blog->created_at)->format('d') }}
                                                    <code>{{ optional($blog->created_at)->format('M') }}</code>
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
