@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Sửa Danh mục</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.categories.update', $category->slug) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Tên danh mục</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="{{ old('name', $category->name) }}" required>
            </div>

            <div class="mb-3">
                <label for="slug" class="form-label">Slug</label>
                <input type="text" class="form-control" id="slug" name="slug"
                    value="{{ old('slug', $category->slug) }}" required>
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control" id="description" name="description">{{ old('description', $category->description) }}</textarea>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" class="form-control" required>
                    <option value="1" {{ old('status', $category->status) == 1 ? 'selected' : '' }}>Hiện</option>
                    <option value="0" {{ old('status', $category->status) == 0 ? 'selected' : '' }}>Ẩn</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-pen-to-square"></i> Cập nhật
            </button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
        </form>
    </div>

    <!-- CKEditor 5 CDN -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>

    <script>
        // Khởi tạo CKEditor
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });

        // Tự động tạo slug từ name
        document.getElementById('name').addEventListener('input', function() {
            let name = this.value;
            let slug = name.toLowerCase()
                .normalize("NFD")
                .replace(/[\u0300-\u036f]/g, "") // Xóa dấu tiếng Việt
                .replace(/\s+/g, '-') // Khoảng trắng -> gạch ngang
                .replace(/[^a-z0-9\-]/g, '') // Loại bỏ ký tự đặc biệt
                .replace(/--+/g, '-') // Loại bỏ dấu -- liên tiếp
                .replace(/^-+|-+$/g, ''); // Xóa - đầu/cuối
            document.getElementById('slug').value = slug;
        });
    </script>
@endsection
