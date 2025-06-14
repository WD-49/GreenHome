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

        .dropdown-toggle::after {
            margin-left: 10px;
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
        <div class="section-header">Quản lý mã giảm giá</div>

        {{-- Dropdown filter --}}
        <div class="mb-4">
            <button class="btn btn-outline-primary btn-custom" type="button" data-bs-toggle="collapse"
                data-bs-target="#filterCollapse" aria-expanded="false" aria-controls="filterCollapse">
                Bộ lọc nâng cao
            </button>
            <div class="collapse mt-3" id="filterCollapse">
                <div class="filter-dropdown">
                    <form method="GET" action="{{ route('admin.discount.index') }}" class="row g-3">
                        <div class="col-md-3">
                            <label for="code" class="form-label">Mã code</label>
                            <input type="text" name="code" class="form-control" placeholder="Nhập mã code"
                                value="{{ request('code') }}">
                        </div>
                        <div class="col-md-3">
                            <label for="discount_value" class="form-label">Giá trị giảm</label>
                            <input type="number" name="discount_value" class="form-control" min="0" step="0.01"
                                value="{{ request('discount_value') }}" placeholder="Giá trị giảm">
                        </div>
                        <div class="col-md-3">
                            <label for="type" class="form-label">Loại giảm giá</label>
                            <select name="type" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>
                                    Giảm theo %</option>
                                <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>
                                    Giảm theo tiền</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="status" class="form-label">Trạng thái</label>
                            <select name="status" class="form-select">
                                <option value="">-- Tất cả --</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                    Đang hoạt động</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                    Không hoạt động</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ngày bắt đầu từ</label>
                            <input type="date" name="start_date" class="form-control"
                                value="{{ request('start_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Ngày kết thúc đến</label>
                            <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Từ ngày tạo</label>
                            <input type="date" name="created_from" class="form-control"
                                value="{{ request('created_from') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Đến ngày tạo</label>
                            <input type="date" name="created_to" class="form-control"
                                value="{{ request('created_to') }}">
                        </div>

                        <div class="col-12 text-end mt-3">
                            <button type="submit" class="btn btn-primary btn-custom">Tìm kiếm</button>
                            <a href="{{ route('admin.discount.index') }}" class="btn btn-secondary btn-custom">Reset</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Action buttons --}}
        <div class="row mb-4">
            <div class="col-md-4">
                <a href="{{ route('admin.discount.trash') }}" class="btn btn-outline-danger btn-custom w-100">
                    Thùng rác
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.discount.history') }}" class="btn btn-outline-secondary btn-custom w-100">
                    Lịch sử dùng mã
                </a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.discount.create') }}" class="btn btn-success btn-custom w-100">
                    Tạo mã giảm giá
                </a>
            </div>
        </div>

        {{-- Table --}}
        <div class="card card-table">
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead>
                        <tr>
                            <th>STT</th>
                            <th>Tiêu đề</th>
                            <th>Mã code</th>
                            <th>Loại</th>
                            <th>Giá trị</th>
                            <th>Trạng thái</th>
                            <th>Bắt đầu</th>
                            <th>Kết thúc</th>
                            <th>Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if ($notFound)
                            <tr>
                                <td colspan="9" class="text-center text-danger">Không tìm thấy mã giảm giá nào phù hợp.
                                </td>
                            </tr>
                        @endif

                        @foreach ($discounts as $index => $discount)
                            <tr>
                                <td>{{ ($discounts->currentPage() - 1) * $discounts->perPage() + $index + 1 }}</td>
                                <td>{{ $discount->title }}</td>
                                <td><span class="badge bg-info">{{ $discount->code }}</span></td>
                                <td>{{ $discount->discount_type == 'percentage' ? 'Phần trăm' : 'Cố định' }}</td>
                                <td>{{ $discount->discount_value }}{{ $discount->discount_type == 'percentage' ? '%' : 'đ' }}
                                </td>
                                <td>
                                    <span
                                        class="badge {{ $discount->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $discount->status == 'active' ? 'Đang hoạt động' : 'Không hoạt động' }}
                                    </span>
                                </td>
                                <td>{{ $discount->start_date ? \Carbon\Carbon::parse($discount->start_date)->format('d/m/Y') : 'Không có' }}
                                </td>
                                <td>{{ $discount->end_date ? \Carbon\Carbon::parse($discount->end_date)->format('d/m/Y') : 'Không có' }}
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

            <ul class="dropdown-menu dropdown-menu-end shadow-sm rounded-3 px-2 py-2"
                style="min-width: 140px;">
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 text-primary"
                        href="{{ route('admin.discount.show', $discount->id) }}">
                        <i data-feather="eye" width="16" height="16"></i>
                        <span>Xem</span>
                    </a>
                </li>
                <li>
                    <a class="dropdown-item d-flex align-items-center gap-2 text-warning"
                        href="{{ route('admin.discount.edit', $discount->id) }}">
                        <i data-feather="edit-3" width="16" height="16"></i>
                        <span>Sửa</span>
                    </a>
                </li>
                <li>
                    <form action="{{ route('admin.discount.delete', $discount->id) }}"
                        method="POST"
                        onsubmit="return confirm('Bạn có muốn xóa mã này không?')">
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $discounts->appends(request()->query())->links('pagination::bootstrap-4') }}
        </div>

    </div>
@endsection
