@extends('layouts.admin')

@section('title', 'Chi tiết lịch sử mã giảm giá')

@section('content')
    <style>
        .discount-container {
            max-width: 1100px;
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
            flex: 0 0 180px;
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

        .btn-group-custom {
            margin-top: 2rem;
            display: flex;
            gap: 1.5rem;
        }

        .btn-custom-outline {
            background: transparent;
            border: 2px solid #6c757d;
            color: #6c757d;
            border-radius: 10px;
            padding: 0.6rem 2rem;
            font-weight: 700;
            font-size: 1rem;
            box-shadow: 0 6px 15px rgba(13, 110, 253, 0.1);
        }

        .btn-custom-outline:hover {
            background: #6c757d;
            color: white;
        }
    </style>

    <div class="container discount-container py-5">
        <h2 class="discount-header">
            <i class="bi bi-receipt-cutoff me-2"></i> Chi tiết sử dụng mã giảm giá
        </h2>

        <div class="card discount-card p-4">
            <h4 class="discount-title">Thông tin lượt sử dụng</h4>

            @php
                $fields = [
                    'ID' => $usage->id,
                    'Mã giảm giá (ID)' => $usage->discount_id ?? 'NULL',
                    'Mã người dùng' => $usage->user_id ?? 'NULL',
                    'Tên người dùng' => $usage->user_name ?? 'NULL',
                    'Mã sản phẩm' => $usage->product_id ?? 'NULL',
                    'Mã giảm giá (Code)' => '<span class="text-uppercase text-success fw-semibold">' . $usage->discount_code . '</span>',
                    'Mã đơn hàng' => $usage->order_id ?? 'NULL',
                    'Ngày sử dụng' => $usage->used_at ? \Carbon\Carbon::parse($usage->used_at)->format('d/m/Y H:i') : 'NULL',
                    'Ngày tạo bản ghi' => $usage->created_at ? $usage->created_at->format('d/m/Y H:i') : 'NULL',
                    'Ngày cập nhật' => $usage->updated_at ? $usage->updated_at->format('d/m/Y H:i') : 'NULL',
                    'Ngày xóa' => $usage->deleted_at ?? 'NULL',
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

            <div class="btn-group-custom">
                <a href="{{ route('admin.discount.history') }}" class="btn btn-custom-outline">
                    <i class="bi bi-arrow-left-circle"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
@endsection
