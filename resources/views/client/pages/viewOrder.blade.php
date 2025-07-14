@extends('layouts.app')
@section('title', 'Đơn hàng')

@section('content')
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
                <form action="#">
                    <div class="cr-table-content">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Người nhận</th>
                                    <th>Tổng tiền</th>
                                    <th>Ngày đặt</th>
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
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm btn-info"
                                                title="Xem chi tiết">
                                                <i class="ri-eye-line"></i>
                                            </a>

                                            @if ($order->canBeCancel())
                                                <form action="{{ route('orders.cancel', $order) }}" method="POST"
                                                    style="display:inline-block"
                                                    onsubmit="return confirm('Bạn có chắc muốn hủy đơn hàng này?');">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-danger" title="Hủy đơn">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </form>
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
                </form>

                <div class="mt-3">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </section>
@endsection
