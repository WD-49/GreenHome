@extends('layouts.app')
@section('title', 'Đơn hàng')
@push('styles')
    <style>
        .cr-shop-list {
            display: flex;
            align-items: flex-start;
            padding: 15px;
            border: 1px solid #ddd;
            margin-bottom: 10px;
            background: #fff;
        }

        .cr-shop-list .images {
            width: 180px;
            margin-right: 20px;
        }

        .cr-shop-list .images img {
            width: 100%;
            height: auto;
            max-height: 120px;
            object-fit: contain;
        }

        .cr-shop-list .images .single-image {
            width: 100%;
            height: auto;
            max-height: 180px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
        }

        .cr-shop-list .image-row {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-bottom: 8px;
        }

        .cr-shop-list .image-row img {
            flex: 1;
            max-width: calc(50% - 5px);
        }

        .cr-shop-list .details {
            flex-grow: 1;
            margin-left: 40px;
        }

        .cr-shop-list .price {
            font-weight: bold;
            color: #28a745;
        }

        .cr-shop-list .status {
            font-size: 12px;
            padding: 2px 8px;
            border-radius: 10px;
        }

        .cancel-form {
            margin-top: 10px;
            display: none;
            width: 100%;
            max-width: 400px;
        }

        .cancel-form.active {
            display: block;
        }

        .filter-section {
            margin-bottom: 20px;
        }

        .filter-section input,
        .filter-section select,
        .filter-section button {
            margin-bottom: 15px;
            padding: 8px;
            width: 100%;
        }

        .date-filter {
            display: flex;
            gap: 15px;
        }

        .date-filter .date-input {
            width: 48%;
            padding: 8px;
        }
    </style>
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
                            <h2>Đơn hàng</h2>
                            <span><a href="{{ route('home') }}">Home</a> / Đơn hàng</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-cart padding-t-100">
        <div class="container">
            <div class="row">
                <div class="col-lg-3 col-12 md-30" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                    <div class="cr-shop-sideview">
                        <div class="filter-section">
                            <h4 class="cr-shop-sub-title">Bộ lọc</h4>
                            <input type="text" id="order-sku" placeholder="Lọc theo mã đơn" class="form-control"
                                value="{{ request()->query('sku') }}">
                            <select id="order-status" class="form-select">
                                <option value="">Tất cả trạng thái</option>
                                @foreach ($statuses as $status)
                                    <option value="{{ $status }}"
                                        {{ request()->query('status') == $status ? 'selected' : '' }}>
                                        {{ $status == 'Đã nhận hàng' ? 'Hoàn tất đơn hàng' : $status }}
                                    </option>
                                @endforeach

                            </select>
                            <select id="payment-status" class="form-select">
                                <option value="">Tất cả thanh toán</option>
                                @foreach ($payments as $pay)
                                    <option value="{{ $pay }}"
                                        {{ request()->query('payment') == $pay ? 'selected' : '' }}>
                                        @switch($pay)
                                            @case('pending')
                                                Chờ thanh toán
                                            @break

                                            @case('paid')
                                                Đã thanh toán
                                            @break

                                            @case('failed')
                                                Thanh toán thất bại
                                            @break

                                            @case('refunded')
                                                Đã hoàn tiền
                                            @break

                                            @default
                                                Không rõ
                                        @endswitch
                                    </option>
                                @endforeach
                            </select>
                            <div class="date-filter">
                                <input type="date" id="start-date" class="form-control date-input"
                                    value="{{ request()->query('start_date') }}">
                                <input type="date" id="end-date" class="form-control date-input"
                                    value="{{ request()->query('end_date') }}">
                            </div>
                            <button id="apply-filter" class="btn btn-success mt-2 w-100">Áp dụng</button>
                            <button id="reset-filter" class="btn btn-primary mt-2 w-100">Làm mới</button>
                        </div>
                    </div>
                </div>
                <div class="col-lg-9 col-12 md-30" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="600">
                    <div class="row">
                        <div class="col-12">
                            <div class="cr-shop-bredekamp">
                                <div class="cr-toggle">
                                    <a href="javascript:void(0)" class="gridRow active-grid">
                                        <i class="ri-list-check-2"></i>
                                    </a>
                                </div>
                                <div class="center-content">
                                    <span>Chúng tôi tìm thấy <span id="total-orders">{{ $orders->total() }}</span> đơn hàng
                                        cho bạn!</span>
                                </div>
                                <div class="cr-select">
                                    <label>Sắp xếp theo :</label>
                                    <select id="sort-orders" class="form-select">
                                        <option value="newest"
                                            {{ request()->query('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                                        <option value="oldest"
                                            {{ request()->query('sort') == 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                                        <option value="high_to_low"
                                            {{ request()->query('sort') == 'high_to_low' ? 'selected' : '' }}>Tổng tiền cao
                                            đến thấp</option>
                                        <option value="low_to_high"
                                            {{ request()->query('sort') == 'low_to_high' ? 'selected' : '' }}>Tổng tiền
                                            thấp đến cao</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="order-list" class="row col-100 mb-minus-24">
                        @foreach ($orders as $order)
                            <div class="col-12 cr-shop-list mb-24" data-order-id="{{ $order->id }}">
                                <div class="images">
                                    @php
                                        $images = $order->items ? $order->items->pluck('product_image')->all() : [];
                                        $half = ceil(count($images) / 2);
                                        $firstRow = array_slice($images, 0, $half);
                                        $secondRow = array_slice($images, $half);
                                        $defaultImage = asset('storage/default.png');
                                    @endphp
                                    @if (count($images) === 1)
                                        <img src="{{ $images[0] ? asset('storage/' . $images[0]) : $defaultImage }}"
                                            alt="Product Image" class="single-image">
                                    @else
                                        <div class="image-row">
                                            @foreach ($firstRow as $image)
                                                <img src="{{ $image ? asset('storage/' . $image) : $defaultImage }}"
                                                    alt="Product Image">
                                            @endforeach
                                        </div>
                                        <div class="image-row">
                                            @foreach ($secondRow as $image)
                                                <img src="{{ $image ? asset('storage/' . $image) : $defaultImage }}"
                                                    alt="Product Image">
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                                <div class="details">
                                    <h4>{{ $order->sku }}</h4>
                                    <p><strong>Người nhận:</strong> {{ $order->shipping_name }}</p>
                                    <p><strong>Tổng tiền:</strong> <span
                                            class="price">{{ number_format($order->total_amount, 0, ',', '.') }} đ</span>
                                    </p>
                                    <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('H:i d/m/Y') }}</p>
                                    <p><strong>Phương thức thanh toán:</strong> {{ $order->payment_method_name }}</p>
                                    <p><strong>Trạng thái:</strong>
                                        @php
                                            $badgeClass =
                                                [
                                                    'Chưa xác nhận' => 'secondary',
                                                    'Xác nhận' => 'primary',
                                                    'Đang vận chuyển' => 'info',
                                                    'Giao hàng thành công' => 'success',
                                                    'Đã nhận hàng' => 'success',
                                                    'Đã hủy' => 'danger',
                                                    'Đã hoàn hàng' => 'success', // Thêm trạng thái Đã hoàn hàng
                                                ][$order->order_status] ?? 'dark';
                                        @endphp
                                        <span
                                            class="badge bg-{{ $badgeClass }} status">{{ $order->order_status == 'Đã nhận hàng' ? 'Đơn hàng hoàn tất' : $order->order_status }}</span>
                                    </p>
                                    <p><strong>Thanh toán:</strong>
                                        @php
                                            $paymentLabel = '';
                                            switch ($order->payment_status) {
                                                case 'pending':
                                                    $paymentLabel = 'Chờ thanh toán';
                                                    $badgeClass = 'warning';
                                                    break;
                                                case 'paid':
                                                    $paymentLabel = 'Đã thanh toán';
                                                    $badgeClass = 'success';
                                                    break;
                                                case 'failed':
                                                    $paymentLabel = 'Thanh toán thất bại';
                                                    $badgeClass = 'danger';
                                                    break;
                                                case 'refunded':
                                                    $paymentLabel = 'Đã hoàn tiền';
                                                    $badgeClass = 'success';
                                                    break;
                                                default:
                                                    $paymentLabel = 'Không rõ';
                                                    $badgeClass = 'dark';
                                                    break;
                                            }
                                        @endphp
                                        <span class="badge bg-{{ $badgeClass }} status">{{ $paymentLabel }}</span>
                                    </p>
                                </div>
                                <div class="actions">
                                    <div class="dropdown">
                                        <button class="btn p-0 border-0 bg-transparent" type="button"
                                            id="dropdownMenuButton-{{ $order->id }}" data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                            <i class="ri-more-2-fill fs-5"></i>
                                        </button>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-{{ $order->id }}">
                                            <li><a class="dropdown-item" href="{{ route('orders.show', $order) }}"><i
                                                        class="ri-eye-line me-1"></i>Xem chi tiết</a></li>
                                            @if ($order->canBeCancel())
                                                <li><a class="dropdown-item text-danger"
                                                        onclick="toggleCancelForm({{ $order->id }})"><i
                                                            class="ri-delete-bin-line me-1"></i>Hủy đơn</a></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                                @if ($order->canBeCancel())
                                    <div class="cancel-form" id="cancel-form-{{ $order->id }}">
                                        <form action="{{ route('orders.cancel', $order->sku) }}" method="POST"
                                            onsubmit="return confirmCancel({{ $order->id }})">
                                            @csrf
                                            @method('POST')
                                            <textarea name="cancel_reason" class="form-control mb-2" rows="2" placeholder="Lý do hủy đơn hàng..."
                                                required></textarea>
                                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">Xác nhận
                                                hủy</button>
                                        </form>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                        @if ($orders->isEmpty())
                            <div class="col-12 text-center">Bạn chưa có đơn hàng nào.</div>
                        @endif
                    </div>
                    <nav aria-label="..." class="cr-pagination" id="pagination">
                        {{ $orders->appends(request()->query())->links() }}
                    </nav>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Định nghĩa hàm fetchOrders ở phạm vi global
        function fetchOrders(url = null) {
            url = url || `${window.location.pathname}?${new URLSearchParams(window.location.search).toString()}`;
            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('order-list').innerHTML = data.html;
                        document.getElementById('total-orders').textContent = data.total;
                        document.getElementById('pagination').innerHTML = data.pagination;
                        history.pushState({}, '', url);
                    } else {
                        alert('Có lỗi xảy ra khi tải danh sách đơn hàng.');
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // Định nghĩa hàm sortOrders ở phạm vi global
        function sortOrders(select) {
            const sort = select.value;
            let url = new URL(window.location);
            url.searchParams.set('sort', sort);
            // Giữ tất cả tham số hiện tại, bao gồm cả bộ lọc và page
            const params = new URLSearchParams(window.location.search);
            params.forEach((value, key) => {
                if (key !== 'sort') url.searchParams.set(key, value);
            });
            fetchOrders(url.toString());
        }

        document.addEventListener('DOMContentLoaded', function() {
            const orderList = document.getElementById('order-list');
            const totalOrders = document.getElementById('total-orders');
            const pagination = document.getElementById('pagination');
            const sortSelect = document.getElementById('sort-orders');

            // Gắn sự kiện onchange cho select
            if (sortSelect) {
                sortSelect.addEventListener('change', function() {
                    sortOrders(this);
                });
            }

            // Bộ lọc
            document.getElementById('apply-filter').addEventListener('click', function() {
                let url = new URL(window.location);
                const sku = document.getElementById('order-sku').value;
                const status = document.getElementById('order-status').value;
                const payment = document.getElementById('payment-status').value;
                const startDate = document.getElementById('start-date').value;
                const endDate = document.getElementById('end-date').value;

                if (sku) url.searchParams.set('sku', sku);
                else url.searchParams.delete('sku');
                if (status) url.searchParams.set('status', status);
                else url.searchParams.delete('status');
                if (payment) url.searchParams.set('payment', payment);
                else url.searchParams.delete('payment');
                if (startDate) url.searchParams.set('start_date', startDate);
                else url.searchParams.delete('start_date');
                if (endDate) url.searchParams.set('end_date', endDate);
                else url.searchParams.delete('end_date');

                fetchOrders(url.toString());
            });

            // Reset bộ lọc
            document.getElementById('reset-filter').addEventListener('click', function() {
                document.getElementById('order-sku').value = '';
                document.getElementById('order-status').value = '';
                document.getElementById('payment-status').value = '';
                document.getElementById('start-date').value = '';
                document.getElementById('end-date').value = '';
                window.location.href = '{{ route('orders.list') }}'; // Reload trang gốc, xóa hết params
            });

            // Xử lý phân trang
            document.addEventListener('click', function(e) {
                const link = e.target.closest('a.page-link');
                if (link && link.href) {
                    e.preventDefault();
                    fetchOrders(link.href);
                }
            });

            // Toggle form hủy đơn
            window.toggleCancelForm = function(orderId) {
                const form = document.getElementById(`cancel-form-${orderId}`);
                form.classList.toggle('active');
            };

            // Xác nhận hủy đơn
            window.confirmCancel = function(orderId) {
                return confirm('Xác nhận hủy đơn hàng này?');
            };
        });
    </script>
@endsection
