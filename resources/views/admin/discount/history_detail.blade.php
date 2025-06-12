@extends('layouts.admin')
@section('title', 'Chi tiết lịch sử mã giảm giá')

@section('content')

    <div class="container py-5">
        <div class="mb-4">
            <h2 class="fw-bold text-primary">
                <i class="bi bi-receipt-cutoff"></i> Chi tiết sử dụng mã giảm giá
            </h2>
            <p class="text-muted">Thông tin chi tiết về lượt sử dụng mã giảm giá</p>
        </div>

        <div class="card shadow-sm rounded-4 border-0">
            <div class="card-body p-4">
                <table class="table table-hover table-bordered align-middle">
                    <tbody>
                        <tr>
                            <th class="bg-light w-25">ID</th>
                            <td>{{ $usage->id }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">ID mã giảm giá</th>
                            <td>{{ $usage->discount_id ?? 'NULL' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">ID người dùng</th>
                            <td>{{ $usage->user_id ?? 'NULL' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">ID sản phẩm</th>
                            <td>{{ $usage->product_id }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Mã giảm giá</th>
                            <td class="text-uppercase text-success fw-semibold">
                                {{ $usage->discount_code }}
                            </td>
                        </tr>
                        <tr>
                            <th class="bg-light">Tên người dùng</th>
                            <td>{{ $usage->user_name }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Ngày sử dụng</th>
                            <td>{{ $usage->used_at ? \Carbon\Carbon::parse($usage->used_at)->format('d/m/Y H:i') : 'NULL' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Ngày tạo bản ghi</th>
                            <td>{{ $usage->created_at ? $usage->created_at->format('d/m/Y H:i') : 'NULL' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Ngày cập nhật</th>
                            <td>{{ $usage->updated_at ? $usage->updated_at->format('d/m/Y H:i') : 'NULL' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">Ngày xóa (soft delete)</th>
                            <td>{{ $usage->deleted_at ?? 'NULL' }}</td>
                        </tr>
                        <tr>
                            <th class="bg-light">ID đơn hàng</th>
                            <td>{{ $usage->order_id }}</td>
                        </tr>
                    </tbody>
                </table>

                <div class="mt-4">
                    <a href="{{ route('admin.discount.history') }}" class="btn btn-outline-primary">
                        <i class="bi bi-arrow-left-circle"></i> Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
