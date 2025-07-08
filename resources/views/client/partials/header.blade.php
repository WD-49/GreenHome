<div class="container">
    <div class="row">
        <div class="col-lg-12">
            <div class="top-header">
                <a href="index.html" class="cr-logo">
                    <img src="{{ asset('assets_client/assets/img/logo/logo.png') }}" alt="logo" class="logo">
                    <img src="{{ asset('assets_client/assets/img/logo/dark-logo.png') }}" alt="logo"
                        class="dark-logo">
                </a>
                <form class="cr-search">
                    <input class="search-input" type="text" placeholder="Search For items...">
                    <select class="form-select" aria-label="Default select example">
                        <option selected>All Categories</option>
                        <option value="1">Mens</option>
                        <option value="2">Womens</option>
                        <option value="3">Electronics</option>
                    </select>
                    <a href="javascript:void(0)" class="search-btn">
                        <i class="ri-search-line"></i>
                    </a>
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
                                            href="{{ route('profile.index') }}">Tài khoản của tôi</a>
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
  @auth
    @if (!Auth::user()->hasVerifiedEmail())
        <a href="{{ route('profile.index') }}"
           class="cr-right-bar-item"
           data-bs-toggle="tooltip"
           data-bs-placement="bottom"
           title="Email chưa xác minh. Nhấn để xác minh!">
            <i class="ri-alert-line" style="color: #ffc107;"></i>
            <span> Cần Xác minh</span>
        </a>
    @endif
@endauth



                    <a href="javascript:void(0)" class="cr-right-bar-item Shopping-toggle">
                        <i class="ri-shopping-cart-line"></i>
                    </a>
                    <a href="javascript:void(0)" class="cr-right-bar-item voucher-toggle">
                        <i class="ri-ticket-line"></i>
                        <span>Voucher</span>
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
                                                            <a href="{{ route('product.show', $product->slug) }}">
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
                                Home
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
                                            <a class="dropdown-item" href="{{ route('category.show', $category->slug) }}">

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

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="{{ route('blog.index') }}">
                                Bài viết
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="">Left
                                        Sidebar</a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="blog-detail-full-width.html">Detail
                                        Full
                                        Width</a>
                                </li>
                            </ul>
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
<script>
    function markAsRead(id) {
        fetch('/notifications/' + id + '/read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (el) {
            return new bootstrap.Tooltip(el);
        });
    });
</script>
