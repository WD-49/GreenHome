@extends('layouts.app')

@section('title', 'Liên hệ')

@section('content')
    <section class="section-breadcrumb">
        <div class="cr-breadcrumb-image">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="cr-breadcrumb-title">
                            <h2>Liên Hệ</h2>
                            <span><a href="{{ route('home') }}">Trang Chủ</a> - Liên Hệ</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="section-Contact padding-tb-100">
        <div class="container">
            <!-- Tiêu đề -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="mb-30" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                        <div class="cr-banner">
                            <h2>Liên Hệ Ngay</h2>
                        </div>
                        <div class="cr-banner-sub-title">
                            <p>Chúng tôi luôn sẵn sàng lắng nghe và trao đổi để mang lại trải nghiệm tốt nhất cho bạn..</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thông tin liên hệ -->
            <div class="row mb-minus-24">
                <!-- Contact -->
                <div class="col-lg-4 col-md-6 col-12 mb-24" data-aos="fade-up" data-aos-duration="2000"
                    data-aos-delay="400">
                    <div class="cr-info-box">
                        <div class="cr-icon">
                            <i class="ri-phone-line"></i>
                        </div>
                        <div class="cr-info-content">
                            <h4 class="heading">Số Điện Thoại</h4>
                            <p><a href="tel:{{ $webInfo['phone'] ?? '' }}">
                                    <i class="ri-phone-line"></i> &nbsp; {{ $webInfo['phone'] ?? '' }}
                                </a></p>
                        </div>
                    </div>
                </div>

                <!-- Mail & Website -->
                <div class="col-lg-4 col-md-6 col-12 mb-24" data-aos="fade-up" data-aos-duration="2000"
                    data-aos-delay="600">
                    <div class="cr-info-box">
                        <div class="cr-icon">
                            <i class="ri-mail-line"></i>
                        </div>
                        <div class="cr-info-content">
                            <h4 class="heading">Email</h4>
                            <p><a href="mailto:{{ $webInfo['email'] ?? '' }}">
                                    <i class="ri-mail-line"></i> &nbsp; {{ $webInfo['email'] ?? '' }}
                                </a></p>
                            <p><a href="javascript:void(0)">
                                    <i class="ri-globe-line"></i> &nbsp; {{ $webInfo['web_name'] ?? '' }}
                                </a></p>
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="col-lg-4 col-12 mb-24" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="800">
                    <div class="cr-info-box">
                        <div class="cr-icon">
                            <i class="ri-map-pin-line"></i>
                        </div>
                        <div class="cr-info-content">
                            <h4 class="heading">Địa Chỉ</h4>
                            <p><a href="javascript:void(0)">
                                    <i class="ri-map-pin-line"></i> &nbsp; {{ $webInfo['address'] ?? '' }}
                                </a></p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Google Map + Form -->
            <div class="row padding-t-100 mb-minus-24">
                <div class="col-md-6 col-12 mb-24" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="400">
                    <iframe src="https://www.google.com/maps?q={{ urlencode($webInfo['address'] ?? '') }}&output=embed"
                        title="maps" width="100%" height="350" style="border:0;" allowfullscreen="" loading="lazy">
                    </iframe>

                </div>
                <div class="col-md-6 col-12 mb-24" data-aos="fade-up" data-aos-duration="2000" data-aos-delay="800">
                    <form class="cr-content-form" id="contactForm" method="POST" action="{{ route('contact.send') }}">
                        @csrf
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Họ và Tên" class="cr-form-control">
                            <small class="error text-danger" id="error-name"></small>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Email" class="cr-form-control">
                            <small class="error text-danger" id="error-email"></small>
                        </div>
                        <div class="form-group">
                            <input type="text" name="phone" placeholder="Số Điện Thoại" class="cr-form-control">
                            <small class="error text-danger" id="error-phone"></small>
                        </div>
                        <div class="form-group">
                            <textarea name="message" class="cr-form-control" rows="4" placeholder="Nội dung"></textarea>
                            <small class="error text-danger" id="error-message"></small>
                        </div>
                        <button type="submit" class="cr-button">Gửi</button>
                    </form>

                    @if (session('success'))
                        <p style="color:green;">{{ session('success') }}</p>
                    @endif

                    <script>
                        document.getElementById('contactForm').addEventListener('submit', function(e) {
                            e.preventDefault();

                            // Xóa lỗi cũ
                            document.querySelectorAll('.error').forEach(el => el.textContent = '');

                            let isValid = true;

                            // Lấy giá trị
                            let name = document.querySelector('[name="name"]').value.trim();
                            let email = document.querySelector('[name="email"]').value.trim();
                            let phone = document.querySelector('[name="phone"]').value.trim();
                            let message = document.querySelector('[name="message"]').value.trim();

                            // Validate Họ và tên
                            if (name === '') {
                                document.getElementById('error-name').textContent = 'Vui lòng nhập họ và tên';
                                isValid = false;
                            } else if (name.length < 3) {
                                document.getElementById('error-name').textContent = 'Họ và tên phải ít nhất 3 ký tự';
                                isValid = false;
                            }

                            // Validate Email
                            let emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                            if (email === '') {
                                document.getElementById('error-email').textContent = 'Vui lòng nhập email';
                                isValid = false;
                            } else if (!emailPattern.test(email)) {
                                document.getElementById('error-email').textContent = 'Email không hợp lệ';
                                isValid = false;
                            }

                            // Validate Số điện thoại (bắt buộc nhập)
                            if (phone === '') {
                                document.getElementById('error-phone').textContent = 'Vui lòng nhập số điện thoại';
                                isValid = false;
                            } else {
                                let phonePattern = /^(0|\+84)\d{9,10}$/;
                                if (!phonePattern.test(phone)) {
                                    document.getElementById('error-phone').textContent = 'Số điện thoại không hợp lệ';
                                    isValid = false;
                                }
                            }
                            // Validate Nội dung
                            if (message === '') {
                                document.getElementById('error-message').textContent = 'Vui lòng nhập nội dung';
                                isValid = false;
                            }

                            // Nếu hợp lệ thì submit
                            if (isValid) {
                                this.submit();
                            }
                        });
                    </script>
                </div>
            </div>
        </div>
    </section>
@endsection
