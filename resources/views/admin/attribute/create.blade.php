@extends('layouts.admin')
@section('content')
<div class="container mt-4">
    <h1>{{$title}}</h1>
    {{-- Form --}}
    <form action="{{ route('admin.attribute.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Tên thuộc tính</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror"
                   id="name" name="name" value="{{ old('name') }}" placeholder="Ví dụ: Màu sắc, Kích thước..."
                value="{{ old('name') }}">
        @error('name')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
        </div>

        <button type="submit" class="btn btn-primary">Thêm mới</button>
    </form>
</div>
@endsection