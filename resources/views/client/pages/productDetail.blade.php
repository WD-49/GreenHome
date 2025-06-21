@extends('layouts.app')

@section('content')
    {{-- <pre>{{ print_r($product->productVariants, true) }}</pre> --}}

    <!-- Breadcrumb -->
    <section class="section-breadcrumb">
        <div class="cr-breadcrumb-image">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="cr-breadcrumb-title">
                            <h2>{{ $product->name }}</h2>
                            <span><a href="{{ route('home') }}">Home</a> - {{ $product->name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Product -->
    <section class="section-product padding-t-100">
        <div class="container">
            <div class="row mb-minus-24" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="600">
                <!-- PHẦN ẢNH SLIDER BIẾN THỂ -->
                <div class="col-xxl-4 col-xl-5 col-md-6 col-12 mb-24 pb-3">
                    <div class="vehicle-detail-banner banner-content clearfix">
                        <div class="banner-slider">
                            <div class="slider slider-for">
                                @foreach ($product->productVariants as $variant)
                                    <div class="slider-banner-image">
                                        <div class="zoom-image-hover">
                                            <img src="{{ asset('storage/' . ($variant->image ?? 'default.jpg')) }}"
                                                alt="{{ $variant->attribute_name }}" class="product-image">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="slider slider-nav thumb-image">
                                @foreach ($product->productVariants as $variant)
                                    <div class="thumbnail-image">
                                        <div class="thumbImg">
                                            <img src="{{ asset('storage/' . $variant->image) }}"
                                                alt="{{ $variant->attribute_name }}">
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PHẦN THÔNG TIN & CHỌN BIẾN THỂ -->
                <div class="col-xxl-8 col-xl-7 col-md-6 col-12 mb-24">
                    <div class="cr-size-and-weight-contain">
                        <h2 class="heading">{{ $product->name }}</h2>
                        <p>{!! $product->description !!}</p>
                    </div>
                    <div class="cr-size-and-weight">
                        <div class="cr-review-star">
                            <div class="cr-star">
                                @php $rating = round($product->comments->avg('rating'), 1); @endphp
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="ri-star-fill{{ $i > $rating ? ' ri-star-line' : '' }}"></i>
                                @endfor
                            </div>
                            <p>({{ $product->comments->count() }} Review)</p>
                        </div>
                        <div class="list">
                            <ul>
                                <li><label>Thương Hiệu <span>:</span></label>{{ $product->brand->name ?? '' }}</li>
                                <li><label>Danh Mục <span>:</span></label>{{ $product->category->name ?? '' }}</li>
                                <li><label>Biến Thể <span>:</span></label>
                                    @foreach ($product->productVariants as $variant)
                                        {{ $variant->attribute_name }}{{ !$loop->last ? ', ' : '' }}
                                    @endforeach
                                </li>
                            </ul>
                        </div>
                        <!-- GIÁ THEO BIẾN THỂ -->
                        <div class="cr-product-price">
                            @php
                                $prices = $product->productVariants->pluck('price')->filter();
                                $minPrice = $prices->min();
                                $maxPrice = $prices->max();
                            @endphp
                            <span class="new-price" id="variant-price">
                                @if ($prices->count())
                                    {{ number_format($minPrice, 0, ',', '.') }}₫
                                    @if ($minPrice != $maxPrice)
                                        - {{ number_format($maxPrice, 0, ',', '.') }}₫
                                    @endif
                                @else
                                    Liên hệ
                                @endif
                            </span>
                        </div>

                        <!-- CHỌN BIẾN THỂ -->
                        <div class="cr-size-weight">
                            <h5><span>Chọn loại</span>:</h5>
                            <div class="cr-kg">
                                <ul>
                                    @foreach ($product->productVariants as $index => $variant)
                                        <li class="variant-option{{ $index == 0 ? ' active-color' : '' }}"
                                            data-price="{{ $variant->price }}"
                                            data-image="{{ asset('storage/' . $variant->image) }}">
                                            {{ $variant->attribute_name }}
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                        <div class="cr-add-card">
                            <div class="cr-qty-main">
                                <input type="text" placeholder="." value="1" minlength="1" maxlength="20"
                                    class="quantity">
                                <button type="button" class="plus">+</button>
                                <button type="button" class="minus">-</button>
                            </div>
                            <div class="cr-add-button">
                                <button type="button" class="cr-button cr-shopping-bag">Add to cart</button>
                            </div>
                            <div class="cr-card-icon">
                                <a href="javascript:void(0)" class="wishlist">
                                    <i class="ri-heart-line"></i>
                                </a>
                                <a class="model-oraganic-product" data-bs-toggle="modal" href="#quickview" role="button">
                                    <i class="ri-eye-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB REVIEW & MÔ TẢ: giữ nguyên, không cần sửa gì -->
            <div class="row" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="600">
                <div class="col-12  pt-5 ">
                    <div class="cr-paking-delivery">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                                    data-bs-target="#description" type="button" role="tab" aria-controls="description"
                                    aria-selected="true">Description</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="additional-tab" data-bs-toggle="tab"
                                    data-bs-target="#additional" type="button" role="tab" aria-controls="additional"
                                    aria-selected="false">Information</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="review-tab" data-bs-toggle="tab" data-bs-target="#review"
                                    type="button" role="tab" aria-controls="review"
                                    aria-selected="false">Review</button>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="description" role="tabpanel"
                                aria-labelledby="description-tab">
                                <div class="cr-tab-content">
                                    <div class="cr-description">
                                        <p>{!! $product->description !!}</p>
                                    </div>
                                    <h4 class="heading">Packaging & Delivery</h4>
                                    <div class="cr-description">
                                        <p>{{ $product->packaging_delivery ?? 'Đang cập nhật...' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="additional" role="tabpanel" aria-labelledby="additional-tab">
                                <div class="cr-tab-content">
                                    <div class="cr-description">
                                        <p>{{ $product->additional_info ?? 'Đang cập nhật...' }}</p>
                                    </div>
                                    <div class="list">
                                        <ul>
                                            <li><label>Brand <span>:</span></label>{{ $product->brand->name ?? '' }}</li>
                                            <li><label>Category <span>:</span></label>{{ $product->category->name ?? '' }}
                                            </li>
                                            <li><label>Weight <span>:</span></label>{{ $product->weight ?? 'N/A' }}</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="review" role="tabpanel" aria-labelledby="review-tab">
                                <div class="cr-tab-content-from">
                                    <div class="post">
                                        @foreach ($product->comments as $comment)
                                            <div class="content {{ !$loop->first ? 'mt-30' : '' }}">
                                                <img src="{{ asset('storage/' . ($comment->user->avatar ?? 'default-avatar.jpg')) }}"
                                                    alt="review">
                                                <div class="details">
                                                    <span
                                                        class="date">{{ $comment->created_at->format('M d, Y') }}</span>
                                                    <span class="name">{{ $comment->user->name ?? 'Guest' }}</span>
                                                </div>
                                                <div class="cr-t-review-rating">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i
                                                            class="ri-star-s-{{ $i <= $comment->rating ? 'fill' : 'line' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                            <p>{{ $comment->content }}</p>
                                        @endforeach
                                    </div>

                                    <h4 class="heading">Add a Review</h4>
                                    <form action="javascript:void(0)">
                                        <div class="cr-ratting-star">
                                            <span>Your rating :</span>
                                            <div class="cr-t-review-rating">
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="ri-star-s-line"></i>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="cr-ratting-input">
                                            <input name="your-name" placeholder="Name" type="text">
                                        </div>
                                        <div class="cr-ratting-input">
                                            <input name="your-email" placeholder="Email*" type="email" required="">
                                        </div>
                                        <div class="cr-ratting-input form-submit">
                                            <textarea name="your-comment" placeholder="Enter Your Comment"></textarea>
                                            <button class="cr-button" type="submit" value="Submit">Submit</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- JS ĐỔI GIÁ THEO BIẾN THỂ -->
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                document.querySelectorAll('.variant-option').forEach(function(li) {
                    li.addEventListener('click', function() {
                        // Xóa active khỏi các biến thể khác
                        document.querySelectorAll('.variant-option').forEach(e => e.classList.remove(
                            'active-color'));
                        this.classList.add('active-color');
                        // Đổi giá
                        let gia = Number(this.dataset.price);
                        document.getElementById('variant-price').textContent = gia.toLocaleString(
                            'vi-VN') + '₫';
                    });
                });
            });
        </script>
    @endpush



    <!-- Popular products -->
    <section class="section-popular-products padding-tb-100" data-aos="fade-up" data-aos-duration="2000"
        data-aos-delay="400">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="mb-30">
                        <div class="cr-banner">
                            <h2>Popular Products</h2>
                        </div>
                        <div class="cr-banner-sub-title">
                            <p>Các sản phẩm nổi bật trên cửa hàng của chúng tôi.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <div class="cr-popular-product">
                        @foreach ($popularProducts as $item)
                            <div class="slick-slide">
                                <div class="cr-product-card">
                                    <div class="cr-product-image">
                                        <div class="cr-image-inner zoom-image-hover">
                                            <img src="{{ asset('storage/' . ($item->productVariants->first()->image ?? 'default.jpg')) }}"
                                                alt="{{ $item->name }}">
                                        </div>
                                        <div class="cr-side-view">
                                            <a href="javascript:void(0)" class="wishlist">
                                                <i class="ri-heart-line"></i>
                                            </a>
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
                                            <a href="#">{{ $item->category->name ?? '' }}</a>
                                            <div class="cr-star">
                                                @php $itemRating = round($item->comments->avg('rating'), 1); @endphp
                                                @for ($i = 1; $i <= 5; $i++)
                                                    <i class="ri-star-{{ $i <= $itemRating ? 'fill' : 'line' }}"></i>
                                                @endfor
                                                <p>({{ $itemRating }})</p>
                                            </div>
                                        </div>
                                        <a href="{{ route('productDetail', $item->slug) }}"
                                            class="title">{{ $item->name }}</a>
                                        @php
                                            $prices = $item->productVariants->pluck('price')->filter();
                                            $minPrice = $prices->min();
                                            $maxPrice = $prices->max();
                                        @endphp
                                        <p class="cr-price">
                                            @if ($prices->count())
                                                <span class="new-price">
                                                    {{ number_format($minPrice, 0, ',', '.') }}₫
                                                    @if ($minPrice != $maxPrice)
                                                        - {{ number_format($maxPrice, 0, ',', '.') }}₫
                                                    @endif
                                                </span>
                                            @else
                                                <span class="new-price">Liên hệ</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
