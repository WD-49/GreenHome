@extends('layouts.app')



@section('content')
    <!-- Breadcrumb -->
    <section class="section-breadcrumb">
        <div class="cr-breadcrumb-image">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="cr-breadcrumb-title">
                            <h2>Shop</h2>
                            <span><a href="{{ route('home') }}">Home</a> - Shop</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Shop -->
    <section class="section-shop padding-tb-100">
        <div class="container">
            <div class="row">
                {{-- Sidebar --}}
                <div class="col-lg-3">
                    <div class="cr-shop-sideview">
                        <form action="{{ route('shop.index') }}" method="GET" id="filter-form" class="mb-4">

                            {{-- Danh mục --}}
                            <div class="mb-3">
                                <label class="fw-bold">Danh mục:</label>
                                <select class="form-select" name="categories[]">

                                    <option value="">Tất cả</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ in_array($category->id, request()->input('categories', [])) ? 'selected' : '' }}>
                                            {{ $category->name }} ({{ $category->products_count }})
                                        </option>
                                    @endforeach
                                </select>


                            </div>

                            {{-- Thương hiệu --}}
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

                            {{-- Biến thể --}}
                            @php $grouped = $attributeValues->groupBy(fn($v) => $v->attribute->name); @endphp
                            @foreach ($grouped as $attrName => $values)
                                <div class="dropdown mb-3">
                                    <button class="btn btn-outline-secondary w-100 text-start dropdown-toggle"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        {{ $attrName }}
                                    </button>
                                    <ul class="dropdown-menu px-3" style="width: 100%;">
                                        @foreach ($values as $value)
                                            <li>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox"
                                                        name="attribute_values[]" value="{{ $value->id }}"
                                                        id="attr-{{ $value->id }}"
                                                        {{ in_array($value->id, request()->input('attribute_values', [])) ? 'checked' : '' }}>
                                                    <label class="form-check-label" for="attr-{{ $value->id }}">
                                                        {{ $value->value }}
                                                    </label>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endforeach

                            {{-- Lọc theo giá --}}
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
                                <button type="submit" class="btn btn-primary">Lọc</button>
                                <a href="{{ route('shop.index') }}" class="btn btn-btn-warning">Reset</a>
                            </div>

                        </form>
                    </div>
                </div>

                {{-- Danh sách sản phẩm --}}
                <div class="col-lg-9">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span>Có {{ $products->total() }} sản phẩm được tìm thấy</span>


                        {{-- Sắp xếp --}}
                        <form method="GET" action="{{ route('shop.index') }}" class="d-flex align-items-center">
                            @foreach (request()->except('sort') as $key => $value)
                                @if (is_array($value))
                                    @foreach ($value as $v)
                                        <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach
                            <label class="me-2">Sắp xếp:</label>
                            <select class="form-select" name="sort" onchange="this.form.submit()">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Mới nhất
                                </option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                                <option value="hot" {{ request('sort') == 'hot' ? 'selected' : '' }}>Hot nhất</option>
                            </select>
                        </form>

                    </div>

                    {{-- Sản phẩm: 4 mỗi hàng --}}
                    <div class="row col-100 mb-minus-24">
                        @foreach ($products as $product)
                            <div class="col-xxl-3 col-xl-4 col-6 cr-product-box mb-24">
                                <div class="cr-product-card">
                                    <div class="cr-product-image">
                                        <div class="cr-image-inner zoom-image-hover">
                                            <img src="{{ asset('storage/' . $product->image) }}"
                                                alt="{{ $product->name }}">
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
                                            <a href="#">{{ $product->brand->name ?? 'Không có thương hiệu' }}</a>

                                            {{-- ⭐️ Hiển thị sao từ reviews --}}
                                            @php
                                                $avg = round($product->reviews_avg_rating ?? 0, 1); // ví dụ: 4.2
                                                $fullStars = floor($avg);
                                                $halfStar = $avg - $fullStars >= 0.5 ? 1 : 0;
                                                $emptyStars = 5 - $fullStars - $halfStar;
                                            @endphp
                                            <div class="cr-star">
                                                @php
                                                    $avg = $product->reviews_avg_rating ?? 0;
                                                    $count = $product->reviews_count ?? 0;
                                                    $fullStars = floor($avg);
                                                    $halfStar = $avg - $fullStars >= 0.5;
                                                    $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                                @endphp

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

                                        <a href="#" class="title">{{ $product->name }}</a>
                                        <p class="text">Sản phẩm chất lượng cao, giá tốt nhất thị trường.</p>

                                        <ul class="list">
                                            <li><label>Brand :</label> {{ $product->brand->name ?? 'Không rõ' }}</li>
                                        </ul>

                                        {{-- Nếu có giá thì hiển thị --}}
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

                                        <a href="{{ route('productDetail', $product->slug) }}"
                                            class="btn btn-primary mt-2">
                                            Xem chi tiết
                                        </a>

                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                </div>

                {{-- list San PHam --}}


                {{-- Phân trang --}}
                <div class="mt-4">
                    {{ $products->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
        </div>
    </section>
@endsection


