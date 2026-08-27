<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $page }}</title>

    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/pos.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/toastr/toastr.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/select3/dist/css/select2.min.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{asset('assets/vendors/slick/slick.min.css')}}" />
    <link rel="stylesheet" type="text/css" href="{{asset('assets/vendors/slick/slick-theme.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
    @yield('style')
</head>

<body>
    <div id="app" class="hide-print"> 

        <!-- Sidebar Component -->
        <x-pos.sidebar-component></x-pos.sidebar-component>
        <!-- End Sidebar -->

        <div id="main" class='layout-navbar '>
            <!-- Header Component -->
            <x-pos.register-header-component></x-pos.register-header-component>
            <!-- End Header -->

            <div id="main-register">

                @yield('content')

                <!-- Footer Component -->
                <x-pos.footer-component></x-pos.footer-component>
                <!-- End Footer -->
            </div>
        </div>
    </div>



    <script src="{{ asset('assets/jquery-3.3.1.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
    <script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/fontawesome/all.min.js') }}"></script>

    <script src="{{ asset('assets/vendors/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/toastr/evolution.js') }}"></script>
    <script src="{{ asset('assets/vendors/slick/slick.min.js')}}"></script>
    <script src="{{ asset('assets/vendors/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/sweetalert/evolution.js') }}"></script>
    <script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/scanner/scanner.js') }}"></script>
    <script src="{{ asset('assets/vendors/select3/dist/js/select2.full.min.js') }}"></script> 
    <script src="{{ asset('js/connection.js') }}"></script>
    @yield('scripts')
</body>

</html>