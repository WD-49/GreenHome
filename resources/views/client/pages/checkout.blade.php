@extends('layouts.app')

@section('content')
    <section class="section-breadcrumb">
        <div class="cr-breadcrumb-image">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="cr-breadcrumb-title">
                            <h2>Checkout</h2>
                            <span> <a href="index.html">Home</a> - Checkout</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Checkout section -->
    <section class="cr-checkout-section padding-tb-100">
        <div class="container">
            <div class="row">
                <!-- Sidebar Area Start -->
                <div class="cr-checkout-rightside col-lg-4 col-md-12">
                    <div class="cr-sidebar-wrap">
                        <!-- Sidebar Summary Block -->
                        <div class="cr-sidebar-block">
                            <div class="cr-sb-title">
                                <h3 class="cr-sidebar-title">Thông tin đơn hàng</h3>
                            </div>
                            <hr>
                            <div class="cr-sb-block-content">

                                <div class="cr-checkout-pro">
                                    <div class="col-sm-12 mb-6" id="checkout-product-list">

                                    </div>

                                </div>
                                <div class="cr-checkout-summary">

                                </div>
                                <div class="cr-discount-select mt-3">
                                    <label for="discount-select" class="fw-bold">Chọn mã giảm giá</label>
                                    <select id="discount-select" class="form-select">
                                        <option value="" selected>-- Chọn mã giảm giá --</option>
                                    </select>
                                    <div id="discount-detail" class="mt-2" style="display: none;"></div>
                                </div>

                            </div>
                        </div>
                        <!-- Sidebar Summary Block -->
                    </div>

                    {{-- </div> --}}
                    <div class="cr-sidebar-wrap cr-checkout-pay-wrap">
                        <!-- Sidebar Payment Block -->
                        <div class="cr-sidebar-block">
                            <div class="cr-sb-title">
                                <h3 class="cr-sidebar-title">Payment Method</h3>
                            </div>
                            <div class="cr-sb-block-content">
                                <div class="cr-checkout-pay">
                                    <div class="cr-pay-desc">Please select the preferred payment method to use on this
                                        order.</div>
                                    <form action="#" class="payment-options" id="payment-options">

                                    </form>
                                </div>
                            </div>
                        </div>
                        <!-- Sidebar Payment Block -->
                    </div>

                </div>
                <div class="cr-checkout-leftside col-lg-8 col-md-12 m-t-991">
                    <!-- checkout content Start -->
                    <div class="cr-checkout-content">
                        <div class="cr-checkout-inner">

                            <div class="cr-checkout-wrap">
                                <div class="cr-checkout-block cr-check-bill">
                                    <h3 class="cr-checkout-title">Billing Details</h3>
                                    <div class="cr-bl-block-content">
                                        <div class="cr-check-bill-form mb-minus-24">
                                            <form action="#" method="post">
                                                <span class="cr-bill-wrap cr-bill-half">
                                                    <label>Họ và tên</label>
                                                    <input type="text" name="fullname" placeholder="Nhập họ và tên"
                                                        required>
                                                </span>

                                                <span class="cr-bill-wrap cr-bill-half">
                                                    <label>Số điện thoại</label>
                                                    <input type="text" name="phone" placeholder="Số điện thoại"
                                                        required>
                                                </span>



                                                <span class="cr-bill-wrap cr-bill-half">
                                                    <label>Thành phố</label>
                                                    <span class="cr-bl-select-inner">
                                                        <select name="province" id="province" class="cr-bill-select"
                                                            required>
                                                            <option selected disabled>Chọn tỉnh / thành</option>
                                                        </select>
                                                    </span>
                                                </span>

                                                <span class="cr-bill-wrap cr-bill-half">
                                                    <label>Quận / Huyện</label>
                                                    <span class="cr-bl-select-inner">
                                                        <select name="district" id="district" class="cr-bill-select"
                                                            required disabled>
                                                            <option selected disabled>Chọn quận / huyện</option>
                                                        </select>
                                                    </span>
                                                </span>

                                                <span class="cr-bill-wrap cr-bill-half">
                                                    <label>Xã / Phường</label>
                                                    <span class="cr-bl-select-inner">
                                                        <select name="ward" id="ward" class="cr-bill-select"
                                                            required disabled>
                                                            <option selected disabled>Chọn xã / phường</option>
                                                        </select>
                                                    </span>
                                                </span>
                                                <span class="cr-bill-wrap">
                                                    <label>Địa chỉ chi tiết</label>
                                                    <input type="text" name="address_detail"
                                                        placeholder="Ví dụ: 123 đường A, khu B" required>
                                                </span>
                                                <span class="cr-bill-wrap cr-bill">
                                                    <label>ghi chú</label>
                                                    <input type="text" name="note" placeholder="Nhập ghi chú"
                                                        required>
                                                </span>
                                            </form>
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <span class="cr-check-order-btn">
                                <a class="cr-button mt-30" href="#" id="place-order-btn">Place Order</a>
                            </span>
                        </div>
                    </div>
                    <!--cart content End -->
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        if (typeof formatVND !== 'function') {
            const formatVND = number => number.toLocaleString('vi-VN') + ' ₫';
        }

        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const type = urlParams.get('type') || 'full';
            const ids = urlParams.getAll('ids[]');

            let apiUrl = `{{ route('checkout.data') }}?type=${type}`;
            if (type === 'selected' && ids.length > 0) {
                ids.forEach(id => apiUrl += `&ids[]=${id}`);
            }

            fetch(apiUrl)
                .then(res => res.json())
                .then(data => {
                    if (!data.success || !data.items || data.items.length === 0) {
                        document.getElementById('checkout-product-list').innerHTML =
                            '<p>Không có sản phẩm nào để thanh toán.</p>';
                        return;
                    }

                    const items = data.items;
                    const shippingFee = parseFloat(data.shippingFee || 0);
                    window.shippingFee = shippingFee;
                    const validDiscounts = data.validDiscounts || [];

                    renderProductList(items);
                    window.updatedItems = items;
                    const total = calculateOrderTotal(items);
                    renderSummary(total, 0, shippingFee);
                    renderPaymentMethods(data.paymentMethods || []);
                    renderDiscountOptions(validDiscounts, total);

                    const discountSelect = document.getElementById('discount-select');
                    discountSelect.addEventListener('change', () => {
                        handleDiscountChange(discountSelect, validDiscounts, items, shippingFee);
                    });

                    initLocationDropdowns();
                })
                .catch(error => {
                    console.error('Lỗi khi tải dữ liệu thanh toán:', error);
                    document.getElementById('checkout-product-list').innerHTML =
                        '<p>Không thể tải dữ liệu đơn hàng.</p>';
                });

            function renderProductList(items) {
                const container = document.getElementById('checkout-product-list');
                container.innerHTML = '';
                items.forEach(item => {
                    const variant = item.product_variant;
                    const product = variant.product;
                    const image = variant.image || product.image || 'default.jpg';
                    const name = product.name;
                    const price = parseFloat(variant.price);
                    const quantity = item.quantity;
                    const subtotal = price * quantity;
                    // Kiểm tra item có giảm giá hay không
                    const hasDiscount = item._discount_amount && item._discount_amount > 0;

                    // Giá sau giảm (nếu có), nếu không thì giá gốc
                    const priceAfterDiscount = hasDiscount ? item._price_after_discount : price;

                    // Hiển thị giá: nếu có giảm thì hiển thị giá giảm + gạch ngang giá gốc, còn không thì chỉ hiển thị giá gốc
                    const priceDisplay = hasDiscount ?
                        `<span class="new-price text-danger">${formatVND(priceAfterDiscount)}</span>
             <del class="text-muted ms-2 small">${formatVND(price)}</del>` :
                        `<span class="new-price">${formatVND(price)}</span>`;

                    container.innerHTML += `
                <div class="col-sm-12 mb-6">
                    <div class="cr-product-inner">
                        <div class="cr-pro-image-outer">
                            <div class="cr-pro-image">
                                <a href="/san-pham/${product.slug}" class="image">
                                    <img height="80px" class="main-image" src="/storage/${image}" alt="${name}">
                                </a>
                            </div>
                        </div>
                        <div class="cr-pro-content cr-product-details">
                            <h5 class="cr-pro-title">
                                <a href="/san-pham/${product.slug}">${name} ${variant.attribute_name ? `(Loại: ${variant.attribute_name})` : ''}</a>
                            </h5>
                            <p class="cr-price">
                        ${priceDisplay}
                        <span class="mx-2">x</span>
                        <span>${quantity}</span>
                        <span>(${formatVND(priceAfterDiscount * quantity)})</span>
                    </p>
                        </p>
                        </div>
                    </div>
                </div><hr>`;
                });
            }

            function calculateOrderTotal(items) {
                return items.reduce((sum, item) => {
                    const price = parseFloat(item.product_variant.price);
                    return sum + price * item.quantity;
                }, 0);
            }

            function renderSummary(total, discount = 0, shipping = 0) {
                const grandTotal = total - discount + shipping;

                document.querySelector('.cr-checkout-summary').innerHTML = `
                <div><span class="text-left">Tổng tiền</span><span class="text-right">${formatVND(total)}</span></div>
                ${discount > 0 ? `<div><span class="text-left">Giảm giá</span><span class="text-right">- ${formatVND(discount)}</span></div>` : ''}
                <div><span class="text-left">Phí vận chuyển</span><span class="text-right">${formatVND(shipping)}</span></div>
                <div class="cr-checkout-summary-total">
                    <span class="text-left">Tổng cộng</span>
                    <span class="text-right">${formatVND(grandTotal)}</span>
                </div>
            `;

                window.finalTotal = grandTotal;
            }

            function renderPaymentMethods(methods) {
                const container = document.getElementById('payment-options');
                container.innerHTML = methods.length === 0 ?
                    '<p>Không có phương thức thanh toán khả dụng.</p>' :
                    methods.map((m, i) => `
                    <span class="cr-pay-option">
                        <span>
                            <input type="radio" id="payment-${m.id}" name="payment_method" value="${m.id}" ${i === 0 ? 'checked' : ''}>
                            <label for="payment-${m.id}">${m.name}</label>
                        </span>
                    </span>`).join('');
            }

            function renderDiscountOptions(discounts, orderTotal) {
                const select = document.getElementById('discount-select');
                select.innerHTML = '<option value="">-- Chọn mã giảm giá --</option>';

                discounts.forEach(discount => {
                    const min = parseFloat(discount.min_order_value || 0);
                    const max = parseFloat(discount.max_order_value || Infinity);

                    if (orderTotal >= min && orderTotal <= max) {
                        const option = document.createElement('option');
                        option.value = discount.id;
                        option.textContent = discount.title;
                        option.dataset.details = JSON.stringify(discount);
                        select.appendChild(option);
                    }
                });
            }

            function handleDiscountChange(select, discounts, items, shippingFee) {
                const selected = select.selectedOptions[0];
                const detailBox = document.getElementById('discount-detail');
                const total = calculateOrderTotal(items);

                if (!selected || !selected.value) {
                    detailBox.style.display = 'none';
                    detailBox.innerHTML = '';
                    // Reset các trường discount trong items để hiển thị giá gốc
                    items.forEach(item => {
                        delete item._discount_amount;
                        delete item._price_after_discount;
                    });
                    renderSummary(total, 0, shippingFee);
                    renderProductList(items);
                    window.selectedDiscountId = null;
                    window.discountAmount = 0;
                    window.updatedItems = items;
                    return;
                }

                const discount = JSON.parse(selected.dataset.details);

                detailBox.style.display = 'block';
                detailBox.innerHTML = `
                <div><strong>Mã:</strong> ${discount.code}</div>
                <div><strong>Giá trị:</strong> ${discount.discount_type === 'percentage' ? discount.discount_value + '%' : formatVND(discount.discount_value)}</div>
                <div><strong>Giảm tối đa:</strong> ${formatVND(discount.max_discount)}</div>
                <div><strong>Áp dụng cho đơn từ:</strong> ${formatVND(discount.min_order_value)} - ${formatVND(discount.max_order_value)}</div>
            `;

                const result = applyDiscount(items, discount, shippingFee);
                renderSummary(total, result.discountAmount, shippingFee);
                renderProductList(result.updatedItems);

                window.selectedDiscountId = discount.id;
                window.discountAmount = result.discountAmount;
                window.selectedDiscountCode = discount.code;
                window.updatedItems = result.updatedItems;
            }

            function applyDiscount(items, discount, shippingFee = 0) {
                let discountAmount = 0;
                let total = 0;
                const isGlobal = discount.applies_to_all_products == 1;
                const type = discount.discount_type;
                const value = parseFloat(discount.discount_value);
                const max = parseFloat(discount.max_discount);

                if (isGlobal) {
                    // Tính tổng trước
                    items.forEach(item => {
                        const variant = item.product_variant;
                        const price = parseFloat(variant.price);
                        const quantity = item.quantity;
                        const subtotal = price * quantity;
                        total += subtotal;
                    });

                    if (type === 'percentage') {
                        discountAmount = total * value / 100;
                    } else {
                        discountAmount = value;
                    }
                    discountAmount = Math.min(discountAmount, max);

                    // Không áp giảm giá vào từng item
                    items.forEach(item => {
                        item._discount_amount = 0;
                        item._price_after_discount = null;
                    });
                } else {
                    // Từng sản phẩm
                    items.forEach(item => {
                        const variant = item.product_variant;
                        const price = parseFloat(variant.price);
                        const quantity = item.quantity;
                        const subtotal = price * quantity;
                        total += subtotal;

                        let itemDiscount = 0;

                        if (discount.products?.some(p => p.id === variant.product_id)) {
                            if (type === 'percentage') {
                                itemDiscount = subtotal * value / 100;
                            } else {
                                itemDiscount = value * quantity;
                            }
                            itemDiscount = Math.min(itemDiscount, max);
                        }

                        item._discount_amount = itemDiscount;
                        item._price_after_discount = (subtotal - itemDiscount) / quantity;
                        discountAmount += itemDiscount;
                    });
                }

                return {
                    discountAmount,
                    grandTotal: total - discountAmount + shippingFee,
                    updatedItems: items
                };
            }


            function initLocationDropdowns() {
                const provinceSel = document.getElementById('province');
                const districtSel = document.getElementById('district');
                const wardSel = document.getElementById('ward');

                fetch('https://provinces.open-api.vn/api/?depth=1')
                    .then(res => res.json())
                    .then(data => {
                        data.forEach(p => provinceSel.add(new Option(p.name, p.code)));
                    });

                provinceSel.addEventListener('change', () => {
                    const code = provinceSel.value;
                    districtSel.innerHTML = '<option selected disabled>Chọn quận / huyện</option>';
                    wardSel.innerHTML = '<option selected disabled>Chọn xã / phường</option>';
                    wardSel.disabled = true;
                    districtSel.disabled = false;

                    fetch(`https://provinces.open-api.vn/api/p/${code}?depth=2`)
                        .then(res => res.json())
                        .then(data => {
                            data.districts.forEach(d => {
                                districtSel.add(new Option(d.name, d.code));
                            });
                        });
                });

                districtSel.addEventListener('change', () => {
                    const code = districtSel.value;
                    wardSel.innerHTML = '<option selected disabled>Chọn xã / phường</option>';
                    wardSel.disabled = false;

                    fetch(`https://provinces.open-api.vn/api/d/${code}?depth=2`)
                        .then(res => res.json())
                        .then(data => {
                            data.wards.forEach(w => {
                                wardSel.add(new Option(w.name, w.code));
                            });
                        });
                });
            }
        });
        document.getElementById('place-order-btn').addEventListener('click', function() {
            const fullname = document.querySelector('[name="fullname"]').value.trim();
            const phone = document.querySelector('[name="phone"]').value.trim();
            const address_detail = document.querySelector('[name="address_detail"]').value.trim();
            const note = document.querySelector('[name="note"]').value.trim();
            const payment_method_id = document.querySelector('input[name="payment_method"]:checked')?.value;

            const provinceSelect = document.getElementById('province');
            const districtSelect = document.getElementById('district');
            const wardSelect = document.getElementById('ward');

            const province = provinceSelect.value;
            const province_name = provinceSelect.options[provinceSelect.selectedIndex].text;

            const district = districtSelect.value;
            const district_name = districtSelect.options[districtSelect.selectedIndex].text;

            const ward = wardSelect.value;
            const ward_name = wardSelect.options[wardSelect.selectedIndex].text;

            if (!fullname || !phone || !province || !district || !ward || !address_detail || !payment_method_id) {
                alert('Vui lòng điền đầy đủ thông tin trước khi đặt hàng.');
                return;
            }

            const payload = {
                fullname,
                phone,
                province_name,
                district_name,
                ward_name,
                address_detail,
                note,
                payment_method_id,
                shipping_fee: shippingFee,
                discount_id: window.selectedDiscountId || null,
                discount_code: window.selectedDiscountCode || null,
                discount_amount: window.discountAmount || 0,
                final_total: window.finalTotal,
                items: window.updatedItems || window.fallbackItems || [],
            };

            console.log('Payload gửi đi:', payload);
            console.log('ship:', shippingFee);

            fetch(`{{ route('checkout.submit') }}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        if (res.redirect_url) {
                            // Nếu backend trả về link thanh toán, redirect sang đó
                            window.location.href = res.redirect_url;
                        } else {
                            alert('Đặt hàng thành công! Cảm ơn bạn đã mua sắm tại GreenHome.');
                            window.location.href = '/'; // hoặc trang cảm ơn
                        }
                    } else {
                        alert(res.message || 'Đặt hàng thất bại. Vui lòng thử lại.');
                    }
                })

                .catch(err => {
                    console.error(err);
                    alert('Có lỗi xảy ra khi đặt hàng.');
                });
        });
    </script>
@endpush
