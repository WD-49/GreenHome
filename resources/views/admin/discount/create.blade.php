@extends('layouts.admin')

@section('title', 'Thêm mã giảm giá')

@section('content')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
    .form-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
        animation: fadeIn 0.6s ease-in-out;
    }

    .form-section {
        background-color: #fff;
        padding: 24px;
        border-radius: 16px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        transition: all 0.3s ease;
    }

    .form-section:hover {
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        transform: translateY(-2px);
    }

    .form-label {
        font-weight: 600;
        color: #333;
    }

    .form-control,
    select,
    textarea {
        transition: all 0.3s;
        border-radius: 8px;
    }

    .form-control:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.25rem rgba(79, 70, 229, 0.25);
    }

    .text-danger {
        font-size: 0.875rem;
        color: #dc3545;
    }

    .btn-primary {
        background-color: #4f46e5;
        border: none;
        transition: background-color 0.3s ease;
    }

    .btn-primary:hover {
        background-color: #3730a3;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
        transition: background-color 0.3s ease;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(16px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .select2-container--default .select2-selection--multiple {
        border-radius: 8px;
        border: 1px solid #ced4da;
        padding: 4px;
    }
</style>

<h1 class="mb-4"> Thêm mã giảm giá mới</h1>

@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('admin.discount.store') }}" method="POST">
    @csrf
    <div class="form-container">
        <!-- Bảng bên trái -->
        <div class="form-section">
            <div class="mb-3">
                <label class="form-label">Tiêu đề</label>
                <input type="text" name="title" class="form-control" value="{{ old('title') }}">
                @error('title')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Mô tả</label>
                <textarea name="description" class="form-control">{{ old('description') }}</textarea>
                @error('description')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Mã</label>
                <div class="input-group">
                    <input type="text" name="code" id="voucher-code" class="form-control"
                        value="{{ old('code') }}">
                    <button type="button" class="btn btn-outline-secondary" onclick="generateCode()">Tạo mã</button>
                </div>
                @error('code')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>


            <div class="mb-3">
                <label class="form-label">Loại</label>
                <select name="discount_type" id="discount_type" class="form-control">
                    <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Phần trăm
                    </option>
                    <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Cố định</option>
                </select>
                @error('discount_type')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Giá trị giảm</label>
                <div style="display: flex; align-items: center;">
                    <input type="number" name="discount_value" class="form-control"
                        value="{{ old('discount_value') }}"
                        @if(old('discount_type')=='percent' ) @endif>
                    <span id="unit_label" style="margin-left: 10px; font-weight: bold;">
                        {{ old('discount_type') == 'fixed' ? 'VND' : '%' }}
                    </span>
                </div>
                @error('discount_value')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>


           
            <div class="mb-3">
                <label class="form-label">Giá trị đơn hàng tối thiểu</label>
                <input type="number" name="min_order_value" class="form-control" value="{{ old('min_order_value') }}">
                @error('min_order_value')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Giá trị giảm tối đa</label>
                <input type="number" name="max_discount" class="form-control" value="{{ old('max_discount') }}">
                @error('max_discount')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Bảng bên phải -->
        <div class="form-section">
            <div class="mb-3">
                <label class="form-label">Số lượng mã</label>
                <input type="number" name="quantity" class="form-control" value="{{ old('quantity') }}">
                @error('quantity')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Giới hạn mỗi người</label>
                <input type="number" name="user_usage_limit" class="form-control"
                    value="{{ old('user_usage_limit') }}">
                @error('user_usage_limit')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Bắt đầu</label>
                <input type="datetime-local" name="start_date" id="start_date" class="form-control"
                    value="{{ old('start_date') }}">
                @error('start_date')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Kết thúc</label>
                <input type="datetime-local" name="end_date" id="end_date" class="form-control"
                    value="{{ old('end_date') }}">
                @error('end_date')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>

            <div class="mb-3">
                <label class="form-label">Áp dụng cho tất cả sản phẩm?</label>
                <select name="applies_to_all_products" id="applies_to_all_products" class="form-control">
                    <option value="1" {{ old('applies_to_all_products') == '1' ? 'selected' : '' }}>Có</option>
                    <option value="0" {{ old('applies_to_all_products') == '0' ? 'selected' : '' }}>Không
                    </option>
                </select>
                @error('applies_to_all_products')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>


            <div class="mb-3" id="product-selection-box"
                style="{{ old('applies_to_all_products') == '1' ? 'display: none;' : '' }}">
                <label class="form-label">Sản phẩm áp dụng</label>
                <input type="text" id="search-product" class="form-control mb-2"
                    placeholder="Tìm kiếm sản phẩm...">

                <div id="product-checkbox-list">
                    @foreach ($products as $product)
                    <div class="form-check">
                        <input class="form-check-input product-checkbox" type="checkbox" name="product_ids[]"
                            value="{{ $product->id }}" id="product_{{ $product->id }}"
                            data-prices="{{ implode(',', $product->productVariants->pluck('price')->toArray()) }}"
                            {{ in_array($product->id, old('product_ids', [])) ? 'checked' : '' }}>
                        <label class="form-check-label" for="product_{{ $product->id }}">
                            {{ $product->name }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>



            <div class="mb-3">
                <label class="form-label">Trạng thái</label>
                <select name="status" class="form-control">
                    <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Kích hoạt</option>
                    <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Không kích hoạt
                    </option>
                </select>
                @error('status')
                <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="mt-4 d-flex gap-3">
        <button type="submit" class="btn btn-primary">Thêm mới</button>
        <a href="{{ route('admin.discount.index') }}" class="btn btn-secondary">Quay lại</a>
    </div>


</form>
@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif
@endsection

@section('scripts')
<script>
    function updateSelectableProducts() {
        const discountType = document.getElementById('discount_type').value;
        const discountValue = parseFloat(document.querySelector('input[name="discount_value"]').value || 0);
        const checkboxes = document.querySelectorAll('.product-checkbox');
        const appliesToAllSelect = document.getElementById('applies_to_all_products');
        const appliesToAll = appliesToAllSelect?.value === '1';

        checkboxes.forEach(checkbox => {
            const priceList = checkbox.dataset.prices?.split(',').map(p => parseFloat(p)) || [];
            const parent = checkbox.closest('.form-check');
            const anyVariantValid = priceList.some(p => discountType !== 'fixed' || p > discountValue);

            if (!appliesToAll && !anyVariantValid) {
                checkbox.disabled = true;
                checkbox.checked = false;
                parent.style.opacity = 0.5;
                checkbox.title = `Tất cả biến thể sản phẩm có giá thấp hơn hoặc bằng ${discountValue} VND.`;
            } else {
                checkbox.disabled = false;
                parent.style.opacity = 1;
                checkbox.removeAttribute('title');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const discountType = document.getElementById('discount_type');
        const unitLabel = document.getElementById('unit_label');
        const appliesToAllSelect = document.getElementById('applies_to_all_products');
        const productSelectionBox = document.getElementById('product-selection-box');
        const discountValueInput = document.querySelector('input[name="discount_value"]');

        function updateUnitLabel() {
            unitLabel.textContent = discountType.value === 'fixed' ? 'VND' : '%';
        }

        function toggleProductBox() {
            productSelectionBox.style.display = appliesToAllSelect.value === '1' ? 'none' : 'block';
        }

        updateUnitLabel();
        toggleProductBox();
        updateSelectableProducts();

        discountType.addEventListener('change', () => {
            // Cập nhật max value tùy theo loại giảm giá
            if (discountType.value === 'percent') {
                discountValueInput.max = 100;
            } else {
                discountValueInput.removeAttribute('max');
            }

            updateUnitLabel();
            updateSelectableProducts();
        });

        discountValueInput.addEventListener('input', updateSelectableProducts);
        appliesToAllSelect.addEventListener('change', () => {
            toggleProductBox();
            updateSelectableProducts();
        });

        const searchBox = document.getElementById('search-product');
        const productCheckboxes = document.querySelectorAll('.product-checkbox');

        searchBox?.addEventListener('keyup', function() {
            const keyword = this.value.toLowerCase();
            productCheckboxes.forEach(checkbox => {
                const label = checkbox.nextElementSibling.textContent.toLowerCase();
                const parent = checkbox.closest('.form-check');
                parent.style.display = label.includes(keyword) ? 'block' : 'none';
            });
        });

        // Fix: Only init Select2 if element exists
        if ($('.select2').length) {
            $('.select2').select2({
                placeholder: "Chọn sản phẩm...",
                allowClear: true,
                width: '100%'
            });
        }

        if (!document.getElementById('voucher-code').value) {
            generateCode();
        }
    });

    async function generateCode(length = 10) {
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
}

</script>
@endsection