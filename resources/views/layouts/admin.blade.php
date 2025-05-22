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


    <!-- Notyf -->
    <link rel="stylesheet" href="{{ asset('assets/vendor/notyf/notyf.min.css') }}">

    <!-- Volt CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/volt.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/custom.css') }}">
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
    {{-- <script>
        setTimeout(function() {
            let alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alertEl) {
                let alert = new bootstrap.Alert(alertEl);
                alert.close();
            });
        }, 2000);
    </script> --}}

</body>

</html>
