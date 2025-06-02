@extends('layouts.admin')
@section('content')
    <h2 class="text-center">Thùng rác - Trạng thái đơn hàng</h2>
    <ul class="nav nav-pills mb-3">
        <li class="nav-item">

        </li>
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.orders.status.index', ['status' => 1]) }}">
                Đang hoạt động ({{$data->count()}})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link active" href="{{ route('admin.orders.status.trashed') }}">
                Thùng rác ({{$deleteCount}})
            </a>
        </li>
    </ul>
    <div class="mt-4 bg-white shadow-sm rounded p-3">

        <div class="d-md-flex align-items-center mb-3">
            <div>
                <h4 class="card-title text-dark">Các trạng thái đã xóa mềm</h4>
                <p class="card-subtitle">Bạn có thể khôi phục hoặc xóa vĩnh viễn</p>
            </div>
        </div>

        @if ($statuses->isEmpty())
            <p class="text-center text-muted">Không có trạng thái nào trong thùng rác.</p>
        @else
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th style="width: 50px;">STT</th>
                        <th style="width: 200px;">Tên trạng thái</th>
                        <th>Ngày xóa</th>
                        <th style="width: 250px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($statuses as $key => $status)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $status->name }}</td>
                            <td>{{ $status->deleted_at->format('d/m/Y H:i') }}</td>
                            <td class="d-flex gap-2">
                                {{-- Khôi phục --}}
                                <form action="{{route('admin.orders.status.restore', $id = $status->id)}}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <button type="submit" class="btn btn-sm btn-success">
                                        <i class="fa fa-undo me-1"></i> Khôi phục
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
    <br>    
    <a href="{{ route('admin.orders.status.index') }}" class="btn btn-secondary mb-3">
        <i class="fa fa-arrow-left me-1"></i> Quay lại danh sách
    </a>
@endsection
