@extends('layouts.app')

@section('content')
    <!-- Breadcrumb -->

    <section class="section-breadcrumb">
        <div class="cr-breadcrumb-image">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="cr-breadcrumb-title">
                            <h2>Giỏ hàng</h2>
                            <span> <a href="{{ route('home') }}">Trang trủ</a> / giỏ hàng</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Cart -->
    <section class="section-cart padding-t-100">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="cr-cart-content" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                        <div id="cart-loader">Đang tải giỏ hàng...</div>
                        <div class="row" id="cart-content" style="display: none;">
                            <form action="#">
                                <div class="cr-table-content">
                                    <table>
                                        <thead>
                                            <tr>
                                                <th><input type="checkbox" id="select-all"></th>
                                                <th>Sản phẩm</th>
                                                <th>Loại</th>
                                                <th>Giá</th>
                                                <th class="text-center">Số lượng</th>
                                                <th>Tổng giá</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="cart-body">
                                            <!-- Dữ liệu sẽ được render bằng JS -->
                                        </tbody>
                                    </table>
                                </div>
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="cr-cart-update-bottom">
                                            <a href="{{ route('shop.index') }}" class="cr-links">Tiếp tục mua sắm</a>

                                            <div class="cr-btn-ds" data-aos="fade-up" data-aos-duration="2000"
                                                data-aos-delay="400">
                                                {{-- <a href="javascript:void(0)" id="checkout-selected" class="cr-button">Thanh
                                                    toán</a> --}}
                                                <button type="button" id="checkout-selected" class="btn btn-success">Thanh
                                                    toán
                                                </button>
                                                <button type="button" id="delete-selected" class="btn btn-danger">Xoá
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div id="cart-empty" style="display: none;" class="text-center">
                            <h2>Ban chưa thêm sản phẩm nào vào giỏ h</h2>
                            <a href="{{ route('home') }}" class="btn btn-success">Quay lại mua sắm</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@push('scripts')
    <script>
        if (typeof formatVND !== 'function') {
            function formatVND(number) {
                return number.toLocaleString('vi-VN') + ' ₫';
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const cartLoader = document.getElementById('cart-loader');
            const cartContent = document.getElementById('cart-content');
            const cartEmpty = document.getElementById('cart-empty');
            const tbody = document.getElementById('cart-body');
            const deleteBtn = document.getElementById('delete-selected');
            const checkoutBtn = document.getElementById('checkout-selected');
            const selectAll = document.getElementById('select-all');

            fetch("{{ route('cart.data') }}")
                .then(res => res.json())
                .then(data => {
                    cartLoader.style.display = 'none';

                    if (!data.success || !data.cart?.items?.length) {
                        cartEmpty.style.display = 'block';
                        return;
                    }

                    renderCart(data.cart.items);
                    bindEvents();
                })
                .catch(error => {
                    console.error('Lỗi khi load giỏ hàng:', error);
                    cartLoader.innerHTML = 'Không thể tải giỏ hàng.';
                });

            function renderCart(items) {
                tbody.innerHTML = items.map(item => {
                    const variant = item.product_variant;
                    const product = variant?.product;
                    if (!product) return '';

                    const image = product.image || 'default.jpg';
                    const price = parseFloat(variant.price) || 0;
                    const quantity = parseInt(item.quantity) || 1;
                    const total = price * quantity;

                    return `
                    <tr data-id="${item.id}" data-price="${price}" data-max="${variant.quantity}">
                        <td><input type="checkbox" class="cart-checkbox" value="${item.id}"></td>
                        <td class="cr-cart-name">
                            <a href="/san-pham/${product.slug}">
                                <img src="/storage/${image}" alt="${product.name}" class="cr-cart-img">
                                ${product.name}
                            </a>
                        </td>
                        <td>${variant.attribute_name || ''}</td>
                        <td class="cr-cart-price"><span class="amount">${formatVND(price)}</span></td>
                        <td class="cr-cart-qty">
                            <div class="cart-qty-plus-minus">
                                <button type="button" class="minus">-</button>
                                <input type="text" value="${quantity}" class="quantity" readonly>
                                <button type="button" class="plus">+</button>

                            </div>
                        </td>
                        <td class="cr-cart-subtotal">${formatVND(total)}</td>
                        <td class="cr-cart-remove">
                            <a href="javascript:void(0)">
                                <i class="ri-delete-bin-line"></i>
                            </a>
                        </td>
                    </tr>
                `;
                }).join('');

                cartContent.style.display = 'block';
            }

            function bindEvents() {
                // Check all
                selectAll.addEventListener('change', function() {
                    document.querySelectorAll('.cart-checkbox').forEach(cb => cb.checked = this.checked);
                });

                // Delete selected
                deleteBtn.addEventListener('click', function() {
                    const selectedIds = getSelectedIds();
                    if (!selectedIds.length) return alert('Vui lòng chọn ít nhất một sản phẩm để xoá.');
                    if (!confirm('Bạn có chắc muốn xoá các sản phẩm đã chọn không?')) return;

                    fetch(`{{ route('cart.deleteMultiple') }}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': @json(csrf_token())
                            },
                            body: JSON.stringify({
                                ids: selectedIds
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                alert('Đã xoá sản phẩm thành công!');
                                location.reload();
                            } else {
                                throw new Error(data.message || 'Lỗi xoá sản phẩm');
                            }
                        })
                        .catch(error => {
                            console.error('Delete error:', error);
                            alert(error.message || 'Không thể xoá sản phẩm!');
                        });
                });

                // Checkout selected
                checkoutBtn.addEventListener('click', function() {
                    const selectedIds = getSelectedIds();
                    const url = selectedIds.length ?
                        `/checkout?type=selected&${selectedIds.map(id => `ids[]=${id}`).join('&')}` :
                        `/checkout?type=full`;

                    window.location.href = url;
                });

                // Plus / Minus
                tbody.querySelectorAll('.plus, .minus').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const row = this.closest('tr');
                        const input = row.querySelector('.quantity');
                        const oldQuantity = parseInt(input.value);
                        let quantity = oldQuantity;
                        const max = parseInt(row.dataset.max); // 🟢 kiểm tra tồn kho

                        if (this.classList.contains('plus')) {
                            if (quantity >= max) {
                                alert('Bạn đã đạt tới số lượng tối đa của sản phẩm.');
                                return;
                            }
                            quantity += 1;
                        } else {
                            quantity = Math.max(1, quantity - 1);
                        }

                        input.value = quantity;
                        updateQuantity(row, quantity, oldQuantity);
                    });
                });

            }

            function getSelectedIds() {
                return Array.from(document.querySelectorAll('.cart-checkbox:checked'))
                    .map(cb => cb.value);
            }

            function updateQuantity(row, quantity, oldQuantity) {
                const cartId = row.dataset.id;
                const price = parseFloat(row.dataset.price);
                const subtotalCell = row.querySelector('.cr-cart-subtotal');
                const input = row.querySelector('.quantity');

                fetch(`{{ route('cart.updateQuantity', ':id') }}`.replace(':id', cartId), {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': @json(csrf_token())
                        },
                        body: JSON.stringify({
                            quantity
                        })
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok || !data.success) {
                            alert(data.message || 'Lỗi cập nhật số lượng!');
                            input.value = oldQuantity;
                            subtotalCell.innerHTML = formatVND(price * oldQuantity);
                            return;
                        }

                        // Thành công
                        input.value = quantity;
                        subtotalCell.innerHTML = formatVND(price * quantity);
                    })
                    .catch(error => {
                        console.error('Update quantity error:', error);
                        alert('Lỗi kết nối máy chủ!');
                        input.value = oldQuantity;
                        subtotalCell.innerHTML = formatVND(price * oldQuantity);
                    });
            }


        });
    </script>
@endpush
