<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <title>@yield('title')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('assets/img/favicon/apple-touch-icon.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/img/favicon/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/img/favicon/favicon-16x16.png') }}">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link rel="manifest" href="{{ asset('assets/img/favicon/site.webmanifest') }}">
    <link rel="mask-icon" href="{{ asset('assets/img/favicon/safari-pinned-tab.svg') }}" color="#ffffff">
    <meta name="theme-color" content="#ffffff">

    <!-- Sweet Alert -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.min.css') }}">

    <!-- Font Awesome 6 - CDN -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">


    <!-- Notyf -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/notyf/notyf.min.css') }}">

    <!-- Volt CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/volt.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
    {{-- <style>
        .sidebar {
            background: linear-gradient(180deg, #2d3748 0%, #1a202c 100%);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
            font-family: 'Inter', sans-serif;
        }

        .sidebar-inner {
            padding: 1.5rem 1rem !important;
        }

        .nav-link {
            border-radius: 8px;
            margin: 0.25rem 0;
            padding: 0.75rem 1rem !important;
            transition: all 0.3s ease;
            color: #e2e8f0 !important;
        }

        .nav-link:hover {
            background-color: #4a5568;
            color: #ffffff !important;
            transform: translateX(5px);
        }

        .nav-link.active {
            background-color: #4a5568;
            color: #ffffff !important;
        }

        .sidebar-icon {
            margin-right: 0.75rem;
            font-size: 1.25rem;
            vertical-align: middle;
        }

        .sidebar-text {
            font-size: 0.95rem;
            font-weight: 500;
            vertical-align: middle;
        }

        .collapse {
            transition: all 0.3s ease;
        }

        .nav-item .collapse .nav-link {
            padding-left: 2.5rem !important;
            font-size: 0.9rem;
            color: #cbd5e0 !important;
        }

        .nav-item .collapse .nav-link:hover {
            color: #ffffff !important;
            background-color: #718096;
        }

        .dropdown-toggle::after {
            transition: transform 0.3s ease;
        }

        .nav-link[aria-expanded="true"] .dropdown-toggle::after {
            transform: rotate(180deg);
        }

        .user-card {
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding-bottom: 1rem !important;
        }

        .user-card img {
            border: 2px solid #ffffff;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary {
            background-color: #4a5568;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-secondary:hover {
            background-color: #718096;
        }
    </style> --}}
</head>

<body>
    @if (session('success'))
        <div class="alert alert-success custom-toast-alert-center" role="alert" id="success-alert">
            <div class="flex-grow-1">
                <strong><i>{{ session('success') }}</i></strong>
            </div>
            <button type="button" class="btn-close ms-3" data-bs-dismiss="alert" aria-label="Đóng"></button>
        </div>
    @endif




    @include('admin.partials.sidebar')
    <main class="content">
        @include('admin.partials.nav')


        @yield('content')

        @include('admin.partials.footer')
    </main>


    <!-- Core -->
    <script src="{{ asset('assets/vendor/@popperjs/core/dist/umd/popper.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/bootstrap/dist/js/bootstrap.min.js') }}"></script>

    <!-- Vendor JS -->
    <script src="{{ asset('assets/vendor/onscreen/dist/on-screen.umd.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/nouislider/distribute/nouislider.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/smooth-scroll/dist/smooth-scroll.polyfills.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chartist/dist/chartist.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/chartist-plugin-tooltips/dist/chartist-plugin-tooltip.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/vanillajs-datepicker/dist/js/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/sweetalert2/dist/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/notyf/notyf.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/simplebar/dist/simplebar.min.js') }}"></script>

    <!-- Moment JS (CDN) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.27.0/moment.min.js"></script>

    <!-- Volt JS -->
    <script src="{{ asset('assets/js/volt.js') }}"></script>
    <script src="{{ asset('assets/js/custom.js') }}"></script>

</body>

</html>
