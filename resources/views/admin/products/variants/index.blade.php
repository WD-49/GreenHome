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
        <div class="d-md-flex align-items-center">
            {{-- Nút thao tác --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách sản phẩm
                    </a>
                </div>

            </div>
            {{-- BỘ LỌC --}}
            <div class="ms-auto mt-3 mt-md-0">

                <div class="dropdown mb-3 text-end">

                    <a href="{{ route('admin.products.variants.create', $product) }}" class="btn btn-outline-primary">
                        <i class="fa-solid fa-square-plus me-1"></i> Thêm biến thể
                    </a>
                    <a class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false"
                        data-bs-auto-close="outside">
                        <i class="fas fa-filter me-1"></i> Bộ lọc
                    </a>
                    <div class="dropdown-menu p-4" style="min-width: 600px;">
                        <form method="GET" action="{{ route('admin.products.variants.index', $product) }}"
                            class="row g-3">

                            {{-- Thuộc tính --}}
                            <div class="col-md-6">
                                <label for="attribute" class="form-label">Thuộc tính</label>
                                <input type="text" name="attribute" id="attribute" class="form-control"
                                    value="{{ request('attribute') }}" placeholder="Màu, size...">
                            </div>

                            {{-- Giá từ đến --}}
                            <div class="col-md-6">
                                <label class="form-label">Khoảng giá</label>
                                <div class="input-group">
                                    <input type="number" name="min_price" class="form-control" placeholder="Từ"
                                        value="{{ request('min_price') }}">
                                    <input type="number" name="max_price" class="form-control" placeholder="Đến"
                                        value="{{ request('max_price') }}">
                                </div>
                            </div>

                            {{-- Trạng thái --}}
                            <div class="col-md-6">
                                <label for="status" class="form-label">Trạng thái</label>
                                <select name="status" id="status" class="form-select">
                                    <option value="">-- Tất cả trạng thái --</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Đang bán
                                    </option>
                                    <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Dừng bán
                                    </option>
                                </select>
                            </div>

                            <div class="col-md-12 d-flex justify-content-end gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-1"></i> Tìm kiếm
                                </button>
                                <a href="{{ route('admin.products.variants.index', $product) }}" class="btn btn-warning">
                                    <i class="fas fa-sync me-1"></i> Làm mới
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <ul class="nav nav-pills mb-3">
            <li class="nav-item">
                <a class="nav-link {{ request('status') == null ? 'active' : '' }}"
                    href="{{ route('admin.products.variants.index', $product) }}">
                    Tất cả ({{ $product->productVariants->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.products.variants.trashed', $product) ? 'active' : '' }}"
                    href="{{ route('admin.products.variants.trashed', $product) }}">
                    Thùng rác ({{ $variantTrashed->count() }})
                </a>
            </li>
        </ul>

        {{-- DANH SÁCH BIẾN THỂ --}}
        <div class="card shadow-sm">

            <div class="card-body">

                <div class="table-responsive">
                    <div>
                        <h4 class="card-title">Danh sách biến thể sản phẩm</h4>
                    </div>
                    <table class="table table-hover align-middle text-nowrap">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Mã SKU</th>
                                <th>Thuộc tính</th>
                                <th>Ảnh</th>
                                <th>Giá</th>
                                <th>Số lượng</th>
                                <th>Trạng thái</th>
                                <th class="text-end">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($variants as $index => $variant)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $variant->sku }}</td>
                                    <td>
                                        <ul class="mb-0">
                                            @foreach ($variant->productVariantValues as $pvv)
                                                <li>{{ $pvv->attributeValue->attribute->name }}:
                                                    {{ $pvv->attributeValue->value }}</li>
                                            @endforeach
                                        </ul>
                                    </td>
                                    <td>

                                        @if ($variant->image)
                                            <img src="{{ asset('storage/' . $variant->image) }}" width="60"
                                                class="rounded" alt="Ảnh biến thể">
                                        @else
                                            biến thể không có ảnh
                                        @endif

                                    </td>
                                    <td>{{ number_format($variant->price, 0) }} đ</td>
                                    <td>{{ $variant->quantity }}</td>
                                    <td>
                                        <span class="badge {{ $variant->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                            {{ $variant->status == 1 ? 'Đang bán' : 'Dừng bán' }}
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
                                                        href="{{ route('admin.products.variants.edit', [$variant->product, $variant]) }}">
                                                        Chỉnh sửa
                                                    </a>
                                                </li>
                                                <li>
                                                    <form
                                                        action="{{ route('admin.products.variants.destroy', [$variant->product, $variant]) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn bỏ sản phẩm này vào thùng rác không?')">
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

                            @if ($variants->count() == 0)
                                <tr>
                                    <td colspan="8" class="text-center text-muted">Không có biến thể nào phù hợp</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>

                {{-- PHÂN TRANG --}}
                @if ($variants->lastPage() > 1)
                    <nav class="mt-3">
                        <ul class="pagination justify-content-end">
                            <li class="page-item {{ $variants->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $variants->previousPageUrl() }}">Previous</a>
                            </li>
                            @for ($i = 1; $i <= $variants->lastPage(); $i++)
                                <li class="page-item {{ $i == $variants->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $variants->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor
                            <li class="page-item {{ !$variants->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $variants->nextPageUrl() }}">Next</a>
                            </li>
                        </ul>
                    </nav>
                @endif

            </div>
        </div>
    </div>
@endsection
