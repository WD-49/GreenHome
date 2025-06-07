@extends('layouts.admin')

@section('content')
    <h2 class="text-center">Thêm bài viết mới</h2>

    <div class="mt-4 bg-white shadow-sm rounded p-4">
        <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
            @csrf

            {{-- Tiêu đề --}}
            <div class="mb-3">
                <label for="title" class="form-label">Tiêu đề</label>
                <input type="text" name="title" id="title" class="form-control @error('title') is-invalid @enderror" 
                    value="{{ old('title') }}">
                @error('title')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Slug --}}
            <div class="mb-3">
                <label for="slug" class="form-label">Slug (tự động nếu để trống)</label>
                <input type="text" name="slug" id="slug" class="form-control @error('slug') is-invalid @enderror" 
                    value="{{ old('slug') }}">
                @error('slug')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Tóm tắt --}}
            <div class="mb-3">
                <label for="summary" class="form-label">Tóm tắt</label>
                <textarea name="summary" id="summary" class="form-control @error('summary') is-invalid @enderror" 
                    rows="3">{{ old('summary') }}</textarea>
                @error('summary')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Nội dung --}}
            <div class="mb-3">
                <label for="content" class="form-label">Nội dung</label>
                <textarea name="content" class="form-control @error('content') is-invalid @enderror" 
                    rows="6">{{ old('content') }}</textarea>
                @error('content')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Thumbnail --}}
            <div class="mb-3">
                <label for="thumbnail" class="form-label">Ảnh đại diện</label>
                <input type="file" name="thumbnail" id="thumbnail" 
                    class="form-control @error('thumbnail') is-invalid @enderror">
                @error('thumbnail')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Trạng thái --}}
            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Hiển thị</option>
                    <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Ẩn</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Thể loại --}}
            <div class="mb-3">
                <label for="blog_category_id" class="form-label">Danh mục</label>
                <select name="blog_category_id" id="blog_category_id" 
                    class="form-select @error('blog_category_id') is-invalid @enderror">
                    <option value="">-- Chọn thể loại --</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" 
                            {{ old('blog_category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('blog_category_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            {{-- Submit --}}
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-save me-1"></i> Lưu bài viết
                </button>
            </div>
        </form>
    </div>
<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    let editor;
    ClassicEditor
        .create(document.querySelector('textarea[name="content"]'))
        .then(newEditor => {
            editor = newEditor;
        })
        .catch(error => {
            console.error(error);
        });

    // Cập nhật nội dung editor về textarea khi submit
    document.querySelector('form').addEventListener('submit', function () {
        document.querySelector('textarea[name="content"]').value = editor.getData();
    });
</script>
@endsection
