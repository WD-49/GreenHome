@extends('layouts.admin')
@section('content')
    <h2 class="text-center">{{ $title }}</h2>
        <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-search"></i> Tìm kiếm</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.orders.status.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label for="keyword" class="form-label">Từ khóa</label>
                    <input type="text" name="keyword" value="{{ request('keyword') }}" class="form-control"
                        placeholder="Nhập từ khóa...">
                </div>
                <div class="col-md-6 d-flex align-items-end gap-2">
                    <button class="btn btn-primary w-100"><i class="fas fa-search me-1"></i> Tìm</button>
                    <a href="{{ route('admin.orders.status.index') }}" class="btn btn-warning w-100">
                        <i class="fas fa-sync me-1"></i> Làm mới
                    </a>
                </div>
            </form>
        </div>
    </div>

        {{-- Nút thao tác --}}
    <div class="col-12 d-flex align-items-center justify-content-center gap-2 mb-3">
        <a href="{{ route('admin.orders.status.create') }}" class="btn btn-success" title="Thêm thương hiệu">
            <i class="fa-solid fa-square-plus"></i>
        </a>
        <a href="{{ route('admin.orders.status.trashed') }}" class="btn btn-secondary" title="Thùng rác">
            <i class="fa-solid fa-dumpster"></i>
        </a>
    </div>

    <div class="mt-4 bg-white shadow-sm rounded p-3">
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
                        <th>Ngày tạo</th>
                        <th style="width: 200px;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($statuses as $key => $status)
                        <tr>
                            <td>{{ ( $key + 1) }}</td>
                            <td>{{ $status->name }}</td>
                            <td>{{ $status->created_at }}</td>
                            <td class="d-flex gap-1">
                                <a href="{{ route('admin.orders.status.edit', $id = $status->id) }}"
                                    class="btn btn-sm btn-warning"><i class="fas fa-pen"></i></a>

                                <form action="{{route('admin.orders.status.destroy', $id = $status->id)}}" method="POST" onsubmit="return confirm('Chuyển vào thùng rác?')"
                                    style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    {{-- <input type="hidden" name="id" value="{{$status->id}}"> --}}
                                    <button type="submit" class="btn btn-sm btn-danger"><i
                                            class="fa-solid fa-trash"></i></button>
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
    </div>
@endsection