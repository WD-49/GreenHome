@extends('layouts.admin')
@section('content')
<div class="container mt-4">
    <h1>{{$title}}</h1>
    {{-- Form --}}
    <form action="{{ route('admin.attribute.update', $id = $attribute->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="name" class="form-label">Tên thuộc tính</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror"
                   id="name" name="name" value="{{ $attribute->name }}" placeholder="Ví dụ: Màu sắc, Kích thước..."
                value="{{ old('name') }}">
        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
        </div>

        <button type="submit" class="btn btn-primary">Cập nhật</button>
    </form>
</div>
@endsection