@extends('layouts.app')

@section('content')
    @push('styles')
        <style>
            .rating-wrapper {
                display: flex;
                flex-direction: row-reverse;
                justify-content: flex-end;
                /* ← CHỈNH LẠI Ở ĐÂY */
                gap: 8px;
            }

            .rating-wrapper input[type="radio"] {
                display: none;
            }

            .rating-wrapper label {
                font-size: 24px;
                color: #ccc;
                cursor: pointer;
                transition: color 0.2s;
            }

            .rating-wrapper input:checked~label,
            .rating-wrapper label:hover,
            .rating-wrapper label:hover~label {
                color: #ffc107;
            }

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
                                <div class="slider-banner-image">
                                    <div class="zoom-image-hover">
                                        <img src="{{ asset('storage/' . ($product->image ?? 'default.jpg')) }}"
                                            alt="{{ $product->name }}" class="product-image">
                                    </div>
                                </div>
                            </div>
                            <div class="slider slider-nav thumb-image">
                                @foreach ($product->productVariants as $variant)
                                    @if ($variant->image)
                                        <div class="thumbnail-image">
                                            <div class="thumbImg">
                                                <img src="{{ asset('storage/' . $variant->image) }}"
                                                    alt="{{ $variant->attribute_name }}">
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- THÔNG TIN SẢN PHẨM -->
                <div class="col-xxl-8 col-xl-7 col-md-6 col-12 mb-24">
                    <div class="cr-size-and-weight-contain">
                        <h2 class="heading">{{ $product->name }}</h2>
                        <p>{{ $product->sort_des ?? '' }}</p>
                    </div>

                    @php
                        $reviewComments = $product->comments->whereNotNull('rating');
                        $totalReviews = $reviewComments->count();
                        $totalStar = $reviewComments->sum('rating');
                        $avgRating = $totalReviews ? round($reviewComments->avg('rating'), 1) : 0;
                        $prices = $product->productVariants->pluck('price')->filter();
                        $minPrice = $prices->min();
                        $maxPrice = $prices->max();
                    @endphp

                    <div class="cr-size-and-weight">
                        {{-- <div class="cr-review-star">
                            <div class="cr-star">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="ri-star-{{ $i <= $avgRating ? 'fill' : 'line' }}"></i>
                                @endfor
                            </div>
                            <p>({{ $totalReviews }} lượt đánh giá | {{ $avgRating }}★ | {{ $totalStar }} sao tổng)
                            </p>
                        </div> --}}
                        <div class="list">
                            <ul>
                                <li><label>Thương Hiệu <span>:</span></label>{{ $product->brand->name ?? '' }}</li>
                                <li><label>Danh Mục <span>:</span></label>{{ $product->category->name ?? '' }}</li>
                                <li><label>Số lượng còn <span>:</span></label><span
                                        id="variant-quantity">{{ $product->quantity }}</span></li>

                                <li><label>Lượt xem <span>:</span></label>{{ $product->view ?? 0 }}</li>
                            </ul>
                        </div>

                        <div class="cr-product-price">
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

                        @if (isset($attributes) && count($attributes) > 0)
                            <div class="cr-size-weight">
                                <h5><span>Chọn loại</span>:</h5>
                                <div class="cr-kg">
                                    <ul>
                                        @foreach ($product->productVariants as $index => $variant)
                                            <li class="variant-option" data-variant-id="{{ $variant->id }}"
                                                data-price="{{ $variant->price }}"
                                                data-image="{{ asset('storage/' . $variant->image) }}"
                                                data-quantity="{{ $variant->quantity }}">

                                                {{ $variant->attribute_name }}
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @else
                            <input type="hidden" class="variant-option"
                                data-variant-id="{{ $product->productVariants->first()->id }}"
                                value="{{ $product->productVariants->first()->id }}">
                        @endif

                        <div class="cr-add-card">
                            <div class="cr-qty-main">
                                <input type="text" placeholder="." value="1" minlength="1" maxlength="20"
                                    class="quantity">
                                <button type="button" class="plus">+</button>
                                <button type="button" class="minus">-</button>
                            </div>
                            <div class="cr-add-button">
                                <button type="button" class="cr-button add-to-cart">Thêm vào giỏ</button>
                            </div>
                            <div class="cr-card-icon">
                                <a href="javascript:void(0);" class="wishlist-button"
                                    data-product-id="{{ $product->id }}">
                                    @if (in_array($product->id, $wishlistProductIds ?? []))
                                        <i class="ri-heart-fill text-danger"></i>
                                    @else
                                        <i class="ri-heart-line"></i>
                                    @endif
                                </a>
                                <a class="model-oraganic-product" data-bs-toggle="modal" href="#quickview" role="button">
                                    <i class="ri-eye-line"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB -->
            <div class="row" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="600">
                <div class="col-12 pt-5">
                    <div class="cr-paking-delivery">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="description-tab" data-bs-toggle="tab"
                                    data-bs-target="#description" type="button" role="tab">Mô tả</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="review-tab" data-bs-toggle="tab" data-bs-target="#review"
                                    type="button" role="tab">Review</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="comment-tab" data-bs-toggle="tab" data-bs-target="#comment"
                                    type="button" role="tab">Bình luận</button>
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
<div class="tab-pane fade" id="review" role="tabpanel">
    <div class="cr-tab-content-from">
        <div class="post">
            @forelse ($reviews as $review)
                <div class="content {{ !$loop->first ? 'mt-30' : '' }}">
                    {{-- Avatar người dùng --}}
                    <img src="{{ asset('storage/' . ($review->user->avatar ?? 'default-avatar.jpg')) }}"
                         alt="review"
                         onerror="if(!this.dataset.error) { this.dataset.error = true; this.src='{{ asset('images/default-avatar.jpg') }}'; }"
                         style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">

                    {{-- Thông tin đánh giá --}}
                    <div class="details">
                        <span class="date">
                            {{ \Carbon\Carbon::parse($review->created_at)->locale('vi')->isoFormat('D [tháng] M, YYYY') }}
                        </span>
                        <span class="name">{{ $review->user->name ?? 'Khách' }}</span>
                        @if ($review->title)
                            <div class="fw-bold mt-1">{{ $review->title }}</div>
                        @endif
                    </div>

                    {{-- Rating sao --}}
                    <div class="cr-t-review-rating mt-1">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="ri-star-s-{{ $i <= $review->rating ? 'fill' : 'line' }}"></i>
                        @endfor
                    </div>
                </div>

                {{-- Nội dung đánh giá --}}
                <p class="mt-2">{{ $review->content }}</p>

                {{-- Hình ảnh trong review --}}
                @if ($review->images && $review->images->count())
                    <div class="review-images mt-2 d-flex flex-wrap">
                        @foreach ($review->images as $image)
                            <img src="{{ asset('storage/' . $image->image) }}"
                                 alt="Review Image"
                                 style="width: 100px; height: auto; border-radius: 6px; margin-right: 8px;"
                                 loading="lazy"
                                 onerror="this.src='{{ asset('images/default-image.jpg') }}'">
                        @endforeach
                    </div>
                @endif
            @empty
                <div class="no-reviews mt-4">
                    <p>Chưa có đánh giá nào cho sản phẩm này.</p>
                </div>
            @endforelse

            {{-- Phân trang --}}
            <div class="mt-4">
                {{ $reviews->links() }}
            </div>
        </div>
    </div>
