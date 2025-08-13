{{-- resources/views/auth/passwords/email.blade.php --}}

@extends('layouts.app')

@section('content')
    <section class="section-breadcrumb">
        <div class="cr-breadcrumb-image">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="cr-breadcrumb-title">
                            <h2>Khôi phục mật khẩu</h2>
                            <span> <a href="#">Home</a> - Khôi phục mật khẩu</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section-login padding-tb-100">
        <div class="container">
            <div class="row d-none">
                <div class="col-lg-12">
                    <div class="mb-30" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                        <div class="cr-banner">
                            <h2>Forgot Password</h2>
                        </div>
                        <div class="cr-banner-sub-title">
                            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt
                                ut labore lacus vel facilisis. </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="cr-login" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                        <div class="form-logo">
                            <img width="50%" src="{{ asset('assets_client/assets/img/logo/GreenHome_logo.png') }}"
                                alt="">
                        </div>
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        <form class="cr-content-form" method="POST" action="{{ route('forgot-password.handle') }}">
                            @csrf
                            <div class="form-group">
                                <label>Email*</label>
                                <input id="email" type="email" placeholder="Nhập email của bạn"
                                    class="cr-form-control @error('email') is-invalid @enderror" name="email"
                                    value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="login-buttons">
                                <button type="submit" class="cr-button">Gửi</button>
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

    @include('client.partials.sideTool')
@endsection
