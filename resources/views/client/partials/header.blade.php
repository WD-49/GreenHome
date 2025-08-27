<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="top-header">
                <a href="{{ route('home') }}" class="cr-logo">
                    <img src="{{ asset('assets_client/assets/img/logo/GreenHome_logo.png') }}" alt="logo"
                        class="logo">

                    <img src="{{ asset('assets_client/assets/img/logo/dark-logo.png') }}" alt="logo"
                        class="dark-logo">
                </a>
                <form class="cr-search" action="{{ route('shop.index') }}" method="GET">
                    <input class="search-input" type="text" name="search" value="{{ request('search') }}"
                        placeholder="Tìm Kiếm...">
                    <button type="submit" class="search-btn">
                        <i class="ri-search-line"></i>
                    </button>
                </form>
                <div class="cr-right-bar">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle cr-right-bar-item" href="javascript:void(0)">
                                <i class="ri-user-3-line"></i>

                                <span>
                                    @auth
                                        {{ $authUser->name }}
                                    @else
                                        Account
                                    @endauth
                                </span>
                            </a>

                            <ul class="dropdown-menu">
                                @guest
                                    <li>
                                        <a class="dropdown-item" href="{{ route('register') }}">Đăng ký</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('login') }}">Đăng nhập</a>
                                    </li>
                                @else
                                    <li>
                                        <a class="dropdown-item"
                                            style="display: block; width: 100%; padding: 0.5rem 1rem; color: #212529; text-align: inherit; background-color: transparent; border: 0; font-size: 1rem;"
                                            href="{{ route('profile.index') }}">Thông tin cá nhân</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item"
                                            style="display: block; width: 100%; padding: 0.5rem 1rem; color: #212529; text-align: inherit; background-color: transparent; border: 0; font-size: 1rem;"
                                            href="{{ route('orders.list') }}">Đơn hàng</a>
                                    </li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST">
                                            @csrf
                                            <button type="submit" class="dropdown-item">Đăng xuất</button>
                                        </form>
                                    </li>
                                @endguest
                            </ul>
                        </li>
                    </ul>
                    <a href="{{ route('wishlist.index') }}" class="cr-right-bar-item">
                        <i class="ri-heart-3-line"></i>
                    </a>
                    <meta name="csrf-token" content="{{ csrf_token() }}"> <!-- Đảm bảo CSRF token có sẵn -->

                    @auth
                        @php
                            $unreadCount = Auth::user()->unreadNotifications->count();
                            Log::info('Unread notifications count', ['count' => $unreadCount]);
                        @endphp

                        <!-- Nút chuông: kích hoạt offcanvas -->
                        <a href="javascript:void(0)" class="cr-right-bar-item" data-bs-toggle="offcanvas"
                            data-bs-target="#notificationPanel" aria-controls="notificationPanel"
                            style="position: relative;" id="notificationBell">
                            <i class="ri-notification-3-line" style="font-size: 22px; position: relative;">
                                @if ($unreadCount > 0)
                                    <span class="notification-count" id="notificationCount">{{ $unreadCount }}</span>
                                @endif
                            </i>
                        </a>

                        <!-- Offcanvas Panel: thông báo -->
                        <div class="offcanvas offcanvas-end" tabindex="-1" id="notificationPanel"
                            aria-labelledby="notificationPanelLabel">
                            <div class="offcanvas-header">
                                <h5 class="offcanvas-title" id="notificationPanelLabel">Thông báo</h5>
                                <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas"
                                    aria-label="Close"></button>
                            </div>
                            <div class="offcanvas-body p-3">
                                @forelse (Auth::user()->notifications as $notification)
                                    @php
                                        Log::info('Notification ID in Blade', ['id' => $notification->id]);
                                    @endphp
                                    <div class="card mb-3 shadow-sm {{ !$notification->read_at ? 'border-primary' : '' }}"
                                        style="border-left: 4px solid {{ !$notification->read_at ? '#0d6efd' : '#e9ecef' }};">
                                        <div class="card-body p-3 d-flex align-items-start gap-3">
                                            <i
                                                class="{{ $notification->data['icon'] ?? 'ri-notification-line' }} fs-4 text-muted"></i>
                                            <div class="flex-grow-1">
                                                @if (!empty($notification->data['url']))
                                                    <a href="javascript:void(0)"
                                                        class="d-block text-{{ !$notification->read_at ? 'primary' : 'dark' }} text-decoration-none notification-link"
                                                        style="font-weight: bold;"
                                                        data-notification-id="{{ $notification->id }}"
                                                        data-url="{{ $notification->data['url'] }}"
                                                        onclick="markAsRead('{{ $notification->id }}', '{{ $notification->data['url'] }}', event)">
                                                        {{ $notification->data['title'] ?? 'Thông báo mới' }}
                                                    </a>
                                                @else
                                                    <strong
                                                        class="d-block text-{{ !$notification->read_at ? 'primary' : 'dark' }}">
                                                        {{ $notification->data['title'] ?? 'Thông báo mới' }}
                                                    </strong>
                                                @endif
                                                <p class="mb-1 text-muted small">
                                                    {{ $notification->data['message'] ?? '...' }}</p>
                                                <span class="badge bg-light text-dark small">
                                                    {{ $notification->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <p class="text-muted text-center py-5">Không có thông báo nào.</p>
                                @endforelse
                            </div>
                        </div>
                    @endauth

                    <style>
                        .notification-count {
                            position: absolute;
                            top: 0px;
                            right: -4px;
                            background-color: red;
                            color: white;
                            font-size: 10px;
                            padding: 2px 5px;
                            border-radius: 999px;
                            line-height: 1;
                            font-weight: bold;
                            min-width: 16px;
                            height: 16px;
                            text-align: center;
                            display: inline-block;
                        }

                        .offcanvas-body {
                            max-height: 100%;
                            overflow-y: auto;
                            scrollbar-width: thin;
                            scrollbar-color: #888 #f1f1f1;
                            padding-right: 10px;
                        }

                        .offcanvas-body::-webkit-scrollbar {
                            width: 8px;
                        }

                        .offcanvas-body::-webkit-scrollbar-track {
                            background: #f1f1f1;
                            border-radius: 10px;
                        }

                        .offcanvas-body::-webkit-scrollbar-thumb {
                            background: #888;
                            border-radius: 10px;
                        }

                        .offcanvas-body::-webkit-scrollbar-thumb:hover {
                            background: #555;
                        }

                        .card {
                            transition: all 0.3s ease;
                            border-radius: 0.5rem;
                        }

                        .card:hover {
                            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                            transform: translateY(-2px);
                        }

                        .card.border-primary {
                            background-color: #f8f9fa;
                        }

                        .badge {
                            border-radius: 0.25rem;
                        }

                        .alert-warning {
                            background-color: #fff3cd;
                            border: 1px solid #ffeeba;
                        }

                        a.text-decoration-none:hover {
                            text-decoration: underline;
                        }
                    </style>

                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            function markAsRead(notificationId, url, event) {
                                console.log('markAsRead called with ID:', notificationId, 'and URL:', url);
                                event.preventDefault(); // Ngăn hành động mặc định

                                // Cập nhật giao diện
                                const titleElement = event.target;
                                titleElement.classList.remove('text-primary');
                                titleElement.classList.add('text-dark');
                                titleElement.closest('.card').style.borderLeftColor = '#e9ecef';
                                titleElement.closest('.card').classList.remove('border-primary');
                                titleElement.closest('.card').classList.add('border-secondary');

                                // Cập nhật số lượng thông báo chưa đọc
                                const countElement = document.getElementById('notificationCount');
                                if (countElement) {
                                    let unreadCount = parseInt(countElement.textContent || '0');
                                    if (unreadCount > 0) {
                                        unreadCount--;
                                        countElement.textContent = unreadCount;
                                        if (unreadCount === 0) {
                                            countElement.style.display = 'none';
                                        }
                                    }
                                }

                                // Redirect đến route để đánh dấu thông báo là đã đọc
                                window.location.href = '/notifications/' + notificationId + '/read-and-redirect?url=' +
                                    encodeURIComponent(url);
                            }

                            // Gắn hàm markAsRead vào window để sử dụng trong onclick
                            window.markAsRead = markAsRead;

                            // Khởi tạo tooltip
                            const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                            tooltipTriggerList.forEach(function(el) {
                                new bootstrap.Tooltip(el);
                            });
                        });
                    </script>

                    <a href="javascript:void(0)" class="cr-right-bar-item Shopping-toggle">
                        <i class="ri-shopping-cart-line"></i>
                    </a>
                    <a href="javascript:void(0)" class="cr-right-bar-item voucher-toggle">
                        <i class="ri-ticket-line"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="cr-fix" id="cr-main-menu-desk">
    <div class="container">
        <div class="cr-menu-list">
            <div class="cr-category-icon-block">
                <div class="cr-category-menu">
                    <div class="cr-category-toggle">
                        <i class="ri-menu-2-line"></i>
                    </div>
                </div>
                <div class="cr-cat-dropdown">
                    <div class="cr-cat-block">
                        <div class="cr-cat-tab">
                            <div class="cr-tab-list nav flex-column nav-pills" id="v-pills-tab" role="tablist"
                                aria-orientation="vertical">
                                @foreach ($menuCategories as $index => $cat)
                                    <button class="nav-link {{ $loop->first ? 'active' : '' }}"
                                        id="v-pills-tab-{{ $cat->id }}" data-bs-toggle="pill"
                                        data-bs-target="#v-pills-{{ $cat->id }}" type="button" role="tab"
                                        aria-controls="v-pills-{{ $cat->id }}"
                                        aria-selected="{{ $loop->first ? 'true' : 'false' }}">
                                        {{ $cat->name }}
                                    </button>
                                @endforeach
                                <a class="nav-link" href="{{ route('shop.index') }}">
                                    View All
                                </a>
                            </div>

                            <div class="tab-content" id="v-pills-tabContent">
                                @foreach ($menuCategories as $cat)
                                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}"
                                        id="v-pills-{{ $cat->id }}" role="tabpanel"
                                        aria-labelledby="v-pills-tab-{{ $cat->id }}">
                                        <div class="tab-list row">
                                            <div class="col">
                                                <h6 class="cr-col-title">{{ $cat->name }}</h6>
                                                <ul class="cat-list">
                                                    @forelse($cat->products as $product)
                                                        <li>
                                                            <a href="{{ route('productDetail', $product->slug) }}">
                                                                {{ $product->name }}
                                                            </a>
                                                        </li>
                                                    @empty
                                                        <li><em>Chưa có sản phẩm</em></li>
                                                    @endforelse
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <nav class="navbar navbar-expand-lg">
                <a href="javascript:void(0)" class="navbar-toggler shadow-none">
                    <i class="ri-menu-3-line"></i>
                </a>
                <div class="cr-header-buttons">
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="javascript:void(0)">
                                <i class="ri-user-3-line"></i>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="register.html">Register</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="checkout.html">Checkout</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="login.html">Login</a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                    <a href="wishlist.html" class="cr-right-bar-item">
                        <i class="ri-heart-line"></i>
                    </a>
                    <a href="javascript:void(0)" class="cr-right-bar-item Shopping-toggle">
                        <i class="ri-shopping-cart-line"></i>
                    </a>
                    <a href="javascript:void(0)" class="cr-right-bar-item Voucher-toggle">
                        <i class="ri-ticket-line"></i>
                    </a>
                </div>
                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">
                                Trang chủ
                            </a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="javascript:void(0)">
                                Danh Mục
                            </a>
                            <ul class="dropdown-menu">
                                @isset($categories3)
                                    @foreach ($categories3 as $category)
                                        <li>
                                            <a class="dropdown-item"
                                                href="{{ route('shop.index', ['categories[]' => $category->id]) }}">
                                                {{ $category->name }}
                                            </a>
                                        </li>
                                    @endforeach
                                @endisset
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('shop.index') }}">
                                Cửa Hàng
                            </a>
                        </li>

                        <li class="nav-item ">
                            <a class="nav-link" href="{{ route('blog.index') }}">
                                Bài viết
                            </a>
                        </li>
                        
                        <li class="nav-item dropdown">
                            <a class="nav-link" href="{{ route('support.index') }}">
                                Trợ giúp
                                 </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('contact.index') }}">
                                Liên Hệ
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            <div class="cr-calling">
                <i class="ri-phone-line"></i>
                <a href="tel:{{ $footerWebInfo['phone'] ?? '#' }}">
                    {{ $footerWebInfo['phone'] ?? 'Đang cập nhật...' }}
                </a>
            </div>
        </div>
    </div>
</div>
