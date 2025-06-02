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
            <p><strong>Số lượng sản phẩm:</strong> {{ $products->total() }}</p>
        </div>
    </div>

    <h3 class="mb-3">Danh sách sản phẩm thuộc thương hiệu</h3>

    @if($products->isEmpty())
        <div class="alert alert-info">Chưa có sản phẩm nào thuộc thương hiệu này.</div>
    @else
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
                            <td class="px-0">{{ $product->category->name ?? 'N/A' }}</td>
                            <td class="px-0">
                                <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/no-image.png') }}"
                                     width="60" class="rounded" alt="Hình ảnh sản phẩm">
                            </td>
                            <td class="px-0">{{ $product->quantity }}</td>
                            <td class="px-0">
                                <span class="badge {{ $product->status == 1 ? 'bg-success' : 'bg-danger' }}">
                                    {{ $product->status == 1 ? 'Đang bán' : 'Dừng bán' }}
                                </span>
                            </td>
                            <td class="px-0 text-end">
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
                                               href="{{ route('admin.products.variants.index', $product) }}">
                                                Xem biến thể
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                               href="{{ route('admin.products.show', $product->id) }}">
                                                Xem chi tiết
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item"
                                               href="{{ route('admin.products.edit', $product->id) }}">
                                                Chỉnh sửa
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.products.destroy', $product->id) }}"
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
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-center mt-3">
            {{ $products->links() }}
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
        </a>
    </div>
@endsection
