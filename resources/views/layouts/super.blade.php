<!doctype html>
<html lang="en" dir="ltr">

<head>

    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="Description" content="POSHUB ACCOUNTING - Super Administrator Enterprise Panel">
    <meta name="Author" content="POSHUB">

    <!-- Title -->
    <title> {{$page}} </title>

    <!--- Favicon --->
    <link rel="icon" href="{{asset('assets/images/icon.png')}}" type="image/x-icon" />

    <!-- Bootstrap css -->
    <link href="{{asset('admin/plugins/bootstrap/css/bootstrap.css')}}" rel="stylesheet" id="style" />

    <!--- Icons css --->
    <link href="{{asset('admin/css/icons.css')}}" rel="stylesheet">

    <!--- Style css --->
    <link href="{{asset('admin/css/style.css')}}" rel="stylesheet">
    <link href="{{asset('admin/css/plugins.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">

    <!--- Animations css --->
    <link href="{{asset('admin/css/animate.css')}}" rel="stylesheet">

    <!-- Switcher css -->
    <link href="{{asset('admin/switcher/css/switcher.css')}}" rel="stylesheet" />
    <link rel="stylesheet" href="{{asset('admin/switcher/demo.css')}}" />
    @yield('styles')

</head>

<body class="main-body app sidebar-mini ltr">

    <!-- Loader -->
    <div id="global-loader">
        <img src="{{asset('admin/img/loaders/loader-4.svg')}}" class="loader-img" alt="Loader">
    </div>
    <!-- /Loader -->

    <!-- page -->
    <div class="page custom-index">

        <!-- main-header -->
        <x-super.header-component></x-super.header-component>
        <!-- /main-header -->

        <!-- main-sidebar -->
        <x-super.sidebar-component></x-super.sidebar-component>
        <!-- main-sidebar -->

        <!-- main-content -->
        <div class="main-content app-content">

            <!-- container -->
            <div class="main-container container-fluid">

                <!-- breadcrumb -->
                <div class="breadcrumb-header justify-content-between">
                    <div>
                        <h4 class="content-title mb-2">{{$page}}</h4>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="javascript:void(0);">Pages</a></li>
                                <li class="breadcrumb-item active" aria-current="page"> {{$page}}</li>
                            </ol>
                        </nav>
                    </div>
 
                </div>
                @yield('content')
            </div>
        </div>
    </div>




    <!-- Footer opened -->
    <x-super.footer-component></x-super.footer-component>
    <!-- Footer closed -->
    </div>
    <!-- page closed -->

    <!--- Back-to-top --->
    <a href="#top" id="back-to-top"><i class="las la-angle-double-up"></i></a>

    <!--- JQuery min js --->
    <script src="{{asset('admin/plugins/jquery/jquery.min.js')}}"></script>

    <!--- Bootstrap Bundle js --->
    <script src="{{asset('admin/plugins/bootstrap/popper.min.js')}}"></script>
    <script src="{{asset('admin/plugins/bootstrap/js/bootstrap.min.js')}}"></script>

    <!--- Ionicons js --->
    <script src="{{asset('admin/plugins/ionicons/ionicons.js')}}"></script>

    <!--- Moment js --->
    <script src="{{asset('admin/plugins/moment/moment.js')}}"></script>

    <!--- JQuery sparkline js --->
    <script src="{{asset('admin/plugins/jquery-sparkline/jquery.sparkline.min.js')}}"></script>

    <!--- P-scroll js --->
    <script src="{{asset('admin/plugins/perfect-scrollbar/perfect-scrollbar.min.js')}}"></script>
    <script src="{{asset('admin/plugins/perfect-scrollbar/p-scroll.js')}}"></script>

    <!--- Switcher js --->
    <script src="{{asset('admin/switcher/js/switcher.js')}}"></script>

    <!--- Eva-icons js --->
    <script src="{{asset('admin/js/eva-icons.min.js')}}"></script>

    <!--- Sidebar js --->
    <script src="{{asset('admin/plugins/side-menu/sidemenu.js')}}"></script>

    <!--- sticky js --->
    <script src="{{asset('admin/js/sticky.js')}}"></script>

    <!--- Right-sidebar js --->
    <script src="{{asset('admin/plugins/sidebar/sidebar.js')}}"></script>
    <script src="{{asset('admin/plugins/sidebar/sidebar-custom.js')}}"></script>

    <!--- Index js --->
    <script src="{{asset('admin/js/script.js')}}"></script>

    <!--themecolor js-->
    <script src="{{asset('admin/js/themecolor.js')}}"></script>

    <!--swither-styles js-->
    <script src="{{asset('admin/js/swither-styles.js')}}"></script>

    <!--- Custom js --->
    <script src="{{asset('admin/js/custom.js')}}"></script>

    <script src="{{ asset('assets/vendors/sweetalert/sweetalert2.all.min.js')}}"></script>
    <script src="{{ asset('assets/vendors/sweetalert/evolution.js')}}"></script>

    @yield('scripts')

</body>

</html>