</div>

                            <!-- Comment -->
<div class="tab-pane fade" id="comment" role="tabpanel">
    <div class="cr-tab-content-from">
        <div class="post">

            @forelse ($comments as $comment)
                <div class="content {{ !$loop->first ? 'mt-30' : '' }}">
                    {{-- Avatar người dùng --}}
                    <img src="{{ asset('storage/' . ($comment->user->avatar ?? 'default-avatar.jpg')) }}"
                         alt="comment"
                         onerror="if(!this.dataset.error) { this.dataset.error = true; this.src='{{ asset('images/default-avatar.jpg') }}'; }"
                         style="width: 50px; height: 50px; border-radius: 50%; object-fit: cover;">

                    <div class="details">
                        <span class="date">
                            {{ \Carbon\Carbon::parse($comment->created_at)->locale('vi')->isoFormat('D [tháng] M, YYYY') }}
                        </span>
                        <span class="name">{{ $comment->user->name ?? 'Khách' }}</span>
                    </div>
                </div>

                <p class="mt-2">{{ $comment->content }}</p>
            @empty
                <p>Chưa có bình luận nào.</p>
            @endforelse

            {{-- PHÂN TRANG --}}
            <div class="mt-4">
                {{ $comments->links() }}
            </div>
        </div>

        {{-- Form thêm bình luận --}}
        <h4 class="heading">Thêm bình luận</h4>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('client.comment.submit') }}" method="POST" class="mt-4">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">

            <div class="mb-3">
                <label class="form-label">Tên của bạn</label>
                <input type="text" class="form-control"
                       value="{{ Auth::user()->name ?? 'Khách' }}" disabled>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" class="form-control"
                       value="{{ Auth::user()->email ?? '' }}" disabled>
            </div>

            <div class="mb-3">
                <label for="content" class="form-label">Nội dung bình luận</label>
                <textarea name="content"
                          class="form-control @error('content') is-invalid @enderror"
                          rows="4"
                          placeholder="Nhập nội dung bình luận..." required>{{ old('content') }}</textarea>
                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="cr-button">Gửi bình luận</button>
        </form>
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
                            </div>
                            <div class="cr-product-details">
                                <div class="cr-brand">
                                    <a href="#">{{ $item->category->name ?? '' }}</a>
                                </div>
                                <a href="{{ route('productDetail', $item->slug) }}"
                                    class="title">{{ $item->name }}</a>
                                @php
                                    $prices = $item->productVariants->pluck('price')->filter();
                                    $minPrice = $prices->min();
                                    $maxPrice = $prices->max();
                                @endphp
                                <p class="cr-price">
                                    <span class="new-price">
                                        @if ($prices->count())
                                            {{ number_format($minPrice, 0, ',', '.') }}₫
                                            @if ($minPrice != $maxPrice)
                                                - {{ number_format($maxPrice, 0, ',', '.') }}₫
                                            @endif
                                        @else
                                            Liên hệ
                                        @endif
                                    </span>
                                </p>
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

                document.querySelectorAll('.variant-option').forEach(function(li) {
                    li.addEventListener('click', function() {
                        document.querySelectorAll('.variant-option').forEach(e => e.classList.remove(
                            'active-color'));
                        this.classList.add('active-color');
                        let gia = Number(this.dataset.price);
                        document.getElementById('variant-price').textContent = gia.toLocaleString(
                            'vi-VN') + '₫';

                        let soLuong = this.dataset.quantity || 0;
                        document.getElementById('variant-quantity').textContent = soLuong;
                    });
                });

                // Thêm vào giỏ hàng
                document.querySelector('.add-to-cart').addEventListener('click', function() {
                    let activeVariant = document.querySelector('.variant-option.active-color');

                    // Nếu không có thẻ .variant-option nào (tức là sản phẩm đơn), thì lấy từ thẻ input ẩn
                    if (!activeVariant) {
                        activeVariant = document.querySelector('.variant-option');
                    }

                    // Sau khi có thẻ, lấy ID
                    let variantId = activeVariant ? activeVariant.getAttribute('data-variant-id') :
                        activeVariant?.value;
                    let quantity = document.querySelector('.quantity').value;
                    console.log('Variant ID:', variantId, 'Quantity:', quantity);

                    if (!variantId) {
                        alert('Vui lòng chọn loại sản phẩm!');
                        return;
                    }

                    fetch('{{ route('cart.add') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                product_variant_id: variantId,
                                quantity: quantity
                            })
                        })
                        .then(response => {
                            if (response.redirected) {
                                // Trường hợp chưa đăng nhập, Laravel redirect về login
                                window.location.href = response.url;
                                alert('Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!');
                                return;
                            }
                            return response.json();
                        })

                        .then(data => {
                            if (data.success) {
                                console.log('Thêm vào giỏ hàng thành công:');
                                showNotify('Đã thêm sản phẩm vào giỏ hàng!', 'success');

                                console.log('Cập nhật giỏ hàng:', data.cart);
                                // if (data.cart) {
                                //     updateMiniCart(data.cart);
                                // } else {
                                // Nếu server không trả cart, bạn có thể gọi lại loadMiniCart để fetch dữ liệu mới
                                loadMiniCart();
                                // }
                            } else {
                                showNotify(data.message || 'Có lỗi xảy ra!', 'error');

                            }
                        })
                        .catch((error) => showNotify(data.message || 'Có lỗi xảy ra!', 'error'));
                });
                const ratingLabels = document.querySelectorAll('.rating-label');

                ratingLabels.forEach((label, index) => {
                    label.addEventListener('click', function() {
                        const radio = this.querySelector('input[type=radio]');
                        if (radio) radio.checked = true;

                        // Reset toàn bộ sao
                        ratingLabels.forEach(l => {
                            const star = l.querySelector('i');
                            if (star) {
                                star.classList.remove('ri-star-s-fill');
                                star.classList.add('ri-star-s-line');
                            }
                        });

                        // Fill từ sao đầu đến sao đang chọn
                        for (let i = 0; i <= index; i++) {
                            const star = ratingLabels[i].querySelector('i');
                            if (star) {
                                star.classList.remove('ri-star-s-line');
                                star.classList.add('ri-star-s-fill');
                            }
                        }
                    });
                });
                document.getElementById('imageInput').addEventListener('change', function(e) {
                    const preview = document.getElementById('preview');
                    preview.innerHTML = ''; // clear preview

                    const files = e.target.files;
                    if (files.length === 0) return;

                    for (let i = 0; i < files.length; i++) {
                        const file = files[i];

                        if (!file.type.startsWith('image/')) continue;

                        const reader = new FileReader();
                        reader.onload = function(event) {
                            const img = document.createElement('img');
                            img.src = event.target.result;
                            img.classList.add('img-thumbnail');
                            img.style.maxWidth = '120px';
                            img.style.maxHeight = '120px';
                            preview.appendChild(img);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });
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
@endsection
