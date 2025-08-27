<!-- Cart -->
<div class="cr-cart-overlay"></div>
<div class="cr-cart-view">
    <div class="cr-cart-inner">
        <div class="cr-cart-top">
            <div class="cr-cart-title">
                <h6>Giỏ hàng của tôi</h6>
                <button type="button" class="close-cart">×</button>
            </div>
            <ul class="crcart-pro-items" id="mini-cart-items"></ul>
        </div>
        <div class="cr-cart-bottom">
            <div class="cart-sub-total">
                <table class="table cart-table">
                    <tbody></tbody>
                </table>
            </div>
            <div class="cart_btn">
                <a href="{{ route('cart.view') }}" class="cr-button">Xem chi tiết</a>
            </div>
        </div>
    </div>
</div>

<style>
    .cr-cart-view {
        position: fixed;
        top: 0;
        right: -340px;
        width: 340px;
        height: 100%;
        background: #fff;
        z-index: 1000;
        transition: right 0.3s ease-in-out;
    }

    .cr-cart-inner {
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .cr-cart-top {
        flex: 1;
        overflow: hidden;
    }

    .crcart-pro-items {
        max-height: 500px;
        /* Giới hạn chiều cao danh sách sản phẩm */
        overflow-y: auto;
        /* Bật thanh cuộn dọc */
        padding: 0;
        margin: 0;
        list-style: none;
    }

    .crcart-pro-items li {
        display: flex;
        padding: 10px;
        border-bottom: 1px solid #eee;
    }

    .cr-cart-bottom {
        padding: 15px;
        border-top: 1px solid #eee;
        background: #fff;
    }

    .cart-table {
        width: 100%;
        margin-bottom: 15px;
    }

    .cart_btn {
        text-align: center;
    }


    /* Đảm bảo thanh cuộn đẹp trên các trình duyệt */
    .crcart-pro-items::-webkit-scrollbar {
        width: 8px;
    }

    .crcart-pro-items::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .crcart-pro-items::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    .crcart-pro-items::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>

<script>
    if (typeof formatVND !== 'function') {
        function formatVND(number) {
            return number.toLocaleString('vi-VN') + ' ₫';
        }
    }

    async function loadMiniCart() {
        try {
            // Kiểm tra trạng thái đăng nhập
            const isAuthenticated = {{ auth()->check() ? 'true' : 'false' }};

            const miniCart = document.getElementById('mini-cart-items');
            const cartTable = document.querySelector('.cart-table');

            if (!isAuthenticated) {
                miniCart.innerHTML =
                    `<li>Hãy đăng nhập để xem giỏ hàng của bạn</li>`;
                cartTable.innerHTML = `
                    <tr><td class="text-left">Tạm tính:</td><td class="text-right">0 ₫</td></tr>
                    <tr><td class="text-left">Tổng cộng:</td><td class="text-right primary-color">0 ₫</td></tr>
                `;
                return;
            }

            const res = await fetch("{{ route('cart.data') }}");
            const data = await res.json();

            if (!data.success || !data.cart?.items?.length) {
                miniCart.innerHTML = `<li>Bạn chưa thêm sản phẩm nào vào giỏ hàng</li>`;
                cartTable.innerHTML = `
                    <tr><td class="text-left">Tạm tính:</td><td class="text-right">0 ₫</td></tr>
                    <tr><td class="text-left">Tổng cộng:</td><td class="text-right primary-color">0 ₫</td></tr>
                `;
                return;
            }

            const items = data.cart.items;
            let html = '';
            let subtotal = 0;

            items.forEach(item => {
                const variant = item.product_variant;
                const product = variant?.product;
                if (!product) return;

                const image = product.image || 'default.jpg';
                const price = parseFloat(variant.price) || 0;
                const quantity = parseInt(item.quantity) || 1;
                const total = price * quantity;
                subtotal += total;

                const disablePlus = quantity >= variant.quantity ? 'disabled' : '';

                html += `
                    <li data-id="${item.id}" data-max="${variant.quantity}">
                        <a href="/san-pham/${product.slug}" class="crside_pro_img">
                            <img src="/storage/${image}" alt="${product.name}">
                        </a>
                        <div class="cr-pro-content">
                            <a href="/san-pham/${product.slug}" class="cart_pro_title">${product.name}</a>
                            <span class="cart-price"><span>${formatVND(price)}</span> x ${quantity}</span>
                            <div class="cr-cart-qty">
                                <div class="cart-qty-plus-minus">
                                    <button type="button" class="minus">-</button>
                                    <input type="text" value="${quantity}" class="quantity" readonly>
                                    <button type="button" class="plus" ${disablePlus}>+</button>
                                </div>
                            </div>
                            <a href="javascript:void(0)" class="remove">×</a>
                        </div>
                    </li>
                `;
            });

            miniCart.innerHTML = html;

            cartTable.innerHTML = `
                <tr><td class="text-left">Tạm tính:</td><td class="text-right">${formatVND(subtotal)}</td></tr>
                <tr><td class="text-left">Tổng cộng:</td><td class="text-right primary-color">${formatVND(subtotal)}</td></tr>
            `;

            bindMiniCartEvents();
        } catch (error) {
            console.error('Lỗi khi tải giỏ hàng:', error);
            miniCart.innerHTML = `<li>Đã có lỗi xảy ra khi tải giỏ hàng</li>`;
            cartTable.innerHTML = `
                <tr><td class="text-left">Tạm tính:</td><td class="text-right">0 ₫</td></tr>
                <tr><td class="text-left">Tổng cộng:</td><td class="text-right primary-color">0 ₫</td></tr>
            `;
        }
    }

    function bindMiniCartEvents() {
        const miniCart = document.getElementById('mini-cart-items');

        miniCart.querySelectorAll('.plus, .minus').forEach(btn => {
            btn.addEventListener('click', async function() {
                const li = this.closest('li');
                const input = li.querySelector('.quantity');
                const id = li.dataset.id;
                const max = parseInt(li.dataset.max);
                const oldQuantity = parseInt(input.value);
                let quantity = oldQuantity;

                if (this.classList.contains('plus')) {
                    if (oldQuantity >= max) {
                        alert('Bạn đã đạt tới số lượng tối đa của sản phẩm.');
                        return;
                    }
                    quantity++;
                } else {
                    quantity = Math.max(1, quantity - 1);
                }

                input.value = quantity;
                await updateMiniCartQuantity(id, quantity);
            });
        });

        miniCart.querySelectorAll('.remove').forEach(btn => {
            btn.addEventListener('click', async function() {
                const li = this.closest('li');
                const id = li.dataset.id;
                if (confirm('Bạn có chắc muốn xoá sản phẩm này?')) {
                    await deleteMiniCartItem(id);
                }
            });
        });
    }

    async function updateMiniCartQuantity(id, quantity) {
        try {
            const res = await fetch(`{{ route('cart.updateQuantity', ':id') }}`.replace(':id', id), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    quantity
                })
            });

            const data = await res.json();
            if (data.success) {
                await loadMiniCart();
            } else {
                alert(data.message || 'Không thể cập nhật số lượng.');
            }
        } catch (error) {
            console.error('Lỗi cập nhật số lượng:', error);
        }
    }

    async function deleteMiniCartItem(id) {
        try {
            const res = await fetch(`{{ route('cart.deleteMultiple') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    ids: [id]
                })
            });

            const data = await res.json();
            if (data.success) {
                await loadMiniCart();
            } else {
                alert(data.message || 'Xoá không thành công.');
            }
        } catch (error) {
            console.error('Lỗi xoá sản phẩm:', error);
        }
    }

    document.addEventListener('DOMContentLoaded', loadMiniCart);
</script>
