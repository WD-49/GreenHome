@extends('layouts.admin')

@section('title', 'Chỉnh sửa mã giảm giá')

@section('content')
<style>
    .form-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        animation: fadeIn 0.5s ease-in-out;
    }

    .form-section {
        background-color: #fff;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .form-section:hover {
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }

    .form-label {
        font-weight: 600;
    }

    .form-control {
        border-radius: 8px;
        transition: all 0.3s;
    }

    .form-control:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .btn-success {
        padding: 10px 24px;
        font-weight: 600;
        border-radius: 12px;
    }

    .btn-secondary {
        border-radius: 12px;
    }
</style>

<h2 class="mb-4"> Chỉnh sửa mã giảm giá</h2>

<form action="{{ route('admin.discount.update', $discount->id) }}" method="POST">
    @csrf
    @method('PUT')

    <div class="form-container">
        {{-- Cột trái --}}
        <div class="form-section">
            <div class="mb-3">
                <label class="form-label">Tiêu đề</label>
                <input type="text" name="title" class="form-control" value="{{ old('title', $discount->title) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea name="description" class="form-control" required>{{ old('description', $discount->description) }}</textarea>
            </div>
            <div class="mb-3">
    <label class="form-label">Mã</label>
   <div class="input-group">
    <input type="text" name="code" id="voucher-code" class="form-control" 
        value="{{ old('code', $discount->code) }}" readonly>
    {{-- <button type="button" class="btn btn-outline-secondary" onclick="generateCode()">Tạo mã</button> --}}
</div>

    <small id="code-status" class="text-muted"></small>
    @error('code')
        <div class="text-danger">{{ $message }}</div>
    @enderror
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
<input type="number" step="0.01" name="discount_value" class="form-control" value="{{ old('discount_value', $discount->discount_value) }}" required >
                    <span id="unit_label" style="margin-left: 10px; font-weight: bold;">
                        {{ old('discount_type', $discount->discount_type) == 'fixed' ? 'VND' : '%' }}
                    </span>
                </div>
                @error('discount_value')
    <div class="text-danger">{{ $message }}</div>
@enderror
            </div>
         


            <div class="mb-3">
                <label class="form-label">Giá trị đơn hàng tối thiểu</label>
                <input type="number" name="min_order_value" class="form-control" value="{{ old('min_order_value', $discount->min_order_value) }}">
                @error('min_order_value') <div class="text-danger">{{ $message }}</div> @enderror
            </div>

             <div class="mb-3">
                <label class="form-label">Giá trị giảm tối đa</label>
                <input type="number" name="max_discount" class="form-control" value="{{ old('max_discount', $discount->max_discount) }}">
                @error('max_discount') <div class="text-danger">{{ $message }}</div> @enderror
            </div>
        </div>

        {{-- Cột phải --}}
        <div class="form-section">
            <div class="mb-3">
                <label class="form-label">Bắt đầu</label>
                <input type="datetime-local" name="start_date" class="form-control"
                    value="{{ old('start_date', \Carbon\Carbon::parse($discount->start_date)->format('Y-m-d\TH:i')) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Kết thúc</label>
                <input type="datetime-local" name="end_date" class="form-control"
                    value="{{ old('end_date', \Carbon\Carbon::parse($discount->end_date)->format('Y-m-d\TH:i')) }}" required>
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
    <select name="applies_to_all_products" id="applies_to_all_products" class="form-control" required>
        <option value="1" {{ old('applies_to_all_products', $discount->applies_to_all_products) == 1 ? 'selected' : '' }}>Có</option>
        <option value="0" {{ old('applies_to_all_products', $discount->applies_to_all_products) == 0 ? 'selected' : '' }}>Không</option>
    </select>
</div>

<div class="mb-3" id="product-selection-box"
     style="{{ old('applies_to_all_products', $discount->applies_to_all_products) == 1 ? 'display: none;' : '' }}">
    <label class="form-label">Sản phẩm áp dụng</label>
    <input type="text" id="search-product" class="form-control mb-2" placeholder="Tìm kiếm sản phẩm...">

    <div id="product-checkbox-list" style="max-height: 200px; overflow-y: auto; border: 1px solid #ced4da; border-radius: 8px; padding: 10px;">
        @foreach($products as $product)
            <div class="form-check">
                <input
                    class="form-check-input product-checkbox"
                    type="checkbox"
                    name="product_ids[]"
                    value="{{ $product->id }}"
                    id="product_{{ $product->id }}"
                    {{ in_array($product->id, old('product_ids', $discount->products->pluck('id')->toArray())) ? 'checked' : '' }}
                >
                <label class="form-check-label" for="product_{{ $product->id }}">
                    {{ $product->name }}
                </label>
            </div>
        @endforeach
    </div>
</div>

</div>

            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-control" required>
                    <option value="active" {{ old('status', $discount->status) == 'active' ? 'selected' : '' }}>Kích hoạt</option>
                    <option value="inactive" {{ old('status', $discount->status) == 'inactive' ? 'selected' : '' }}>Không kích hoạt</option>
                </select>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <button type="submit" class="btn btn-success">💾 Lưu thay đổi</button>
        <a href="{{ route('admin.discount.index') }}" class="btn btn-secondary ms-2">← Quay lại</a>
    </div>
</form>

{{-- Script cập nhật đơn vị giảm giá --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const appliesToAllSelect = document.getElementById('applies_to_all_products');
        const productSelectionBox = document.getElementById('product-selection-box');
        const discountType = document.getElementById('discount_type');
        const unitLabel = document.getElementById('unit_label');
        const searchBox = document.getElementById('search-product');
        const productCheckboxes = document.querySelectorAll('.product-checkbox');

        // Cập nhật đơn vị hiển thị (VND hoặc %)
        function updateUnitLabel() {
            unitLabel.textContent = discountType.value === 'fixed' ? 'VND' : '%';
        }

        // Hiển thị/ẩn khung chọn sản phẩm
        function toggleProductBox() {
            if (appliesToAllSelect.value === '1') {
                productSelectionBox.style.display = 'none';
            } else {
                productSelectionBox.style.display = 'block';
            }
        }

        // Tìm kiếm sản phẩm trong checkbox
        function filterProducts() {
            const keyword = searchBox.value.toLowerCase();
            productCheckboxes.forEach(checkbox => {
                const label = checkbox.nextElementSibling.textContent.toLowerCase();
                const parent = checkbox.closest('.form-check');
                parent.style.display = label.includes(keyword) ? 'block' : 'none';
            });
        }

        // Khởi tạo ban đầu
        updateUnitLabel();
        toggleProductBox();

        // Sự kiện
        discountType.addEventListener('change', updateUnitLabel);
        appliesToAllSelect.addEventListener('change', toggleProductBox);
        searchBox.addEventListener('keyup', filterProducts);
    });
    async function generateCode(length = 10) {
        const status = document.getElementById('code-status');
        status.textContent = 'Đang tạo mã...';

        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        const getRandom = () =>
            Array.from({ length }, () => chars[Math.floor(Math.random() * chars.length)]).join('');

        let code, exists;

        do {
            code = getRandom();
            const res = await fetch(`/admin/discount/check-voucher-code?code=${code}`);

            exists = (await res.json()).exists;
        } while (exists);

        document.getElementById('voucher-code').value = code;
        status.textContent = 'Đã tạo mã duy nhất!';
    }
</script>

@endsection
