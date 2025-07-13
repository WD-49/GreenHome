@extends('layouts.app')
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
        <div class="container">
            <div class="row">
                <!-- Bộ lọc Sidebar -->
                <div class="col-lg-3">
                    <div class="cr-shop-sideview">
                        <form action="{{ route('voucher.products', $voucher->code) }}" method="GET" id="filter-form"
                            class="mb-4">
                            <div class="mb-3">
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

                            <div class="mb-3">
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
                            </div>

                            @php
                                $selectedValues = collect(request()->input('attribute_values', []))->map(
                                    fn($id) => (int) $id,
                                );
                                $grouped = $attributeValues->groupBy(fn($v) => $v->attribute->name);
                            @endphp

                            @foreach ($grouped as $attrName => $values)
                                <div class="dropdown mb-3">
                                    <button class="btn btn-outline-secondary w-100 text-start dropdown-toggle"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ $attrName }}
                                        @php
                                            $selectedLabels = $values
                                                ->filter(fn($v) => $selectedValues->contains($v->id))
                                                ->pluck('value')
                                                ->implode(', ');
                                        @endphp
                                        @if ($selectedLabels)
                                            <span class="text-primary small">: {{ $selectedLabels }}</span>
                                        @endif
                                    </button>
                                    <ul class="dropdown-menu px-3" style="width: 100%;">
                                        @foreach ($values as $value)
                                            <li>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="attribute_values[]" value="{{ $value->id }}"
                                                        id="attr-{{ $value->id }}"
                                                        {{ $selectedValues->contains($value->id) ? 'checked' : '' }}>
                                                    <label class="form-check-label"
                                                        for="attr-{{ $value->id }}">{{ $value->value }}</label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach

                            <div class="mb-3">
                                <label class="fw-bold">Đánh giá:</label>
                                @for ($i = 5; $i >= 1; $i--)
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="rating"
                                            id="rating{{ $i }}" value="{{ $i }}"
                                            {{ request('rating') == $i ? 'checked' : '' }}>
                                        <label class="form-check-label" for="rating{{ $i }}">
                                            @for ($j = 1; $j <= $i; $j++)
                                                <i class="ri-star-fill text-warning"></i>
                                            @endfor
                                            @for ($j = $i + 1; $j <= 5; $j++)
                                                <i class="ri-star-line"></i>
                                            @endfor
                                        </label>
                                    </div>
                                @endfor
                            </div>

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

                <!-- Danh sách sản phẩm -->
                <div class="col-lg-9">
                    <div class="row">
                        <div class="col-12">
                            <div class="cr-shop-bredekamp d-flex justify-content-between align-items-center flex-wrap">
                                <div class="cr-toggle d-flex align-items-center gap-2 mb-2 mb-lg-0">
                                    <a href="javascript:void(0)" class="gridCol active-grid">
                                        <i class="ri-grid-line"></i>
                                    </a>
                                    <a href="javascript:void(0)" class="gridRow">
                                        <i class="ri-list-check-2"></i>
                                    </a>
                                </div>

                                <div class="center-content mb-2 mb-lg-0">
                                    <span>Có {{ $products->total() }} sản phẩm áp dụng được!</span>
                                </div>

                                <div class="cr-select">
                                    <form method="GET" action="{{ route('voucher.products', $voucher->code) }}"
                                        class="d-flex align-items-center gap-2">
                                        <label>Sort By:</label>
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

                    <div class="row col-100 mb-minus-24">
                        @forelse ($products as $product)
                            <div class="col-xxl-3 col-xl-4 col-6 cr-product-box mb-24">
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
                                        </div>
                                        <a class="cr-shopping-bag" href="#"><i
                                                class="ri-shopping-bag-line"></i></a>
                                    </div>
                                    <div class="cr-product-details">
                                        <div class="cr-brand">
                                            <a href="#">{{ $product->brand->name ?? 'Không có thương hiệu' }}</a>
                                            @php
                                                $avg = $product->reviews_avg_rating ?? 0;
                                                $count = $product->reviews_count ?? 0;
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
                                        <a href="{{ route('productDetail', $product->slug) }}"
                                            class="title">{{ $product->name }}</a>
                                        <p class="text">Sản phẩm chất lượng cao, giá tốt nhất thị trường.</p>
                                        <ul class="list">
                                            <li><label>Brand :</label> {{ $product->brand->name ?? 'Không rõ' }}</li>
                                        </ul>
                                        @php
                                            $prices = optional($product->productVariants)->pluck('price')->filter();
                                        @endphp
                                        @if ($prices->isNotEmpty())
                                            <p class="cr-price">
                                                <span class="new-price">
                                                    @php
                                                        $min = $prices->min();
                                                        $max = $prices->max();
                                                    @endphp
                                                    {{ $min === $max ? number_format($min, 0, ',', '.') . ' đ' : number_format($min, 0, ',', '.') . ' đ - ' . number_format($max, 0, ',', '.') . ' đ' }}
                                                </span>
                                            </p>
                                        @else
                                            <p class="cr-price"><span class="new-price">Chưa có giá</span></p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p>Không có sản phẩm nào phù hợp với mã này.</p>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $products->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            xhrFields: {
                withCredentials: true
            }
        });

        $(document).on('click', '.wishlist-button', function(e) {
            e.preventDefault();
            let $btn = $(this);
            let productId = $btn.data('product-id');

            $.ajax({
                url: '{{ route('wishlist.toggle') }}',
                method: 'POST',
                data: {
                    product_id: productId
                },
                success: function(res) {
                    if (res.added) {
                        $btn.find('i').removeClass('ri-heart-line').addClass(
                            'ri-heart-fill text-danger');
                    } else {
                        $btn.find('i').removeClass('ri-heart-fill text-danger').addClass(
                            'ri-heart-line');
                    }
                    alert(res.message);
                },
                error: function(xhr) {
                    if (xhr.status === 401) {
                        alert('Vui lòng đăng nhập để thêm vào wishlist');
                    } else {
                        alert('Đã có lỗi xảy ra!');
                    }
                }
            });
        });
    </script>
@endpush
