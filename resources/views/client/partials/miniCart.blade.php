<!-- Cart -->
<div class="cr-cart-overlay"></div>
<div class="cr-cart-view">
    <div class="cr-cart-inner">
        <div class="cr-cart-top">
            <div class="cr-cart-title">
                <h6>My Cart</h6>
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
                <a href="{{ route('cart.view') }}" class="cr-button">View Cart</a>
                <a href="checkout.html" class="cr-btn-secondary">Checkout</a>
            </div>
        </div>
    </div>
</div>

<script>
    if (typeof formatVND !== 'function') {
        function formatVND(number) {
            return number.toLocaleString('vi-VN') + ' ₫';
        }
    }

    async function loadMiniCart() {
        try {
            const response = await fetch("{{ route('cart.data') }}");
            const data = await response.json();

            const miniCart = document.getElementById('mini-cart-items');
            const cartTable = document.querySelector('.cart-table');

            if (!data.success || !data.cart || !data.cart.items.length) {
                miniCart.innerHTML = `<li>Bạn chưa thêm sản phẩm nào vào giỏ hàng</li>`;
                cartTable.innerHTML = `
                    <tr><td class="text-left">Tạm tính :</td><td class="text-right">0 ₫</td></tr>
                    <tr><td class="text-left">Tổng cộng :</td><td class="text-right primary-color">0 ₫</td></tr>
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

                html += `
                    <li data-id="${item.id}">
                        <a href="/product/${product.slug}" class="crside_pro_img">
                            <img src="/storage/${image}" alt="${product.name}">
                        </a>
                        <div class="cr-pro-content">
                            <a href="/product/${product.slug}" class="cart_pro_title">${product.name}</a>
                            <span class="cart-price"><span>${formatVND(price)}</span> x ${quantity}</span>
                            <div class="cr-cart-qty">
                                <div class="cart-qty-plus-minus">
                                    <button type="button" class="plus">+</button>
                                    <input type="text" value="${quantity}" class="quantity" readonly>
                                    <button type="button" class="minus">-</button>
                                </div>
                            </div>
                            <a href="javascript:void(0)" class="remove">×</a>
                        </div>
                    </li>
                `;
            });

            miniCart.innerHTML = html;

            cartTable.innerHTML = `
                <tr><td class="text-left">Tạm tính :</td><td class="text-right">${formatVND(subtotal)}</td></tr>
                <tr><td class="text-left">Tổng cộng :</td><td class="text-right primary-color">${formatVND(subtotal)}</td></tr>
            `;

            // Gắn sự kiện sau khi render HTML
            document.querySelectorAll('#mini-cart-items .plus').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const li = this.closest('li');
                    const input = li.querySelector('.quantity');
                    const id = li.dataset.id;
                    let quantity = parseInt(input.value) + 1;
                    input.value = quantity;

                    await updateMiniCartQuantity(id, quantity);
                });
            });

            document.querySelectorAll('#mini-cart-items .minus').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const li = this.closest('li');
                    const input = li.querySelector('.quantity');
                    const id = li.dataset.id;
                    let quantity = Math.max(1, parseInt(input.value) - 1);
                    input.value = quantity;

                    await updateMiniCartQuantity(id, quantity);
                });
            });

            document.querySelectorAll('#mini-cart-items .remove').forEach(btn => {
                btn.addEventListener('click', async function() {
                    const li = this.closest('li');
                    const id = li.dataset.id;

                    if (!confirm('Bạn có chắc muốn xoá sản phẩm này?')) return;

                    await deleteMiniCartItem(id);
                });
            });

        } catch (error) {
            console.error('Error loading cart:', error);
        }
    }

    async function updateMiniCartQuantity(id, quantity) {
        try {
            const response = await fetch(`{{ route('cart.updateQuantity', ':id') }}`.replace(':id', id), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': @json(csrf_token())
                },
                body: JSON.stringify({
                    quantity
                })
            });

            const data = await response.json();
            if (data.success) {
                loadMiniCart(); // cập nhật lại
            } else {
                alert(data.message || 'Không thể cập nhật số lượng.');
            }
        } catch (error) {
            console.error('Update quantity error:', error);
        }
    }

    async function deleteMiniCartItem(id) {
        try {
            const response = await fetch(`{{ route('cart.deleteMultiple') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': @json(csrf_token())
                },
                body: JSON.stringify({
                    ids: [id]
                })
            });

            const data = await response.json();
            if (data.success) {
                loadMiniCart();
            } else {
                alert(data.message || 'Xoá không thành công.');
            }
        } catch (error) {
            console.error('Delete mini cart error:', error);
        }
    }
</script>


@auth
    <script>
        document.addEventListener('DOMContentLoaded', loadMiniCart);
    </script>
@endauth
