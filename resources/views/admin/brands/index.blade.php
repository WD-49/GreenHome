@extends('layouts.admin')

@section('title')
    {{ $title ?? 'Quản lý thương hiệu' }}
@endsection

@section('content')
<div class="container-xxl">
    <div class="py-3 d-flex align-items-center flex-sm-row flex-column mb-3">
        <div class="flex-grow-1 d-flex align-items-center gap-2">
            <i class="mdi mdi-tag-multiple-outline fs-3 text-primary"></i>
            <h4 class="fs-20 fw-bold m-0">Quản lý thương hiệu</h4>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Danh sách thương hiệu</h5>
                    <div>
                        <a href="{{ route('admin.brands.create') }}" class="btn btn-success shadow-sm">
                            + Thêm thương hiệu
                        </a>
                        <a href="{{ route('admin.brands.trash') }}" class="btn btn-danger shadow-sm">
                            <i class="fas fa-trash-restore fa-sm text-white-50"></i> Thùng rác
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    {{-- Chọn số lượng hiển thị --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <form id="perPageForm" method="GET" action="{{ route('admin.brands.index') }}"
                              class="d-flex align-items-center">
                            {{-- <label for="perPage" class="me-2 mb-0">Hiển thị</label> --}}
                            {{-- <select name="per_page" id="perPage" class="form-select form-select-sm w-auto"
                                    onchange="this.form.submit()">
                                @foreach ([10, 20, 50, 100] as $size)
                                    <option value="{{ $size }}"
                                        {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                        {{ $size }}
                                    </option>
                                @endforeach
                            </select>
                            <span class="ms-2">mục</span> --}}
                            {{-- Giữ lại các filter --}}
                            @foreach (request()->except('per_page', 'page') as $key => $value)
                                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                            @endforeach
                        </form>

                        {{-- Nếu muốn thêm bộ lọc mở rộng sau này --}}
                        <div></div>
                    </div>

                    {{-- Tìm kiếm từ khóa --}}
                    <form method="GET" action="{{ route('admin.brands.index') }}" class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="keyword" class="form-label">Từ khóa</label>
                            <input type="text" name="keyword" id="keyword" value="{{ request('keyword') }}"
                                   class="form-control" placeholder="Nhập tên thương hiệu...">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <button type="submit" class="btn btn-outline-primary me-2">
                                <i class="fas fa-search me-1"></i> Tìm
                            </button>
                            <a href="{{ route('admin.brands.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-sync me-1"></i> Làm mới
                            </a>
                        </div>
                    </form>

                    {{-- Bảng danh sách --}}
                    <div class="table-responsive">
                        <table class="table table-striped w-100 nowrap">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Tên thương hiệu</th>
                                    <th>Slug</th>
                                    <th>Mô tả</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($brands as $index => $brand)
                                    <tr>
                                        <td>{{ ($brands->currentPage() - 1) * $brands->perPage() + $index + 1 }}</td>
                                        <td>{{ $brand->name }}</td>
                                        <td><span class="badge bg-info">{{ $brand->slug }}</span></td>
                                        <td><div style="max-width: 300px">{!! $brand->description !!}</div></td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-light btn-sm me-2" type="button"
                                                    data-bs-toggle="dropdown" aria-expanded="false">
                                                    <span class="mdi mdi-settings-helper"></span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item"
                                                           href="{{ route('admin.brands.show', $brand->slug) }}">Chi tiết</a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                           href="{{ route('admin.brands.edit', $brand->slug) }}">Chỉnh sửa</a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('admin.brands.destroy', $brand->slug) }}"
                                                              method="POST"
                                                              onsubmit="return confirm('Bạn có chắc chắn muốn bỏ thương hiệu này vào thùng rác không?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button class="dropdown-item text-danger" type="submit">Xóa</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Không tìm thấy thương hiệu nào.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- phân trang --}}
                    <x-ajax-pagination :paginator="$brands" />
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
