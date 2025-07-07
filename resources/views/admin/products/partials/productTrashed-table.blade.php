<div class="table-responsive mt-3">
    <table class="table table-hover align-middle text-nowrap">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Tên sản phẩm</th>
                <th>Danh mục</th>
                <th>Thương hiệu</th>
                <th>Ảnh</th>
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
                    <td>{{ $product->category->name ?? '' }}</td>
                    <td>{{ $product->brand->name ?? '' }}</td>
                    <td>
                        @if ($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}" width="60" class="rounded"
                                alt="Ảnh sản phẩm">
                        @else
                            <span class="text-muted">Không có ảnh</span>
                        @endif
                    </td>
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
            @if ($products->count() == 0)
                <tr>
                    <td colspan="9" class="text-center text-muted">Không có sản phẩm nào trong
                        thùng rác</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>

<x-ajax-pagination :paginator="$products" />
