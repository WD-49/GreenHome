<!-- resources/views/components/voucher-popup.blade.php -->
<div id="voucher-popup" class="voucher-modal"style="display: none;">
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
                        <div class="voucher-desc">🏷 Tối đa: {{ number_format($voucher->max_discount, 0) }}đ</div>
                        <div class="voucher-desc">📟 Đơn tối thiểu: {{ number_format($voucher->min_order_value, 0) }}đ</div>
                        <div class="voucher-desc">⏰ Hết hạn: {{ \Carbon\Carbon::parse($voucher->end_date)->format('d/m/Y') }}</div>
                    </div>
                    <button class="apply-voucher" data-code="{{ $voucher->code }}">Áp dụng</button>
                </li>
            @empty
                <li class="voucher-item">Hiện tại không có mã nào</li>
            @endforelse
        </ul>
    </div>
</div>

<style>
.voucher-modal {
    display: none;
    position: fixed;
    inset: 0;
    z-index: 9999;
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
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
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
.apply-voucher {
    background-color: #2b9348;
    color: white;
    padding: 6px 12px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
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
</style>

@push('scripts')
<script>

    document.addEventListener('DOMContentLoaded', function () {
        const voucherPopup = document.getElementById('voucher-popup');
        const overlay = voucherPopup.querySelector('.voucher-overlay');
        const openBtn = document.querySelector('.voucher-toggle');
        const closeBtn = document.getElementById('close-voucher');

        // Debug xem có lấy được phần tử hay không
        console.log('openBtn:', openBtn);
        console.log('voucherPopup:', voucherPopup);

        openBtn?.addEventListener('click', () => {
            voucherPopup.classList.add('active');
        });

        closeBtn?.addEventListener('click', () => {
            voucherPopup.classList.remove('active');
        });

        overlay?.addEventListener('click', () => {
            voucherPopup.classList.remove('active');
        });

     document.querySelectorAll('.apply-voucher').forEach(btn => {
    btn.addEventListener('click', () => {
        const code = btn.dataset.code;
        window.location.href = `/voucher/${code}/eligible-products`; // điều hướng sang trang lọc sản phẩm đủ điều kiện
    });
});

    });

console.log('voucherPopup:', voucherPopup);
console.log('openBtn (Voucher-toggle):', openBtn);

</script>
@endpush
