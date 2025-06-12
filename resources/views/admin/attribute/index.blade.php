@extends('layouts.admin')

@section('content')
  <h2 class="text-center">{{ $title }}</h2>

  <ul class="nav nav-pills mb-3">
    <li class="nav-item"></li>
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

  <div class="mt-4 bg-white shadow-sm rounded p-3">
    <div class="d-md-flex align-items-center mb-4">
      <div>
        <h4 class="card-title text-dark">Danh sách Thuộc tính</h4>
        <p class="card-subtitle">Quản lý các thuộc tính của sản phẩm</p>
      </div>
    </div>
    {{-- Form tạo thuộc tính mới --}}
<form action="{{ route('admin.attribute.store') }}" method="POST" class="row g-2 mb-4">
  @csrf
  <div class="col-md-6">
    <input type="text" name="name" class="form-control" placeholder="Tên thuộc tính" required>
    <small class="text-muted d-block mt-1">Tạo thuộc tính mới tại đây</small>
  </div>
  <div class="col-md-2">
    <button type="submit" class="btn btn-primary">
      <i class="fa-solid fa-plus me-1"></i> Tạo thuộc tính
    </button>
  </div>
</form>


    {{-- Thông báo lỗi --}}
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
    @if (count($attributes) <= 0)
      <p class="text-center text-muted">Không có thuộc tính nào</p>
    @else
      <table class="table table-bordered table-striped">
        <thead class="thead-dark">
          <tr>
            <th style="width: 50px;">#</th>
            <th style="width: 200px;">Tên thuộc tính</th>
            <th>Giá trị hiện có</th>
            <th style="width: 200px;">Thao tác</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($attributes as $attribute)
            <tr>
              <td>{{ $attribute->id }}</td>
              <td>{{ $attribute->name }}</td>
              <td>{{ $attribute->attributeValues->count() ?? 0 }}</td>
              <td class="px-0 text-end">
                <div class="dropdown">
                  <button class="btn btn-light btn-sm me-2" type="button" id="dropdownMenuButton"
                          data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v">...</i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
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
                            onsubmit="return confirm('Bạn có chắc chắn muốn bỏ sản phẩm này vào thùng rác không?')">
                        @csrf
                        @method('DELETE')
                        <button class="dropdown-item text-danger" type="submit">
                          Xóa sản phẩm
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
    @endif
  </div>
@endsection
