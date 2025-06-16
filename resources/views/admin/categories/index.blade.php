@extends('layouts.admin')

@section('title')
    {{ $title ?? 'Danh mục' }}
@endsection

@section('content')
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-center flex-sm-row flex-column mb-3">
            <div class="flex-grow-1 d-flex align-items-center gap-2">
                <i class="mdi mdi-format-list-bulleted-type fs-3 text-primary"></i>
                <h4 class="fs-20 fw-bold m-0">Quản lý danh mục</h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách danh mục</h5>
                        <div>
                            <a href="{{ route('admin.categories.create') }}" class="btn btn-success shadow-sm">
                                + Thêm danh mục
                            </a>
                            <a href="{{ route('admin.categories.trash') }}" class="btn btn-danger shadow-sm">
                                <i class="fas fa-trash-restore fa-sm text-white-50"></i> Thùng rác
                            </a>
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- Bộ lọc --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <form id="perPageForm" method="GET" action="{{ route('admin.categories.index') }}"
                                class="d-flex align-items-center">
                                <label for="perPage" class="me-2 mb-0">Show</label>
                                <select name="per_page" id="perPage" class="form-select form-select-sm w-auto">
                                    @foreach ([10, 20, 50, 100] as $size)
                                        <option value="{{ $size }}"
                                            {{ request('per_page', 10) == $size ? 'selected' : '' }}>
                                            {{ $size }}
                                        </option>
                                    @endforeach
                                </select>
                                <span class="ms-2">entries</span>
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
                                <form method="GET" action="{{ route('admin.categories.index') }}">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <label class="form-label">Tên danh mục</label>
                                            <input type="text" name="name" class="form-control"
                                                value="{{ request('name') }}">
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Trạng thái</label>
                                            <select name="status" class="form-select">
                                                <option value="">Tất cả</option>
                                                <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>
                                                    Đang hoạt động</option>
                                                <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>
                                                    Không hoạt động</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Ngày từ</label>
                                            <input type="date" name="min_date" class="form-control"
                                                value="{{ request('min_date') }}">
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Ngày đến</label>
                                            <input type="date" name="max_date" class="form-control"
                                                value="{{ request('max_date') }}">
                                        </div>
                                        <div class="col-md-12 text-end">
                                            <button type="submit" class="btn btn-outline-primary">Lọc</button>
                                            <a href="{{ route('admin.categories.index') }}"
                                                class="btn btn-outline-secondary">Reset</a>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Danh sách danh mục --}}
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle">
                                <thead>
                                    <tr>
                                        <th class="text-center">#</th>
                                        <th class="text-center">Tên danh mục</th>
                                        <th class="text-center">Trạng thái</th>
                                        <th class="text-center">Ngày tạo</th>
                                        <th class="text-center">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $index => $category)
                                        <tr>
                                            <td class="text-center">{{ $index + 1 }}</td>
                                            <td class="text-center">{{ $category->name }}</td>
                                            <td class="text-center">
                                                <span
                                                    class="badge {{ $category->status ? 'bg-success' : 'bg-secondary' }}">
                                                    {{ $category->status ? 'Đang hoạt động' : 'Không hoạt động' }}
                                                </span>
                                            </td>
                                            <td class="text-center">{{ $category->created_at->format('d/m/Y') }}</td>
                                            <td class="text-center">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm btn-light" type="button"
                                                        data-bs-toggle="dropdown">
                                                        <i class="mdi mdi-dots-horizontal"></i>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-end">
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('admin.categories.show', $category->id) }}">Chi
                                                                tiết</a></li>
                                                        <li><a class="dropdown-item"
                                                                href="{{ route('admin.categories.edit', $category->id) }}">Chỉnh
                                                                sửa</a></li>
                                                        <li>
                                                            <form
                                                                action="{{ route('admin.categories.destroy', $category->slug) }}"
                                                                method="POST"
                                                                onsubmit="return confirm('Bạn có chắc chắn muốn bỏ danh mục này vào thùng rác không?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="dropdown-item text-danger">Xóa danh mục</button>
                                                            </form>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center">Không có danh mục nào.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                            {{ $categories->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- JS xử lý perPage --}}
    <script>
        document.getElementById('perPage').addEventListener('change', function() {
            document.getElementById('perPageForm').submit();
        });
    </script>

    @vite('resources/js/app.js')
@endsection
