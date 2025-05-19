@extends('layouts.admin')

@section('content')
<div class="mt-4 bg-white shadow-sm rounded p-3">
    <h4>Cập nhật giá trị thuộc tính</h4>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{route('admin.attribute.value.update', $id =  $attributeValue->id)}}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label for="value" class="form-label">Giá trị</label>
            <input type="text" name="value" id="value" class="form-control @error('value') is-invalid @enderror"
                   value="{{ old('value', $attributeValue->value) }}">
            @error('value')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <input type="hidden" name="attribute_id" value="{{ $attributeValue->attribute_id }}">

        <button type="submit" class="btn btn-primary">Cập nhật</button>
        <a href="{{ route('admin.attribute.value.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
</div>
@endsection 