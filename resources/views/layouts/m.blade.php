<!DOCTYPE html>
<html lang="id">

<head>
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
      <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}" type="image/x-icon">
      <title>{{$page}}</title>

      <!-- Icons Css --> 
      <link href="{{asset('assets/vendors/icons/feather.css')}}" rel="stylesheet" type="text/css">

      <!-- Plugins -->
      <link rel="preconnect" href="https://fonts.gstatic.com/">
      <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&amp;display=swap" rel="stylesheet">
      <link rel="stylesheet" href="{{ asset('assets/vendors/toastr/toastr.min.css') }}">
      <link href="{{asset('assets/vendors/mobile/css/bootstrap.min.css')}}" rel="stylesheet">
      <link rel="stylesheet" href="{{asset('assets/vendors/mobile/css/bootstrap-icons.css')}}"> 

      <link href="{{asset('assets/vendors/mobile/style.css')}}" rel="stylesheet">
      <link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
</head>

<body>
      <div class="internet-connection-status" id="internetStatus"></div>
      <div id="loading"></div>
      @yield('content')

      <script src="{{ asset('assets/jquery-3.3.1.min.js') }}"></script>
      <script src="{{asset('theme/js/bootstrap.min.js')}}"></script>

      <script src="{{ asset('assets/vendors/toastr/toastr.min.js') }}"></script>
      <script src="{{ asset('assets/vendors/toastr/evolution.js') }}"></script>
      <script src="{{ asset('assets/vendors/sweetalert/sweetalert2.all.min.js') }}"></script>
      <script src="{{ asset('assets/vendors/sweetalert/evolution.js') }}"></script>
      <script src="{{ asset('assets/vendors/mobile/js/internet-status.js')}}"></script>
      <script src="{{ asset('assets/vendors/mobile/js/pwa.js')}}"></script>
      <script src="{{ asset('assets/vendors/mobile/js/active.js')}}"></script>
      <script src="{{ asset('assets/vendors/moment/moment.min.js') }}"></script>
      <script src="{{ asset('js/connection.js') }}"></script>
      <script src="{{ asset('js/main.js') }}"></script>

      @yield('scripts')
</body>

</html>