@extends('layouts.admin')

@section('content')
<h3>Sửa thương hiệu</h3>
<form method="POST" action="{{ route('admin.brands.update', $brand) }}">
    @csrf @method('PUT')
    <div class="mb-3">
        <label>Tên thương hiệu</label>
        <input type="text" name="name" class="form-control" value="{{ $brand->name }}" required>
    </div>
    <div class="mb-3">
        <label>Mô tả</label>
        <textarea name="description" class="form-control">{{ $brand->description }}</textarea>
    </div>
    <button class="btn btn-success">Cập nhật</button>
</form>
@endsection
