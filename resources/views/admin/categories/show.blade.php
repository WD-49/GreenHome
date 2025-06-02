@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Chi tiết danh mục: {{ $category->name }}</h1>

        <div class="card">
            <div class="card-header">
                <h5>{{ $category->name }}</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tbody>
                        <tr>
                            <th>Tên danh mục</th>
                            <td>{{ $category->name }}</td>
                        </tr>
                        <tr>
                            <th>Mô tả</th>
                            <td>{{ $category->description }}</td>
                        </tr>
                        <tr>
                            <th>Slug</th>
                            <td>{{ $category->slug }}</td>
                        </tr>
                        <tr>
                            <th>Ngày tạo</th>
                            <td>{{ $category->created_at }}</td>
                        </tr>
                        <tr>
                            <th>Ngày cập nhật</th>
                            <td>{{ $category->updated_at }}</td>
                        </tr>
                        <tr>
                            <th>Ngày xóa</th>
                            <td>{{ $category->deleted_at ? $category->deleted_at : 'Chưa xóa' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer">
                <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
                </a>
            </div>
        </div>
    </div>
@endsection
