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
    function formatVND(number) {
        return number.toLocaleString('vi-VN') + ' ₫';
    }

    async function loadMiniCart() {
        try {
            const response = await fetch("{{ route('cart.data') }}");
            const data = await response.json();

            console.log('CART DATA:', data);

            if (!data.success || !data.cart || !data.cart.items.length) {
                document.getElementById('mini-cart-items').innerHTML =
                    `<li>Bạn chưa thêm sản phẩm nào vào giỏ hàng</li>`;
                document.querySelector('.cart-table').innerHTML = `
                    <tr><td class="text-left">Tạm tính :</td><td class="text-right">0 ₫</td></tr>
                    <tr><td class="text-left">Tổng cộng :</td><td class="text-right primary-color">0 ₫</td></tr>
                `;
                return;
            }

            const items = data.cart.items || [];
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

                    <li>
                        <a href="/product/${product.slug}" class="crside_pro_img">
                            <img src="/storage/${image}" alt="${product.name}">
                        </a>
                        <div class="cr-pro-content">
                            <a href="/product/${product.slug}" class="cart_pro_title">${product.name}</a>
                            <span class="cart-price"><span>${formatVND(price)}</span> x ${quantity}</span>
                            <div class="cr-cart-qty">
                                <div class="cart-qty-plus-minus">
                                    <button type="button" class="plus">+</button>
                                    <input type="text" value="${quantity}" class="quantity">
                                    <button type="button" class="minus">-</button>
                                </div>
                            </div>
                            <a href="javascript:void(0)" class="remove">×</a>
                        </div>
                    </li>
                `;
            });

            document.getElementById('mini-cart-items').innerHTML = html;

            const total = subtotal;

            document.querySelector('.cart-table').innerHTML = `
                <tr>
                    <td class="text-left">Tạm tính :</td>
                    <td class="text-right">${formatVND(subtotal)}</td>
                </tr>
                <tr>
                    <td class="text-left">Tổng cộng :</td>
                    <td class="text-right primary-color">${formatVND(total)}</td>
                </tr>
            `;
        } catch (error) {
            console.error('Error loading cart:', error);
        }
    }

    // function updateMiniCart(cart) {
    //     if (!cart || !cart.items || cart.items.length === 0) {
    //         document.getElementById('mini-cart-items').innerHTML = `<li>Bạn chưa thêm sản phẩm nào vào giỏ hàng</li>`;
    //         document.querySelector('.cart-table').innerHTML = `
    //             <tr><td class="text-left">Tạm tính :</td><td class="text-right">0 ₫</td></tr>
    //             <tr><td class="text-left">Tổng cộng :</td><td class="text-right primary-color">0 ₫</td></tr>
    //         `;
    //         return;
    //     }

    //     let html = '';
    //     let subtotal = 0;

    //     cart.items.forEach(item => {
    //         const variant = item.product_variant;
    //         const product = variant?.product;
    //         if (!product) return;

    //         const image = product.image || 'default.jpg';
    //         const price = parseFloat(variant.price) || 0;
    //         const quantity = parseInt(item.quantity) || 1;
    //         const total = price * quantity;
    //         subtotal += total;

    //         html += `
    //             <li>
    //                 <a href="/product/${product.slug}" class="crside_pro_img">
    //                     <img src="/storage/${image}" alt="${product.name}">
    //                 </a>
    //                 <div class="cr-pro-content">
    //                     <a href="/product/${product.slug}" class="cart_pro_title">${product.name}</a>
    //                     <span class="cart-price"><span>${formatVND(price)}</span> x ${quantity}</span>
    //                     <div class="cr-cart-qty">
    //                         <div class="cart-qty-plus-minus">
    //                             <button type="button" class="plus">+</button>
    //                             <input type="text" value="${quantity}" class="quantity">
    //                             <button type="button" class="minus">-</button>
    //                         </div>
    //                     </div>
    //                     <a href="javascript:void(0)" class="remove">×</a>
    //                 </div>
    //             </li>
    //         `;
    //     });

    //     document.getElementById('mini-cart-items').innerHTML = html;

    //     const total = subtotal;

    //     document.querySelector('.cart-table').innerHTML = `
    //         <tr>
    //             <td class="text-left">Tạm tính :</td>
    //             <td class="text-right">${formatVND(subtotal)}</td>
    //         </tr>
    //         <tr>
    //             <td class="text-left">Tổng cộng :</td>
    //             <td class="text-right primary-color">${formatVND(total)}</td>
    //         </tr>
    //     `;
    // }
</script>

@auth
    <script>
        document.addEventListener('DOMContentLoaded', loadMiniCart);
    </script>
@endauth
