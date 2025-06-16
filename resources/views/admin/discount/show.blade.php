@extends('layouts.admin')

@section('title', 'Chi tiết mã giảm giá')

@section('content')
    <style>
        .discount-container {
            max-width: 1100px;
            /* font-family: 'Nunito', sans-serif; */
        }

        .discount-header {
            font-size: 2.2rem;
            font-weight: 700;
            color: #0d6efd;
            margin-bottom: 1.5rem;
            user-select: none;
        }

        .discount-card {
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(13, 110, 253, 0.15);
            border: none;
        }

        .discount-title {
            font-weight: 600;
            font-size: 1.75rem;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 0.5rem;
            color: #0d6efd;
            margin-bottom: 2rem;
            user-select: none;
        }

        .discount-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e9ecef;
            transition: background-color 0.3s ease;
            border-radius: 8px;
            margin-bottom: 0.7rem;
        }

        .discount-row:hover {
            background-color: #f8f9fa;
        }

        .label-text {
            flex: 0 0 160px;
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

        .btn-custom {
            border-radius: 10px;
            padding: 0.6rem 2rem;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 6px 15px rgba(13, 110, 253, 0.25);
            transition: background 0.4s ease;
        }

        .btn-custom-primary {
            background: linear-gradient(45deg, #0d6efd, #0056b3);
            border: none;
            color: white;
        }

        .btn-custom-primary:hover {
            background: linear-gradient(45deg, #0056b3, #0d6efd);
            color: white;
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

        .btn-group-custom {
            margin-top: 2rem;
            display: flex;
            gap: 1.5rem;
        }

        @media (max-width: 768px) {
            .discount-row {
                flex-direction: column;
                align-items: flex-start;
            }

            .label-text {
                margin-bottom: 0.25rem;
            }
        }
    </style>

    <div class="container discount-container py-4">

        <h2 class="discount-header"> Chi tiết mã giảm giá</h2>

        <div class="card discount-card p-4">
            <h4 class="discount-title">{{ $discount->title ?? 'Không có tiêu đề' }}</h4>

            @php
                $fields = [
                    'Mô tả' => $discount->description ?? 'Không có',
                    'Mã giảm giá' => $discount->code,
                    'Loại giảm giá' => $discount->discount_type === 'percentage' ? 'Phần trăm' : 'Cố định',
                    'Giá trị giảm' =>
                        number_format($discount->discount_value, 2) .
                        ($discount->discount_type === 'percentage' ? '%' : ' VNĐ'),
                    'Ngày bắt đầu' => \Carbon\Carbon::parse($discount->start_date)->format('d/m/Y H:i'),
                    'Ngày kết thúc' => \Carbon\Carbon::parse($discount->end_date)->format('d/m/Y H:i'),
                    'Giá trị đơn hàng tối đa' => number_format($discount->max_order_value, 0) . ' VNĐ',
                    'Giá trị đơn hàng tối thiểu' => number_format($discount->min_order_value, 0) . ' VNĐ',
                    'Giá trị giảm tối đa' => number_format($discount->max_discount, 0) . ' VNĐ',
                    'Số lượng mã' => $discount->quantity,
                    'Giới hạn mỗi người dùng' => $discount->user_usage_limit,
                    'Áp dụng cho tất cả sản phẩm' => $discount->applies_to_all_products ? 'Có' : 'Không',
                ];

                $half = ceil(count($fields) / 2);
                $leftFields = array_slice($fields, 0, $half, true);
                $rightFields = array_slice($fields, $half, null, true);
            @endphp
            <div class="row gx-5">
                <div class="col-md-6">
                    @foreach ($leftFields as $label => $value)
                        <div class="discount-row">
                            <div class="label-text">{{ $label }}:</div>
                            <div class="value-text">{!! $value !!}</div>
                        </div>
                    @endforeach

                    {{-- @if (!$discount->applies_to_all_products && $products && count($products)) --}}
                    {{-- <div class="discount-row mt-3">
        <label class="fw-semibold mb-3 d-block text-muted">Sản phẩm áp dụng:</label> --}}

                    {{-- <div class="table-responsive" style="max-width: 500px;">
            <table class="table align-middle border mb-0 shadow-sm rounded-4 overflow-hidden"
                   style="border-collapse: separate; border-spacing: 0; border-radius: 12px;">
                <thead style="background-color: #e9f0ff;" class="text-dark fw-bold text-center">
                    <tr>
                        <th style="width: 50px; border-top-left-radius: 12px;">#</th>
                        <th style="border-top-right-radius: 12px;">Tên sản phẩm</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($products as $index => $product)
                        <tr class="{{ $index % 2 == 0 ? 'bg-light' : '' }}">
                            <td class="text-center">{{ $index + 1 }}</td>
                            <td class="text-start">{{ $product->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div> --}}
                    {{-- </div> --}}
                    {{-- @endif --}}



                </div>
                <div class="col-md-6">
                    @foreach ($rightFields as $label => $value)
                        <div class="discount-row">
                            <div class="label-text">{{ $label }}:</div>
                            <div class="value-text">{!! $value !!}</div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="discount-row mt-4 justify-content-start">
                <div class="label-text">Trạng thái:</div>
                <div>
                    @if ($discount->status === 'active')
                        <span class="status-badge bg-success">Kích hoạt</span>
                    @else
                        <span class="status-badge bg-secondary">Không kích hoạt</span>
                    @endif
                </div>
            </div>
        </div>
        @if (!$discount->applies_to_all_products && $products && count($products))
            <div class="card discount-card p-4 mt-5">
                <h5 class="fw-bold mb-4" style="font-size: 1.6rem; color: #0d6efd;">
                    <i class="bi bi-bag-check-fill me-2"></i> Sản phẩm áp dụng mã giảm giá
                </h5>

                <div class="table-responsive rounded-4 overflow-hidden">
                    <table class="table table-striped align-middle mb-0 shadow-sm"
                        style="border-collapse: separate; border-spacing: 0;">
                        <thead class="text-white text-center"
                            style="background: linear-gradient(to right, #0d6efd, #0056b3);">
                            <tr>
                                <th class="py-3 px-4" style="border-top-left-radius: 12px; width: 60px;">#</th>
                                <th class="py-3 px-4" style="border-top-right-radius: 12px;">Tên sản phẩm</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $index => $product)
                                <tr style="transition: background 0.3s ease;"
                                    class="{{ $index % 2 == 0 ? 'bg-white' : 'bg-light' }}">
                                    <td class="text-center fw-bold text-secondary py-3 px-4">{{ $index + 1 }}</td>
                                    <td class="fw-semibold text-dark py-3 px-4">{{ $product->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif


        <div class="btn-group-custom">
            <a href="{{ route('admin.discount.index') }}" class="btn btn-custom btn-custom-outline">
                ← Quay lại
            </a>
            <a href="{{ route('admin.discount.edit', $discount->id) }}" class="btn btn-custom btn-custom-primary">
                Chỉnh sửa
            </a>
        </div>
    </div>
@endsection
