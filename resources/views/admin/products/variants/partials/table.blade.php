{{-- filepath: c:\laragon\www\GreenHome\resources\views\admin\products\variants\table.blade.php --}}
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
                    <td>{{ $index + 1 + ($variants->currentPage() - 1) * $variants->perPage() }}</td>
                    <td>{{ $variant->sku }}</td>
                    <td>
                        @if ($variant->attribute_name)
                            {{ $variant->attribute_name }}
                        @else
                            Sản phẩm không có thuộc tính
                        @endif
                    </td>
                    <td>
                        @if ($variant->image)
                            <img src="{{ asset('storage/' . $variant->image) }}" width="60" class="rounded"
                                alt="Ảnh biến thể">
                        @else
                            <span class="text-muted">Không có ảnh</span>
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
                                id="dropdownMenuButton{{ $variant->id }}" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end"
                                aria-labelledby="dropdownMenuButton{{ $variant->id }}">
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
