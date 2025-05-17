@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Sửa Banner</h1>

    <form action="{{ route('admin.banners.update', $banner) }}" method="POST" enctype="multipart/form-data">
        @csrf @method('PUT')

        <div class="mb-3">
            <label class="form-label">Tên banner</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $banner->name) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="1" {{ $banner->status ? 'selected' : '' }}>Hiển thị</option>
                <option value="0" {{ !$banner->status ? 'selected' : '' }}>Ẩn</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Hình ảnh hiện tại</label><br>
            @if($banner->img)
                <img src="{{ asset($banner->img) }}" alt="Banner" style="width: 120px;">
            @else
                <p class="text-muted">Chưa có ảnh</p>
            @endif
        </div>

        <div class="mb-3">
            <label class="form-label">Chọn ảnh mới (nếu muốn thay)</label>
            <input type="file" name="img" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
