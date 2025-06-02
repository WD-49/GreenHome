@extends('layouts.admin')

@section('content')
<h3>Sửa thương hiệu</h3>
<a href="{{ route('admin.brands.index') }}" class="btn btn-secondary mb-3">← Quay lại danh sách</a>

<form method="POST" action="{{ route('admin.brands.update', $brand->slug) }}" novalidate>
    @csrf
    @method('PUT')
    <div class="mb-3">
        <label>Tên thương hiệu</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $brand->name) }}" required>
        @error('name')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>
    <div class="mb-3">
        <label>Mô tả</label>
        <textarea name="description" class="form-control">{{ old('description', $brand->description) }}</textarea>
        @error('description')
            <div class="text-danger mt-1">{{ $message }}</div>
        @enderror
    </div>
    <button class="btn btn-success">Cập nhật</button>
</form>

<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Summernote CSS + JS -->
<link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

<script>
  $(document).ready(function() {
    $('textarea[name="description"]').summernote({
      height: 200
    });
  });
</script>
@endsection
