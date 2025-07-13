@extends('layouts.admin')
@section('title', 'Sửa Đơn Hàng #' . ($order->sku ?? $order->id))

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="../../assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />
    <link href="../../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
@endpush

@section('content')
    <div class="container-xxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Sửa Đơn Hàng <b>#{{ $order->sku ?? $order->id }}</b></h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.orders.index') }}">Đơn Hàng</a></li>
                    <li class="breadcrumb-item active">Sửa</li>
                </ol>
            </div>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <h5>Đã có lỗi xảy ra:</h5>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Thông tin đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.orders.update', $order->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                {{-- Cột thông tin giao hàng --}}
                                <div class="col-lg-6">
                                    <h6 class="mb-3 text-muted">Thông tin Giao hàng</h6>
                                    <div class="mb-3">
                                        <label for="shipping_name" class="form-label">Tên người nhận:</label>
                                        <input type="text" class="form-control" id="shipping_name" name="shipping_name"
                                            value="{{ old('shipping_name', $order->shipping_name) }}" required>
                                        @error('shipping_name')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="shipping_phone" class="form-label">Số điện thoại:</label>
                                        <input type="text" class="form-control" id="shipping_phone" name="shipping_phone"
                                            value="{{ old('shipping_phone', $order->shipping_phone) }}" required>
                                        @error('shipping_phone')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="shipping_address" class="form-label">Địa chỉ:</label>
                                        <input type="text" class="form-control" id="shipping_address"
                                            name="shipping_address"
                                            value="{{ old('shipping_address', $order->shipping_address) }}" required>
                                        @error('shipping_address')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="note" class="form-label">Ghi chú:</label>
                                        <textarea class="form-control" id="note" name="note" rows="3">{{ old('note', $order->note) }}</textarea>
                                        @error('note')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Cột thông tin đơn hàng & trạng thái --}}
                                <div class="col-lg-6">
                                    <h6 class="mb-3 text-muted">Chi tiết Đơn hàng</h6>

                                    {{-- Trạng thái đơn hàng --}}
                                    <div class="mb-3">
                                        <label for="order_status" class="form-label">Trạng thái đơn hàng:</label>
                                        <select class="form-select" id="order_status" name="order_status" required
                                            data-current-status="{{ $order->order_status }}"> {{-- Thêm data attribute --}}
                                            @foreach ($allOrderStatuses as $status)
                                                <option value="{{ $status }}"
                                                    {{ old('order_status', $order->order_status) == $status ? 'selected' : '' }}>
                                                    {{ $status }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('order_status')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Lý do hủy (sẽ hiển thị/ẩn bằng JS) --}}
                                    <div class="mb-3" id="cancel_reason_field"
                                        style="display: {{ old('order_status', $order->order_status) === 'Hủy đơn' || $errors->has('cancel_reason') ? 'block' : 'none' }}">
                                        <label for="cancel_reason" class="form-label">Lý do hủy (<span
                                                class="text-danger">*</span>):</label> {{-- Thêm dấu sao cho bắt buộc --}}
                                        <textarea class="form-control" id="cancel_reason" name="cancel_reason" rows="3" minlength="10"
                                            {{ old('order_status', $order->order_status) === 'Hủy đơn' ?: '' }}>{{ old('cancel_reason', $order->cancel_reason) }}</textarea> {{-- Thêm minlength và required --}}
                                        @error('cancel_reason')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    {{-- Trạng thái thanh toán --}}
                                    <div class="mb-3">
                                        <label for="payment_status" class="form-label">Trạng thái thanh toán:</label>
                                        <select class="form-select" id="payment_status" name="payment_status" required
                                            data-current-payment-status="{{ $order->payment_status }}">
                                            @foreach ($paymentStatuses as $pStatus)
                                                <option value="{{ $pStatus }}"
                                                    {{ old('payment_status', $order->payment_status) == $pStatus ? 'selected' : '' }}>
                                                    @php
                                                        echo [
                                                            'pending' => 'Chờ thanh toán',
                                                            'paid' => 'Đã thanh toán',
                                                            'failed' => 'Thất bại',
                                                        ][$pStatus] ?? 'Không xác định';
                                                    @endphp
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('payment_status')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="payment_method_name" class="form-label">Phương thức thanh
                                            toán:</label>
                                        <select class="form-select" id="payment_method_name" name="payment_method_name"
                                            required>
                                            @php
                                                $methods = [
                                                    'Thanh toán khi nhận hàng (COD)',
                                                    'Chuyển khoản ngân hàng',
                                                    'Thanh toán qua Momo',
                                                ];
                                            @endphp

                                            @foreach ($methods as $method)
                                                <option value="{{ $method }}"
                                                    {{ old('payment_method_name', $order->payment_method_name ?? '') == $method ? 'selected' : '' }}>
                                                    {{ $method }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('payment_method_name')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="shipping_fee" class="form-label">Phí vận chuyển:</label>
                                        <input type="number" step="0.01" class="form-control" id="shipping_fee"
                                            name="shipping_fee" value="{{ old('shipping_fee', $order->shipping_fee) }}"
                                            required min="0">
                                        @error('shipping_fee')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="total_amount" class="form-label">Tổng tiền:</label>
                                        <input type="number" step="0.01" class="form-control" id="total_amount"
                                            name="total_amount" value="{{ old('total_amount', $order->total_amount) }}"
                                            required>
                                        @error('total_amount')
                                            <div class="text-danger small">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary me-2">Cập nhật đơn hàng</button>
                                <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Hủy</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
</div> @endsection

@push('scripts')
    {{-- Các script bạn đã có từ template --}}
    <script src="../../assets/libs/jquery/jquery.min.js"></script>
    <script src="../../assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            // Logic hiển thị/ẩn trường lý do hủy khi trạng thái đơn hàng thay đổi
            const orderStatusSelect = $('#order_status');
            const cancelReasonField = $('#cancel_reason_field');
            const cancelReasonTextarea = $('#cancel_reason'); // Lấy textarea lý do hủy
            const currentOrderStatus = orderStatusSelect.data('currentStatus');
            const orderStatusesOrder = ['Chưa xác nhận', 'Xác nhận', 'Đang vận chuyển', 'Giao hàng thành công',
                'Hủy đơn'
            ];

            // Logic cho trạng thái thanh toán
            const paymentStatusSelect = $('#payment_status');
            const currentPaymentStatus = paymentStatusSelect.data('currentPaymentStatus');
            const paymentStatusesOrder = ['pending', 'paid', 'failed'];

            // Hàm để cập nhật các option của select trạng thái đơn hàng (disable/enable)
            function updateOrderStatusOptions() {
                const currentStatusIndex = orderStatusesOrder.indexOf(currentOrderStatus);

                orderStatusSelect.find('option').each(function() {
                    const optionValue = $(this).val();
                    const optionIndex = orderStatusesOrder.indexOf(optionValue);

                    // Luôn cho phép chọn trạng thái hiện tại
                    if (optionValue === currentOrderStatus) {
                        $(this).prop('disabled', false);
                        return; // Chuyển sang option tiếp theo
                    }

                    // Nếu trạng thái hiện tại là 'Hủy đơn', không cho phép chọn trạng thái khác
                    if (currentOrderStatus === 'Hủy đơn') {
                        $(this).prop('disabled', true);
                    }
                    // Nếu trạng thái hiện tại là 'Giao hàng thành công', không cho phép chọn trạng thái khác
                    else if (currentOrderStatus === 'Giao hàng thành công') {
                        $(this).prop('disabled', true);
                    }
                    // Nếu trạng thái mới là lùi lại so với trạng thái hiện tại (trong chuỗi tiến triển)
                    else if (optionIndex < currentStatusIndex) {
                        $(this).prop('disabled', true);
                    }
                    // Trạng thái 'Hủy đơn' có thể chọn từ bất kỳ trạng thái nào (trừ khi đã 'Giao hàng thành công' và không thể hủy)
                    // Logic này sẽ được kiểm tra ở backend, nhưng có thể disable ở frontend để UX tốt hơn
                    else if (optionValue === 'Hủy đơn') {
                        // Tạm thời không disable ở frontend, backend sẽ kiểm tra canBeCancelled
                        $(this).prop('disabled', false);
                    } else {
                        // Cho phép chọn các trạng thái tiến triển
                        $(this).prop('disabled', false);
                    }
                });
            }

            // Hàm để cập nhật các option của select trạng thái thanh toán (disable/enable)
            function updatePaymentStatusOptions() {
                const currentPaymentStatusIndex = paymentStatusesOrder.indexOf(currentPaymentStatus);

                paymentStatusSelect.find('option').each(function() {
                    const optionValue = $(this).val();
                    const optionIndex = paymentStatusesOrder.indexOf(optionValue);

                    // Luôn cho phép chọn trạng thái hiện tại
                    if (optionValue === currentPaymentStatus) {
                        $(this).prop('disabled', false);
                        return true; // Chuyển sang option tiếp theo
                    }

                    // Không cho phép lùi lại trạng thái thanh toán
                    if (optionIndex < currentPaymentStatusIndex) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });
            }


            // Gọi hàm cập nhật lần đầu khi trang tải
            updateOrderStatusOptions();
            updatePaymentStatusOptions();

            // Lắng nghe sự kiện thay đổi trên dropdown trạng thái đơn hàng
            orderStatusSelect.on('change', function() {
                const selectedStatus = $(this).val();
                if (selectedStatus === 'Hủy đơn') {
                    cancelReasonField.slideDown();
                    cancelReasonTextarea.prop('required', true); // Đặt required
                } else {
                    cancelReasonField.slideUp();
                    cancelReasonTextarea.val(''); // Xóa nội dung lý do khi không phải trạng thái hủy
                    cancelReasonTextarea.prop('required', false); // Bỏ required
                    // Xóa trạng thái validation nếu có
                    cancelReasonTextarea.removeClass('is-invalid');
                    cancelReasonField.find('.text-danger.small').text('');
                }
            });

            // Nếu trang tải lại với lỗi validation cho cancel_reason, đảm bảo trường này hiển thị
            // và đặt required (dùng .length để kiểm tra xem có lỗi được Laravel truyền vào không)
            if ($('#cancel_reason_field .text-danger.small').text().length > 0) { // Kiểm tra nội dung lỗi
                cancelReasonField.slideDown();
                cancelReasonTextarea.prop('required', true);
            }
        });
    </script>
@endpush
