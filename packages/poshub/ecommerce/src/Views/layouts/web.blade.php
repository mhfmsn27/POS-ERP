<!DOCTYPE html>
<html class="no-js" lang="en">

<head>
      <x-ecommerce-meta-component></x-ecommerce-meta-component>
      <meta name="theme-color" content="#1e40af">
      <meta name="mobile-web-app-capable" content="yes">
      <meta name="apple-mobile-web-app-capable" content="yes">
      <link rel="manifest" href="/manifest.json">
      <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}" type="image/x-icon"> 
      <link rel="apple-touch-icon" href="{{ asset('assets/images/icon.png') }}">
      <link rel="stylesheet" href="{{asset('ecommerce/css/plugins/animate.min.css')}}" />
      <link rel="stylesheet" href="{{asset('ecommerce/css/main.css')}}?v=5.5" />
      <link rel="stylesheet" href="{{ asset('assets/vendors/toastr/toastr.min.css') }}"> 
      <link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
      @yield('styles')
</head> 
<body> 

      <x-ecommerce-header-component></x-ecommerce-header-component>

      <x-ecommerce-menu-component></x-ecommerce-menu-component>

      @yield('content')

      <x-ecommerce-footer-component></x-ecommerce-footer-component>
      <!-- Preloader Start -->
      <div id="preloader-active">
            <div class="preloader d-flex align-items-center justify-content-center">
                  <div class="preloader-inner position-relative">
                        <div class="text-center">
                              <img src="{{asset('ecommerce/imgs/theme/loading.gif')}}" alt="" />
                        </div>
                  </div>
            </div>
      </div>
      <!-- Vendor JS-->
      <script src="{{asset('ecommerce/js/vendor/modernizr-3.6.0.min.js')}}"></script>
      <script src="{{asset('ecommerce/js/vendor/jquery-3.6.0.min.js')}}"></script>
      <script src="{{asset('ecommerce/js/vendor/jquery-migrate-3.3.0.min.js')}}"></script>
      <script src="{{asset('ecommerce/js/vendor/bootstrap.bundle.min.js')}}"></script>
      <script src="{{asset('ecommerce/js/plugins/slick.js')}}"></script>
      <script src="{{asset('ecommerce/js/plugins/jquery.syotimer.min.js')}}"></script>
      <script src="{{asset('ecommerce/js/plugins/waypoints.js')}}"></script>
      <script src="{{asset('ecommerce/js/plugins/wow.js')}}"></script>
      <script src="{{asset('ecommerce/js/plugins/perfect-scrollbar.js')}}"></script>
      <script src="{{asset('ecommerce/js/plugins/magnific-popup.js')}}"></script>
      <script src="{{asset('ecommerce/js/plugins/select2.min.js')}}"></script>
      <script src="{{asset('ecommerce/js/plugins/counterup.js')}}"></script>
      <script src="{{asset('ecommerce/js/plugins/jquery.countdown.min.js')}}"></script>
      <script src="{{asset('ecommerce/js/plugins/images-loaded.js')}}"></script>
      <script src="{{asset('ecommerce/js/plugins/isotope.js')}}"></script>
      <script src="{{asset('ecommerce/js/plugins/scrollup.js')}}"></script>
      <script src="{{asset('ecommerce/js/plugins/jquery.vticker-min.js')}}"></script>
      <script src="{{asset('ecommerce/js/plugins/jquery.theia.sticky.js')}}"></script>
      <script src="{{asset('ecommerce/js/plugins/jquery.elevatezoom.js')}}"></script>
      <!-- Template  JS -->
      <script src="{{asset('ecommerce/js/main.js')}}?v=5.5"></script>
      <script src="{{asset('ecommerce/js/shop.js')}}?v=5.5"></script>
      <script src="{{ asset('js/connection.js') }}"></script>
      <script src="{{ asset('assets/vendors/sweetalert/sweetalert2.all.min.js') }}"></script>
      <script src="{{ asset('assets/vendors/toastr/toastr.min.js') }}"></script>
      <script src="{{ asset('assets/vendors/sweetalert/sweetalert2.all.min.js')}}"></script>
    <script src="{{ asset('assets/vendors/sweetalert/evolution.js')}}"></script>
      <script src="{{asset('ecommerce/js/custom.js')}}"></script>
      <script src="{{ asset('js/pwa-manager.js') }}"></script>

      <script>
            (function() {
                  "use strict";
                  function hidePreloader() {
                        var preloader = document.getElementById("preloader-active");
                        if (preloader) {
                              preloader.style.transition = "opacity 0.25s ease";
                              preloader.style.opacity = "0";
                              setTimeout(function() {
                                    preloader.style.display = "none";
                              }, 250);
                        }
                  }
                  if (document.readyState === "complete" || document.readyState === "interactive") {
                        setTimeout(hidePreloader, 200);
                  } else {
                        window.addEventListener("load", hidePreloader);
                        document.addEventListener("DOMContentLoaded", hidePreloader);
                  }
                  setTimeout(hidePreloader, 600); // Safety fallback
            })();
      </script>

      @yield('scripts')
</body>

</html>