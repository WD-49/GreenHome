document.addEventListener('DOMContentLoaded', function () {
  // ========== XỬ LÝ CONFIRM ALERT ==========

  // Tạo modal confirm nếu chưa có trong DOM (tự động thêm)
  if (!document.getElementById('customConfirmModal')) {
    const modalHTML = `
      <div class="modal fade" id="customConfirmModal" tabindex="-1" aria-hidden="true" aria-labelledby="customConfirmLabel">
        <div class="modal-dialog">
          <div class="modal-content border-warning">
            <div class="modal-header bg-success text-dark">
              <h5 class="modal-title" id="customConfirmLabel">Xác nhận</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>
            <div class="modal-body" id="customConfirmBody"></div>
            <div class="modal-footer">
              <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Hủy</button>
              <button type="button" class="btn btn-success" id="customConfirmBtn">Xác nhận</button>
            </div>
          </div>
        </div>
      </div>`;
    document.body.insertAdjacentHTML('beforeend', modalHTML);
  }

  const confirmModalEl = document.getElementById('customConfirmModal');
  const confirmModal = new bootstrap.Modal(confirmModalEl);
  const confirmModalBody = document.getElementById('customConfirmBody');
  const confirmModalBtn = document.getElementById('customConfirmBtn');
  let formToSubmit = null;

  // Bắt sự kiện click cho các nút confirm
  document.querySelectorAll('.btn-confirm').forEach(btn => {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      formToSubmit = this.closest('form');
      const message = this.dataset.confirmMessage || 'Bạn có chắc chắn muốn thực hiện thao tác này?';
      confirmModalBody.textContent = message;
      confirmModal.show();
    });
  });

  // Khi nhấn nút Xác nhận modal
  confirmModalBtn.addEventListener('click', function () {
    if (formToSubmit) {
      formToSubmit.submit();
    }
  });

  setTimeout(function () {
    let alerts = document.querySelectorAll('.alert');
    alerts.forEach(function (alertEl) {
      let alert = new bootstrap.Alert(alertEl);
      alert.close();
    });
  }, 3000);
});

document.querySelectorAll('.attribute-checkbox').forEach(checkbox => {
  checkbox.addEventListener('change', function () {
    const select = this.closest('.form-check').querySelector('.attribute-select');
    if (select) {
      select.disabled = !this.checked;
    }
  });
});

