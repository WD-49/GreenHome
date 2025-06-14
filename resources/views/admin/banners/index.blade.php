@extends('layouts.admin')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Quản lý Banner</h1>
    {{-- Bộ lọc nâng cao --}}
<div class="mb-4">
    <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse"
            data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
        Bộ lọc nâng cao
    </button>

    <div class="collapse mt-3" id="filterCollapse">
        <div class="border rounded p-3 bg-light">
            <form method="GET" action="{{ route('admin.banners.index') }}" class="row g-3">
                {{-- Tên banner --}}
                <div class="col-md-4">
                    <label for="search" class="form-label">Tên banner</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="Nhập tên banner" value="{{ request('search') }}">
                </div>

                {{-- Trạng thái --}}
                <div class="col-md-4">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">-- Tất cả --</option>
                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>Hiển thị</option>
                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>Ẩn</option>
                    </select>
                </div>

                {{-- Ngày tạo --}}
                <div class="col-md-4">
                    <label class="form-label">Ngày tạo</label>
                    <div class="input-group">
                        <span class="input-group-text">Từ</span>
                        <input type="date" name="min_date" class="form-control"
                               value="{{ request('min_date') }}">
                        <span class="input-group-text">đến</span>
                        <input type="date" name="max_date" class="form-control"
                               value="{{ request('max_date') }}">
                    </div>
                </div>

                {{-- Nút lọc --}}
                <div class="col-12 text-end mt-2">
                    <button type="submit" class="btn btn-primary">Tìm kiếm</button>
                    <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Reset</a>
                </div>
            </form>
        </div>
    </div>
</div>


    <a href="{{ route('admin.banners.create') }}" class="btn btn-primary mb-3">Thêm mới</a>

   <div class="table-responsive shadow-sm rounded">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr class="text-center">
                <th>Hình ảnh</th>
                <th>Tên</th>
                <th style="width: 300px;">Mô tả</th>
                <th>Liên kết</th>
                <th>Ưu tiên</th>
                <th>Trạng thái</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @forelse($banners as $banner)
                <tr>
                    <td class="text-center">
                        @if($banner->img)
                            <img src="{{ asset($banner->img) }}" alt="Banner" style="max-width: 100px; max-height: 60px; object-fit: cover;" class="rounded shadow-sm">
                        @else
                            <span class="text-muted fst-italic">Chưa có ảnh</span>
                        @endif
                    </td>
                    <td class="fw-medium">{{ $banner->name }}</td>
                    <td>
                        <div style="max-height: 100px; overflow: auto;" class="small">
                            {!! $banner->description ?? '<span class="text-muted">Không có</span>' !!}
                        </div>
                    </td>
                    <td class="text-center">
                        @if($banner->link)
                            <a href="{{ $banner->link }}" target="_blank" class="btn btn-sm btn-outline-info">Xem</a>
                        @else
                            <span class="text-muted">Không có</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $banner->priority ?? 0 }}</td>
                    <td class="text-center">
                        @if($banner->status)
                            <span class="badge bg-success">Hiển thị</span>
                        @else
                            <span class="badge bg-secondary">Ẩn</span>
                        @endif
                    </td>
                    <td class="text-center">
                        <a href="{{ route('admin.banners.edit', $banner->id) }}" class="btn btn-sm btn-warning">
                            <i class="bi bi-pencil-square"></i> Sửa
                        </a>
                        <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa banner này?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">
                                <i class="bi bi-trash"></i> Xóa
                            </button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted">Không có banner nào.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-3">
    <div class="mt-3">
    {{ $banners->appends(request()->query())->links() }}
</div>

</div>

</div>
@endsection
