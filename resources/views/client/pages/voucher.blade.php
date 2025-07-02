@extends('layouts.app')
@section('content')

<style>
    .voucher-products h3 {
        font-size: 26px;
        margin-bottom: 25px;
        text-align: center;
        font-weight: 600;
    }

    .filter-box {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        margin-bottom: 30px;
    }

    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        border-radius: 12px;
        overflow: hidden;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .product-card img {
        height: 200px;
        object-fit: cover;
    }

    .product-title {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .product-price {
        font-size: 16px;
        color: #28a745;
        margin-bottom: 15px;
    }

    .btn-filter {
        width: 100%;
    }

    .pagination {
        justify-content: center;
        margin-top: 30px;
    }
</style>

<div class="container voucher-products mt-4">
    <h3> Sản phẩm đủ điều kiện cho mã giảm giá: <span class="text-success">{{ $voucher->code }}</span></h3>

    <form method="GET" class="row filter-box g-3">
        <div class="col-md-4">
            <input type="text" name="keyword" class="form-control" placeholder=" Tìm sản phẩm..." value="{{ request('keyword') }}">
        </div>
        <div class="col-md-4">
            <select name="category" class="form-select">
                <option value=""> Tất cả danh mục</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-grid">
            <button class="btn btn-success btn-filter"><i class="ri-filter-3-line"></i> Lọc sản phẩm</button>
        </div>
    </form>

    <div class="row">
        @forelse ($products as $product)
            <div class="col-md-3 mb-4">
                <div class="card product-card h-100">
                    <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                    <div class="card-body">
                        <h5 class="product-title">{{ $product->name }}</h5>
<p class="product-price">
    {{ number_format(optional($product->productVariant)->price) }} ₫
</p>

                        <a href="{{ route('productDetail', $product->slug) }}" class="btn btn-outline-primary w-100">
                            Xem chi tiết
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center">Không tìm thấy sản phẩm phù hợp với mã này.</div>
            </div>
        @endforelse
    </div>

    {{ $products->links() }}
</div>

@endsection
