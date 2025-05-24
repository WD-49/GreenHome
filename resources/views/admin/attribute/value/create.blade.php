@extends('layouts.admin')
@section('content')
<h2 class="text-center">{{ $title }}</h2>
<div class="mt-4 bg-white shadow-sm rounded p-3">
    <form action="{{route('admin.attribute.value.store')}}" method="post">
        @csrf
        <input type="hidden" name="attribute_id" value="{{$attribute->id}}" id="">
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