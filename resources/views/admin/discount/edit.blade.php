@extends('layouts.admin')

@section('title', 'Chỉnh sửa mã giảm giá')

@section('content')
<div class="container py-4">
    <h2 class="mb-4">✏️ Chỉnh sửa mã giảm giá</h2>

    <form action="{{ route('admin.discount.update', $discount->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label class="form-label">Tiêu đề</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $discount->title) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" required>{{ old('description', $discount->description) }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Mã giảm giá</label>
            <input type="text" name="code" class="form-control" value="{{ old('code', $discount->code) }}" required>
        </div>

     <div class="mb-3">
    <label class="form-label">Loại giảm giá</label>
    <select name="discount_type" id="discount_type" class="form-control" required>
        <option value="percentage" {{ old('discount_type', $discount->discount_type) == 'percentage' ? 'selected' : '' }}>Phần trăm</option>
        <option value="fixed" {{ old('discount_type', $discount->discount_type) == 'fixed' ? 'selected' : '' }}>Cố định</option>
    </select>
</div>

<div class="mb-3">
    <label class="form-label">Giá trị giảm</label>
    <div style="display: flex; align-items: center;">
        <input 
            type="number" 
            name="discount_value" 
            class="form-control" 
            value="{{ old('discount_value', $discount->discount_value ?? '') }}" 
            required 
            style="flex: 1;"
        >
        <span id="unit_label" style="margin-left: 10px; font-weight: bold;">
            @php
                $type = old('discount_type', $discount->discount_type ?? 'percentage');
                echo $type === 'fixed' ? 'VND' : '%';
            @endphp
        </span>
    </div>
    @error('discount_value')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const discountType = document.getElementById('discount_type');
        const unitLabel = document.getElementById('unit_label');

        if (discountType && unitLabel) {
            function updateLabel() {
                unitLabel.textContent = discountType.value === 'fixed' ? 'VND' : '%';
            }

            discountType.addEventListener('change', updateLabel);
        }
    });
</script>


        

     <div class="mb-3">
    <label class="form-label">Ngày bắt đầu</label>
    <input type="datetime-local" id="start_date" name="start_date" class="form-control"
        value="{{ old('start_date', \Carbon\Carbon::parse($discount->start_date)->format('Y-m-d\TH:i')) }}" required>
</div>

<div class="mb-3">
    <label class="form-label">Ngày kết thúc</label>
    <input type="datetime-local" id="end_date" name="end_date" class="form-control"
        value="{{ old('end_date', \Carbon\Carbon::parse($discount->end_date)->format('Y-m-d\TH:i')) }}" required>
</div>


        <div class="mb-3">
            <label class="form-label">Giá trị đơn hàng tối đa</label>
            <input type="number" name="max_order_value" class="form-control" value="{{ old('max_order_value', $discount->max_order_value) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Giá trị đơn hàng tối thiểu</label>
            <input type="number" name="min_order_value" class="form-control" value="{{ old('min_order_value', $discount->min_order_value) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Số lượng mã</label>
            <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $discount->quantity) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Giới hạn mỗi người dùng</label>
            <input type="number" name="user_usage_limit" class="form-control" value="{{ old('user_usage_limit', $discount->user_usage_limit) }}" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Áp dụng cho tất cả sản phẩm?</label>
            <select name="applies_to_all_products" class="form-control" required>
                <option value="1" {{ old('applies_to_all_products', $discount->applies_to_all_products) == 1 ? 'selected' : '' }}>Có</option>
                <option value="0" {{ old('applies_to_all_products', $discount->applies_to_all_products) == 0 ? 'selected' : '' }}>Không</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-control" required>
                <option value="active" {{ old('status', $discount->status) == 'active' ? 'selected' : '' }}>Kích hoạt</option>
                <option value="inactive" {{ old('status', $discount->status) == 'inactive' ? 'selected' : '' }}>Không kích hoạt</option>
            </select>
        </div>

        <button type="submit" class="btn btn-success">💾 Lưu thay đổi</button>
        <a href="{{ route('admin.discount.index') }}" class="btn btn-secondary ms-2">← Quay lại</a>
    </form>
</div>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startInput = document.querySelector('input[name="start_date"]');
        const endInput = document.querySelector('input[name="end_date"]');

        function updateEndMin() {
            if (startInput.value) {
                endInput.min = startInput.value;
                if (endInput.value && endInput.value < startInput.value) {
                    endInput.value = '';
                }
            } else {
                endInput.min = '';
            }
        }

        // Set min cho end ngay khi load trang
        updateEndMin();

        // Cập nhật min khi thay đổi start_date
        startInput.addEventListener('input', updateEndMin);

        // Kiểm tra khi submit form
        const form = startInput.closest('form');
        form.addEventListener('submit', function(e) {
            if (startInput.value && endInput.value && endInput.value < startInput.value) {
                e.preventDefault();
                alert('Ngày giờ kết thúc phải lớn hơn hoặc bằng ngày giờ bắt đầu.');
                endInput.focus();
            }
        });
    });
</script>
@endsection
