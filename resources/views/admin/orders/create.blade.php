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
                                    <div class="summary-section">
                                        <div id="discount_error_message"
                                            style="color: red; margin-top: 10px; font-weight: bold;"></div>
                                    </div>
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
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dữ liệu này cần được truyền từ Controller xuống View
            const productVariantsData = @json($productVariantsForJs ?? []);
            const discountDetailsData = @json($discountsForJs ?? []);
            // Đảm bảo discountProductsMap được truyền từ controller
            const discountProductsMap = @json($discountProductsMap ?? []); 

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
                let totalProductQuantityInCart = 0; // Tổng số lượng sản phẩm trong giỏ (cho discount nếu cần)
                
                const productItems = productsContainer.querySelectorAll('.product-item');
                let productIdsInCart = []; // Mảng chứa product_id của các sản phẩm đang có trong form

                productItems.forEach(row => {
                    const variantSelect = row.querySelector('.product-variant-select');
                    const quantityInput = row.querySelector('.product-quantity-input');
                    const totalPriceInput = row.querySelector('.product-total-price');

                    const variantId = variantSelect?.value;
                    const quantity = parseInt(quantityInput?.value) || 0;

                    let lineTotal = 0;

                    if (variantId && productVariantsData[variantId] && quantity > 0) {
                        const variantDetails = productVariantsData[variantId];
                        const price = parseFloat(variantDetails.price);
                        lineTotal = price * quantity;
                        subtotal += lineTotal;
                        totalProductQuantityInCart += quantity; // Cập nhật tổng số lượng

                        // Thu thập product_id cho logic giảm giá theo sản phẩm
                        if (variantDetails.product_id) {
                            productIdsInCart.push(variantDetails.product_id);
                        }
                    }

                    if (totalPriceInput) {
                        totalPriceInput.value = formatCurrency(lineTotal);
                    }
                });

                let totalDiscount = 0;
                const selectedDiscountId = discountSelectEl.value;

                if (selectedDiscountId && discountDetailsData[selectedDiscountId]) {
                    const discount = discountDetailsData[selectedDiscountId];
                    let amountEligibleForDiscount = 0;
                    let totalQuantityForFixedDiscount = 0; // Tổng số lượng sản phẩm áp dụng cho fixed discount

                    // --- Logic xác định tổng tiền đủ điều kiện giảm giá ---
                    if (discount.applies_to_all_products === 1) { // Áp dụng cho tất cả sản phẩm
                        amountEligibleForDiscount = subtotal;
                        totalQuantityForFixedDiscount = totalProductQuantityInCart;
                    } else { // Áp dụng cho sản phẩm cụ thể (applies_to_all_products === 0)
                        const applicableProductIdsForDiscount = discountProductsMap[selectedDiscountId] || [];
                        
                        productItems.forEach(row => {
                            const variantSelect = row.querySelector('.product-variant-select');
                            const quantityInput = row.querySelector('.product-quantity-input');
                            
                            const variantId = variantSelect?.value;
                            const quantity = parseInt(quantityInput?.value) || 0;

                            if (variantId && productVariantsData[variantId] && quantity > 0) {
                                const variantDetails = productVariantsData[variantId];
                                const currentProductId = variantDetails.product_id;

                                if (applicableProductIdsForDiscount.includes(currentProductId)) {
                                    const price = parseFloat(variantDetails.price);
                                    amountEligibleForDiscount += price * quantity;
                                    totalQuantityForFixedDiscount += quantity;
                                }
                            }
                        });

                        if (amountEligibleForDiscount === 0) {
                            // Nếu không có sản phẩm nào hợp lệ, mã giảm giá không áp dụng
                            // Không cần thông báo lỗi ở đây, chỉ là discount = 0
                        }
                    }

                    // --- Kiểm tra điều kiện tối thiểu để áp dụng mã giảm giá ---
                    if (amountEligibleForDiscount < discount.minValue) {
                        totalDiscount = 0; // Chưa đạt giá trị tối thiểu
                    } 
                    else {
                        // --- Tính toán số tiền giảm giá ---
                        let calculatedDiscount = 0;
                        if (discount.type === 'percentage') {
                            calculatedDiscount = amountEligibleForDiscount * (discount.value / 100);
                        } else if (discount.type === 'fixed') {
                            // Logic fixed discount khớp với controller:
                            // Nếu discount áp dụng cho tất cả sản phẩm, thì fixed amount là cho cả đơn hàng.
                            // Nếu chỉ áp dụng cho sản phẩm cụ thể, thì fixed amount có thể là cho mỗi sản phẩm hợp lệ,
                            // hoặc một lần cho toàn bộ phần hợp lệ.
                            // Giả định fixed là tổng fixed amount cho tất cả các sản phẩm đủ điều kiện trong đơn hàng.
                            // Hoặc nếu bạn muốn fixed per eligible product: discount.value * totalQuantityForFixedDiscount;
                            calculatedDiscount = discount.value; // Fixed amount for the whole order/eligible items
                        }
                        
                        // Áp dụng giới hạn tối đa của mã giảm giá (max_discount)
                        totalDiscount = Math.min(calculatedDiscount, discount.maxValue);
                        
                        // Đảm bảo tổng giảm giá không lớn hơn số tiền đủ điều kiện giảm giá
                        totalDiscount = Math.min(totalDiscount, amountEligibleForDiscount);
                    }
                }

                const shippingFee = parseFloat(shippingFeeInputEl.value) || 0;
                const grandTotal = subtotal - totalDiscount + shippingFee;

                summarySubtotalEl.textContent = formatCurrency(subtotal);
                summaryDiscountEl.textContent = formatCurrency(totalDiscount);
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
    </script> --}}
    {{-- <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dữ liệu này cần được truyền từ Controller xuống View
            const productVariantsData = @json($productVariantsForJs ?? []);
            const discountDetailsData = @json($discountsForJs ?? []);
            const discountProductsMap = @json($discountProductsMap ?? []);

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
                let totalProductQuantityInCart = 0; // Tổng số lượng sản phẩm trong giỏ (cho discount nếu cần)

                const productItems = productsContainer.querySelectorAll('.product-item');

                productItems.forEach(row => {
                    const variantSelect = row.querySelector('.product-variant-select');
                    const quantityInput = row.querySelector('.product-quantity-input');
                    const totalPriceInput = row.querySelector('.product-total-price');

                    const variantId = variantSelect?.value;
                    const quantity = parseInt(quantityInput?.value) || 0;

                    let lineTotal = 0;

                    if (variantId && productVariantsData[variantId] && quantity > 0) {
                        const variantDetails = productVariantsData[variantId];
                        const price = parseFloat(variantDetails.price);
                        lineTotal = price * quantity;
                        subtotal += lineTotal;
                        totalProductQuantityInCart += quantity; // Cập nhật tổng số lượng
                    }

                    if (totalPriceInput) {
                        totalPriceInput.value = formatCurrency(lineTotal);
                    }
                });

                let totalDiscount = 0;
                const selectedDiscountId = discountSelectEl.value;

                if (selectedDiscountId && discountDetailsData[selectedDiscountId]) {
                    const discount = discountDetailsData[selectedDiscountId];
                    let amountEligibleForDiscount = 0;
                    let totalQuantityForFixedDiscount =
                    0; // Tổng số lượng sản phẩm đủ điều kiện cho fixed discount (Frontend)

                    // --- Logic xác định tổng tiền đủ điều kiện giảm giá ---
                    if (parseInt(discount.applies_to_all_products) ===
                        1) { // Sử dụng parseInt để đảm bảo so sánh số
                        amountEligibleForDiscount = subtotal;
                        totalQuantityForFixedDiscount = totalProductQuantityInCart;
                    } else { // Áp dụng cho sản phẩm cụ thể (applies_to_all_products === 0)
                        const applicableProductIdsForDiscount = discountProductsMap[selectedDiscountId] || [];

                        productItems.forEach(row => {
                            const variantSelect = row.querySelector('.product-variant-select');
                            const quantityInput = row.querySelector('.product-quantity-input');

                            const variantId = variantSelect?.value;
                            const quantity = parseInt(quantityInput?.value) || 0;

                            if (variantId && productVariantsData[variantId] && quantity > 0) {
                                const variantDetails = productVariantsData[variantId];
                                const currentProductId = variantDetails.product_id;

                                if (applicableProductIdsForDiscount.includes(currentProductId)) {
                                    const price = parseFloat(variantDetails.price);
                                    amountEligibleForDiscount += price * quantity;
                                    totalQuantityForFixedDiscount += quantity;
                                }
                            }
                        });

                        if (amountEligibleForDiscount === 0 && parseFloat(discount.min_order_value) >
                            0) { // Kiểm tra min_order_value là số
                            totalDiscount = 0;
                            updateSummaryDisplay(subtotal, totalDiscount, parseFloat(shippingFeeInputEl.value) || 0,
                                subtotal + (parseFloat(shippingFeeInputEl.value) || 0)
                                ); // Cập nhật tổng cuối để không bị NaN
                            return;
                        }
                    }

                    // --- Kiểm tra điều kiện tối thiểu để áp dụng mã giảm giá ---
                    if (amountEligibleForDiscount < parseFloat(discount
                        .min_order_value)) { // SỬA ĐỔI: discount.minValue -> discount.min_order_value
                        totalDiscount = 0;
                    }
                    // --- Kiểm tra điều kiện tối đa của đơn hàng để áp dụng mã giảm giá (nếu có) ---
                    else if (discount.max_order_value && subtotal > parseFloat(discount
                        .max_order_value)) { // SỬA ĐỔI: max_order_value
                        totalDiscount = 0;
                    } else {
                        // --- Tính toán số tiền giảm giá ---
                        let calculatedDiscount = 0;
                        if (discount.discount_type ===
                            'percentage') { // SỬA ĐỔI: discount.type -> discount.discount_type
                            calculatedDiscount = amountEligibleForDiscount * (parseFloat(discount.discount_value) /
                                100); // SỬA ĐỔI: discount.value -> discount.discount_value
                        } else if (discount.discount_type ===
                            'fixed') { // SỬA ĐỔI: discount.type -> discount.discount_type
                            calculatedDiscount = parseFloat(discount.discount_value) *
                            totalQuantityForFixedDiscount; // SỬA ĐỔI: discount.value -> discount.discount_value
                        }

                        // Áp dụng giới hạn tối đa của mã giảm giá (max_discount)
                        totalDiscount = Math.min(calculatedDiscount, parseFloat(discount
                        .max_discount)); // SỬA ĐỔI: discount.maxValue -> discount.max_discount

                        // Đảm bảo tổng giảm giá không lớn hơn số tiền đủ điều kiện giảm giá
                        totalDiscount = Math.min(totalDiscount, amountEligibleForDiscount);
                    }
                }

                const shippingFee = parseFloat(shippingFeeInputEl.value) || 0;
                const grandTotal = subtotal - totalDiscount + shippingFee;

                updateSummaryDisplay(subtotal, totalDiscount, shippingFee, grandTotal);
            }

            function updateSummaryDisplay(subtotal, totalDiscount, shippingFee, grandTotal) {
                summarySubtotalEl.textContent = formatCurrency(subtotal);
                summaryDiscountEl.textContent = formatCurrency(totalDiscount);
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
    </script> --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Dữ liệu này cần được truyền từ Controller xuống View
            const productVariantsData = @json($productVariantsForJs ?? []);
            const discountDetailsData = @json($discountsForJs ?? []);
            const discountProductsMap = @json($discountProductsMap ?? []);

            const productsContainer = document.getElementById('products_container');
            const addProductRowBtn = document.getElementById('addProductRowBtn');
            const discountSelectEl = document.getElementById('discount_id_select');
            const shippingFeeInputEl = document.getElementById('shipping_fee_input');

            const summarySubtotalEl = document.getElementById('summary_subtotal');
            const summaryDiscountEl = document.getElementById('summary_discount');
            const summaryShippingFeeEl = document.getElementById('summary_shipping_fee');
            const summaryGrandTotalEl = document.getElementById('summary_grand_total');
            const discountErrorMessageEl = document.getElementById(
            'discount_error_message'); // Thêm dòng này: Tham chiếu đến phần tử hiển thị lỗi

            function formatCurrency(amount) {
                return new Intl.NumberFormat('vi-VN', {
                    style: 'currency',
                    currency: 'VND'
                }).format(amount);
            }

            function calculateAndUpdateSummary() {
                let subtotal = 0;
                let totalProductQuantityInCart = 0;

                const productItems = productsContainer.querySelectorAll('.product-item');

                // Xóa thông báo lỗi cũ mỗi khi tính toán lại
                if (discountErrorMessageEl) { // Kiểm tra để đảm bảo phần tử tồn tại trước khi thao tác
                    discountErrorMessageEl.textContent = '';
                }

                productItems.forEach(row => {
                    const variantSelect = row.querySelector('.product-variant-select');
                    const quantityInput = row.querySelector('.product-quantity-input');
                    const totalPriceInput = row.querySelector('.product-total-price');

                    const variantId = variantSelect?.value;
                    const quantity = parseInt(quantityInput?.value) || 0;

                    let lineTotal = 0;

                    if (variantId && productVariantsData[variantId] && quantity > 0) {
                        const variantDetails = productVariantsData[variantId];
                        const price = parseFloat(variantDetails.price);
                        lineTotal = price * quantity;
                        subtotal += lineTotal;
                        totalProductQuantityInCart += quantity;
                    }

                    if (totalPriceInput) {
                        totalPriceInput.value = formatCurrency(lineTotal);
                    }
                });

                let totalDiscount = 0;
                const selectedDiscountId = discountSelectEl.value;

                if (selectedDiscountId && discountDetailsData[selectedDiscountId]) {
                    const discount = discountDetailsData[selectedDiscountId];
                    let amountEligibleForDiscount = 0;
                    let totalQuantityForFixedDiscount = 0;

                    if (parseInt(discount.applies_to_all_products) === 1) {
                        amountEligibleForDiscount = subtotal;
                        totalQuantityForFixedDiscount = totalProductQuantityInCart;
                    } else {
                        const applicableProductIdsForDiscount = discountProductsMap[selectedDiscountId] || [];

                        productItems.forEach(row => {
                            const variantSelect = row.querySelector('.product-variant-select');
                            const quantityInput = row.querySelector('.product-quantity-input');

                            const variantId = variantSelect?.value;
                            const quantity = parseInt(quantityInput?.value) || 0;

                            if (variantId && productVariantsData[variantId] && quantity > 0) {
                                const variantDetails = productVariantsData[variantId];
                                const currentProductId = variantDetails.product_id;

                                if (applicableProductIdsForDiscount.includes(currentProductId)) {
                                    const price = parseFloat(variantDetails.price);
                                    amountEligibleForDiscount += price * quantity;
                                    totalQuantityForFixedDiscount += quantity;
                                }
                            }
                        });

                        if (amountEligibleForDiscount === 0 && parseFloat(discount.min_order_value) > 0) {
                            // Hiển thị thông báo nếu không có sản phẩm đủ điều kiện và min_order_value > 0
                            if (discountErrorMessageEl) {
                                discountErrorMessageEl.textContent =
                                    'Mã giảm giá không áp dụng cho bất kỳ sản phẩm nào trong giỏ hàng hiện tại.';
                            }
                            updateSummaryDisplay(subtotal, totalDiscount, parseFloat(shippingFeeInputEl.value) || 0,
                                subtotal + (parseFloat(shippingFeeInputEl.value) || 0));
                            return;
                        }
                    }

                    if (amountEligibleForDiscount < parseFloat(discount
                        .min_order_value)) { // Đảm bảo min_order_value là số
                        totalDiscount = 0;
                        // Hiển thị thông báo khi không đạt giá trị tối thiểu
                        if (discountErrorMessageEl) {
                            const formattedMinOrderValue = formatCurrency(parseFloat(discount.min_order_value));
                            discountErrorMessageEl.textContent =
                                `Đơn hàng chưa đủ giá trị tối thiểu (${formattedMinOrderValue}) để áp dụng mã giảm giá.`;
                        }
                    } else if (discount.max_order_value && subtotal > parseFloat(discount
                        .max_order_value)) { // Đảm bảo max_order_value là số
                        totalDiscount = 0;
                        // Hiển thị thông báo khi vượt quá giá trị tối đa
                        if (discountErrorMessageEl) {
                            const formattedMaxOrderValue = formatCurrency(parseFloat(discount.max_order_value));
                            discountErrorMessageEl.textContent =
                                `Tổng giá trị đơn hàng vượt quá giới hạn tối đa (${formattedMaxOrderValue}) cho phép của mã giảm giá.`;
                        }
                    } else {
                        let calculatedDiscount = 0;
                        if (discount.discount_type === 'percentage') {
                            calculatedDiscount = amountEligibleForDiscount * (parseFloat(discount.discount_value) /
                                100);
                        } else if (discount.discount_type === 'fixed') {
                            calculatedDiscount = parseFloat(discount.discount_value) *
                            totalQuantityForFixedDiscount;
                        }

                        totalDiscount = Math.min(calculatedDiscount, parseFloat(discount.max_discount));
                        totalDiscount = Math.min(totalDiscount, amountEligibleForDiscount);
                    }
                }

                const shippingFee = parseFloat(shippingFeeInputEl.value) || 0;
                const grandTotal = subtotal - totalDiscount + shippingFee;

                updateSummaryDisplay(subtotal, totalDiscount, shippingFee, grandTotal);
            }

            function updateSummaryDisplay(subtotal, totalDiscount, shippingFee, grandTotal) {
                summarySubtotalEl.textContent = formatCurrency(subtotal);
                summaryDiscountEl.textContent = formatCurrency(totalDiscount);
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
