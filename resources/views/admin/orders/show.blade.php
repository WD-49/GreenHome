@extends('layouts.admin')

@section('content')
    <div class="container py-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Chi tiết đơn hàng #{{ $order->sku }}</h3>
            </div>
            <div class="card-body">
                {{-- Mã đơn hàng --}}
                <p><strong>Mã đơn hàng:</strong> {{ $order->sku ?? $order->id }}</p>

                {{-- Thông tin người đặt & người nhận --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>👤 Thông tin người đặt</h5>
                        <p><strong>Họ tên:</strong> {{ $order->user->name }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->user->profile->phone ?? 'Chưa có số điện thoại' }}</p>
                        <p><strong>Email:</strong> {{ $order->user->email }}</p>
                    </div>
                    <div class="col-md-6">
                        <h5>📦 Thông tin người nhận</h5>
                        <p><strong>Họ tên:</strong> {{ $order->shipping_name }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->shipping_phone }}</p>
                        <p><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>
                        <p><strong>Ngày đặt:</strong>
                            {{ optional($order->created_at)->format('d/m/Y H:i') ?? 'Chưa xác định' }}</p>
                    </div>
                </div>

                {{-- Trạng thái đơn hàng & Trạng thái thanh toán --}}
                <div class="d-flex flex-wrap gap-5 mb-4">

                    {{-- Trạng thái đơn hàng --}}
                    <div>
                        <h5 class="mb-3">📌 Trạng thái đơn hàng</h5>
                        <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="d-flex align-items-center gap-3">
                                <select name="status_id" class="form-select w-auto">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}"
                                            {{ $order->status_id == $status->id ? 'selected' : '' }}>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn btn-sm btn-primary">
                                    <i class="bi bi-arrow-repeat me-1"></i> Cập nhật
                                </button>
                            </div>
                        </form>

                        @if ($order->status->name === 'Đã hủy' && $order->cancel_reason)
                            <div class="mt-2 text-danger">
                                <strong>❌ Lý do huỷ:</strong> {{ $order->cancel_reason }}
                            </div>
                        @endif
                    </div>

                    {{-- Trạng thái thanh toán --}}
                    <div>
                        <h5 class="mb-3">💳 Trạng thái thanh toán</h5>
                        <form method="POST" action="{{ route('admin.orders.updatePaymentStatus', $order->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="d-flex align-items-center gap-3">
                                <select name="payment_status" class="form-select w-auto">
                                    <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>
                                        Chờ thanh toán
                                    </option>
                                    <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>
                                        Đã thanh toán
                                    </option>
                                    <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>
                                        Thanh toán thất bại
                                    </option>
                                </select>
                                <button class="btn btn-sm btn-secondary">
                                    <i class="bi bi-arrow-repeat me-1"></i> Cập nhật
                                </button>
                            </div>
                        </form>
                    </div>

                </div>

                @php
                    $totalOrderAmount = 0;
                @endphp

                {{-- Danh sách sản phẩm --}}
                <h5 class="mb-3">🛒 Sản phẩm trong đơn hàng</h5>
                <div class="table-responsive mb-4">
                    <table class="table table-bordered text-center align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Tên sản phẩm</th>
                                <th>Mã biến thể</th>
                                <th>Giá gốc</th>
                                <th>Giá đặt mua</th>
                                <th>Số lượng</th>
                                <th>Giảm giá (sp)</th>
                                <th>Tổng giá</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->productVariant->product->name }}</td>
                                    <td>{{ $item->productVariant->sku }}</td>
                                    <td>{{ number_format($item->productVariant->price, 0, ',', '.') }} VND</td>
                                    <td>{{ number_format($item->unit_price, 0, ',', '.') }} VND</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>
                                        @if ($item->discount_id > 0)
                                            -{{ number_format($item->discount_id, 0, ',', '.') }} VND
                                        @else
                                            Không có
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->total_price, 0, ',', '.') }} VND</td>
                                    @php
                                        $totalOrderAmount += $item->total_price;
                                    @endphp
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mã giảm giá & Tổng tiền --}}
                {{-- Mã giảm giá & Tổng tiền --}}
                @php
                    $discountAmount = $order->discount_amount ?? 0;
                    $shippingFee = $order->shipping_fee ?? 0;
                    $finalAmount = $totalOrderAmount + $shippingFee - $discountAmount;
                @endphp

                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Tổng tiền hàng</th>
                                        <td>{{ number_format($totalOrderAmount, 0, ',', '.') }} VND</td>
                                    </tr>
                                    <tr>
                                        <th>Phí vận chuyển</th>
                                        <td>{{ number_format($shippingFee, 0, ',', '.') }} VND</td>
                                    </tr>
                                    <tr>
                                        <th>Mã giảm giá ({{ $order->discount->code ?? 'Không áp dụng' }})</th>
                                        @if ($discountAmount > 0)
                                            <td>
                                                -{{ number_format($discountAmount, 0, ',', '.') }} VND
                                                <br>
                                                <small>({{ $order->discount->type == 'order' ? 'Áp dụng toàn đơn' : 'Áp dụng sản phẩm' }})</small>
                                            </td>
                                        @endif
                                        @if ($discountAmount <= 0)
                                            <td>Không áp dụng</td>
                                        @endif
                                    </tr>


                                    <tr class="table-success">
                                        <th>Tổng thanh toán</th>
                                        <td><strong>{{ number_format($finalAmount, 0, ',', '.') }} VND</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-end">
                            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-primary">
                                ← Quay lại danh sách đơn hàng
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection