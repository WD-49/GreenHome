@extends('layouts.admin')

@section('content')
<h3>Danh sách thương hiệu</h3>

<a href="{{ route('admin.brands.create') }}" class="btn btn-primary mb-2">Thêm thương hiệu</a>
<a href="{{ route('admin.brands.trashed') }}" class="btn btn-secondary mb-2">Thùng rác</a>

{{-- Form tìm kiếm --}}
<div class="d-flex justify-content-end mb-3">
    <form method="GET" action="{{ route('admin.brands.index') }}" class="d-flex" style="max-width: 400px;">
        <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control me-2" placeholder="Tìm theo tên...">
        <button class="btn btn-outline-primary">Tìm</button>
    </form>
</div>


@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Tên</th>
            <th>Mô tả</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @forelse($brands as $brand)
        <tr>
            <td>{{ $brand->name }}</td>
            <td>{{ $brand->description }}</td>
            <td>
                <a href="{{ route('admin.brands.edit', $brand) }}" class="btn btn-sm btn-warning">Sửa</a>
                <form action="{{ route('admin.brands.destroy', $brand) }}" method="POST" style="display:inline;" onsubmit="return confirm('Bạn chắc chắn muốn xóa?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Xóa</button>
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

{{-- Giữ lại keyword khi phân trang --}}
{{ $brands->appends(['keyword' => request('keyword')])->links() }}
@endsection
