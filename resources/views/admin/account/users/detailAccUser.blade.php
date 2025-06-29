@extends('layouts.admin')
@section('title', 'Chi tiết người dùng')

@push('styles')
    <style>
        .avatar-xxl {
            width: 150px;
            height: 150px;
            object-fit: cover;
            border: 3px solid #e9ecef;
        }

        .table-sm th,
        .table-sm td {
            padding: 0.5rem;
        }

        .badge.bg-success {
            background-color: #28a745 !important;
        }

        .badge.bg-info.text-dark {
            background-color: #17a2b8 !important;
            color: #212529 !important;
        }

        .badge.bg-danger {
            background-color: #dc3545 !important;
        }

        .badge.bg-secondary {
            background-color: #6c757d !important;
        }

        .badge.bg-primary {
            background-color: #007bff !important;
        }

        .badge.bg-warning.text-dark {
            background-color: #ffc107 !important;
            color: #212529 !important;
        }

        .btn-xs {
            padding: .25rem .5rem;
            font-size: .75rem;
            line-height: 1.5;
            border-radius: .2rem;
        }
    </style>
@endpush

@section('content')

    <head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
            integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Tài khoản <b>{{ $user->name }}</b></h4>
            </div>


            <div class="ms-auto">
                <a href="{{ route('admin.account.listUsers') }}" class="btn btn-sm btn-outline-secondary shadow-sm">
                    <i class="fas fa-arrow-left fa-sm text-gray-700 me-1"></i> Quay lại danh sách
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->profile && $user->profile->user_image ? asset('storage/' . $user->profile->user_image) : 'https://cdn2.iconfinder.com/data/icons/audio-16/96/user_avatar_profile_login_button_account_member-512.png' }}"
                                    class="rounded-circle avatar-xxl img-thumbnail float-start" alt="image profile">

                                <div class="overflow-hidden ms-4">
                                    <h4 class="m-0 text-dark fs-20">{{ $user->name }}</h4>
                                    <p class="my-1 text-muted fs-16">{{ $user->email }}</p>

                                    <span class="fs-15">
                                        <i class="mdi mdi-account-group me-2 align-middle"></i>Vai trò:
                                        <span>
                                            @if ($user->role == 'admin' || $user->role == 'superadmin')
                                                <span class="badge bg-success">{{ ucfirst($user->role) }}</span>
                                            @else
                                                <span class="badge bg-info text-dark">{{ ucfirst($user->role) }}</span>
                                            @endif
                                            <span class="badge bg-primary-subtle text-primary px-2 py-1 fs-13 fw-normal">
                                                @if ($user->status == 1)
                                                    <span class="badge bg-success"><i
                                                            class="fas fa-check-circle me-1"></i>Hoạt động</span>
                                                @else
                                                    <span class="badge bg-danger"><i
                                                            class="fas fa-times-circle me-1"></i>Ngừng hoạt động</span>
                                                @endif
                                            </span>
                                        </span>
                                    </span>
                                    @if ($user->profile)
                                        <div class="mt-3 text-start">
                                            <p class="mb-1 small"><strong><i
                                                        class="fas fa-phone me-2 text-primary"></i>SĐT:</strong>
                                                {{ $user->profile->phone ?: 'Chưa cập nhật' }}</p>
                                            <p class="mb-1 small"><strong><i
                                                        class="fas fa-map-marker-alt me-2 text-primary"></i>Địa
                                                    chỉ:</strong>
                                                {{ $user->profile->address ?: 'Chưa cập nhật' }}</p>
                                            <p class="mb-0 small"><strong><i
                                                        class="fas fa-venus-mars me-2 text-primary"></i>Giới tính:</strong>
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
                                        <div class="alert alert-warning mt-3 small" role="alert">
                                            <i class="fas fa-exclamation-triangle me-1"></i> Người dùng chưa cập nhật thông
                                            tin hồ sơ.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- THAY ĐỔI CÁC ID VÀ HREF CỦA TAB NAVIGATION --}}
                        <ul class="nav nav-underline border-bottom pt-2" id="userProfileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active p-2" id="tab-info" data-bs-toggle="tab"
                                    data-bs-target="#pane-info" type="button" role="tab" aria-controls="pane-info"
                                    aria-selected="true">
                                    <span class="d-block d-sm-none"><i class="mdi mdi-information"></i></span>
                                    <span class="d-none d-sm-block">Thông tin</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link p-2" id="tab-orders" data-bs-toggle="tab" data-bs-target="#pane-orders"
                                    type="button" role="tab" aria-controls="pane-orders" aria-selected="false">
                                    <span class="d-block d-sm-none"><i class="fas fa-receipt"></i></span>
                                    <span class="d-none d-sm-block">Đơn hàng
                                        @if (isset($user->orders_count) && $user->orders_count > 0)
                                            <span class="badge rounded-pill bg-info ms-1">{{ $user->orders_count }}</span>
                                        @endif
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link p-2" id="tab-cart" data-bs-toggle="tab" data-bs-target="#pane-cart"
                                    type="button" role="tab" aria-controls="pane-cart" aria-selected="false">
                                    <span class="d-block d-sm-none"><i class="fas fa-shopping-cart"></i></span>
                                    <span class="d-none d-sm-block">Giỏ hàng Hiện tại
                                        @if (isset($user->cart_items_count) && $user->cart_items_count > 0)
                                            <span
                                                class="badge rounded-pill bg-warning text-dark ms-1">{{ $user->cart_items_count }}</span>
                                        @endif
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link p-2" id="tab-comments" data-bs-toggle="tab"
                                    data-bs-target="#pane-comments" type="button" role="tab"
                                    aria-controls="pane-comments" aria-selected="false">
                                    <span class="d-block d-sm-none"><i class="fas fa-comments"></i></span>
                                    <span class="d-none d-sm-block">Bình luận
                                        @php $totalCommentsCount = $user->comments->count(); @endphp
                                        @if ($totalCommentsCount > 0)
                                            <span
                                                class="badge rounded-pill bg-secondary ms-1">{{ $totalCommentsCount }}</span>
                                        @endif
                                    </span>
                                </a>
                            </li>
                        </ul>

                        {{-- NỘI DUNG CÁC TAB --}}
                        <div class="tab-content text-muted bg-white">

                            {{-- Tab Thông tin (profile_about) - Active mặc định --}}
                            <div class="tab-pane fade show active pt-4" id="pane-info" role="tabpanel"
                                aria-labelledby="tab-info">
                                @if ($user->profile)
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <h5 class="fs-16 text-dark fw-semibold mb-4 text-capitalize">Thông tin cá nhân
                                            </h5> {{-- mb-4 để tạo khoảng cách rõ ràng hơn --}}

                                            <div class="row g-3"> {{-- Sử dụng g-3 để tạo khoảng cách giữa các cột --}}
                                                {{-- Cột Email --}}
                                                <div class="col-md-6">
                                                    <h6 class="text-uppercase fs-13 text-muted mb-1">Email người dùng</h6>
                                                    <p class="fs-14 mb-0"><a href="mailto:{{ $user->email }}"
                                                            class="text-primary text-decoration-underline">{{ $user->email }}</a>
                                                    </p>
                                                </div>

                                                {{-- Cột Số điện thoại --}}
                                                <div class="col-md-6">
                                                    <h6 class="text-uppercase fs-13 text-muted mb-1">Số điện thoại</h6>
                                                    <p class="fs-14 mb-0">{{ $user->profile->phone ?: 'Chưa cập nhật' }}
                                                    </p>
                                                </div>

                                                {{-- Cột Địa chỉ --}}
                                                <div class="col-md-6">
                                                    <h6 class="text-uppercase fs-13 text-muted mb-1">Địa chỉ</h6>
                                                    <p class="fs-14 mb-0">{{ $user->profile->address ?: 'Chưa cập nhật' }}
                                                    </p>
                                                </div>

                                                {{-- Cột Giới tính --}}
                                                <div class="col-md-6">
                                                    <h6 class="text-uppercase fs-13 text-muted mb-1">Giới tính</h6>
                                                    <p class="fs-14 mb-0">
                                                        @if ($user->profile->gender == 'male' || $user->profile->gender == 'nam')
                                                            Nam
                                                        @elseif($user->profile->gender == 'female' || $user->profile->gender == 'nu')
                                                            Nữ
                                                        @else
                                                            {{ ucfirst($user->profile->gender ?: 'Khác') }}
                                                        @endif
                                                    </p>
                                                </div>

                                                {{-- Cột Ngày sinh --}}
                                                <div class="col-md-6">
                                                    <h6 class="text-uppercase fs-13 text-muted mb-1">Ngày sinh</h6>
                                                    <p class="fs-14 mb-0">
                                                        {{ $user->profile->birth_date ? \Carbon\Carbon::parse($user->profile->birth_date)->format('d/m/Y') : 'Chưa cập nhật' }}
                                                    </p>
                                                </div>

                                                {{-- Các cột khác (nếu có, thêm vào đây) --}}
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="alert alert-warning mt-3" role="alert">
                                        <i class="fas fa-exclamation-triangle me-1"></i> Người dùng chưa cập nhật thông tin
                                        hồ sơ.
                                    </div>
                                @endif
                            </div>

                            {{-- Tab Đơn hàng (profile_experience) --}}
                            <div class="tab-pane fade pt-4" id="pane-orders" role="tabpanel"
                                aria-labelledby="tab-orders">
                                <h5 class="mb-3">Danh sách Đơn hàng</h5>
                                @if ($user->orders && $user->orders->count() > 0)
                                    @foreach ($user->orders as $order)
                                        <div class="card mb-3 order-card-collapsible">
                                            <div class="card-body p-3 cursor-pointer d-flex justify-content-between align-items-center"
                                                data-bs-toggle="collapse"
                                                data-bs-target="#orderCollapse{{ $order->id }}" aria-expanded="false"
                                                aria-controls="orderCollapse{{ $order->id }}">
                                                <div class="flex-grow-1">
                                                    <h6 class="mb-1 text-primary">Mã đơn hàng
                                                        #{{ $order->sku ?? $order->id }}</h6>
                                                    <p class="mb-1 fs-14 text-muted">Ngày đặt:
                                                        {{ $order->created_at->format('d/m/Y H:i') }}</p>
                                                    <p class="mb-0 fs-14">
                                                        Tổng tiền: <span
                                                            class="text-success fw-semibold">{{ number_format($order->total_amount, 0, ',', '.') }}
                                                            VNĐ</span>
                                                        @php
                                                            $orderStatusClass = '';
                                                            switch ($order->order_status) {
                                                                case 'Chưa xác nhận':
                                                                    $orderStatusClass = 'bg-secondary';
                                                                    break;
                                                                case 'Xác nhận':
                                                                    $orderStatusClass = 'bg-primary';
                                                                    break;
                                                                case 'Đang vận chuyển':
                                                                    $orderStatusClass = 'bg-info text-dark';
                                                                    break;
                                                                case 'Giao hàng thành công':
                                                                    $orderStatusClass = 'bg-success';
                                                                    break;
                                                                case 'Hủy đơn':
                                                                    $orderStatusClass = 'bg-danger';
                                                                    break;
                                                                default:
                                                                    $orderStatusClass = 'bg-secondary';
                                                                    break;
                                                            }
                                                        @endphp
                                                        <span
                                                            class="badge {{ $orderStatusClass }} ms-2">{{ $order->order_status }}</span>
                                                    </p>
                                                </div>
                                                <div>
                                                    <i class="fas fa-chevron-down order-toggle-icon"></i>
                                                </div>
                                            </div>

                                            <div class="collapse" id="orderCollapse{{ $order->id }}">
                                                <div class="card-body border-top py-3">
                                                    <h6 class="mb-2"><i class="fas fa-boxes me-2"></i>Sản phẩm trong
                                                        đơn:</h6>
                                                    @if ($order->items->count() > 0)
                                                        <div class="table-responsive mb-2">
                                                            <table
                                                                class="table table-sm table-borderless table-striped align-middle">
                                                                <thead>
                                                                    <tr>
                                                                        <th style="width: 50px;"></th>
                                                                        <th>Sản phẩm</th>
                                                                        <th>Số lượng</th>
                                                                        <th>Phương thức TT</th>
                                                                        <th>Đơn giá</th>
                                                                        <th>Thành tiền</th>
                                                                        <th>Ghi chú</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach ($order->items as $item)
                                                                        <tr>
                                                                            @if ($item->product && $item->product->image)
                                                                                <td>
                                                                                    <img src="{{ asset('storage/' . $item->product->image) }}"
                                                                                        alt="Product Image"
                                                                                        class="img-fluid rounded me-2"
                                                                                        style="width:50px; height:50px; object-fit:cover;">
                                                                                </td>
                                                                            @else
                                                                                <td>
                                                                                    <img src="https://spencil.vn/wp-content/uploads/2024/11/chup-anh-san-pham-SPencil-Agency-1.jpg"
                                                                                        alt="Product Image"
                                                                                        class="img-fluid rounded me-2"
                                                                                        style="width:50px; height:50px; object-fit:cover;">
                                                                                </td>
                                                                            @endif
                                                                            <td></td>
                                                                            <td>
                                                                                {{ $item->product_name }}
                                                                                @if ($item->product_attribute)
                                                                                    <br><small class="text-muted">Phân
                                                                                        loại:
                                                                                        ({{ $item->product_attribute }})
                                                                                    </small>
                                                                                @endif
                                                                            </td>
                                                                            <td>{{ $item->quantity }}</td>
                                                                            <td>{{ $order->payment_method_name }}</td>
                                                                            <td>{{ number_format($item->unit_price, 0, ',', '.') }}
                                                                                VNĐ</td>
                                                                            <td>{{ number_format($item->total_price, 0, ',', '.') }}
                                                                                VNĐ</td>
                                                                            <td>{{ $order->note ?? 'Không có ghi chú đơn hàng' }}
                                                                            </td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    @else
                                                        <div class="alert alert-info py-2 small" role="alert">Không có
                                                            sản phẩm nào trong đơn hàng này.</div>
                                                    @endif
                                                    <div class="border-top pt-3 mt-3 d-flex justify-content-end">
                                                        {{-- Chỉ justify-content-end để căn cả khối sang phải --}}
                                                        <div class="col-sm-8 col-md-6 col-lg-5"> {{-- Giảm độ rộng của cột giá để gọn hơn trên các màn hình lớn --}}
                                                            <table class="table table-borderless table-sm mb-0">
                                                                {{-- table-borderless bỏ viền, table-sm làm nhỏ gọn, mb-0 bỏ margin --}}
                                                                <tbody>
                                                                    <tr>
                                                                        <td class="text-start ps-0">Đơn giá:</td>
                                                                        <td class="text-end pe-0 fw-semibold">
                                                                            {{ number_format($item->unit_price, 0, ',', '.') }}
                                                                            VNĐ</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-start ps-0">Giảm giá sản phẩm:</td>
                                                                        <td class="text-end pe-0">
                                                                            @if ($item->discount_amount > 0)
                                                                                <span class="text-danger">-
                                                                                    {{ number_format($item->discount_amount, 0, ',', '.') }}
                                                                                    VNĐ</span>
                                                                            @else
                                                                                <span class="text-muted">Không áp
                                                                                    dụng</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-start ps-0">Giảm giá tổng đơn hàng:
                                                                        </td>
                                                                        <td class="text-end pe-0">
                                                                            @if ($order->discount_amount > 0)
                                                                                <span class="text-danger">-
                                                                                    {{ number_format($order->discount_amount, 0, ',', '.') }}
                                                                                    VNĐ</span>
                                                                            @else
                                                                                <span class="text-muted">Không áp
                                                                                    dụng</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr>
                                                                        <td class="text-start ps-0">Phí ship:</td>
                                                                        <td class="text-end pe-0">
                                                                            @if ($order->shipping_fee > 0)
                                                                                {{ number_format($order->shipping_fee, 0, ',', '.') }}
                                                                                VNĐ
                                                                            @else
                                                                                <span class="text-muted">Miễn phí</span>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                    <tr class="fw-bold fs-5 text-primary">
                                                                        {{-- Dòng tổng cộng đậm và lớn hơn --}}
                                                                        <td class="text-start ps-0">Tổng cộng:</td>
                                                                        <td class="text-end pe-0">
                                                                            {{ number_format($order->total_amount, 0, ',', '.') }}
                                                                            VNĐ</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>


                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="alert alert-light text-center" role="alert">
                                        <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                        Người dùng này chưa có đơn hàng nào.
                                    </div>
                                @endif
                            </div>

                            {{-- Tab Giỏ hàng (portfolio_education) --}}
                            <div class="tab-pane fade pt-4" id="pane-cart" role="tabpanel" aria-labelledby="tab-cart">
                                <h5 class="mb-3">Sản phẩm trong Giỏ hàng</h5>
                                @if ($user->cartItems && $user->cartItems->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm align-middle">
                                            <thead>
                                                <tr>
                                                    <th style="width: 70px;">Ảnh</th>
                                                    <th>Sản phẩm</th>
                                                    <th>Số lượng</th>
                                                    <th>Đơn giá</th>
                                                    <th>Thành tiền</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @php $cartTotal = 0; @endphp
                                                @foreach ($user->cartItems as $item)
                                                    <tr>
                                                        <td>
                                                            <img src="{{ optional($item->productVariant->product)->image ? asset('storage/' . $item->productVariant->product->image) : 'https://spencil.vn/wp-content/uploads/2024/11/chup-anh-san-pham-SPencil-Agency-1.jpg' }}"
                                                                alt="{{ optional($item->productVariant)->sku ?? 'Product Image' }}"
                                                                {{-- Alt text nên dựa trên variant hoặc product --}} class="img-fluid rounded"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                        </td>
                                                        <td>{{ $item->productVariant->product->name }}</td>
                                                        <td>{{ $item->quantity }}</td>
                                                        <td>{{ number_format($item->unit_price, 0, ',', '.') }} VNĐ</td>
                                                        <td>{{ number_format($item->total_price, 0, ',', '.') }} VNĐ</td>
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

                            {{-- Tab Bình luận (setting_tab cũ) --}}
                            <div class="tab-pane fade pt-4" id="pane-comments" role="tabpanel"
                                aria-labelledby="tab-comments">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5>Danh sách Bình luận</h5>
                                    {{-- <button id="toggleTrashedCommentsBtn" class="btn btn-sm btn-outline-danger"
                                        data-user-id="{{ $user->id }}"
                                        data-fetch-url="{{ route('admin.account.comment.account.trashedComments', ['user' => $user->id]) }}">
                                        <i class="fas fa-trash"></i> Thùng rác bình luận
                                    </button> --}}
                                </div>
                                <div id="activeCommentsContainer">
                                    @php
                                        $activeComments = $user->comments->filter(function ($comment) {
                                            return is_null($comment->deleted_at);
                                        });
                                    @endphp
                                    @if ($activeComments->count() > 0)
                                        <table class="table table-sm table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nội dung</th>
                                                    <th>Ngày gửi</th>
                                                    <th>Trạng thái</th>
                                                    <th style="width: 20%;">Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($activeComments as $key => $comment)
                                                    <tr id="active-comment-row-{{ $comment->id }}">
                                                        <td>{{ $key + 1 }}</td>
                                                        <td>{{ Str::limit($comment->content, 70) }}</td>
                                                        <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                                                        <td class="comment-status-cell"
                                                            id="comment-status-cell-{{ $comment->id }}">
                                                            {{-- Logic trạng thái và badge --}}
                                                            @switch($comment->status)
                                                                @case('hiển thị')
                                                                    <span class="badge bg-success">Hiển thị</span>
                                                                @break

                                                                @case('ẩn')
                                                                    <span class="badge bg-warning text-dark">Bị ẩn</span>
                                                                @break

                                                                @case('chưa duyệt')

                                                                    @default
                                                                        <span class="badge bg-secondary">Chưa duyệt</span>
                                                                    @break
                                                                @endswitch
                                                            </td>
                                                            <td class="comment-actions-cell"
                                                                id="comment-actions-cell-{{ $comment->id }}">
                                                                {{-- Nút "Xem chi tiết" --}}
                                                                <button
                                                                    class="btn btn-xs btn-outline-info view-comment-details-btn me-1"
                                                                    data-comment-id="{{ $comment->id }}"
                                                                    title="Xem chi tiết" data-bs-toggle="tooltip">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>


                                                        </tr>
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

                // Khởi tạo tất cả tooltips khi trang tải
                // Kiểm tra nếu Bootstrap đã được tải, thì mới khởi tạo tooltip
                if ($.fn.tooltip) {
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }

                // --- HÀM TIỆN ÍCH (giữ nguyên hoặc điều chỉnh nếu cần) ---
                function updateActiveCommentUIAfterAction(commentId, statusText, statusClassBadge, actionsHtml) {
                    /* ... */
                }

                function updateTotalCommentsBadgeOnTab(newCount) {
                    /* ... */
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
                    // Try to prepend to #main-wrapper (if exists) or body
                    const container = $('#main-wrapper').length ? $('#main-wrapper') : $('body');
                    container.prepend(messageDiv);
                    setTimeout(function() {
                        $('#' + messageId).fadeOut(500, function() {
                            $(this).remove();
                        });
                    }, duration);
                }

                // ++++ XỬ LÝ COLLAPSE CHO ĐƠN HÀNG (TRONG TAB "ĐƠN HÀNG") ++++
                // Bắt sự kiện khi một panel collapse ĐƯỢC KÍCH HOẠT (toggle)
                $('#pane-orders').on('show.bs.collapse', '.collapse', function() {
                    const currentCollapseId = $(this).attr('id');

                    // Đóng tất cả các panel khác trong cùng tab và reset icon của chúng
                    $('#pane-orders .collapse').each(function() {
                        if ($(this).attr('id') !== currentCollapseId && $(this).hasClass('show')) {
                            $(this).collapse('hide');
                            // Cập nhật icon của panel đang đóng
                            $(this).prev('.card-body.cursor-pointer').find('.order-toggle-icon')
                                .removeClass('fa-chevron-up')
                                .addClass('fa-chevron-down');
                        }
                    });

                    // Cập nhật icon của panel đang mở
                    const cardBodyToggle = $(this).prev('.card-body.cursor-pointer');
                    cardBodyToggle.find('.order-toggle-icon')
                        .removeClass('fa-chevron-down')
                        .addClass('fa-chevron-up');
                });

                // Bắt sự kiện khi một panel collapse ĐÓNG LẠI (ẩn)
                $('#pane-orders').on('hide.bs.collapse', '.collapse', function() {
                    // Cập nhật icon của panel đang đóng
                    const cardBodyToggle = $(this).prev('.card-body.cursor-pointer');
                    cardBodyToggle.find('.order-toggle-icon')
                        .removeClass('fa-chevron-up')
                        .addClass('fa-chevron-down');
                });


                // ++++ AJAX CHO NÚT "THÙNG RÁC BÌNH LUẬN" ++++
                $('#toggleTrashedCommentsBtn').on('click', function() {
                    const buttonSelf = $(this);
                    const userId = buttonSelf.data('userId');
                    const trashedSection = $('#trashedCommentsSection');
                    const trashedContainer = $('#trashedCommentsContainer');

                    let urlFetchTrashedComments =
                        "{{ route('admin.account.comment.account.trashedComments', ['user' => ':userId']) }}";
                    urlFetchTrashedComments = urlFetchTrashedComments.replace(':userId', userId);

                    console.log('Fetching trashed comments from:', urlFetchTrashedComments);

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
                                // Re-initialize tooltips for newly added buttons in trashed comments
                                if ($.fn.tooltip) {
                                    $('#trashedCommentsContainer [data-bs-toggle="tooltip"]')
                                        .tooltip(
                                            'dispose').tooltip();
                                }
                                buttonSelf.html('<i class="fas fa-eye-slash"></i> Ẩn thùng rác');
                                buttonSelf.removeClass('btn-outline-danger').addClass('btn-danger');
                            },
                            error: function(xhr) {
                                console.error("Lỗi khi lấy thùng rác bình luận:", xhr.responseText);
                                showTemporaryMessage('Lỗi tải thùng rác. Vui lòng thử lại.',
                                    'error');
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
                $('#pane-comments').on('click', '.view-comment-details-btn', function() {
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
                                        // SỬ DỤNG TRỰC TIẾP 'image_url' NẾU NÓ ĐÃ ĐƯỢC TẠO SẴN Ở BACKEND VÀ LUÔN ĐÚNG
                                        const productImageUrl = response.product.image_url ?
                                            response.product.image_url :
                                            // Sử dụng trực tiếp URL đã được tạo sẵn
                                            'https://placehold.co/70x70/EBF0F5/7F8EA3?text=SP'; // Placeholder

                                        html += '<div class="col-md-12">';
                                        html +=
                                            '<h6><i class="fas fa-box-open text-success me-2"></i>Sản phẩm được bình luận:</h6>';
                                        html += '<div class="d-flex align-items-center">';
                                        html +=
                                            `<img src="${productImageUrl}" alt="${productNameDisplay}" class="img-thumbnail me-3" style="width: 70px; height: 70px; object-fit: cover;">`;
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
                                    // ... (phần xử lý lỗi response.success == false) ...
                                }
                            },
                            error: function(xhr) {
                                console.error("Lỗi AJAX khi lấy chi tiết bình luận:", xhr
                                    .responseText);
                                showTemporaryMessage('Lỗi khi tải chi tiết. Vui lòng thử lại.',
                                    'error');
                                detailContentDiv.html(
                                    '<p class="text-danger small">Lỗi khi tải chi tiết. Vui lòng thử lại.</p>'
                                );
                                button.html('<i class="fas fa-eye"></i>').attr('title',
                                    'Xem chi tiết');
                                setTimeout(function() {
                                    detailRow.slideUp();
                                }, 3000);
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
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                updateActiveCommentUIAfterAction(response.comment_id, response
                                    .new_status_text, response.new_status_class_badge, response
                                    .new_actions_html);
                                showTemporaryMessage(response.message, 'success');
                            } else {
                                showTemporaryMessage(response.message || 'Hành động thất bại.',
                                    'error');
                                $('#comment-actions-cell-' + commentId).html(
                                    originalActionsCellHtml);
                                if ($.fn.tooltip) {
                                    $(`#comment-actions-cell-${commentId} [data-bs-toggle="tooltip"]`)
                                        .tooltip('dispose').tooltip();
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error("Lỗi AJAX khi thay đổi trạng thái:", xhr.responseText);
                            showTemporaryMessage('Lỗi hệ thống khi thay đổi trạng thái.', 'error');
                            $('#comment-actions-cell-' + commentId).html(originalActionsCellHtml);
                            if ($.fn.tooltip) {
                                $(`#comment-actions-cell-${commentId} [data-bs-toggle="tooltip"]`)
                                    .tooltip('dispose').tooltip();
                            }
                        }
                    });
                });

                // ---- AJAX CHO XÓA MỀM BÌNH LUẬN ----
                $('#activeCommentsContainer').on('click', '.soft-delete-comment-btn', function() {
                    const button = $(this);
                    const commentId = button.data('commentId');
                    let deleteUrl =
                        "{{ route('admin.account.comment.softDeleteComment', ['comment' => ':commentId']) }}";
                    deleteUrl = deleteUrl.replace(':commentId', commentId);

                    if (!confirm('Bạn có chắc muốn chuyển bình luận này vào thùng rác?')) return;

                    const originalActionsCellHtml = button.closest('.comment-actions-cell').html();
                    button.closest('.comment-actions-cell').html(
                        '<i class="fas fa-spinner fa-spin text-danger"></i>');

                    $.ajax({
                        url: deleteUrl,
                        type: 'POST',
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
                                    $('#toggleTrashedCommentsBtn').click();
                                    setTimeout(function() {
                                        $('#toggleTrashedCommentsBtn').click();
                                    }, 250);
                                }
                            } else {
                                showTemporaryMessage(response.message || 'Lỗi khi xóa.', 'error');
                                $('#comment-actions-cell-' + commentId).html(
                                    originalActionsCellHtml);
                                if ($.fn.tooltip) {
                                    $(`#comment-actions-cell-${commentId} [data-bs-toggle="tooltip"]`)
                                        .tooltip('dispose').tooltip();
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error("Lỗi hệ thống khi xóa bình luận.", xhr.responseText);
                            showTemporaryMessage('Lỗi hệ thống khi xóa bình luận.', 'error');
                            $('#comment-actions-cell-' + commentId).html(originalActionsCellHtml);
                            if ($.fn.tooltip) {
                                $(`#comment-actions-cell-${commentId} [data-bs-toggle="tooltip"]`)
                                    .tooltip('dispose').tooltip();
                            }
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

                                if (response.restored_comment_html) {
                                    $('#activeCommentsContainer table tbody').prepend(response
                                        .restored_comment_html);
                                    if ($.fn.tooltip) {
                                        $('#activeCommentsContainer [data-bs-toggle="tooltip"]')
                                            .tooltip('dispose').tooltip();
                                    }
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
            });
        </script>
    @endpush
