@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Chỉnh sửa Banner</h1>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                {{-- Tên --}}
                <div class="mb-3">
                    <label class="form-label">Tên banner</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $banner->name) }}">
                    @error('name')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Liên kết --}}
                <div class="mb-3">
                    <label class="form-label">Liên kết</label>
                    <input type="text" name="link" class="form-control" value="{{ old('link', $banner->link) }}">
                    @error('link')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Mức ưu tiên --}}
                <div class="mb-3">
                    <label class="form-label">Mức ưu tiên</label>
                    <input type="number" name="priority" class="form-control" value="{{ old('priority', $banner->priority) }}">
                    @error('priority')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Loại --}}
                <div class="mb-3">
                    <label class="form-label">Loại banner</label>
                    <select name="type" class="form-select">
                        <option value="slider" {{ old('type', $banner->type) == 'slider' ? 'selected' : '' }}>Slide trang chủ</option>
                        <option value="category_banner" {{ old('type', $banner->type) == 'category_banner' ? 'selected' : '' }}>Banner danh mục</option>
                       
                    </select>
                    @error('type')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Danh mục --}}
                <div class="mb-3">
                    <label class="form-label">Áp dụng cho danh mục</label>
                    <select name="category_id" class="form-select">
                        <option value="">-- Áp dụng cho tất cả danh mục --</option>
                        @foreach ($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $banner->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('category_id')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="col-md-6">
                {{-- Trạng thái --}}
                <div class="mb-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="1" {{ old('status', $banner->status) == '1' ? 'selected' : '' }}>Hiển thị</option>
                        <option value="0" {{ old('status', $banner->status) == '0' ? 'selected' : '' }}>Ẩn</option>
                    </select>
                    @error('status')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Hình ảnh hiện tại --}}
                <div class="mb-3">
                    <label class="form-label">Hình ảnh hiện tại</label><br>
                    @if ($banner->img)
                        <img src="{{ asset($banner->img) }}" width="150" class="rounded shadow-sm" alt="Ảnh hiện tại">
                    @else
                        <span class="text-muted">Chưa có hình ảnh</span>
                    @endif
                </div>

                {{-- Upload ảnh --}}
                <div class="mb-3">
                    <label class="form-label">Thay đổi hình ảnh</label>
                    <input type="file" name="img" class="form-control">
                    @error('img')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Mô tả --}}
        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control ckeditor" id="editor" rows="6">{{ old('description', $banner->description) }}</textarea>
            @error('description')
                <div class="text-danger mt-1">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#editor'))
        .catch(error => {
            console.error(error);
        });
</script>
@endsection
