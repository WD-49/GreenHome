@extends('layouts.admin')
@section('content')
    <h2 class="text-center">{{ $title }}</h2>
    <div class="mt-4 bg-white shadow-sm rounded p-3">
        <a href="{{ route('admin.orders.status.create') }}" class="btn btn-warning"><i class="fas fa-plus me-2"></i>Thêm
            mới</a>
        @if (count($statuses) <= 0)
            <div>
                <p class="text-center text-muted">Không có dữ liệu</p>
            </div>
        @endif
        @if (count($statuses) > 0)
            <table class="table table-bordered mt-4 table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th style="width: 50px;">STT</th>
                        <th style="width: 200px;">Tên trạng thái</th>
                        <th>Ngày xóa</th>
                        <th style="width: 200px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($statuses as $key => $status)
                        <tr>
                            <td>{{ ( $key + 1) }}</td>
                            <td>{{ $status->name }}</td>
                            <td>{{ $status->deleted_at }}</td>
                            <td class="d-flex gap-1">
                                <form action="{{route('admin.orders.status.restore', $status->id)}}" method="POST" onsubmit="return confirm('Khôi phục dữ liệu?')"
                                    style="display:inline">
                                    @csrf
                                    @method('PATCH')
                                    {{-- <input type="hidden" name="id" value="{{$status->id}}"> --}}
                                    <button type="submit" class="btn btn-sm btn-success"> <i class="fas fa-recycle"></i>Khôi phục</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">Không dữ liệu...</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        @endif
        <a href="{{ route('admin.orders.status.index') }}" class="btn btn-primary btn-sm" title="">
            Quay lại
        </a>
    </div>
@endsection