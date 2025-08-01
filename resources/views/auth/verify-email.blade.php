@extends('layouts.app') {{-- Hoặc layout khác của bạn --}}

@section('content')
{{-- Trang Gửi Mail Xác Minh Email Khi Đăng Ký mới Ở Hệ thống --}}
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Xác minh địa chỉ Email của bạn') }}</div>

                    <div class="card-body">
                        <div class="mb-4 text-sm text-gray-600">
                            {{ __('Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, bạn có thể vui lòng xác minh địa chỉ email của mình bằng cách nhấp vào liên kết chúng tôi vừa gửi cho bạn qua email không? Nếu bạn không nhận được email, chúng tôi sẽ vui lòng gửi cho bạn một email khác.') }}
                        </div>

                        @if (session('status') == 'verification-link-sent')
                            <div class="mb-4 font-medium text-sm text-green-600">
                                {{ __('Một liên kết xác minh mới đã được gửi đến địa chỉ email bạn đã cung cấp khi đăng ký.') }}
                            </div>
                        @endif

                        <div class="mt-4 flex items-center justify-between">
                            <form method="POST" action="{{ route('verification.send') }}">
                                @csrf

                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Gửi lại Email xác minh') }}
                                    </button>
                                </div>
                            </form>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf

                                <button type="submit" class="btn btn-link text-danger">
                                    {{ __('Đăng xuất') }}
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection