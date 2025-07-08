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
                                            <a href="javascript:void(0)" id="checkout-selected" class="cr-button">Thanh
                                                toán</a>
                                            <div class="cr-btn-ds" data-aos="fade-up" data-aos-duration="2000"
                                                data-aos-delay="400">
                                                <button type="button" id="delete-selected" class="btn btn-danger">Xoá mục
                                                    đã
                                                    chọn</button>
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
            fetch("{{ route('cart.data') }}")
                .then(res => res.json())
                .then(data => {
                    document.getElementById('cart-loader').style.display = 'none';

                    if (!data.success || !data.cart || !data.cart.items.length) {
                        document.getElementById('cart-empty').style.display = 'block';
                        return;
                    }

                    const tbody = document.getElementById('cart-body');
                    const items = data.cart.items;
                    let html = '';

                    items.forEach(item => {
                        const variant = item.product_variant;
                        const product = variant?.product;
                        if (!product) return;

                        const image = product.image || 'default.jpg';
                        const price = parseFloat(variant.price) || 0;
                        const quantity = parseInt(item.quantity) || 1;
                        const total = price * quantity;

                        html += `
                        <tr data-id="${item.id}" data-price="${price}">
                        <td><input type="checkbox" class="cart-checkbox" value="${item.id}"></td>
                            <td class="cr-cart-name">
                                <a href="/san-pham/${product.slug}">
                                    <img src="/storage/${image}" alt="${product.name}" class="cr-cart-img">
                                    ${product.name}
                                </a>
                            </td>
                           <td >
                                    ${variant.attribute_name || ''}
                            </td>
                            <td class="cr-cart-price"><span class="amount">${formatVND(price)}</span></td>
                            <td class="cr-cart-qty">
                                <div class="cart-qty-plus-minus">
                                    <button type="button" class="plus">+</button>
                                    <input type="text" value="${quantity}" class="quantity" readonly>
                                    <button type="button" class="minus">-</button>
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
                    });

                    tbody.innerHTML = html;
                    document.getElementById('cart-content').style.display = 'block';

                    // Chọn tất cả
                    document.getElementById('select-all').addEventListener('change', function() {
                        document.querySelectorAll('.cart-checkbox').forEach(cb => {
                            cb.checked = this.checked;
                        });
                    });

                    document.getElementById('delete-selected').addEventListener('click', function() {
                        const selectedIds = Array.from(document.querySelectorAll(
                                '.cart-checkbox:checked'))
                            .map(cb => cb.value);

                        console.log('Selected IDs to delete:', selectedIds);

                        if (!selectedIds.length) {
                            alert('Vui lòng chọn ít nhất một sản phẩm để xoá.');
                            return;
                        }

                        if (!confirm('Bạn có chắc muốn xoá các sản phẩm đã chọn không?')) {
                            return;
                        }

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
                            .then(async res => {
                                console.log('Fetch response status:', res.status);
                                const data = await res.json().catch(() => {
                                    throw new Error(
                                        'Server trả về không phải JSON hợp lệ');
                                });

                                if (res.ok && data.success) {
                                    // console.log('Response data:', data);
                                    alert('Đã xoá sản phẩm thành công!');
                                    location.reload();
                                } else {
                                    throw new Error(data.message || 'Lỗi xoá sản phẩm');
                                }
                            })
                            .catch(error => {
                                console.error('Delete selected error:', error);
                                alert(error.message || 'Không thể xoá sản phẩm!');
                            });
                    });

                    document.getElementById('checkout-selected').addEventListener('click', function() {
                        const selectedIds = Array.from(document.querySelectorAll(
                                '.cart-checkbox:checked'))
                            .map(cb => cb.value);

                        let url = '';

                        if (selectedIds.length > 0) {
                            // Chuyển sang chế độ selected
                            const query = selectedIds.map(id => `ids[]=${id}`).join('&');
                            url = `/checkout?type=selected&${query}`;
                        } else {
                            // Chuyển sang chế độ full cart
                            url = `/checkout?type=full`;
                        }

                        window.location.href = url;
                    });

                    // Bind + and - buttons
                    tbody.querySelectorAll('.plus').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const row = this.closest('tr');
                            const input = row.querySelector('.quantity');
                            let quantity = parseInt(input.value) + 1;
                            input.value = quantity;
                            updateQuantity(row, quantity);
                        });
                    });

                    tbody.querySelectorAll('.minus').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const row = this.closest('tr');
                            const input = row.querySelector('.quantity');
                            let quantity = Math.max(1, parseInt(input.value) - 1);
                            input.value = quantity;
                            updateQuantity(row, quantity);
                        });
                    });

                    function updateQuantity(row, quantity) {
                        const cartId = row.dataset.id;
                        const price = parseFloat(row.dataset.price);
                        const subtotalCell = row.querySelector('.cr-cart-subtotal');
                        subtotalCell.innerHTML = formatVND(price * quantity);

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

                            .then(res => {
                                if (!res.ok) {
                                    throw new Error(`Lỗi máy chủ: ${res.status}`);
                                }
                                return res.json();
                            })
                            .then(data => {
                                if (!data.success) {
                                    alert(data.message || 'Lỗi cập nhật số lượng!');
                                }
                            })
                            .catch(error => {
                                console.error('Update error:', error);
                                alert('Lỗi kết nối máy chủ!');
                            });
                    }
                })
                .catch(error => {
                    console.error('Lỗi khi load giỏ hàng:', error);
                    document.getElementById('cart-loader').innerHTML = 'Không thể tải giỏ hàng.';
                });
        });
    </script>
@endpush
