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

                        <a href="javascript:void(0)" class="view-detail" data-voucher='@json($voucher)'>
                            Chi tiết
                        </a>

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

   .voucher-detail-card {
    background: #fdfdfd;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.1);
    animation: fadeInDetail 0.3s ease-in-out;
    font-size: 15px;
    color: #333;
}

.voucher-header {
    text-align: center;
    margin-bottom: 20px;
}

.voucher-code-big {
    font-size: 26px;
    font-weight: bold;
    color: #2b9348;
    margin-bottom: 6px;
}

.voucher-title-small {
    font-size: 16px;
    color: #666;
}

.voucher-info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px 24px;
    line-height: 1.5;
}

@keyframes fadeInDetail {
    from {
        opacity: 0;
        transform: scale(0.95);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}
.voucher-detail-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 12px;
    font-size: 15px;
}

.voucher-detail-table th,
.voucher-detail-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #eee;
    text-align: left;
}

.voucher-detail-table th {
    background-color: #f7f7f7;
    width: 40%;
    color: #444;
}

.voucher-detail-table td {
    color: #333;
}

.voucher-detail-card {
    animation: fadeInDetail 0.3s ease-in-out;
}

</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const voucherPopup = document.getElementById('voucher-popup');
    const voucherOverlay = voucherPopup.querySelector('.voucher-overlay');
    const openVoucherBtn = document.querySelector('.voucher-toggle');
    const closeVoucherBtn = document.getElementById('close-voucher');

    const detailPopup = document.getElementById('voucher-detail-popup');
    const detailOverlay = detailPopup.querySelector('.voucher-overlay');
    const detailContent = detailPopup.querySelector('.voucher-detail-content');
    const closeDetailBtn = document.getElementById('close-detail');
    const applyFromDetailBtn = document.getElementById('apply-from-detail');

    let selectedCode = null;

    // ====== Hiển thị Popup Voucher chính ======
    openVoucherBtn?.addEventListener('click', () => {
        voucherPopup.classList.add('active');
    });

    closeVoucherBtn?.addEventListener('click', () => {
        voucherPopup.classList.remove('active');
    });

    voucherOverlay?.addEventListener('click', () => {
        voucherPopup.classList.remove('active');
    });

    // ====== Áp dụng mã giảm giá từ danh sách ======
    document.querySelectorAll('.apply-voucher').forEach(button => {
        button.addEventListener('click', () => {
            const code = button.dataset.code;
            if (code) {
                window.location.href = `/voucher/${code}/eligible-products`;
            }
        });
    });

    // ====== Hiển thị chi tiết mã giảm giá bằng popup ======
    document.querySelectorAll('.view-detail').forEach(button => {
        button.addEventListener('click', () => {
            try {
                const data = JSON.parse(button.dataset.voucher);
                selectedCode = data.code;

             detailContent.innerHTML = `
    <div class="voucher-detail-card">
        <div class="voucher-header">
            <div class="voucher-code-big">🎟 ${data.code}</div>
            <div class="voucher-title-small">${data.title}</div>
        </div>
        <table class="voucher-detail-table">
            <tbody>
                <tr>
                    <th>Mô tả</th>
                    <td>${data.description || 'Không có mô tả'}</td>
                </tr>
                <tr>
                    <th>Loại</th>
                    <td>${data.discount_type === 'percentage' ? 'Phần trăm (%)' : 'Cố định (VNĐ)'}</td>
                </tr>
                <tr>
                    <th>Giá trị giảm</th>
                    <td>${parseFloat(data.discount_value).toLocaleString()} ${data.discount_type === 'percentage' ? '%' : '₫'}</td>
                </tr>
                <tr>
                    <th>Giảm tối đa</th>
                    <td>${parseFloat(data.max_discount).toLocaleString()} ₫</td>
                </tr>
                <tr>
                    <th>Đơn tối thiểu</th>
                    <td>${parseFloat(data.min_order_value).toLocaleString()} ₫</td>
                </tr>
                ${data.max_order_value ? `
                <tr>
                    <th>Đơn tối đa</th>
                    <td>${parseFloat(data.max_order_value).toLocaleString()} ₫</td>
                </tr>` : ''}
                <tr>
                    <th>Số lượng còn</th>
                    <td>${data.quantity}</td>
                </tr>
                <tr>
                    <th>Giới hạn/người</th>
                    <td>${data.user_usage_limit}</td>
                </tr>
                <tr>
                    <th>Áp dụng toàn bộ SP</th>
                    <td>${data.applies_to_all_products ? 'Có' : 'Không'}</td>
                </tr>
                <tr>
                    <th>Ngày bắt đầu</th>
                    <td>${data.start_date}</td>
                </tr>
                <tr>
                    <th>Ngày kết thúc</th>
                    <td>${data.end_date}</td>
                </tr>
            </tbody>
        </table>
    </div>
`;

                detailPopup.classList.add('active');
            } catch (error) {
                console.error("❌ Lỗi khi phân tích JSON chi tiết mã giảm giá:", error);
                alert('Không thể hiển thị chi tiết mã giảm giá.');
            }
        });
    });

    // ====== Áp dụng mã từ popup chi tiết ======
    applyFromDetailBtn?.addEventListener('click', () => {
        if (selectedCode) {
            window.location.href = `/voucher/${selectedCode}/eligible-products`;
        }
    });

    // ====== Đóng popup chi tiết ======
    closeDetailBtn?.addEventListener('click', () => {
        detailPopup.classList.remove('active');
    });

    detailOverlay?.addEventListener('click', () => {
        detailPopup.classList.remove('active');
    });
});
</script>
@endpush

