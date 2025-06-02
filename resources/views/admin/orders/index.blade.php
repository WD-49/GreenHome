@extends('layouts.admin')
@section('title', 'Danh sách đơn hàng')
@section('content')
    <div class="container mt-5">
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center rounded-top-4">
                <h3 class="mb-0 fw-bold">Danh sách đơn hàng</h3>
                <div>
                    <a href="{{ route('admin.orders.trash') }}" class="btn btn-outline-light btn-sm me-2"
                        data-bs-toggle="tooltip" title="Xem thùng rác">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                    <a href="{{ route('admin.orders.create') }}" class="btn btn-light btn-sm fw-semibold"
                        data-bs-toggle="tooltip" title="Tạo đơn hàng mới">
                        <i class="fas fa-plus-circle me-1"></i> Tạo đơn hàng
                    </a>
                </div>
            </div>

            {{-- Form Lọc giữ nguyên --}}
            <form method="GET" action="{{ route('admin.orders.index') }}" class="card-body">
                <div class="row gx-2 gy-2 align-items-center">
                    <div class="col-auto" style="min-width: 120px;">
                        <input type="text" name="order_code" class="form-control form-control-sm" placeholder="Mã đơn"
                            value="{{ request('order_code') }}">
                    </div>
                    <div class="col-auto" style="min-width: 140px;">
                        <input type="text" name="customer_name" class="form-control form-control-sm"
                            placeholder="Khách hàng" value="{{ request('customer_name') }}">
                    </div>
                    <div class="col-auto" style="min-width: 140px;">
                        <select name="payment_status" class="form-select form-select-sm">
                            <option value="">Trạng thái thanh toán</option>
                            <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Đã thanh toán
                            </option>
                            <option value="pending" {{ request('payment_status') == 'pending' ? 'selected' : '' }}>Chờ thanh
                                toán</option>
                            <option value="failed" {{ request('payment_status') == 'failed' ? 'selected' : '' }}>Thanh toán
                                thất bại</option>
                        </select>
                    </div>
                    <div class="col-auto" style="min-width: 140px;">
                        <select name="order_status" class="form-select form-select-sm">
                            <option value="">Trạng thái đơn hàng</option>
                            @foreach ($orderStatuses as $status)
                                <option value="{{ $status->id }}"
                                    {{ request('order_status') == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto" style="min-width: 130px;">
                        <input type="date" name="date_from" class="form-control form-control-sm"
                            value="{{ request('date_from') }}" placeholder="Từ ngày">
                    </div>
                    <div class="col-auto" style="min-width: 130px;">
                        <input type="date" name="date_to" class="form-control form-control-sm"
                            value="{{ request('date_to') }}" placeholder="Đến ngày">
                    </div>
                    <div class="col-auto" style="min-width: 150px;">
                        <select name="payment_method" class="form-select form-select-sm">
                            <option value="">Phương thức thanh toán</option>
                            @foreach ($paymentMethods as $method)
                                <option value="{{ $method->id }}"
                                    {{ request('payment_method') == $method->id ? 'selected' : '' }}>
                                    {{ $method->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-auto d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm fw-semibold">Lọc</button>
                        <a href="{{ route('admin.orders.index') }}"
                            class="btn btn-outline-secondary btn-sm fw-semibold">Đặt lại</a>
                    </div>
                </div>
            </form>


            <div class="card-body pt-0">
                <div class="table-responsive shadow-sm rounded-4">
                    <table class="table table-striped table-hover align-middle mb-0">
                        <thead class="table-dark text-center">
                            <tr>
                                <th scope="col" class="text-nowrap">STT</th>
                                <th scope="col" class="text-nowrap">Mã đơn</th>
                                <th scope="col" class="text-nowrap">Khách hàng</th>
                                <th scope="col" class="text-nowrap">Tên người nhận</th>
                                <th scope="col" class="text-nowrap">Ngày đặt</th>
                                <th scope="col" class="text-nowrap">Tổng tiền</th>
                                <th scope="col" class="text-nowrap">Phương thức</th>
                                <th scope="col" class="text-nowrap">Trạng thái</th>
                                <th scope="col" class="text-nowrap">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $index => $order)
                                <tr>
                                    <td class="text-center">
                                        {{ $loop->iteration + ($orders->currentPage() - 1) * $orders->perPage() }}</td>
                                    <td class="text-primary fw-bold">#{{ $order->sku ?? $order->id }}</td>
                                    <td>{{ $order->user->name ?? 'N/A' }}</td>
                                    <td>{{ $order->shipping_name }}</td>
                                    <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="text-end fw-semibold text-success">
                                        {{ number_format($order->total_amount, 0) }} VND</td>
                                    <td class="text-capitalize">{{ $order->paymentMethod->name }}</td>
                                    <td class="text-center">
                                        <span
                                            class="badge rounded-pill
                                        @if ($order->status->name == 'Hoàn tất') bg-success
                                        @elseif ($order->status->name == 'Đang xử lý') bg-warning text-dark
                                        @elseif ($order->status->name == 'Đã hủy') bg-danger
                                        @else bg-info text-dark @endif">
                                            {{ $order->status->name ?? 'Chưa cập nhật' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        {{-- NÚT 3 CHẤM VÀ DROPDOWN HÀNH ĐỘNG --}}
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-outline-secondary dropdown-toggle"
                                                type="button" id="actionDropdown-{{ $order->id }}"
                                                data-bs-toggle="dropdown" aria-expanded="false" title="Hành động">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end"
                                                aria-labelledby="actionDropdown-{{ $order->id }}">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.orders.edit', $order->id) }}">
                                                        <i class="fas fa-edit me-2"></i> Sửa
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('admin.orders.show', $order->id) }}">
                                                        <i class="fas fa-eye me-2"></i> Xem chi tiết
                                                    </a>
                                                </li>
                                                @if (method_exists($order, 'canBeCancelled') && $order->canBeCancelled())
                                                    <li>
                                                        <button type="button" class="dropdown-item"
                                                            data-bs-toggle="modal"
                                                            data-bs-target="#cancelOrderModal-{{ $order->id }}">
                                                            <i class="fas fa-ban me-2"></i> Hủy đơn hàng
                                                        </button>
                                                    </li>
                                                @endif
                                                <li>
                                                    <hr class="dropdown-divider">
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.orders.destroy', $order->id) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('Bạn có chắc muốn xóa đơn hàng này (đưa vào thùng rác)?')"
                                                        class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-danger">
                                                            <i class="fas fa-trash me-2"></i> Xóa (Thùng rác)
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>

                                {{-- MODAL HỦY ĐƠN HÀNG --}}
                                @if (method_exists($order, 'canBeCancelled') && $order->canBeCancelled())
                                    <div class="modal fade" id="cancelOrderModal-{{ $order->id }}" tabindex="-1"
                                        aria-labelledby="cancelOrderModalLabel-{{ $order->id }}" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form action="{{ route('admin.orders.cancel', $order->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="cancelOrderModalLabel-{{ $order->id }}">Hủy đơn hàng
                                                            #{{ $order->sku ?? $order->id }}</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            {{-- SỬA 'for' và 'id' ở đây --}}
                                                            <label for="cancel_reason-{{ $order->id }}"
                                                                class="form-label">Lý do hủy đơn hàng <span
                                                                    class="text-danger">*</span></label>
                                                            {{-- SỬA 'id' và 'name' ở đây --}}
                                                            <textarea class="form-control" id="cancel_reason-{{ $order->id }}" name="cancel_reason" rows="4" required
                                                                minlength="10"></textarea>
                                                            {{-- Nếu bạn có hiển thị lỗi validation cụ thể cho trường này, cũng cần cập nhật tên trường trong đó --}}

                                                            @if ($errors->hasBag("cancelForm_{$order->id}") && $errors->getBag("cancelForm_{$order->id}")->has('cancel_reason'))
                                                                <div class="invalid-feedback d-block">
                                                                    {{ $errors->getBag("cancelForm_{$order->id}")->first('cancel_reason') }}
                                                                </div>
                                                            @endif

                                                            <div id="cancel_reason_error-{{ $order->id }}"
                                                                class="invalid-feedback d-block"></div>
                                                            {{-- ID cho JS error display cũng nên thống nhất --}}
                                                        </div>
                                                        <p class="form-text">Vui lòng nhập ít nhất 10 ký tự.</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Đóng</button>
                                                        <button type="submit" class="btn btn-danger">Xác nhận
                                                            hủy</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-5">Không có đơn hàng nào.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4 d-flex justify-content-end">
                    {{ $orders->withQueryString()->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Enable Bootstrap 5 tooltips --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            tooltipTriggerList.forEach(function(tooltipTriggerEl) {
                new bootstrap.Tooltip(tooltipTriggerEl)
            });

            // Script để xử lý lỗi validation cho lý do hủy nếu không muốn reload trang
            // Hoặc bạn có thể xử lý lỗi chuẩn của Laravel bằng cách redirect back with errors.
            // Ví dụ đơn giản về hiển thị lỗi (nếu bạn dùng AJAX sau này hoặc muốn custom):
            document.querySelectorAll('form[action*="/cancel"]').forEach(form => {
                form.addEventListener('submit', function(event) {
                    const textarea = form.querySelector('textarea[name="cancellation_reason"]');
                    const errorDivId = 'cancellation_reason_error-' + textarea.id.split('-').pop();
                    const errorDiv = document.getElementById(errorDivId);
                    if (textarea.value.trim().length < 10) {
                        event.preventDefault(); // Ngăn submit
                        errorDiv.textContent = 'Lý do hủy phải có ít nhất 10 ký tự.';
                        textarea.classList.add('is-invalid');
                    } else {
                        errorDiv.textContent = '';
                        textarea.classList.remove('is-invalid');
                    }
                });
            });
        });
    </script>
@endsection
