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

        <!-- Sửa lại route để sử dụng slug thay vì id -->
        <form method="POST" action="{{ route('admin.categories.update', $category->slug) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Tên danh mục</label>
                <input type="text" class="form-control" id="name" name="name"
                    value="{{ old('name', $category->name) }}" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control" id="description" name="description">{{ old('description', $category->description) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-pen-to-square"></i> Cập nhật
            </button>
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Quay lại
            </a>
        </form>
    </div>

    <!-- Thêm CKEditor -->
    {{-- <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script> --}}
    <script>
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });

        document.getElementById('name').addEventListener('input', function() {
            let name = this.value;

            // Hàm để chuyển đổi từ có dấu sang không dấu
            let slug = name.toLowerCase()
                .normalize("NFD") // Tách các ký tự dấu ra khỏi ký tự
                .replace(/[\u0300-\u036f]/g, "") // Loại bỏ các ký tự dấu
                .replace(/\s+/g, '-') // Thay thế khoảng trắng thành dấu gạch nối
                .replace(/[^a-z0-9\-]/g, '') // Loại bỏ các ký tự đặc biệt
                .replace(/^-+|-+$/g, ''); // Xóa dấu gạch nối ở đầu và cuối

            document.getElementById('slug').value = slug; // Đặt slug vào ô input
        });
    </script>
@endsection
