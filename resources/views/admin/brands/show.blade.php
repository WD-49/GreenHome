@extends('layouts.admin')

@section('title')
    Chi tiết thương hiệu: {{ $brand->name }}
@endsection

@section('content')
<div class="container-xxl">
    <div class="py-3 d-flex align-items-center flex-sm-row flex-column mb-3">
        <div class="flex-grow-1 d-flex align-items-center gap-2">
            <i class="mdi mdi-tag-multiple-outline fs-3 text-primary"></i>
            <h4 class="fs-20 fw-bold m-0">Chi tiết thương hiệu: {{ $brand->name }}</h4>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Thông tin thương hiệu</h5>
        </div>
        <div class="card-body">
            <p><strong>Tên thương hiệu:</strong> {{ $brand->name }}</p>
            <p><strong>Mô tả:</strong> {!! $brand->description ?? '<em>Chưa có mô tả</em>' !!}</p>
            <p><strong>Tổng số sản phẩm:</strong> {{ $products->total() }}</p>
        </div>
    </div>

    {{-- FORM LỌC --}}
    <form method="GET" action="{{ route('admin.brands.show', $brand->slug) }}" class="row g-3 mb-4">
        <div class="col-md-3">
            <input type="text" name="keyword" class="form-control" placeholder="Tìm tên sản phẩm..." value="{{ request('keyword') }}">
        </div>
        <div class="col-md-3">
            <select name="category_id" class="form-select">
                <option value="">-- Chọn danh mục --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select">
                <option value="">-- Trạng thái --</option>
                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang bán</option>
                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Dừng bán</option>
            </select>
        </div>
        <div class="col-md-2">
            <input type="number" name="min_quantity" class="form-control" placeholder="SL tối thiểu" value="{{ request('min_quantity') }}">
        </div>
        <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary">Lọc sản phẩm</button>
        </div>
    </form>

    {{-- DANH SÁCH SẢN PHẨM --}}
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Sản phẩm thuộc thương hiệu</h5>
        </div>
        <div class="card-body">
            @if ($products->isEmpty())
                <div class="alert alert-info">Không có sản phẩm nào thuộc thương hiệu này.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle text-nowrap">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Ảnh</th>
                                <th>Tên</th>
                                <th>Slug</th>
                                <th>Danh mục</th>
                                <th>Số lượng</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $index => $product)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        @if($product->image)
                                            <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}"
                                                 class="rounded" width="50" height="50" style="object-fit: cover;">
                                        @else
                                            <span class="text-muted">Không có ảnh</span>
                                        @endif
                                    </td>
                                    <td>{{ $product->name }}</td>
                                    <td><span class="badge bg-info">{{ $product->slug }}</span></td>
                                    <td>{{ $product->category->name ?? 'Chưa có danh mục' }}</td>
                                    <td>{{ $product->quantity }}</td>
                                    <td>
                                        @if ($product->status)
                                            <span class="badge bg-success">Đang bán</span>
                                        @else
                                            <span class="badge bg-secondary">Dừng bán</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <span class="mdi mdi-dots-vertical"></span>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="{{ route('admin.products.show', $product->id) }}">Chi tiết</a></li>
                                                <li><a class="dropdown-item" href="{{ route('admin.products.edit', $product->id) }}">Chỉnh sửa</a></li>
                                                <li>
                                                    <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="dropdown-item text-danger" type="submit">Xóa</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-center mt-3">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách thương hiệu
        </a>
    </div>
</div>
@endsection
