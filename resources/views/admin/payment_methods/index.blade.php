@extends('layouts.admin') <!-- Thay bằng layout admin của bạn -->

@section('content')
<div class="container">
   <h1 class="mt-4 mb-4">Danh sách phương thức thanh toán</h1>

{{-- Thông báo thành công --}}


<div class="card-header bg-primary text-white">
    <h5 class="mb-0"><i class="fas fa-filter"></i> Lọc phương thức thanh toán</h5>
</div>
<div class="card">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.paymentMethods.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="name" class="form-label">Tên phương thức</label>
                <input type="text" name="name" id="name" class="form-control" placeholder="Nhập tên phương thức"
                       value="{{ request('name') }}">
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search me-1"></i> Tìm kiếm
                </button>
                <a href="{{ route('admin.paymentMethods.index') }}" class="btn btn-warning w-100">
                    <i class="fas fa-sync-alt me-1"></i> Làm mới
                </a>
            </div>
        </form>
    </div>
</div>
  <div class="col-12 d-flex justify-content-center gap-2 my-3">
        <a href="{{ route('admin.paymentMethods.create') }}" class="btn btn-success" title="Thêm danh mục">
            <i class="fa-solid fa-square-plus"></i>
        </a>
        <a href="{{ route('admin.paymentMethods.trash') }}" class="btn btn-warning" title="Thùng rác">
            <i class="fa-solid fa-dumpster"></i>
        </a>
    </div>


    {{-- Bảng dữ liệu --}}
    <table class="table table-bordered align-middle">
        <thead class="table-light">
            <tr>
                <th style="width: 25%;">Tên</th>
                <th>Mô tả</th>
                <th style="width: 12%;">Trạng thái</th>
                <th style="width: 15%;">Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @forelse($paymentMethods as $method)
                <tr>
                    <td>{{ $method->name }}</td>
                    <td>{{ $method->description }}</td>
                    <td>
                        <span class="badge {{ $method->status ? 'bg-success' : 'bg-danger' }}">
                            {{ $method->status ? 'Kích hoạt' : 'Tạm tắt' }}
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('admin.paymentMethods.edit', $method->id) }}" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen"></i></a>
                          <form action="{{ route('admin.paymentMethods.destroy', $method) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm btn-confirm" title="xóa"
                                        data-confirm-message="Bạn có chắc chắn muốn bỏ phương thức thanh toán này vào thùng rác không?"><i
                                            class="fa-solid fa-trash"></i></button>
                                </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Không có dữ liệu</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- Phân trang --}}
    <div>
        {{ $paymentMethods->appends(request()->query())->links() }}
    </div>
</div>
@endsection
