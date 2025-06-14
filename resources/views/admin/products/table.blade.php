<table class="table table-striped w-100 nowrap">
    <thead>
        <tr>
            <th>#</th>
            <th>Ảnh</th>
            <th>Tên sản phẩm</th>
            <th>Danh mục</th>
            <th>Thương hiệu</th>
            <th>Trạng thái</th>
            <th>Ngày nhập</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($products as $index => $product)
            <tr>
                <td>{{ $index + 1 }}</td>

                <td>
                    @if ($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" width="50" class="rounded"
                            alt="Hình ảnh sản phẩm">
                    @else
                        no image
                    @endif

                </td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category->name }}</td>
                {{-- Khi xóa vĩnh viễn thương hiệu --}}
                <td>{{ $product->brand?->name ?? 'Không có thương hiệu' }}</td>  
                <td>
                    <span class="badge {{ $product->status == 1 ? 'bg-success' : 'bg-danger' }}">
                        {{ $product->status == 1 ? 'Đang bán' : 'Dừng bán' }}
                    </span>
                </td>
                <td>{{ $product->date_of_entry->format('d/m/Y') }}</td>
                <td>
                    <div class="dropdown">
                        <button class="btn btn-light btn-sm me-2" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <span class="mdi mdi-settings-helper"></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item"
                                    href="{{ route('admin.products.variants.index', $product->id) }}">Chi tiết biến
                                    thể</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.products.show', $product->id) }}">Chi
                                    tiết</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.products.edit', $product->id) }}">Chỉnh
                                    sửa</a></li>
                            <li>
                                <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST"
                                    onsubmit="return confirm('Bạn có chắc chắn muốn bỏ sản phẩm này vào thùng rác không?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="dropdown-item text-danger" type="submit">Xóa sản phẩm</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

{{-- phân trang --}}
<x-ajax-pagination :paginator="$products" />
