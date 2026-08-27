<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}" type="image/x-icon">
    <title>{{$page}}</title>

    <link href="{{asset('assets/vendors/icons/feather.css')}}" rel="stylesheet" type="text/css">

    <link href="{{asset('assets/vendors/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/vendors/toastr/toastr.min.css') }}">
    <link href="{{asset('assets/mobile/css/style.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
</head>

<body class="fixed-bottom-bar">
    <div id="loader"></div>
    <div id="sound"></div> 
    <x-admin.lang-component></x-admin.lang-component>
    <div id="cTransaction">
        <div class="poshub-home-page">
            @yield('content')
        </div>
    </div>


    <script src="{{ asset('assets/jquery-3.3.1.min.js') }}"></script>
    <script src="{{asset('assets/vendors/bootstrap/js/bootstrap.bundle.min.js')}}"></script>

    <script src="{{ asset('assets/vendors/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/toastr/evolution.js') }}"></script>
    <script src="{{ asset('assets/vendors/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/sweetalert/evolution.js') }}"></script>
    <script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
    <script src="{{ asset('js/connection.js') }}"></script>
    @yield('scripts')



</body>

</html>