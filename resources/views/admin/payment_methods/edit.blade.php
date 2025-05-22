@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Sửa phương thức thanh toán</h1>

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
            <textarea name="description" class="form-control @error('description') is-invalid @enderror">{{ old('description', $paymentMethod->description) }}</textarea>
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
        <button class="btn btn-success" type="submit">Cập nhật</button>
        <a href="{{ route('admin.paymentMethods.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection
