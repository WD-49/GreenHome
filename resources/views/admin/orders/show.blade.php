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
                        {{-- Đặt ID cho form và select --}}
                        <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}"
                            id="updateOrderStatusForm">
                            @csrf
                            @method('PUT')
                            {{-- Trường ẩn để chứa lý do hủy --}}
                            <input type="hidden" name="cancel_reason" id="hidden_cancel_reason">

                            <div class="d-flex align-items-center gap-2"> {{-- Giảm gap --}}
                                @php
                                    $cancelledStatusId = null;
                                    foreach ($statuses as $statusLoop) {
                                        if (trim(strtolower($statusLoop->name)) === 'Đã hủy') {
                                            $cancelledStatusId = $statusLoop->id;
                                            break;
                                        }
                                    }
                                @endphp
                                <select name="status_id" id="order_status_select" class="form-select form-select-sm w-auto"
                                    data-current-status-id="{{ $order->status_id }}"
                                    data-cancelled-status-id="{{ $cancelledStatusId }}">
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}"
                                            {{ $order->status_id == $status->id ? 'selected' : '' }}>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">
                                    <i class="bi bi-arrow-repeat me-1"></i> Cập nhật
                                </button>
                            </div>
                        </form>

                        @if (optional($order->status)->name === 'Đã hủy' && $order->cancel_reason)
                            <div class="mt-2 text-danger small">
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
                                        @if ($item->discount_id)
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
                                                -{{ number_format($discountAmount, 0, ',', '.') }} VND
                                                <br>
                                                <small>({{ $order->discount->type == 'order' ? 'Áp dụng toàn đơn' : 'Áp dụng sản phẩm' }})</small>
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

