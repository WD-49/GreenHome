<div class="row col-100 mb-minus-24">
    @foreach ($orders as $order)
        <div class="col-12 cr-shop-list mb-24" data-order-id="{{ $order->id }}">
            <div class="images">
                @php
                    $images = $order->items ? $order->items->pluck('product_image')->all() : [];
                    $half = ceil(count($images) / 2);
                    $firstRow = array_slice($images, 0, $half);
                    $secondRow = array_slice($images, $half);
                    $defaultImage = asset('images/default.png');
                @endphp
                @if (count($images) === 1)
                    <img src="{{ $images[0] ? asset('storage/' . $images[0]) : $defaultImage }}" alt="Product Image"
                        class="single-image">
                @else
                    <div class="image-row">
                        @foreach ($firstRow as $image)
                            <img src="{{ $image ? asset('storage/' . $image) : $defaultImage }}" alt="Product Image">
                        @endforeach
                    </div>
                    <div class="image-row">
                        @foreach ($secondRow as $image)
                            <img src="{{ $image ? asset('storage/' . $image) : $defaultImage }}" alt="Product Image">
                        @endforeach
                    </div>
                @endif
            </div>
            <div class="details">
                <h4>{{ $order->sku }}</h4>
                <p><strong>Người nhận:</strong> {{ $order->shipping_name }}</p>
                <p><strong>Tổng tiền:</strong> <span
                        class="price">{{ number_format($order->total_amount, 0, ',', '.') }} đ</span></p>
                <p><strong>Ngày đặt:</strong> {{ $order->created_at->format('H:i d/m/Y') }}</p>
                <p><strong>Phương thức thanh toán:</strong> {{ $order->payment_method_name }}</p>
                <p><strong>Trạng thái:</strong>
                    @php $badgeClass = ['Chưa xác nhận' => 'secondary', 'Xác nhận' => 'primary', 'Đang vận chuyển' => 'info', 'Giao hàng thành công' => 'success', 'Đã nhận hàng' => 'success', 'Đã hủy' => 'danger'][$order->order_status] ?? 'dark'; @endphp
                    <span class="badge bg-{{ $badgeClass }} status">{{ $order->order_status }}</span>
                </p>
                <p><strong>Thanh toán:</strong>
                    @php [$payLabel, $payClass] = ['pending' => ['Chờ thanh toán', 'warning'], 'paid' => ['Đã thanh toán', 'success'], 'failed' => ['Thanh toán thất bại', 'danger']][$order->payment_status] ?? ['Không rõ', 'dark']; @endphp
                    <span class="badge bg-{{ $payClass }} status">{{ $payLabel }}</span>
                </p>
            </div>
            <div class="actions">
                <div class="dropdown">
                    <button class="btn p-0 border-0 bg-transparent" type="button"
                        id="dropdownMenuButton-{{ $order->id }}" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="ri-more-2-fill fs-5"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton-{{ $order->id }}">
                        <li><a class="dropdown-item" href="{{ route('orders.show', $order) }}"><i
                                    class="ri-eye-line me-1"></i>Xem chi tiết</a></li>
                        @if ($order->canBeCancel())
                            <li><a class="dropdown-item text-danger" onclick="toggleCancelForm({{ $order->id }})"><i
                                        class="ri-delete-bin-line me-1"></i>Huỷ đơn</a></li>
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
                        <textarea name="cancel_reason" class="form-control mb-2" rows="2" placeholder="Lý do hủy đơn hàng..." required></textarea>
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100">Xác nhận hủy</button>
                    </form>
                </div>
            @endif
        </div>
    @endforeach
    @if ($orders->isEmpty())
        <div class="col-12 text-center">Bạn chưa có đơn hàng nào.</div>
    @endif
</div>
