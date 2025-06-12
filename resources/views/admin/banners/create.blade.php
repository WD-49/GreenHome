@extends('layouts.admin')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Thêm Banner</h1>

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
            <div class="row mb-3 banner-row align-items-center">
                <div class="col-md-6">
                    <label class="form-label">Tên banner</label>
                    <input type="text" name="name" class="form-control form-control-lg"
                        value="{{ old('name') }}" placeholder="Tên banner">
                    @error('name')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label">Liên kết</label>
                    <input type="text" name="link" class="form-control form-control-lg"
                        value="{{ old('link') }}" placeholder="http://...">
                    @error('link')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">Mức ưu tiên</label>
                    <input type="number" name="priority" class="form-control form-control-lg"
                        value="{{ old('priority', 0) }}" placeholder="Ví dụ: 1">
                    @error('priority')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mt-3">
                    <label class="form-label">Trạng thái</label>
                    <select name="status" class="form-select form-select-lg">
                        <option value="1" {{ old('status') == '1' ? 'selected' : '' }}>Hiển thị</option>
                        <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Ẩn</option>
                    </select>
                    @error('status')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mt-3">
                    <label class="form-label">Hình ảnh</label>
                    <input type="file" name="img" class="form-control form-control-lg">
                    @error('img')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-12 mt-3">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description" class="form-control ckeditor" placeholder="Mô tả">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary btn-lg me-2">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu
                </button>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary btn-lg">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </form>
    </div>

    <style>
        .banner-row {
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .banner-row input,
        .banner-row textarea,
        .banner-row select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .banner-row input:focus,
        .banner-row textarea:focus,
        .banner-row select:focus {
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }

        .form-label {
            font-weight: bold;
        }

        @media (max-width: 768px) {
            .banner-row {
                padding: 15px;
            }

            .form-control,
            .form-select {
                font-size: 14px;
                padding: 10px;
            }

            .btn-lg {
                font-size: 16px;
                padding: 10px 20px;
            }
        }
    </style>
@endsection

@section('scripts')
    <!-- CKEditor CDN -->
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        function initializeEditors() {
            document.querySelectorAll('.ckeditor').forEach((textarea) => {
                if (!textarea.classList.contains('ckeditor-loaded')) {
                    ClassicEditor.create(textarea)
                        .then(editor => {
                            textarea.classList.add('ckeditor-loaded');
                        })
                        .catch(error => {
                            console.error(error);
                        });
                }
            });
        }

        window.addEventListener('DOMContentLoaded', initializeEditors);
    </script>
@endsection
