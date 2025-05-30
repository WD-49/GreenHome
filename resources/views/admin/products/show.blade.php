@extends('layouts.admin')

@section('title', 'Chi tiết sản phẩm - ' . $product->name)

@section('content')
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> --}}
    <style>
        body {
            background-color: #f8f9fa;
        }

        .product-container {
            max-width: 900px;
            margin: 50px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
        }

        .product-image img {
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
        }

        .price {
            color: #dc3545;
            font-size: 1.5rem;
            font-weight: bold;
        }

        .promotional-price {
            color: #28a745;
            font-size: 1.3rem;
            font-weight: bold;
        }

        .status-active {
            color: #28a745;
            font-weight: bold;
        }

        .status-inactive {
            color: #dc3545;
            font-weight: bold;
        }

        .detail-label {
            font-weight: 600;
            color: #495057;
        }
    </style>
    <div class="container product-container">
        <div class="row">
            <!-- Product Image -->
            <div class="col-md-6 product-image">
                <!-- Replace with dynamic image source from the database -->
                <img src="{{ asset('storage/' . $product->image) }}" alt="Product Image" class="img-fluid">
            </div>
            <!-- Product Details -->
            <div class="col-md-6">
                <h2 class="mb-3">{{ $product->name }}</h2>
                <p class="text-muted">{{ $product->description ?? 'Sản phẩm chưa có mô tả' }}</p>

                <!-- Price and Promotional Price -->
                <div class="mb-3">
                    <span class="detail-label">Giá:</span>
                    <span class="price">{{ number_format($product->price, 0) }} đ</span>

                </div>
                <div class="mb-3">
                    @if ($product->promotional_price)
                        <span class="detail-label">khuyến mãi:
                            {{ number_format($product->promotional_price, 0) }} đ</span>
                    @endif
                </div>

                <!-- Quantity -->
                <div class="mb-3">
                    <span class="detail-label">Số lượng:</span> {{ $product->quantity }}
                </div>

                <!-- Category -->
                <div class="mb-3">
                    <span class="detail-label">Danh mục:</span> {{ $product->category->name ?? 'N/A' }}
                </div>

                <!-- Brand -->
                <div class="mb-3">
                    <span class="detail-label">Thương hiệu:</span> {{ $product->brand->name ?? 'N/A' }}
                </div>

                <!-- Date of Entry -->
                <div class="mb-3">
                    <span class="detail-label">Ngày nhập:</span>
                    {{ $product->date_of_entry ? $product->date_of_entry->format('d/m/Y H:i') : 'N/A' }}
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <span class="detail-label">Trạng thái:</span>
                    <span class="{{ $product->status ? 'status-active' : 'status-inactive' }}">
                        {{ $product->status ? 'Đang bán' : 'Dừng bán' }}
                    </span>
                </div>

                <!-- View Count -->
                <div class="mb-3">
                    <span class="detail-label">Lượt xem:</span> {{ $product->view }}
                </div>

                <!-- Action Buttons -->
                <div class="mt-4">
                    <a href="{{ route('admin.products.variants.index', $product) }}" class="btn btn-info btn-sm"
                        title="">Xem biến thể</i>
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Back to Products</a>
                </div>
            </div>
        </div>
    </div>
    {{-- <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script> --}}
@endsection
