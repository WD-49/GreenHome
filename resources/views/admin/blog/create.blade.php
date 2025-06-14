@extends('layouts.admin')

@section('content')
    <h2 class="text-center">Thêm bài viết mới</h2>

    <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row mt-4">
            {{-- Cột trái: Nội dung bài viết --}}
            <div class="col-md-8">
{{-- Tiêu đề + Tóm tắt --}}
<div class="card mb-3">
    <div class="card-body">
        {{-- Tiêu đề --}}
        <label for="title" class="form-label fw-bold">Tiêu đề</label>
        <input type="text" name="title" id="title"
            class="form-control mb-3 @error('title') is-invalid @enderror"
            value="{{ old('title') }}">
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror

        {{-- Tóm tắt --}}
        <label for="summary" class="form-label fw-bold">Tóm tắt</label>
        <input type="text" name="summary" id="summary"
            class="form-control @error('summary') is-invalid @enderror"
            value="{{ old('summary') }}">
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
                            class="form-control @error('content') is-invalid @enderror">{{ old('content') }}</textarea>
                        @error('content')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- Cột phải: Cài đặt và thông tin phụ --}}
            <div class="col-md-4">
                {{-- Trạng thái và submit --}}
                <div class="card mb-3">
                    <div class="card-header fw-bold">Đăng bài</div>
                    <div class="card-body">
                        {{-- Trạng thái --}}
                        <div class="mb-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select name="status" id="status" class="form-select @error('status') is-invalid @enderror">
                                <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>Hiển thị</option>
                                <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>Bản nháp</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fa-solid fa-save me-1"></i> Lưu bài viết
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
                                <option value="{{ $category->id }}" {{ old('blog_category_id') == $category->id ? 'selected' : '' }}>
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
                        <input type="file" name="thumbnail" class="form-control @error('thumbnail') is-invalid @enderror">
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
            .create(document.querySelector('textarea[name="content"]'), {
                height: '500px'
            })
            .then(newEditor => {
                editor = newEditor;
            })
            .catch(error => {
                console.error(error);
            });

        document.querySelector('form').addEventListener('submit', function () {
            document.querySelector('textarea[name="content"]').value = editor.getData();
        });
    </script>
@endsection