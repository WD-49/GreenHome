@extends('layouts.admin')

@section('content')
<h3>Thêm thương hiệu</h3>
<form method="POST" action="{{ route('brands.store') }}">
    @csrf
    <div class="mb-3">
        <label>Tên thương hiệu</label>
        <input type="text" name="name" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Mô tả</label>
        <textarea name="description" class="form-control"></textarea>
    </div>
    <button class="btn btn-success">Lưu</button>
</form>
@endsection
