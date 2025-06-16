@extends('layouts.admin')

@section('content')
    <h2 class="text-center mb-4">Chỉnh sửa bài viết</h2>

    <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            {{-- Cột trái: nội dung bài viết --}}
            <div class="col-md-8">
                <div class="card mb-3">
                    <div class="card-body">
                        {{-- Tiêu đề --}}
                        <label for="title" class="form-label fw-bold">Tiêu đề</label>
                        <input type="text" name="title" id="title"
                               class="form-control mb-3 @error('title') is-invalid @enderror"
                               value="{{ old('title', $blog->title) }}">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        {{-- Slug --}}
                        <label for="slug" class="form-label fw-bold">Slug</label>
                        <input type="text" name="slug" id="slug"
                               class="form-control mb-3 @error('slug') is-invalid @enderror"
                               value="{{ old('slug', $blog->slug) }}">
                        @error('slug')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror

                        {{-- Tóm tắt --}}
                        <label for="summary" class="form-label fw-bold">Tóm tắt</label>
                        <input type="text" name="summary" id="summary"
                               class="form-control @error('summary') is-invalid @enderror"
                               value="{{ old('summary', $blog->summary) }}">
                        @error('summary')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Nội dung --}}
                <div class="card">
                    <div class="card-body">
                        <label for="content" class="form-label fw-bold">Nội dung</label>
                        <textarea name="content" rows="12"
                                  class="form-control @error('content') is-invalid @enderror">{{ old('content', $blog->content) }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Cột phải: thông tin phụ --}}
            <div class="col-md-4">
                {{-- Trạng thái --}}
                <div class="card mb-3">
                    <div class="card-header fw-bold">Đăng bài</div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="1" {{ old('status', $blog->status) == 1 ? 'selected' : '' }}>Hiển thị</option>
                                <option value="0" {{ old('status', $blog->status) == 0 ? 'selected' : '' }}>Bản nháp</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa fa-save me-1"></i> Cập nhật bài viết
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Danh mục --}}
                <div class="card mb-3">
                    <div class="card-header fw-bold">Danh mục</div>
                    <div class="card-body">
                        <select name="blog_category_id" class="form-select @error('blog_category_id') is-invalid @enderror">
                            <option value="">-- Chọn thể loại --</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('blog_category_id', $blog->blog_category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('blog_category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Thumbnail --}}
                <div class="card">
                    <div class="card-header fw-bold">Ảnh bìa</div>
                    <div class="card-body">
                        <div class="mb-2">
    <img id="thumbnail-preview"
         src="{{ $blog->thumbnail ? asset('storage/' . $blog->thumbnail) : '' }}"
         alt="Thumbnail"
         style="max-height: 150px; {{ $blog->thumbnail ? '' : 'display: none;' }}">
</div>
                        <input type="file" name="thumbnail"
                               class="form-control @error('thumbnail') is-invalid @enderror">
                        @error('thumbnail')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </form>

    {{-- CKEditor --}}
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

        // Cập nhật dữ liệu từ CKEditor về textarea khi submit
        document.querySelector('form').addEventListener('submit', function () {
            document.querySelector('textarea[name="content"]').value = editor.getData();
        });

          // Xử lý xem trước ảnh khi chọn ảnh mới
    const thumbnailInput = document.querySelector('input[name="thumbnail"]');
    const previewImage = document.querySelector('#thumbnail-preview');

    if (thumbnailInput && previewImage) {
        thumbnailInput.addEventListener('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function (event) {
                    previewImage.src = event.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
    </script>
@endsection
