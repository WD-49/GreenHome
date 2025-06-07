@extends('layouts.admin')

@section('title')
    Danh sách thương hiệu
@endsection

@section('content')
    <h1 class="text-center mb-4">Danh sách thương hiệu</h1>

    {{-- Form tìm kiếm --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-search"></i> Tìm kiếm thương hiệu</h5>
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
    <div class="d-flex justify-content-center gap-2 mb-4">
        <a href="{{ route('admin.brands.create') }}" class="btn btn-success" title="Thêm thương hiệu">
            <i class="fa-solid fa-square-plus me-1"></i> Thêm mới
        </a>
        <a href="{{ route('admin.brands.trash') }}" class="btn btn-secondary" title="Thùng rác">
            <i class="fa-solid fa-dumpster me-1"></i> Thùng rác
        </a>
    </div>

    {{-- Bảng danh sách thương hiệu --}}
    <div class="card shadow-sm mb-4">
        <div class="table-responsive py-3 px-2">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;">ID</th>
                        <th style="width: 10%;">Tên thương hiệu</th>
                        <th style="width: 10%;">Slug</th>
                        <th style="width: 30%;">Mô tả</th>

                        <th style="width: 10%;">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($brands as $brand)
                        <tr>
                            <td>{{ $brand->id }}</td>
                            <td>{{ $brand->name }}</td>
                            <td>{{ $brand->slug }}</td>
                            <td>{!! $brand->description !!}</td>
                            


                            <td class="text-center">
                                <a href="{{ route('admin.brands.show', $brand->slug) }}" class="btn btn-info btn-sm"
                                    title="Xem chi tiết">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.brands.edit', $brand->slug) }}" class="btn btn-warning btn-sm"
                                    title="Chỉnh sửa">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.brands.destroy', $brand->slug) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-confirm" title="Xóa"
                                        data-confirm-message="Bạn có chắc chắn muốn bỏ thương hiệu '{{ $brand->name }}' vào thùng rác không?">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Không tìm thấy thương hiệu nào.</td>
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
        // Tránh gắn sự kiện nhiều lần bằng cách dùng once
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.btn-confirm').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    const message = button.getAttribute('data-confirm-message') ||
                        'Bạn có chắc chắn muốn xóa?';
                    if (!confirm(message)) {
                        e.preventDefault();
                    }
                }, {
                    once: true
                }); // <- chỉ gắn 1 lần duy nhất
            });
        });
    </script>
@endpush
