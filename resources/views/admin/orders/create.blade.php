@extends('layouts.admin')
@section('title', 'Tạo đơn hàng mới')

@section('content')
    <div class="container py-5">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h3 class="mb-0">Tạo đơn hàng mới</h3>
            </div>
            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('admin.orders.store') }}" method="POST" id="createOrderForm">
                    @csrf

                    <div class="mb-3">
                        <label for="user_id" class="form-label">Người đặt hàng <span class="text-danger">*</span></label>
                        <select name="user_id" id="user_id" class="form-select @error('user_id') is-invalid @enderror">
                            <option value="">-- Chọn người dùng --</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} - {{ $user->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <h5 class="mb-3">Thông tin người nhận</h5>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="shipping_name" class="form-label">Tên người nhận <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="shipping_name" id="shipping_name"
                                class="form-control @error('shipping_name') is-invalid @enderror"
                                value="{{ old('shipping_name') }}">
                            @error('shipping_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="shipping_phone" class="form-label">Số điện thoại <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="shipping_phone" id="shipping_phone"
                                class="form-control @error('shipping_phone') is-invalid @enderror"
                                value="{{ old('shipping_phone') }}">
                            @error('shipping_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="shipping_address" class="form-label">Địa chỉ <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="shipping_address" id="shipping_address"
                                class="form-control @error('shipping_address') is-invalid @enderror"
                                value="{{ old('shipping_address') }}">
                            @error('shipping_address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h5 class="mb-3">Chọn sản phẩm</h5>
                    <div id="products_container" class="mb-3">
                        {{-- Dòng sản phẩm mẫu sẽ được clone --}}
                        <div class="row product-item mb-2 align-items-center">
                            <div class="col-md-6">
                                <label class="form-label visually-hidden">Sản phẩm</label>
                                <select name="products[]" class="form-select product-variant-select">
                                    <option value="">-- Chọn biến thể sản phẩm --</option>
                                    @foreach ($productVariants as $variant)
                                        <option value="{{ $variant->id }}" data-price="{{ $variant->price }}">
                                            {{ $variant->product->name }} ({{ $variant->sku }}) -
                                            {{ number_format($variant->price, 0, ',', '.') }} VND
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label visually-hidden">Số lượng</label>
                                <input type="number" name="quantities[]" class="form-control product-quantity-input"
                                    min="1" value="1">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label visually-hidden">Thành tiền</label>
                                <input type="text" class="form-control product-total-price" readonly>
                            </div>
                            <div class="col-md-1">
                                <button type="button" class="btn btn-danger btn-sm remove-product-row">&times;</button>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-success mb-3" id="addProductRowBtn">+ Thêm sản
                        phẩm</button>

                    <div class="mb-3">
                        <label for="discount_id" class="form-label">Mã giảm giá (nếu có)</label>
                        <select name="discount_id" id="discount_id_select" class="form-select">
                            <option value="">-- Không áp dụng --</option>
                            @if (isset($discounts))
                                @foreach ($discounts as $discount)
                                    <option value="{{ $discount->id }}" data-type="{{ $discount->discount_type }}"
                                        data-value="{{ (float) $discount->discount_value }}"
                                        data-max-value="{{ (float) ($discount->max_discount ?? 0) }}"
                                        data-min-value="{{ (float) ($discount->min_order_value ?? 0) }}"
                                        {{ old('discount_id') == $discount->id ? 'selected' : '' }}>
                                        {{ $discount->code }}
                                        (@if ($discount->discount_type == 'percentage')
                                            Giảm {{ $discount->discount_value }}% (tối đa
                                            {{ number_format($discount->max_discount) }}đ)
                                        @else
                                            Giảm {{ number_format($discount->discount_value) }}đ
                                        @endif
                                        - ĐH tối thiểu: {{ number_format($discount->min_order_value) }}đ
                                        - Còn: {{ $discount->quantity }} lượt)
                                    </option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="payment_method_id" class="form-label">Phương thức thanh toán <span
                                    class="text-danger">*</span></label>
                            <select name="payment_method_id" id="payment_method_id"
                                class="form-select @error('payment_method_id') is-invalid @enderror">
                                @if (isset($payMethods))
                                    @foreach ($payMethods as $item)
                                        <option value="{{ $item->id }}"
                                            {{ old('payment_method_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                            @error('payment_method_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="shipping_fee" class="form-label">Phí vận chuyển <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="shipping_fee" id="shipping_fee_input"
                                class="form-control @error('shipping_fee') is-invalid @enderror" min="0"
                                value="{{ old('shipping_fee', 0) }}">
                            @error('shipping_fee')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="note" class="form-label">Ghi chú</label>
                            <textarea name="note" id="note" class="form-control" rows="1">{{ old('note') }}</textarea>
                        </div>
                    </div>

                    <div class="row justify-content-end mt-4">
                        <div class="col-md-6">
                            <h5 class="mb-3">Tóm tắt đơn hàng</h5>
                            <table class="table table-bordered">
                                <tbody>
                                    <tr>
                                        <th>Tổng tiền hàng (tạm tính)</th>
                                        <td id="summary_subtotal" class="text-end">0 VND</td>
                                    </tr>
                                    <tr>
                                        <th>Giảm giá (tạm tính)</th>
                                        <td id="summary_discount" class="text-end">0 VND</td>
                                    </tr>
                                    <tr>
                                        <th>Phí vận chuyển</th>
                                        <td id="summary_shipping_fee" class="text-end">0 VND</td>
                                    </tr>
                                    <tr class="table-success">
                                        <th><strong>Tổng thanh toán (tạm tính)</strong></th>
                                        <td id="summary_grand_total" class="text-end"><strong>0 VND</strong></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="submit" class="btn btn-primary">Tạo đơn hàng</button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Hủy</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dữ liệu này cần được truyền từ Controller xuống View
            // Ví dụ: $productVariantsForJs = $productVariants->mapWithKeys(function($v){ return [$v->id => ['price' => $v->price, 'name' => $v->product->name, 'sku' => $v->sku]]; });
            // Và $discountsForJs = $discounts->mapWithKeys(function($d){ return [$d->id => ['type' => $d->discount_type, ...]]; });
            // Hãy đảm bảo bạn đã truyền biến $productVariantsForJs và $discountsForJs từ controller create()
            const productVariantsData =
                @json($productVariantsForJs ?? []); // Sử dụng ?? [] để tránh lỗi nếu biến không tồn tại
            const discountDetailsData = @json($discountsForJs ?? []);

            const productsContainer = document.getElementById('products_container');
            const addProductRowBtn = document.getElementById('addProductRowBtn');
            const discountSelectEl = document.getElementById('discount_id_select');
            const shippingFeeInputEl = document.getElementById('shipping_fee_input');

            const summarySubtotalEl = document.getElementById('summary_subtotal');
            const summaryDiscountEl = document.getElementById('summary_discount');
            const summaryShippingFeeEl = document.getElementById('summary_shipping_fee');
            const summaryGrandTotalEl = document.getElementById('summary_grand_total');

            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(amount);
            }

            function calculateAndUpdateSummary() {
                let subtotal = 0;

                const productItems = productsContainer.querySelectorAll('.product-item');
                productItems.forEach(row => {
                    const variantSelect = row.querySelector('.product-variant-select');
                    const quantityInput = row.querySelector('.product-quantity-input');
                    const totalPriceInput = row.querySelector('.product-total-price');

                    const variantId = variantSelect?.value;
                    const quantity = parseInt(quantityInput?.value) || 0;

                    let lineTotal = 0;

                    if (variantId && productVariantsData[variantId] && quantity > 0) {
                        const price = parseFloat(productVariantsData[variantId].price);
                        lineTotal = price * quantity;
                        subtotal += lineTotal;
                    }

                    if (totalPriceInput) {
                        totalPriceInput.value = formatCurrency(lineTotal);
                    }
                });

                // Tạm thời bỏ qua discount để hiển thị tổng đơn giản
                const shippingFee = parseFloat(shippingFeeInputEl.value) || 0;
                const grandTotal = subtotal + shippingFee;

                summarySubtotalEl.textContent = formatCurrency(subtotal);
                summaryDiscountEl.textContent = formatCurrency(0); // nếu chưa xử lý giảm giá
                summaryShippingFeeEl.textContent = formatCurrency(shippingFee);
                summaryGrandTotalEl.innerHTML = `<strong>${formatCurrency(grandTotal)}</strong>`;
            }



            function addEventListenersToRow(rowElement) {
                const variantSelect = rowElement.querySelector('.product-variant-select');
                const quantityInput = rowElement.querySelector('.product-quantity-input');
                const removeBtn = rowElement.querySelector('.remove-product-row');

                if (variantSelect) {
                    variantSelect.addEventListener('change', calculateAndUpdateSummary);
                }
                if (quantityInput) {
                    quantityInput.addEventListener('input', calculateAndUpdateSummary);
                }
                if (removeBtn) {
                    removeBtn.addEventListener('click', function() {
                        rowElement.remove();
                        calculateAndUpdateSummary(); // Cập nhật lại tổng sau khi xóa
                    });
                }
            }

            // Gắn event cho các dòng sản phẩm đã có sẵn (nếu có từ old input)
            productsContainer.querySelectorAll('.product-item').forEach(addEventListenersToRow);

            if (addProductRowBtn) {
                addProductRowBtn.addEventListener('click', function() {
                    const firstProductItem = productsContainer.querySelector('.product-item');
                    if (firstProductItem) {
                        const newRow = firstProductItem.cloneNode(true);
                        // Reset giá trị cho hàng mới
                        newRow.querySelector('.product-variant-select').selectedIndex = 0;
                        newRow.querySelector('.product-quantity-input').value = '1';
                        if (newRow.querySelector('.product-total-price')) {
                            newRow.querySelector('.product-total-price').value = '';
                        }
                        productsContainer.appendChild(newRow);
                        addEventListenersToRow(newRow); // Gắn event cho hàng mới
                        calculateAndUpdateSummary(); // Cập nhật lại tổng
                    } else {
                        // Xử lý trường hợp không có dòng mẫu nào (có thể tạo HTML từ đầu)
                        console.warn("Không tìm thấy dòng sản phẩm mẫu để clone.");
                    }
                });
            }

            if (discountSelectEl) {
                discountSelectEl.addEventListener('change', calculateAndUpdateSummary);
            }
            if (shippingFeeInputEl) {
                shippingFeeInputEl.addEventListener('input', calculateAndUpdateSummary);
            }

            // Tính toán lần đầu khi tải trang
            calculateAndUpdateSummary();
        });
    </script>
@endpush
