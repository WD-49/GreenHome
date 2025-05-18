@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Quản lý Danh mục</h1>

        <!-- Form tìm kiếm -->
        <form method="GET" action="{{ route('admin.categories.index') }}" class="mb-4">
            <div class="input-group">
                <input type="text" class="form-control" name="search" placeholder="Tìm kiếm theo tên"
                    value="{{ request('search') }}">
                <button class="btn btn-outline-secondary" type="submit">Tìm kiếm</button>
            </div>
        </form>

        <!-- Thêm mới danh mục -->
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mb-3">Thêm mới</a>

        <!-- Bảng danh sách categories -->
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Tên</th>
                    <th>Mô tả</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $category)
                    <tr>
                        <td>{{ $category->name }}</td>
                        <td>{{ $category->description }}</td>
                        <td>
                            <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-warning btn-sm">Sửa</a>

                            <!-- Xóa mềm -->
                            <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa danh mục này?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Xóa</button>
                            </form>

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Phân trang -->
        {{ $categories->links() }}

        <!-- Thùng rác -->
        <a href="{{ route('admin.categories.trash') }}" class="btn btn-secondary mt-3">Thùng rác</a>
    </div>
@endsection
