@extends('layouts.admin')

@section('title')
    {{ $title }}
@endsection
@section('content')
    <h1 class="text-center">{{ $title }}</h1>

    {{-- <div class="card-header bg-primary text-white">
        <h5 class="mb-0 "><i class="fas fa-search"></i> Lọc sản phẩm</h5>
    </div>
    <div class="card">s
        <div class="card-body">
            <form method="GET" action="{{ route('admin.products.index') }}" class="row g-3">
                <!-- Tên sản phẩm -->
                <div class="col-md-3">
                    <label for="name" class="form-label">Tên sản phẩm</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Nhập tên sản phẩm"
                        value="{{ request('name') }}" style="border-color: #e9ecef;">
                </div>
                <!-- Danh mục -->
                <div class="col-md-3">
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
                <div class="col-md-3">
                    <label for="brand_id" class="form-label">Thương hiệu</label>
                    <select name="brand_id" id="brand_id" class="form-select">
                        <option value="">-- Tất cả thương hiệu --</option>
                        @foreach ($brands as $brand)
                            <option value="{{ $brand->id }}" {{ request('brand_id') == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Trạng thái -->
                <div class="col-md-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select name="status" id="status" class="form-select">
                        <option value="">-- Tất cả trạng thái --</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đang bán</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Dừng bán</option>
                    </select>
                </div>

                <!-- Ngày nhập -->
                <div class="col-md-4">
                    <label for="date_range" class="form-label">Ngày nhập</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">Từ ngày</span>
                        <input type="date" name="min_date" class="form-control" value="{{ request('min_date') }}">
                        <span class="input-group-text bg-light">đến ngày</span>
                        <input type="date" name="max_date" class="form-control" value="{{ request('max_date') }}">
                    </div>
                </div>

                <!-- Khoảng giá -->
                <div class="col-md-4">
                    <label for="price_range" class="form-label">Khoảng giá</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light">Từ</span>
                        <input type="number" name="min_price" id="min_price" class="form-control"
                            placeholder="Giá tối thiểu" value="{{ request('min_price') }}" min="0">
                        <span class="input-group-text bg-light">đến</span>
                        <input type="number" name="max_price" id="max_price" class="form-control" placeholder="Giá tối đa"
                            value="{{ request('max_price') }}" min="0">
                        <span class="input-group-text bg-light">VNĐ</span>
                    </div>
                </div>
                <!-- Nút Tìm kiếm và Làm mới -->
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Tìm kiếm
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-warning w-100">
                        <i class="fas fa-sync me-1"></i> Làm mới
                    </a>
                </div>
            </form>
        </div>
    </div> --}}
    <div class="col-12 d-flex align-center justify-content-center gap-2">
        <a href="{{ route('admin.products.variants.index', $product) }}" class="btn btn-success align-content-center"
            title="Thêm sản phẩm"> Back
        </a>
    </div>
    <div class="card shadow-sm mb-4">
        <div class="table-responsive py-4">
            <table class="table table-flush" id="datatable">
                <thead class="thead-light">
                    <tr>
                        <th>STT</th>
                        <th>Mã sản phẩm</th>
                        <th>thuộc tính</th>
                        <th>ảnh</th>
                        <th>Giá</th>
                        <th>số lượng</th>
                        <th>Trạng thái</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($variants as $index => $variant)
                        <tr>
                            <td>{{ $variant->id }}</td>
                            <td>{{ $variant->sku }}</td>
                            <td>
                                @foreach ($variant->productVariantValues as $pvv)
                                    <li>
                                        {{ $pvv->attributeValue->attribute->name }}: {{ $pvv->attributeValue->value }}
                                    </li>
                                @endforeach
                            </td>
                            <td> <img src="{{ asset('storage/' . $variant->image) }}" width="100px"
                                    alt="Hình ảnh sản phẩm">
                            </td>
                            <td>{{ number_format($variant->price, 0) }} đ</td>

                            <td>{{ $variant->quantity }}</td>
                            <td scope="row">
                                <span class="badge {{ $variant->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $variant->status == 1 ? 'Đang bán' : 'Dừng bán' }}
                                </span>
                            </td>
                            {{-- @php
                                // dd($variant->product, $variant);

                            @endphp --}}

                            <td>
                                <a href="{{ route('admin.products.variants.restore', [$variant->product, $variant]) }}"
                                    class="btn btn-primary btn-sm" title="Khôi phục"><i class="fa-solid fa-rotate-left"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if ($variants->lastPage() > 1)
            <nav aria-label="Page navigation example">
                <ul class="pagination mb-0">

                    {{-- Previous --}}
                    <li class="page-item {{ $variants->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $variants->previousPageUrl() }}" tabindex="-1">Previous</a>
                    </li>

                    {{-- Page Numbers --}}
                    @for ($i = 1; $i <= $variants->lastPage(); $i++)
                        <li class="page-item {{ $i == $variants->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $variants->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor

                    {{-- Next --}}
                    <li class="page-item {{ !$variants->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $variants->nextPageUrl() }}">Next</a>
                    </li>
                </ul>
            </nav>
        @endif
    @endsection
