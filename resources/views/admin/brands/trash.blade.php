@extends('layouts.admin')

@section('content')
<h3>🗑️ Thùng rác - Thương hiệu</h3>
<a href="{{ route('admin.brands.index') }}" class="btn btn-secondary mb-3">← Quay lại danh sách</a>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

@if($brands->count())
<table class="table table-bordered">
    <thead class="table-dark">
        <tr>
            <th>Tên thương hiệu</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        @foreach($brands as $brand)
        <tr>
            <td>{{ $brand->name }}</td>
            <td>
                {{-- Khôi phục --}}
                <form action="{{ route('admin.brands.restore', $brand->slug) }}" method="POST" class="d-inline">
                    @csrf
                    <button class="btn btn-sm btn-info">
                        ♻️ Khôi phục
                    </button>
                </form>

                {{-- Xóa vĩnh viễn với confirm --}}
                <form action="{{ route('admin.brands.forceDelete', $brand->slug) }}" method="POST" class="d-inline" 
                      onsubmit="return confirm('⚠️ Xóa vĩnh viễn thương hiệu này đồng nghĩa các sản phẩm liên quan sẽ bị mất liên kết thương hiệu. Bạn có chắc chắn muốn xóa?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">
                        ❌ Xóa vĩnh viễn
                    </button>
                </form>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{{-- Phân trang --}}
<div class="d-flex justify-content-center mt-3">
    {{ $brands->links() }}
</div>

@else
    <p>Không có thương hiệu nào trong thùng rác.</p>
@endif
@endsection
