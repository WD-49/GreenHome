@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Thêm Banner</h1>

    {{-- Hiển thị lỗi chung --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Tên banner</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}">
            @error('name')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-select">
                <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Hiển thị</option>
                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Ẩn</option>
            </select>
            @error('status')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Hình ảnh</label>
            <input type="file" name="img" class="form-control">
            @error('img')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Lưu</button>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
