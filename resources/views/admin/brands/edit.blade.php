@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h1 class="mb-4">Sửa Thương hiệu</h1>

        <form method="POST" action="{{ route('admin.brands.update', $brand->slug) }}" novalidate>
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label for="name" class="form-label">Tên thương hiệu</label>
                <input type="text"
                       class="form-control @error('name') is-invalid @enderror"
                       id="name" name="name"
                       value="{{ old('name', $brand->name) }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label for="description" class="form-label">Mô tả</label>
                <textarea class="form-control @error('description') is-invalid @enderror"
                          id="description" name="description">{{ old('description', $brand->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-pen-to-square"></i> Cập nhật
            </button>
            <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
                <i class=""></i> Quay lại
            </a>
        </form>
    </div>

    <!-- CKEditor -->
    <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
    </script>
@endsection
