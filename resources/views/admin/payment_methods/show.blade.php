@extends('layouts.admin')

@section('title', 'Chi tiết phương thức thanh toán')

@section('content')
    <style>
        .payment-method-container {
            max-width: 1100px;
        }

        .payment-method-header {
            font-size: 2.2rem;
            font-weight: 700;
            color: #0d6efd;
            margin-bottom: 1.5rem;
            user-select: none;
        }

        .payment-method-card {
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(13, 110, 253, 0.15);
            border: none;
        }

        .payment-method-title {
            font-weight: 600;
            font-size: 1.75rem;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 0.5rem;
            color: #0d6efd;
            margin-bottom: 2rem;
            user-select: none;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
            border-radius: 8px;
            margin-bottom: 0.7rem;
        }

        .info-row:hover {
            background-color: #f8f9fa;
        }

        .label-text {
            flex: 0 0 200px;
            font-weight: 600;
            color: #6c757d;
            user-select: none;
        }

        .value-text {
            flex: 1;
            font-weight: 700;
            color: #212529;
            word-break: break-word;
        }

        .status-badge {
            border-radius: 20px;
            padding: 0.45rem 1.2rem;
            font-size: 1rem;
            font-weight: 600;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            user-select: none;
        }

        .btn-group-custom {
            margin-top: 2rem;
            display: flex;
            gap: 1.5rem;
        }

        .btn-custom {
            border-radius: 10px;
            padding: 0.6rem 2rem;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 6px 15px rgba(13, 110, 253, 0.25);
        }

        .btn-custom-outline {
            background: transparent;
            border: 2px solid #6c757d;
            color: #6c757d;
        }

        .btn-custom-outline:hover {
            background: #6c757d;
            color: white;
        }

        .btn-custom-primary {
            background: linear-gradient(45deg, #0d6efd, #0056b3);
            border: none;
            color: white;
        }

        .btn-custom-primary:hover {
            background: linear-gradient(45deg, #0056b3, #0d6efd);
        }

        @media (max-width: 768px) {
            .info-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .label-text {
                margin-bottom: 0.25rem;
            }
        }
    </style>

    <div class="container payment-method-container py-4">
        <h2 class="payment-method-header">Chi tiết phương thức thanh toán</h2>

        <div class="card payment-method-card p-4">
            <h4 class="payment-method-title">{{ $paymentMethod->name ?? 'Không có tên' }}</h4>

            <div class="info-row">
                <div class="label-text">Tên phương thức:</div>
                <div class="value-text">{{ $paymentMethod->name }}</div>
            </div>

            <div class="info-row">
                <div class="label-text">Mô tả:</div>
                <div class="value-text">{!! $paymentMethod->description ?? '<span class="text-muted">Không có mô tả</span>' !!}</div>
            </div>

            <div class="info-row">
                <div class="label-text">Trạng thái:</div>
                <div class="value-text">
                    @if ($paymentMethod->status)
                        <span class="status-badge bg-success">Kích hoạt</span>
                    @else
                        <span class="status-badge bg-secondary">Tạm tắt</span>
                    @endif
                </div>
            </div>

            <div class="info-row">
                <div class="label-text">Ngày tạo:</div>
                <div class="value-text">
                    {{ $paymentMethod->created_at ? $paymentMethod->created_at->format('d/m/Y H:i') : 'N/A' }}</div>
            </div>

            <div class="info-row">
                <div class="label-text">Ngày cập nhật:</div>
                <div class="value-text">
                    {{ $paymentMethod->updated_at ? $paymentMethod->updated_at->format('d/m/Y H:i') : 'N/A' }}</div>
            </div>
        </div>

        <div class="btn-group-custom">
            <a href="{{ route('admin.paymentMethods.index') }}" class="btn btn-custom btn-custom-outline">
                ← Quay lại
            </a>
            <a href="{{ route('admin.paymentMethods.edit', $paymentMethod->id) }}"
                class="btn btn-custom btn-custom-primary">
                Chỉnh sửa
            </a>
        </div>

        {{-- BẢNG ĐƠN HÀNG --}}
        <div class="mt-5">
            <h4 class="payment-method-title">Đơn hàng sử dụng phương thức này</h4>

            @if ($paymentMethod->orders->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>ID</th>
                                <th>Mã đơn hàng</th>
                                <th>Khách hàng</th>
                                <th>Ngày đặt</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($paymentMethod->orders as $order)
                                <tr>
                                    <td>{{ $order->id }}</td>
                                    {{-- 5:59 code trong order là cái gì?? --}}
                                    <td>{{ $order->code }}</td>
                                    {{-- 5:59 customer_name là cái gì??? --}}
                                    <td>{{ $order->customer_name ?? 'N/A' }}</td>
                                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        @if ($order->status === 'completed')
                                            <span class="badge bg-success">Hoàn tất</span>
                                        @elseif ($order->status === 'pending')
                                            <span class="badge bg-warning text-dark">Đang xử lý</span>
                                        @else
                                            <span class="badge bg-secondary">Khác</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-muted">Không có đơn hàng nào sử dụng phương thức này.</p>
            @endif
        </div>
    </div>
@endsection
