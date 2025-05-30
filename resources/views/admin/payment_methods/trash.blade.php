@extends('layouts.admin')

@section('content')
<div class="container">
    <h1 class="mt-4 mb-4">Thùng rác phương thức thanh toán</h1>

    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Lọc phương thức đã xoá</h5>
    </div>
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.paymentMethods.trash') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="name" class="form-label">Tên phương thức</label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Nhập tên phương thức"
                           value="{{ request('name') }}">
                </div>
                <div class="col-md-4 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Tìm kiếm
                    </button>
                    <a href="{{ route('admin.paymentMethods.trash') }}" class="btn btn-warning w-100">
                        <i class="fas fa-sync-alt me-1"></i> Làm mới
                    </a>
                </div>
                <div class="col-md-4 d-flex align-items-end justify-content-end">
                    <a href="{{ route('admin.paymentMethods.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Danh sách chính
                    </a>
                </div>
            </form>
        </div>
    </div>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tên</th>
                <th>Mô tả</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paymentMethods as $method)
                <tr>
                    <td>{{ $method->name }}</td>
                    <td>{{ $method->description }}</td>
                    <td><span class="badge {{ $method->status ? 'bg-success' : 'bg-danger' }}">
                            {{ $method->status ? 'Kích hoạt' : 'Tạm tắt' }}
                        </span></td>
                    <td>
                        <form action="{{ route('admin.paymentMethods.restore', $method->id) }}" method="POST" style="display:inline">
                            @csrf
                            <button class="btn btn-info btn-sm"><i class="fa-solid fa-rotate-left"></i></button>
                        </form>
                         <form action="{{ route('admin.paymentMethods.forceDelete', $method->id) }}" method="POST"
                                class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm btn-confirm" title="xóa vĩnh viễn"
                                    data-confirm-message="Bạn có chắc chắn muốn xóa vĩnh viễn phương thức thanh toán này không?"><i
                                        class="fa-solid fa-trash"></i></button>
                            </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $paymentMethods->links() }}
</div>
@endsection
