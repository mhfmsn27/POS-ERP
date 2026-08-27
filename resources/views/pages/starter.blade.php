<!DOCTYPE html>
<html lang="en" dir="ltr">

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

<body class="app ltr light-mode horizontal">

    <!-- GLOBAL-LOADER -->
    <div id="global-loader">
        <img src="{{asset('newtheme/images/svgs/loader.svg')}}" class="loader-img" alt="Loader">
    </div>
    <!-- GLOBAL-LOADER -->

    <!-- PAGE -->
    <script type="text/javascript">
        window.Laravel = { csrfToken: "{{ csrf_token() }}", jsPermissions: {!! auth()->check() ? auth()->user()->jsPermissions() : 0 !!} };
    </script>

    <div class="page" id="app"></div>
    <!-- END PAGE-->


  
    <!-- JQUERY JS -->
    <script src="{{asset('newtheme/plugins/jquery/jquery.min.js')}}"></script>

    <!-- BOOTSTRAP JS -->
    <script src="{{asset('newtheme/plugins/bootstrap/js/popper.min.js')}}"></script>
    <script src="{{asset('newtheme/plugins/bootstrap/js/bootstrap.min.js')}}"></script>
 

    <!-- SIDEBAR JS -->
    <script src="{{asset('newtheme/plugins/sidebar/sidebar.js')}}"></script>

    <!-- Perfect SCROLLBAR JS--> 

    <!-- APP JS-->
    <script src="{{asset('newtheme/js/themeColors.js')}}"></script>
    <script src="{{asset('newtheme/js/custom.js')}}"></script> 

    <script src="/js/starter.js"></script>

</body>

</html>