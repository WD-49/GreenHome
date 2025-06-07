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
                    <div class="col-md-5">
                        <label for="name" class="form-label">Tên danh mục</label>
                        <input type="text" name="name[]" class="form-control form-control-lg" placeholder="Tên danh mục">
                    </div>
                    <div class="col-md-5">
                        <label for="description" class="form-label">Mô tả</label>
                        <input type="text" name="description[]" class="form-control form-control-lg" placeholder="Mô tả">
                    </div>
                    <div class="col-md-2 text-center">
                        <button type="button" class="btn btn-danger btn-remove" onclick="removeCategory(this)" title="Xóa danh mục"></button>
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
        /* Cải thiện thiết kế nút remove */
        .category-row {
            display: flex; /* Dùng flexbox để các phần tử nằm ngang */
            align-items: center; /* Căn giữa theo chiều dọc */
            justify-content: space-between; /* Căn đều giữa các phần tử */
            padding: 10px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .category-row input {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px;
            font-size: 16px;
            width: 100%;
            box-sizing: border-box;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .category-row input:focus {
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.5);
        }

        /* Nút remove */
        .category-row .btn-remove {
            font-size: 16px;
            width: 40px;
            height: 40px;
            padding: 0;
            margin-top: 30px; /* Đảm bảo nút có khoảng cách với ô input */
            border-radius: 8px; /* Bo góc để trông mềm mại */
            background-color: #e74c3c; /* Màu đỏ nhẹ */
            border: none;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        /* Hiệu ứng hover */
        .category-row .btn-remove:hover {
            background-color: #c0392b; /* Đổi màu khi hover */
            transform: scale(1.1); /* Tăng kích thước khi hover để làm nổi bật */
        }

        .category-row .btn-remove::before {
            content: '-'; /* Sử dụng dấu trừ thay vì icon */
            font-size: 22px;
            font-weight: bold;
        }

        /* Nút "Thêm danh mục" */
        #add-category {
            font-weight: bold;
            padding: 12px 25px;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        #add-category:hover {
            background-color: #28a745;
            color: white;
        }

        /* Đảm bảo responsive */
        @media (max-width: 768px) {
            .category-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .col-md-5 {
                width: 100%;
                margin-bottom: 10px;
            }

            .btn-remove {
                margin-top: 10px;
                align-self: flex-start; /* Căn nút remove lên trên cùng */
            }

            #add-category {
                width: 100%;
                font-size: 16px;
            }
        }
    </style>

    <script>
        document.getElementById('add-category').addEventListener('click', function () {
            var container = document.getElementById('categories-container');
            var newRow = document.createElement('div');
            newRow.classList.add('row', 'mb-3', 'category-row', 'align-items-center');
            newRow.innerHTML = `
                <div class="col-md-5">
                    <label class="form-label">Tên danh mục</label>
                    <input type="text" name="name[]" class="form-control form-control-lg" placeholder="Tên danh mục">
                </div>
                <div class="col-md-5">
                    <label class="form-label">Mô tả</label>
                    <input type="text" name="description[]" class="form-control form-control-lg" placeholder="Mô tả">
                </div>
                <div class="col-md-2 text-center">
                    <button type="button" class="btn btn-danger btn-remove" onclick="removeCategory(this)" title="Xóa danh mục"></button>
                </div>
            `;
            container.appendChild(newRow);
        });

        function removeCategory(button) {
            var categoryRow = button.closest('.category-row');
            categoryRow.remove();
        }
    </script>
@endsection
