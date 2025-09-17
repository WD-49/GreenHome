@extends('layouts.admin')

@section('content')
    <div class="container py-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h3 class="mb-0">Chi tiết đơn hàng #{{ $order->sku }}</h3>
            </div>
            <div class="card-body">
                {{-- Mã đơn hàng - Trạng thái đơn hàng --}}
                @if ($order->refundTransactions && count($order->refundTransactions) > 0)
                    <div class="text-warning mb-2">
                        <i class="bi bi-exclamation-circle me-1"></i>
                        Đã tồn tại yêu cầu hoàn tiền cho đơn hàng này
                    </div>
                @endif
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

                {{-- Trạng thái đơn hàng & Trạng thái thanh toán --}}
                <div class="d-flex flex-wrap gap-5 mb-4">

                    {{-- Trạng thái đơn hàng --}}
                    <div>
                        <h5 class="mb-3">📌 Trạng thái đơn hàng</h5>
                        @php
                            $orderStatuses = [
                                'Chưa xác nhận' => 'Xác nhận',
                                'Xác nhận' => 'Đang vận chuyển',
                                'Đang vận chuyển' => 'Giao hàng thành công',
                            ];
                            $currentOrderStatus = $order->order_status;
                            $nextStatus = $orderStatuses[$currentOrderStatus] ?? null;

                            // Kiểm tra nếu là VNPAY và chưa thanh toán thì không cho chuyển sang Đang vận chuyển
                            if (
                                $order->payment_method_name === 'VNPAY' &&
                                $order->payment_status !== 'paid' && // Nếu chưa thanh toán thì không cho chuyển sang Đang vận chuyển
                                $currentOrderStatus === 'Xác nhận'
                            ) {
                                $nextStatus = null;
                            }
                        @endphp

                        @if ($order->payment_method === 'VNPAY' && $order->payment_status !== 'paid' && $currentOrderStatus === 'Xác nhận')
                            <div class="alert alert-warning mb-3 d-flex align-items-center">
                                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                <span>Không thể chuyển sang trạng thái "Đang vận chuyển" khi đơn hàng VNPAY chưa được thanh
                                    toán!</span>
                            </div>
                        @endif

                        <div class="d-flex align-items-center gap-3">
                            <div class="badge bg-primary fs-6">{{ $currentOrderStatus }}</div>

                            @if ($nextStatus && $currentOrderStatus !== 'Hủy đơn' && $currentOrderStatus !== 'Đã nhận hàng')
                                <form class="d-inline" method="POST"
                                    action="{{ route('admin.orders.updateStatus', $order->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="order_status" value="{{ $nextStatus }}">
                                    <button type="submit" class="btn btn-success">
                                        <i class="bi bi-arrow-right-circle me-1"></i>
                                        Chuyển sang {{ $nextStatus }}
                                    </button>
                                </form>
                            @endif

                            @if ($currentOrderStatus === 'Chưa xác nhận')
                                <button type="button" class="btn btn-danger" data-bs-toggle="modal"
                                    data-bs-target="#cancelReasonModal">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Hủy đơn
                                </button>
                            @endif
                        </div>

                        @if ($order->order_status === 'Hủy đơn' && $order->cancel_reason)
                            <div class="mt-2 text-danger">
                                <strong>❌ Lý do huỷ:</strong> {{ $order->cancel_reason }}
                            </div>
                        @endif
                    </div>

                    {{-- Trạng thái thanh toán --}}
                    <div>
                        <h5 class="mb-3">💳 Trạng thái thanh toán</h5>
                        @php
                            $paymentStatuses = [
                                'pending' => ['next' => 'paid', 'display' => 'Chờ thanh toán'],
                                'paid' => ['next' => null, 'display' => 'Đã thanh toán'],
                                'failed' => ['next' => 'pending', 'display' => 'Thanh toán thất bại'],
                                'refunded' => ['next' => null, 'display' => 'Đã hoàn tiền'],
                            ];
                            $currentPaymentStatus = $order->payment_status;
                            $currentDisplayStatus = $paymentStatuses[$currentPaymentStatus]['display'];
                            $nextStatus = $paymentStatuses[$currentPaymentStatus]['next'];
                        @endphp

                        <div class="d-flex align-items-center gap-3">
                            {{-- Trạng thái thanh toán --}}
                            <div
                                class="badge {{ $currentPaymentStatus === 'paid' ? 'bg-success' : ($currentPaymentStatus === 'failed' ? 'bg-danger' : 'bg-warning') }} fs-6">
                                {{ $currentDisplayStatus }}
                            </div>

                            {{-- Nút chuyển trạng thái thanh toán --}}
                            @if ($nextStatus)
                                <form class="d-inline" method="POST"
                                    action="{{ route('admin.orders.updatePaymentStatus', $order->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="payment_status" value="{{ $nextStatus }}">
                                    <button type="submit"
                                        class="btn {{ $nextStatus === 'paid' ? 'btn-success' : 'btn-primary' }}">
                                        <i class="bi bi-arrow-right-circle me-1"></i>
                                        Chuyển sang {{ $paymentStatuses[$nextStatus]['display'] }}
                                    </button>
                                </form>
                            @endif

                            {{-- Nút đánh dấu thất bại --}}
                            @if ($currentPaymentStatus === 'pending')
                                <form class="d-inline" method="POST"
                                    action="{{ route('admin.orders.updatePaymentStatus', $order->id) }}">
                                    @csrf
                                    @method('PUT')
                                    <input type="hidden" name="payment_status" value="failed">
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-x-circle me-1"></i>
                                        Đánh dấu thất bại
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
                @if ($order->payment_method_name === 'VNPAY' && $order->payment_status !== 'paid')
                    <div class="d-flex align-items-center mb-3 py-2 px-3"
                        style="background-color: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px;">
                        <i class="bi bi-exclamation-circle text-warning me-2 fs-5"></i>
                        <p class="mb-0">Đơn hàng VNPAY chưa được thanh toán - Không hỗ trợ vận chuyển</p>
                    </div>
                @endif

                @php
                    $totalOrderAmount = 0;
                @endphp

                <hr>
                <div class="mt-4">
                    <h5 class="mb-3">💰 Yêu cầu hoàn tiền</h5>
                    @if ($order->refundTransactions && count($order->refundTransactions) > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Ngày yêu cầu</th>
                                        <th>Số tiền hoàn</th>
                                        <th>Lý do</th>
                                        <th>Hình ảnh</th>
                                        <th>Thông tin hoàn tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($order->refundTransactions as $refund)
                                        <tr>
                                            <td>{{ $refund->created_at->format('d/m/Y H:i') }}</td>
                                            <td>{{ number_format($refund->refund_cost, 0, ',', '.') }} VND</td>
                                            <td>{{ $refund->refund_reason }}</td>
                                            <td>
                                                @if ($refund->refund_image)
                                                    <img src="{{ asset('storage/' . $refund->refund_image) }}"
                                                        alt="Minh chứng chuyển khoản"
                                                        style="width: 150px; height: auto; border: 1px solid #ddd; border-radius: 5px;">
                                                @else
                                                    Không có ảnh
                                                @endif
                                            </td>
                                            <td>
                                                @if ($refund->refund_account_name)
                                                    <strong>Ngân hàng:</strong> {{ $refund->refund_account_bank }}<br>
                                                    <strong>Tên TK:</strong> {{ $refund->refund_account_name }}<br>
                                                    <strong>Số TK:</strong> {{ $refund->refund_account_number }}
                                                    @if ($refund->refund_account_qr)
                                                        <br>
                                                        <button class="btn btn-sm btn-info"
                                                            onclick="showQRCode('{{ asset('storage/' . $refund->refund_account_qr) }}')">
                                                            <i class="bi bi-qr-code"></i> Xem QR
                                                        </button>
                                                    @endif
                                                @else
                                                    <span class="text-muted">Chưa cung cấp ngân hàng</span>
                                                @endif
                                            </td>
                                            <td>
                                                @php
                                                    $statusClass = '';
                                                    $statusText = '';
                                                    switch ($refund->refund_status) {
                                                        case 'pending':
                                                            $statusClass = 'bg-warning';
                                                            $statusText = 'Chờ xử lý';
                                                            break;
                                                        case 'approved':
                                                            $statusClass = 'bg-primary';
                                                            $statusText = 'Đã duyệt';
                                                            break;
                                                        case 'refund_pending':
                                                            $statusClass = 'bg-info';
                                                            $statusText = 'Đang hoàn tiền';
                                                            break;
                                                        case 'refunded':
                                                            $statusClass = 'bg-success';
                                                            $statusText = 'Đã hoàn tiền';
                                                            break;
                                                        case 'rejected':
                                                            $statusClass = 'bg-danger';
                                                            $statusText = 'Từ chối';
                                                            break;
                                                        default:
                                                            $statusClass = 'bg-secondary';
                                                            $statusText = 'Không xác định';
                                                    }
                                                @endphp
                                                <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                                @if ($refund->refund_date)
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ \Carbon\Carbon::parse($refund->refund_date)->format('d/m/Y H:i') }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <button class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#refundDetailModal{{ $refund->id }}">
                                                    Xem chi tiết
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{-- Popup Xem chi tiết yêu cầu hoàn tiền --}}
                        <div class="modal fade" id="refundDetailModal{{ $refund->id }}" tabindex="-1"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">Chi tiết yêu cầu: {{ $refund->order->sku }}
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <!-- Thông tin hoàn hàng -->
                                            <div class="col-md-6">
                                                <h6 class="fw-bold mb-3">Thông tin hoàn hàng</h6>
                                                <p><strong>Mã đơn hàng:</strong> {{ $refund->order->sku }}</p>
                                                <p><strong>Lý do:</strong> {{ $refund->refund_reason }}</p>
                                                <p><strong>Ảnh minh chứng:</strong><br>
                                                    @if ($refund->refund_image)
                                                        <img src="{{ asset('storage/' . $refund->refund_image) }}"
                                                            alt="Minh chứng"
                                                            style="width: 150px; height: auto; border: 1px solid #ddd; border-radius: 5px;">
                                                    @else
                                                        Không có ảnh
                                                    @endif
                                                </p>
                                                <p><strong>Ngày yêu cầu:</strong>
                                                    {{ $refund->created_at->format('H:i d/m/Y') }}</p>
                                                <p><strong>Trạng thái:</strong> <span
                                                        class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                                </p>
                                                <p><strong>Ghi chú admin:</strong>
                                                    {{ $refund->admin_note ?? 'Chưa có ghi chú' }}</p>
                                            </div>
                                            <!-- Thông tin hoàn tiền -->
                                            <div class="col-md-6">
                                                <h6 class="fw-bold mb-3">Thông tin hoàn tiền</h6>
                                                <p><strong>Số tiền:</strong>
                                                    {{ number_format($refund->refund_cost ?? 0, 0, ',', '.') }}
                                                    đ</p>
                                                <p><strong>Tài khoản ngân hàng:</strong><br>
                                                    {{ $refund->refund_account_name ?? 'chưa cập nhật' }} -
                                                    {{ $refund->refund_account_bank ?? 'chưa cập nhật' }}<br>
                                                    Số TK:
                                                    {{ $refund->refund_account_number ?? 'chưa cập nhật' }}
                                                </p>
                                                <p><strong>Ảnh QR Code:</strong><br>
                                                    @if ($refund->refund_account_qr)
                                                        <img src="{{ asset('storage/' . $refund->refund_account_qr) }}"
                                                            alt="QR Code"
                                                            style="width: 250px; height: auto; border: 1px solid #ddd; border-radius: 5px;">
                                                    @else
                                                        Không có ảnh
                                                    @endif
                                                </p>

                                                <p><strong>Ảnh minh chứng chuyển khoản:</strong><br>
                                                    @if ($refund->refund_proof_image)
                                                        <img src="{{ asset('storage/' . $refund->refund_proof_image) }}"
                                                            alt="Minh chứng chuyển khoản"
                                                            style="width: 150px; height: auto; border: 1px solid #ddd; border-radius: 5px;">
                                                    @else
                                                        Không có ảnh
                                                    @endif
                                                </p>
                                                @if ($refund->refund_date)
                                                    <p><strong>Ngày hoàn tất:</strong>

                                                        {{ $refund->refund_date->format('H:i d/m/Y') }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                        <!-- Form cập nhật trạng thái -->
                                        <hr>
                                        <h6 class="fw-bold mb-3">Cập nhật trạng thái</h6>
                                        <form action="{{ route('admin.refunds.update-status') }}" method="POST"
                                            enctype="multipart/form-data">
                                            @csrf
                                            <input type="hidden" name="refund_id" value="{{ $refund->id }}">
                                            <div class="mb-3">
                                                <label class="form-label">Trạng thái</label>
                                                <select name="status" class="form-select" required
                                                    onchange="toggleProofImage(this);"
                                                    {{ in_array($refund->refund_status, ['refunded', 'rejected']) ? 'disabled' : '' }}>
                                                    <option value="" disabled>Chọn trạng thái</option>

                                                    @if ($refund->refund_status === 'pending')
                                                        <option value="pending" selected>Chờ xử lý</option>
                                                        <option value="approved">Duyệt yêu cầu</option>
                                                        <option value="rejected">Từ chối yêu cầu</option>
                                                    @elseif ($refund->refund_status === 'approved')
                                                        <option value="approved" selected>Đã duyệt</option>
                                                        <option value="rejected">Từ chối yêu cầu</option>
                                                    @elseif ($refund->refund_status === 'refund_pending')
                                                        <option value="refund_pending" selected>Đang hoàn tiền</option>
                                                        <option value="refunded">Đã hoàn tiền</option>
                                                        <option value="rejected">Từ chối yêu cầu</option>
                                                    @elseif ($refund->refund_status === 'refunded')
                                                        <option value="refunded" selected>Đã hoàn tiền</option>
                                                    @elseif ($refund->refund_status === 'rejected')
                                                        <option value="rejected" selected>Đã từ chối</option>
                                                    @endif
                                                </select>
                                            </div>
                                            <div class="mb-3" id="proofImage_{{ $refund->id }}"
                                                style="display: none;">
                                                <label class="form-label">Ảnh minh chứng chuyển khoản (bắt buộc
                                                    khi hoàn tiền)</label>
                                                <input type="file" name="refund_proof_image" class="form-control"
                                                    accept="image/*"
                                                    onchange="previewProofImage(this, 'proofImagePreview_{{ $refund->id }}')"
                                                    {{ $refund->refund_status === 'refunded' ? 'disabled' : '' }}>
                                                <div id="proofImagePreview_{{ $refund->id }}" class="mt-2">
                                                    @if ($refund->refund_status === 'refunded' && $refund->refund_proof_image)
                                                        <img src="{{ asset('storage/' . $refund->refund_proof_image) }}"
                                                            alt="Minh chứng chuyển khoản"
                                                            style="width: 150px; height: auto; border: 1px solid #ddd; border-radius: 5px;">
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Ghi chú admin</label>
                                                <textarea name="admin_note" class="form-control" placeholder="Ghi chú"
                                                    {{ $refund->refund_status === 'refunded' ? 'disabled' : '' }}>{{ $refund->admin_note }}</textarea>
                                            </div>
                                            <p><small class="text-muted">* Ghi chú sẽ tự động cập nhật theo trạng thái nếu
                                                    không nhập</small></p>
                                            <button type="submit" class="btn btn-success"
                                                {{ $refund->refund_status === 'refunded' ? 'disabled' : '' }}>Cập
                                                nhật</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-muted">Chưa có yêu cầu hoàn tiền nào cho đơn hàng này.</p>
                        <div class="d-flex align-items-center mb-3 py-2 px-3"
                            style="background-color: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px;">
                            <i class="bi bi-exclamation-circle text-warning me-2 fs-5"></i>
                            <p class="mb-0">Chỉ hỗ trợ hoàn tiền cho đơn hàng đã nhận hàng.</p>
                        </div>
                    @endif

                    @php
                        $hasExistingRefund = $order->refundTransactions && count($order->refundTransactions) > 0;
                    @endphp
                    @if ($order->payment_status === 'paid' && in_array($order->order_status, ['Đã nhận hàng']) && !$hasExistingRefund)
                        <button type="button" class="btn btn-warning" data-bs-toggle="modal"
                            data-bs-target="#refundPaymentReasonModal">
                            <i class="bi bi-cash-coin me-1"></i>
                            Tạo yêu cầu hoàn tiền
                        </button>
                    @elseif ($hasExistingRefund)
                        <div class="text-warning mb-2">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            Đã tồn tại yêu cầu hoàn tiền cho đơn hàng này
                        </div>
                    @endif
                </div><br>
                <hr>

                {{-- Biểu đồ doanh thu --}}
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
                                <th>Note</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order->items as $item)
                                <tr>
                                    <td>{{ $item->product_name }}</td>
                                    <td>{{ $item->product_attribute }}</td>
                                    {{-- 6:35 --}}
                                    {{-- <td>{{ number_format($item->productVariant->price, 0, ',', '.') }} VND</td> --}}
                                    <td>{{ number_format($item->unit_price, 0, ',', '.') }} VND</td>
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
                                    <td>
                                        @if ($order->note)
                                            {{ $order->note }}
                                        @else
                                            Không có
                                        @endif
                                    </td>
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
                                        <th>Mã giảm giá
                                            ({{ optional($order)->discount_code ?? 'Không áp dụng' }}). <br>
                                            {{ optional($order->discount)->description }}</th>
                                        @if ($discountAmount > 0)
                                            <td>
                                                - {{ number_format($discountAmount, 0, ',', '.') }} VND
                                                <br>
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

    <!-- Modal Tạo yêu cầu hoàn tiền -->
    <div class="modal fade" id="refundPaymentReasonModal" tabindex="-1" aria-labelledby="refundPaymentReasonModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="refundPaymentReasonModalLabel">Tạo yêu cầu hoàn tiền</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('admin.refunds.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="order_id" value="{{ $order->id }}">

                        @if ($order->order_status !== 'Đã nhận hàng')
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Đơn hàng phải có trạng thái "Đã nhận hàng" để yêu cầu hoàn trả
                            </div>
                        @endif

                        @if ($order->payment_status !== 'paid')
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Đơn hàng phải được thanh toán thành công để yêu cầu hoàn trả
                            </div>
                        @endif

                        @if (now()->diffInDays($order->delivery_at ?? $order->created_at) >= 3)
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-2"></i>
                                Yêu cầu hoàn trả chỉ được thực hiện trong vòng 3 ngày kể từ ngày giao hàng
                            </div>
                        @endif

                        <div class="mb-3">
                            <label class="form-label">Lý do hoàn tiền <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="refund_reason" required rows="3" placeholder="Nhập lý do hoàn tiền..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ảnh minh chứng <span class="text-danger">*</span></label>
                            <input type="file" class="form-control" name="refund_image" required accept="image/*">
                            <small class="text-muted">Hỗ trợ: JPG, PNG (tối đa 2MB)</small>
                        </div>

                        <hr>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-primary">Tạo yêu cầu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Lý do Hủy Đơn Hàng -->
    <div class="modal fade" id="cancelReasonModal" tabindex="-1" aria-labelledby="cancelReasonModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('admin.orders.updateStatus', $order->id) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="order_status" value="Hủy đơn">
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelReasonModalLabel">Xác nhận Hủy Đơn Hàng</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Bạn đang chọn hủy đơn hàng này. Vui lòng nhập lý do hủy:</p>
                        <textarea class="form-control" id="cancel_reason_text" name="cancel_reason" rows="3"
                            placeholder="Nhập lý do hủy đơn hàng..." required></textarea>
                        <small class="text-danger d-none" id="cancelReasonError">Vui lòng nhập lý do hủy.</small>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-danger">Xác nhận và Hủy Đơn</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal hiển thị QR Code -->
    <div class="modal fade" id="qrCodeModal" tabindex="-1" aria-labelledby="qrCodeModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="qrCodeModalLabel">Mã QR Hoàn Tiền</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="qrCodeImage" src="" alt="QR Code" style="max-width: 100%; height: auto;">
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Kiểm tra trạng thái ban đầu của tất cả các select
            document.querySelectorAll('select[name="status"]').forEach(select => {
                toggleProofImage(select);
            });
        });

        function toggleProofImage(selectElement) {
            const proofImageDiv = selectElement.closest('form').querySelector('[id^=proofImage_]');
            const fileInput = proofImageDiv?.querySelector('input[type="file"]');

            if (selectElement.value === 'refunded') {
                if (proofImageDiv) {
                    proofImageDiv.style.display = 'block';
                    // Thêm class animation nếu cần
                    proofImageDiv.classList.add('fade-in');
                }
                if (fileInput) {
                    fileInput.required = true;
                }
            } else {
                if (proofImageDiv) {
                    proofImageDiv.style.display = 'none';
                    proofImageDiv.classList.remove('fade-in');
                }
                if (fileInput) {
                    fileInput.required = false;
                    // Reset giá trị file input
                    fileInput.value = '';
                }
            }
        }

        function showQRCode(qrImageUrl) {
            const qrImage = document.getElementById('qrCodeImage');
            if (qrImage) {
                qrImage.src = qrImageUrl;
                const qrModal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
                qrModal.show();
            }
        }

        // Gọi toggleProofImage khi trang load để hiển thị/ẩn đúng trạng thái
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('select[name="status"]').forEach(select => {
                toggleProofImage(select);
            });
            const orderStatusSelect = document.getElementById('order_status_select');
            const orderStatusForm = document.getElementById('orderStatusForm');
            const cancelReasonModal = new bootstrap.Modal(document.getElementById('cancelReasonModal'));
            const cancelReasonTextarea = document.getElementById('cancel_reason_text');
            const confirmCancelOrderBtn = document.getElementById('confirmCancelOrderBtn');
            const cancelReasonError = document.getElementById('cancelReasonError');

            // Lưu trữ trạng thái gốc khi trang được tải
            let originalOrderStatus = orderStatusSelect.value;

            orderStatusSelect.addEventListener('change', function() {
                // Khi chọn 'Hủy đơn', hiện modal
                if (this.value === 'Hủy đơn') {
                    // Kiểm tra nếu đơn hàng đã thanh toán
                    const paymentStatus = '{{ $order->payment_status }}';
                    const orderStatus = '{{ $order->order_status }}';
                    const needsRefund = paymentStatus === 'paid' && orderStatus === 'Chưa xác nhận';

                    if (needsRefund) {
                        // Thêm hidden input để đánh dấu cần tạo yêu cầu hoàn tiền
                        let autoRefundInput = orderStatusForm.querySelector(
                            'input[name="auto_create_refund"]');
                        if (!autoRefundInput) {
                            autoRefundInput = document.createElement('input');
                            autoRefundInput.type = 'hidden';
                            autoRefundInput.name = 'auto_create_refund';
                            autoRefundInput.value = '1';
                            orderStatusForm.appendChild(autoRefundInput);
                        }
                    }

                    cancelReasonTextarea.value = ''; // Xóa lý do cũ
                    cancelReasonError.classList.add('d-none'); // Ẩn lỗi
                    cancelReasonModal.show();
                } else {
                    // Nếu thay đổi sang trạng thái khác 'Hủy đơn', đảm bảo không có hidden input 'cancel_reason'
                    const existingReasonInput = orderStatusForm.querySelector(
                        'input[name="cancel_reason"]');
                    if (existingReasonInput) {
                        existingReasonInput.remove();
                    }
                    // Xóa hidden input auto_create_refund nếu có
                    const autoRefundInput = orderStatusForm.querySelector(
                        'input[name="auto_create_refund"]');
                    if (autoRefundInput) {
                        autoRefundInput.remove();
                    }
                    // Tự động submit form nếu không phải là 'Hủy đơn'
                    orderStatusForm.submit();
                }
            });

            confirmCancelOrderBtn.addEventListener('click', function() {
                const reason = cancelReasonTextarea.value.trim();
                if (reason.length < 1) { // Kiểm tra độ dài tối thiểu
                    cancelReasonError.textContent = 'Vui lòng nhập lý do hủy.';
                    cancelReasonError.classList.remove('d-none');
                } else {
                    cancelReasonError.classList.add('d-none');
                    // Tạo hoặc cập nhật hidden input cho cancel_reason
                    let hiddenInput = orderStatusForm.querySelector('input[name="cancel_reason"]');
                    if (!hiddenInput) {
                        hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'cancel_reason';
                        orderStatusForm.appendChild(hiddenInput);
                    }
                    hiddenInput.value = reason;

                    cancelReasonModal.hide();
                    orderStatusForm.submit(); // Gửi form
                }
            });

            // Khi modal đóng, nếu người dùng đã mở modal nhưng không xác nhận hủy,
            // đặt lại trạng thái dropdown về trạng thái ban đầu.
            cancelReasonModal._element.addEventListener('hidden.bs.modal', function() {
                if (orderStatusSelect.value === 'Hủy đơn' && !orderStatusForm.querySelector(
                        'input[name="cancel_reason"]')) {
                    orderStatusSelect.value = originalOrderStatus;
                }
            });
        });
    </script>
@endpush
