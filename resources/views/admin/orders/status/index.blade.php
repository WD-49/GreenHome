@extends('layouts.admin')
@section('content')
    <h2 class="text-center">{{ $title }}</h2>
    <div class="mt-4 bg-white shadow-sm rounded p-3">
        <a href="{{ route('admin.orders.status.create') }}" class="btn btn-warning"><i class="fas fa-plus me-2"></i>Thêm mới</a>
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

                                {{-- <form action="" method="POST"
                                    onsubmit="return confirm('Chuyển thuộc tính vào thùng rác?')" style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Xóa</button>
                                </form> --}}
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
