@extends('layouts.admin')
@section('title', 'Quản lý đơn hàng')

@push('styles')
    {{-- Đảm bảo Font Awesome đã được nhúng --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Đảm bảo các đường dẫn này chính xác so với cấu trúc assets của bạn --}}
    <link href="../../assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <link href="../../assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />
    <link href="../../assets/libs/datatables.net-keytable-bs5/css/keyTable.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />
    <link href="../../assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />
    <link href="../../assets/libs/datatables.net-select-bs5/css/select.bootstrap5.min.css" rel="stylesheet"
        type="text/css" />

    <link href="../../assets/css/app.min.css" rel="stylesheet" type="text/css" id="app-style" />

    <link href="../../assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <style>
        /* Thêm style cho badge trạng thái */
        .badge-status {
            display: inline-block;
            padding: .35em .65em;
            font-size: .75em;
            font-weight: 700;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25rem;
        }

        .badge-status.bg-success {
            background-color: #28a745 !important;
        }

        .badge-status.bg-danger {
            background-color: #dc3545 !important;
        }

        .badge-status.bg-warning.text-dark {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .badge-status.bg-info.text-dark {
            background-color: #17a2b8 !important;
            color: #212529 !important;
        }

        .badge-status.bg-secondary {
            background-color: #6c757d !important;
        }

        .badge-status.bg-primary {
            background-color: #007bff !important;
        }

        /* Style cho nút hành động nhỏ hơn và có khoảng cách */
        .btn-action-sm {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            line-height: 1.5;
            border-radius: 0.2rem;
            margin-right: 0.25rem;
            /* Khoảng cách giữa các nút */
        }

        /* Để nút cuối cùng trong nhóm không có margin-right */
        .btn-action-sm:last-child {
            margin-right: 0;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxxl">

        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Quản lý đơn hàng</h4>
            </div>

            <div class="text-end">
                <ol class="breadcrumb m-0 py-0">
                    <h6 class="breadcrumb-item active">Home / Đơn hàng / Quản lý đơn hàng</h6>
                </ol>
            </div>
        </div>
        {{-- Thông báo thành công từ Session --}}
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        {{-- Thông báo lỗi từ Session --}}
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Danh sách đơn hàng</h5>
                        <div>
                            <div class="d-flex gap-2">
                                <h3 class="btn btn-warning shadow-sm">
                                    <i class="fas fa-bell fa-lg text-warning"></i> Đơn hàng chưa xác nhận hôm nay:
                                    {{ $unconfirmedTodayCount }}
                                </h3>
                                @if($refundRequestsCount > 0)
                                <h3 class="btn btn-info shadow-sm">
                                    <i class="fas fa-sync-alt fa-lg text-white"></i> Đơn có yêu cầu hoàn tiền:
                                    {{ $refundRequestsCount }}
                                </h3>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="card-body">


                        <table id="orders-datatable" class="table table-bordered dt-responsive table-responsive nowrap">
                            {{-- Đã đổi ID bảng --}}
                            <thead>
                                <tr>
                                    <th>STT</th>
                                    <th>Mã đơn</th>
                                    <th>Khách hàng</th>
                                    <th>Tên người nhận</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Phương thức</th>
                                    <th>Trạng thái thanh toán</th>
                                    <th>Trạng thái đơn hàng</th>
                                    <th>Trạng thái hoàn tiền</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($orders as $index => $order)
                                    <tr 
                                        @if ($order->order_status === 'Chưa xác nhận') 
                                            class="table-danger"
                                        @elseif ($order->refundTransactions && count($order->refundTransactions) > 0)
                                            class="table-warning"
                                        @endif
                                    >
                                        <td>{{ $index + 1 }}</td>
                                        <td>#{{ $order->sku ?? $order->id }}</td>
                                        <td>{{ $order->user->name ?? 'N/A' }}</td>
                                        <td>{{ $order->shipping_name }}</td>
                                        <td>
                                            {{ \Carbon\Carbon::parse($order->created_at)->format('d/m/Y H:i') }}
                                        </td>
                                        <td>
                                            {{ number_format($order->total_amount, 0, ',', '.') }} VNĐ
                                        </td>
                                        <td>
                                            {{ $order->payment_method_name ?? 'N/A' }}
                                        </td>
                                        <td>
                                            {{-- Hiển thị trạng thái thanh toán bằng tiếng Việt với màu sắc --}}
                                            @php
                                                $paymentStatus = $order->payment_status; // Lấy trạng thái từ DB
                                                $vietnamesePaymentStatus =
                                                    [
                                                        'pending' => 'Chờ thanh toán',
                                                        'paid' => 'Đã thanh toán',
                                                        'failed' => 'Thất bại',
                                                    ][$paymentStatus] ?? 'Không xác định';

                                                $paymentStatusBadgeClass = '';
                                                switch ($paymentStatus) {
                                                    case 'pending':
                                                        $paymentStatusBadgeClass = 'bg-warning text-dark';
                                                        break;
                                                    case 'paid':
                                                        $paymentStatusBadgeClass = 'bg-success';
                                                        break;
                                                    case 'failed':
                                                        $paymentStatusBadgeClass = 'bg-danger';
                                                        break;
                                                    default:
                                                        $paymentStatusBadgeClass = 'bg-secondary';
                                                        break;
                                                }
                                            @endphp
                                            <span class="badge rounded-pill {{ $paymentStatusBadgeClass }}">
                                                {{ $vietnamesePaymentStatus }}
                                            </span>
                                        </td>
                                        <td>
                                            {{-- Logic hiển thị badge trạng thái đơn hàng --}}
                                            @php
                                                $orderStatus = $order->order_status;
                                                $orderStatusBadgeClass = '';
                                                switch ($orderStatus) {
                                                    case 'Giao hàng thành công':
                                                        $orderStatusBadgeClass = 'bg-success';
                                                        break;
                                                    case 'Chưa xác nhận':
                                                        $orderStatusBadgeClass = 'bg-secondary';
                                                        break;
                                                    case 'Hủy đơn':
                                                        $orderStatusBadgeClass = 'bg-danger';
                                                        break;
                                                    case 'Đang vận chuyển':
                                                        $orderStatusBadgeClass = 'bg-info text-dark';
                                                        break;
                                                    case 'Xác nhận':
                                                        $orderStatusBadgeClass = 'bg-primary';
                                                        break;
                                                    default:
                                                        $orderStatusBadgeClass = 'bg-secondary';
                                                        break;
                                                }
                                            @endphp
                                            <span class="badge rounded-pill {{ $orderStatusBadgeClass }}">
                                                {{ $orderStatus ?? 'Chưa cập nhật' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($order->refundTransactions && count($order->refundTransactions) > 0)
                                                @php
                                                    $latestRefund = $order->refundTransactions->sortByDesc('created_at')->first();
                                                    $refundStatusClass = '';
                                                    $refundStatusText = '';
                                                    
                                                    switch($latestRefund->refund_status) {
                                                        case 'pending':
                                                            $refundStatusClass = 'bg-warning text-dark';
                                                            $refundStatusText = 'Chờ xử lý hoàn tiền';
                                                            break;
                                                        case 'approved':
                                                            $refundStatusClass = 'bg-info';
                                                            $refundStatusText = 'Đã duyệt hoàn tiền';
                                                            break;
                                                        case 'refunded':
                                                            $refundStatusClass = 'bg-success';
                                                            $refundStatusText = 'Đã hoàn tiền';
                                                            break;
                                                        case 'rejected':
                                                            $refundStatusClass = 'bg-danger';
                                                            $refundStatusText = 'Từ chối hoàn tiền';
                                                            break;
                                                        default:
                                                            $refundStatusClass = 'bg-secondary';
                                                            $refundStatusText = 'Không xác định';
                                                    }
                                                @endphp
                                                <span class="badge rounded-pill {{ $refundStatusClass }}">
                                                    {{ $refundStatusText }}
                                                </span>
                                            @else
                                                <span class="text-muted">Không có</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{-- Các nút hành động trực tiếp --}}
                                            <div class="btn-group" role="group" aria-label="Order Actions">
                                                {{-- Nút Xem chi tiết --}}
                                                <a href="{{ route('admin.orders.show', $order->id) }}"
                                                    class="btn btn-action-sm btn-info" title="Xem chi tiết">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Không có đơn hàng nào được tìm thấy.</td>
                                        {{-- Cập nhật colspan --}}
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> {{-- MODAL HỦY ĐƠN HÀNG (Một modal chung cho tất cả các nút hủy trong bảng) --}}
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                {{-- Form này sẽ được AJAX xử lý --}}
                <form id="modalCancelOrderForm" method="POST"> {{-- action sẽ được điền bởi JS --}}
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="cancelOrderModalLabel">Hủy đơn hàng <span id="modalOrderSku"></span>
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="order_id_to_cancel" id="order_id_to_cancel"> {{-- Trường ẩn để lưu ID đơn hàng --}}
                        <div class="mb-3">
                            <label for="cancellation_reason_modal" class="form-label">Lý do hủy (<span
                                    class="text-danger">*</span>):</label>
                            <textarea class="form-control" id="cancellation_reason_modal" name="cancellation_reason" rows="3"></textarea>
                            <div id="cancellation_reason_error_modal" class="invalid-feedback d-block"></div>
                        </div>
                        <p class="text-muted small">Vui lòng cung cấp lý do hủy chi tiết (ít nhất 10 ký tự).</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn btn-danger" id="confirmCancelBtn">Xác nhận Hủy</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    {{-- Đảm bảo các đường dẫn này chính xác và không bị trùng lặp với layout chính --}}
    <script src="../../assets/libs/jquery/jquery.min.js"></script>
    <script src="../../assets/libs/simplebar/simplebar.min.js"></script>
    <script src="../../assets/libs/node-waves/waves.min.js"></script>
    <script src="../../assets/libs/waypoints/lib/jquery.waypoints.min.js"></script>
    <script src="../../assets/libs/jquery.counterup/jquery.counterup.min.js"></script>
    <script src="../../assets/libs/feather-icons/feather.min.js"></script>

    <script src="../../assets/libs/datatables.net/js/jquery.dataTables.min.js"></script>
    <script src="../../assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js"></script>
    <script src="../../assets/libs/datatables.net-buttons/js/dataTables.buttons.min.js"></script>
    <script src="../../assets/libs/datatables.net-buttons/js/buttons.colVis.min.js"></script>
    <script src="../../assets/libs/datatables.net-buttons/js/buttons.flash.min.js"></script>
    <script src="../../assets/libs/datatables.net-buttons/js/buttons.html5.min.js"></script>
    <script src="../../assets/libs/datatables.net-buttons/js/buttons.print.min.js"></script>
    <script src="../../assets/libs/datatables.net-buttons-bs5/js/buttons.bootstrap5.min.js"></script>
    <script src="../../assets/libs/datatables.net-keytable/js/dataTables.keyTable.min.js"></script>
    <script src="../../assets/libs/datatables.net-keytable-bs5/js/keyTable.bootstrap5.min.js"></script>
    <script src="../../assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js"></script>
    <script src="../../assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js"></script>
    <script src="../../assets/libs/datatables.net-select/js/dataTables.select.min.js"></script>
    <script src="../../assets/libs/datatables.net-select-bs5/js/select.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // Lấy CSRF token từ meta tag và cấu hình jQuery để tự động gửi
            const csrfTokenGlobal = $('meta[name="csrf-token"]').attr('content');
            if (csrfTokenGlobal) {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': csrfTokenGlobal
                    }
                });
            } else {
                console.error("CSRF token meta tag not found. AJAX requests might fail.");
            }

            // Hàm hiển thị thông báo tạm thời (toast messages)
            function showTemporaryMessage(message, type = 'success', duration = 3000) {
                let alertClass = 'alert-success';
                if (type === 'error') alertClass = 'alert-danger';
                if (type === 'info') alertClass = 'alert-info';
                if (type === 'warning') alertClass = 'alert-warning';

                const messageId = 'temp-alert-' + Date.now();
                const messageDiv = $(
                    `<div class="alert ${alertClass} alert-dismissible fade show m-2" role="alert" id="${messageId}" style="position:fixed; top: 20px; right: 20px; z-index: 1050; min-width: 250px; max-width: 400px;">
                        ${message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>`
                );
                $('body').prepend(messageDiv);
                setTimeout(function() {
                    $('#' + messageId).fadeOut(500, function() {
                        $(this).remove();
                    });
                }, duration);
            }

            // Khởi tạo DataTables cho bảng của bạn
            if (!$.fn.DataTable.isDataTable('#orders-datatable')) {
                $('#orders-datatable').DataTable({
                    "paging": true,
                    "lengthChange": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "autoWidth": false,
                    "responsive": true,
                    // "language": {
                    //     "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/Vietnamese.json"
                    // }
                });
            }

            // --- Xử lý Modal Hủy đơn hàng (Chỉ một modal chung) ---
            const cancelOrderModalElement = document.getElementById('cancelOrderModal');
            const cancelOrderModal = new bootstrap.Modal(cancelOrderModalElement);
            const modalCancelOrderForm = $('#modalCancelOrderForm');
            const modalOrderSkuSpan = $('#modalOrderSku');
            const orderIdToCancelInput = $('#order_id_to_cancel');
            const modalCancellationReasonTextarea = $('#cancellation_reason_modal');
            const modalCancellationErrorDiv = $('#cancellation_reason_error_modal');

            // Khi nút "Hủy đơn hàng" trong bảng được click
            $('#orders-datatable').on('click', '.cancel-order-btn', function() {
                const orderId = $(this).data('orderId');
                const orderSku = $(this).closest('tr').find('td:nth-child(2)').text().trim();

                modalOrderSkuSpan.text(orderSku);
                orderIdToCancelInput.val(orderId);

                // Reset form validation và nội dung khi mở modal
                modalCancellationReasonTextarea.val('');
                modalCancellationReasonTextarea.removeClass('is-invalid');
                modalCancellationErrorDiv.text('');

                cancelOrderModal.show();
            });

            // Xử lý submit form Hủy đơn hàng trong modal
            modalCancelOrderForm.on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const button = form.find('button[type="submit"]');
                const originalButtonHtml = button.html();

                // CLIENT-SIDE VALIDATION cho lý do hủy
                const reason = modalCancellationReasonTextarea.val().trim();
                const MIN_REASON_LENGTH = 10; // Đặt giá trị minlength bạn muốn

                if (reason.length < MIN_REASON_LENGTH) {
                    modalCancellationReasonTextarea.addClass('is-invalid');
                    modalCancellationErrorDiv.text(`Lý do hủy phải có ít nhất ${MIN_REASON_LENGTH} ký tự.`);
                    return; // Dừng submit nếu validation thất bại
                } else {
                    modalCancellationReasonTextarea.removeClass('is-invalid');
                    modalCancellationErrorDiv.text('');
                }

                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                const orderId = orderIdToCancelInput.val();
                const cancelUrl = `{{ route('admin.orders.cancel', ['order' => ':orderId']) }}`.replace(
                    ':orderId', orderId);

                // Gửi dữ liệu một cách tường minh để loại trừ vấn đề serialize()
                $.ajax({
                    url: cancelUrl,
                    type: 'POST', // Đảm bảo khớp với route (POST/PATCH)
                    data: {
                        _token: csrfTokenGlobal, // Gửi token từ biến global
                        cancellation_reason: reason // Gửi giá trị đã được trim()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showTemporaryMessage(response.message, 'success');
                            cancelOrderModal.hide();
                            setTimeout(function() {
                                location.reload();
                            }, 500);
                        } else {
                            if (response.errors && response.errors.cancel_reason) {
                                modalCancellationReasonTextarea.addClass('is-invalid');
                                modalCancellationErrorDiv.text(response.errors.cancel_reason[
                                    0]);
                            } else {
                                showTemporaryMessage(response.message ||
                                    'Hủy đơn hàng thất bại.', 'error');
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Error canceling order:', xhr.responseText);
                        const errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr
                            .responseJSON.message : 'Lỗi hệ thống khi hủy đơn hàng.';
                        showTemporaryMessage(errorMessage, 'error');
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors &&
                            xhr.responseJSON.errors.cancel_reason) {
                            modalCancellationReasonTextarea.addClass('is-invalid');
                            modalCancellationErrorDiv.text(xhr.responseJSON.errors
                                .cancel_reason[0]);
                        }
                    },
                    complete: function() {
                        button.prop('disabled', false).html(originalButtonHtml);
                    }
                });
            });


            // Xử lý form "Xóa (Thùng rác)" (Soft Delete Order)
            $('#orders-datatable').on('submit', 'form.soft-delete-order-form', function(e) {
                e.preventDefault();
                const form = $(this);
                const button = form.find('button[type="submit"]');
                const originalButtonHtml = button.html();

                if (!confirm('Bạn có chắc muốn xóa đơn hàng này (đưa vào thùng rác)?')) {
                    return;
                }

                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                $.ajax({
                    url: form.attr('action'),
                    type: 'POST', // Laravel dùng POST cho @method('DELETE')
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showTemporaryMessage(response.message, 'info');
                            form.closest('tr').fadeOut(500, function() {
                                $(this).remove();
                                if ($.fn.DataTable.isDataTable('#orders-datatable')) {
                                    $('#orders-datatable').DataTable().row(this)
                                        .remove().draw();
                                }
                            });
                        } else {
                            showTemporaryMessage(response.message || 'Xóa thất bại.', 'error');
                        }
                    },
                    error: function(xhr) {
                        console.error('Error soft deleting order:', xhr.responseText);
                        const errorMessage = xhr.responseJSON && xhr.responseJSON.message ? xhr
                            .responseJSON.message : 'Lỗi hệ thống khi xóa đơn hàng.';
                        showTemporaryMessage(errorMessage, 'error');
                    },
                    complete: function() {
                        button.prop('disabled', false).html(originalButtonHtml);
                    }
                });
            });

            // Khởi tạo tooltip cho tất cả các phần tử có data-bs-toggle="tooltip" trên trang
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
