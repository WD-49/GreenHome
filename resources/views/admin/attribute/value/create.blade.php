@extends('layouts.admin')
@section('content')
<div class="container mt-4">
    <h1>{{ $title }}</h1>
    <form action="{{route('admin.attribute.value.store')}}" method="post">
        @csrf

        <div class="mb-3">
        <label for="attribute_id" class="form-label">Chọn thuộc tính</label>
        <select name="attribute_id" id="attribute_id" class="form-select @error('attribute_id') is-invalid @enderror">
            <option value="">-- Chọn thuộc tính --</option>
            @foreach ($attributes as $attribute)
                <option value="{{ $attribute->id }}" {{ old('attribute_id') == $attribute->id ? 'selected' : '' }}>
                    {{ $attribute->name }}
                </option>
            @endforeach
        </select>
        @error('attribute_id')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

        <div class="mb-3">
            <label for="name" class="form-label">Tên Giá trị</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror"
                   id="name" name="name" value="{{ old('name') }}" placeholder="Giá trị của thuộc tính"
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