@extends('layouts.admin')


@section('content')
  <h2 class="text-center">{{ $title }}</h2>
      {{-- Nút thao tác --}}
    <div class="col-12 d-flex align-items-center justify-content-center gap-2 mb-3">
        <a href="{{ route('admin.attribute.create') }}" class="btn btn-success" title="Thêm thuộc tính">
            <i class="fa-solid fa-square-plus"></i>
        </a>
        <a href="{{ route('admin.attribute.trash') }}" class="btn btn-secondary" title="Thùng rác">
            <i class="fa-solid fa-dumpster"></i>
        </a>
    </div>
  <div class=" mt-4 bg-white shadow-sm rounded p-3 ">
    @if (count($attributes) <= 0)
    <div>
    <p class="text-center text-muted">Không có thuộc tính nào</p>
    </div>
    @endif
    @if (count($attributes) > 0)
    <table class="table table-bordered mt-4 table-striped">
    <thead class="thead-dark">
      <tr>
      <th style="width: 50px;">#</th>
      <th style="width: 200px;">Tên thuộc tính</th>
      <th>Giá trị hiện có</th>
      <th style="width: 200px;">Thao tác</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($attributes as $attribute)
      <tr>
      <td>{{ $attribute->id }}</td>
      <td>{{ $attribute->name }}</td>
      <td>{{ $attribute->attributeValues->count() ?? 0 }}</td>
      <td class="d-flex gap-1">
      <a href="{{ route('admin.attribute.show', $attribute->id) }}" class="btn btn-sm btn-info">Chi tiết</a>
      <a href="{{ route('admin.attribute.value.create', $attribute->id) }}" class="btn btn-sm btn-info">Thêm giá trị</a>
      <a href="{{ route('admin.attribute.edit', $attribute->id) }}" class="btn btn-sm btn-warning">Sửa</a>

      <form action="{{ route('admin.attribute.destroy', $attribute->id) }}" method="POST"
      onsubmit="return confirm('Chuyển thuộc tính vào thùng rác?')" style="display:inline">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
      </form>
      </td>
      </tr>
    @empty
      <tr>
      <td colspan="4">Không có thuộc tính nào.</td>
      </tr>
    @endforelse
    </tbody>
    </table>
    @endif
    <a href="{{ route('admin.attribute.trash') }}" class="btn btn-primary btn-sm" title="Xem thùng rác">
    <i class="fas fa-trash-alt"></i> Thùng rác
    </a>
  </div>
@endsection