@extends('layouts.admin')
@section('content')
    <h2 class="text-center">{{ $title }}</h2>
    <ul class="nav nav-pills mb-3">
        <li class="nav-item">

        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('admin.orders.status.index', ['status' => 1]) }}">
                Đang hoạt động ({{$statuses->count()}})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.orders.status.trashed') }}">
                Thùng rác ({{$deleteCount}})
            </a>
        </li>
    </ul>
    <div class="mt-4 bg-white shadow-sm rounded p-3">

        <div class="d-md-flex align-items-center">
            <div>
                <h4 class="card-title text-dark">Danh sách trạng thái đơn hàng</h4>
                <p class="card-subtitle">Quản lý các trạng tái đơn hàng</p>
            </div>
            <div class="ms-auto mt-3 mt-md-0">

                <a href="{{ route('admin.orders.status.create') }}" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-1"></i> Thêm mới
                </a>

            </div>
        </div>

        @if (count($statuses) <= 0)
            <div>
                <p class="text-center text-muted">Trạng thái đang trống, hãy thêm trạng thái mới</p>
            </div>
        @endif
        @if (count($statuses) > 0)
            <table class="table table-bordered mt-4 table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th style="width: 50px;">STT</th>
                        <th style="width: 200px;">Tên trạng thái</th>
                        <th>Ngày tạo</th>
                        <th style="width: 200px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($statuses as $key => $status)
                        <tr>
                            <td>{{ $status->key + 1 }}</td>
                            <td>{{ $status->name }}</td>
                            <td>{{ $status->created_at }}</td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('admin.orders.status.edit', $id = $status->id) }}"
                                    class="btn btn-sm btn-warning">Sửa</a>

                                <form action="{{route('admin.orders.status.destroy', $id = $status->id)}}" method="POST" onsubmit="return confirm('Chuyển vào thùng rác?')"
                                    style="display:inline">
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
    </div>
@endsection