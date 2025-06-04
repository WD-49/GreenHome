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
                <i class="fas fa-user-circle me-2"></i>Chi tiết tài khoản: {{ $user->name }}
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
                        <img src="{{ $user->profile && $user->profile->user_image ? asset('storage/' . $user->profile->user_image) : 'https://cdn2.iconfinder.com/data/icons/audio-16/96/user_avatar_profile_login_button_account_member-512.png' }}"
                            alt="Ảnh đại diện" class="img-fluid rounded-circle mb-3 shadow"
                            style="width: 150px; height: 150px; object-fit: cover;">

                        <h4 class="card-title mb-1">{{ $user->name }}</h4>
                        <p class="text-muted mb-2">{{ $user->email }}</p>
                        <p class="mb-1">
                            <strong>Vai trò:</strong>
                            @if ($user->role == 'admin' || $user->role == 'superadmin')
                                <span class="badge bg-success">{{ ucfirst($user->role) }}</span>
                            @else
                                <span class="badge bg-info text-dark">{{ ucfirst($user->role) }}</span>
                            @endif
                        </p>
                        <p class="mb-3">
                            <strong>Trạng thái:</strong>
                            @if ($user->status == 1)
                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Hoạt động</span>
                            @else
                                <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Ngừng hoạt động</span>
                            @endif
                        </p>
                        <hr>
                        @if ($user->profile)
                            <div class="text-start">
                                <p class="mb-2"><strong><i class="fas fa-phone me-2 text-primary"></i>SĐT:</strong>
                                    {{ $user->profile->phone ?: 'Chưa cập nhật' }}</p>
                                <p class="mb-2"><strong><i class="fas fa-map-marker-alt me-2 text-primary"></i>Địa
                                        chỉ:</strong> {{ $user->profile->address ?: 'Chưa cập nhật' }}</p>
                                <p class="mb-0"><strong><i class="fas fa-venus-mars me-2 text-primary"></i>Giới
                                        tính:</strong>
                                    @if ($user->profile->gender == 'male' || $user->profile->gender == 'nam')
                                        Nam
                                    @elseif($user->profile->gender == 'female' || $user->profile->gender == 'nu')
                                        Nữ
                                    @else
                                        {{ ucfirst($user->profile->gender ?: 'Khác') }}
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
                                    @if (isset($user->orders_count) && $user->orders_count > 0)
                                        <span class="badge rounded-pill bg-info ms-1">{{ $user->orders_count }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cart-tab" data-bs-toggle="tab" data-bs-target="#cart-pane"
                                    type="button" role="tab" aria-controls="cart-pane" aria-selected="false">
                                    <i class="fas fa-shopping-cart me-1"></i> Giỏ hàng Hiện tại
                                    @if (isset($user->cart_items_count) && $user->cart_items_count > 0)
                                        <span
                                            class="badge rounded-pill bg-warning text-dark ms-1">{{ $user->cart_items_count }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="comments-tab" data-bs-toggle="tab"
                                    data-bs-target="#comments-pane" type="button" role="tab"
                                    aria-controls="comments-pane" aria-selected="false">
                                    <i class="fas fa-comments me-1"></i> Bình luận
                                    @php $totalCommentsCount = $user->comments->count(); @endphp
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
                                @if ($user->orders && $user->orders->count() > 0)
                                    {{-- ... (Bảng đơn hàng của bạn giữ nguyên) ... --}}
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped table-sm">
                                            {{-- ... thead ... --}}
                                            <tbody>
                                                @foreach ($user->orders as $order)
                                                    <tr>
                                                        <td>#{{ $order->sku ?? $order->id }}</td>
                                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                                        <td>{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</td>
                                                        <td>
                                                            @if (
                                                                $order->status == 'completed' ||
                                                                    optional($order->status)->name == 'Hoàn thành' ||
                                                                    optional($order->status)->name == 'Đã giao hàng')
                                                                <span class="badge bg-success">Hoàn thành</span>
                                                            @elseif(
                                                                $order->status == 'pending' ||
                                                                    optional($order->status)->name == 'Chờ xử lý' ||
                                                                    optional($order->status)->name == 'Chờ xác nhận')
                                                                <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                                            @elseif($order->status == 'processing' || optional($order->status)->name == 'Đang xử lý')
                                                                <span class="badge bg-info">Đang xử lý</span>
                                                            @elseif($order->status == 'cancelled' || optional($order->status)->name == 'Đã hủy')
                                                                <span class="badge bg-danger">Đã hủy</span>
                                                            @else
                                                                <span
                                                                    class="badge bg-secondary">{{ is_object($order->status) ? $order->status->name : ucfirst($order->status ?? 'N/A') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-outline-primary btn-sm"
                                                                onclick="alert('Chức năng xem chi tiết đơn hàng #{{ $order->id }} chưa được triển khai.')"
                                                                title="Xem chi tiết">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
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
                                @if ($user->cartItems && $user->cartItems->count() > 0)
                                    {{-- ... (Bảng giỏ hàng của bạn giữ nguyên) ... --}}
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm align-middle">
                                            {{-- ... thead ... --}}
                                            <tbody>
                                                @php $cartTotal = 0; @endphp
                                                @foreach ($user->cartItems as $item)
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
                                aria-labelledby="comments-tab"> {{-- Sửa ID và labelledby cho khớp --}}
                                {{-- <h1>NỘI DUNG TAB BÌNH LUẬN</h1> --}}
                                {{-- Nội dung thật của tab bình luận --}}
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Danh sách Bình luận</h5>
                                    <button id="toggleTrashedCommentsBtn" class="btn btn-sm btn-outline-danger"
                                        data-user-id="{{ $user->id }}"
                                        data-fetch-url="{{ route('admin.account.comment.account.trashedComments', ['user' => $user->id]) }}">
                                        <i class="fas fa-trash"></i> Thùng rác bình luận
                                    </button>
                                </div>
                                <div id="activeCommentsContainer">
                                    @php
                                        $activeComments = $user->comments->filter(function ($comment) {
                                            return is_null($comment->deleted_at);
                                        });
                                    @endphp
                                    @if ($activeComments->count() > 0)
                                        {{-- ... (Bảng bình luận active của bạn giữ nguyên) ... --}}
                                        <table class="table table-sm table-bordered table-hover">
                                            {{-- ... thead ... --}}
                                            <tbody>
                                                @foreach ($activeComments as $comment)
                                                    <tr>
                                                        <td>{{ $comment->id }}</td>
                                                        <td>{{ Str::limit($comment->content, 100) }}</td>
                                                        <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                                                        <td>
                                                            <span class="badge bg-success">{{ $comment->status }}</span>
                                                        </td>
                                                        <td>
                                                            <button
                                                                class="btn btn-xs btn-outline-info view-comment-details-btn me-1"
                                                                title="Xem chi tiết bình luận"
                                                                data-comment-id="{{ $comment->id }}"
                                                                data-bs-toggle="tooltip" data-bs-placement="top">
                                                                {{-- Thêm data-bs-toggle cho tooltip nếu muốn --}}
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            {{-- Các nút ẩn/hiện và xóa tạm thời khác (nếu có) --}}
                                                            {{-- Ví dụ:
                <button class="btn btn-xs btn-outline-warning toggle-comment-status-btn" data-comment-id="{{ $comment->id }}"><i class="fas fa-toggle-on"></i></button>
                <button class="btn btn-xs btn-outline-danger soft-delete-comment-btn" data-comment-id="{{ $comment->id }}"><i class="fas fa-trash-alt"></i></button>
                --}}
                                                        </td>
                                                    </tr>
                                                    {{-- Hàng ẩn để hiển thị chi tiết bình luận (sẽ được AJAX tải vào) --}}
                                                    <tr class="comment-detail-row"
                                                        id="comment-detail-row-{{ $comment->id }}"
                                                        style="display: none;">
                                                        <td colspan="5"> {{-- Colspan bằng số lượng cột của bảng --}}
                                                            <div class="comment-detail-content p-2 border-top"
                                                                id="comment-detail-content-{{ $comment->id }}">
                                                                {{-- Nội dung chi tiết sẽ được tải vào đây --}}
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
    {{-- Đẩy script của trang này vào stack 'scripts' của layout --}}
    <script>
        $(document).ready(function() {
            // Script AJAX cho thùng rác bình luận (giữ nguyên như bạn đã sửa)
            $('#toggleTrashedCommentsBtn').on('click', function() {
                const userId = $(this).data('userId');
                const trashedSection = $('#trashedCommentsSection');
                const trashedContainer = $('#trashedCommentsContainer');
                const button = $(this);
                const csrfToken = $('meta[name="csrf-token"]').attr('content');
                const urlFetchTrashedComments =
                    `/admin/account/comment/users/${userId}/comments/trashed`; // URL này khớp với route của bạn

                if (trashedSection.is(':visible')) {
                    trashedSection.slideUp();
                    button.html('<i class="fas fa-trash"></i> Thùng rác bình luận');
                    button.removeClass('btn-danger').addClass('btn-outline-danger');
                } else {
                    trashedContainer.html(
                        '<p class="text-center"><i class="fas fa-spinner fa-spin"></i> Đang tải...</p>');
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
                                    `<thead class="table-light"><tr><th>ID</th><th>Nội dung</th><th>Ngày xóa</th><th>Hành động</th></tr></thead><tbody>`;
                                response.comments.forEach(function(comment) {
                                    html += `<tr>
                                        <td>${comment.id}</td>
                                        <td>${comment.content}</td>
                                        <td>${comment.deleted_at}</td>
                                        <td>
                                            <form action="${comment.restore_url}" method="POST" style="display:inline-block;" onsubmit="return confirm('Khôi phục bình luận này?')">
                                                <input type="hidden" name="_token" value="${csrfToken}">
                                                <button type="submit" class="btn btn-xs btn-success" title="Khôi phục"><i class="fas fa-trash-restore"></i></button>
                                            </form>
                                            <form action="${comment.force_delete_url}" method="POST" style="display:inline-block;" onsubmit="return confirm('Xóa vĩnh viễn bình luận này?')">
                                                <input type="hidden" name="_token" value="${csrfToken}">
                                                <input type="hidden" name="_method" value="DELETE">
                                                <button type="submit" class="btn btn-xs btn-danger" title="Xóa vĩnh viễn"><i class="fas fa-times-circle"></i></button>
                                            </form>
                                        </td></tr>`;
                                });
                                html += '</tbody></table>';
                            } else {
                                html =
                                    '<p class="text-center text-muted">Không có bình luận nào trong thùng rác.</p>';
                            }
                            trashedContainer.html(html);
                            button.html('<i class="fas fa-eye-slash"></i> Ẩn thùng rác');
                            button.removeClass('btn-outline-danger').addClass('btn-danger');
                        },
                        error: function(xhr) {
                            console.error("Lỗi khi lấy bình luận đã xóa:", xhr.responseText);
                            trashedContainer.html(
                                '<p class="text-center text-danger">Lỗi khi tải bình luận. Vui lòng thử lại.</p>'
                            );
                        }
                    });
                }
            });

            // Test thử xem tab có được Bootstrap khởi tạo không
            var firstTabEl = document.querySelector('#userTabs button[data-bs-toggle="tab"]')
            if (firstTabEl) {
                var tab = new bootstrap.Tab(firstTabEl) // Chỉ để test, không nhất thiết phải .show()
                console.log('Bootstrap Tab instance created for the first tab trigger:', tab);
            } else {
                console.error('Could not find any tab triggers to test Bootstrap Tab initialization.');
            }
        });
    </script>
@endpush
