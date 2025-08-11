{{-- resources/views/client/pages/profile.blade.php --}}
@extends('layouts.app')
@section('title', 'Thông tin cá nhân của ' . $user->name) {{-- Thay đổi tiêu đề cho phù hợp với trang cá nhân --}}

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

    /* Đảm bảo các badge màu sắc tương tự Bootstrap 4/5 */
    .badge.bg-success {
        background-color: #28a745 !important;
    }

    .badge.bg-info.text-dark {
        background-color: #17a2b8 !important;
        color: #64B496 !important;
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
        color: #64B496 !important;
    }

    .btn-xs {
        padding: .25rem .5rem;
        font-size: .75rem;
        line-height: 1.5;
        border-radius: .2rem;
    }

    /* Thêm CSS cho tab tùy chỉnh */
    .tab-content-item {
        display: none;
        /* Mặc định ẩn tất cả các tab content */
    }

    .tab-content-item.active {
        display: block;
        /* Chỉ hiển thị tab content đang active */
    }

    .nav-link {
        cursor: pointer;
        /* Cho biết các tab có thể click được */
    }

    .nav-link {
        color: #000000;
        /* Màu xanh dương nhạt */
    }

    .nav-link.active {
        color: #64B496 !important;
        /* Ví dụ: màu hồng khi active */
        font-weight: bold;
    }

    /* Style cho rating stars */
    .rating-stars .fas.fa-star {
        color: gold;
    }

    .rating-stars .far.fa-star {
        /* empty star */
        color: lightgray;
    }
</style>


