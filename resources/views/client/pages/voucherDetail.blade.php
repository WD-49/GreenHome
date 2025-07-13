@extends('layouts.app')

@section('content')
<style>
    .voucher-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
        background: linear-gradient(135deg, #f8fff8, #f0fdf4);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .voucher-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 16px 30px rgba(0, 0, 0, 0.12);
    }
    .voucher-header {
        background: #2b9348;
        color: white;
        padding: 30px 20px;
        text-align: center;
        border-bottom: 1px solid #e3e3e3;
    }
    .voucher-header h3 {
        margin: 0;
        font-size: 26px;
        font-weight: bold;
    }
    .voucher-icon {
        font-size: 40px;
        margin-bottom: 10px;
        display: block;
    }
    .voucher-info li {
        padding: 12px 18px;
        border-bottom: 1px dashed #ccc;
        font-size: 15px;
    }
    .voucher-info li:last-child {
        border-bottom: none;
    }
    .voucher-info strong {
        color: #1b4332;
        min-width: 140px;
        display: inline-block;
    }
    .voucher-actions {
        padding: 25px 20px;
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 10px;
    }
    .voucher-actions .btn {
        border-radius: 50px;
        padding: 10px 24px;
        font-size: 15px;
        min-width: 150px;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-md-10">

            <div class="card voucher-card">
                <div class="voucher-header">
                    <span class="voucher-icon">🎟</span>
                    <h3>Chi tiết mã giảm giá</h3>
                </div>

                <ul class="list-group list-group-flush voucher-info">
                    <li><strong>Mã:</strong> <span class="text-primary">{{ $voucher->code }}</span></li>
                    <li><strong>Tiêu đề:</strong> {{ $voucher->title }}</li>
                    <li><strong>Mô tả:</strong> {{ $voucher->description }}</li>
                    <li>
                        <strong>Giá trị:</strong>
                        <span class="text-danger fw-bold">
                            {{ $voucher->discount_type === 'percentage' ? $voucher->discount_value . '%' : number_format($voucher->discount_value) . ' ₫' }}
                        </span>
                    </li>
                    <li><strong>Giảm tối đa:</strong> {{ number_format($voucher->max_discount) }} ₫</li>
                    <li><strong>Đơn tối thiểu:</strong> {{ number_format($voucher->min_order_value) }} ₫</li>
                    <li><strong>Hạn sử dụng:</strong> {{ \Carbon\Carbon::parse($voucher->end_date)->format('d/m/Y') }}</li>
                  
                </ul>

                <div class="voucher-actions">
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary">
                        ⬅ Quay lại
                    </a>
                    <a href="{{ url('/voucher/' . $voucher->code . '/eligible-products') }}" class="btn btn-success">
                        💡 Áp dụng ngay
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
