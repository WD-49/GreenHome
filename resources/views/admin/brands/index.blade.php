@extends('layouts.admin')

@section('title')
    Danh sách thương hiệu
@endsection

@section('content')
    <h1 class="text-center">Danh sách thương hiệu</h1>

    {{-- Thông báo thành công --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- Form tìm kiếm --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-search"></i> Tìm kiếm thương hiệu</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.brands.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label for="keyword" class="form-label">Từ khóa</label>
                    <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control"
                        placeholder="Nhập tên thương hiệu...">
                </div>
                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Tìm</button>
                    <a href="{{ route('admin.brands.index') }}" class="btn btn-warning w-100">
                        <i class="fas fa-sync me-1"></i> Làm mới
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Nút thao tác --}}
    <div class="col-12 d-flex align-items-center justify-content-center gap-2 mb-3">
        <a href="{{ route('admin.brands.create') }}" class="btn btn-success" title="Thêm thương hiệu">
            <i class="fa-solid fa-square-plus"></i>
        </a>
        <a href="{{ route('admin.brands.trashed') }}" class="btn btn-secondary" title="Thùng rác">
            <i class="fa-solid fa-dumpster"></i>
        </a>
    </div>

    {{-- Bảng danh sách thương hiệu --}}
    <div class="card shadow-sm mb-4">
        <div class="table-responsive py-4">
            <table class="table table-bordered table-hover">
                <thead class="thead-light">
                    <tr>
                        <th style="width: 20%;">Tên thương hiệu</th>
                        <th style="width: 50%;">Mô tả</th>
                        <th style="width: 30%;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($brands as $brand)
                        <tr>
                            <td>{{ $brand->name }}</td>
                            <td>{{ $brand->description }}</td>
                            <td>
                                <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-warning btn-sm" title="Chỉnh sửa">
                                    <i class="fas fa-pen"></i>
                                </a>
                               <form action="{{ route('admin.brands.destroy', $brand->id) }}" method="POST" class="d-inline delete-form">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm btn-delete">
        <i class="fas fa-trash-alt"></i> 
    </button>
</form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">Không tìm thấy thương hiệu nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Phân trang --}}
        @if ($brands->lastPage() > 1)
            <nav class="d-flex justify-content-center" aria-label="Page navigation example">
                <ul class="pagination">
                    <li class="page-item {{ $brands->onFirstPage() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $brands->previousPageUrl() }}" tabindex="-1">Previous</a>
                    </li>

                    @for ($i = 1; $i <= $brands->lastPage(); $i++)
                        <li class="page-item {{ $i == $brands->currentPage() ? 'active' : '' }}">
                            <a class="page-link" href="{{ $brands->url($i) }}">{{ $i }}</a>
                        </li>
                    @endfor

                    <li class="page-item {{ !$brands->hasMorePages() ? 'disabled' : '' }}">
                        <a class="page-link" href="{{ $brands->nextPageUrl() }}">Next</a>
                    </li>
                </ul>
            </nav>
        @endif
    </div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const deleteForms = document.querySelectorAll('.delete-form');

        deleteForms.forEach(form => {
            form.addEventListener('submit', function (e) {
                e.preventDefault();

              Swal.fire({
    title: 'Xác nhận',
    text: 'Bạn có chắc chắn muốn bỏ thương hiệu này vào thùng rác không?',
    icon: 'success', // icon màu xanh lá giống sản phẩm
    showCancelButton: true,
    confirmButtonText: 'Xác nhận',
    cancelButtonText: 'Huỷ',
    reverseButtons: true,
    customClass: {
        popup: 'rounded-3',
        title: 'fw-bold text-success',
        confirmButton: 'btn btn-success px-4',
        cancelButton: 'btn btn-dark px-4'
    },
    buttonsStyling: false
}).then((result) => {
    if (result.isConfirmed) {
        form.submit();
    }
});

            });
        });
    });
</script>
