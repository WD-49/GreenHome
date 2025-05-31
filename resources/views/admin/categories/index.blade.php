@extends('layouts.admin')

@section('title')
    Danh sách danh mục
@endsection

@section('content')
    <h1 class="text-center">Quản lý Danh mục</h1>

    <!-- Bộ lọc -->
 <div class="card-header bg-primary text-white d-flex align-items-center" style="font-family: 'EB Garamond', serif; box-shadow: 0 4px 6px rgba(0,0,0,0.1); border-radius: 8px 8px 0 0; color: #fff;">
    <i class="fas fa-filter me-2" style="font-size: 1.3rem;"></i>
    <h5 class="mb-0 fw-semibold" style="letter-spacing: 0.05em; color: #fff;">Lọc Danh Mục</h5>
</div>



    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Tên Danh Mục</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Nhập tên danh mục"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Tìm kiếm
                    </button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-warning w-100">
                        <i class="fas fa-sync-alt me-1"></i> Làm mới
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Nút tạo mới & thùng rác -->
    <div class="col-12 d-flex justify-content-center gap-2 my-3">
        <a href="{{ route('admin.categories.create') }}" class="btn btn-success" title="Thêm danh mục">
            <i class="fa-solid fa-square-plus"></i>
        </a>
        <a href="{{ route('admin.categories.trash') }}" class="btn btn-warning" title="Thùng rác">
            <i class="fa-solid fa-dumpster"></i>
        </a>
    </div>

    <!-- Bảng danh sách -->
    <div class="card shadow-sm mb-4">
    <div class="table-responsive py-3">
        <table class="table table-striped table-hover">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>Tên danh mục</th>
                    <th>Mô tả</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->id }}</td>
                        <td>{{ $category->name }}</td>
                        <td>{!! $category->description !!}</td>
                        <td>
                            <!-- Xem: Link đến trang chi tiết danh mục -->
                            <a href="{{ route('admin.categories.show', $category->slug) }}" class="btn btn-info btn-sm"
                                title="Xem chi tiết">
                                <i class="fa-solid fa-eye"></i>
                            </a>

                            <!-- Sửa: Cập nhật đường dẫn để sử dụng slug -->
                            <a href="{{ route('admin.categories.edit', $category->slug) }}"
                                class="btn btn-warning btn-sm" title="Chỉnh sửa">
                                <i class="fa-solid fa-pen"></i>
                            </a>

                            <!-- Xóa: Cập nhật form để sử dụng slug -->
                            <form action="{{ route('admin.categories.destroy', $category->slug) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-confirm" title="xóa"
                                    data-confirm-message="Bạn có chắc chắn muốn bỏ danh mục này vào thùng rác không?"><i
                                        class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">Không tìm thấy danh mục nào.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Phân trang -->
    @if ($categories->lastPage() > 1)
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center mb-0">
                <li class="page-item {{ $categories->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $categories->previousPageUrl() }}">Previous</a>
                </li>

                @for ($i = 1; $i <= $categories->lastPage(); $i++)
                    <li class="page-item {{ $i == $categories->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $categories->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor

                <li class="page-item {{ !$categories->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $categories->nextPageUrl() }}">Next</a>
                </li>
            </ul>
        </nav>
    @endif
</div>

@endsection
