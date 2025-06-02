@extends('layouts.admin')

@section('content')
    <div class="container mt-4">
        <h2 class="text-center mb-4">Chi tiết thuộc tính: <strong>{{ $attribute->name }}</strong></h2>

        <div class="card shadow-sm">
            <div class="card-body">
                @if($attributeValues->isEmpty())
                    <div class="alert alert-info text-center">Không có giá trị nào cho thuộc tính này.</div>
                @else
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Giá trị</th>
                                <th>Ngày tạo</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($attributeValues as $index => $value)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $value->value }}</td>
                                    <td>{{ $value->created_at->format('d/m/Y') }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.attribute.value.edit', $value->id) }}"
                                            class="btn btn-sm btn-warning me-1">
                                            <i class="fas fa-edit"></i> Sửa
                                        </a>
                                        <form method="POST" action="{{ route('admin.attribute.value.destroy', $value->id) }}"
                                            style="display:inline-block;"
                                            onsubmit="return confirm('Bạn có chắc muốn xóa giá trị này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">
                                                <i class="fas fa-trash-alt"></i> Xóa
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>

                    @endif
                    {{-- Form thêm giá trị mới --}}
                    <form action="{{ route('admin.attribute.value.store') }}" method="POST" class="d-flex w-50 mt-3">
                        @csrf
                        <input type="hidden" name="attribute_id" value="{{$attribute_id}}">
                       <div class="w-75 me-2">
                         <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name"
                            value="{{ old('name') }}" placeholder="Thêm mới giá trị tại đây..." value="{{ old('name') }}">
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                       </div>
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-plus-circle me-1"></i> Thêm
                        </button>
                    </form>

                <div class="mt-4 d-flex justify-content-between">
                    <a href="{{ route('admin.attribute.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách thuộc tính
                    </a>
                    <a href="{{ route('admin.attribute.value.trash', $attribute->id) }}" class="btn btn-outline-danger">
                        <i class="fas fa-trash-alt me-1"></i> Giá trị đã xóa
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection