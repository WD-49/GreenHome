@extends('layouts.admin')

@section('content')
    <h2 class="text-center mb-4">Chỉnh sửa bài viết</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="{{route('admin.blogs.update', $id = $blog->id)}}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Tiêu đề --}}
                <div class="mb-3">
                    <label for="title" class="form-label">Tiêu đề</label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
                        value="{{ old('title', $blog->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Slug --}}
                <div class="mb-3">
                    <label for="slug" class="form-label">Slug</label>
                    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug"
                        value="{{ old('slug', $blog->slug) }}" required>
                    @error('slug')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Tóm tắt --}}
                <div class="mb-3">
                    <label for="summary" class="form-label">Tóm tắt</label>
                    <textarea class="form-control @error('summary') is-invalid @enderror" id="summary" name="summary"
                        rows="3" required>{{ old('summary', $blog->summary) }}</textarea>
                    @error('summary')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Nội dung --}}
                <div class="mb-3">
                    <label for="content" class="form-label">Nội dung</label>
                    <textarea class="form-control @error('content') is-invalid @enderror" id="content" name="content"
                        rows="8" required>{{ old('content', $blog->content) }}</textarea>
                    @error('content')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Thumbnail --}}
                <div class="mb-3">
                    <label for="thumbnail" class="form-label">Ảnh đại diện (Thumbnail)</label>
                    @if ($blog->thumbnail)
                        <div class="mb-2">
                            <img src="{{ asset('storage/' . $blog->thumbnail) }}" alt="Thumbnail" style="max-height: 120px;">
                        </div>
                    @endif
                    <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" id="thumbnail"
                        name="thumbnail">
                    @error('thumbnail')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Trạng thái --}}
                <div class="mb-3">
                    <label class="form-label d-block">Trạng thái</label>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="status1" value="1" {{ old('status', $blog->status) == 1 ? 'checked' : '' }}>
                        <label class="form-check-label" for="status1">Hiển thị</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="status" id="status0" value="0" {{ old('status', $blog->status) == 0 ? 'checked' : '' }}>
                        <label class="form-check-label" for="status0">Ẩn</label>
                    </div>
                    @error('status')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Thể loại --}}
                <div class="mb-3">
                    <label for="blog_category_id" class="form-label">Danh mục</label>
                    <select name="blog_category_id" id="blog_category_id"
                        class="form-select @error('blog_category_id') is-invalid @enderror" required>
                        <option value="">-- Chọn thể loại --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" {{ old('blog_category_id', $blog->blog_category_id) == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('blog_category_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save me-1"></i> Cập nhật
                </button>
                <a href="" class="btn btn-secondary ms-2">Hủy</a>
            </form>
        </div>
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