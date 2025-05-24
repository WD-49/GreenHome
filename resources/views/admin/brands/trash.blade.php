@extends('layouts.admin')

@section('content')
<h3>Thùng rác - Thương hiệu</h3>
<a href="{{ route('admin.brands.index') }}" class="btn btn-secondary mb-2">Quay lại</a>

@if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Tên</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach($brands as $brand)
        <tr>
            <td>{{ $brand->name }}</td>
            <td>
                <form action="{{ route('admin.brands.restore', $brand->id) }}" method="POST" style="display:inline-block">
                    @csrf
                    <button class="btn btn-sm btn-info">Khôi phục</button>
                </form>
                <form action="{{ route('admin.brands.forceDelete', $brand->id) }}" method="POST" style="display:inline-block" onsubmit="return confirm('Bạn muốn xóa vĩnh viễn thương hiệu này?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-danger">Xóa vĩnh viễn</button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
@endsection
