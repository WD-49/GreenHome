@extends('layouts.admin')

@section('title', 'Chi tiết mã giảm giá')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">🎟️ Chi tiết mã giảm giá</h2>

    <div class="card shadow-sm">
        <div class="card-body">
            <h4 class="mb-3 text-primary">{{ $discount->title ?? 'Không có tiêu đề' }}</h4>

            <div class="row mb-2">
                <div class="col-md-4"><strong>Mô tả:</strong></div>
                <div class="col-md-8">{{ $discount->description ?? 'Không có' }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4"><strong>Mã giảm giá:</strong></div>
                <div class="col-md-8">{{ $discount->code }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4"><strong>Loại giảm giá:</strong></div>
                <div class="col-md-8">
                    {{ $discount->discount_type === 'percentage' ? 'Phần trăm' : 'Cố định' }}
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4"><strong>Giá trị giảm:</strong></div>
                <div class="col-md-8">
                    {{ number_format($discount->discount_value, 2) }}
                    {{ $discount->discount_type === 'percentage' ? '%' : ' VNĐ' }}
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4"><strong>Ngày bắt đầu:</strong></div>
                <div class="col-md-8">
                    {{ \Carbon\Carbon::parse($discount->start_date)->format('d/m/Y H:i') }}
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4"><strong>Ngày kết thúc:</strong></div>
                <div class="col-md-8">
                    {{ \Carbon\Carbon::parse($discount->end_date)->format('d/m/Y H:i') }}
                </div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4"><strong>Giảm tối đa:</strong></div>
                <div class="col-md-8">{{ number_format($discount->max_discount, 0) }} VNĐ</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4"><strong>Giá trị đơn hàng tối thiểu:</strong></div>
                <div class="col-md-8">{{ number_format($discount->min_order_value, 0) }} VNĐ</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4"><strong>Số lượng mã:</strong></div>
                <div class="col-md-8">{{ $discount->quantity }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4"><strong>Giới hạn mỗi người dùng:</strong></div>
                <div class="col-md-8">{{ $discount->user_usage_limit }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4"><strong>Áp dụng cho tất cả sản phẩm:</strong></div>
                <div class="col-md-8">{{ $discount->applies_to_all_products ? 'Có' : 'Không' }}</div>
            </div>

            <div class="row mb-2">
                <div class="col-md-4"><strong>Trạng thái:</strong></div>
                <div class="col-md-8">
                    @if($discount->status === 'active')
                        <span class="badge bg-success">Kích hoạt</span>
                    @else
                        <span class="badge bg-secondary">Không kích hoạt</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('admin.discount.index') }}" class="btn btn-outline-secondary">← Quay lại</a>
        <a href="{{ route('admin.discount.edit', $discount->id) }}" class="btn btn-primary">✏️ Chỉnh sửa</a>
    </div>
</div>
@endsection
