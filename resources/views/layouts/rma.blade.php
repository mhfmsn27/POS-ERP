<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta content="POSHUB - Platform Enterprise ERP & Omnichannel Point of Sale" name="description">
    <meta content="POSHUB" name="author">
    <title>{{$page}}</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}" type="image/x-icon">
    <link href="{{asset('css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/all.min.css') }}">
    <link rel="stylesheet" href="{{asset('css/typography.css')}}">
    <link rel="stylesheet" href="{{asset('css/login.css')}}">
    <link rel="stylesheet" href="{{asset('css/responsive.css')}}">
   
    <link rel="stylesheet" href="{{asset('theme/css/style.css')}}"> 
    <link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">

    @yield('styles')
</head>

<body>

    <section class="sign-in-page bg-white">
        <div class="container-fluid p-0">
            <div class="row no-gutters">
                <div class="col-lg-12 d-flex justify-content-center align-items-center" style="min-height: 100vh;">

                    @yield('content')

                </div>
            </div>
        </div>
    </section>
    <script src="{{ asset('assets/jquery-3.3.1.min.js') }}"></script>
    <script src="{{asset('theme/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('assets/vendors/fontawesome/all.min.js') }}"></script>
    <script src="{{asset('theme/js/owl.carousel.min.js')}}"></script>
    <script src="{{asset('js/signin.js')}}"></script>
</body>

</html>