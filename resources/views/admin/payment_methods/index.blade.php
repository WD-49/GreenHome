@extends('layouts.admin')

@section('content')
    <style>
        .section-header {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            color: #1d4583;
        }

        .card-table {
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        .table th {
            background: #1c4077;
            color: #fff;
            vertical-align: middle;
            text-align: center;
        }

        .table td {
            vertical-align: middle;
            text-align: center;
        }

        .btn-custom {
            font-weight: 600;
            border-radius: 8px;
        }

        .filter-dropdown {
            background: #f8f9fa;
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1rem;
        }

        .table-actions a,
        .table-actions form {
            display: inline-block;
        }

        .table-actions button {
            border-radius: 6px;
        }
    </style>

    <div class="container-fluid">
        <div class="section-header">Quản lý phương thức thanh toán</div>

        {{-- Tabs trạng thái --}}
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

        {{-- Bộ lọc nâng cao --}}
        <div class="mb-4">
            <button class="btn btn-outline-primary btn-custom" type="button" data-bs-toggle="collapse"
                    data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                Bộ lọc nâng cao
            </button>
            <div class="collapse mt-3" id="filterCollapse">
                <div class="filter-dropdown">
                    <form method="GET" action="{{ route('admin.paymentMethods.index') }}" class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label">Tên phương thức</label>
                            <input type="text" name="name" id="name" class="form-control" placeholder="Nhập tên phương thức"
                                   value="{{ request('name') }}">
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select name="status" id="status" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Kích hoạt</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Tạm tắt</option>
                            </select>
                        </div>
                        <div class="col-12 text-end mt-3">
                            <button type="submit" class="btn btn-primary btn-custom">Tìm kiếm</button>
                            <a href="{{ route('admin.paymentMethods.index') }}" class="btn btn-secondary btn-custom">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Nút thêm mới --}}
        <div class="mb-4 text-end">
            <a href="{{ route('admin.paymentMethods.create') }}" class="btn btn-success btn-custom">
                <i data-feather="plus" class="me-1"></i> Thêm phương thức
            </a>
        </div>

        {{-- Bảng danh sách --}}
        <div class="card card-table">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên phương thức</th>
                            <th>Mô tả</th>
                            <th>Trạng thái</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($paymentMethods as $method)
                            <tr>
                                <td>{{ $method->id }}</td>
                                <td>{{ $method->name }}</td>
                                <td>{!! $method->description !!}</td>
                                <td>
                                    <span class="badge {{ $method->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $method->status ? 'Kích hoạt' : 'Tạm tắt' }}
                                    </span>
                                </td>
                                <td class="position-relative">
                                    <div class="d-flex justify-content-center align-items-center" style="height: 100%;">
                                        <div class="dropdown">
                                            <button type="button"
                                                class="btn btn-sm border border-primary text-primary bg-white rounded-circle d-flex align-items-center justify-content-center p-0"
                                                style="width: 36px; height: 36px;" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i data-feather="more-horizontal" style="width: 20px; height: 20px;"></i>
                                            </button>

                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 px-2 py-2" style="min-width: 140px;">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 text-primary"
                                                       href="{{ route('admin.paymentMethods.show', $method->id) }}">
                                                        <i data-feather="eye" width="16" height="16"></i>
                                                        <span>Xem</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center gap-2 text-warning"
                                                       href="{{ route('admin.paymentMethods.edit', $method->id) }}">
                                                        <i data-feather="edit-3" width="16" height="16"></i>
                                                        <span>Sửa</span>
                                                    </a>
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.paymentMethods.destroy', $method->id) }}"
                                                          method="POST"
                                                          onsubmit="return confirm('Bạn có chắc chắn muốn xóa phương thức thanh toán này?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                                class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                                            <i data-feather="trash" width="16" height="16"></i>
                                                            <span>Xóa</span>
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-danger">Không tìm thấy phương thức thanh toán nào.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Phân trang --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $paymentMethods->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        feather.replace();
    </script>
@endpush
