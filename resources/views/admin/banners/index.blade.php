@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Quản lý Banner</h1>

    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary mb-3">Thêm mới</a>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>Hình ảnh</th>
                <th>Tên</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($banners as $banner)
                <tr>
                  <td>
                        @if($banner->img)
                            <img src="{{ asset($banner->img) }}" alt="Banner" style="width: 120px; height: 80px; object-fit: cover; border-radius: 4px;">
                        @else
                            <span class="text-muted">Chưa có ảnh</span>
                        @endif
                    </td>

                    <td>{{ $banner->name }}</td>
                    <td>
                        @if($banner->status)
                            <span class="badge bg-success">Hiển thị</span>
                        @else
                            <span class="badge bg-secondary">Ẩn</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.banners.edit', $banner) }}" class="btn btn-warning btn-sm">Sửa</a>
                        <form action="{{ route('admin.banners.destroy', $banner) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Xóa banner này?')">Xóa</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $banners->links() }}
</div>
@endsection
