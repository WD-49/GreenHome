@extends('layouts.admin')

@section('title')
    Danh sách thương hiệu
@endsection

@section('content')
    <h1 class="text-center mb-4">Danh sách thương hiệu</h1>

    {{-- Form tìm kiếm --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-search me-2"></i> Tìm kiếm thương hiệu</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.brands.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label for="keyword" class="form-label">Từ khóa</label>
                    <input type="text" name="keyword" id="keyword" value="{{ request('keyword') }}"
                           class="form-control" placeholder="Nhập tên thương hiệu...">
                </div>
                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Tìm
                    </button>
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-warning w-100">
                        <i class="fas fa-sync me-1"></i> Làm mới
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Nút thao tác --}}
    <div class="d-flex flex-wrap justify-content-center align-items-center gap-2 mb-4">
        <a href="{{ route('admin.brands.create') }}" class="btn btn-success">
            <i class="fa-solid fa-square-plus me-1"></i> Thêm mới
        </a>

        <a href="{{ route('admin.brands.trash') }}" class="btn btn-secondary">
            <i class="fa-solid fa-dumpster me-1"></i> Thùng rác
        </a>

        <form id="bulkActionForm" method="POST" class="d-flex align-items-center gap-2">
            @csrf
            <input type="hidden" name="brand_ids" id="selectedBrandIds">

            <button type="submit" formaction="{{ route('admin.brands.bulkSoftDelete') }}"
                    class="btn btn-danger btn-bulk-delete"
                    onclick="return confirm('Bạn có chắc chắn muốn xóa mềm các mục đã chọn?')">
                <i class="fa-solid fa-trash me-1"></i> Xóa mềm mục chọn
            </button>
        </form>
    </div>

    {{-- Danh sách --}}
    <div class="card shadow-sm border rounded">
        <div class="table-responsive">
            <table class="table align-middle text-nowrap mb-0">
                <thead class="table-light">
                    <tr>
                        <th>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="select-all">
                            </div>
                        </th>
                        <th scope="col">Tên thương hiệu</th>
                        <th scope="col">Slug</th>
                        <th scope="col">Mô tả</th>
                        <th class="text-nowrap" style="width: 1%;">Hành động</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse ($brands as $brand)
                        <tr>
                            <td>
                                <div class="form-check mb-0">
                                    <input class="form-check-input brand-checkbox" type="checkbox" value="{{ $brand->id }}">
                                </div>
                            </td>
                            <td><h6 class="mb-0 fs-6">{{ $brand->name }}</h6></td>
                            <td><span class="badge bg-info">{{ $brand->slug }}</span></td>
                            <td><div style="max-width: 300px;">{!! $brand->description !!}</div></td>
                            <td class="text-nowrap" style="width: 1%;">
                                <div class="dropdown text-end">
                                    <button class="btn btn-sm btn-light" type="button"
                                            id="dropdownMenuButton{{ $brand->id }}" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        <i class="bi bi-three-dots-vertical fs-5"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton{{ $brand->id }}">
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.brands.show', $brand->slug) }}">
                                                <i class="fa-solid fa-eye me-2"></i> Xem chi tiết
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ route('admin.brands.edit', $brand->slug) }}">
                                                <i class="fas fa-pen me-2"></i> Chỉnh sửa
                                            </a>
                                        </li>
                                        <li>
                                            <form action="{{ route('admin.brands.destroy', $brand->slug) }}" method="POST"
                                                  onsubmit="return confirm('Bạn có chắc chắn muốn bỏ thương hiệu {{ $brand->name }} vào thùng rác không?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fa-solid fa-trash me-2"></i> Xóa
                                                </button>
                                            </form>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Không tìm thấy thương hiệu nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        @if ($brands->hasPages())
            <div class="card-footer d-flex justify-content-center">
                <nav>
                    <ul class="pagination m-0">
                        <li class="page-item {{ $brands->onFirstPage() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $brands->previousPageUrl() }}">«</a>
                        </li>
                        @for ($i = 1; $i <= $brands->lastPage(); $i++)
                            <li class="page-item {{ $brands->currentPage() == $i ? 'active' : '' }}">
                                <a class="page-link" href="{{ $brands->url($i) }}">{{ $i }}</a>
                            </li>
                        @endfor
                        <li class="page-item {{ !$brands->hasMorePages() ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $brands->nextPageUrl() }}">»</a>
                        </li>
                    </ul>
                </nav>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    // nút selectALl
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all');
        const checkboxes = document.querySelectorAll('.brand-checkbox');
        const form = document.getElementById('bulkActionForm');
        const hiddenInput = document.getElementById('selectedBrandIds');

        // Toggle select all
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
        });

        // On submit bulk action
        form.addEventListener('submit', function (e) {
            const selectedIds = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            if (selectedIds.length === 0) {
                alert('Vui lòng chọn ít nhất một thương hiệu.');
                e.preventDefault();
                return;
            }

            hiddenInput.value = selectedIds.join(',');
        });
    });
</script>
@endpush
