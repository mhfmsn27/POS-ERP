<!DOCTYPE html>
<html lang="en" dir="ltr" style="--volgh-primary-rgb: 31, 87, 219; --volgh-primary-rgb1: #1f57db;">

<head>

    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="">
    <meta name="author" content="POSHUB Enterprise">
    <meta name="keywords" content="">

    <!-- TITLE -->
    <title>{{$page}}</title>

    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}" type="image/x-icon">
    <link id="style" href="{{asset('newtheme/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link id="style" href="{{asset('newtheme/css/icons.css')}}" rel="stylesheet">
    <link id="style" href="{{asset('newtheme/switcher/demo.css')}}" rel="stylesheet">
    <link id="style" href="{{asset('newtheme/switcher/css/switcher.css')}}" rel="stylesheet">

    <link id="style" href="{{asset('newtheme/css/style.css')}}" rel="stylesheet">
    <link id="style" href="{{asset('newtheme/css/plugins.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{asset('theme/css/theme.css')}}">
</head>

<body class="app ltr sidebar-mini light-mode">

    <!-- GLOBAL-LOADER -->
    <div id="global-loader">
        <img src="{{asset('newtheme/images/svgs/loader.svg')}}" class="loader-img" alt="Loader">
    </div>

    <!-- GLOBAL-LOADER -->
    <div id="app"></div>
    <!-- END PAGE-->

    <script src="{{asset('newtheme/plugins/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('newtheme/plugins/bootstrap/js/popper.min.js')}}"></script>
    <script src="{{asset('newtheme/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('js/panel.js') }}"></script>

    <script>
        (function() {
            "use strict";
            function hideLoader() {
                var loader = document.getElementById("global-loader");
                if (loader) {
                    loader.style.transition = "opacity 0.25s ease";
                    loader.style.opacity = "0";
                    setTimeout(function() {
                        loader.style.display = "none";
                    }, 250);
                }
            }
            if (document.readyState === "complete" || document.readyState === "interactive") {
                setTimeout(hideLoader, 200);
            } else {
                window.addEventListener("load", hideLoader);
                document.addEventListener("DOMContentLoaded", hideLoader);
            }
            setTimeout(hideLoader, 500); // Safety fallback
        })();
    </script>
</body>

</html>