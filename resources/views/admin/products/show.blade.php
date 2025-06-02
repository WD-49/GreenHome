@extends('layouts.admin')

@section('title', 'Chi tiết sản phẩm - ' . $product->name)

@section('content')
    <style>
        .product-detail-container {
            max-width: 1200px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
            font-family: 'Arial', sans-serif;
        }

        .product-main-image {
            width: 100%;
            border-radius: 10px;
            object-fit: contain;
            background-color: #f5f5f5;
        }

        .product-thumbnails img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border: 1px solid #ddd;
            border-radius: 6px;
            cursor: pointer;
            margin-right: 10px;
            transition: border-color 0.3s ease;
        }

        .product-thumbnails img:hover {
            border-color: #007bff;
        }

        .product-title {
            font-size: 28px;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 20px;
        }

        .detail-label {
            font-weight: 600;
            color: #555;
            font-size: 16px;
            width: 150px;
            display: inline-block;
        }

        .detail-value {
            font-size: 16px;
            color: #333;
            font-weight: 500;
        }

        .price {
            font-size: 24px;
            font-weight: 700;
            color: #e74c3c;
            background: #f1f1f1;
            padding: 8px 16px;
            border-radius: 6px;
            display: inline-block;
        }

        .promotional-price {
            font-size: 20px;
            font-weight: 600;
            color: #27ae60;
            background: #e8f5e9;
            padding: 8px 16px;
            border-radius: 6px;
            display: inline-block;
        }

        .status-active {
            color: #27ae60;
            font-weight: 600;
            background: #e8f5e9;
            padding: 5px 10px;
            border-radius: 6px;
        }

        .status-inactive {
            color: #e74c3c;
            font-weight: 600;
            background: #fce4e4;
            padding: 5px 10px;
            border-radius: 6px;
        }

        .tabs-section {
            margin-top: 40px;
        }

        .tab-content {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            font-size: 16px;
            color: #333;
        }

        .nav-tabs .nav-link {
            font-size: 16px;
            font-weight: 600;
            color: #555;
        }

        .nav-tabs .nav-link.active {
            color: #007bff;
            border-color: #007bff;
        }

        .btn-info,
        .btn-outline-secondary {
            font-size: 14px;
            font-weight: 600;
            padding: 10px 20px;
            border-radius: 6px;
        }

        .btn-info {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn-outline-secondary {
            border-color: #6c757d;
            color: #6c757d;
        }
    </style>

    <div class="container product-detail-container">
        <div class="row">
            <div class="col-md-6">
                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid product-main-image" alt="Product Image">
                <div class="mt-3 d-flex product-thumbnails">
                    @foreach ($product->images ?? [] as $img)
                        <img src="{{ asset('storage/' . $img->path) }}" alt="Thumb">
                    @endforeach
                </div>
            </div>

            <div class="col-md-6">
                <h2 class="product-title">{{ $product->name }}</h2>

                <!-- Price -->

                @php
                    $variantCount = $variants->count();
                @endphp

                <div class="mb-3">
                    <span class="detail-label">Giá:</span>
                    <span class="detail-value price">
                        @if ($variantCount === 0)
                            {{ number_format($product->price, 0) }} đ
                        @elseif ($variantCount === 1)
                            {{ number_format($variants->first()->price, 0) }} đ
                        @else
                            @php
                                $prices = $variants->pluck('price')->unique()->sort();
                            @endphp

                            @if ($prices->count() === 1)
                                {{ number_format($prices->first(), 0) }} đ
                            @else
                                {{ number_format($prices->first(), 0) }} đ - {{ number_format($prices->last(), 0) }} đ
                            @endif
                        @endif
                    </span>
                </div>



                <!-- Quantity -->
                <div class="mb-3">
                    <span class="detail-label">Số lượng:</span>
                    <span class="detail-value">{{ $product->quantity }}</span>
                </div>

                <!-- Category -->
                <div class="mb-3">
                    <span class="detail-label">Danh mục:</span>
                    <span class="detail-value">{{ $product->category->name ?? 'N/A' }}</span>
                </div>

                <!-- Brand -->
                <div class="mb-3">
                    <span class="detail-label">Thương hiệu:</span>
                    <span class="detail-value">{{ $product->brand->name ?? 'N/A' }}</span>
                </div>

                <!-- Date of Entry -->
                <div class="mb-3">
                    <span class="detail-label">Ngày nhập:</span>
                    <span
                        class="detail-value">{{ $product->date_of_entry ? $product->date_of_entry->format('d/m/Y H:i') : 'N/A' }}</span>
                </div>

                <!-- Status -->
                <div class="mb-3">
                    <span class="detail-label">Trạng thái:</span>
                    <span class="detail-value {{ $product->status ? 'status-active' : 'status-inactive' }}">
                        {{ $product->status ? 'Đang bán' : 'Dừng bán' }}
                    </span>
                </div>

                <!-- View Count -->
                <div class="mb-3">
                    <span class="detail-label">Lượt xem:</span>
                    <span class="detail-value">{{ $product->view }}</span>
                </div>

                <!-- Action Buttons -->
                <div class="mt-4">
                    <a href="{{ route('admin.products.variants.index', $product) }}" class="btn btn-info">Xem biến thể</a>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Quay lại danh sách</a>
                </div>
            </div>
        </div>

        <div class="tabs-section mt-5">
            <ul class="nav nav-tabs" id="productTabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="description-tab" data-bs-toggle="tab" href="#description"
                        role="tab">Mô tả</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="comments-tab" data-bs-toggle="tab" href="#comments" role="tab">Bình luận</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="variants-tab" data-bs-toggle="tab" href="#variants" role="tab">Biến thể</a>
                </li>
            </ul>
            <div class="tab-content mt-3">
                <div class="tab-pane fade show active" id="description" role="tabpanel">
                    <div class="tab-content">
                        {!! $product->description !!}
                    </div>
                </div>
                <div class="tab-pane fade" id="comments" role="tabpanel">
                    <form method="GET" action="{{ route('admin.products.show', $product) }}#comments" class="row g-3">

                        {{-- tên người dùng --}}
                        <div class="col-md-8">
                            <label for="name" class="form-label">tên người dùng</label>
                            <input type="text" name="name" id="name" class="form-control"
                                value="{{ request('name') }}" placeholder="tên...">
                        </div>

                        <div class="col-md-4 d-flex justify-content-end gap-1 align-items-end">
                            <div class="col">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fas fa-search me-1"></i> Tìm kiếm
                                </button>
                            </div>
                            <div class="col">
                                <a href="{{ route('admin.products.show', $product) }}#comments"
                                    class="btn btn-warning w-100">
                                    <i class="fas fa-sync me-1"></i> Làm mới
                                </a>
                            </div>
                        </div>
                    </form>
                    <table class="table mb-0 text-nowrap varient-table align-middle fs-3">
                        <thead>
                            <tr>
                                <th class="px-0 text-muted">STT</th>
                                <th class="px-0 text-muted">Người bình luận</th>
                                <th class="px-0 text-muted">Nội dung</th>
                                <th class="px-0 text-muted">Trạng thái</th>
                                <th class="px-0 text-muted">Ngày bình luận</th>
                                <th class="px-0 text-muted text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($comments as $index => $comment)
                                <tr>
                                    <td class="px-0">{{ $index + 1 }}</td>
                                    <td class="px-0">{{ $comment->user->name }}</td>
                                    <td class="px-0">{{ $comment->content }}</td>
                                    <td class="px-0">
                                        <span
                                            class="badge {{ $comment->status == 'hiển thị' ? 'bg-success' : ($comment->status == 'ẩn' ? 'bg-secondary' : 'bg-warning') }}">
                                            {{ $comment->status }}
                                        </span>
                                    </td>
                                    <td class="px-0">{{ $comment->created_at }}</td>

                                    <td class="px-0 text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button"
                                                id="dropdownMenuButton{{ $comment->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="dropdownMenuButton{{ $comment->id }}">
                                                <li>
                                                    @if ($comment->status !== 'hiển thị')
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.comments.approve', $comment) }}">
                                                            Xem biến thể
                                                        </a>
                                                    @endif

                                                    @if ($comment->status === 'hiển thị')
                                                        <a class="dropdown-item"
                                                            href="{{ route('admin.products.variants.index', $product) }}">
                                                            Xem biến thể
                                                        </a>
                                                    @endif
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.comments.destroy', $comment->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn bỏ sản phẩm này vào thùng rác không?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="dropdown-item text-danger" type="submit">
                                                            Xóa bình luận
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Không có bình luận
                                        nào.</td>
                                </tr>
                            @endforelse

                        </tbody>
                    </table>
                    <div class="mt-3">
                        {{ $comments->withQueryString()->fragment('comments')->links() }}

                    </div>
                </div>
                <div class="tab-pane fade" id="variants" role="tabpanel">
                    <div class="table-responsive">

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
                                            <img src="{{ asset('storage/' . $variant->image) }}" width="60"
                                                class="rounded" alt="Ảnh biến thể">
                                        </td>
                                        <td>{{ number_format($variant->price, 0) }} đ</td>
                                        <td>{{ $variant->quantity }}</td>
                                        <td>
                                            <span class="badge {{ $variant->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                                {{ $variant->status == 1 ? 'Đang bán' : 'Dừng bán' }}
                                            </span>
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('admin.products.variants.edit', [$variant->product, $variant]) }}"
                                                class="btn btn-warning btn-sm">
                                                <i class="fa-solid fa-pen"></i>
                                            </a>
                                            <form
                                                action="{{ route('admin.products.variants.destroy', [$variant->product, $variant]) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm btn-confirm"
                                                    title="Xóa"
                                                    data-confirm-message="Bạn có chắc chắn muốn bỏ biến thể này vào thùng rác không?">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                                @if ($variants->count() == 0)
                                    <tr>
                                        <td colspan="8" class="text-center text-muted">Không có biến thể nào phù hợp
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                        <div class="mt-3">
                            {{ $variants->withQueryString()->fragment('variants')->links() }}

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // console.log("hi");
        document.addEventListener('DOMContentLoaded', function() {
            // Nếu có hash (ví dụ #comments) thì kích hoạt tab đó
            let hash = window.location.hash;
            console.log('Hash:', hash);
            if (hash) {
                let tabTrigger = document.querySelector(`a[data-bs-toggle="tab"][href="${hash}"]`);
                if (tabTrigger) {
                    new bootstrap.Tab(tabTrigger).show();
                }
            }

            // Khi người dùng bấm tab, cập nhật lại hash trong URL (nhưng không reload)
            let tabLinks = document.querySelectorAll('a[data-bs-toggle="tab"]');
            tabLinks.forEach(function(tabLink) {
                tabLink.addEventListener('shown.bs.tab', function(e) {
                    history.replaceState(null, null, e.target.getAttribute('href'));
                });
            });
        });
    </script>
@endpush
