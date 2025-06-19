@extends('layouts.admin')

@section('content')
    <div class="container-xxl">
        <h2 class="text-center mb-4">Chi tiết thuộc tính: <strong>{{ $attribute->name }}</strong></h2>

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <form id="perPageForm" method="GET" action="{{ route('admin.attribute.show', $attribute->id) }}"
                        class="d-flex align-items-center">
                        <label for="perPage" class="me-2 mb-0">Hiển thị</label>
                        <select name="per_page" id="perPage" class="form-select form-select-sm w-auto"
                            onchange="document.getElementById('perPageForm').submit();">
                            @foreach ([5, 10, 20, 50] as $size)
                                <option value="{{ $size }}"
                                    {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                    {{ $size }}
                                </option>
                            @endforeach
                        </select>
                        <span class="ms-2">giá trị</span>
                    </form>

                    <div>
                        <a href="{{ route('admin.attribute.index') }}" class="btn btn-outline-secondary btn-sm me-2">
                            <i class="fas fa-arrow-left me-1"></i> Quay lại
                        </a>
                        <a href="{{ route('admin.attribute.value.trash', $attribute->id) }}"
                            class="btn btn-outline-danger btn-sm">
                            <i class="fas fa-trash-alt me-1"></i> Giá trị đã xóa
                        </a>
                    </div>
                </div>

                @if ($attributeValues->isEmpty())
                    <div class="alert alert-info text-center">Không có giá trị nào cho thuộc tính này.</div>
                @else
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Giá trị</th>
                                    <th>Ngày tạo</th>
                                    <th class="text-center">Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attributeValues as $index => $value)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $value->value }}</td>
                                        <td>{{ $value->created_at->format('d/m/Y') }}</td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.attribute.value.edit', $value->id) }}"
                                                class="btn btn-sm btn-warning me-1">
                                                <i class="fas fa-edit"></i> Sửa
                                            </a>
                                            <form method="POST"
                                                action="{{ route('admin.attribute.value.destroy', $value->id) }}"
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
                        {{-- 5:34 17/6 note: appends chỉ dùng cho panigator not collection --}}
                        {{-- {{ $attributeValues->appends(request()->query())->links() }} --}}
                    </div>
                @endif

                {{-- Form thêm giá trị mới --}}
                <form action="{{ route('admin.attribute.value.store') }}" method="POST" class="row g-2 w-50 mt-4">
                    @csrf
                    <input type="hidden" name="attribute_id" value="{{ $attribute_id }}">
                    <div class="col-md-8">
                        <input type="text" class="form-control @error('name') is-invalid @enderror" name="name"
                            value="{{ old('name') }}" placeholder="Thêm mới giá trị tại đây...">
                        @error('name')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-success btn-sm w-100">
                            <i class="fas fa-plus-circle me-1"></i> Thêm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
