@extends('layouts.admin')

@section('content')
    <div class="container py-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Chi tiết đơn hàng #{{ $order->id }}</h3>
            </div>
            <div class="card-body">
                {{-- Thông tin người đặt & người nhận --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>👤 Thông tin người đặt</h5>
                        <p><strong>Họ tên:</strong> {{ $order->user->name }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->user->profile->phone }}</p>
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

                {{-- Trạng thái đơn hàng và lý do huỷ nếu có --}}
                <div class="mb-4">
                    <h5>📌 Trạng thái đơn hàng</h5>
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
                            <button class="btn btn-sm btn-primary">Cập nhật</button>
                        </div>
                    </form>

                    @if ($order->status->name === 'Đã hủy' && $order->cancel_reason)
                        <p class="mt-2 text-danger"><strong>Lý do huỷ:</strong> {{ $order->cancel_reason }}</p>
                    @endif
                </div>

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
                                        @if ($item->discount_amount > 0)
                                            -{{ number_format($item->discount_amount, 0, ',', '.') }} VND
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{ number_format($item->total_price, 0, ',', '.') }} VND</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mã giảm giá & Tổng tiền --}}
                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Tổng tiền hàng</th>
                                        <td>{{ number_format($order->subtotal_amount, 0, ',', '.') }} VND</td>
                                    </tr>
                                    <tr>
                                        <th>Phí vận chuyển</th>
                                        <td>{{ number_format($order->shipping_fee, 0, ',', '.') }} VND</td>
                                    </tr>
                                    @if ($order->discount)
                                        <tr>
                                            <th>Mã giảm giá ({{ $order->discount->code }})</th>
                                            <td>
                                                -{{ number_format($order->discount->amount, 0, ',', '.') }} VND
                                                <br>
                                                <small>({{ $order->discount->type == 'order' ? 'Áp dụng toàn đơn' : 'Áp dụng sản phẩm' }})</small>
                                            </td>
                                        </tr>
                                    @endif
                                    <tr class="table-success">
                                        <th>Tổng thanh toán</th>
                                        <td><strong>{{ number_format($order->total_amount, 0, ',', '.') }} VND</strong>
                                        </td>
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
