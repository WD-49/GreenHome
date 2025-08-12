<!-- Modal product -->
<div class="modal fade quickview-modal" id="quickview" aria-hidden="true" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered cr-modal-dialog">
        <div class="modal-content">
            <button type="button" class="cr-close-model btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5 col-sm-12 col-xs-12">
                        <div class="zoom-image-hover modal-border-image">
                            <img id="modal-product-image" alt="product-image" class="product-image">
                        </div>
                    </div>
                    <div class="col-md-7 col-sm-12 col-xs-12">
                        <div class="cr-size-and-weight-contain">
                            <h2 id="modal-product-name" class="heading">Tên sản phẩm</h2>
                            <p id="modal-product-category" class="text-muted"><strong>Danh mục:</strong> Chưa có</p>
                            <p id="modal-product-brand" class="text-muted"><strong>Thương hiệu:</strong> Chưa có</p>
                            <p id="modal-product-views" class="text-muted"><strong>Lượt xem:</strong> 0</p>
                            <p id="modal-product-description">Mô tả sản phẩm...</p>
                        </div>
                        <div class="cr-size-and-weight">
                            <div class="cr-review-star">
                                <div id="modal-product-rating" class="cr-star">
                                    <!-- Đánh giá sẽ được cập nhật động -->
                                </div>
                                <p id="modal-product-review-count">(0 Review)</p>
                            </div>
                            <div class="cr-product-price">
                                <span id="modal-product-price" class="new-price">0 ₫</span>
                                <span id="modal-product-old-price" class="old-price"></span>
                            </div>
                            <div class="cr-product-quantity">
                                <p id="modal-product-quantity" class="text-muted">Số lượng: 0</p>
                            </div>
                            <div class="cr-size-weight" id="modal-product-variants-container" style="display: none;">
                                <h5>Loại:</h5>
                                <div class="cr-kg">
                                    <ul id="modal-product-variants">
                                        <!-- Biến thể sẽ được cập nhật động -->
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .cr-size-and-weight-contain p strong {
        font-weight: bold;
    }

    .cr-star {
        display: inline-flex;
        align-items: center;
    }

    .cr-star i {
        font-size: 1.2rem;
        margin-right: 2px;
    }

    .cr-star .half-star {
        position: relative;
        display: inline-block;
        overflow: hidden;
        width: 0.6rem;
        /* Nửa chiều rộng của sao */
    }

    .cr-star .half-star::before {
        content: '\e900';
        /* Mã icon của ri-star-fill */
        position: absolute;
        left: 0;
        color: #ffc107;
        /* Màu sao đầy */
    }
</style>

