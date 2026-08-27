<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="POSHUB - Platform Enterprise ERP & Omnichannel Point of Sale" name="description">
    <meta content="POSHUB" name="author">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{$page}}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="{{asset('theme/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('theme/css/typography.css')}}">
    <link rel="stylesheet" href="{{asset('theme/css/style.css')}}">
    <link rel="stylesheet" href="{{asset('theme/css/theme.css')}}">
    <link rel="stylesheet" href="{{asset('theme/css/responsive.css')}}">
    <link rel="stylesheet" href="{{asset('assets/vendors/toastr/toastr.min.css')}}">
    <link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
    @yield('styles')
</head>

<body class="icon-with-text">
    <div id="loader"></div>
    <div id="sound"></div>
    <div id="pageName" class="d-none">{{$page}}</div>
    <x-admin.lang-component></x-admin.lang-component>

    <div id="loading">
        <div id="background"></div>
        <div id="logocontainer">
            <div id="pelogo">POSHUB</div>
            <div class="loader" style="left:2vh; top:0; height:2vh; width:0; animation:slide1 1s linear forwards infinite"></div>
            <div class="loader" style="right:0; top:2vh; width:2vh; height:0; animation:slide2 1s linear forwards infinite; animation-delay:0.5s"></div>
            <div class="loader" style="right:2vh; bottom:0; height:2vh; width:0; animation:slide3 1s linear forwards infinite"></div>
            <div class="loader" style="left:0; bottom:2vh; width:2vh; height:0; animation:slide4 1s linear forwards infinite; animation-delay:0.5s"></div>
        </div>
    </div>
    <div class="wrapper">

        <!-- Sidebar Component -->
        <x-admin.sidebar-component></x-admin.sidebar-component>
        <!-- End Sidebar Component -->

        <!-- Header Component -->
        <x-admin.header-component></x-admin.header-component>
        <!-- End Header Component -->

        @yield('content')

        <!-- Footer Component -->
        <x-admin.footer-component></x-admin.footer-component>
        <!-- Footer END -->
    </div>

    <script type="text/javascript">
    window.Laravel = {
        csrfToken: "{{ csrf_token() }}",
        jsPermissions: {!! auth()->check() ? auth()->user()->jsPermissions() : 0 !!}
    }
</script>

    <script src="{{asset('theme/js/jquery.min.js')}}"></script>
    <script src="{{asset('theme/js/popper.min.js')}}"></script>
    <script src="{{asset('theme/js/bootstrap.min.js')}}"></script>
    <script src="{{asset('theme/js/jquery.appear.js')}}"></script>
    <script src="{{asset('theme/js/wow.min.js')}}"></script>
    <script src="{{asset('theme/js/jquery.magnific-popup.min.js')}}"></script>
    <script src="{{asset('theme/js/smooth-scrollbar.js')}}"></script>
    <script src="{{asset('theme/js/custom.js')}}"></script>

    <script src="{{ asset('assets/vendors/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/toastr/evolution.js') }}"></script>
    <script src="{{ asset('assets/vendors/sweetalert/sweetalert2.all.min.js')}}"></script>
    <script src="{{ asset('assets/vendors/sweetalert/evolution.js')}}"></script>
    <script src="{{ asset('js/lang.js') }}"></script>
    <script src="{{ asset('js/connection.js') }}"></script>
    <script src="{{ asset('js/main.js') }}"></script>
    @yield('scripts')
</body>

</html>