@push('scripts')
    <script>
        $(document).ready(function() {
            const csrfToken = $('meta[name="csrf-token"]').attr('content');
            const updateOrderStatusForm = $('#updateOrderStatusForm');
            const orderStatusSelect = $('#order_status_select');
            const cancelReasonModalElement = document.getElementById('cancelReasonModal');
            const cancelReasonTextarea = $('#cancel_reason_text');
            const hiddenCancelReasonInput = $('#hidden_cancel_reason');
            const confirmCancelOrderBtn = $('#confirmCancelOrderBtn');
            const cancelReasonError = $('#cancelReasonError');

            let cancelReasonModalInstance;
            if (cancelReasonModalElement) {
                cancelReasonModalInstance = new bootstrap.Modal(cancelReasonModalElement);
            } else {
                console.error('Lỗi: Không tìm thấy phần tử modal #cancelReasonModal!');
                // Bạn có thể alert ở đây nếu muốn người dùng biết ngay
                // alert('Lỗi cấu hình: Modal lý do hủy không tồn tại.');
            }

            // Lấy ID từ data attributes và đảm bảo chúng là chuỗi để so sánh
            const configCancelledStatusId = orderStatusSelect.data('cancelled-status-id') ? String(orderStatusSelect
                .data('cancelled-status-id')) : null;
            const initialPageLoadOrderStatusId = orderStatusSelect.data('current-status-id') ? String(
                orderStatusSelect.data('current-status-id')) : null;

            console.log("JS - ID Trạng Thái Hủy (từ data-attr):", configCancelledStatusId,
                typeof configCancelledStatusId);
            console.log("JS - ID Trạng Thái Ban Đầu Của Đơn Hàng (từ data-attr):", initialPageLoadOrderStatusId,
                typeof initialPageLoadOrderStatusId);

            if (!configCancelledStatusId) {
                console.warn(
                    "JS - Cảnh báo: Không thể xác định ID của trạng thái 'Đã hủy' từ data attribute. Chức năng yêu cầu lý do hủy có thể không hoạt động chính xác."
                );
            }

            updateOrderStatusForm.on('submit', function(e) {
                const selectedStatusId = orderStatusSelect.val(); // Đây là string

                console.log("--- Form Submit Event ---");
                console.log("Trạng thái được chọn (select.val):", selectedStatusId,
                    `(Kiểu: ${typeof selectedStatusId})`);
                console.log("ID trạng thái hủy đã định nghĩa:", configCancelledStatusId,
                    `(Kiểu: ${typeof configCancelledStatusId})`);
                console.log("Trạng thái ban đầu của đơn hàng:", initialPageLoadOrderStatusId,
                    `(Kiểu: ${typeof initialPageLoadOrderStatusId})`);
                console.log("Giá trị trường ẩn lý do hủy:", `"${hiddenCancelReasonInput.val()}"`);

                // Điều kiện 1: Người dùng có chọn trạng thái "Đã hủy" không?
                const isCancelling = (selectedStatusId === configCancelledStatusId);
                console.log("Đang chọn hủy?", isCancelling);

                // Điều kiện 2: Đơn hàng có đang ở trạng thái khác "Đã hủy" không?
                // (Nghĩa là đây là lần đầu tiên chuyển sang hủy, chứ không phải submit lại khi đã hủy)
                const isNotAlreadyCancelled = (initialPageLoadOrderStatusId !== configCancelledStatusId);
                // Lưu ý: Nếu bạn muốn cho phép nhập lại lý do nếu đơn hàng đã hủy và người dùng lại chọn "Đã hủy",
                // thì điều kiện này có thể cần điều chỉnh. Hiện tại nó chỉ kích hoạt modal nếu chuyển TỪ trạng thái KHÁC sang "Đã hủy".
                console.log("Đơn hàng chưa bị hủy trước đó?", isNotAlreadyCancelled);

                // Điều kiện 3: Lý do hủy đã được điền vào trường ẩn chưa (nghĩa là modal đã được xác nhận chưa)
                const reasonNotYetProvidedViaModal = (hiddenCancelReasonInput.val().trim() === '');
                console.log("Lý do chưa được cung cấp qua modal?", reasonNotYetProvidedViaModal);


                if (isCancelling && isNotAlreadyCancelled && reasonNotYetProvidedViaModal) {
                    e.preventDefault(); // Ngăn form submit ngay
                    console.log(">>> ĐIỀU KIỆN HIỂN THỊ MODAL ĐƯỢC ĐÁP ỨNG. Ngăn submit, hiển thị modal.");
                    cancelReasonError.addClass('d-none');
                    cancelReasonTextarea.val('');
                    if (cancelReasonModalInstance) {
                        cancelReasonModalInstance.show();
                    } else {
                        alert("Lỗi: Không thể khởi tạo modal lý do hủy.");
                    }
                    return false;
                } else {
                    console.log(
                        ">>> Điều kiện hiển thị modal KHÔNG được đáp ứng hoặc lý do đã có. Cho phép submit trực tiếp."
                    );
                }
                // Cho phép submit nếu không phải là hủy hoặc nếu hủy nhưng đã có lý do
                return true;
            });

            confirmCancelOrderBtn.on('click', function() {
                const reason = cancelReasonTextarea.val().trim();
                console.log("Nút 'Xác nhận và Hủy Đơn' trong modal được click. Lý do nhập:", `"${reason}"`);
                if (reason === '') {
                    cancelReasonError.removeClass('d-none');
                    return;
                }
                cancelReasonError.addClass('d-none');
                hiddenCancelReasonInput.val(reason); // Gán lý do vào trường ẩn

                if (cancelReasonModalInstance) {
                    cancelReasonModalInstance.hide();
                }

                console.log("Đã gán lý do vào trường ẩn. Tiến hành submit form chính...");
                // Quan trọng: Submit form chính sau khi đã có lý do
                updateOrderStatusForm.off('submit').submit();
            });

            if (cancelReasonModalElement) {
                cancelReasonModalElement.addEventListener('hidden.bs.modal', function() {
                    console.log("Modal lý do hủy đã đóng.");
                    // Nếu modal đóng mà trường lý do ẩn vẫn rỗng (nghĩa là người dùng không click "Xác nhận và Hủy Đơn")
                    // và trạng thái đang được chọn là "Đã hủy"
                    if (hiddenCancelReasonInput.val().trim() === '' && orderStatusSelect.val() ===
                        configCancelledStatusId) {
                        console.log("Modal đóng không xác nhận. Reset select về trạng thái ban đầu:",
                            initialPageLoadOrderStatusId);
                        orderStatusSelect.val(initialPageLoadOrderStatusId);
                    }
                    cancelReasonTextarea.val('');
                    cancelReasonError.addClass('d-none');
                });
            }

            // Khởi tạo tooltip
            $('[data-bs-toggle="tooltip"]').tooltip();

        });
    </script>
@endpush
