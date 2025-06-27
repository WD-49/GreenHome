/**
 * Hiển thị thông báo toàn cục, có thể tái sử dụng cho mọi loại thông báo
 * @param {string} message - Nội dung thông báo
 * @param {string} [type] - Loại thông báo: 'success', 'error', 'info' (tùy chỉnh CSS nếu muốn)
 */
window.showNotify = function (message, type = 'success') {
    const notify = document.getElementById('global-notify');
    const msg = document.getElementById('global-notify-message');
    if (!notify || !msg) return;

    msg.textContent = message;
    notify.className = '';
    notify.classList.add('notify-' + type);
    notify.style.display = 'block';
    setTimeout(() => {
        notify.style.display = 'none';
    }, 2000);
}