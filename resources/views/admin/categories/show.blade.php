@extends('layouts.admin')

@section('title')
    Chi tiết danh mục: {{ $category->name }}
@endsection

@section('content')
    <div class="row">
        <h2 class="text-center mb-4">{{ $category->name }}</h2>
           <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Quay lại danh mục sản phẩm
                    </a>
                </div>
            </div>

        <!-- Mô tả danh mục -->
        <div class="col-md-12 mb-4">
            <label class="form-label">Mô tả danh mục</label>
            <p>{!! $category->description !!}</p>
        </div>

        <!-- Bộ lọc tìm kiếm -->
        <form method="GET" action="{{ route('admin.categories.show', $category->slug) }}" class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="product_name" class="form-label">Tên sản phẩm</label>
                <input type="text" name="product_name" id="product_name" class="form-control"
                    value="{{ request('product_name') }}">
            </div>
            <div class="col-md-4">
                <label for="min_price" class="form-label">Giá từ</label>
                <input type="number" name="min_price" id="min_price" class="form-control"
                    value="{{ request('min_price') }}">
            </div>
            <div class="col-md-4">
                <label for="max_price" class="form-label">Giá đến</label>
                <input type="number" name="max_price" id="max_price" class="form-control"
                    value="{{ request('max_price') }}">
            </div>
            <div class="col-md-12 d-flex justify-content-end gap-2 mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i> Tìm kiếm
                </button>
            </div>
        </form>

        <!-- Danh sách sản phẩm -->
        <div class="col-md-12">
            <h5>Sản phẩm trong danh mục</h5>
            <p>Tổng số sản phẩm: {{ $products->total() }}</p>

            <div class="table-responsive mt-3">
                <table class="table table-striped table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th class="text-muted">ID</th>
                            <th class="text-muted">Tên sản phẩm</th>
                            <th class="text-muted">Giá</th>
                            <th class="text-muted">Số lượng</th>
                            <th class="text-muted text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>{{ $product->name }}</td>
                                <td>
                                    @if ($product->productVariants->count() > 0)
                                        @php
                                            $minPrice = $product->productVariants->min('price');
                                            $maxPrice = $product->productVariants->max('price');
                                        @endphp
                                        {{ number_format($minPrice) }} VND
                                        @if ($minPrice != $maxPrice)
                                            - {{ number_format($maxPrice) }} VND
                                        @endif
                                    @else
                                        {{ number_format($product->price) }} VND
                                    @endif
                                </td>

                                <td>{{ $product->quantity }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.products.show', $product->id) }}"
                                        class="btn btn-sm btn-primary">Xem chi tiết</a>
                    <a href="{{ route('admin.products.variants.index', $product) }}" class="btn btn-sm btn btn-info">Xem biến thể</a>

                                </td>
                            </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <div class="d-flex justify-content-end">
                {{ $products->links() }}
            </div>
        </div>
    </div>
@endsection
