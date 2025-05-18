@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="" >Danh sách giá trị thuộc tính</h1>
    <a href="{{route('admin.attribute.value.create')}}" class="btn btn-warning">Thêm giá trị mới</a>
    <table class="table table-bordered mt-4 table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Giá trị</th>
                <th>Thuộc tính</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($attributeValues as $value)
                <tr>
                    <td>{{ $value->id }}</td>
                    <td>{{ $value->value }}</td>
                    <td>{{ $value->attribute->name ?? 'Không xác định' }}</td>
                    <td>
                        <a href="{{route('admin.attribute.value.edit', $id = $value->id)}}" class="btn btn-sm btn-warning">Sửa</a>
                        <form action="{{route('admin.attribute.value.destroy', $id = $value->id)}}" method="POST" class="d-inline-block" onsubmit="return confirm('Bạn có chắc muốn xóa?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">Không có giá trị nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection