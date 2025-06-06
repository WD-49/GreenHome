@extends('layouts.admin')

@section('title', 'Chi tiết danh mục - ' . $category->name)

@section('content')
<style>
    body {
        background-color: #f8f9fa;
    }

    .category-container {
        max-width: 1000px;
        margin: 50px auto;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        padding: 30px;
    }

    .detail-label {
        font-weight: 600;
        color: #495057;
    }

    .status-active {
        color: #28a745;
        font-weight: bold;
    }

    .status-inactive {
        color: #dc3545;
        font-weight: bold;
    }

    .product-table th, .product-table td {
        vertical-align: middle;
    }
</style>

<div class="container category-container">
    {{-- Breadcrumbs --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.categories.index') }}">Danh mục</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $category->name }}</li>
        </ol>
    </nav>

    <h2 class="mb-4">Chi tiết danh mục: {{ $category->name }}</h2>

    <div class="row">
        <div class="col-md-12">
            <div class="mb-3">
                <span class="detail-label">Tên danh mục:</span> {{ $category->name }}
            </div>

            <div class="mb-3">
                <span class="detail-label">Mô tả:</span>
                {!! $category->description ?? '<em>Không có mô tả</em>' !!}
            </div>

            <div class="mb-3">
                <span class="detail-label">Slug:</span> {{ $category->slug }}
            </div>

            <div class="mb-3">
                <span class="detail-label">Trạng thái:</span>
                <span class="{{ $category->status ? 'status-active' : 'status-inactive' }}">
                    {{ $category->status ? 'Hiển thị' : 'Ẩn' }}
                </span>
            </div>

            <div class="mb-3">
                <span class="detail-label">Ngày tạo:</span>
                {{ $category->created_at ? $category->created_at->format('d/m/Y H:i') : 'N/A' }}
            </div>

            <div class="mb-3">
                <span class="detail-label">Ngày cập nhật:</span>
                {{ $category->updated_at ? $category->updated_at->format('d/m/Y H:i') : 'N/A' }}
            </div>

            <div class="mb-3">
                <span class="detail-label">Ngày xóa:</span>
                {{ $category->deleted_at ? $category->deleted_at->format('d/m/Y H:i') : 'Chưa xóa' }}
            </div>

            <div class="mb-4">
                <span class="detail-label">Tổng số sản phẩm:</span> {{ $productCount }}
            </div>

            {{-- Danh sách sản phẩm --}}
            <div class="mb-4">
                <h5>Sản phẩm thuộc danh mục</h5>
                @if ($products->isEmpty())
                    <p><em>Không có sản phẩm nào trong danh mục này.</em></p>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered product-table">
                            <thead class="thead-light">
                                <tr>
                                    <th>#</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Kho</th>
                                    <th>Ngày tạo</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($products as $index => $product)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $product->name }}</td>
                                        <td>{{ number_format($product->price, 0, ',', '.') }}₫</td>
                                        <td>{{ $product->stock_quantity ?? '0' }}</td>
                                        <td>{{ $product->created_at ? $product->created_at->format('d/m/Y') : '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="mt-4">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
