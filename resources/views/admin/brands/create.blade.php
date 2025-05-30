@extends('layouts.admin')

@section('content')
<h3>Thêm thương hiệu</h3>
<a href="{{ route('admin.brands.index') }}" class="btn btn-secondary mb-3">← Quay lại danh sách</a>
<form method="POST" action="{{ route('admin.brands.store') }}" novalidate>
    @csrf
    <div class="mb-3">
        <label>Tên thương hiệu</label>
        <input type="text" name="name" class="form-control" required> <br>
        @error('name')
    <div class="text-danger mt-1">{{ $message }}</div>
@enderror
    </div>
    <div class="mb-3">
        <label>Mô tả</label>
        <textarea name="description" class="form-control"></textarea> 
        @error('description')
    <div class="text-danger mt-1">{{ $message }}</div>
@enderror
    </div>
    <button class="btn btn-success">Lưu</button>
</form>
@endsection
