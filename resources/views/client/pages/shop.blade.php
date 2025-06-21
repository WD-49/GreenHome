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
                            <span> <a href="index.html">Home</a> - Shop</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Shop -->
    <section class="section-shop padding-tb-100">
        <div class="container">
            <div class="row d-none">
                <div class="col-lg-12">
                    <div class="mb-30" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                        <div class="cr-banner">
                            <h2>Categories</h2>
                        </div>
                        <div class="cr-banner-sub-title">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                                ut labore lacus vel facilisis. </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                {{-- sidebar -------------------------------------------------------------------------------------------- --}}
                <div class="col-lg-3 col-12 md-30" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                    <div class="cr-shop-sideview">
                        <form action="{{ route('shop.index') }}" method="GET" id="filter-form" class="mb-4">

                            {{-- Lọc theo danh mục --}}
                            <div class="mb-2 d-flex align-items-center">
                                <label for="category-select" class="fw-bold me-2"
                                    style="white-space: nowrap; width: 100px;">Danh mục:</label>
                                <select class="form-select" id="category-select" name="categories[]"
                                    onchange="this.form.submit()" style="width: 220px; max-width: 100%;">
                                    <option value="">Tất cả</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}"
                                            {{ in_array($category->id, request()->input('categories', [])) ? 'selected' : '' }}>
                                            {{ $category->name }} ({{ $category->products_count }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Lọc theo thương hiệu --}}
                            <div class="mb-2 d-flex align-items-center">
                                <label for="brand-select" class="fw-bold me-2"
                                    style="white-space: nowrap; width: 100px;">Thương hiệu:</label>
                                <select class="form-select" id="brand-select" name="brand_id" onchange="this.form.submit()"
                                    style="width: 220px; max-width: 100%;">
                                    <option value="">Tất cả</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}"
                                            {{ request()->input('brand_id') == $brand->id ? 'selected' : '' }}>
                                            {{ $brand->name }} ({{ $brand->products_count }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </form>





                        <div class="cr-shop-price">
                            <h4 class="cr-shop-sub-title">Price</h4>
                            <div class="price-range-slider">
                                <div id="slider-range" class="range-bar"></div>
                                <p class="range-value">
                                    <label>Price :</label>
                                    <input type="text" id="amount" placeholder="'" readonly>
                                </p>
                                <button type="button" class="cr-button">Filter</button>
                            </div>
                        </div>
                        <div class="cr-shop-color">
                            <h4 class="cr-shop-sub-title">Colors</h4>
                            <div class="cr-checkbox">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="blue">
                                    <label for="blue">Blue</label>
                                    <span class="blue"></span>
                                </div>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="yellow">
                                    <label for="yellow">Yellow</label>
                                    <span class="yellow"></span>
                                </div>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="red">
                                    <label for="red">Red</label>
                                    <span class="red"></span>
                                </div>
                            </div>
                        </div>
                        <div class="cr-shop-weight">
                            <h4 class="cr-shop-sub-title">Weight</h4>
                            <div class="cr-checkbox">
                                <div class="checkbox-group">
                                    <input type="checkbox" id="2kg">
                                    <label for="2kg">2kg Pack</label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="20kg">
                                    <label for="20kg">20kg Pack</label>
                                </div>
                                <div class="checkbox-group">
                                    <input type="checkbox" id="30kg">
                                    <label for="30kg">30kg pack</label>
                                </div>
                            </div>
                        </div>
                        <div class="cr-shop-tags">
                            <h4 class="cr-shop-sub-title">Tages</h4>
                            <div class="cr-shop-tags-inner">
                                <ul class="cr-tags">
                                    <li><a href="javascript:void(0)">Vegetables</a></li>
                                    <li><a href="javascript:void(0)">juice</a></li>
                                    <li><a href="javascript:void(0)">Food</a></li>
                                    <li><a href="javascript:void(0)">Dry Fruits</a></li>
                                    <li><a href="javascript:void(0)">Vegetables</a></li>
                                    <li><a href="javascript:void(0)">juice</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- endsidebar ---------------------------------------------------------------------------------------- --}}

                <div class="col-lg-9 col-12 md-30" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="600">
                    <div class="row">
                        <div class="col-12">
                            <div class="cr-shop-bredekamp">

                                <div class="center-content">
                                    <span>Có {{ $products->total() }} sản phẩm được tìm thấy</span>
                                </div>

                                <div class="cr-select">
                                    <label>Sắp xếp theo :</label>
                                    <form method="GET" action="{{ route('shop.index') }}">
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

                    {{-- list San PHam --}}
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
                                        <a class="cr-shopping-bag" href="#"><i
                                                class="ri-shopping-bag-line"></i></a>
                                    </div>

                                    <div class="cr-product-details">
                                        <div class="cr-brand">
                                            <a href="#">{{ $product->brand->name ?? 'Không có thương hiệu' }}</a>
                                            <div class="cr-star">
                                                <i class="ri-star-fill"></i><i class="ri-star-fill"></i>
                                                <i class="ri-star-fill"></i><i class="ri-star-fill"></i><i
                                                    class="ri-star-line"></i>
                                                <p>(4.5)</p>
                                            </div>
                                        </div>

                                        <a href="#" class="title">{{ $product->name }}</a>
                                        <p class="text">Sản phẩm chất lượng cao, giá tốt nhất thị trường.</p>

                                        <ul class="list">
                                            <li><label>Brand :</label> {{ $product->brand->name ?? 'Không rõ' }}</li>
                                            {{-- <li><label>Diet Type :</label> Vegetarian</li>
                                            <li><label>Speciality :</label> Gluten Free, Sugar Free</li> --}}
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


                    <nav aria-label="..." class="cr-pagination">
                        <ul class="pagination">
                            <li class="page-item disabled">
                                <span class="page-link">Previous</span>
                            </li>
                            <li class="page-item active" aria-current="page">
                                <span class="page-link">1</span>
                            </li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">3</a></li>
                            <li class="page-item">
                                <a class="page-link" href="#">Next</a>
                            </li>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>
    </section>
@endsection
