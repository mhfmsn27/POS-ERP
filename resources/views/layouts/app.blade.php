<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="POSHUB - Platform Enterprise ERP & Omnichannel Point of Sale" name="description">
    <meta content="POSHUB Enterprise" name="author">
    <title>{{$page ?? 'Authentication'}} - POSHUB ENTERPRISE</title>
    
    <!-- Google Fonts: Inter (Professional Corporate Typography) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}" type="image/x-icon">
    <link id="style" href="{{asset('newtheme/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link id="style" href="{{asset('newtheme/css/icons.css')}}" rel="stylesheet">
    <link id="style" href="{{asset('newtheme/css/style.css')}}" rel="stylesheet">
    <link id="style" href="{{asset('newtheme/css/plugins.css')}}" rel="stylesheet"> 
    
    <!-- POSHUB Modern Solid Enterprise Design Tokens -->
    <link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
    @yield('styles')
</head>

<body class="login-img">

    <!-- GLOBAL-LOADER -->
    <div id="global-loader" style="background-color: #f8fafc;">
        <img src="{{asset('newtheme/images/svgs/loader.svg')}}" class="loader-img" alt="Loader">
    </div>
    <!-- GLOBAL-LOADER -->

    <!-- PAGE CONTENT -->
    <div class="page bg-img">
        @yield('content')
    </div>

    <!-- JQUERY & BOOTSTRAP JS -->
    <script src="{{asset('newtheme/plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('newtheme/plugins/bootstrap/js/popper.min.js')}}"></script>
    <script src="{{asset('newtheme/plugins/bootstrap/js/bootstrap.min.js')}}"></script>

    @yield('scripts')

    <!-- APP JS-->
    <script src="{{asset('newtheme/js/themeColors.js')}}"></script>
    <script src="{{asset('newtheme/js/custom.js')}}"></script>

    <script>
        (function($) {
            "use strict";
            function hideLoader() {
                $("#global-loader").fadeOut(250);
            }
            $(window).on("load", hideLoader);
            setTimeout(hideLoader, 500); // Safety fallback
        })(jQuery);
    </script>
</body>

</html>