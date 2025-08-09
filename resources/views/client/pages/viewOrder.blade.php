@extends('layouts.app')
@section('title', 'Đơn hàng')
@push('styles')
    <style>
        /* Table nhỏ gọn */
        .table.table-compact th,
        .table.table-compact td {
            font-size: 15px;
            /* Tăng nhẹ chữ toàn bảng */
            padding: 0.4rem 0.6rem;
            /* Tăng nhẹ khoảng cách */
            vertical-align: middle;
        }

        /* Đầu bảng (thead) to và nổi bật hơn */
        .table.table-compact thead th {
            font-size: 17px;
            font-weight: 600;
            /* Màu nền nhẹ */
        }

        /* Badge nhỏ lại */
        .table.table-compact .badge {
            font-size: 11px;
            padding: 0.25em 0.5em;
        }

        /* Nút nhỏ lại */
        .table.table-compact .btn {
            font-size: 11px;
            padding: 0.2rem 0.4rem;
        }

        /* Dropdown menu nhỏ */
        .table.table-compact .dropdown-menu {
            font-size: 12px;
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
                            <h2>Đơn hàng</h2>
                            <span><a href="{{ route('home') }}">Home</a> / Đơn hàng</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-cart padding-t-100">
        <div class="container">
            <div class="cr-cart-content" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                <div class="cr-table-content">
                    <table class="table table-hover table-compact">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Người nhận</th>
                                <th>Tổng tiền</th>
                                <th>Ngày đặt</th>
                                <th>Phương thức thanh toán</th>
                                <th>Trạng thái đơn</th>
                                <th>Thanh toán</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                                <tr>
                                    <td>{{ $order->sku }}</td>
                                    <td>{{ $order->shipping_name }}</td>


                                    <td>{{ number_format($order->total_amount, 0, ',', '.') }} đ</td>
                                    <td>{{ $order->created_at->format('H:i d/m/Y') }}</td>
                                    <td>{{ $order->payment_method_name }}</td>
                                    <td>
                                        @php
                                            $statusClassMap = [
                                                'Chưa xác nhận' => 'secondary',
                                                'Xác nhận' => 'primary',
                                                'Đang vận chuyển' => 'info',
                                                'Giao hàng thành công' => 'success',
                                                'Đã hủy' => 'danger',
                                            ];

                                            $badgeClass = $statusClassMap[$order->order_status] ?? 'dark';
                                        @endphp

                                        <span class="badge bg-{{ $badgeClass }}">{{ $order->order_status }}</span>

                                    </td>

                                    <td>
                                        @php
                                            $paymentMap = [
                                                'pending' => ['Chờ thanh toán', 'warning'],
                                                'paid' => ['Đã thanh toán', 'success'],
                                                'failed' => ['Thanh toán thất bại', 'danger'],
                                            ];
                                            [$payLabel, $payClass] = $paymentMap[$order->payment_status] ?? [
                                                'Không rõ',
                                                'dark',
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $payClass }}">{{ $payLabel }}</span>
                                    </td>

                                    <td>
                                        <div class="dropdown">
                                            <button class="btn p-0 border-0 bg-transparent" type="button"
                                                id="dropdownMenuButton-{{ $order->id }}" data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                <i class="ri-more-2-fill fs-5"></i>
                                            </button>
                                            <ul class="dropdown-menu"
                                                aria-labelledby="dropdownMenuButton-{{ $order->id }}">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('orders.show', $order) }}">
                                                        <i class="ri-eye-line me-1"></i> Xem chi tiết
                                                    </a>
                                                </li>
                                                @if ($order->canBePay())
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('orders.payAgain', $order) }}">
                                                            <i class="ri-refresh-line me-1"></i> Thanh toán lại
                                                        </a>
                                                    </li>
                                                @endif

                                                @if ($order->canBeCancel())
                                                    <li>
                                                        <a class="dropdown-item text-danger" data-bs-toggle="collapse"
                                                            href="#cancel-form-{{ $order->id }}" role="button"
                                                            aria-expanded="false"
                                                            aria-controls="cancel-form-{{ $order->id }}">
                                                            <i class="ri-delete-bin-line me-1"></i> Huỷ đơn
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>

                                        @if ($order->canBeCancel())
                                            <div class="collapse mt-2" id="cancel-form-{{ $order->id }}">
                                                <form action="{{ route('orders.cancel', $order->sku) }}" method="POST"
                                                    onsubmit="return confirm('Xác nhận hủy đơn hàng này?')">
                                                    @csrf
                                                    @method('POST')
                                                    <textarea name="cancel_reason" class="form-control mb-2" rows="2" placeholder="Lý do hủy đơn hàng..." required></textarea>
                                                    <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                                        Xác nhận hủy
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>




                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center">Bạn chưa có đơn hàng nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
