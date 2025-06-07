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
            <p><strong>Mô tả:</strong> {!! $brand->description ?? '<em>Chưa có mô tả</em>' !!}</p>
            <p><strong>Số lượng sản phẩm:</strong> {{ $products->total() }}</p>
        </div>
    </div>

    {{-- FORM LỌC SẢN PHẨM --}}
    <form action="{{ route('admin.brands.show', $brand->slug) }}" method="GET" class="mb-4">
        <div class="row g-3">
            <div class="col-md-3">
                <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tên sản phẩm"
                       value="{{ request('keyword') }}">
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
                    <option value="">-- Chọn trạng thái --</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Đang bán</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Dừng bán</option>
                </select>
            </div>

            <div class="col-md-2">
                <input type="number" name="min_quantity" class="form-control" placeholder="Số lượng tối thiểu"
                       value="{{ request('min_quantity') }}">
            </div>

            <div class="col-md-2 d-grid">
                <button type="submit" class="btn btn-primary">Lọc sản phẩm</button>
            </div>
        </div>
    </form>

    {{-- Danh sách sản phẩm --}}
    @if($products->isEmpty())
        <div class="alert alert-info">Chưa có sản phẩm nào thuộc thương hiệu này.</div>
    @else
        <!-- Bảng danh sách sản phẩm giữ nguyên như bạn đã có -->
        <div class="table-responsive">
            <table class="table mb-0 text-nowrap varient-table align-middle fs-3">
                <thead>
                    <tr>
                        <th class="px-0 text-muted">ID</th>
                        <th class="px-0 text-muted">Tên sản phẩm</th>
                        <th class="px-0 text-muted">Slug</th>
                        <th class="px-0 text-muted">Danh mục</th>
                        <th class="px-0 text-muted">Ảnh</th>
                        <th class="px-0 text-muted">Số lượng</th>
                        <th class="px-0 text-muted">Trạng thái</th>
                        <th class="px-0 text-muted text-end">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                   @foreach ($products as $product)
    <tr>
        <td class="px-0">{{ $product->id }}</td>
        <td class="px-0">{{ $product->name }}</td>
        <td class="px-0">{{ $product->slug }}</td>
        <td class="px-0">{{ $product->category->name ?? 'Chưa có danh mục' }}</td>
        <td class="px-0">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="50" height="50" style="object-fit: cover;">
            @else
                <span class="text-muted">Không có ảnh</span>
            @endif
        </td>
        <td class="px-0">{{ $product->quantity }}</td>
        <td class="px-0">
            @if($product->status)
                <span class="badge bg-success">Đang bán</span>
            @else
                <span class="badge bg-secondary">Dừng bán</span>
            @endif
        </td>
        <td class="px-0 text-end">
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning me-1">Sửa</a>
            {{-- Thêm nút xóa, chi tiết nếu cần --}}
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

    <div class="mt-4">
        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>
@endsection
