<!doctype html>
<html lang="en" >

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="POSHUB - Platform Enterprise ERP & Omnichannel Point of Sale" name="description">
    <meta content="POSHUB" name="author">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{$page ?? 'POSHUB'}}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}" type="image/x-icon">
    <link id="style" href="{{asset('newtheme/plugins/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link id="style" href="{{asset('newtheme/css/icons.css')}}" rel="stylesheet">
    <link id="style" href="{{asset('newtheme/switcher/demo.css')}}" rel="stylesheet">
    <link id="style" href="{{asset('newtheme/switcher/css/switcher.css')}}" rel="stylesheet">

    <link id="style" href="{{asset('newtheme/css/style.css')}}" rel="stylesheet">
    <link id="style" href="{{asset('newtheme/css/plugins.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
    @yield('styles')
</head>
<x-admin.lang-component></x-admin.lang-component>

<body class="app ltr light-mode horizontal">

    <!-- GLOBAL-LOADER -->
    <div id="global-loader">
        <img src="{{asset('newtheme/images/svgs/loader.svg')}}" class="loader-img" alt="Loader">
    </div>
    <!-- GLOBAL-LOADER -->

    <!-- PAGE -->
    <div class="page">

        <div class="page-main">

            <!-- App-Header -->
            <x-starter.header-component></x-starter.header-component>
            <!-- End App-Header -->

            <!--App-Sidebar-->
            <x-starter.sidebar-component></x-starter.sidebar-component>
            <!-- End App-Sidebar-->

            <!--app-content open-->
            <div class="app-content main-content">
                <div class="side-app">
                    <div class="main-container">

                        @yield('content')

                    </div>
                </div> 
            </div> 

        </div> 

        @yield('modals')

    </div>
    <!-- END PAGE-->
 

</body>

<!-- JQUERY JS -->
<script src="{{asset('newtheme/plugins/jquery/jquery.min.js')}}"></script>

<!-- BOOTSTRAP JS -->
<script src="{{asset('newtheme/plugins/bootstrap/js/popper.min.js')}}"></script>
<script src="{{asset('newtheme/plugins/bootstrap/js/bootstrap.min.js')}}"></script>

 <!-- SIDE-MENU JS -->
 <script src="{{asset('newtheme/plugins/sidemenu/sidemenu.js')}}"></script>

 <!-- SIDEBAR JS -->
 <script src="{{asset('newtheme/plugins/sidebar/sidebar.js')}}"></script>

<!-- Perfect SCROLLBAR JS--> 

<!-- APP JS-->
<script src="{{asset('newtheme/js/themeColors.js')}}"></script>
<script src="{{asset('newtheme/js/custom.js')}}"></script>
<script src="{{asset('newtheme/js/custom-switcher.js')}}"></script>

<!-- END SCRIPTS -->

<script src="{{ asset('assets/vendors/sweetalert/sweetalert2.all.min.js')}}"></script>
<script src="{{ asset('assets/vendors/sweetalert/evolution.js')}}"></script>

@yield('scripts')

</html>