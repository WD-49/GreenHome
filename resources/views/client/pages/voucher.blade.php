@extends('layouts.app')
<link href="https://cdn.jsdelivr.net/npm/remixicon@2.5.0/fonts/remixicon.css" rel="stylesheet">
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
@section('content')
    <!-- Breadcrumb -->
    <section class="section-breadcrumb">
        <div class="cr-breadcrumb-image">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="cr-breadcrumb-title">
                            <h2>Áp dụng mã: {{ $voucher->code }}</h2>
                            <span><a href="{{ route('home') }}">Home</a> - Mã giảm giá</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Shop Section -->
    <section class="section-shop padding-tb-100">
        <div class="container-xl">
            <div class="row">
                {{-- Sidebar --}}
                <div class="col-lg-3">
                    <div class="cr-shop-sideview">
                        <form action="{{ route('voucher.products', $voucher->code) }}" method="GET"
                            class="mb-4">
                            {{-- Tìm kiếm tên sản phẩm --}}
                            <div class="mb-3">
                                <label class="fw-bold">Tìm kiếm sản phẩm:</label>
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Nhập từ khóa..."
                                        value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="ri-search-line"></i>
                                    </button>
                                </div>
                            </div>

                            {{-- Lọc danh mục --}}
                            <div class="mb-3">
                                {{-- Lọc Theo Danh Mục----------------------------------------------------------- --}}
                                <label class="fw-bold">Danh mục:</label>
                                <select class="form-select" name="category_id">
                                    <option value="">Tất cả</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }} ({{ $category->products_count }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Lọc thương hiệu --}}
                            <div class="mb-3">
                                {{-- Lọc Thương hiệu -------------------------------------- --}}
                                <label class="fw-bold">Thương hiệu:</label>
                                <select class="form-select" name="brand_id">
                                    <option value="">Tất cả</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }} ({{ $brand->products_count }})
                                        </option>
                                    @endforeach
                                </select>
                                {{-- Lọc Theo Biến Thể ----------------------------------------------------------- --}}
                            </div>

                            {{-- Lọc theo biến thể --}}
                            @php
                                use Illuminate\Support\Str;
                                $selectedValues = collect(request()->input('attribute_values', []))->map(
                                    fn($id) => (int) $id,
                                );
                                $grouped = $attributeValues->groupBy(fn($v) => $v->attribute->name);
                            @endphp

                            @foreach ($grouped as $attrName => $values)
                                @php
                                    $dropdownId = 'dropdown-' . Str::slug($attrName);
                                    $selectedLabels = $values
                                        ->filter(fn($v) => $selectedValues->contains($v->id))
                                        ->pluck('value')
                                        ->implode(', ');
                                @endphp

                                <div class="dropdown mb-3">
                                    <button
                                        class="btn btn-outline-secondary w-100 text-start dropdown-toggle attribute-dropdown"
                                        type="button" id="{{ $dropdownId }}" data-bs-toggle="dropdown"
                                        aria-expanded="false" data-attribute-name="{{ $attrName }}">
                                        <strong>{{ strtoupper($attrName) }}</strong>
                                        <span class="selected-label text-primary small">
                                            @if ($selectedLabels)
                                                : {{ $selectedLabels }}
                                            @endif
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu w-100 px-3" aria-labelledby="{{ $dropdownId }}">
                                        @foreach ($values as $value)
                                            <li>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="attribute_values[]" value="{{ $value->id }}"
                                                        id="attr-{{ $value->id }}"
                                                        {{ $selectedValues->contains($value->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="attr-{{ $value->id }}">
                                                        {{ $value->value }}
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach

                            {{-- Lọc đánh giá --}}
                            {{-- Lọc Theo Review----------------------------------------------------------- --}}
                            <div class="mb-3">
                                <label class="fw-bold d-block mb-2">Đánh giá:</label>
                                <div class="rating-wrapper">
                                    @for ($i = 5; $i >= 1; $i--)
                                        <input type="radio" name="rating" id="star{{ $i }}"
                                            value="{{ $i }}" {{ request('rating') == $i ? 'checked' : '' }}>
                                        <label for="star{{ $i }}"><i class="ri-star-fill"></i></label>
                                    @endfor
                                </div>
                                {{-- Lọc Theo Giá   ----------------------------------------------------------- --}}
                            </div>


                            {{-- Lọc giá --}}
                            <div class="mb-3">
                                <label class="fw-bold">Giá từ (VNĐ):</label>
                                <input type="number" name="min_price" value="{{ request('min_price') }}"
                                    class="form-control" placeholder="VD: 100000">
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold">Đến (VNĐ):</label>
                                <input type="number" name="max_price" value="{{ request('max_price') }}"
                                    class="form-control" placeholder="VD: 500000">
                            </div>

                            {{-- Nút lọc --}}
                            <div class="d-grid gap-2">
                                <button type="submit"
                                    class="cr-button d-flex align-items-center justify-content-center gap-1">
                                    <i class="ri-search-line"></i> Lọc
                                </button>
                                <a href="{{ route('voucher.products', $voucher->code) }}"
                                    class="cr-button reset-button d-flex align-items-center justify-content-center gap-1">
                                    <i class="ri-refresh-line"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Danh sách sản phẩm --}}
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-12">
                            <div class="cr-shop-bredekamp d-flex justify-content-between align-items-center flex-wrap">
                                <div class="cr-toggle d-flex align-items-center gap-2 mb-2 mb-lg-0">
                                    <a href="javascript:void(0)" class="gridCol active-grid"><i
                                            class="ri-grid-line"></i></a>
                                    <a href="javascript:void(0)" class="gridRow"><i class="ri-list-check-2"></i></a>
                                </div>
                                <div class="center-content mb-2 mb-lg-0">
                                    <span id="product-count">Có {{ $products->total() }} sản phẩm áp dụng được!</span>
                                </div>
                                <div class="cr-select">
                                    <form method="GET" action="{{ route('voucher.products', $voucher->code) }}"
                                        class="d-flex align-items-center gap-2">
                                        <label>Sắp Xếp Theo:</label>
                                        @foreach (request()->except('sort') as $key => $value)
                                            @if (is_array($value))
                                                @foreach ($value as $v)
                                                    <input type="hidden" name="{{ $key }}[]"
                                                        value="{{ $v }}">
                                                @endforeach
                                            @else
                                                <input type="hidden" name="{{ $key }}"
                                                    value="{{ $value }}">
                                            @endif
                                        @endforeach
                                        <select class="form-select" name="sort" onchange="this.form.submit()">
                                            <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Mới
                                                nhất</option>
                                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ
                                                nhất</option>
                                            <option value="hot" {{ request('sort') == 'hot' ? 'selected' : '' }}>Hot
                                                nhất</option>
                                        </select>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Danh sách sản phẩm --}}
                    <div id="product-list-wrapper">
                        @if ($products->count())
                            <div class="row col-100 mb-minus-24" id="product-list">
                                @foreach ($products as $product)
                                    <div class="col-xxl-3 col-xl-4 col-6 cr-product-box mb-24">
                                        <div class="cr-product-card">
                                            <div class="cr-left">
                                                <div class="cr-product-image">
                                                    <div class="cr-image-inner zoom-image-hover">
                                                        <img src="{{ asset('storage/' . $product->image) }}"
                                                            alt="{{ $product->name }}" class="product-img">
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
                                                    </div>
                                                    <a class="cr-shopping-bag" href="#"><i
                                                            class="ri-shopping-bag-line"></i></a>
                                                </div>
                                            </div>

                                            <div class="cr-product-details flex-grow-1">
                                                <div class="cr-brand">
                                                    <a href="#">{{ $product->brand->name ?? '' }}</a>
                                                    @php
                                                        $variantReviews = $product->productVariants->flatMap->reviews;
                                                        $avg = round($variantReviews->avg('rating'), 1);
                                                        $fullStars = floor($avg);
                                                        $halfStar = $avg - $fullStars >= 0.5;
                                                        $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                                        $count = $variantReviews->count();
                                                    @endphp
                                                    <div class="text-center" style="line-height: 1.2;">
                                                        <div class="mb-1">
                                                            @for ($i = 0; $i < $fullStars; $i++)
                                                                <i class="ri-star-fill text-warning"></i>
                                                            @endfor
                                                            @if ($halfStar)
                                                                <i class="ri-star-half-line text-warning"></i>
                                                            @endif
                                                            @for ($i = 0; $i < $emptyStars; $i++)
                                                                <i class="ri-star-line text-warning"></i>
                                                            @endfor
                                                        </div>
                                                        <div class="text-muted small">
                                                            ({{ $avg }} / {{ $count }} đánh giá)
                                                        </div>
                                                    </div>
                                                </div>

                                                <a href="{{ route('productDetail', $product->slug) }}" class="title">
                                                    {{ $product->name }}
                                                </a>

                                                <div class="text product-description d-none">
                                                    {!! $product->description !!}
                                                </div>

                                                <ul class="list">
                                                    <li><label>Brand :</label> {{ $product->brand->name ?? '' }}</li>
                                                </ul>

                                                @php
                                                    $prices = optional($product->productVariants)
                                                        ->pluck('price')
                                                        ->filter();
                                                @endphp
                                                @if ($prices->isNotEmpty())
                                                    <p class="cr-price">
                                                        <span class="new-price">
                                                            @php
                                                                $min = $prices->min();
                                                                $max = $prices->max();
                                                            @endphp
                                                            {{ $min === $max
                                                                ? number_format($min, 0, ',', '.') . ' đ'
                                                                : number_format($min, 0, ',', '.') . ' đ - ' . number_format($max, 0, ',', '.') . ' đ' }}
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

                            {{-- Phân trang --}}
                            <div id="pagination-wrapper" class="mt-4">
                                {{ $products->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <img src="" alt="Không có sản phẩm"
                                    style="max-width: 200px;" class="mb-3">
                                <h5>Không có sản phẩm nào áp dụng mã giảm giá này.</h5>
                                <p>Vui lòng thử lại với mã khác hoặc xem các sản phẩm khác trong cửa hàng.</p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection
{{-- @push('scripts')
<script>
    $('#filter-form').on('submit', function(e) {
        e.preventDefault();
        let form = $(this);
        let url = form.attr('action');
        let data = form.serialize();

        $.ajax({
            url: url,
            method: 'GET',
            data: data,
            success: function(response) {
                $('#product-list').html(response); // ⚠️ phải có div này
            },
            error: function(xhr) {
                alert('Lỗi khi lọc sản phẩm.');
                console.error(xhr.responseText);
            }
        });
    });
</script>
@endpush --}}
