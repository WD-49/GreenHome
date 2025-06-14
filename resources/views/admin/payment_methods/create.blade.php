@extends('layouts.admin')

@section('title', 'Thêm phương thức thanh toán')

@section('content')
    <div class="container mt-5">
        <h1 class="mb-4 text-center">Thêm phương thức thanh toán</h1>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.paymentMethods.store') }}">
            @csrf

            <div id="payment-methods-container">
                <div class="row mb-3 payment-method-row align-items-center">
                    <div class="col-md-12">
                        <label class="form-label">Tên phương thức</label>
                        <input type="text" name="name[]" class="form-control" placeholder="Tên phương thức">
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description[]" class="form-control ckeditor" placeholder="Mô tả"></textarea>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Trạng thái</label>
                        <select name="status[]" class="form-select">
                            <option value="1">Kích hoạt</option>
                            <option value="0">Tạm tắt</option>
                        </select>
                    </div>
                    <div class="col-md-12 p-2">
                        <button type="button" class="btn btn-outline-danger btn-lg" onclick="removePaymentMethod(this)">
                            Xóa
                        </button>
                    </div>
                </div>
            </div>

            <button type="button" class="btn btn-outline-success btn-lg" id="add-payment-method">
                <i class="fas fa-plus"></i> Thêm phương thức
            </button>

            <div class="d-flex justify-content-end mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu
                </button>
                <a href="{{ route('admin.paymentMethods.index') }}" class="btn btn-secondary btn-lg ms-2">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </form>
    </div>

    {{-- CKEditor --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
    <script>
        function initializeEditors() {
            document.querySelectorAll('.ckeditor').forEach(textarea => {
                if (!textarea.classList.contains('ckeditor-loaded')) {
                    ClassicEditor.create(textarea)
                        .then(editor => {
                            textarea.classList.add('ckeditor-loaded');
                        })
                        .catch(error => console.error(error));
                }
            });
        }

        window.addEventListener('DOMContentLoaded', initializeEditors);

        document.getElementById('add-payment-method').addEventListener('click', function () {
            var container = document.getElementById('payment-methods-container');
            var newRow = document.createElement('div');
            newRow.classList.add('row', 'mb-3', 'payment-method-row', 'align-items-center');
            newRow.innerHTML = `
                <div class="col-md-12">
                    <label class="form-label">Tên phương thức</label>
                    <input type="text" name="name[]" class="form-control" placeholder="Tên phương thức">
                </div>
                <div class="col-md-12">
                    <label class="form-label">Mô tả</label>
                    <textarea name="description[]" class="form-control ckeditor" placeholder="Mô tả"></textarea>
                </div>
                <div class="col-md-12">
                    <label class="form-label">Trạng thái</label>
                    <select name="status[]" class="form-select">
                        <option value="1">Kích hoạt</option>
                        <option value="0">Tạm tắt</option>
                    </select>
                </div>
                <div class="col-md-12 p-2">
                    <button type="button" class="btn btn-outline-danger btn-lg" onclick="removePaymentMethod(this)">
                        Xóa
                    </button>
                </div>
            `;
            container.appendChild(newRow);
            initializeEditors();
        });

        function removePaymentMethod(button) {
            button.closest('.payment-method-row').remove();
        }
    </script>

    {{-- CSS nhỏ cho bố cục --}}
    <style>
        .payment-method-row {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        @media (max-width: 768px) {
            #add-payment-method {
                width: 100%;
            }
        }
    </style>
@endsection
