@extends('layouts.admin')

@section('title')
    Chi tiết thương hiệu: {{ $brand->name }}
@endsection

@section('content')
    <h1 class="text-center mb-4">Chi tiết thương hiệu: <strong>{{ $brand->name }}</strong></h1>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Thông tin thương hiệu</h5>
        </div>
        <div class="card-body">
            <p><strong>Tên thương hiệu:</strong> {{ $brand->name }}</p>
            <p><strong>Mô tả:</strong> {{ $brand->description ?? 'Chưa có mô tả' }}</p>
            {{-- Bạn có thể thêm các thông tin khác nếu có --}}
        </div>
    </div>

    <h3 class="mb-3">Danh sách sản phẩm thuộc thương hiệu</h3>

    @if($brand->products->isEmpty())
        <div class="alert alert-info">Chưa có sản phẩm nào thuộc thương hiệu này.</div>
    @else
        <div class="row row-cols-1 row-cols-md-3 g-4">
            @foreach ($brand->products as $product)
                <div class="col">
                    <div class="card h-100 shadow-sm">
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}">
                        @else
                            <img src="{{ asset('images/no-image.png') }}" class="card-img-top" alt="No image">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>
                            <p class="card-text text-truncate" style="max-height: 3em;">
                                {{ $product->description ?? 'Chưa có mô tả' }}
                            </p>
                            <p class="card-text"><strong>Giá: </strong>{{ number_format($product->price, 0, ',', '.') }} VNĐ</p>
                            <a href="{{ route('admin.products.show', $product->id) }}" class="btn btn-sm btn-primary">Xem chi tiết</a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary"><i class="fas fa-arrow-left me-1"></i> Quay lại danh sách</a>
    </div>
@endsection
