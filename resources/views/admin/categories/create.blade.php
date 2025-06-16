@extends('layouts.admin')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Thêm Danh mục</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <div id="categories-container">
                <div class="row mb-3 category-row align-items-center">
                    <div class="col-md-12">
                        <label for="name" class="form-label">Tên danh mục</label>
                        <input type="text" name="name[]" class="form-control form-control-lg"
                            placeholder="Tên danh mục" value="{{ old('name.0') }}">
                    </div>
                    <div class="col-md-12">
                        <label for="description" class="form-label">Mô tả</label>
                        <textarea name="description[]" class="form-control ckeditor" placeholder="Mô tả">{{ old('description.0') }}</textarea>
                    </div>
                    <div class="col-md-12">
                        <label for="status" class="form-label">Trạng thái</label>
                        <select name="status[]" class="form-control" required>
                            <option value="1" {{ old('status.0', 1) == 1 ? 'selected' : '' }}>Hiện</option>
                            <option value="0" {{ old('status.0', 1) == 0 ? 'selected' : '' }}>Ẩn</option>
                        </select>
                    </div>
                    <div class="col-md-12 p-2">
                        <button type="button" class="btn btn-outline-danger btn-lg"
                            onclick="removeCategory(this)">
                            Xóa 
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-outline-success btn-lg" id="add-category">
                <i class="fas fa-plus"></i> Thêm danh mục
            </button>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu
                </button>
            </div>
        </form>
    </div>

    <style>
        /* ...giữ nguyên phần style... */
        .category-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .category-row input,
        .category-row textarea,
        .category-row select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .category-row input:focus,
        .category-row textarea:focus,
        .category-row select:focus {
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }
        /* ...giữ nguyên phần style còn lại... */
    </style>

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

        document.getElementById('add-category').addEventListener('click', function() {
            var container = document.getElementById('categories-container');
            var index = container.querySelectorAll('.category-row').length;
            var newRow = document.createElement('div');
            newRow.classList.add('row', 'mb-3', 'category-row', 'align-items-center');
            newRow.innerHTML = `
                <div class="col-md-12">
                    <label class="form-label">Tên danh mục</label>
                    <input type="text" name="name[]" class="form-control form-control-lg" placeholder="Tên danh mục">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description[]" class="form-control ckeditor" placeholder="Mô tả"></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Trạng thái</label>
                    <select name="status[]" class="form-control" required>
                        <option value="1" selected>Hiện</option>
                        <option value="0">Ẩn</option>
                    </select>
                </div>
                <div class="col-md-12 p-2">
                    <button type="button" class="btn btn-outline-danger btn-lg"
                        onclick="removeCategory(this)">
                        Xóa 
                    </button>
                </div>
            `;
            container.appendChild(newRow);
            initializeEditors();
        });

        function removeCategory(button) {
            var categoryRow = button.closest('.category-row');
            categoryRow.remove();
        }
    </script>
@endsection
