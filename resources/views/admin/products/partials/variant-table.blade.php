<table class="table table-striped w-100 nowrap">
    <thead>
        <tr>
            <th>#</th>
            <th>Mã SKU</th>
            <th>Thuộc tính</th>
            <th>Ảnh</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Trạng thái</th>
            <th></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($variants as $index => $variant)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $variant->sku }}</td>
                <td>
                    {{ $variant->attribute_name ?? 'Không có thuộc tính' }}
                </td>
                <td>
                    <img src="{{ asset('storage/' . $variant->image) }}" width="100px" class="rounded" alt="Ảnh biến thể">
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
                        <button class="btn btn-light btn-sm" type="button" id="dropdownMenuButton{{ $product->id }}"
                            data-bs-toggle="dropdown" aria-expanded="false">
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
                <td colspan="8" class="text-center text-muted">Không có biến thể nào phù hợp
                </td>
            </tr>
        @endif
    </tbody>
</table>
