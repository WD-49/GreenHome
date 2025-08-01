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
                            <span> <a href="{{ route('home') }}">Trang trủ</a>/<a href="{{ route('orders.list') }}">Đơn
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
                        {{-- <div class="cr-card-header">
                            <h4 class="cr-card-title">Invoice</h4>
                            <div class="header-tools">
                                <button class="cr-btn-primary m-r-5">Save</button>
                                <button class="cr-btn-secondary">Print</button>
                            </div>
                        </div> --}}
                        <div class="cr-card-content card-default">

                            <div class="invoice-wrapper">

                                <div class="row">
                                    {{-- <div class="col-md-6 col-lg-3 col-sm-6">
                                        <img src="{{ asset('assets_client/assets/img/logo/logo.png') }}" alt="logo">

                                        <address>
                                            <br> 321, Porigo alto, new st george church, Nr. Jogas garden, USA.
                                        </address>
                                    </div> --}}
                                    <div class="col-md-6 col-lg-4 col-sm-6">
                                        <p class="text-dark mb-2">Thông tin người đặt</p>

                                        <address>
                                            <span>{{ $user->name }}</span>
                                            <br> {{ $user->profile->address ?? ' ' }}
                                            <br> <span>Email:</span> {{ $user->email }}
                                            <br> <span>Sdt:</span> {{ $user->profile->phone ?? ' ' }}
                                        </address>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-sm-6">
                                        <p class="text-dark mb-2">Thông tin người nhận</p>

                                        <address>
                                            <span>{{ $order->shipping_name }}</span>
                                            <br> {{ $order->shipping_address }}
                                            <br> <span>Sdt:</span> {{ $order->shipping_phone }}
                                        </address>
                                    </div>
                                    <div class="col-md-6 col-lg-4 col-sm-6">
                                        <p class="text-dark mb-2">Chi tiết</p>

                                        @php
                                            $paymentStatusMap = [
                                                'pending' => ['Chờ thanh toán', 'secondary'],
                                                'paid' => ['Đã thanh toán', 'success'],
                                                'failed' => ['Thanh toán thất bại', 'danger'],
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
                                                <span class="badge bg-primary">{{ $order->order_status }}</span>
                                                {{-- Nếu cần dịch thì tạo thêm map tương tự --}}
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
                                        <h6>Đơn hàng</h6>
                                        <h5>{{ $order->sku }}</h5>
                                    </div>
                                    <div class="block">
                                        <h6>Tổng tiền</h6>
                                        <h5>{{ number_format($order->total_amount, 0, ',', '.') }} đ</h5>
                                        </h5>
                                    </div>
                                    {{-- <div class="block">
                                        <h6>Quantity</h6>
                                        <h5>30
                                        </h5>
                                    </div> --}}
                                    <div class="block">
                                        <h6>Ngày đặt</h6>
                                        <h5>{{ $order->created_at->format('H:i d/m/Y') }}</h5>
                                        </h5>
                                    </div>
                                </div>
                                <div class="table-responsive tbl-800">
                                    <div>
                                        <table class="table-invoice table-striped" style="width:100%">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Sản phẩm</th>
                                                    <th>giá đặt</th>
                                                    <th>số lượng</th>
                                                    <th>tổng tiền</th>
                                                    <th>giảm giá</th>
                                                    <th>Tổng tiền</th>
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
                                                            @if ($order->order_status === 'Giao hàng thành công' && $order->payment_status === 'paid' && !$item->review)
                                                                <button class="btn btn-outline-warning btn-sm p-2"
                                                                    onclick="showReviewModal({{ $item->id }}, '{{ $item->product_name }}', '{{ $item->product_attribute ?? '' }}')"
                                                                    title="Đánh giá sản phẩm">
                                                                    <i class="fas fa-star"></i> {{-- Font Awesome icon --}}
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
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal đánh giá sản phẩm --}}
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

                        <!-- Input tiêu đề -->
                        <div class="mb-3">
                            <label for="reviewTitle" class="form-label">Tiêu đề đánh giá</label>
                            <input type="text" name="title" id="reviewTitle" class="form-control"
                                placeholder="Nhập tiêu đề đánh giá" required>
                        </div>

                        <!-- Hàng sao -->
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

                        <!-- Nội dung đánh giá -->
                        <div class="mb-3">
                            <label for="reviewContent" class="form-label">Nội dung đánh giá</label>
                            <textarea name="content" id="reviewContent" class="form-control" rows="3" required></textarea>
                        </div>

                        <!-- Hình ảnh -->
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
        function showReviewModal(orderItemId, productName, productAttribute = null) {
            document.getElementById('orderItemId').value = orderItemId;

            let modalTitle = `Đánh giá sản phẩm: ${productName}`;
            if (productAttribute) {
                modalTitle += ` (Loại: ${productAttribute})`;
            }

            document.getElementById('reviewModal').querySelector('.modal-title').textContent = modalTitle;
            new bootstrap.Modal(document.getElementById('reviewModal')).show();
        }

        function validateImageCount(input) {
            if (input.files.length > 3) {
                alert('Bạn chỉ được tải tối đa 3 ảnh.');
                input.value = ''; // Reset input nếu vượt quá giới hạn
            }
        }

        function previewImages(input) {
            const container = document.getElementById('imagePreviewContainer');
            container.innerHTML = ''; // Xóa nội dung cũ

            if (input.files.length > 3) {
                alert('Bạn chỉ được tải tối đa 3 ảnh.');
                input.value = ''; // Reset input nếu vượt quá giới hạn
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
                    star.classList.add('text-warning'); // Đổi màu sao thành vàng
                } else {
                    star.classList.remove('text-warning');
                    star.classList.add('text-muted'); // Đổi màu sao thành xám
                }
            });
            document.getElementById('ratingValue').value = value; // Gán giá trị sao vào input ẩn
        }
    </script>
@endpush
