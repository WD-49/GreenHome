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
                            <h2>Đăng ký</h2>
                            <span> <a href="index.html">Home</a> - Đăng ký</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Register -->
    <section class="section-register padding-tb-100">
        <div class="container">
            <div class="row d-none">
                <div class="col-lg-12">
                    <div class="mb-30" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                        <div class="cr-banner">
                            <h2>Register</h2>
                        </div>

                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="cr-register" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                        <div class="form-logo">
                            <img width="50%" src="{{ asset('assets_client/assets/img/logo/GreenHome_logo.png') }}"
                                alt="">
                        </div>
                        <form class="cr-content-form" method="POST" action="{{ route('register') }}">
                            @csrf
                            <div class="row">
                                <div class="col-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Họ và tên*</label>
                                        <input id="name" type="text" placeholder="Enter Your First Name"
                                            class="cr-form-control @error('name') is-invalid @enderror" name="name"
                                            value="{{ old('name') }}" autocomplete="name" autofocus>
                                        @error('name')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Email*</label>
                                        <input id="email" type="email" placeholder="Enter Your email"
                                            class="cr-form-control @error('email') is-invalid @enderror" name="email"
                                            value="{{ old('email') }}" autocomplete="email">
                                        @error('email')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Mật khẩu*</label>
                                        <input id="password" type="password" placeholder="Nhập nhập mật khẩu"
                                            class="cr-form-control @error('password') is-invalid @enderror" name="password"
                                            autocomplete="new-password">
                                        @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Xác nhận mật khẩu*</label>
                                        <input id="password-confirm" type="password" placeholder="Nhập lại mật khẩu"
                                            class="cr-form-control" name="password_confirmation"
                                            autocomplete="new-password">
                                    </div>
                                </div>
                                <div class="cr-register-buttons">
                                    <button type="submit" class="cr-button">Đăng ký</button>
                                    <a href="{{ route('login') }}" class="link">
                                        Bạn đã có tài khoản?
                                    </a>
                                </div>
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
