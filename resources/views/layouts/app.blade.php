<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="POSHUB - Platform Enterprise ERP & Omnichannel Point of Sale" name="description">
    <meta content="POSHUB" name="author">
    <title>{{$page}}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}" type="image/x-icon">
    <link id="style" href="{{asset('newtheme/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link id="style" href="{{asset('newtheme/css/icons.css')}}" rel="stylesheet">
    <link id="style" href="{{asset('newtheme/switcher/demo.css')}}" rel="stylesheet">
    <link id="style" href="{{asset('newtheme/switcher/demo/switcher.css')}}" rel="stylesheet">

    <link id="style" href="{{asset('newtheme/css/style.css')}}" rel="stylesheet">
    <link id="style" href="{{asset('newtheme/css/plugins.css')}}" rel="stylesheet"> 
    <link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
</head>

<body class="login-img">

    <!-- GLOBAL-LOADER -->
    <div id="global-loader">
        <img src="{{asset('newtheme/images/svgs/loader.svg')}}" class="loader-img" alt="Loader">
    </div>
    <!-- GLOBAL-LOADER -->

    <!-- PAGE -->
    <div class="page bg-img">

        @yield('content')

    </div>

    <!-- JQUERY JS -->
    <script src="{{asset('newtheme/plugins/jquery/jquery.min.js')}}"></script>

    <!-- BOOTSTRAP JS -->
    <script src="{{asset('newtheme/plugins/bootstrap/js/popper.min.js')}}"></script>
    <script src="{{asset('newtheme/plugins/bootstrap/js/bootstrap.min.js')}}"></script>

    @yield('scripts')

    <!-- APP JS-->
    <script src="{{asset('newtheme/js/themeColors.js')}}"></script>
    <script src="{{asset('newtheme/js/custom.js')}}"></script>
    <script src="{{asset('newtheme/js/custom-switcher.js')}}"></script>
    <!-- END SCRIPTS -->

</body>

</html>