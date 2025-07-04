<div id="voucher-popup" class="voucher-modal" style="display: none;">
    <div class="voucher-overlay"></div>
    <div class="voucher-popup-box">
        <button id="close-voucher" class="voucher-close">&times;</button>
        <h3 class="voucher-title">🎟 Chọn mã giảm giá</h3>
        <ul class="voucher-list">
            @forelse ($vouchers as $voucher)
                <li class="voucher-item">
                    <div style="flex: 1;">
                        <div class="voucher-code">{{ $voucher->code }}</div>
                        <div class="voucher-desc">{{ $voucher->title ?? 'Giảm giá đặc biệt' }}</div>
                        <div class="voucher-desc">🏷 Giảm tối đa: {{ number_format($voucher->max_discount, 0) }}đ</div>
                        <div class="voucher-desc">📟 Đơn tối thiểu: {{ number_format($voucher->min_order_value, 0) }}đ
                        </div>
                        <div class="voucher-desc">Giá trị giảm: {{ number_format($voucher->discount_value, 0) }}đ</div>

                        <div class="voucher-desc">⏰ Hết hạn:
                            {{ \Carbon\Carbon::parse($voucher->end_date)->format('d/m/Y') }}</div>
                    </div>
                    <div style="display: flex; flex-direction: column; gap: 6px;">
                        <button class="apply-voucher" data-code="{{ $voucher->code }}">Áp dụng</button>
                    <a href="{{ route('voucherDetail', ['code' => $voucher->code]) }}" class="view-detail">Chi tiết</a>

                    </div>
                </li>
            @empty
                <li class="voucher-item">Hiện tại không có mã nào</li>
            @endforelse
        </ul>
    </div>
</div>

<!-- Popup Chi tiết mã giảm giá -->
<div id="voucher-detail-popup" class="voucher-modal" style="display: none;">
    <div class="voucher-overlay"></div>
    <div class="voucher-popup-box">
        <button id="close-detail" class="voucher-close">&times;</button>
        <h3 class="voucher-title">📄 Chi tiết mã giảm giá</h3>
        <div class="voucher-detail-content"></div>
        <button id="apply-from-detail" class="apply-voucher" style="margin-top: 20px;">Áp dụng mã</button>
    </div>
</div>

<style>
    .voucher-modal {
        display: none;
        position: fixed;
        inset: 0;
        z-index: 10000;
        justify-content: center;
        align-items: center;
    }

    .voucher-modal.active {
        display: flex !important;
    }

    .voucher-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
    }

    .voucher-popup-box {
        position: relative;
        background: #fff;
        padding: 30px;
        border-radius: 16px;
        width: 500px;
        max-width: 95vw;
        max-height: 90vh;
        overflow-y: auto;
        z-index: 1;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        animation: slideUp 0.3s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(40px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .voucher-title {
        text-align: center;
        font-size: 22px;
        margin-bottom: 20px;
    }

    .voucher-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .voucher-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 12px 20px;
        margin-bottom: 12px;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 10px;
    }

    .voucher-code {
        font-weight: bold;
        color: #333;
    }

    .voucher-desc {
        font-size: 14px;
        color: #555;
        margin-bottom: 3px;
    }

    .apply-voucher,
    .view-detail {
        background-color: #2b9348;
        color: white;
        padding: 6px 12px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
    }

    .view-detail {
        background-color: #1e6091;
    }

    .voucher-close {
        position: absolute;
        top: 12px;
        right: 16px;
        font-size: 20px;
        border: none;
        background: none;
        color: #888;
        cursor: pointer;
    }

    .voucher-detail-content p {
        font-size: 14px;
        margin-bottom: 8px;
        color: #444;
    }
</style>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Popup chính
            const voucherPopup = document.getElementById('voucher-popup');
            const overlay = voucherPopup.querySelector('.voucher-overlay');
            const openBtn = document.querySelector('.voucher-toggle');
            const closeBtn = document.getElementById('close-voucher');

            // Popup chi tiết
            const detailPopup = document.getElementById('voucher-detail-popup');
            const detailContent = detailPopup.querySelector('.voucher-detail-content');
            const applyFromDetail = document.getElementById('apply-from-detail');
            const closeDetail = document.getElementById('close-detail');

            let selectedCode = null;

            openBtn?.addEventListener('click', () => {
                voucherPopup.classList.add('active');
            });

            closeBtn?.addEventListener('click', () => {
                voucherPopup.classList.remove('active');
            });

            overlay?.addEventListener('click', () => {
                voucherPopup.classList.remove('active');
            });

            // Áp dụng mã
            document.querySelectorAll('.apply-voucher').forEach(btn => {
                btn.addEventListener('click', () => {
                    const code = btn.dataset.code;
                    if (code) {
                        window.location.href = `/voucher/${code}/eligible-products`;
                    }
                });
            });

            // Xem chi tiết mã
            document.querySelectorAll('.view-detail').forEach(btn => {
                btn.addEventListener('click', () => {
                    console.log('Đã click nút chi tiết'); // kiểm tra debug
                    const data = JSON.parse(btn.dataset.voucher);
                    selectedCode = data.code;

                    detailContent.innerHTML = `
                <p><strong>Mã:</strong> ${data.code}</p>
                <p><strong>Tiêu đề:</strong> ${data.title}</p>
                <p><strong>Mô tả:</strong> ${data.description}</p>
                <p><strong>Loại:</strong> ${data.discount_type}</p>
                <p><strong>Giá trị:</strong> ${parseFloat(data.discount_value).toLocaleString()} ${data.discount_type === 'percentage' ? '%' : '₫'}</p>
                <p><strong>Giảm tối đa:</strong> ${parseFloat(data.max_discount).toLocaleString()} ₫</p>
                <p><strong>Đơn hàng tối thiểu:</strong> ${parseFloat(data.min_order_value).toLocaleString()} ₫</p>
                ${data.max_order_value ? `<p><strong>Đơn hàng tối đa:</strong> ${parseFloat(data.max_order_value).toLocaleString()} ₫</p>` : ''}
                <p><strong>Số lượng còn:</strong> ${data.quantity}</p>
                <p><strong>Giới hạn/người:</strong> ${data.user_usage_limit}</p>
                <p><strong>Áp dụng cho tất cả SP:</strong> ${data.applies_to_all_products ? 'Có' : 'Không'}</p>
                <p><strong>Trạng thái:</strong> ${data.status}</p>
                <p><strong>Bắt đầu:</strong> ${data.start_date}</p>
                <p><strong>Kết thúc:</strong> ${data.end_date}</p>
            `;

                    detailPopup.classList.add('active');
                });
            });

            // Áp dụng trong popup chi tiết
            applyFromDetail?.addEventListener('click', () => {
                if (selectedCode) {
                    window.location.href = `/voucher/${selectedCode}/eligible-products`;
                }
            });

            // Đóng popup chi tiết
            closeDetail?.addEventListener('click', () => {
                detailPopup.classList.remove('active');
            });

            detailPopup.querySelector('.voucher-overlay')?.addEventListener('click', () => {
                detailPopup.classList.remove('active');
            });
        });
    </script>
@endpush
