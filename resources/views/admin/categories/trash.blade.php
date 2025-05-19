@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Thùng rác Danh mục</h1>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <!-- Form tìm kiếm -->
        <form method="GET" action="{{ route('admin.categories.trash') }}" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Tìm kiếm theo tên"
                    value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit">
                    <i class="fa-solid fa-magnifying-glass"></i> Tìm kiếm
                </button>
            </div>
        </form>

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
                        <td>{{ $category->description }}</td>
                        <td>
                            <!-- Khôi phục -->
                            <form action="{{ route('admin.categories.restore', $category->id) }}" method="POST"
                                class="d-inline">
                                @csrf
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </button>
                            </form>

                            <!-- Xóa vĩnh viễn -->
                            <form action="{{ route('admin.categories.forceDelete', $category->id) }}" method="POST"
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