<script>
    // Hàm định dạng tiền tệ
    if (typeof formatVND !== 'function') {
        function formatVND(number) {
            return number.toLocaleString('vi-VN', {
                minimumFractionDigits: 0,
                maximumFractionDigits: 0
            }) + ' ₫';
        }
    }

    // Xử lý khi modal được mở
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('quickview');
        modal.addEventListener('show.bs.modal', async function(event) {
            const button = event.relatedTarget; // Nút kích hoạt modal
            const productId = button.dataset.productId; // Lấy product_id từ nút

            try {
                // Gọi API để lấy dữ liệu sản phẩm
                const response = await fetch(`/products/${productId}`);
                const data = await response.json();

                if (!data.success || !data.product) {
                    alert('Không thể tải thông tin sản phẩm.');
                    return;
                }

                const product = data.product;
                const variant = product.product_variants?.[0]; // Lấy biến thể đầu tiên

                // Cập nhật hình ảnh
                document.getElementById('modal-product-image').src =
                    `/storage/${product.image || 'default.jpg'}`;
                document.getElementById('modal-product-image').alt = product.name;

                // Cập nhật tên, danh mục, thương hiệu, lượt xem và mô tả
                document.getElementById('modal-product-name').textContent = product.name;
                document.getElementById('modal-product-category').innerHTML =
                    `<strong>Danh mục:</strong> ${product.category?.name || 'Chưa có'}`;
                document.getElementById('modal-product-brand').innerHTML =
                    `<strong>Thương hiệu:</strong> ${product.brand?.name || 'Chưa có'}`;
                document.getElementById('modal-product-views').innerHTML =
                    `<strong>Lượt xem:</strong> ${product.view || 0}`;
                document.getElementById('modal-product-description').textContent =
                    product.sortDes || '';

                // Cập nhật đánh giá
                const averageRating = product.average_rating || 0; // Lấy số sao trung bình từ API
                const reviewCount = product.review_count || 0;
                let ratingHtml = '';
                const fullStars = Math.floor(averageRating); // Số sao đầy
                const hasHalfStar = averageRating % 1 >= 0.5; // Kiểm tra có nửa sao
                for (let i = 0; i < 5; i++) {
                    if (i < fullStars) {
                        ratingHtml += `<i class="ri-star-fill text-warning"></i>`;
                    } else if (i === fullStars && hasHalfStar) {
                        ratingHtml += `<i class="ri-star-fill text-muted half-star"></i>`;
                    } else {
                        ratingHtml += `<i class="ri-star-fill text-muted"></i>`;
                    }
                }
                document.getElementById('modal-product-rating').innerHTML = ratingHtml;
                document.getElementById('modal-product-review-count').textContent =
                    `(${reviewCount} đánh giá)`;

                // Cập nhật giá và số lượng
                if (variant) {
                    document.getElementById('modal-product-price').textContent = formatVND(Number(
                        variant.price));
                    document.getElementById('modal-product-old-price').textContent =
                        variant.old_price ? formatVND(Number(variant.old_price)) : '';
                    document.getElementById('modal-product-quantity').textContent =
                        `Số lượng: ${variant.quantity || 0}`;
                } else {
                    document.getElementById('modal-product-price').textContent = 'Chưa có giá';
                    document.getElementById('modal-product-old-price').textContent = '';
                    document.getElementById('modal-product-quantity').textContent = 'Số lượng: 0';
                }

                // Cập nhật biến thể (Loại)
                const variantsContainer = document.getElementById(
                    'modal-product-variants-container');
                const variantsHtml = product.product_variants
                    .filter(v => v.attribute_name) // Chỉ lấy biến thể có attribute_name
                    .map((v, index) =>
                        `<li class="${index === 0 ? 'active-color' : ''}" data-variant-id="${v.id}">
                            ${v.attribute_name}
                        </li>`
                    ).join('');

                if (variantsHtml) {
                    document.getElementById('modal-product-variants').innerHTML = variantsHtml;
                    variantsContainer.style.display = 'block'; // Hiển thị nếu có biến thể
                } else {
                    variantsContainer.style.display = 'none'; // Ẩn nếu không có biến thể
                }

                // Xử lý chọn biến thể
                const variantList = document.querySelectorAll('#modal-product-variants li');
                variantList.forEach(li => {
                    li.addEventListener('click', function() {
                        variantList.forEach(item => item.classList.remove(
                            'active-color'));
                        this.classList.add('active-color');
                        const selectedVariant = product.product_variants.find(v => v
                            .id == this.dataset.variantId);
                        document.getElementById('modal-product-price').textContent =
                            formatVND(Number(selectedVariant.price));

                        document.getElementById('modal-product-old-price')
                            .textContent =
                            selectedVariant.old_price ? formatVND(Number(
                                selectedVariant.old_price)) : '';
                        document.getElementById('modal-product-quantity')
                            .textContent =
                            `Số lượng: ${selectedVariant.quantity || 0}`;
                    });
                });

            } catch (error) {
                console.error('Lỗi khi tải sản phẩm:', error);
                alert('Đã có lỗi xảy ra khi tải thông tin sản phẩm.');
            }
        });
    });
</script>