@section('content')

    <head>
        {{-- Sử dụng CDN Font Awesome mới nhất để có nhiều icon hơn nếu cần --}}
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
            integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head><br>

    <!-- Breadcrumb -->
    <section class="section-breadcrumb">
        <div class="cr-breadcrumb-image">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="cr-breadcrumb-title">
                            <h2>Tài khoản</h2>
                            <span><a href="{{ route('home') }}">Home</a> - Tài khoản</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><br>

    {{-- Đặt ở đầu phần nội dung chính của trang --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif
    {{-- Đây là khối chung để hiển thị tất cả lỗi validation nếu bạn có --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container-xxl">
        <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
            <div class="flex-grow-1">
                <h4 class="fs-18 fw-semibold m-0">Trang cá nhân của <b>{{ $user->name }}</b></h4>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="align-items-center">
                            <div class="d-flex align-items-center">
                                <img src="{{ $user->profile && $user->profile->user_image ? asset('storage/' . $user->profile->user_image) : 'https://img.freepik.com/free-vector/blue-circle-with-white-user_78370-4707.jpg?semt=ais_hybrid&w=740' }}"
                                    class="rounded-circle avatar-xxl img-thumbnail float-start" alt="Ảnh đại diện"
                                    width="150px" height="150px">

                                <div class="overflow-hidden ms-4">
                                    <h4 class="m-0 text-dark fs-20">{{ $user->name }}</h4>
                                    {{-- <p class="my-1 text-muted fs-16">{{ $user->email }}</p> --}}
                                    {{-- Trạng thái xác minh Email --}}
                                    <p class="my-1 text-muted fs-16">
                                        {{ $user->email }}
                                        @if ($user->email_verified_at)
                                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Đã xác
                                                thực</span>
                                        @else
                                            <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Chưa xác
                                                thực</span>
                                            <a href="{{ route('verification.notice') }}" class="btn btn-outline-warning">Xác
                                                nhận email</a>
                                        @endif
                                    </p>
                                    @if ($user->role == 'admin' || $user->role == 'superadmin')
                                        <span class="fs-15">
                                            <i class="mdi mdi-account-group me-2 align-middle"></i>Vai trò:
                                            <span>
                                                @if ($user->role == 'admin' || $user->role == 'superadmin')
                                                    <span class="badge bg-success">{{ ucfirst($user->role) }}</span>
                                                @else
                                                    <span class="badge bg-info text-dark">{{ ucfirst($user->role) }}</span>
                                                @endif
                                                <span
                                                    class="badge bg-primary-subtle text-primary px-2 py-1 fs-13 fw-normal">
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
                                    @else
                                        {{-- <span class="badge bg-info text-dark">{{ ucfirst($user->role) }}</span> --}}
                                    @endif
                                    {{-- Phần thông tin ngắn gọn này có thể giữ hoặc bỏ tùy ý, vì sẽ có tab "Thông tin cá nhân" chi tiết hơn --}}
                                    @if ($user->profile)
                                        <div class="mt-3 text-start">
                                            <p class="mb-1 small"><strong><i
                                                        class="fas fa-phone me-2 text-success"></i>SĐT:</strong>
                                                {{ $user->profile->phone ?: 'Chưa cập nhật' }}</p>
                                            <p class="mb-1 small"><strong><i
                                                        class="fas fa-map-marker-alt me-2 text-success"></i>Địa
                                                    chỉ:</strong>
                                                {{ $user->profile->address ?: 'Chưa cập nhật' }}</p>
                                            <p class="mb-0 small"><strong><i
                                                        class="fas fa-venus-mars me-2 text-success"></i>Giới tính:</strong>
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

                        {{-- NAVIGATION TABS --}}
                        <ul class="nav nav-underline border-bottom pt-2" id="userProfileTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                {{-- Dùng data-tab thay vì data-bs-toggle="tab" để tự xử lý JS --}}
                                <a class="nav-link p-2" data-tab="info" style="color: #000000;">
                                    <span class="d-block d-sm-none"><i class="mdi mdi-information"></i></span>
                                    <span class="d-none d-sm-block">Thông tin</span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link p-2" data-tab="orders" style="color: #000000;">
                                    <span class="d-block d-sm-none"><i class="fas fa-receipt"></i></span>
                                    <span class="d-none d-sm-block">Đơn hàng
                                        @if (isset($data['orders']) && $data['orders']->total() > 0)
                                            <span
                                                class="badge rounded-pill bg-info ms-1">{{ $data['orders']->total() }}</span>
                                        @endif
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link p-2" data-tab="reviews" style="color: #000000;">
                                    <span class="d-block d-sm-none"><i class="fas fa-star"></i></span>
                                    <span class="d-none d-sm-block">Đánh giá
                                        @if (isset($data['reviews']) && $data['reviews']->total() > 0)
                                            <span
                                                class="badge rounded-pill bg-primary ms-1">{{ $data['reviews']->total() }}</span>
                                        @endif
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link p-2" data-tab="cart" style="color: #000000;">
                                    <span class="d-block d-sm-none"><i class="fas fa-shopping-cart"></i></span>
                                    <span class="d-none d-sm-block">Giỏ hàng
                                        @if (isset($data['cart']) && $data['cart'] && $data['cart']->items && $data['cart']->items->count() > 0)
                                            {{-- Đảm bảo bạn đang hiển thị đúng số lượng items, không phải $user->cart_items_count nếu biến đó không tồn tại --}}
                                            <span
                                                class="badge rounded-pill bg-warning text-dark ms-1">{{ $data['cart']->items->count() }}</span>
                                        @endif
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link p-2" data-tab="comments" style="color: #000000;">
                                    <span class="d-block d-sm-none"><i class="fas fa-comments"></i></span>
                                    <span class="d-none d-sm-block">Bình luận
                                        @php $totalCommentsCount = isset($data['comments']) ? $data['comments']->total() : 0; @endphp
                                        @if ($totalCommentsCount > 0)
                                            <span
                                                class="badge rounded-pill bg-secondary ms-1">{{ $totalCommentsCount }}</span>
                                        @endif
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link p-2" data-tab="wishlist" style="color: #000000;">
                                    <span class="d-block d-sm-none"><i class="fas fa-heart"></i></span>
                                    <span class="d-none d-sm-block">Yêu thích
                                        @if (isset($data['wishlistItems']) && $data['wishlistItems']->total() > 0)
                                            <span
                                                class="badge rounded-pill bg-danger ms-1">{{ $data['wishlistItems']->total() }}</span>
                                        @endif
                                    </span>
                                </a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link p-2" data-tab="password" style="color: #000000;">
                                    <span class="d-block d-sm-none"><i class="fas fa-lock"></i></span>
                                    <span class="d-none d-sm-block">Đổi mật khẩu</span>
                                </a>
                            </li>
                        </ul>

                        {{-- NỘI DUNG CÁC TAB - Tất cả trong một div.tab-content --}}
                        <div class="tab-content text-muted bg-white mt-3"> {{-- Thêm mt-3 cho khoảng cách --}}

                            {{-- Tab Content - Thông tin cá nhân (Info) --}}
                            {{-- Sử dụng $tab == 'info' để xác định tab active ban đầu từ controller --}}
                            <div id="info" class="tab-content-item {{ $tab == 'info' ? 'active' : '' }}">
                                <h3 style="color: #64B496;">Thông tin cá nhân</h3>
                                <form action="{{ route('profile.update') }}" method="POST"
                                    enctype="multipart/form-data">
                                    @csrf

                                    <div class="form-group mb-3"> {{-- Thêm mb-3 cho khoảng cách --}}
                                        <label for="name">Tên của bạn:</label>
                                        <input type="text" class="form-control" id="name" name="name"
                                            value="{{ old('name', $user->name) }}">
                                        @error('name')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="email">Email:</label>
                                        <input type="email" class="form-control" id="email" name="email"
                                            value="{{ old('email', $user->email) }}">
                                        @error('email')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="phone">Số điện thoại:</label>
                                        <input type="text" class="form-control" id="phone" name="phone"
                                            value="{{ old('phone', $data['profile']->phone ?? '') }}">
                                        @error('phone')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    {{-- <div class="form-group mb-3">
                                        <label for="address">Địa chỉ:</label>
                                        <input type="text" class="form-control" id="address" name="address"
                                            value="{{ old('address', $data['profile']->address ?? '') }}">
                                        @error('address')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div> --}}
                                    @php
                                        $addressParts = explode(', ', $data['profile']->address ?? '');
                                        $street = $addressParts[0] ?? '';
                                        $ward = $addressParts[1] ?? '';
                                        $district = $addressParts[2] ?? '';
                                        $province = $addressParts[3] ?? '';
                                    @endphp
                                    <div class="form-group mt-3">
                                        <label for="province">Tỉnh/Thành phố</label>
                                        <select id="province" class="form-control">
                                            <option value="">-- Chọn Tỉnh/TP --</option>
                                        </select>
                                        @error('address')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="district">Quận/Huyện</label>
                                        <select id="district" class="form-control" disabled>
                                            <option value="">-- Chọn Quận/Huyện --</option>
                                        </select>
                                        @error('address')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="ward">Phường/Xã</label>
                                        <select id="ward" class="form-control" disabled>
                                            <option value="">-- Chọn Phường/Xã --</option>
                                        </select>
                                        @error('address')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="street">Số nhà, tên đường</label>
                                        <input type="text" id="street" class="form-control"
                                            value="{{ old('street', $street) }}">
                                        @error('address')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    {{-- Trường ẩn để lưu chuỗi đầy đủ --}}
                                    {{-- <input type="hidden" name="address" id="full_address"> input ẩn không được xóa, lưu dl ẩn kho sửa địa chỉ --}}

                                    {{-- Trường ẩn để lưu chuỗi đầy đủ --}}
                                    <input type="hidden" name="address" id="full_address">
                                    <div class="form-group mb-3">
                                        <label for="gender">Giới tính:</label>
                                        <select class="form-control" id="gender" name="gender">
                                            <option value=""
                                                {{ old('gender', $data['profile']->gender ?? '') == '' ? 'selected' : '' }}>
                                                -- Chọn giới tính --
                                            </option>
                                            <option value="nam"
                                                {{ old('gender', $data['profile']->gender ?? '') == 'nam' ? 'selected' : '' }}>
                                                Nam</option>
                                            <option value="nu"
                                                {{ old('gender', $data['profile']->gender ?? '') == 'nu' ? 'selected' : '' }}>
                                                Nữ</option>
                                            <option value="khac"
                                                {{ old('gender', $data['profile']->gender ?? '') == 'khac' ? 'selected' : '' }}>
                                                Khác</option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="birth_date">Ngày sinh:</label>
                                        <input type="date"
                                            class="form-control @error('birth_date') is-invalid @enderror" id="birth_date"
                                            name="birth_date"
                                            value="{{ old('birth_date', optional($data['profile']?->birth_date)->format('Y-m-d') ?? '') }}">
                                        @error('birth_date')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <div class="form-group mb-3">
                                        <label for="user_image">Ảnh đại diện:</label>
                                        <input type="file" class="form-control-file" id="user_image"
                                            name="user_image">
                                        <img src="{{ $user->profile && $user->profile->user_image ? asset('storage/' . $user->profile->user_image) : 'https://img.freepik.com/free-vector/blue-circle-with-white-user_78370-4707.jpg?semt=ais_hybrid&w=740' }}"
                                            class="img-thumbnail mt-2" alt="" width="100">
                                        @error('user_image')
                                            <div class="invalid-feedback">
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-success">Cập nhật thông tin</button>
                                </form>
                            </div>

                            {{-- Tab Content - Đơn hàng của tôi (Orders) --}}
                            <div id="orders" class="tab-content-item {{ $tab == 'orders' ? 'active' : '' }}">
                                <h3 style="color: #64B496;">Đơn hàng của tôi</h3>
                                @if (isset($data['orders']) && $data['orders']->isNotEmpty())
                                    <div class="accordion" id="ordersAccordion">
                                        @foreach ($data['orders'] as $order)
                                            <div class="card mb-2 order-card-collapsible">
                                                <div class="card-header p-3 cursor-pointer d-flex justify-content-between align-items-center"
                                                    {{-- Điều chỉnh padding --}} data-bs-toggle="collapse"
                                                    style="background-color: #E0F2F1"
                                                    data-bs-target="#orderCollapse{{ $order->id }}"
                                                    aria-expanded="false"
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
                                                        <h6 class="mb-2"><i class="fas fa-truck me-2"></i>Thông tin vận
                                                            chuyển:</h6>
                                                        <p class="mb-1 small"><strong>Người nhận:</strong>
                                                            {{ $order->shipping_name }}</p>
                                                        <p class="mb-1 small"><strong>Điện thoại:</strong>
                                                            {{ $order->shipping_phone }}</p>
                                                        <p class="mb-1 small"><strong>Địa chỉ:</strong>
                                                            {{ $order->shipping_address }}</p>
                                                        <p class="mb-1 small"><strong>Phương thức thanh toán:</strong>
                                                            {{ $order->payment_method_name }}</p>
                                                        <p class="mb-1 small">
                                                            <strong>Trạng thái thanh toán:</strong>
                                                            <span
                                                                class="badge {{ $order->payment_status == 'paid' ? 'bg-success' : ($order->payment_status == 'pending' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                                                {{ $order->payment_status == 'paid' ? 'Đã thanh toán' : ($order->payment_status == 'pending' ? 'Đang chờ' : 'Thất bại') }}
                                                            </span>
                                                        </p>



                                                        @if ($order->note)
                                                            <p class="mb-1 small"><strong>Ghi chú đơn hàng:</strong>
                                                                {{ $order->note }}</p>
                                                        @endif
                                                        @if ($order->cancel_reason)
                                                            <p class="mb-1 small text-danger"><strong>Lý do hủy:</strong>
                                                                {{ $order->cancel_reason }}</p>
                                                        @endif

                                                        <h6 class="mb-2 mt-3"><i class="fas fa-boxes me-2"></i>Sản phẩm
                                                            trong đơn:</h6>

                                                        {{-- PHẦN TRONG TAB ĐƠN HÀNG (orders) --}}
                                                        @if (optional($order->items)->isNotEmpty())
                                                            <div class="table-responsive mb-2">
                                                                <table
                                                                    class="table table-sm table-borderless table-striped align-middle">
                                                                    <thead>
                                                                        <tr>
                                                                            <th style="width: 50px;"></th>
                                                                            <th>Sản phẩm</th>
                                                                            <th>Số lượng</th>
                                                                            <th>Đơn giá</th>
                                                                            <th>Thành tiền</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @php $subtotalBeforeDiscount = 0; @endphp {{-- Biến mới để tính tổng giá trị sản phẩm trước giảm giá --}}
                                                                        @foreach ($order->items as $item)
                                                                            {{-- @dd($item) --}}
                                                                            @php

                                                                                $itemOriginalPrice =
                                                                                    $item->quantity * $item->unit_price;
                                                                                $subtotalBeforeDiscount += $itemOriginalPrice;
                                                                            @endphp
                                                                            <tr>
                                                                                <td>
                                                                                    @if ($item->product_image)
                                                                                        <img src="{{ asset('storage/' . $item->product_image) }}"
                                                                                            alt="Product Image"
                                                                                            class="img-fluid rounded me-2"
                                                                                            style="width:50px; height:50px; object-fit:cover;">
                                                                                    @else
                                                                                        <img src="https://spencil.vn/wp-content/uploads/2024/11/chup-anh-san-pham-SPencil-Agency-1.jpg"
                                                                                            alt="Product Image"
                                                                                            class="img-fluid rounded me-2"
                                                                                            style="width:50px; height:50px; object-fit:cover;">
                                                                                    @endif
                                                                                </td>
                                                                                <td>
                                                                                    {{ $item->product_name }}
                                                                                    @if ($item->product_attribute)
                                                                                        <br><small class="text-muted">
                                                                                            loại:
                                                                                            ({{ $item->product_attribute }})
                                                                                        </small>
                                                                                    @endif
                                                                                </td>
                                                                                <td>{{ $item->quantity }}</td>
                                                                                <td>{{ number_format($item->unit_price, 0, ',', '.') }}
                                                                                    VNĐ</td>
                                                                                <td>{{ number_format($subtotalBeforeDiscount, 0, ',', '.') }}
                                                                                    VNĐ</td>
                                                                            </tr>
                                                                        @endforeach
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        @else
                                                            <div class="alert alert-info py-2 small" role="alert">Không
                                                                có sản phẩm nào trong đơn hàng này.</div>
                                                        @endif

                                                        <div class="border-top pt-3 mt-3 d-flex justify-content-end">
                                                            <div class="col-sm-8 col-md-6 col-lg-5">
                                                                <table class="table table-borderless table-sm mb-0">
                                                                    <tbody>
                                                                        <tr>
                                                                            <td class="text-start ps-0">Tổng giá trị sản
                                                                                phẩm:</td>
                                                                            
                                                                            <td class="text-end pe-0 fw-semibold">
                                                                                {{ number_format($subtotalBeforeDiscount, 0, ',', '.') }}
                                                                                VNĐ
                                                                            </td>
                                                                        </tr>
                                                                        <tr>
                                                                            <td class="text-start ps-0">Giảm giá tổng đơn
                                                                                hàng:</td>
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
                                                                                    <span class="text-muted">Miễn
                                                                                        phí</span>
                                                                                @endif
                                                                            </td>
                                                                        </tr>
                                                                        <tr class="fw-bold fs-5 text-primary">
                                                                            <td class="text-start ps-0">Tổng cộng:</td>
                                                                            {{-- Dòng này đã hiển thị đúng total_amount từ bảng orders --}}
                                                                            <td class="text-end pe-0">
                                                                                {{ number_format($order->total_amount, 0, ',', '.') }}
                                                                                VNĐ
                                                                            </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-light text-center" role="alert">
                                        <i class="fas fa-folder-open fa-2x mb-2 d-block"></i>
                                        Người dùng này chưa có đơn hàng nào.
                                    </div>
                                @endif
                                <div class="mt-3">
                                    {{ $data['orders']->appends(request()->except('orders_page') + ['tab' => 'orders'])->links() }}
                                </div>
                            </div>

                            {{-- Tab Content - Đánh giá sản phẩm (Reviews) --}}
                            <div id="reviews" class="tab-content-item {{ $tab == 'reviews' ? 'active' : '' }}">
                                <h3 style="color: #64B496;">Đánh giá sản phẩm của tôi</h3>
                                @if (isset($data['reviews']) && $data['reviews']->isNotEmpty())
                                    <div class="list-group">
                                        @foreach ($data['reviews'] as $review)
                                            <div class="list-group-item mb-3">
                                                <h5 class="mb-1" style="color: #64B496;">{{ $review->title }}</h5>
                                                <p class="mb-1"><strong>Sản phẩm:</strong>
                                                    {{ optional(optional($review->productVariant)->product)->name ?? 'N/A' }}
                                                    ({{ optional($review->productVariant)->attribute_name ?? 'N/A' }})
                                                </p>
                                                <p class="mb-1 rating-stars"><strong>Đánh giá:</strong>
                                                    @for ($i = 0; $i < $review->rating; $i++)
                                                        <i class="fas fa-star"></i>
                                                    @endfor
                                                    @for ($i = $review->rating; $i < 5; $i++)
                                                        <i class="far fa-star"></i>
                                                    @endfor
                                                </p>
                                                <p class="mb-1">{{ $review->content }}</p>
                                                <small class="text-muted">Ngày đánh giá:
                                                    {{ $review->created_at->format('d/m/Y H:i') }} - Trạng thái:
                                                    @if ($review->status === 'pending')
                                                        <span class="text-warning">Chờ duyệt</span>
                                                    @elseif($review->status === 'approved')
                                                        <span class="text-success">Đã duyệt</span>
                                                    @elseif($review->status === 'rejected')
                                                        <span class="text-danger">Từ chối</span>
                                                    @else
                                                        <span class="text-muted">Không xác định</span>
                                                    @endif
                                                </small>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-light text-center" role="alert">
                                        <i class="fas fa-star fa-2x mb-2 d-block"></i>
                                        Người dùng này chưa có đánh giá sản phẩm nào.
                                    </div>
                                @endif
                                <div class="mt-3">
                                    {{ $data['reviews']->appends(['tab' => 'reviews'])->links() }}
                                </div>
                            </div>

                            {{-- Tab Content - Giỏ hàng (Cart) --}}
                            <div id="cart" class="tab-content-item {{ $tab == 'cart' ? 'active' : '' }}">
                                <h5 class="mb-3" style="color: #64B496;">Sản phẩm trong Giỏ hàng</h5>
                                {{-- Đảm bảo $data['cart'] không null, và $data['cart']->items không rỗng --}}
                                @if (optional($data['cart'])->items?->isNotEmpty()) {{-- Đã sửa dùng optional() và ?-> --}}
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
                                                @foreach ($data['cart']->items as $item)
                                                    {{-- Đã sửa dùng ->items --}}
                                                    @php $cartTotal += $item->total_price; @endphp
                                                    <tr>
                                                        <td>
                                                            {{-- PHẦN NÀY CẦN SỬA ĐỂ LẤY ẢNH SẢN PHẨM --}}
                                                            {{-- Lấy ảnh từ productVariant, HOẶC từ product nếu ảnh chính nằm ở đó --}}
                                                            <img src="{{ optional($item->productVariant->product)->image ? asset('storage/' . $item->productVariant->product->image) : 'https://spencil.vn/wp-content/uploads/2024/11/chup-anh-san-pham-SPencil-Agency-1.jpg' }}"
                                                                alt="{{ optional($item->productVariant)->sku ?? 'Product Image' }}"
                                                                {{-- Alt text nên dựa trên variant hoặc product --}} class="img-fluid rounded"
                                                                style="width: 50px; height: 50px; object-fit: cover;">
                                                        </td>
                                                        <td>
                                                            {{ optional(optional($item->productVariant)->product)->name ?? 'Sản phẩm không rõ' }}
                                                            @if (optional($item->productVariant)->attribute_name)
                                                                <br><small class="text-muted">Phân loại:
                                                                    ({{ $item->productVariant->attribute_name }})
                                                                </small>
                                                            @endif
                                                        </td>
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

                            {{-- Tab Content - Bình luận (Comments) --}}
                            <div id="comments" class="tab-content-item {{ $tab == 'comments' ? 'active' : '' }}">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 style="color: #64B496;">Danh sách Bình luận</h5>
                                    {{-- Nút "Thùng rác bình luận" cần được thêm vào nếu bạn muốn chức năng này --}}
                                    {{-- Ví dụ: --}}
                                    {{-- <button id="toggleTrashedCommentsBtn" class="btn btn-sm btn-outline-danger"
                                        data-user-id="{{ $user->id }}">
                                        <i class="fas fa-trash"></i> Thùng rác bình luận
                                    </button> --}}
                                </div>
                                <div id="activeCommentsContainer">
                                    @php
                                        // $data['comments'] đã được tải đầy đủ, không cần filter lại
                                        $activeComments = $data['comments']->filter(function ($comment) {
                                            return is_null($comment->deleted_at); // Đảm bảo chỉ lấy comment chưa bị soft delete
                                        });
                                    @endphp
                                    @if ($activeComments->isNotEmpty())
                                        <table class="table table-sm table-bordered table-hover">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Sản phẩm</th>
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
                                                        <td>
                                                            {{ optional($comment->product)->name ?? 'Sản phẩm không rõ' }}
                                                        </td>
                                                        <td>{{ Str::limit($comment->content, 70) }}</td>
                                                        <td>{{ $comment->created_at->format('d/m/Y H:i') }}</td>
                                                        <td class="comment-status-cell"
                                                            id="comment-status-cell-{{ $comment->id }}">
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
                                                                <button
                                                                    class="btn btn-xs btn-outline-info view-comment-details-btn me-1"
                                                                    data-comment-id="{{ $comment->id }}"
                                                                    title="Xem chi tiết" data-bs-toggle="tooltip">
                                                                    <i class="fas fa-eye"></i>
                                                                </button>

                                                                {{-- Các nút hành động khác cho admin, ví dụ: --}}
                                                                {{-- @if ($comment->status == 'chưa duyệt' || $comment->status == 'ẩn')
                                                                    <button
                                                                        class="btn btn-xs btn-outline-success change-comment-status-btn me-1"
                                                                        data-comment-id="{{ $comment->id }}"
                                                                        data-action="approve" title="Duyệt bình luận"
                                                                        data-bs-toggle="tooltip">
                                                                        <i class="fas fa-check"></i>
                                                                    </button>
                                                                @endif --}}
                                                                {{-- @if ($comment->status == 'hiển thị' || $comment->status == 'chưa duyệt')
                                                                    <button
                                                                        class="btn btn-xs btn-outline-warning change-comment-status-btn me-1"
                                                                        data-comment-id="{{ $comment->id }}"
                                                                        data-action="hide" title="Ẩn bình luận"
                                                                        data-bs-toggle="tooltip">
                                                                        <i class="fas fa-eye-slash"></i>
                                                                    </button>
                                                                @endif --}}
                                                                {{-- <button
                                                                    class="btn btn-xs btn-outline-danger soft-delete-comment-btn"
                                                                    data-comment-id="{{ $comment->id }}"
                                                                    title="Xóa bình luận" data-bs-toggle="tooltip">
                                                                    <i class="fas fa-trash"></i>
                                                                </button> --}}
                                                            </td>
                                                        </tr>
                                                        <tr class="comment-detail-row"
                                                            id="comment-detail-row-{{ $comment->id }}"
                                                            style="display: none;">
                                                            <td colspan="6"> {{-- Cập nhật colspan --}}
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
                                        <div class="mt-3">
                                            {{ $data['comments']->appends(['tab' => 'comments'])->links() }}
                                        </div>
                                    </div>
                                    <hr>
                                    <div id="trashedCommentsSection" style="display: none;">
                                        <h5 class="mb-3">Bình luận đã xóa</h5>
                                        <div id="trashedCommentsContainer">
                                            <p class="text-center text-muted">Nhấp vào "Thùng rác bình luận" để xem.</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Tab Content - Sản phẩm yêu thích (Wishlist) --}}
                                <div id="wishlist" class="tab-content-item {{ $tab == 'wishlist' ? 'active' : '' }}">
                                    <h3 style="color: #64B496;">Sản phẩm yêu thích của tôi</h3>
                                    @if (isset($data['wishlistItems']) && $data['wishlistItems']->isNotEmpty())
                                        <div class="list-group">
                                            @foreach ($data['wishlistItems'] as $wishlistItem)
                                                <div class="list-group-item mb-3">
                                                    <div class="row align-items-center">
                                                        <div class="col-md-2">
                                                            <img src="{{ optional($wishlistItem->product)->image ? asset('storage/' . $wishlistItem->product->image) : 'https://spencil.vn/wp-content/uploads/2024/11/chup-anh-san-pham-SPencil-Agency-1.jpg' }}"
                                                                alt="" {{-- Alt text nên dựa trên variant hoặc product --}}
                                                                class="img-fluid rounded"
                                                                style="width: 111px; height: 111px;">
                                                        </div>
                                                        <div class="col-md-7">
                                                            <h5 class="mb-1" style="color: #64B496;">
                                                                {{ optional($wishlistItem->product)->name ?? 'N/A' }}</h5>
                                                            <p class="mb-1 text-muted">
                                                                Thêm vào:
                                                                {{ \Carbon\Carbon::parse($wishlistItem->add_at)->format('d/m/Y') }}
                                                            </p>

                                                            <p class="mb-1">
                                                                Ưu tiên:
                                                                <span
                                                                    class="badge 
                                                                    @if ($wishlistItem->priority === 'High') bg-danger
                                                                    @elseif($wishlistItem->priority === 'Medium') bg-warning text-dark
                                                                    @elseif($wishlistItem->priority === 'Low') bg-info text-dark
                                                                    @else bg-secondary @endif">
                                                                    @if ($wishlistItem->priority === 'High')
                                                                        Cao
                                                                    @elseif($wishlistItem->priority === 'Medium')
                                                                        Trung bình
                                                                    @elseif($wishlistItem->priority === 'Low')
                                                                        Thấp
                                                                    @else
                                                                        Không xác định
                                                                    @endif
                                                                </span>
                                                            </p>

                                                            </p>
                                                            <p class="mb-0">Thông báo khi giảm giá:
                                                                {{ $wishlistItem->notify_on_sale ? 'Có' : 'Không' }}</p>
                                                        </div>
                                                        {{-- <div class="col-md-3 text-right">
                                                            <a href="#" class="btn btn-sm btn-success">Thêm vào giỏ
                                                                hàng</a>
                                                            Bạn có thể thêm form xóa sản phẩm khỏi wishlist ở đây
                                                        </div> --}}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <div class="alert alert-light text-center" role="alert">
                                            <i class="fas fa-heart fa-2x mb-2 d-block"></i>
                                            Người dùng này chưa có sản phẩm nào trong danh sách yêu thích.
                                        </div>
                                    @endif
                                    <div class="mt-3">
                                        {{ $data['wishlistItems']->appends(['tab' => 'wishlistItems'])->links() }}
                                    </div>
                                </div>

                            </div> {{-- End tab-content --}}

                            {{-- Tab Content - Đổi mật khẩu (Password) --}}
                            <div id="password" class="tab-content-item {{ $tab == 'password' ? 'active' : '' }}">
                                <h3 style="color: #64B496;">Đổi mật khẩu</h3>
                                <form action="{{ route('profile.updatePassword') }}" method="POST">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="current_password">Mật khẩu hiện tại:</label>
                                        <div class="input-group">
                                            <input type="password"
                                                class="form-control @error('current_password') is-invalid @enderror"
                                                id="current_password" name="current_password" required>
                                            <button class="btn btn-outline-secondary" type="button"
                                                id="toggleCurrentPassword" style="border-left: none;">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            @error('current_password')
                                                {{-- Validation feedback should still be outside the input-group if you want it to display below --}}
                                                <div class="invalid-feedback d-block">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="new_password">Mật khẩu mới:</label>
                                        <div class="input-group">
                                            <input type="password"
                                                class="form-control @error('new_password') is-invalid @enderror"
                                                id="new_password" name="new_password" required>
                                            <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                            @error('new_password')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="new_password_confirmation">Xác nhận mật khẩu mới:</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="new_password_confirmation"
                                                name="new_password_confirmation" required>
                                            <button class="btn btn-outline-secondary" type="button"
                                                id="toggleConfirmPassword">
                                                <i class="fa fa-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
                                </form>
                            </div>
                        </div> {{-- End card-body --}}
                    </div> {{-- End card --}}
                </div> {{-- End col-12 --}}
            </div> {{-- End row --}}
        </div> {{-- End container-xxl --}}
    @endsection


    {{-- Phần cho nút ẩn hiện password --}}
    {{-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    @push('scripts')
        <script>
            $(document).ready(function() {
                const csrfTokenGlobal = $('meta[name="csrf-token"]').attr('content');

                // BẮT ĐẦU PHẦN LOAD ĐỊA CHỈ
                const oldProvince = @json($province);
                const oldDistrict = @json($district);
                const oldWard = @json($ward);


                // Load danh sách Tỉnh
                $.get('https://provinces.open-api.vn/api/?depth=1', function(provinces) {
                    $('#province').html('<option value="">-- Chọn Tỉnh/TP --</option>');
                    provinces.forEach(p => {
                        const selected = p.name === oldProvince ? 'selected' : '';
                        $('#province').append(
                            `<option value="${p.code}" ${selected}>${p.name}</option>`);
                    });

                    // Nếu có tỉnh cũ → load quận
                    const selectedProvince = provinces.find(p => p.name === oldProvince);
                    if (selectedProvince) {
                        loadDistricts(selectedProvince.code, oldDistrict, oldWard);
                    }
                });

                // Khi chọn tỉnh → load quận
                $('#province').on('change', function() {
                    const provinceCode = $(this).val();
                    loadDistricts(provinceCode);
                });

                // Hàm load quận
                function loadDistricts(provinceCode, selectedDistrictName = null, selectedWardName = null) {
                    if (!provinceCode) return;
                    $('#district').html('<option>Đang tải...</option>').prop('disabled', true);
                    $('#ward').html('<option>-- Chọn Phường/Xã --</option>').prop('disabled', true);

                    $.get(`https://provinces.open-api.vn/api/p/${provinceCode}?depth=2`, function(provinceData) {
                        $('#district').html('<option value="">-- Chọn Quận/Huyện --</option>');
                        provinceData.districts.forEach(d => {
                            const selected = d.name === selectedDistrictName ? 'selected' : '';
                            $('#district').append(
                                `<option value="${d.code}" ${selected}>${d.name}</option>`);
                        });
                        $('#district').prop('disabled', false);

                        // Nếu có quận cũ → load phường
                        const selectedDistrict = provinceData.districts.find(d => d.name ===
                            selectedDistrictName);
                        if (selectedDistrict && selectedWardName) {
                            loadWards(selectedDistrict.code, selectedWardName);
                        }
                    });
                }

                // Khi chọn quận → load phường
                $('#district').on('change', function() {
                    const districtCode = $(this).val();
                    loadWards(districtCode);
                });

                // Hàm load phường
                function loadWards(districtCode, selectedWardName = null) {
                    if (!districtCode) return;
                    $('#ward').html('<option>Đang tải...</option>').prop('disabled', true);

                    $.get(`https://provinces.open-api.vn/api/d/${districtCode}?depth=2`, function(districtData) {
                        $('#ward').html('<option value="">-- Chọn Phường/Xã --</option>');
                        districtData.wards.forEach(w => {
                            const selected = w.name === selectedWardName ? 'selected' : '';
                            $('#ward').append(
                                `<option value="${w.code}" ${selected}>${w.name}</option>`);
                        });
                        $('#ward').prop('disabled', false);
                    });
                }

                // Ghép địa chỉ khi submit
                $('form').on('submit', function() {
                    const province = $('#province option:selected').text();
                    const district = $('#district option:selected').text();
                    const ward = $('#ward option:selected').text();
                    const street = $('#street').val();

                    const fullAddress = `${street}, ${ward}, ${district}, ${province}`;
                    $('#full_address').val(fullAddress);
                });

                // HẾT PHẦN LOAD ĐỊA CHỈ API

                // Khởi tạo tất cả tooltips khi trang tải
                if ($.fn.tooltip) { // Kiểm tra nếu Bootstrap tooltip đã được tải
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }

                // --- HÀM TIỆN ÍCH (giữ nguyên hoặc điều chỉnh nếu cần) ---
                function updateActiveCommentUIAfterAction(commentId, newStatusText, newStatusClassBadge,
                    newActionsHtml) {
                    // Cập nhật trạng thái
                    $('#comment-status-cell-' + commentId).html(
                        `<span class="badge ${newStatusClassBadge}">${newStatusText}</span>`);
                    // Cập nhật hành động
                    const actionsCell = $('#comment-actions-cell-' + commentId);
                    actionsCell.html(newActionsHtml);
                    // Khởi tạo lại tooltips cho các nút mới
                    if ($.fn.tooltip) {
                        actionsCell.find('[data-bs-toggle="tooltip"]').tooltip('dispose').tooltip();
                    }
                }

                function updateTotalCommentsBadgeOnTab(newCount) {
                    const badgeElement = $('.nav-link[data-tab="comments"] .badge');
                    if (newCount > 0) {
                        badgeElement.text(newCount).show();
                    } else {
                        badgeElement.hide();
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

                // ++++ LOGIC CHUYỂN ĐỔI TAB (QUAN TRỌNG) ++++
                const initialTab = "{{ $tab }}"; // Lấy tab hiện tại từ Blade
                // Ẩn tất cả tab content
                $('.tab-content-item').removeClass('active');
                // Hiển thị tab content tương ứng với initialTab và thêm class 'active' cho nav-link
                $('#' + initialTab).addClass('active');
                $('.nav-link[data-tab="' + initialTab + '"]').addClass('active');

                $('.nav-link').on('click', function() {
                    // Xóa active class khỏi tất cả các nav-link và tab-content
                    $('.nav-link').removeClass('active');
                    $('.tab-content-item').removeClass('active');

                    // Lấy ID của tab được click
                    var tabId = $(this).data('tab');

                    // Thêm active class cho nav-link được click
                    $(this).addClass('active');

                    // Hiển thị tab content tương ứng
                    $('#' + tabId).addClass('active');

                    // Cập nhật URL mà không cần tải lại trang
                    history.pushState(null, '', '/profile/' + tabId);

                    // Nếu tab là 'orders', tự động kích hoạt collapse đầu tiên nếu có
                    if (tabId === 'orders') {
                        const firstOrderCollapse = $('#ordersAccordion .collapse').first();
                        if (firstOrderCollapse.length && !firstOrderCollapse.hasClass('show')) {
                            firstOrderCollapse.collapse('show');
                            // Cập nhật icon của panel đang mở
                            const cardBodyToggle = firstOrderCollapse.prev(
                                '.card-header'); // Đã đổi từ .card-body.cursor-pointer sang .card-header
                            cardBodyToggle.find('.order-toggle-icon')
                                .removeClass('fa-chevron-down')
                                .addClass('fa-chevron-up');
                        }
                    }
                });


                // ++++ XỬ LÝ COLLAPSE CHO ĐƠN HÀNG (TRONG TAB "ĐƠN HÀNG") ++++
                // Bắt sự kiện khi một panel collapse ĐƯỢC KÍCH HOẠT (show)
                $('#ordersAccordion').on('show.bs.collapse', '.collapse', function() {
                    const currentCollapseId = $(this).attr('id');

                    // Đóng tất cả các panel khác trong cùng tab và reset icon của chúng
                    $('#ordersAccordion .collapse').each(function() {
                        if ($(this).attr('id') !== currentCollapseId && $(this).hasClass('show')) {
                            $(this).collapse('hide');
                            // Cập nhật icon của panel đang đóng
                            $(this).prev('.card-header').find(
                                    '.order-toggle-icon'
                                ) // Đã đổi từ .card-body.cursor-pointer sang .card-header
                                .removeClass('fa-chevron-up')
                                .addClass('fa-chevron-down');
                        }
                    });

                    // Cập nhật icon của panel đang mở (nếu cần, vì show.bs.collapse đã tự động)
                    const cardHeaderToggle = $(this).prev(
                        '.card-header'); // Đã đổi từ .card-body.cursor-pointer sang .card-header
                    cardHeaderToggle.find('.order-toggle-icon')
                        .removeClass('fa-chevron-down')
                        .addClass('fa-chevron-up');
                });

                // Bắt sự kiện khi một panel collapse ĐÓNG LẠI (hide)
                $('#ordersAccordion').on('hide.bs.collapse', '.collapse', function() {
                    // Cập nhật icon của panel đang đóng
                    const cardHeaderToggle = $(this).prev(
                        '.card-header'); // Đã đổi từ .card-body.cursor-pointer sang .card-header
                    cardHeaderToggle.find('.order-toggle-icon')
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
                                    <td>${comment.content_full}</td> {{-- Dùng content_full để hiển thị toàn bộ nội dung --}}
                                    <td>${comment.deleted_at_formatted}</td> {{-- Dùng deleted_at_formatted nếu có --}}
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
                                        .tooltip('dispose').tooltip();
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
                $('#comments').on('click', '.view-comment-details-btn', function() {
                    const button = $(this);
                    const commentId = button.data('commentId');
                    const detailRow = $('#comment-detail-row-' + commentId);
                    const detailContentDiv = $('#comment-detail-content-' + commentId);

                    // Ẩn tất cả các chi tiết bình luận khác và reset icon
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
                            "{{ route('detailWithProduct', ['comment' => ':commentId']) }}";
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
                                        const productImageUrl = response.product.image_url ||
                                            'https://placehold.co/70x70/EBF0F5/7F8EA3?text=SP';

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
                                    showTemporaryMessage(response.message || 'Hành động thất bại.',
                                        'error');
                                    detailContentDiv.html(
                                        '<p class="text-danger small">Lỗi tải chi tiết.</p>');
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

                // ---- XỬ LÝ CHO CÁC NÚT THAY ĐỔI TRẠNG THÁI (TRONG TAB COMMENTS) ----
                $('#comments').on('click', '.change-comment-status-btn', function() {
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
                    } else if (actionType ===
                        'show_again') { // Có vẻ bạn không dùng show_again trong các nút hiện tại
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
                $('#comments').on('click', '.soft-delete-comment-btn', function() {
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
                                // Nếu thùng rác đang mở, đóng và mở lại để cập nhật
                                if ($('#trashedCommentsSection').is(':visible')) {
                                    $('#toggleTrashedCommentsBtn').click(); // Đóng
                                    setTimeout(function() {
                                        $('#toggleTrashedCommentsBtn')
                                            .click(); // Mở lại để refresh
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

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const toggleCurrentPassword = document.getElementById('toggleCurrentPassword');
                const currentPasswordInput = document.getElementById('current_password');

                if (toggleCurrentPassword && currentPasswordInput) {
                    toggleCurrentPassword.addEventListener('click', function() {
                        const type = currentPasswordInput.getAttribute('type') === 'password' ? 'text' :
                            'password';
                        currentPasswordInput.setAttribute('type', type);
                        this.querySelector('i').classList.toggle('fa-eye');
                        this.querySelector('i').classList.toggle('fa-eye-slash');
                    });
                }

                const toggleNewPassword = document.getElementById('toggleNewPassword');
                const newPasswordInput = document.getElementById('new_password');

                if (toggleNewPassword && newPasswordInput) {
                    toggleNewPassword.addEventListener('click', function() {
                        const type = newPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                        newPasswordInput.setAttribute('type', type);
                        this.querySelector('i').classList.toggle('fa-eye');
                        this.querySelector('i').classList.toggle('fa-eye-slash');
                    });
                }

                const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
                const confirmPasswordInput = document.getElementById('new_password_confirmation');

                if (toggleConfirmPassword && confirmPasswordInput) {
                    toggleConfirmPassword.addEventListener('click', function() {
                        const type = confirmPasswordInput.getAttribute('type') === 'password' ? 'text' :
                            'password';
                        confirmPasswordInput.setAttribute('type', type);
                        this.querySelector('i').classList.toggle('fa-eye');
                        this.querySelector('i').classList.toggle('fa-eye-slash');
                    });
                }
            });
        </script>
    @endpush
