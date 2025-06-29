@extends('layouts.app')

@section('content')
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
                <!-- SLIDER ẢNH -->
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

                <!-- THÔNG TIN SẢN PHẨM -->
                <div class="col-xxl-8 col-xl-7 col-md-6 col-12 mb-24">
                    <div class="cr-size-and-weight-contain">
                        <h2 class="heading">{{ $product->name }}</h2>
                        @if ($product->sortDes)
                            <p>{{ $product->sortDes }}</p>
                        @endif
                    </div>

                    @php
                        $reviewComments = $product->comments->whereNotNull('rating');
                        $totalReviews = $reviewComments->count();
                        $totalStar = $reviewComments->sum('rating');
                        $avgRating = $totalReviews ? round($reviewComments->avg('rating'), 1) : 0;
                        $prices = $product->productVariants->pluck('price')->filter();
                        $minPrice = $prices->min();
                        $maxPrice = $prices->max();
                        $totalQuantity = $product->productVariants->sum('quantity') ?: $product->quantity;
                    @endphp

                    <div class="cr-size-and-weight">
                        @php
                            $avg = $product->reviews_avg_rating ?? 0;
                            $count = $product->reviews_count ?? 0;
                            $fullStars = floor($avg);
                            $halfStar = $avg - $fullStars >= 0.5;
                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                        @endphp
                        <div class="cr-star d-flex align-items-center gap-1">
                            @for ($i = 0; $i < $fullStars; $i++)
                                <i class="ri-star-fill text-warning"></i>
                            @endfor

                            @if ($halfStar)
                                <i class="ri-star-half-line text-warning"></i>
                            @endif

                            @for ($i = 0; $i < $emptyStars; $i++)
                                <i class="ri-star-line text-warning"></i>
                            @endfor

                            <span class="ms-2 text-muted">({{ $avg }} / {{ $count }}
                                đánh giá)</span>
                        </div>


                        <div class="list">
                            <ul>
                                <li><label>Thương Hiệu <span>:</span></label>{{ $product->brand->name ?? '' }}</li>
                                <li><label>Danh Mục <span>:</span></label>{{ $product->category->name ?? '' }}</li>
                                <li><label>Tổng số lượng <span>:</span></label>{{ $totalQuantity ?? 'N/A' }}</li>
                                <li><label>Lượt xem <span>:</span></label>{{ $product->view ?? 0 }}</li>
                            </ul>
                        </div>

                        <div class="cr-product-price">
                            <span class="new-price" id="variant-price">
                                @if ($prices->count())
                                    @if ($minPrice == $maxPrice)
                                        {{ number_format($minPrice, 0, ',', '.') }}₫
                                    @else
                                        {{ number_format($minPrice, 0, ',', '.') }}₫ -
                                        {{ number_format($maxPrice, 0, ',', '.') }}₫
                                    @endif
                                @else
                                    Liên hệ
                                @endif
                            </span>
                        </div>

                        @if (isset($attributes) && count($attributes) > 0)
                            <div class="cr-size-weight">
                                <h5><span>Chọn loại</span>:</h5>
                                <div class="cr-kg">
                                    <ul>
                                        @foreach ($product->productVariants as $index => $variant)
                                            <li class="variant-option{{ $index == 0 ? ' active-color' : '' }}"
                                                data-id="{{ $variant->id }}" data-price="{{ $variant->price }}"
                                                data-quantity="{{ $variant->quantity }}"
                                                data-image="{{ asset('storage/' . $variant->image) }}">
                                                {{ $variant->attribute_name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div id="variant-stock" class="mt-2" style="color: #28a745; font-weight: 500;"></div>
                        @endif

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
                <div class="row" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="600">
                    <div class="col-12 pt-5">
                        <div class="cr-paking-delivery">
                            <ul class="nav nav-tabs" id="myTab" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                                        data-bs-target="#description" type="button" role="tab">Description</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="review-tab" data-bs-toggle="tab" data-bs-target="#review"
                                        type="button" role="tab">Review</button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="comment-tab" data-bs-toggle="tab"
                                        data-bs-target="#comment" type="button" role="tab">Comment</button>
                                </li>
                            </ul>

                            <div class="tab-content" id="myTabContent">
                                <!-- Description -->
                                <div class="tab-pane fade show active" id="description" role="tabpanel">
                                    <div class="cr-tab-content">
                                        <div class="cr-description">
                                            <p>{!! $product->description !!}</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Review -->
                                <div class="tab-pane fade" id="review" role="tabpanel">
                                    <div class="cr-tab-content-from">
                                        <div class="post">
                                            @forelse ($reviews as $review)
                                                <div class="content {{ !$loop->first ? 'mt-30' : '' }}">
                                                    <img src="{{ asset('storage/' . ($review->user->avatar ?? 'default-avatar.jpg')) }}"
                                                        alt="review"
                                                        onerror="if(!this.dataset.error){ this.dataset.error = true; this.src='{{ asset('images/default-avatar.jpg') }}'; }">

                                                    <div class="details">
                                                        <span class="date">
                                                            {{ \Carbon\Carbon::parse($review->created_at)->locale('vi')->isoFormat('D [tháng] M, YYYY') }}
                                                        </span>
                                                        <span class="name">{{ $review->user->name ?? 'Guest' }}</span>
                                                        @php
                                                            $productName = optional(
                                                                optional($review->productVariant)->product,
                                                            )->name;
                                                            $variantAttributes = optional(
                                                                $review->productVariant->productVariantValues ??
                                                                    collect(),
                                                            )
                                                                ->map(fn($v) => $v->attributeValue->value ?? '')
                                                                ->implode(' / ');
                                                        @endphp

                                                        @if ($productName)
                                                            <div class="text-muted small">Sản phẩm: {{ $productName }}
                                                            </div>
                                                        @endif

                                                        @if ($variantAttributes)
                                                            <div class="text-muted small">Biến thể:
                                                                {{ $variantAttributes }}</div>
                                                        @endif
                                                    </div>

                                                    <div class="cr-t-review-rating mt-1">
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            <i
                                                                class="ri-star-s-{{ $i <= $review->rating ? 'fill' : 'line' }}"></i>
                                                        @endfor
                                                    </div>
                                                </div>

                                                @if ($review->title)
                                                    <p>{{ $review->title }}</p>
                                                @endif
                                                @if ($review->content)
                                                    <p>{{ $review->content }}</p>
                                                @endif
                                            @empty
                                                <div class="no-reviews">
                                                    <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                                                </div>
                                            @endforelse
                                        </div>

                                        {{-- Form thêm đánh giá (chỉ UI) --}}
                                        <h4 class="heading">Thêm đánh giá của bạn</h4>
                                        <form action="javascript:void(0)">
                                            <div class="cr-ratting-star mb-3">
                                                <span>Đánh giá của bạn:</span>
                                                <div class="cr-t-review-rating">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <i class="ri-star-s-line"></i>
                                                    @endfor
                                                </div>
                                            </div>

                                            <div class="cr-ratting-input">
                                                <input name="your-name" placeholder="Tên của bạn" type="text">
                                            </div>
                                            <div class="cr-ratting-input">
                                                <input name="your-email" placeholder="Email*" type="email" required>
                                            </div>
                                            <div class="cr-ratting-input">
                                                <input name="your-title" placeholder="Tiêu đề đánh giá" type="text">
                                            </div>
                                            <div class="cr-ratting-input form-submit">
                                                <textarea name="your-comment" placeholder="Nội dung đánh giá"></textarea>
                                                <button class="cr-button" type="submit">Gửi đánh giá</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>




                                <!-- Comment -->
                                <div class="tab-pane fade" id="comment" role="tabpanel">
                                    <div class="cr-tab-content-from">
                                        <div class="post">
                                            @php $comments = $product->comments->whereNull('rating'); @endphp
                                            @forelse ($comments as $comment)
                                                <div class="content {{ !$loop->first ? 'mt-30' : '' }}">
                                                    <img src="{{ asset('storage/' . ($comment->user->avatar ?? 'default-avatar.jpg')) }}"
                                                        alt="comment">
                                                    <div class="details">
                                                        <span class="date">
                                                            {{ \Carbon\Carbon::parse($comment->created_at)->locale('vi')->isoFormat('D [tháng] M, YYYY') }}
                                                        </span>
                                                        <span class="name">{{ $comment->user->name ?? 'Guest' }}</span>
                                                        <div class="product-name text-muted" style="font-size: 0.9rem;">
                                                            Sản phẩm: {{ $product->name }}
                                                        </div>
                                                    </div>
                                                </div>
                                                <p>{{ $comment->content }}</p>
                                            @empty
                                                <p>Chưa có bình luận nào.</p>
                                            @endforelse


                                        </div>
                                        <h4 class="heading">Add a Comment</h4>
                                        <form action="javascript:void(0)">
                                            <div class="cr-ratting-input">
                                                <input name="your-name" placeholder="Name" type="text">
                                            </div>
                                            <div class="cr-ratting-input">
                                                <input name="your-email" placeholder="Email*" type="email"
                                                    required="">
                                            </div>
                                            <div class="cr-ratting-input form-submit">
                                                <textarea name="your-comment" placeholder="Enter Your Comment"></textarea>
                                                <button class="cr-button" type="submit">Submit</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>


            </div>
        </div>

    </section>

    <!-- Related Products -->
    <section class="section-popular-products padding-tb-100" data-aos="fade-up" data-aos-duration="2000"
        data-aos-delay="400">
        <div class="container">
            <div class="cr-banner">
                <h2>Sản phẩm liên quan</h2>
                <p>Các sản phẩm cùng danh mục với {{ $product->name }}</p>
            </div>
            <div class="cr-popular-product">
                @foreach ($relatedProducts as $item)
                    <div class="slick-slide">
                        <div class="cr-product-card">
                            <div class="cr-product-image">
                                <div class="cr-image-inner zoom-image-hover">
                                    <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->name }}">
                                </div>
                                <div class="cr-side-view">
                                    <a href="#" class="wishlist"><i class="ri-heart-line"></i></a>
                                    <a class="model-oraganic-product" data-bs-toggle="modal" href="#quickview"><i
                                            class="ri-eye-line"></i></a>
                                </div>
                                <a class="cr-shopping-bag" href="#"><i class="ri-shopping-bag-line"></i></a>
                            </div>

                            <div class="cr-product-details">
                                <div class="cr-brand">
                                    <a href="#">{{ $item->brand->name ?? 'Không có thương hiệu' }}</a>

                                    {{-- ⭐️ Hiển thị sao từ reviews --}}
                                    @php
                                        $avg = $item->reviews_avg_rating ?? 0;
                                        $count = $item->reviews_count ?? 0;
                                        $fullStars = floor($avg);
                                        $halfStar = $avg - $fullStars >= 0.5;
                                        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                    @endphp
                                    <div class="cr-star">
                                        @for ($i = 0; $i < $fullStars; $i++)
                                            <i class="ri-star-fill"></i>
                                        @endfor

                                        @if ($halfStar)
                                            <i class="ri-star-half-line"></i>
                                        @endif

                                        @for ($i = 0; $i < $emptyStars; $i++)
                                            <i class="ri-star-line"></i>
                                        @endfor

                                        <p>({{ $avg }} / {{ $count }} đánh giá)</p>
                                    </div>
                                </div>

                                <a href="{{ route('productDetail', $item->slug) }}"
                                    class="title">{{ $item->name }}</a>

                                <p class="text">Sản phẩm chất lượng cao, giá tốt nhất thị trường.</p>

                                <ul class="list">
                                    <li><label>Brand :</label> {{ $item->brand->name ?? 'Không rõ' }}</li>
                                </ul>

                                {{-- Nếu có giá thì hiển thị --}}
                                @php
                                    $itemPrices = optional($item->productVariants)->pluck('price')->filter();
                                @endphp
                                @if ($itemPrices->isNotEmpty())
                                    <p class="cr-price">
                                        <span class="new-price">
                                            @php
                                                $itemMin = $itemPrices->min();
                                                $itemMax = $itemPrices->max();
                                            @endphp
                                            @if ($itemMin == $itemMax)
                                                {{ number_format($itemMin, 0, ',', '.') }}₫
                                            @else
                                                {{ number_format($itemMin, 0, ',', '.') }}₫ -
                                                {{ number_format($itemMax, 0, ',', '.') }}₫
                                            @endif
                                        </span>
                                    </p>
                                @else
                                    <p class="cr-price"><span class="new-price">Chưa có giá</span></p>
                                @endif

                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const variantOptions = document.querySelectorAll('.variant-option');
                const stockDisplay = document.getElementById('variant-stock');
                const priceDisplay = document.getElementById('variant-price');

                variantOptions.forEach(function(option) {
                    option.addEventListener('click', function() {
                        document.querySelectorAll('.variant-option').forEach(e => e.classList.remove(
                            'active-color'));
                        this.classList.add('active-color');

                        const quantity = parseInt(this.getAttribute('data-quantity'));
                        const price = parseFloat(this.getAttribute('data-price'));

                        stockDisplay.innerHTML = quantity > 0 ?
                            `Còn ${quantity} sản phẩm` :
                            '<span style="color:red">Hết hàng</span>';

                        priceDisplay.textContent = price.toLocaleString('vi-VN') + '₫';
                    });
                });

                const first = document.querySelector('.variant-option');
                if (first) first.click();
            });
        </script>
    @endpush
@endsection
