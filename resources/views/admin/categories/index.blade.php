@extends('layouts.admin')

@section('title')
    {{ $title ?? 'Danh sách danh mục' }}
@endsection

@section('content')
    <style>
        .nav-pills .nav-link.active {
            font-weight: 700;
            border-radius: 0 !important;
            background-color: transparent !important;
            color: #0768e8;
        }
    </style>
    <div class="row">
        <h2 class="text-center mb-4">{{ $title ?? 'Danh sách danh mục' }}</h2>
        <!-- Tabs Trạng thái -->
        <ul class="nav nav-pills mb-3">
            <li class="nav-item">
                <a class="nav-link {{ request('status') == null ? 'active' : '' }}"
                   href="{{ route('admin.categories.index') }}">
                    Tất cả ({{ $categoryAll->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') == 'active' ? 'active' : '' }}"
                   href="{{ route('admin.categories.index', ['status' => 'active']) }}">
                    Đang hoạt động ({{ $categoryActive->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') == 'deleted' || request()->routeIs('admin.categories.trash') ? 'active' : '' }}"
                   href="{{ route('admin.categories.trash') }}">
                    Thùng rác ({{ $categoryTrashed->count() }})
                </a>
            </li>
        </ul>

        <div class="card">
            <div class="card-body">
                <div class="d-md-flex align-items-center">
                    <div>
                        <h4 class="card-title mb-1">Danh sách danh mục</h4>
                        <p class="card-subtitle">Quản lý các danh mục trong hệ thống</p>
                    </div>
                    <div class="ms-auto mt-3 mt-md-0">
                        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
                            <i class="fa-solid fa-plus me-1"></i> Thêm danh mục
                        </a>
                        <a class="btn btn-outline-secondary dropdown-toggle ms-2" type="button" id="filterDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                            <i class="fas fa-filter me-1"></i> Bộ lọc
                        </a>
                        <div class="dropdown-menu p-4" style="min-width: 500px;" aria-labelledby="filterDropdown">
                            <form method="GET" action="{{ route('admin.categories.index') }}" class="row g-3">
                                <div class="col-md-6">
                                    <label for="search" class="form-label">Tên danh mục</label>
                                    <input type="text" name="search" id="search" class="form-control" placeholder="Nhập tên danh mục"
                                        value="{{ request('search') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Trạng thái</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                                        <option value="deleted" {{ request('status') == 'deleted' ? 'selected' : '' }}>Đã xóa</option>
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">Ngày tạo</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light">Từ</span>
                                        <input type="date" name="min_date" class="form-control" value="{{ request('min_date') }}">
                                        <span class="input-group-text bg-light">đến</span>
                                        <input type="date" name="max_date" class="form-control" value="{{ request('max_date') }}">
                                    </div>
                                </div>
                                <div class="col-md-12 d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('admin.categories.index') }}" class="btn btn-warning">
                                        <i class="fas fa-sync me-1"></i> Làm mới
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="table-responsive mt-4">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th class="px-0 text-muted">ID</th>
                                <th class="px-0 text-muted">Tên danh mục</th>
                                <th class="px-0 text-muted">Slug</th>
                                <th class="px-0 text-muted">Mô tả</th>
                                <th class="px-0 text-muted">Trạng thái</th>
                                <th class="px-0 text-muted">Ngày tạo</th>
                                <th class="px-0 text-muted text-end"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                                <tr>
                                    <td class="px-0">{{ $category->id }}</td>
                                    <td class="px-0">{{ $category->name }}</td>
                                    <td class="px-0">{{ $category->slug }}</td>
                                    <td class="px-0">{!! $category->description !!}</td>
                                    <td class="px-0">
                                        <span class="badge {{ $category->deleted_at ? 'bg-danger' : 'bg-success' }}">
                                            {{ $category->deleted_at ? 'Đã xóa' : 'Đang hoạt động' }}
                                        </span>
                                    </td>
                                    <td class="px-0">{{ $category->created_at->format('d/m/Y') }}</td>
                                    <td class="px-0 text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button"
                                                id="dropdownMenuButton{{ $category->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="dropdownMenuButton{{ $category->id }}">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.categories.show', $category->slug) }}">
                                                        Xem chi tiết
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.categories.edit', $category->slug) }}">
                                                        Chỉnh sửa
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.categories.destroy', $category->slug) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn bỏ danh mục này vào thùng rác không?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="dropdown-item text-danger" type="submit">
                                                            Xóa danh mục
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7">Không tìm thấy danh mục nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- PHÂN TRANG -->
                @if ($categories->lastPage() > 1)
                    <nav aria-label="Page navigation example">
                        <ul class="pagination justify-content-end mt-3 mb-0">
                            <li class="page-item {{ $categories->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $categories->previousPageUrl() }}">Previous</a>
                            </li>
                            @for ($i = 1; $i <= $categories->lastPage(); $i++)
                                <li class="page-item {{ $i == $categories->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $categories->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor
                            <li class="page-item {{ !$categories->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $categories->nextPageUrl() }}">Next</a>
                            </li>
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </div>
@endsection
