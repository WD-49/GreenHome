{{-- layouts.admin.blade.php --}}
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>@yield('title', 'Trang Quản Trị')</title> {{-- Thêm tiêu đề mặc định --}}
    <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- Đặt CSRF token ở đây --}}

    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/images/logos/favicon.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}" /> {{-- custom.css nên ở sau styles.min.css để ghi đè nếu cần --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2&display=swap" rel="stylesheet">

    @stack('styles') {{-- Cho CSS tùy chỉnh từ các trang con --}}
</head>

<body>
    @if (session('success'))
    <div class="alert alert-success custom-toast-alert-center" role="alert" id="success-alert" style="position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 9999; min-width: 300px;">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-2"></i>
            <div class="flex-grow-1">
                <strong><i>{{ session('success') }}</i></strong>
            </div>
            <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    </div>
    @endif

    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full">
        @include('admin.partials.sidebar')
        <div class="body-wrapper">
            @include('admin.partials.header')
            <div class="body-wrapper-inner">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset('assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script> {{-- Quan trọng: bundle đã bao gồm Popper.js --}}

    <script src="{{ asset('assets/js/sidebarmenu.js') }}"></script>
    <script src="{{ asset('assets/js/app.min.js') }}"></script>
    <script src="{{ asset('assets/libs/apexcharts/dist/apexcharts.min.js') }}"></script>
    <script src="{{ asset('assets/libs/simplebar/dist/simplebar.js') }}"></script>
    <script src="{{ asset('assets/js/dashboard.js') }}"></script> {{-- File này bạn đã sửa --}}
    <script src="{{ asset('assets/js/custom.js') }}"></script>

    {{-- Các thư viện JS khác nếu cần toàn cục (ví dụ Summernote, Moment) --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.20/dist/summernote-bs4.min.js"></script> --}}
    {{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script> --}}
    {{-- <script src="https://cdn.jsdelivr.net/npm/iconify-icon@1.0.8/dist/iconify-icon.min.js"></script> --}}

    @stack('scripts') {{-- Nơi các script từ trang con sẽ được đẩy vào --}}

</body>
</html>