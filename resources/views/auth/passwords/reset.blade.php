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
    <style>
        .btn-social-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 18px;
            text-decoration: none;
        }

        .btn-google {
            background-color: #dd4b39;
        }

        .btn-facebook {
            background-color: #3b5998;
        }
    </style>

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
                            <h2>Đặt lại mật khẩu</h2>
                            <span> <a href="index.html">Home</a> - Đặt lại mật khẩu</span>
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
                        <form class="cr-content-form" method="POST" action="{{ route('password.update') }}">
                            @csrf

                            <input type="hidden" name="token" value="{{ $token }}">

                            <div class="form-group">
                                <label>{{ __('Email') }}</label>
                                <input id="email" type="email"
                                    class="form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <label>{{ __('Mật khẩu') }}</label>
                                <div class="d-flex">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="new-password">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleNewPassword">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group">
                                <label>{{ __('Xác nhận mật khẩu') }}</label>

                                <div class="d-flex">
                                    <input id="password-confirm" type="password" class="form-control"
                                        name="password_confirmation" required autocomplete="new-password">
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="fa fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-12">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Đặt lại mật khẩu') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
@push('scripts')
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
            const newPasswordInput = document.getElementById('password');

            if (toggleNewPassword && newPasswordInput) {
                toggleNewPassword.addEventListener('click', function() {
                    const type = newPasswordInput.getAttribute('type') === 'password' ? 'text' : 'password';
                    newPasswordInput.setAttribute('type', type);
                    this.querySelector('i').classList.toggle('fa-eye');
                    this.querySelector('i').classList.toggle('fa-eye-slash');
                });
            }

            const toggleConfirmPassword = document.getElementById('toggleConfirmPassword');
            const confirmPasswordInput = document.getElementById('password-confirm');

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
