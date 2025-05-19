@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Thùng rác</h1>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">← Quay lại danh sách</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

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
                            <form action="{{ route('admin.categories.restore', $category->id) }}" method="POST" 
                                class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn khôi phục danh mục này?')">
                                @csrf
                                <button type="submit" class="btn btn-success">Khôi phục</button>
                            </form>
                           

                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $categories->links() }}
    </div>
@endsection
