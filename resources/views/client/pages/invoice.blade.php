@extends('layouts.app')
@section('title', 'Invoice')
@push('styles')
    <link href="{{ asset('assets/css/vendor/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/invoice-custom.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
@endpush

@section('content')
    @if (session('success'))
        <script>
            alert(@json(session('success')));
        </script>
    @endif
    @if (session('error'))
        <script>
            alert(@json(session('error')));
        </script>
    @endif
    <section class="section-breadcrumb">
        <div class="cr-breadcrumb-image">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="cr-breadcrumb-title">
                            <h2>Đơn hàng: {{ $order->sku }}</h2>
                            <span> <a href="{{ route('home') }}">Trang chủ</a>/<a href="{{ route('orders.list') }}">Đơn
                                    hàng</a> /
                                Chi tiết đơn hàng</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <div class="cr-main-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="cr-card cr-invoice max-width-1170">
                        <div class="cr-card-content card-default">
                            <div class="invoice-wrapper">
                                <div class="row">
                                    <div class="col-md-6 col-lg-4 col-sm-6">
                                        <p class="text-dark mb-2"><strong>Thông tin người đặt</strong></p>
                                        <address>
                                            <span>{{ $user->name }}</span>
                                            <br> {{ $user->profile->address ?? ' ' }}
                                            <br> <span>Email:</span> {{ $user->email }}
                                            <br> <span>Sdt:</span> {{ $user->profile->phone ?? ' ' }}
                                        </address>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-sm-6">
                                        <p class="text-dark mb-2"><strong>Thông tin người nhận</strong></p>
                                        <address>
                                            <span>{{ $order->shipping_name }}</span>
                                            <br> {{ $order->shipping_address }}
                                            <br> <span>Sdt:</span> {{ $order->shipping_phone }}
                                        </address>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-sm-6">
                                        <p class="text-dark mb-2"><strong>Chi tiết đơn hàng</strong></p>
                                        @php
                                            $paymentStatusMap = [
                                                'pending' => ['Chờ thanh toán', 'secondary'],
                                                'paid' => ['Đã thanh toán', 'success'],
                                                'failed' => ['Thanh toán thất bại', 'danger'],
                                                'refunded' => ['Đã hoàn tiền', 'success'],
                                            ];
                                            [$paymentLabel, $paymentClass] = $paymentStatusMap[
                                                $order->payment_status
                                            ] ?? ['Không rõ', 'dark'];
                                        @endphp
                                        <address>
                                            <div><span>Mã đơn hàng: </span> <span
                                                    class="text-dark">{{ $order->sku }}</span></div>
                                            <div><span>Phương thức thanh toán: </span> {{ $order->payment_method_name }}
                                            </div>
                                            <div>
                                                <span>Trạng thái đơn hàng: </span>
                                                <span
                                                    class="badge bg-primary">{{ $order->order_status == 'Đã nhận hàng' ? 'Đơn hàng hoàn tất' : $order->order_status }}</span>
                                            </div>
                                            <div>
                                                <span>Trạng thái thanh toán: </span>
                                                <span class="badge bg-{{ $paymentClass }}">{{ $paymentLabel }}</span>
                                            </div>
                                        </address>
                                    </div>
                                </div>
                                <div class="cr-chart-header">
                                    <div class="block">
                                        <h6><strong>Đơn hàng</strong></h6>
                                        <h5>{{ $order->sku }}</h5>
                                    </div>
                                    <div class="block">
                                        <h6><strong>Tổng tiền</strong></h6>
                                        <h5>{{ number_format($order->total_amount, 0, ',', '.') }} đ</h5>
                                    </div>
                                    <div class="block">
                                        <h6><strong>Ngày đặt</strong></h6>
                                        <h5>{{ $order->created_at->format('H:i d/m/Y') }}</h5>
                                    </div>
                                </div>
                                <div class="table-responsive tbl-800">
                                    <div>
                                        <table class="table-invoice table-striped" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th><strong>#</strong></th>
                                                    <th><strong>Sản Phẩm</strong></th>
                                                    <th><strong>Giá Đặt</strong></th>
                                                    <th><strong>Số Lượng</strong></th>
                                                    <th><strong>Tổng Tiền</strong></th>
                                                    <th><strong>Giảm Giá</strong></th>
                                                    <th><strong>Tổng Tiền</strong></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->items as $index => $item)
                                                    <tr>
                                                        <td>{{ $index + 1 }}</td>
                                                        <td class="d-flex align-items-start">
                                                            <img class="invoice-item-img me-2"
                                                                src="{{ asset('storage/' . $item->product_image) }}"
                                                                alt="product-image"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                            <div>
                                                                <div class="fw-semibold">{{ $item->product_name }}</div>
                                                                @if (!empty($item->product_attribute))
                                                                    <div class="text-muted small">
                                                                        (Loại: {{ $item->product_attribute }})
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>{{ number_format($item->unit_price, 0, ',', '.') }}đ </td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}đ
                                                        </td>
                                                        <td>-{{ number_format($item->discount_amount, 0, ',', '.') ?? 0 }}đ
                                                        </td>
                                                        <td>{{ number_format($item->total_price, 0, ',', '.') }}đ </td>
                                                        <td>
                                                            @if ($order->order_status === 'Đã nhận hàng' && $order->payment_status === 'paid' && !$item->review)
                                                                <button class="btn btn-outline-info btn-sm p-1"
                                                                    onclick="showReviewModal({{ $item->id }}, '{{ $item->product_name }}', '{{ $item->product_attribute ?? '' }}')"
                                                                    title="Đánh giá sản phẩm">
                                                                    đánh giá
                                                                </button>
                                                            @else
                                                                <span class="text-success"></span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="row justify-content-end inc-total">
                                    <div class="col-lg-8 order-lg-1 order-md-2 order-sm-2">
                                        <div class="note">
                                            @if ($order->note)
                                                <label>Ghi chú:</label>
                                                <p>{{ $order->note }}</p>
                                            @endif
                                            @if ($order->cancel_reason)
                                                <label>Lý do hủy đơn:</label>
                                                <p>{{ $order->cancel_reason }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-lg-4 order-lg-2 order-md-1 order-sm-1">
                                        @php
                                            $productDiscount = $order->items->sum('discount_amount');
                                            $orderDiscount = $order->discount_amount;
                                            $isProductDiscount = $productDiscount > 0;
                                            $isOrderDiscount = $orderDiscount > $productDiscount;
                                            $statusMap = [
                                                'pending' => ['Chờ xử lý', 'info'],
                                                'approved' => ['Đã phê duyệt', 'success'],
                                                'rejected' => ['Bị từ chối', 'danger'],
                                                'refund_pending' => ['Chờ hoàn tiền', 'warning'],
                                                'refunded' => ['Đã hoàn tiền', 'success'],
                                                'account_invalid' => ['Tài khoản không hợp lệ', 'danger'],
                                            ];
                                            $refundStatus = $order->refund ? $order->refund->refund_status : null;
                                            [$displayStatus, $statusClass] = $statusMap[$refundStatus] ?? [
                                                'Không có yêu cầu hoàn hàng',
                                                'secondary',
                                            ];
                                        @endphp
                                        <ul class="list-unstyled">
                                            <li class="mid pb-3 text-dark">Tổng tiền sản phẩm:
                                                <span class="d-inline-block float-right text-default">
                                                    {{ number_format(
                                                        $order->items->sum(function ($item) {
                                                            return $item->unit_price * $item->quantity;
                                                        }),
                                                        0,
                                                        ',',
                                                        '.',
                                                    ) }}đ
                                                </span>
                                            </li>
                                            @if ($isProductDiscount)
                                                <li class="mid pb-3 text-dark">
                                                    Giảm giá theo sản phẩm: <br>(mã: {{ $order->discount_code }})
                                                    <span class="d-inline-block float-right text-default">
                                                        -{{ number_format($productDiscount, 0, ',', '.') }}đ
                                                    </span>
                                                </li>
                                            @endif
                                            @if ($isOrderDiscount)
                                                <li class="mid pb-3 text-dark">
                                                    Giảm giá toàn đơn: <br>(mã: {{ $order->discount_code }})
                                                    <span class="d-inline-block float-right text-default">
                                                        -{{ number_format($orderDiscount - $productDiscount, 0, ',', '.') }}đ
                                                    </span>
                                                </li>
                                            @endif
                                            <li class="mid pb-3 text-dark">Phí vận chuyển:
                                                <span class="d-inline-block float-right text-default">
                                                    {{ number_format($order->shipping_fee, 0, ',', '.') }}đ
                                                </span>
                                            </li>
                                            <li class="text-dark">Tổng tiền đơn hàng:
                                                <span class="d-inline-block float-right">
                                                    {{ number_format($order->total_amount, 0, ',', '.') }}đ
                                                </span>
                                            </li>
                                        </ul>
                                        @if ($order->canBePay())
                                            <a class="btn btn-info" href="{{ route('orders.payAgain', $order) }}">
                                                <i class="ri-refresh-line me-1"></i> Thanh toán lại
                                            </a>
                                        @endif
                                        @if ($order->order_status === 'Giao hàng thành công')
                                            <form action="{{ route('orders.confirmReceived', $order) }}" method="POST"
                                                onsubmit="return confirm('Xác nhận bạn đã nhận được hàng cho đơn này?')">
                                                @csrf
                                                <button type="submit"
                                                    class="btn btn-success btn-sm w-100 d-flex align-items-center mt-2">
                                                    <i class="ri-check-double-line me-1"></i>
                                                    Xác nhận đã nhận hàng
                                                </button>
                                            </form>
                                        @endif
                                        @if (
                                            $order->order_status === 'Đã nhận hàng' &&
                                                $order->payment_status === 'paid' &&
                                                $order->delivery_at !== null &&
                                                $order->delivery_at->diffInDays(now()) < 3 &&
                                                !$order->refund)
                                            <button type="button" class="btn btn-danger btn-sm w-100 mt-2"
                                                data-bs-toggle="modal" data-bs-target="#refundModal">
                                                <i class="ri-refresh-line me-1"></i> Yêu cầu hoàn trả hàng
                                            </button>
                                        @elseif ($order->refund)
                                            <div class="alert alert-{{ $statusClass }} mt-2">
                                                <strong>Trạng thái hoàn hàng:</strong> {{ $displayStatus }}
                                                @if ($order->refund->admin_note)
                                                    <br><strong>Ghi chú:</strong> {{ $order->refund->admin_note }}
                                                @endif
                                                @if ($refundStatus === 'refunded' && $order->refund->refund_proof_image)
                                                    <br><strong>Ảnh minh chứng chuyển khoản:</strong>
                                                    <br>
                                                    <img src="{{ asset('storage/' . $order->refund->refund_proof_image) }}"
                                                        alt="Minh chứng chuyển khoản"
                                                        style="width: 150px; height: auto; border: 1px solid #ddd; border-radius: 5px;">
                                                @endif
                                            </div>
                                            @if (in_array($refundStatus, ['approved', 'account_invalid']))
                                                <button type="button" class="btn btn-primary btn-sm w-100 mt-2"
                                                    data-bs-toggle="modal" data-bs-target="#bankInfoModal">
                                                    <i class="ri-bank-line me-1"></i>
                                                    {{ $refundStatus === 'account_invalid' ? 'Cung cấp lại thông tin tài khoản' : 'Cung cấp thông tin tài khoản' }}
                                                </button>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Yêu cầu hoàn trả -->
    <div id="refundModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yêu cầu hoàn trả hàng</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="refundForm" action="{{ route('refund.store') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <div class="mb-3">
                            <label for="refundReason" class="form-label">Lý do hoàn trả</label>
                            <textarea name="refund_reason" id="refundReason" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="refundImage" class="form-label">Ảnh minh chứng (Tối đa 1 ảnh)</label>
                            <input type="file" name="refund_image" id="refundImage" class="form-control"
                                accept="image/*" onchange="validateImage(this)">
                            <div id="imagePreview" class="mt-2"></div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Gửi yêu cầu</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Cung cấp thông tin tài khoản -->
    <div id="bankInfoModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ $order->refund && $order->refund->refund_status === 'account_invalid' ? 'Cung cấp lại thông tin tài khoản' : 'Cung cấp thông tin tài khoản' }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @php
                        $refund_cost = $order->total_amount - $order->shipping_fee;
                    @endphp
                    <div class="alert alert-info mb-3">
                        Số tiền được hoàn trả của bạn là {{ number_format($refund_cost, 0, ',', '.') }} đ
                    </div>
                    <form id="bankInfoForm" action="{{ route('refund.updateBankInfo') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="refund_id" value="{{ $order->refund->id ?? '' }}">
                        <div class="mb-3">
                            <label for="accountName" class="form-label">Tên chủ tài khoản</label>
                            <input type="text" name="refund_account_name" id="accountName" class="form-control"
                                value="{{ $order->refund->refund_account_name ?? '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="accountNumber" class="form-label">Số tài khoản</label>
                            <input type="text" name="refund_account_number" id="accountNumber" class="form-control"
                                value="{{ $order->refund->refund_account_number ?? '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="bankName" class="form-label">Tên ngân hàng</label>
                            <input type="text" name="refund_account_bank" id="bankName" class="form-control"
                                value="{{ $order->refund->refund_account_bank ?? '' }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="qrCodeImage" class="form-label">Ảnh QR Code (Không bắt buộc)</label>
                            <input type="file" name="refund_qr_image" id="qrCodeImage" class="form-control"
                                accept="image/*" onchange="validateQrImage(this)">
                            <div id="qrImagePreview" class="mt-2">
                                @if ($order->refund && $order->refund->refund_account_qr)
                                    <img src="{{ asset('storage/' . $order->refund->refund_account_qr) }}" alt="QR Code"
                                        style="width: 100px; height: 100px; object-fit: cover; border: 1px solid #ddd; border-radius: 5px;">
                                @endif
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Gửi thông tin</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal đánh giá sản phẩm -->
    <div id="reviewModal" class="modal fade" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Đánh giá sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="reviewForm" action="{{ route('client.review.submit') }}" method="POST"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="order_item_id" id="orderItemId">
                        <div class="mb-3">
                            <label for="reviewTitle" class="form-label">Tiêu đề đánh giá</label>
                            <input type="text" name="title" id="reviewTitle" class="form-control"
                                placeholder="Nhập tiêu đề đánh giá" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số sao đánh giá</label>
                            <div id="starRating" class="d-flex">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star text-muted" data-value="{{ $i }}"
                                        onclick="selectStarRating({{ $i }})"
                                        style="cursor: pointer; font-size: 24px; margin-right: 5px;"></i>
                                @endfor
                            </div>
                            <input type="hidden" name="rating" id="ratingValue" required>
                        </div>
                        <div class="mb-3">
                            <label for="reviewContent" class="form-label">Nội dung đánh giá</label>
                            <textarea name="content" id="reviewContent" class="form-control" rows="3" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="reviewImages" class="form-label">Hình ảnh (Tối đa 3 ảnh)</label>
                            <input type="file" name="images[]" id="reviewImages" class="form-control" multiple
                                onchange="previewImages(this)">
                            <div id="imagePreviewContainer" class="mt-3"></div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Gửi đánh giá</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function validateImage(input) {
            if (input.files.length > 1) {
                alert('Bạn chỉ được tải tối đa 1 ảnh.');
                input.value = '';
                document.getElementById('imagePreview').innerHTML = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.style.border = '1px solid #ddd';
                img.style.borderRadius = '5px';
                document.getElementById('imagePreview').innerHTML = '';
                document.getElementById('imagePreview').appendChild(img);
            };
            if (input.files[0]) reader.readAsDataURL(input.files[0]);
        }

        function validateQrImage(input) {
            if (input.files.length > 1) {
                alert('Bạn chỉ được tải tối đa 1 ảnh QR code.');
                input.value = '';
                document.getElementById('qrImagePreview').innerHTML = '';
                return;
            }
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '100px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.style.border = '1px solid #ddd';
                img.style.borderRadius = '5px';
                document.getElementById('qrImagePreview').innerHTML = '';
                document.getElementById('qrImagePreview').appendChild(img);
            };
            if (input.files[0]) reader.readAsDataURL(input.files[0]);
        }

        function showReviewModal(orderItemId, productName, productAttribute = null) {
            document.getElementById('orderItemId').value = orderItemId;
            let modalTitle = `Đánh giá sản phẩm: ${productName}`;
            if (productAttribute) {
                modalTitle += ` (Loại: ${productAttribute})`;
            }
            document.getElementById('reviewModal').querySelector('.modal-title').textContent = modalTitle;
            new bootstrap.Modal(document.getElementById('reviewModal')).show();
        }

        function previewImages(input) {
            const container = document.getElementById('imagePreviewContainer');
            container.innerHTML = '';
            if (input.files.length > 3) {
                alert('Bạn chỉ được tải tối đa 3 ảnh.');
                input.value = '';
                return;
            }
            Array.from(input.files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '100px';
                    img.style.height = '100px';
                    img.style.objectFit = 'cover';
                    img.style.marginRight = '10px';
                    img.style.border = '1px solid #ddd';
                    img.style.borderRadius = '5px';
                    container.appendChild(img);
                };
                reader.readAsDataURL(file);
            });
        }

        function selectStarRating(value) {
            const stars = document.querySelectorAll('#starRating .fa-star');
            stars.forEach(star => {
                const starValue = parseInt(star.getAttribute('data-value'));
                if (starValue <= value) {
                    star.classList.remove('text-muted');
                    star.classList.add('text-warning');
                } else {
                    star.classList.remove('text-warning');
                    star.classList.add('text-muted');
                }
            });
            document.getElementById('ratingValue').value = value;
        }
    </script>
@endpush
