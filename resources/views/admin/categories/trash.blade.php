@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Thùng rác Danh mục</h1>


        <!-- Form tìm kiếm -->
            <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Lọc danh mục</h5>
    </div>
    <div class="card">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.categories.trash') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Tên danh mục</label>
                    <input type="text" name="search" id="search" class="form-control" placeholder="Nhập tên danh mục"
                        value="{{ request('search') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Tìm kiếm
                    </button>
                    <a href="{{ route('admin.categories.trash') }}" class="btn btn-warning w-100">
                        <i class="fas fa-sync-alt me-1"></i> Làm mới
                    </a>
                </div>
            </form>
        </div>
    </div>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tên</th>
                    <th>Mô tả</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{!! $category->description !!}</td>
                        <td>
                            <!-- Khôi phục -->
                            <form action="{{ route('admin.categories.restore', $category->slug) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </button>
                            </form>

                            <!-- Xóa vĩnh viễn -->
                            <form action="{{ route('admin.categories.forceDelete', $category->slug) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-confirm" title="xóa vĩnh viễn"
                                    data-confirm-message="Bạn có chắc chắn muốn xóa vĩnh viễn danh mục này không?"><i
                                        class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">Không có danh mục nào trong thùng rác.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{ $categories->links() }}

        <!-- Nút quay lại -->
        <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary mt-3">
            <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
        </a>
    </div>
    
@endsection
