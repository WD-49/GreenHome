@extends('layouts.admin') {{-- hoặc layouts.master tùy layout của bạn --}}

@section('content')
<h2 class="text-center">Chi tiết thuộc tính: {{ $attribute->name }}</h2>
<div class="mt-4 bg-white shadow-sm rounded p-3">

    @if($attributeValues->isEmpty())
        <p>Không có giá trị nào cho thuộc tính này.</p>
    @else
        <table class="table table-bordered">
            <thead class="thead-dark">
                <tr>
                    <th>#</th>
                    <th>Giá trị</th>
                    <th>Ngày tạo</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($attributeValues as $index => $value)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $value->value }}</td>
                        <td>{{ $value->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.attribute.value.edit', $value->id) }}" class="btn btn-sm btn-warning">Sửa</a>
                            <form method="POST" action="{{route('admin.attribute.value.destroy', $id = $value->id)}}" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button onclick="return confirm('Xác nhận xóa?')" class="btn btn-sm btn-danger">Xóa</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <a href="{{ route('admin.attribute.index') }}" class="btn btn-primary">Quay lại danh sách thuộc tính</a>
    <a href="{{ route('admin.attribute.value.trash', $id=$attribute->id) }}" class="btn btn-primary"> <i class="fas fa-trash-alt me-2"></i>Giá trị đã xóa</a>
</div>
@endsection