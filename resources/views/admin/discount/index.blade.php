@extends('layouts.admin')

@section('content')
    <style>
        .filter-form label {
            font-weight: 600;
            color: #495057;
        }

        .card-table {
            border-radius: 15px;
            box-shadow: 0 6px 24px rgba(93, 81, 81, 0.05);
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

        .table-actions a,
        .table-actions form {
            display: inline-block;
            margin: 0 2px;
        }

        .table-actions button {
            border-radius: 6px;
        }

        .btn-custom {
            font-weight: 600;
            border-radius: 8px;
        }

        .section-header {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 1.5rem;
            color: #1d4583;
        }

        .card-actions .btn {
            width: 100%;
            margin-bottom: 0.5rem;
        }
    </style>

    <div class="container-fluid">
        <div class="section-header"> Quản lý mã giảm giá</div>

        <form method="GET" action="{{ route('admin.discount.index') }}" class="row g-3 mb-4 filter-form">
            <div class="col-md-3">
                <label for="code" class="form-label">Mã code</label>
                <input type="text" name="code" id="code" class="form-control"
                    value="{{ old('code', request('code')) }}" placeholder="Nhập mã code">
            </div>
            <div class="col-md-3">
                <label for="start_date" class="form-label">Ngày bắt đầu từ</label>
                <input type="date"  name="start_date" class="form-control"
                    value="{{ request('start_date') }}">
            </div>

            <div class="col-md-3">
                <label for="end_date" class="form-label">Ngày kết thúc đến</label>
                <input type="date"  name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>

            <div class="col-md-3">
                <label for="discount_value" class="form-label">Giá trị giảm</label>
                <input type="number"  name="discount_value" class="form-control"
                    value="{{ request('discount_value') }}" min="0" step="0.01" placeholder="Nhập giá trị giảm">
            </div>
            <div class="col-md-3">
                <label for="type" class="form-label">Loại giảm giá</label>
                <select name="type" id="type" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="percentage" {{ request('type') == 'percentage' ? 'selected' : '' }}>Giảm theo %</option>
                    <option value="fixed" {{ request('type') == 'fixed' ? 'selected' : '' }}>Giảm theo tiền</option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" id="status" class="form-select">
                    <option value="">-- Tất cả --</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Đang hoạt động</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Không hoạt động
                    </option>
                </select>
            </div>

            <div class="col-md-3">
                <label for="created_from" class="form-label">Từ ngày tạo</label>
                <input type="date" name="created_from" class="form-control" value="{{ request('created_from') }}">
            </div>

            <div class="col-md-3">
                <label for="created_to" class="form-label">Đến ngày tạo</label>
                <input type="date" name="created_to" class="form-control" value="{{ request('created_to') }}">
            </div>

            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary btn-custom"> Tìm kiếm</button>
                <a href="{{ route('admin.discount.index') }}" class="btn btn-secondary btn-custom"> Reset</a>
            </div>
        </form>

        <div class="row mb-4">
            <div class="col-md-4">
                <a href="{{ route('admin.discount.trash') }}" class="btn btn-outline-danger btn-custom w-100"> Thùng
                    rác</a>
            </div>
            <div class="col-md-4">
                <a href="{{ route('admin.discount.history') }}" class="btn btn-outline-secondary btn-custom w-100"> Lịch sử
                    dùng mã</a>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.discount.create') }}" class="btn btn-success btn-custom w-100"> Tạo mã giảm
                    giá</a>
            </div>
        </div>

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
                                <td class="table-actions">
                                    <a href="{{ route('admin.discount.show', $discount->id) }}"
                                        class="btn btn-sm btn-primary" title="Xem">
                                        <i data-feather="eye"></i>
                                    </a>
                                    <a href="{{ route('admin.discount.edit', $discount->id) }}"
                                        class="btn btn-sm btn-warning" title="Sửa">
                                        <i data-feather="edit"></i>
                                    </a>
                                    <form action="{{ route('admin.discount.delete', $discount->id) }}" method="POST"
                                        onsubmit="return confirm('Bạn có muốn xóa mã này không?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Xóa">
                                            <i data-feather="trash-2"></i>
                                        </button>
                                    </form>
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
