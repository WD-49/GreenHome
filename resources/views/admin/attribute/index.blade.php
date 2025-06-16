@extends('layouts.admin')

@section('title')
    {{ $title }}
@endsection

@section('content')
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-center flex-sm-row flex-column mb-3">
            <div class="flex-grow-1 d-flex align-items-center gap-2">
                <i class="mdi mdi-format-list-bulleted fs-3 text-primary"></i>
                <h4 class="fs-20 fw-bold m-0">{{ $title }}</h4>
            </div>
        </div>

        <ul class="nav nav-pills mb-3">
            <li class="nav-item">
                <a class="nav-link active" href="{{ route('admin.products.index', ['status' => 1]) }}">
                    Đang hoạt động ({{ $attributes->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.attribute.trash') }}">
                    Thùng rác ({{ $deleteCount }})
                </a>
            </li>
        </ul>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">Danh sách Thuộc tính</h5>
            </div>
            
            <div class="card-body">
                {{-- Form tạo thuộc tính mới --}}
                <form action="{{ route('admin.attribute.store') }}" method="POST" class="row g-2 mb-4 align-items-end">
                    @csrf
                    <div class="col-md-6">
                        <label for="name" class="form-label">Tên thuộc tính</label>
                        <input type="text" name="name" id="name" class="form-control" placeholder="Nhập tên thuộc tính" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                             Tạo mới
                        </button>
                    </div>
                </form>

                {{-- Hiển thị lỗi --}}
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Danh sách thuộc tính --}}
                
                @if ($attributes->isEmpty())
                    <p class="text-center text-muted">Không có thuộc tính nào</p>
                @else
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table">
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th style="width: 250px;">Tên thuộc tính</th>
                                <th>Giá trị hiện có</th>
                                <th style="width: 160px;" class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($attributes as $attribute)
                                <tr>
                                    <td>{{ $attribute->id }}</td>
                                    <td>{{ $attribute->name }}</td>
                                    <td>{{ $attribute->attributeValues->count() }}</td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.attribute.show', $attribute->id) }}">
                                                        Chi tiết
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.attribute.edit', $attribute->id) }}">
                                                        Chỉnh sửa
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.attribute.destroy', $attribute->id) }}" method="POST"
                                                          onsubmit="return confirm('Bạn có chắc chắn muốn bỏ thuộc tính này vào thùng rác?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="dropdown-item text-danger" type="submit">
                                                            Xóa
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{-- Dropdown chọn số bản ghi --}}
<form method="GET" class="mb-3 d-flex align-items-center">
    <label class="me-2">Hiển thị</label>
    <select name="per_page" class="form-select w-auto me-2" onchange="this.form.submit()">
        @foreach ([5, 10, 20, 50, 100] as $option)
            <option value="{{ $option }}" {{ request('per_page') == $option ? 'selected' : '' }}>
                {{ $option }}
            </option>
        @endforeach
    </select>
    <span>bản ghi</span>
</form>
                @endif
            </div>
        </div>
    </div>
@endsection
