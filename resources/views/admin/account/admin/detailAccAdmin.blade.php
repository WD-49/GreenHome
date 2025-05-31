@extends('layouts.admin')

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
                                <span class="badge bg-info">{{ ucfirst($admins->role) }}</span>
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

            {{-- Cột chứa Tabs Đơn hàng, Giỏ hàng, Bình luận --}}
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header p-0">
                        <ul class="nav nav-tabs nav-fill" id="userTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="orders-tab" data-bs-toggle="tab"
                                    data-bs-target="#orders" type="button" role="tab" aria-controls="orders"
                                    aria-selected="true">
                                    <i class="fas fa-receipt me-1"></i> Đơn hàng
                                    {{-- Giả sử $admins->orders_count được truyền từ controller --}}
                                    @if (isset($admins->orders_count) && $admins->orders_count > 0)
                                        <span class="badge rounded-pill bg-info ms-1">{{ $admins->orders_count }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cart-tab" data-bs-toggle="tab" data-bs-target="#cart"
                                    type="button" role="tab" aria-controls="cart" aria-selected="false">
                                    <i class="fas fa-shopping-cart me-1"></i> Giỏ hàng Hiện tại
                                    {{-- Giả sử $admins->cart_items_count được truyền từ controller --}}
                                    @if (isset($admins->cart_items_count) && $admins->cart_items_count > 0)
                                        <span
                                            class="badge rounded-pill bg-warning text-dark ms-1">{{ $admins->cart_items_count }}</span>
                                    @endif
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="comments-tab" data-bs-toggle="tab" data-bs-target="#comments"
                                    type="button" role="tab" aria-controls="comments" aria-selected="false">
                                    <i class="fas fa-comments me-1"></i> Bình luận
                                    @if ($admins->comments()->withTrashed()->count() > 0)
                                        <span
                                            class="badge rounded-pill bg-secondary ms-1">{{ $admins->comments()->withTrashed()->count() }}</span>
                                    @endif
                                </button>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="userTabsContent">
                            {{-- Tab Đơn hàng --}}
                            <div class="tab-pane fade show active" id="orders" role="tabpanel"
                                aria-labelledby="orders-tab">
                                <h5 class="mb-3">Danh sách Đơn hàng</h5>
                                @if ($admins->orders && $admins->orders->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Mã ĐH</th>
                                                    <th>Ngày đặt</th>
                                                    <th>Tổng tiền</th>
                                                    <th>Trạng thái</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($admins->orders as $order)
                                                    <tr>
                                                        <td>#{{ $order->id }}</td>
                                                        <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                                        <td>{{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</td>
                                                        <td>
                                                            @if ($order->status == 'completed')
                                                                <span class="badge bg-success">Hoàn thành</span>
                                                            @elseif($order->status == 'pending')
                                                                <span class="badge bg-warning text-dark">Chờ xử lý</span>
                                                            @elseif($order->status == 'processing')
                                                                <span class="badge bg-info">Đang xử lý</span>
                                                            @elseif($order->status == 'cancelled')
                                                                <span class="badge bg-danger">Đã hủy</span>
                                                            @else
                                                                <span
                                                                    class="badge bg-secondary">{{ $order->status->name }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            {{-- <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-info btn-sm" title="Xem chi tiết"><i class="fas fa-eye"></i></a> --}}
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
                                    {{-- {{ $admins->orders->links() }} --}}
                                @else
                                    <div class="alert alert-light text-center" role="alert">
                                        <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                        Người dùng này chưa có đơn hàng nào.
                                    </div>
                                @endif
                            </div>

                            {{-- Tab Giỏ hàng --}}
                            <div class="tab-pane fade" id="cart" role="tabpanel" aria-labelledby="cart-tab">
                                <h5 class="mb-3">Sản phẩm trong Giỏ hàng</h5>
                                {{-- Giả sử $admins->cartItems là một collection các sản phẩm trong giỏ --}}
                                @if ($admins->cartItems && $admins->cartItems->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 10%;">Ảnh</th>
                                                    <th>Tên sản phẩm</th>
                                                    <th>Số lượng</th>
                                                    <th>Đơn giá</th>
                                                    <th>Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $cartTotal = 0; @endphp
                                                @foreach ($admins->cartItems as $item)
                                                    @php
                                                        // Giả sử $item có quan hệ 'product' và product có 'image_url'
                                                        // Giả sử $item có 'quantity' và 'price' (đơn giá tại thời điểm thêm vào giỏ)
                                                        $productImageUrl =
                                                            $item->productVariant->product->image ??
                                                            'https://placehold.co/60x60/EBF0F5/7F8EA3?text=Ảnh+SP';
                                                        $productName =
                                                            $item->productVariant->product->name ??
                                                            'Sản phẩm không xác định';
                                                        $quantity = $item->quantity ?? 1;
                                                        $price = $item->unit_price ?? 0;
                                                        $lineTotal = $quantity * $price;
                                                        $cartTotal += $lineTotal;
                                                    @endphp
                                                    <tr>
                                                        <td>
                                                            {{-- {{ asset('storage/' . $admins->profile->user_image) }}" --}}
                                                            <img src="{{ asset('storage/' . $productImageUrl) }}"
                                                                alt="{{ $productName }}" class="img-fluid rounded"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                            {{-- <img src="{{ $productImageUrl }}" alt="{{ $productName }}" class="img-fluid rounded" style="width: 50px; height: 50px; object-fit: cover;"> --}}
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
                            <div class="tab-pane fade" id="comments" role="tabpanel" aria-labelledby="comments-tab">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="mb-0">Danh sách Bình luận</h5>
                                    {{-- Nút Thùng rác bình luận có thể cần route khác nếu nó không phụ thuộc vào user ID hiện tại --}}
                                    <a href="{{ route('admin.account.comment.trashed') }}"
                                        class="btn btn-outline-danger btn-sm">
                                        <i class="fas fa-trash-alt me-1"></i> Thùng rác bình luận
                                    </a>
                                </div>

                                @if ($admins->comments()->withTrashed()->get()->isEmpty())
                                    <div class="alert alert-light text-center" role="alert">
                                        <i class="far fa-comment-dots fa-2x mb-2 d-block"></i>
                                        Người dùng này chưa có bình luận nào.
                                    </div>
                                @else
                                    <div class="table-responsive">
                                        <table class="table table-hover table-striped table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Nội dung</th>
                                                    <th>Ngày tạo</th>
                                                    <th>Trạng thái</th>
                                                    <th>Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($admins->comments()->withTrashed()->orderBy('created_at', 'desc')->get() as $comment)
                                                    <tr>
                                                        <td>{{ $comment->id }}</td>
                                                        <td>{{ Str::limit($comment->content, 70) }}</td>
                                                        <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                                                        <td>
                                                            @if ($comment->deleted_at)
                                                                <span class="badge bg-dark">Đã ẩn (trong thùng rác)</span>
                                                            @elseif ($comment->status == 1)
                                                                <span class="badge bg-success">Hiển thị</span>
                                                            @else
                                                                <span class="badge bg-warning text-dark">Bị ẩn</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="btn-group btn-group-sm" role="group">
                                                                <form
                                                                    action="{{ route('admin.account.comment.toggleStatus', $comment->id) }}"
                                                                    method="POST" class="d-inline">
                                                                    @csrf
                                                                    <button type="submit" class="btn btn-outline-info"
                                                                        title="Chuyển trạng thái">
                                                                        <i class="fas fa-sync-alt"></i>
                                                                    </button>
                                                                </form>
                                                                @if (!$comment->deleted_at)
                                                                    <form
                                                                        action="{{ route('admin.account.comment.softDelete', $comment->id) }}"
                                                                        method="POST" class="d-inline">
                                                                        @csrf
                                                                        <button type="submit"
                                                                            class="btn btn-outline-warning"
                                                                            title="Xóa mềm">
                                                                            <i class="fas fa-trash"></i>
                                                                        </button>
                                                                    </form>
                                                                @else
                                                                    {{-- Có thể thêm nút khôi phục ở đây nếu cần --}}
                                                                    <button class="btn btn-outline-secondary" disabled
                                                                        title="Đã trong thùng rác"><i
                                                                            class="fas fa-trash-restore-alt"></i></button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Đảm bảo bạn đã nhúng Font Awesome và Bootstrap JS (cho Tabs) trong layout chính --}}
    {{-- Ví dụ:
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
--}}
@endsection
