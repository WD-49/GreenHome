@extends('layouts.app')

@section('content')
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

    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}
        </div>
    @endif
    <head>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
            integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
    </head>
    <!-- Breadcrumb -->
    <section class="section-breadcrumb">
        <div class="cr-breadcrumb-image">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="cr-breadcrumb-title">
                            <h2>Đăng nhập</h2>
                            <span> <a href="index.html">Home</a> - Đăng nhập</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Login -->
    <section class="section-login padding-tb-100">
        <div class="container">
            <div class="row d-none">
                <div class="col-lg-12">
                    <div class="mb-30" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                        <div class="cr-banner">
                            <h2>Login</h2>
                        </div>
                        <div class="cr-banner-sub-title">

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="cr-login" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                        <div class="form-logo">
                            <img src="{{ asset('assets_client/assets/img/logo/logo.png') }}" alt="">
                        </div>
                        <form class="cr-content-form" method="POST" action="{{ route('login') }}">@csrf
                            <div class="form-group">
                                <label>Email *</label>
                                <input id="email" type="email" placeholder="Nhập email"
                                    class="cr-form-control  @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Mật khẩu*</label>
                                <div class="d-flex">
                                    <input id="password" type="password" placeholder="Nhập mật khẩu"
                                        class="cr-form-control @error('password') is-invalid @enderror" name="password"
                                        autocomplete="current-password">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword"
                                        style="border-left: none;">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="remember">
                                <span class="form-group custom" style="display: none;">
                                    <input type="checkbox" name="remember" id="remember" checked>
                                    <label for="remember"></label>
                                </span>
                                <a class="link" href="{{ route('forgot-password.form') }}">Quên mật khẩu?</a>
                            </div><br>
                            <div class="login-buttons">
                                <button type="submit" class="cr-button">Đăng nhập</button>
                                <a href="{{ route('register') }}" class="link">
                                    Bạn chưa có tài khoản?
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    @include('client.partials.sideTool')
@endsection

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleCurrentPassword = document.getElementById('toggleCurrentPassword');
        // Sửa ID của input mật khẩu từ 'current_password' thành 'password'
        const currentPasswordInput = document.getElementById('password');

        if (toggleCurrentPassword && currentPasswordInput) {
            toggleCurrentPassword.addEventListener('click', function() {
                const type = currentPasswordInput.getAttribute('type') === 'password' ? 'text' :
                    'password';
                currentPasswordInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
        }

        // Các phần mã dưới đây (toggleNewPassword, toggleConfirmPassword) không cần thiết
        // cho trang đăng nhập này vì không có các trường mật khẩu mới/xác nhận.
        // Bạn có thể giữ hoặc xóa chúng tùy ý nếu chúng chỉ dành cho trang profile.
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