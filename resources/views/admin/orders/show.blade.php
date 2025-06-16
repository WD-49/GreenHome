@extends('layouts.admin')

@section('content')
    <div class="container py-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Chi tiết đơn hàng #{{ $order->sku }}</h3>
            </div>
            <div class="card-body">
                {{-- Mã đơn hàng - Trạng thái đơn hàng --}}
                <p class="fs-5"><strong>Mã đơn hàng:</strong> {{ $order->sku ?? $order->id }} |
                    <strong>Trạng thái đơn hàng:</strong> {{ $order->order_status }}
                </p>

                {{-- Thông tin người đặt & người nhận --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5><b>Thông tin người đặt</b></h5>
                        @if ($order->user)
                            {{-- Kiểm tra nếu đối tượng user tồn tại --}}
                            <p><strong>Họ tên:</strong> {{ $order->user->name ?? 'Người dùng không tồn tại' }}</p>
                            <p><strong>Email:</strong> {{ $order->user->email ?? 'Không có Email' }}</p>

                            {{-- Kiểm tra nếu profile tồn tại trước khi truy cập phone --}}
                            <p><strong>Số điện thoại:</strong>
                                {{ optional($order->user->profile)->phone ?? 'Chưa có số điện thoại' }}</p>
                        @else
                            <p class="text-danger">Người dùng đặt hàng không tồn tại hoặc đã bị xóa.</p>
                            <p><strong>Họ tên:</strong> Người dùng ẩn danh</p>
                            <p><strong>Email:</strong> N/A</p>
                            <p><strong>Số điện thoại:</strong> N/A</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h5><b>Thông tin người nhận</b></h5>
                        <p><strong>Họ tên:</strong> {{ $order->shipping_name }}</p>
                        <p><strong>Số điện thoại:</strong> {{ $order->shipping_phone }}</p>
                        <p><strong>Địa chỉ:</strong> {{ $order->shipping_address }}</p>
                        <p><strong>Ngày đặt:</strong>
                            {{ optional($order->created_at)->format('d/m/Y H:i') ?? 'Chưa xác định' }}</p>
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
                                <th>Loại sản phẩm</th>
                                <th>Giá đặt mua</th>
                                <th>Số lượng</th>
                                <th>Tổng giá</th>
                                <th>Giảm giá (sp)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->product_attribute }}</td>
                                    <td>{{ number_format($item->productVariant->price, 0, ',', '.') }} VND</td>
                                    <td>{{ $item->quantity }}</td>
                                    <td>{{ number_format($item->total_price, 0, ',', '.') }} VND</td>

                                    <td>
                                        @if ($item->discount_amount > 0)
                                            -{{ number_format($item->discount_amount, 0, ',', '.') }} VND
                                        @else
                                            Không có
                                        @endif
                                    </td>
                                    @php
                                        $totalOrderAmount += $item->total_price;
                                    @endphp
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @php
                    $discountAmount = $order->discount_amount ?? 0; // Số tiền giảm giá của toàn đơn hàng
                    $shippingFee = $order->shipping_fee ?? 0; // Phí vận chuyển

                    // Tính toán lại finalAmount theo logic mới
                    $subtotalAfterDiscount = $totalOrderAmount - $discountAmount; // Tiền hàng sau khi trừ mã giảm giá
                    $finalAmount = $subtotalAfterDiscount + $shippingFee; // Cộng thêm phí ship

                    // Đảm bảo tổng cuối cùng không âm (trường hợp hiếm khi giảm giá > tiền hàng)
                    $finalAmount = max(0, $finalAmount);
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
                                        <th>Mã giảm giá ({{ optional($order->discount)->code ?? 'Không áp dụng' }}). <br>
                                            {{ optional($order->discount)->description }}</th>
                                        @if ($discountAmount > 0)
                                            <td>
                                                - {{ number_format($discountAmount, 0, ',', '.') }} VND
                                                <br>
                                                <p>
                                                    {{-- Áp dụng cho tất cả sản phẩm: --}}
                                                    {{-- Kiểm tra nếu discount tồn tại VÀ discount đó có thuộc tính applies_to_all_products là true --}}
                                                    {{-- {{ optional($order->discount)->applies_to_all_products ? 'Có' : (optional($order->discount) ? 'Không' : 'N/A') }} --}}
                                                </p>
                                            </td>
                                        @endif
                                        @if ($discountAmount <= 0)
                                            <td>Không áp dụng</td>
                                        @endif
                                    </tr>
                                    <tr>
                                        <th>Phí vận chuyển</th>
                                        <td>{{ number_format($shippingFee, 0, ',', '.') }} VND</td>
                                    </tr>


                                    <tr class="table-success">
                                        <th>Tổng thanh toán</th>
                                        <td><strong>{{ number_format($order->total_amount, 0, ',', '.') }} VND</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Phương thức thanh toán</th>
                                        <td>
                                            {{ $order->payment_method_name }}
                                            @php
                                                $paymentStatuses = [
                                                    'pending' => 'Chờ thanh toán',
                                                    'paid' => 'Đã thanh toán',
                                                    'failed' => 'Thất bại',
                                                ];
                                                $currentStatusKey = old('payment_status', $order->payment_status);
                                                $displayStatus =
                                                    $paymentStatuses[$currentStatusKey] ?? 'Không xác định';

                                                // Thêm class màu sắc cho trạng thái nếu cần
                                                $statusColorClass = '';
                                                switch ($currentStatusKey) {
                                                    case 'pending':
                                                        $statusColorClass = 'text-warning'; // Màu vàng cho chờ thanh toán
                                                        break;
                                                    case 'paid':
                                                        $statusColorClass = 'text-success'; // Màu xanh lá cho đã thanh toán
                                                        break;
                                                    case 'failed':
                                                        $statusColorClass = 'text-danger'; // Màu đỏ cho thất bại
                                                        break;
                                                }
                                            @endphp
                                            @if (!empty($displayStatus))
                                                - <span
                                                    class="{{ $statusColorClass }}"><strong>{{ $displayStatus }}</strong></span>
                                                {{-- In đậm và thêm màu cho trạng thái --}}
                                            @endif
                                        </td>
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
    <!-- Modal Lý do Hủy Đơn Hàng -->
    <div class="modal fade" id="cancelReasonModal" tabindex="-1" aria-labelledby="cancelReasonModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelReasonModalLabel">Xác nhận Hủy Đơn Hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn đang chọn hủy đơn hàng này. Vui lòng nhập lý do hủy:</p>
                    <textarea class="form-control" id="cancel_reason_text" rows="3" placeholder="Nhập lý do hủy đơn hàng..."></textarea>
                    <small id="cancelReasonError" class="text-danger d-none">Vui lòng nhập lý do hủy.</small>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-danger" id="confirmCancelOrderBtn">Xác nhận và Hủy Đơn</button>
                </div>
            </div>
        </div>
    </div>
@endsection
