@extends('layouts.admin')
@section('content')
    <h2 class="text-center">{{$title}}</h2>
    <div class="mt-4 bg-white shadow-sm rounded p-3">
        <form action="{{route('admin.order.status.update', $id = $status->id)}}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label for="name" class="form-label">Tên trạng thái</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                    value="{{ old('name', $status->name) }}" placeholder="Trạng thái đơn hàng" value="{{ old('name') }}">
                @error('name')
                    <div class="invalid-feedback">
                        {{ $message }}
                    </div>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Cập nhật</button>
            <a href="{{route('admin.order.status.index')}}" class="btn btn-primary">Quay lại</a>
        </form>
    </div>
@endsection