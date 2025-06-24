@extends('layouts.admin')

@section('title')
    Quản lý Banner
@endsection

@section('content')
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-center flex-sm-row flex-column mb-3">
            <div class="flex-grow-1 d-flex align-items-center gap-2">
                <i class="mdi mdi-image-area fs-3 text-primary"></i>
                <h4 class="fs-20 fw-bold m-0">Quản lý Banner</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách Banner</h5>
                        <div>
                            <a href="{{ route('admin.banners.create') }}" class="btn btn-success shadow-sm">
                                + Thêm Banner
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <form id="perPageForm" method="GET" action="{{ route('admin.banners.index') }}"
                                class="d-flex align-items-center">
                                <label for="perPage" class="me-2 mb-0">Hiển thị</label>
                                <select name="per_page" id="perPage" class="form-select form-select-sm w-auto"
                                    onchange="this.form.submit()">
                                    @foreach ([10, 20, 50, 100] as $size)
                                        <option value="{{ $size }}" {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                            {{ $size }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="ms-2">bản ghi</span>
                                @foreach (request()->except('per_page', 'page') as $key => $value)
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endforeach
                            </form>

                            <button class="btn btn-outline-primary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                                <i class="mdi mdi-filter-outline me-1"></i> Bộ lọc
                            </button>
                        </div>

                        <div class="collapse mb-3" id="filterCollapse">
                            <div class="card card-body">
                                <form method="GET" action="{{ route('admin.banners.index') }}">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Tên banner</label>
                                            <input type="text" name="search" class="form-control" value="{{ request('search') }}">
                                        </div>

                                        <div class="col-md-4">
                                            <label class="form-label">Trạng thái</label>
                                            <select name="status" class="form-select">
                                                <option value="">Tất cả</option>
                                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Hiển thị</option>
                                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Ẩn</option>
                                            </select>
                                        </div>

                                        <div class="col-md-2">
                                            <label class="form-label">Ngày từ</label>
                                            <input type="date" name="min_date" class="form-control" value="{{ request('min_date') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Ngày đến</label>
                                            <input type="date" name="max_date" class="form-control" value="{{ request('max_date') }}">
                                        </div>

                                        <div class="col-md-12 text-end">
                                            <button type="submit" class="btn btn-outline-primary">Lọc</button>
                                            <a href="{{ route('admin.banners.index') }}" class="btn btn-outline-secondary">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div class="table-responsive shadow-sm rounded">
                            <table class="table table-hover align-middle">
                                <thead class="table-light text-center">
                                    <tr>
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
                                                    <img src="{{ asset($banner->img) }}" alt="Banner"
                                                        style="max-width: 100px; max-height: 60px; object-fit: cover;"
                                                        class="rounded shadow-sm">
                                                @else
                                                    <span class="text-muted fst-italic">Chưa có ảnh</span>
                                                @endif
                                            </td>
                                            <td>{{ $banner->name }}</td>
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
                                                <form action="{{ route('admin.banners.destroy', $banner->id) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa banner này?');">
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
                            {{ $banners->appends(request()->query())->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- @vite('resources/js/app.js') --}}
@endsection
