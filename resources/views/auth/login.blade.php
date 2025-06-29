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
                                <input id="password" type="password" placeholder="Nhập mật khẩu"
                                    class="cr-form-control @error('password') is-invalid @enderror" name="password"
                                    autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
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
