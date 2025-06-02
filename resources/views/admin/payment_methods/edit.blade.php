@extends('layouts.admin')

@section('title', 'Sửa phương thức thanh toán')

@section('content')
    <style>
        .payment-method-form-container {
            max-width: 700px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>

    <div class="container payment-method-form-container">
        <h2 class="mb-4">Sửa phương thức thanh toán</h2>

        @if($errors->any())
            <div class="alert alert-danger">Vui lòng kiểm tra lại các lỗi bên dưới.</div>
        @endif

        <form action="{{ route('admin.paymentMethods.update', $paymentMethod->id) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Tên phương thức</label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                       value="{{ old('name', $paymentMethod->name) }}">
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea name="description" id="description" class="form-control @error('description') is-invalid @enderror"
                          rows="4">{{ old('description', $paymentMethod->description) }}</textarea>
                @error('description')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-select @error('status') is-invalid @enderror">
                    <option value="1" {{ old('status', $paymentMethod->status) == 1 ? 'selected' : '' }}>Kích hoạt</option>
                    <option value="0" {{ old('status', $paymentMethod->status) == 0 ? 'selected' : '' }}>Tạm tắt</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="mt-4">
                <button class="btn btn-success" type="submit">
                    <i class="fa-solid fa-pen-to-square"></i> Cập nhật
                </button>
                <a href="{{ route('admin.paymentMethods.index') }}" class="btn btn-secondary">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại
                </a>
            </div>
        </form>
    </div>

    {{-- CKEditor Script --}}
    <script src="https://cdn.ckeditor.com/ckeditor5/35.0.1/classic/ckeditor.js"></script>
    <script>
        ClassicEditor
            .create(document.querySelector('#description'))
            .catch(error => {
                console.error(error);
            });
    </script>
@endsection
