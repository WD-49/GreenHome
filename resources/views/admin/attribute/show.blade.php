@extends('layouts.admin') {{-- hoặc layouts.master tùy layout của bạn --}}

@section('content')
<div class="container">
    <h1>Danh sách giá trị của thuộc tính: {{ $attribute->name }}</h1>

    @if($attributeValues->isEmpty())
        <p>Không có giá trị nào cho thuộc tính này.</p>
    @else
        <table class="table table-bordered">
            <thead>
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
                            <a href="{{ route('admin.attribute.value.edit', $value->id) }}" class="btn btn-sm btn-primary">Sửa</a>
                            <form method="POST" style="display:inline-block;">
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

    <a href="{{ route('admin.attribute.index') }}" class="btn btn-secondary">Quay lại danh sách thuộc tính</a>
</div>
@endsection