@extends('layouts.admin')

@section('title', 'Thêm mã giảm giá')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <h1>Thêm mã giảm giá</h1>
    @if (session('test'))
       <div class="alert alert-success">{{ session('test') }}</div>
   @endif
   

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('admin.discount.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label class="form-label">Tiêu đề</label>
            <input type="text" name="title" class="form-control"  value="{{ old('title') }}">
            @error('title')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Mô tả</label>
            <textarea name="description" class="form-control" >{{ old('description') }}</textarea>
            @error('description')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Mã giảm giá</label>
            <input type="text" name="code" class="form-control"  value="{{ old('code') }}">
            @error('code')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

       <div class="mb-3">
    <label class="form-label">Loại giảm giá</label>
    <select name="discount_type" id="discount_type" class="form-control">
        <option value="percentage" {{ old('discount_type') == 'percentage' ? 'selected' : '' }}>Phần trăm</option>
        <option value="fixed" {{ old('discount_type') == 'fixed' ? 'selected' : '' }}>Cố định</option>
    </select>
    @error('discount_type')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>


<div class="mb-3">
    <label class="form-label">Giá trị giảm giá</label>
    <div style="display: flex; align-items: center;">
        <input type="number" name="discount_value" value="{{ old('discount_value') }}" class="form-control" style="flex:1;">
        <span id="unit_label" style="margin-left: 10px; font-weight: bold;">
            {{ old('discount_type') == 'fixed' ? 'VND' : '%' }}
        </span>
    </div>
    @error('discount_value')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>

<script>
    document.getElementById('discount_type').addEventListener('change', function () {
        const unitLabel = document.getElementById('unit_label');
        unitLabel.textContent = this.value === 'fixed' ? 'VND' : '%';
    });
</script>


       

        <div class="mb-3">
            <label class="form-label">Ngày bắt đầu</label>
            <input type="datetime-local" name="start_date" id="start_date" class="form-control"  value="{{ old('start_date') }}">
            @error('start_date')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Ngày kết thúc</label>
            <input type="datetime-local" name="end_date" id="end_date" class="form-control"  value="{{ old('end_date') }}">
            @error('end_date')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Giảm tối đa</label>
            <input type="number" name="max_discount" class="form-control"  value="{{ old('max_discount') }}">
            @error('max_discount')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Giá trị đơn hàng tối thiểu</label>
            <input type="number" name="min_order_value" class="form-control"  value="{{ old('min_order_value') }}">
            @error('min_order_value')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Số lượng mã</label>
            <input type="number" name="quantity" class="form-control"  value="{{ old('quantity') }}">
            @error('quantity')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">Giới hạn mỗi người dùng</label>
            <input type="number" name="user_usage_limit" class="form-control"  value="{{ old('user_usage_limit') }}">
            @error('user_usage_limit')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>
<div class="mb-3">
    <label class="form-label">Áp dụng cho tất cả sản phẩm?</label>
    <select name="applies_to_all_products" id="applies_to_all_products" class="form-control">
        <option value="1" {{ old('applies_to_all_products') == '1' ? 'selected' : '' }}>Có</option>
        <option value="0" {{ old('applies_to_all_products') == '0' ? 'selected' : '' }}>Không</option>
    </select>
    @error('applies_to_all_products')
        <div class="text-danger">{{ $message }}</div>
    @enderror
</div>
{{-- 
<div class="mb-3" id="product-selection-box">
    <label class="form-label">Chọn sản phẩm áp dụng</label>
    <div class="border p-2" style="max-height: 250px; overflow-y: auto;">
        @foreach($products as $product)
            <div class="form-check">
                <input 
                    type="checkbox" 
                    name="product_ids[]" 
                    value="{{ $product->id }}" 
                    class="form-check-input" 
                    id="product_{{ $product->id }}"
                    {{ in_array($product->id, old('product_ids', [])) ? 'checked' : '' }}
                >
                <label class="form-check-label" for="product_{{ $product->id }}">
                    {{ $product->name }}
                </label>
            </div>
        @endforeach
    </div>
</div> --}}
<div class="mb-3" id="product-selection-box">
    <label class="form-label">Chọn sản phẩm áp dụng</label>
    <select name="product_ids[]" class="form-control select2" multiple>
        @foreach($products as $product)
            <option value="{{ $product->id }}"
                {{ in_array($product->id, old('product_ids', [])) ? 'selected' : '' }}>
                {{ $product->name }}
            </option>
        @endforeach
    </select>
</div>


<script>
    document.addEventListener("DOMContentLoaded", function () {
        const selectElement = document.getElementById("applies_to_all_products");
        const productBox = document.getElementById("product-selection-box");

        function toggleProductBox() {
            if (selectElement.value === "0") {
                productBox.style.display = "block";
            } else {
                productBox.style.display = "none";
            }
        }

        // Gọi khi trang vừa load (nếu có old input)
        toggleProductBox();

        // Gọi khi người dùng thay đổi select
        selectElement.addEventListener("change", toggleProductBox);
    });
</script>




        <div class="mb-3">
            <label class="form-label">Trạng thái</label>
            <select name="status" class="form-control" >
                <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Kích hoạt</option>
                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Không kích hoạt</option>
            </select>
            @error('status')
                <div class="text-danger">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Thêm mới</button>
        <a href="{{ route('admin.discount.index') }}" class="btn btn-secondary">Quay lại</a>
    </form>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const startInput = document.getElementById('start_date');
        const endInput = document.getElementById('end_date');

        startInput.addEventListener('change', function () {
            if (startInput.value) {
                endInput.min = startInput.value;
                if (endInput.value && endInput.value < startInput.value) {
                    endInput.value = '';
                }
            }
        });
    });
</script>
@endsection
