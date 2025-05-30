@extends('layouts.admin') {{-- hoặc layout bạn đang dùng --}}

@section('content')
<div class="container mt-4">
    <h2>Thùng rác - Giá trị thuộc tính</h2>
    
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Thuộc tính</th>
                <th>Giá trị</th>
                <th>Ngày xóa</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($values as $value)
            <tr>
                <td>{{ $value->id }}</td>
                <td>{{ $value->attribute->name ?? '[Đã xóa]' }}</td>
                <td>{{ $value->value }}</td>
                <td>{{ $value->deleted_at}}</td>
                <td>
                    <form action="{{route('admin.attribute.value.restore', $id =$value->id)}}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Bạn có chắc muốn phục hồi?')">
                            <i class="bi bi-arrow-clockwise"></i> Phục hồi
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center">Không có giá trị thuộc tính nào trong thùng rác.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
