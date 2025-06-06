@extends('layouts.admin')

@push('styles')
    {{-- Đẩy style của trang này vào stack 'styles' của layout --}}
    <style>
        .nav-tabs .nav-link.active {
            /* Có thể tùy chỉnh thêm nếu cần */
        }

        .img-fluid.rounded-circle {
            border: 3px solid #e9ecef;
        }

        .card-title {
            color: #333;
        }

        .table-sm th,
        .table-sm td {
            padding: 0.5rem;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid mt-4">
        <div class="d-sm-flex align-items-center justify-content-between mb-4">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-user-circle me-2"></i>Chi tiết tài khoản: {{ $admins->name }}
            </h1>
            <a href="{{ route('admin.account.listUsers') }}" class="btn btn-sm btn-outline-secondary shadow-sm">
                <i class="fas fa-arrow-left fa-sm text-gray-700 me-1"></i> Quay lại danh sách
            </a>
        </div>

        <div class="row">
            {{-- Cột Thông tin cá nhân --}}
            <div class="col-lg-4 mb-4">
                {{-- ... (Nội dung cột thông tin cá nhân giữ nguyên như bạn cung cấp) ... --}}
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white py-3">
                        <h6 class="m-0 font-weight-bold"><i class="fas fa-id-card me-2"></i>Thông tin Cá nhân</h6>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ $admins->profile && $admins->profile->user_image ? asset('storage/' . $admins->profile->user_image) : 'https://cdn2.iconfinder.com/data/icons/audio-16/96/user_avatar_profile_login_button_account_member-512.png' }}"
                            alt="Ảnh đại diện" class="img-fluid rounded-circle mb-3 shadow"
                            style="width: 150px; height: 150px; object-fit: cover;">

                        <h4 class="card-title mb-1">{{ $admins->name }}</h4>
                        <p class="text-muted mb-2">{{ $admins->email }}</p>
                        <p class="mb-1">
                            <strong>Vai trò:</strong>
                            @if ($admins->role == 'admin' || $admins->role == 'superadmin')
                                <span class="badge bg-success">{{ ucfirst($admins->role) }}</span>
                            @else
                                <span class="badge bg-info text-dark">{{ ucfirst($admins->role) }}</span>
                            @endif
                        </p>
                        <p class="mb-3">
                            <strong>Trạng thái:</strong>
                            @if ($admins->status == 1)
                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Hoạt động</span>
                            @else
                                <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Ngừng hoạt động</span>
                            @endif
                        </p>
                        <hr>
                        @if ($admins->profile)
                            <div class="text-start">
                                <p class="mb-2"><strong><i class="fas fa-phone me-2 text-primary"></i>SĐT:</strong>
                                    {{ $admins->profile->phone ?: 'Chưa cập nhật' }}</p>
                                <p class="mb-2"><strong><i class="fas fa-map-marker-alt me-2 text-primary"></i>Địa
                                        chỉ:</strong> {{ $admins->profile->address ?: 'Chưa cập nhật' }}</p>
                                <p class="mb-0"><strong><i class="fas fa-venus-mars me-2 text-primary"></i>Giới
                                        tính:</strong>
                                    @if ($admins->profile->gender == 'male' || $admins->profile->gender == 'nam')
                                        Nam
                                    @elseif($admins->profile->gender == 'female' || $admins->profile->gender == 'nu')
                                        Nữ
                                    @else
                                        {{ ucfirst($admins->profile->gender ?: 'Khác') }}
                                    @endif
                                </p>
                            </div>
                        @else
                            <div class="alert alert-warning mt-3" role="alert">
                                <i class="fas fa-exclamation-triangle me-1"></i> Người dùng chưa cập nhật thông tin hồ sơ.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Cột chứa Tabs --}}
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header p-0">
                        <ul class="nav nav-tabs nav-fill" id="userTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="orders-tab" data-bs-toggle="tab"
                                    data-bs-target="#orders-pane" type="button" role="tab" aria-controls="orders-pane"
                                    aria-selected="true">
                                    <i class="fas fa-receipt me-1"></i> Đơn hàng
                                    @if (isset($admins->orders_count) && $admins->orders_count > 0)
                                        <span class="badge rounded-pill bg-info ms-1">{{ $admins->orders_count }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cart-tab" data-bs-toggle="tab" data-bs-target="#cart-pane"
                                    type="button" role="tab" aria-controls="cart-pane" aria-selected="false">
                                    <i class="fas fa-shopping-cart me-1"></i> Giỏ hàng Hiện tại
                                    @if (isset($admins->cart_items_count) && $admins->cart_items_count > 0)
                                        <span
                                            class="badge rounded-pill bg-warning text-dark ms-1">{{ $admins->cart_items_count }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="comments-tab" data-bs-toggle="tab"
                                    data-bs-target="#comments-pane" type="button" role="tab"
                                    aria-controls="comments-pane" aria-selected="false">
                                    <i class="fas fa-comments me-1"></i> Bình luận
                                    @php $totalCommentsCount = $admins->comments->count(); @endphp
                                    @if ($totalCommentsCount > 0)
                                        <span class="badge rounded-pill bg-secondary ms-1">{{ $totalCommentsCount }}</span>
                                    @endif
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="userTabsContent">
                            {{-- Tab Đơn hàng --}}
                            <div class="tab-pane fade show active" id="orders-pane" role="tabpanel"
                                aria-labelledby="orders-tab">
                                {{-- THÊM NỘI DUNG TEST ĐỂ KIỂM TRA TAB CÓ HOẠT ĐỘNG KHÔNG --}}
                                {{-- <h1>NỘI DUNG TAB ĐƠN HÀNG</h1> --}}
                                {{-- Nội dung thật của tab đơn hàng --}}
                                <h5 class="mb-3">Danh sách Đơn hàng</h5>
                                @if ($admins->orders && $admins->orders->count() > 0)
                                    {{-- ... (Bảng đơn hàng của bạn giữ nguyên) ... --}}
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped table-sm">
                                            {{-- ... thead ... --}}
                                            <tbody>
                                                @foreach ($admins->orders as $order)
                                                    <tr id="order-summary-row-{{ $order->id }}"> {{-- ID cho hàng tóm tắt --}}
                                                        <td>#{{ $order->sku ?? $order->id }}</td>
                                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                                        <td>{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</td>
                                                        <td>
                                                            {{-- Thêm badge cho trạng thái --}}
                                                            @if ($order->status)
                                                                <span
                                                                    class="badge {{ $order->status->color_class ?? 'bg-secondary' }}">
                                                                    {{ $order->status->name }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-secondary">Chưa xác định</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{-- NÚT XEM CHI TIẾT ĐƠN HÀNG MỚI --}}
                                                            <button
                                                                class="btn btn-xs btn-outline-primary view-order-details-btn"
                                                                data-order-id="{{ $order->id }}"
                                                                title="Xem chi tiết đơn hàng" data-bs-toggle="tooltip"
                                                                data-bs-placement="top">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            {{-- Bạn có thể thêm các nút khác ở đây nếu cần, ví dụ nút hủy đơn hàng nếu trạng thái cho phép --}}
                                                        </td>
                                                    </tr>
                                                    {{-- HÀNG ẨN ĐỂ HIỂN THỊ CHI TIẾT ĐƠN HÀNG --}}
                                                    <tr class="order-detail-row" id="order-detail-row-{{ $order->id }}"
                                                        style="display: none;">
                                                        <td colspan="5"> {{-- Colspan bằng số lượng cột của bảng --}}
                                                            <div class="order-detail-content p-3 border-top bg-light"
                                                                id="order-detail-content-{{ $order->id }}">
                                                                <p class="text-center text-muted mb-0"><i
                                                                        class="fas fa-spinner fa-spin"></i> Đang tải chi
                                                                    tiết đơn hàng...</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-light text-center" role="alert">
                                        <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                        Người dùng này chưa có đơn hàng nào.
                                    </div>
                                @endif
                            </div>

                            {{-- Tab Giỏ hàng --}}
                            <div class="tab-pane fade" id="cart-pane" role="tabpanel" aria-labelledby="cart-tab">
                                {{-- <h1>NỘI DUNG TAB GIỎ HÀNG</h1> --}}
                                {{-- Nội dung thật của tab giỏ hàng --}}
                                <h5 class="mb-3">Sản phẩm trong Giỏ hàng</h5>
                                @if ($admins->cartItems && $admins->cartItems->count() > 0)
                                    {{-- ... (Bảng giỏ hàng của bạn giữ nguyên) ... --}}
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm align-middle">
                                            {{-- ... thead ... --}}
                                            <tbody>
                                                @php $cartTotal = 0; @endphp
                                                @foreach ($admins->cartItems as $item)
                                                    @php
                                                        $productImageUrl =
                                                            optional($item->productVariant->product)->image ??
                                                            (optional($item->productVariant->product->images)->first()
                                                                ->image_url ??
                                                                'https://placehold.co/60x60/EBF0F5/7F8EA3?text=Ảnh+SP');
                                                        $productName =
                                                            optional($item->productVariant->product)->name ??
                                                            'Sản phẩm không xác định';
                                                        $variantName = $item->productVariant
                                                            ? collect($item->productVariant->attributes)
                                                                ->pluck('value')
                                                                ->implode(' - ')
                                                            : '';
                                                        if ($variantName) {
                                                            $productName .= ' (' . $variantName . ')';
                                                        }
                                                        $quantity = $item->quantity ?? 1;
                                                        $price = $item->unit_price ?? 0;
                                                        $lineTotal = $quantity * $price;
                                                        $cartTotal += $lineTotal;
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            <img src="{{ str_starts_with($productImageUrl, 'http') ? $productImageUrl : asset('storage/' . $productImageUrl) }}"
                                                                alt="{{ $productName }}" class="img-fluid rounded"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                        </td>
                                                        <td>{{ $productName }}</td>
                                                        <td>{{ $quantity }}</td>
                                                        <td>{{ number_format($price, 0, ',', '.') }} VNĐ</td>
                                                        <td>{{ number_format($lineTotal, 0, ',', '.') }} VNĐ</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="4" class="text-end fw-bold">Tổng cộng:</td>
                                                    <td class="fw-bold">{{ number_format($cartTotal, 0, ',', '.') }} VNĐ
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                @else
                                    <div class="alert alert-light text-center" role="alert">
                                        <i class="fas fa-shopping-bag fa-2x mb-2 d-block"></i>
                                        Giỏ hàng của người dùng hiện đang trống.
                                    </div>
                                @endif
                            </div>

                            {{-- Tab Bình luận --}}
                            <div class="tab-pane fade" id="comments-pane" role="tabpanel"
                                aria-labelledby="comments-tab">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Danh sách Bình luận</h5>
                                    <button id="toggleTrashedCommentsBtn" class="btn btn-sm btn-outline-danger"
                                        data-user-id="{{ $admins->id }}"
                                        data-fetch-url="{{ route('admin.account.comment.account.trashedComments', ['user' => $admins->id]) }}">
                                        <i class="fas fa-trash"></i> Thùng rác bình luận
                                    </button>
                                </div>
                                <div id="activeCommentsContainer">
                                    @php
                                        $activeComments = $admins->comments->filter(function ($comment) {
                                            return is_null($comment->deleted_at);
                                        });
                                    @endphp
                                    @if ($activeComments->count() > 0)
                                        <table class="table table-sm table-bordered table-hover">
                                            <tbody>
                                                @foreach ($activeComments as $comment)
                                                    @php
                                                        // Tái tạo logic tạo trạng thái và nút bấm như trong controller helper getCommentUIData
                                                        // Hoặc truyền dữ liệu này từ controller chính (detailAccUser) nếu bạn có thể
                                                        $statusText = '';
                                                        $statusClassBadge = '';
                                                        $currentActionsHtml = '';

                                                        $currentActionsHtml .=
                                                            '<button class="btn btn-xs btn-outline-info view-comment-details-btn me-1" data-comment-id="' .
                                                            $comment->id .
                                                            '" title="Xem chi tiết" data-bs-toggle="tooltip"><i class="fas fa-eye"></i></button>';

                                                        switch ($comment->status) {
                                                            case 'hiển thị':
                                                                $statusText = 'Hiển thị';
                                                                $statusClassBadge = 'bg-success';
                                                                $currentActionsHtml .=
                                                                    '<button class="btn btn-xs btn-outline-secondary change-comment-status-btn me-1" data-comment-id="' .
                                                                    $comment->id .
                                                                    '" data-action="hide" title="Ẩn bình luận này" data-bs-toggle="tooltip"><i class="fas fa-eye-slash"></i></button>';
                                                                break;
                                                            case 'ẩn':
                                                                $statusText = 'Bị ẩn';
                                                                $statusClassBadge = 'bg-warning text-dark';
                                                                $currentActionsHtml .=
                                                                    '<button class="btn btn-xs btn-outline-info change-comment-status-btn me-1" data-comment-id="' .
                                                                    $comment->id .
                                                                    '" data-action="show_again" title="Hiện lại bình luận này" data-bs-toggle="tooltip"><i class="fas fa-redo-alt"></i></button>';
                                                                break;
                                                            case 'chưa duyệt':
                                                            default:
                                                                $statusText = 'Chưa duyệt';
                                                                $statusClassBadge = 'bg-secondary';
                                                                $currentActionsHtml .=
                                                                    '<button class="btn btn-xs btn-outline-primary change-comment-status-btn me-1" data-comment-id="' .
                                                                    $comment->id .
                                                                    '" data-action="approve" title="Duyệt bình luận này" data-bs-toggle="tooltip"><i class="fas fa-check"></i></button>';
                                                                // Optional: Thêm nút ẩn từ trạng thái chưa duyệt
                                                                $currentActionsHtml .=
                                                                    '<button class="btn btn-xs btn-outline-secondary change-comment-status-btn ms-1 me-1" data-comment-id="' .
                                                                    $comment->id .
                                                                    '" data-action="hide" title="Ẩn bình luận (từ chưa duyệt)" data-bs-toggle="tooltip"><i class="fas fa-eye-slash"></i></button>';
                                                                break;
                                                        }
                                                        $currentActionsHtml .=
                                                            '<button class="btn btn-xs btn-outline-danger soft-delete-comment-btn" data-comment-id="' .
                                                            $comment->id .
                                                            '" title="Chuyển vào thùng rác" data-bs-toggle="tooltip"><i class="fas fa-trash-alt"></i></button>';
                                                    @endphp

                                                    <tr id="active-comment-row-{{ $comment->id }}">
                                                        <td>{{ $comment->id }}</td>
                                                        <td>{{ Str::limit($comment->content, 70) }}</td>
                                                        <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                                                        <td class="comment-status-cell"
                                                            id="comment-status-cell-{{ $comment->id }}">
                                                            <span
                                                                class="badge {{ $statusClassBadge }}">{{ $statusText }}</span>
                                                        </td>
                                                        <td class="comment-actions-cell"
                                                            id="comment-actions-cell-{{ $comment->id }}">
                                                            {!! $currentActionsHtml !!}
                                                        </td>
                                                    </tr>
                                                    {{-- Hàng chi tiết bình luận (đã có) --}}
                                                    <tr class="comment-detail-row"
                                                        id="comment-detail-row-{{ $comment->id }}"
                                                        style="display: none;">
                                                        <td colspan="5">
                                                            <div class="comment-detail-content p-2 border-top"
                                                                id="comment-detail-content-{{ $comment->id }}">
                                                                <p class="text-center text-muted"><i
                                                                        class="fas fa-spinner fa-spin"></i> Đang tải chi
                                                                    tiết...</p>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        <div class="alert alert-light text-center">Không có bình luận nào đang hoạt động.
                                        </div>
                                    @endif
                                </div>
                                <hr>
                                <div id="trashedCommentsSection" style="display: none;">
                                    <h5 class="mb-3">Bình luận đã xóa</h5>
                                    <div id="trashedCommentsContainer">
                                        <p class="text-center text-muted">Nhấp vào "Thùng rác bình luận" để xem.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            const csrfTokenGlobal = $('meta[name="csrf-token"]').attr('content');

            // --- HÀM TIỆN ÍCH ---
            function updateActiveCommentUIAfterAction(commentId, statusText, statusClassBadge, actionsHtml) {
                const statusCell = $('#comment-status-cell-' + commentId);
                const actionsCell = $('#comment-actions-cell-' + commentId);

                if (statusCell.length) {
                    statusCell.html(`<span class="badge ${statusClassBadge}">${statusText}</span>`);
                }
                if (actionsCell.length) {
                    actionsCell.html(actionsHtml);
                    actionsCell.find('[data-bs-toggle="tooltip"]').tooltip('dispose').tooltip();
                }
            }

            function updateTotalCommentsBadgeOnTab(newCount) {
                const badgeElement = $('#comments-tab').find('.badge.rounded-pill');
                if (badgeElement.length) {
                    if (newCount > 0) {
                        badgeElement.text(newCount).show();
                    } else {
                        badgeElement.text('0').hide();
                    }
                } else {
                    if (newCount > 0) {
                        $('#comments-tab').append(
                            `<span class="badge rounded-pill bg-secondary ms-1">${newCount}</span>`);
                    }
                }
            }

            function showTemporaryMessage(message, type = 'success', duration = 3500) {
                let alertClass = 'alert-success';
                if (type === 'error') alertClass = 'alert-danger';
                if (type === 'info') alertClass = 'alert-info';
                const messageId = 'temp-alert-' + Date.now();
                const messageDiv = $(
                    `<div class="alert ${alertClass} alert-dismissible fade show m-2" role="alert" id="${messageId}" style="position:fixed; top: 60px; right: 20px; z-index: 1050; min-width: 250px; max-width: 400px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
             </div>`
                );
                const container = $('#main-wrapper').length ? $('#main-wrapper') : $('body');
                container.prepend(messageDiv);
                setTimeout(function() {
                    $('#' + messageId).fadeOut(500, function() {
                        $(this).remove();
                    });
                }, duration);
            }

            // ++++ AJAX CHO NÚT "XEM CHI TIẾT ĐƠN HÀNG" ++++
            $('#orders-pane').on('click', '.view-order-details-btn', function() {
                const button = $(this);
                const orderId = button.data('orderId');
                const detailRow = $('#order-detail-row-' + orderId);
                const detailContentDiv = $('#order-detail-content-' + orderId);

                // Đóng các chi tiết đơn hàng khác đang mở
                $('.order-detail-row').not(detailRow).slideUp();
                $('.view-order-details-btn').not(button).html('<i class="fas fa-eye"></i>').attr('title',
                    'Xem chi tiết đơn hàng');

                if (detailRow.is(':visible')) {
                    detailRow.slideUp();
                    button.html('<i class="fas fa-eye"></i>').attr('title', 'Xem chi tiết đơn hàng');
                } else {
                    detailContentDiv.html(
                        '<p class="text-center my-3"><i class="fas fa-spinner fa-spin"></i> Đang tải chi tiết đơn hàng...</p>'
                    );
                    detailRow.slideDown();

                    // Đảm bảo tên route này đúng và đã được định nghĩa trong web.php
                    // Ví dụ: 'admin.account.order.ajaxDetails'
                    let fetchOrderDetailUrl =
                        "{{ route('admin.account.order.ajaxDetails', ['order' => ':orderId']) }}";
                    fetchOrderDetailUrl = fetchOrderDetailUrl.replace(':orderId', orderId);

                    console.log('Fetching order detail from:', fetchOrderDetailUrl);

                    $.ajax({
                        url: fetchOrderDetailUrl,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success && response.order) {
                                const order = response.order;
                                const items = response.order_items;
                                const shipping = response.shipping_address;
                                const customer = response.customer;

                                let html = `<div class="p-md-3 p-2">`; // Thêm padding
                                html += `<div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Chi tiết Đơn Hàng #${order.sku}</h5>
                                    <button type="button" class="btn-close" aria-label="Đóng chi tiết" onclick="$(this).closest('.order-detail-row').slideUp(); $('.view-order-details-btn[data-order-id=${order.id}]').html('<i class=\\'fas fa-eye\\'></i>').attr('title', 'Xem chi tiết đơn hàng');"></button>
                                 </div>`;

                                html +=
                                    '<div class="card mb-3"><div class="card-body py-2 px-3"><div class="row">';
                                html +=
                                    `<div class="col-md-6 mb-2 mb-md-0"><small><strong>Ngày đặt:</strong> ${order.created_at_formatted}</small></div>`;
                                html +=
                                    `<div class="col-md-6"><small><strong>Trạng thái:</strong> <span class="badge ${order.status_color_class || 'bg-secondary'}">${order.status_name}</span></small></div>`;
                                html +=
                                    `<div class="col-md-6 mb-2 mb-md-0"><small><strong>PT Thanh toán:</strong> ${order.payment_method}</small></div>`;
                                html +=
                                    `<div class="col-md-6"><small><strong>TT Thanh toán:</strong> <span class="badge ${order.payment_status_class}">${order.payment_status_display}</span></small></div>`;
                                html += '</div></div></div>'; // End row, card-body, card

                                html += '<div class="row mb-3">';
                                html +=
                                    '<div class="col-md-6 mb-3 mb-md-0"><div class="card h-100"><div class="card-body py-2 px-3">';
                                html +=
                                    '<h6><i class="fas fa-user me-2"></i>Thông tin người đặt</h6>';
                                html +=
                                    `<p class="mb-1 small"><strong>Tên:</strong> ${customer.name}</p>`;
                                html +=
                                    `<p class="mb-0 small"><strong>Email:</strong> ${customer.email}</p>`;
                                html += '</div></div></div>';

                                html +=
                                    '<div class="col-md-6"><div class="card h-100"><div class="card-body py-2 px-3">';
                                html +=
                                    '<h6><i class="fas fa-shipping-fast me-2"></i>Thông tin giao hàng</h6>';
                                if (shipping) {
                                    html +=
                                        `<p class="mb-1 small"><strong>Người nhận:</strong> ${shipping.name}</p>`;
                                    html +=
                                        `<p class="mb-1 small"><strong>Điện thoại:</strong> ${shipping.phone}</p>`;
                                    let fullAddress = shipping.address_line1;
                                    if (shipping.ward) fullAddress += ', ' + shipping.ward;
                                    if (shipping.district) fullAddress += ', ' + shipping
                                        .district;
                                    if (shipping.city) fullAddress += ', ' + shipping.city;
                                    html +=
                                        `<p class="mb-0 small"><strong>Địa chỉ:</strong> ${fullAddress}</p>`;
                                } else {
                                    html +=
                                        '<p class="small text-muted">Không có thông tin giao hàng chi tiết.</p>';
                                }
                                html += '</div></div></div>';
                                html += '</div>'; // End row for customer & shipping

                                html +=
                                    '<h6><i class="fas fa-boxes me-2"></i>Sản phẩm trong đơn:</h6>';
                                if (items && items.length > 0) {
                                    html +=
                                        '<div class="table-responsive"><table class="table table-sm table-bordered table-striped mt-2">';
                                    html +=
                                        '<thead class="table-light"><tr><th style="width:60px;">Ảnh</th><th>Sản phẩm</th><th>SL</th><th>Đơn giá</th><th>Thành tiền</th></tr></thead><tbody>';
                                    items.forEach(function(item) {
                                        html += `<tr>
                                            <td><img src="${item.image_url}" alt="" style="width:50px; height:50px; object-fit:cover;" class="rounded"></td>
                                            <td class="small">${item.product_name}</td>
                                            <td class="small text-center">${item.quantity}</td>
                                            <td class="small text-end">${item.unit_price}</td>
                                            <td class="small text-end">${item.sub_total}</td>
                                         </tr>`;
                                    });
                                    html += '</tbody></table></div>';
                                } else {
                                    html +=
                                        '<p class="small text-muted">Đơn hàng không có sản phẩm.</p>';
                                }

                                html += '<div class="row justify-content-end mt-3">';
                                html += '<div class="col-md-6 col-lg-5">';
                                html +=
                                    '<table class="table table-sm table-borderless table-striped">'; // Thêm table-striped
                                html +=
                                    `<tr><td class="small">Phí vận chuyển:</td><td class="text-end small">${order.shipping_fee}</td></tr>`;
                                if (parseFloat((order.discount_amount || "0 VNĐ").replace(/\D/g,
                                        '')) > 0) {
                                    html +=
                                        `<tr><td class="small">Giảm giá:</td><td class="text-end small text-danger">-${order.discount_amount}</td></tr>`;
                                }
                                html +=
                                    `<tr><td class="fw-bold small">TỔNG CỘNG:</td><td class="text-end fw-bold small">${order.total_amount}</td></tr>`;
                                html += '</table>';
                                html += '</div></div>';

                                if (order.notes) {
                                    html += '<h6 class="mt-3">Ghi chú đơn hàng:</h6>';
                                    html +=
                                        `<p class="small border p-2 bg-white rounded"><em>${order.notes || 'Không có ghi chú.'}</em></p>`;
                                }

                                html += `</div>`; // end p-2
                                detailContentDiv.html(html);
                                button.html('<i class="fas fa-eye-slash"></i>').attr('title',
                                    'Ẩn chi tiết đơn hàng');
                            } else {
                                detailContentDiv.html(
                                    '<p class="text-danger small">Không thể tải chi tiết đơn hàng hoặc đơn hàng không tồn tại.</p>'
                                );
                                button.html('<i class="fas fa-eye"></i>').attr('title',
                                    'Xem chi tiết đơn hàng');
                            }
                        },
                        error: function(xhr) {
                            console.error("Lỗi AJAX khi lấy chi tiết đơn hàng:", xhr
                                .responseText);
                            detailContentDiv.html(
                                '<p class="text-danger small">Lỗi khi tải chi tiết đơn hàng. Vui lòng thử lại.</p>'
                            );
                            button.html('<i class="fas fa-eye"></i>').attr('title',
                                'Xem chi tiết đơn hàng');
                            setTimeout(function() {
                                detailRow.slideUp();
                            }, 3000);
                        }
                    });
                }
            });

            // --- AJAX CHO NÚT "THÙNG RÁC BÌNH LUẬN" ---
            $('#toggleTrashedCommentsBtn').on('click', function() {
                const buttonSelf = $(this);
                const userId = buttonSelf.data('userId');
                const trashedSection = $('#trashedCommentsSection');
                const trashedContainer = $('#trashedCommentsContainer');

                // Sử dụng tên route đã được xác nhận từ file routes/web.php của bạn
                // Ví dụ: admin.account.comment.account.trashedComments (nếu có group admin)
                // Hoặc admin.account.comment.fetchUserTrashed (nếu bạn đã đổi tên)
                // Hoặc URL hardcoded nếu bạn chắc chắn
                let urlFetchTrashedComments =
                    "{{ route('admin.account.comment.account.trashedComments', ['user' => ':userId']) }}";
                urlFetchTrashedComments = urlFetchTrashedComments.replace(':userId', userId);
                // const urlFetchTrashedComments = `/admin/account/comment/users/${userId}/comments/trashed`;

                console.log('Fetch trashed URL:', urlFetchTrashedComments);

                if (trashedSection.is(':visible')) {
                    trashedSection.slideUp();
                    buttonSelf.html('<i class="fas fa-trash"></i> Thùng rác bình luận');
                    buttonSelf.removeClass('btn-danger').addClass('btn-outline-danger');
                } else {
                    trashedContainer.html(
                        '<p class="text-center my-3"><i class="fas fa-spinner fa-spin"></i> Đang tải...</p>'
                    );
                    trashedSection.slideDown();
                    $.ajax({
                        url: urlFetchTrashedComments,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            let html = '';
                            if (response.comments && response.comments.length > 0) {
                                html +=
                                    '<table class="table table-sm table-hover table-bordered">';
                                html +=
                                    `<thead class="table-light"><tr><th>ID</th><th>Người gửi</th><th>Nội dung</th><th>Ngày xóa</th><th style="width: 10%;">Hành động</th></tr></thead><tbody>`;
                                response.comments.forEach(function(comment) {
                                    html += `<tr id="trashed-comment-row-${comment.id}">
                                        <td>${comment.id}</td>
                                        <td>${comment.user_name || 'N/A'}</td>
                                        <td>${comment.content}</td>
                                        <td>${comment.deleted_at}</td>
                                        <td>
                                            <form action="${comment.restore_url}" method="POST" class="d-inline restore-comment-form">
                                                <input type="hidden" name="_token" value="${csrfTokenGlobal}">
                                                <button type="button" class="btn btn-xs btn-success restore-comment-btn" title="Khôi phục" data-comment-id="${comment.id}" data-bs-toggle="tooltip"><i class="fas fa-trash-restore"></i></button>
                                            </form>
                                        </td></tr>`;
                                });
                                html += '</tbody></table>';
                            } else {
                                html =
                                    '<p class="text-center text-muted">Không có bình luận nào trong thùng rác.</p>';
                            }
                            trashedContainer.html(html);
                            $('#trashedCommentsContainer [data-bs-toggle="tooltip"]').tooltip(
                                'dispose').tooltip();
                            buttonSelf.html('<i class="fas fa-eye-slash"></i> Ẩn thùng rác');
                            buttonSelf.removeClass('btn-outline-danger').addClass('btn-danger');
                        },
                        error: function(xhr) {
                            console.error("Lỗi khi lấy thùng rác bình luận:", xhr.responseText);
                            trashedContainer.html(
                                '<p class="text-center text-danger">Lỗi tải thùng rác. Vui lòng thử lại.</p>'
                            );
                            buttonSelf.html('<i class="fas fa-trash"></i> Thùng rác bình luận')
                                .removeClass('btn-danger').addClass('btn-outline-danger');
                        }
                    });
                }
            });

            // --- AJAX CHO NÚT "XEM CHI TIẾT BÌNH LUẬN" ---
            $('#activeCommentsContainer').on('click', '.view-comment-details-btn', function() {
                const button = $(this);
                const commentId = button.data('commentId');
                const detailRow = $('#comment-detail-row-' + commentId);
                const detailContentDiv = $('#comment-detail-content-' + commentId);

                $('.comment-detail-row').not(detailRow).slideUp();
                $('.view-comment-details-btn').not(button).html('<i class="fas fa-eye"></i>').attr('title',
                    'Xem chi tiết bình luận');

                if (detailRow.is(':visible')) {
                    detailRow.slideUp();
                    button.html('<i class="fas fa-eye"></i>').attr('title', 'Xem chi tiết bình luận');
                } else {
                    detailContentDiv.html(
                        '<p class="text-center my-3"><i class="fas fa-spinner fa-spin"></i> Đang tải chi tiết...</p>'
                    );
                    detailRow.slideDown();
                    // Route name: admin.account.comment.detailWithProduct
                    let fetchDetailUrl =
                        "{{ route('admin.account.comment.detailWithProduct', ['comment' => ':commentId']) }}";
                    fetchDetailUrl = fetchDetailUrl.replace(':commentId', commentId);
                    console.log('Fetching comment detail from:', fetchDetailUrl);
                    $.ajax({
                        url: fetchDetailUrl,
                        type: 'GET',
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                let html = '<div class="container-fluid"><div class="row">';
                                html += '<div class="col-md-12 mb-3">';
                                html +=
                                    '<h6><i class="fas fa-comment-dots text-primary me-2"></i>Chi tiết Bình luận:</h6>';
                                html +=
                                    `<p class="mb-1"><small><strong>Người gửi:</strong> ${response.comment.user_name}</small></p>`;
                                html +=
                                    `<p class="mb-1"><small><strong>Ngày gửi:</strong> ${response.comment.created_at_formatted}</small></p>`;
                                html +=
                                    `<div class="comment-full-content border p-2 bg-light rounded small" style="white-space: pre-wrap;">${response.comment.content_full}</div>`;
                                html += '</div>';
                                if (response.product) {
                                    const productNameDisplay = response.product.name;
                                    html += '<div class="col-md-12">';
                                    html +=
                                        '<h6><i class="fas fa-box-open text-success me-2"></i>Sản phẩm được bình luận:</h6>';
                                    html += '<div class="d-flex align-items-center">';
                                    html +=
                                        `<img src="${response.product.image_url}" alt="${response.product.name}" class="img-thumbnail me-3" style="width: 70px; height: 70px; object-fit: cover;">`;
                                    html +=
                                        `<div><p class="mb-1"><strong>Tên Sản phẩm:</strong> ${productNameDisplay}</p></div></div></div>`;
                                } else {
                                    html +=
                                        '<div class="col-md-12"><p class="text-muted small">Không tìm thấy thông tin sản phẩm.</p></div>';
                                }
                                html += '</div></div>';
                                detailContentDiv.html(html);
                                button.html('<i class="fas fa-eye-slash"></i>').attr('title',
                                    'Ẩn chi tiết');
                            } else {
                                detailContentDiv.html(
                                    '<p class="text-danger small">Không thể tải chi tiết.</p>'
                                );
                                button.html('<i class="fas fa-eye"></i>').attr('title',
                                    'Xem chi tiết');
                            }
                        },
                        error: function(xhr) {
                            /* ... */
                        }
                    });
                }
            });

            // ---- XỬ LÝ CHO CÁC NÚT THAY ĐỔI TRẠNG THÁI ----
            $('#activeCommentsContainer').on('click', '.change-comment-status-btn', function() {
                const button = $(this);
                const commentId = button.data('commentId');
                const actionType = button.data('action');

                let actionUrl = '';
                let buttonTitleText = button.data('original-title') || button.attr('title') ||
                    "thực hiện hành động";

                if (typeof buttonTitleText === 'undefined' || buttonTitleText === null || buttonTitleText
                    .trim() === '') {
                    buttonTitleText = "thực hiện hành động này";
                }

                // Xác định URL dựa trên actionType và tên route tương ứng
                if (actionType === 'approve') {
                    actionUrl =
                        "{{ route('admin.account.comment.approveComment', ['comment' => ':commentId']) }}"
                        .replace(':commentId', commentId);
                } else if (actionType === 'hide') {
                    actionUrl =
                        "{{ route('admin.account.comment.hideComment', ['comment' => ':commentId']) }}"
                        .replace(':commentId', commentId);
                } else if (actionType === 'show_again') {
                    actionUrl =
                        "{{ route('admin.account.comment.showAgainComment', ['comment' => ':commentId']) }}"
                        .replace(':commentId', commentId);
                } else {
                    console.error('Hành động không xác định cho change-status:', actionType);
                    showTemporaryMessage('Hành động không hợp lệ.', 'error');
                    return;
                }

                let confirmMessage = `Bạn có chắc muốn ${buttonTitleText.toLowerCase()}?`;
                if (!confirm(confirmMessage)) return;

                const originalActionsCellHtml = button.closest('.comment-actions-cell').html();
                button.closest('.comment-actions-cell').html(
                    '<i class="fas fa-spinner fa-spin text-primary"></i>');

                $.ajax({
                    url: actionUrl,
                    type: 'POST',
                    data: {
                        _token: csrfTokenGlobal
                    }, // action_type đã được xác định qua URL route
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // response.new_actions_html phải chứa các nút mới với đúng class và data-action
                            updateActiveCommentUIAfterAction(response.comment_id, response
                                .new_status_text, response.new_status_class_badge, response
                                .new_actions_html);
                            showTemporaryMessage(response.message, 'success');
                        } else {
                            showTemporaryMessage(response.message || 'Hành động thất bại.',
                                'error');
                            $('#comment-actions-cell-' + commentId).html(
                                originalActionsCellHtml);
                            $(`#comment-actions-cell-${commentId} [data-bs-toggle="tooltip"]`)
                                .tooltip('dispose').tooltip();
                        }
                    },
                    error: function(xhr) {
                        console.error("Lỗi AJAX khi thay đổi trạng thái:", xhr.responseText);
                        showTemporaryMessage('Lỗi hệ thống khi thay đổi trạng thái.', 'error');
                        $('#comment-actions-cell-' + commentId).html(originalActionsCellHtml);
                        $(`#comment-actions-cell-${commentId} [data-bs-toggle="tooltip"]`)
                            .tooltip('dispose').tooltip();
                    }
                });
            });

            // ---- AJAX CHO XÓA MỀM BÌNH LUẬN ----
            $('#activeCommentsContainer').on('click', '.soft-delete-comment-btn', function() {
                const button = $(this);
                const commentId = button.data('commentId');
                // Route name: admin.account.comment.softDeleteComment (nếu route dùng {comment})
                // hoặc admin.account.comment.softDelete (nếu route dùng {id})
                let deleteUrl =
                    "{{ route('admin.account.comment.softDeleteComment', ['comment' => ':commentId']) }}";
                deleteUrl = deleteUrl.replace(':commentId', commentId);

                if (!confirm('Bạn có chắc muốn chuyển bình luận này vào thùng rác?')) return;

                const originalActionsCellHtml = button.closest('.comment-actions-cell').html();
                button.closest('.comment-actions-cell').html(
                    '<i class="fas fa-spinner fa-spin text-danger"></i>');

                $.ajax({
                    url: deleteUrl,
                    type: 'POST', // Hoặc DELETE nếu route là DELETE và bạn gửi _method
                    data: {
                        _token: csrfTokenGlobal
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#active-comment-row-' + response.comment_id).fadeOut(500,
                                function() {
                                    $(this).remove();
                                });
                            $('#comment-detail-row-' + response.comment_id).fadeOut(500,
                                function() {
                                    $(this).remove();
                                });
                            showTemporaryMessage(response.message, 'info');
                            if (response.new_total_comment_count !== undefined) {
                                updateTotalCommentsBadgeOnTab(response.new_total_comment_count);
                            }
                            if ($('#trashedCommentsSection').is(':visible')) {
                                $('#toggleTrashedCommentsBtn').click(); // Đóng
                                setTimeout(function() {
                                    $('#toggleTrashedCommentsBtn').click();
                                }, 250); // Mở lại để tải mới
                            }
                        } else {
                            showTemporaryMessage(response.message || 'Lỗi khi xóa.', 'error');
                            $('#comment-actions-cell-' + commentId).html(
                                originalActionsCellHtml);
                            $(`#comment-actions-cell-${commentId} [data-bs-toggle="tooltip"]`)
                                .tooltip('dispose').tooltip();
                        }
                    },
                    error: function(xhr) {
                        showTemporaryMessage('Lỗi hệ thống khi xóa bình luận.', 'error');
                        $('#comment-actions-cell-' + commentId).html(originalActionsCellHtml);
                        $(`#comment-actions-cell-${commentId} [data-bs-toggle="tooltip"]`)
                            .tooltip('dispose').tooltip();
                    }
                });
            });

            // ---- AJAX CHO KHÔI PHỤC BÌNH LUẬN TỪ THÙNG RÁC ----
            $('#trashedCommentsContainer').on('click', '.restore-comment-btn', function() {
                const button = $(this);
                const commentId = button.data('commentId');
                const form = button.closest('form');
                const restoreUrl = form.attr('action');

                if (!confirm('Bạn có chắc muốn khôi phục bình luận này?')) return;

                const originalButtonHtml = button.html();
                button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

                $.ajax({
                    url: restoreUrl,
                    type: 'POST',
                    data: form.serialize(),
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#trashed-comment-row-' + response.comment_id).fadeOut(500,
                                function() {
                                    $(this).remove();
                                });
                            showTemporaryMessage(response.message, 'success');

                            if (response.new_total_comment_count !== undefined) {
                                updateTotalCommentsBadgeOnTab(response.new_total_comment_count);
                            }

                            if (response
                                .restored_comment_html
                            ) { // Nếu controller trả về HTML render sẵn
                                $('#activeCommentsContainer table tbody').prepend(response
                                    .restored_comment_html);
                                $('#activeCommentsContainer [data-bs-toggle="tooltip"]')
                                    .tooltip('dispose').tooltip();
                            }

                            if ($('#trashedCommentsContainer table tbody tr').length === 0) {
                                $('#trashedCommentsContainer').html(
                                    '<p class="text-center text-muted">Không có bình luận nào trong thùng rác.</p>'
                                );
                            }
                        } else {
                            showTemporaryMessage(response.message || 'Lỗi khi khôi phục.',
                                'error');
                        }
                    },
                    error: function(xhr) {
                        console.error("Lỗi AJAX khi khôi phục:", xhr.responseText);
                        showTemporaryMessage('Lỗi hệ thống khi khôi phục.', 'error');
                    },
                    complete: function() {
                        button.prop('disabled', false).html(originalButtonHtml);
                    }
                });
            });

            // Khởi tạo tooltip cho các nút ban đầu
            $('[data-bs-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush
