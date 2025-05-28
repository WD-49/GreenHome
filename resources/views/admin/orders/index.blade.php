@extends('layouts.admin')

@section('content')
    <div class="container mt-5">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top-4">
                <h3 class="mb-0 fw-bold">Danh sách đơn hàng</h3>
                <div>
                    <a href="{{ route('admin.orders.trash') }}" class="btn btn-outline-light btn-sm me-2"
                        data-bs-toggle="tooltip" title="Xem thùng rác">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                    <a href="{{ route('admin.orders.create') }}" class="btn btn-light btn-sm fw-semibold"
                        data-bs-toggle="tooltip" title="Tạo đơn hàng mới">
                        <i class="fas fa-plus-circle me-1"></i> Tạo đơn hàng
                    </a>
                </div>
            </div>

            <form method="GET" action="{{ route('admin.orders.index') }}" class="card-body">
                <div class="row gx-2 gy-2 align-items-center">
                    <div class="col-auto" style="min-width: 120px;">
                        <input type="text" name="order_code" class="form-control form-control-sm" placeholder="Mã đơn"
                            value="{{ request('order_code') }}">
                    </div>
                    <div class="col-auto" style="min-width: 140px;">
                        <input type="text" name="customer_name" class="form-control form-control-sm"
                            placeholder="Khách hàng" value="{{ request('customer_name') }}">
                    </div>
                    <div class="col-auto" style="min-width: 140px;">
                        <select name="payment_status" class="form-select form-select-sm">
                            <option value="">Trạng thái thanh toán</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán
                            </option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Chờ thanh
                                toán</option>
                            <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Thanh toán
                                thất bại</option>
                        </select>
                    </div>
                    <div class="col-auto" style="min-width: 140px;">
                        <select name="order_status" class="form-select form-select-sm">
                            <option value="">Trạng thái đơn hàng</option>
                            @foreach ($orderStatuses as $status)
                                <option value="{{ $status->id }}"
                                    {{ request('order_status') == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto" style="min-width: 130px;">
                        <input type="date" name="date_from" class="form-control form-control-sm"
                            value="{{ request('date_from') }}" placeholder="Từ ngày">
                    </div>
                    <div class="col-auto" style="min-width: 130px;">
                        <input type="date" name="date_to" class="form-control form-control-sm"
                            value="{{ request('date_to') }}" placeholder="Đến ngày">
                    </div>
                    <div class="col-auto" style="min-width: 150px;">
                        <select name="payment_method" class="form-select form-select-sm">
                            <option value="">Phương thức thanh toán</option>
                            @foreach ($paymentMethods as $method)
                                <option value="{{ $method->id }}"
                                    {{ request('payment_method') == $method->id ? 'selected' : '' }}>
                                    {{ $method->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm fw-semibold">Lọc</button>
                        <a href="{{ route('admin.orders.index') }}"
                            class="btn btn-outline-secondary btn-sm fw-semibold">Đặt lại</a>
                    </div>
                </div>
            </form>


            <div class="card-body pt-0">
                <div class="table-responsive shadow-sm rounded-4">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th scope="col" class="text-nowrap">STT</th>
                                <th scope="col" class="text-nowrap">Mã đơn</th>
                                <th scope="col" class="text-nowrap">Khách hàng</th>
                                <th scope="col" class="text-nowrap">Tên người nhận</th>
                                <th scope="col" class="text-nowrap">Ngày đặt</th>
                                <th scope="col" class="text-nowrap">Tổng tiền</th>
                                <th scope="col" class="text-nowrap">Phương thức</th>
                                <th scope="col" class="text-nowrap">Trạng thái</th>
                                <th scope="col" class="text-nowrap">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $index => $order)
                                <tr>
                                    <td class="text-center">{{ $index + 1 }}</td>
                                    <td class="text-primary fw-bold">#{{ $order->sku ?? $order->id }}</td>
                                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                                    <td>{{ $order->shipping_name }}</td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-end fw-semibold text-success">
                                        {{ number_format($order->total_amount, 0) }} VND</td>
                                    <td class="text-capitalize">{{ $order->paymentMethod->name }}</td>
                                    <td class="text-center">
                                        <span
                                            class="badge rounded-pill 
                                        @if ($order->status->name == 'Hoàn tất') bg-success
                                        @elseif ($order->status->name == 'Đang xử lý') bg-warning text-dark
                                        @else bg-info text-dark @endif">
                                            {{ $order->status->name ?? 'Chưa cập nhật' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="{{ route('admin.orders.edit', $order->id) }}"
                                                class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip"
                                                title="Sửa">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.orders.show', $order->id) }}"
                                                class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip"
                                                title="Xem">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <form action="{{ route('admin.orders.destroy', $order->id) }}" method="POST"
                                                onsubmit="return confirm('Bạn có chắc muốn xóa đơn hàng này?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    data-bs-toggle="tooltip" title="Xóa">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">Không có đơn hàng nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    {{ $orders->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Enable Bootstrap 5 tooltips --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
@endsection
