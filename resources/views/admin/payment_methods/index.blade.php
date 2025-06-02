@extends('layouts.admin')

@section('title')
    Danh sách phương thức thanh toán
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
    <div class="container mt-4">
        <h2 class="text-center mb-4">Danh sách phương thức thanh toán</h2>

        <!-- Tabs trạng thái (tuỳ chọn, có thể bỏ nếu không filter theo trạng thái) -->
        <ul class="nav nav-pills mb-3">
            <li class="nav-item">
                <a class="nav-link {{ request('status') == null ? 'active' : '' }}"
                    href="{{ route('admin.paymentMethods.index') }}">
                    Tất cả ({{ $paymentAll->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') == 'active' ? 'active' : '' }}"
                    href="{{ route('admin.paymentMethods.index', ['status' => 'active']) }}">
                    Kích hoạt ({{ $paymentActive->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request('status') == 'inactive' ? 'active' : '' }}"
                    href="{{ route('admin.paymentMethods.index', ['status' => 'inactive']) }}">
                    Tạm tắt ({{ $paymentInactive->count() }})
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link {{ request()->routeIs('admin.paymentMethods.trash') ? 'active' : '' }}"
                    href="{{ route('admin.paymentMethods.trash') }}">
                    Thùng rác ({{ $paymentTrashed->count() }})
                </a>
            </li>
        </ul>

        <!-- Bộ lọc & nút thêm -->
        <div class="card">
            <div class="card-body">
                <div class="d-md-flex align-items-center">
                    <div>
                        <h4 class="card-title mb-1">Danh sách phương thức thanh toán</h4>
                        <p class="card-subtitle">Quản lý các phương thức thanh toán trong hệ thống</p>
                    </div>
                    <div class="ms-auto mt-3 mt-md-0">
                        <a href="{{ route('admin.paymentMethods.create') }}" class="btn btn-primary">
                            <i class="fa-solid fa-plus me-1"></i> Thêm phương thức
                        </a>
                        <a class="btn btn-outline-secondary dropdown-toggle ms-2" type="button" id="filterDropdown"
                            data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">
                            <i class="fas fa-filter me-1"></i> Bộ lọc
                        </a>
                        <div class="dropdown-menu p-4" style="min-width: 400px;" aria-labelledby="filterDropdown">
                            <form method="GET" action="{{ route('admin.paymentMethods.index') }}" class="row g-3">
                                <div class="col-md-6">
                                    <label for="name" class="form-label">Tên phương thức</label>
                                    <input type="text" name="name" id="name" class="form-control"
                                        placeholder="Nhập tên phương thức" value="{{ request('name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Trạng thái</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="">-- Tất cả --</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Kích
                                            hoạt</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                            Tạm tắt</option>
                                    </select>
                                </div>
                                <div class="col-md-12 d-flex justify-content-end gap-2">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-search me-1"></i> Tìm kiếm
                                    </button>
                                    <a href="{{ route('admin.paymentMethods.index') }}" class="btn btn-warning">
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
                                <th class="px-0 text-muted">Tên phương thức</th>
                                <th class="px-0 text-muted">Mô tả</th>
                                <th class="px-0 text-muted">Trạng thái</th>
                                <th class="px-0 text-muted text-end">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($paymentMethods as $method)
                                <tr>
                                    <td class="px-0">{{ $method->id }}</td>
                                    <td class="px-0">{{ $method->name }}</td>
                                    <td class="px-0">{!! $method->description !!}</td>
                                    <td class="px-0">
                                        <span class="badge {{ $method->status ? 'bg-success' : 'bg-danger' }}">
                                            {{ $method->status ? 'Kích hoạt' : 'Tạm tắt' }}
                                        </span>
                                    </td>
                                    <td class="px-0 text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-light btn-sm" type="button"
                                                id="dropdownMenuButton{{ $method->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
           

                                            <ul class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="dropdownMenuButton{{ $method->id }}">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.paymentMethods.show', $method->id) }}">
                                                        Xem chi tiết
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.paymentMethods.edit', $method->id) }}">
                                                        Chỉnh sửa
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.paymentMethods.destroy', $method->id) }}"
                                                        method="POST" class="d-inline"
                                                        onsubmit="return confirm('Bạn có chắc chắn muốn bỏ phương thức thanh toán này vào thùng rác không?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            Xóa
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Không có dữ liệu</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{-- Phân trang --}}
                @if ($paymentMethods->lastPage() > 1)
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-end mt-3 mb-0">
                            <li class="page-item {{ $paymentMethods->onFirstPage() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $paymentMethods->previousPageUrl() }}">Previous</a>
                            </li>
                            @for ($i = 1; $i <= $paymentMethods->lastPage(); $i++)
                                <li class="page-item {{ $i == $paymentMethods->currentPage() ? 'active' : '' }}">
                                    <a class="page-link" href="{{ $paymentMethods->url($i) }}">{{ $i }}</a>
                                </li>
                            @endfor
                            <li class="page-item {{ !$paymentMethods->hasMorePages() ? 'disabled' : '' }}">
                                <a class="page-link" href="{{ $paymentMethods->nextPageUrl() }}">Next</a>
                            </li>
                        </ul>
                    </nav>
                @endif
            </div>
        </div>
    </div>
@endsection
