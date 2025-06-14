@extends('layouts.admin')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <style>
        .nav-pills .nav-link.active {
            font-weight: 700;
            border-radius: 0 !important;
            background-color: transparent !important;
            color: #0768e8;
        }
    </style>

    <div class="row">
        <h2 class="text-center">{{ $title }}</h2>

        <!-- Tabs -->
        <ul class="nav nav-pills mb-3">
            {{-- <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.products.index') ? 'active' : '' }}"
                    href="{{ route('admin.products.index') }}">
                    Tất cả ({{ $productAll->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.products.trashed') ? 'active' : '' }}"
                    href="{{ route('admin.products.trashed') }}">
                    Thùng rác ({{ $productTrashed->count() }})
                </a>
            </li> --}}
        </ul>

        <div class="card">
            <div class="card-body">
                <div class="d-md-flex align-items-center">
                    <div>
                        <h4 class="card-title">Sản phẩm đã xóa</h4>
                        <p class="card-subtitle">Quản lý các sản phẩm trong thùng rác</p>
                    </div>

                    <div class="ms-auto mt-3 mt-md-0">
                        <!-- Bộ lọc -->
                        <div class="dropdown mb-3">
                            <a class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown"
                                data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                                <i class="fas fa-filter me-1"></i> Bộ lọc
                            </a>
                            <div class="dropdown-menu p-4" style="min-width: 800px;" aria-labelledby="filterDropdown">
                                <form method="GET" action="{{ route('admin.products.trashed') }}" class="row g-3">
                                    <!-- Tên sản phẩm -->
                                    <div class="col-md-6">
                                        <label for="name" class="form-label">Tên sản phẩm</label>
                                        <input type="text" name="name" id="name" class="form-control"
                                            placeholder="Nhập tên sản phẩm" value="{{ request('name') }}">
                                    </div>

                                    <!-- Danh mục -->
                                    <div class="col-md-6">
                                        <label for="category_id" class="form-label">Danh mục</label>
                                        <select name="category_id" id="category_id" class="form-select">
                                            <option value="">-- Tất cả danh mục --</option>
                                            @foreach ($categories as $category)
                                                <option value="{{ $category->id }}"
                                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                                    {{ $category->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Thương hiệu -->
                                    <div class="col-md-6">
                                        <label for="brand_id" class="form-label">Thương hiệu</label>
                                        <select name="brand_id" id="brand_id" class="form-select">
                                            <option value="">-- Tất cả thương hiệu --</option>
                                            @foreach ($brands as $brand)
                                                <option value="{{ $brand->id }}"
                                                    {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Trạng thái -->
                                    <div class="col-md-6">
                                        <label for="status" class="form-label">Trạng thái</label>
                                        <select name="status" id="status" class="form-select">
                                            <option value="">-- Tất cả trạng thái --</option>
                                            <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đang
                                                bán</option>
                                            <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Dừng
                                                bán</option>
                                        </select>
                                    </div>

                                    <!-- Ngày nhập -->
                                    <div class="col-md-12">
                                        <label class="form-label">Ngày nhập</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">Từ ngày</span>
                                            <input type="date" name="min_date" class="form-control"
                                                value="{{ request('min_date') }}">
                                            <span class="input-group-text bg-light">đến ngày</span>
                                            <input type="date" name="max_date" class="form-control"
                                                value="{{ request('max_date') }}">
                                        </div>
                                    </div>

                                    <!-- Khoảng giá -->
                                    <div class="col-md-12">
                                        <label class="form-label">Khoảng giá</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light">Từ</span>
                                            <input type="number" name="min_price" class="form-control" min="0"
                                                value="{{ request('min_price') }}">
                                            <span class="input-group-text bg-light">đến</span>
                                            <input type="number" name="max_price" class="form-control" min="0"
                                                value="{{ request('max_price') }}">
                                            <span class="input-group-text bg-light">VNĐ</span>
                                        </div>
                                    </div>

                                    <!-- Nút Tìm kiếm và Làm mới -->
                                    <div class="col-md-12 d-flex justify-content-end gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-search me-1"></i> Tìm kiếm
                                        </button>
                                        <a href="{{ route('admin.products.trashed') }}" class="btn btn-warning">
                                            <i class="fas fa-sync me-1"></i> Làm mới
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bảng sản phẩm -->
                <div class="table-responsive mt-4">
                    <table class="table mb-0 text-nowrap align-middle fs-3">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tên sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Ảnh</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->id }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->category->name }}</td>
                                    <td><img src="{{ asset('storage/' . $product->image) }}" width="60" class="rounded"
                                            alt="Ảnh sản phẩm"></td>
                                    <td>{{ number_format($product->price) }} đ</td>
                                    <td>{{ $product->quantity }}</td>
                                    <td>
                                        <span class="badge {{ $product->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $product->status == 1 ? 'Đang bán' : 'Dừng bán' }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button"
                                                id="dropdownMenuButton{{ $product->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="dropdownMenuButton{{ $product->id }}">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.products.restore', $product->id) }}">
                                                        Khôi phục
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.products.forceDelete', $product->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này vĩnh viễn không?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="dropdown-item text-danger" type="submit">
                                                            Xóa sản phẩm
                                                        </button>
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

                <!-- Phân trang -->
                @if ($products->lastPage() > 1)
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-end mt-3 mb-0">
                            <li class="page-item {{ $products->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $products->previousPageUrl() }}">Previous</a>
                            </li>

                            @for ($i = 1; $i <= $products->lastPage(); $i++)
                                <li class="page-item {{ $i == $products->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $products->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor

                            <li class="page-item {{ !$products->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $products->nextPageUrl() }}">Next</a>
                            </li>
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </div>
@endsection
