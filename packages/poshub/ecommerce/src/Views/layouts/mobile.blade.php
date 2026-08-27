<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, minimum-scale=1, viewport-fit=cover">

    <!-- font -->
    <link rel="stylesheet" href="{{asset('emobile/fonts/fonts.css')}}">
    <!-- Icons -->
    <link rel="stylesheet" href="{{asset('emobile/fonts/font-icons.css')}}">
    <link rel="stylesheet" href="{{asset('emobile/css/bootstrap.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('emobile/css/nouislider.min.css')}}" />
    <link rel="stylesheet" href="{{asset('emobile/css/swiper-bundle.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('emobile/css/styles.css')}}" />
    <!-- <link rel="manifest" href="_manifest.json" data-pwa-version="set_in_manifest_and_pwa_js"> -->
    <!-- Favicon and Touch Icons  -->
    <link rel="stylesheet" href="{{ asset('assets/vendors/fontawesome/all.min.css') }}">
    <link rel="shortcut icon" href="{{ asset('assets/images/icon.png') }}" />
    <link rel="apple-touch-icon-precomposed" href="{{ asset('assets/images/icon.png') }}" />
    <link rel="stylesheet" href="{{asset('assets/vendors/toastr/toastr.min.css')}}">

    @yield('styles')
    <title>{{$page}}</title>


</head>

<body class="bg-wallet">
    <!-- preloade -->
    <div class="preload preload-container">
        <div class="spinner-circle spinner-line">
            <span class="spinner-circle1 spinner-child"></span>
            <span class="spinner-circle2 spinner-child"></span>
            <span class="spinner-circle3 spinner-child"></span>
            <span class="spinner-circle4 spinner-child"></span>
            <span class="spinner-circle5 spinner-child"></span>
            <span class="spinner-circle6 spinner-child"></span>
            <span class="spinner-circle7 spinner-child"></span>
            <span class="spinner-circle8 spinner-child"></span>
            <span class="spinner-circle9 spinner-child"></span>
        </div>
    </div>

    @yield('content')




    <div class="modal fade action-sheet full" id="filterCart">
        <div class="modal-dialog" role="document">
            <div class="cart-footer">
                <div class="inner">
                    <div class="top">
                        <h3>Tambah Ke Keranjang</h3>
                        <span class="icon-close1" data-bs-dismiss="modal"></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-16">
                        <span class="text-caption">Masukan Qty</span>
                        <span class="text-caption">Total</span>
                    </div>
                    <div class="mt-8 d-flex justify-content-between align-items-center">
                        <div class="tf-stepper round-2 sm surface">
                            <input class="stepper" type="text" value="1">
                        </div>
                        <h5>Rp 50,000</h5>
                    </div>
                    <div class="bottom-btn">
                        <span class="press-toggle default-press">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="21" viewBox="0 0 20 21" fill="none">
                                <path d="M17.3671 4.34172C16.9415 3.91589 16.4361 3.5781 15.8799 3.34763C15.3237 3.11716 14.7275 2.99854 14.1254 2.99854C13.5234 2.99854 12.9272 3.11716 12.371 3.34763C11.8147 3.5781 11.3094 3.91589 10.8838 4.34172L10.0004 5.22506L9.11709 4.34172C8.25735 3.48198 7.09129 2.99898 5.87542 2.99898C4.65956 2.99898 3.4935 3.48198 2.63376 4.34172C1.77401 5.20147 1.29102 6.36753 1.29102 7.58339C1.29102 8.79925 1.77401 9.96531 2.63376 10.8251L3.51709 11.7084L10.0004 18.1917L16.4838 11.7084L17.3671 10.8251C17.7929 10.3994 18.1307 9.89407 18.3612 9.33785C18.5917 8.78164 18.7103 8.18546 18.7103 7.58339C18.7103 6.98132 18.5917 6.38514 18.3612 5.82893C18.1307 5.27271 17.7929 4.76735 17.3671 4.34172V4.34172Z" stroke="#787982" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <a href="{{route('ecommerce.mobile.cart')}}" class="tf-btn primary">Masukan Ke Keranjang</a>
                    </div>

                </div>

            </div>


        </div>
    </div>


    <!-- noti popup -->
    <div class="modal fade modalCenter" id="modalNoti">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="p-16 line-bt text-center">
                    <h5>“Selamat Datang Di Toko Online Kami</h5>
                    <p class="mt-8 text-large">Kami Mengucapkan Selamat berbelanja</p>
                </div>
                <div class="grid-2">
                    <a href="#" class="line-r text-center text-button fw-6 p-10 text-secondary btn-hide-modal" data-bs-dismiss="modal">Ok
                    </a>
                </div>
            </div>
        </div>
    </div>





    <script type="text/javascript" src="{{asset('emobile/js/bootstrap.min.js')}}"></script>
    <script src="{{ asset('assets/vendors/fontawesome/all.min.js') }}"></script>
    <script type="text/javascript" src="{{asset('emobile/js/jquery.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('emobile/js/swiper-bundle.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('emobile/js/carousel.js')}}"></script>
    <script type="text/javascript" src="{{asset('emobile/js/nouislider.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('emobile/js/rangle-slider.js')}}"></script>
    <script type="text/javascript" src="{{asset('emobile/js/count-down.js')}}"></script>
    <script type="text/javascript" src="{{asset('emobile/js/init.js')}}"></script>
    <script type="text/javascript" src="{{asset('emobile/js/bootstrap-touchspin.min.js')}}"></script>
    <script type="text/javascript" src="{{asset('emobile/js/main.js')}}"></script>
    <script type="text/javascript" src="{{asset('emobile/js/multiple-modal.js')}}"></script>
    <script src="{{ asset('js/connection.js') }}"></script>
    <script src="{{ asset('assets/vendors/sweetalert/sweetalert2.all.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/toastr/toastr.min.js') }}"></script>
    <script src="{{ asset('assets/vendors/toastr/evolution.js') }}"></script>
    <script src="{{ asset('assets/vendors/sweetalert/evolution.js')}}"></script>

    @yield('scripts')
    <script>
        $(".choosevariant").on("click", function() {
            var variationId = $(this).val();
            setTimeout(function() {
                $.ajax({
                    url: domain + domainpath + '/m-ecommerce/shop/variation-detail/' + variationId,
                    type: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        timeout: 0,
                    },
                    success: function(data, json, errorThrown) {
                        if (data.status == true) {
                            console.log(data.data)
                            var subtotalPrice = parseInt($("#qtyCart").val()) * parseInt(data.data.price);
                            $("#priceVariation").val(data.data.price);
                            $('.in-stock').html(formatRupiah(data.data.stock.toString()) + ' Tersedia')
                            $('#variationID').val(data.data.id)
                            $('.current-price').html('Rp ' + formatRupiah(data.data.price.toString()))
                            $('.title-detail').html(data.data.product_name + ' - ' + data.data.name)
                            $(".pricetotal").html("Rp " + formatRupiah(subtotalPrice.toString()));
                            $("#maxStock").val(data.data.stock);
                        }
                    },

                    cache: false,
                    contentType: false,
                    processData: false,
                })
            }, 130)
        })

        $("#qtyCart").on("change", function() {

            if (parseInt($(this).val()) > parseInt($("#maxStock").val())) {
                console.log("loh Piye!")
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Stok Tidak Mencukupi',
                    icon: 'warning',
                    showCancelButton: false,
                    confirmButtonColor: '#435ebe',
                    cancelButtonColor: '#198754',
                    confirmButtonText: 'Ok Saya Mengerti',
                }).then((result) => {})
                $("#qtyCart").val(parseInt($("#maxStock").val()));
                return
            } else {
                var price = parseInt($("#priceVariation").val());
                var subtotal = price * parseInt($(this).val());
                $(".pricetotal").html("Rp " + formatRupiah(subtotal.toString()));
            }

        })

        $('#addToCartProduct').on('click', function() {
            var quantity = $('#qtyCart').val()
            var variationid = $('#variationID').val()

            if (quantity < 1) {
                Swal.fire({
                    title: 'Peringatan!',
                    text: 'Qty Harus lebih dari satu',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#435ebe',
                    cancelButtonColor: '#198754',
                    confirmButtonText: 'Ok Saya Mengerti',
                }).then((result) => {})
                return
            }

            var sendData = {
                variationid: variationid,
                quantity: quantity,
            }
            setTimeout(function() {
                $.ajax({
                    url: domain + domainpath + '/m-ecommerce/account/cart/add',
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        timeout: 0,
                    },
                    data: JSON.stringify(sendData),
                    success: function(data, json, errorThrown) {
                        if (data.status == false) {
                            Swal.fire({
                                title: 'Error',
                                html: data.message,
                                width: 'auto',
                                confirmButtonText: 'Ok Saya Mengerti',
                                showCancelButton: false,
                            })
                        } else {
                            $('#qtyCart').val(1)

                            // For Website
                            $(".cart-total").html(data.total);

                            toastr.success(data.message, 'Berhasil', {
                                timeOut: 5e3,
                                closeButton: !0,
                                debug: !1,
                                newestOnTop: !0,
                                progressBar: !0,
                                positionClass: 'toast-top-right',
                                preventDuplicates: !0,
                                onclick: null,
                                showDuration: '100',
                                hideDuration: '1000',
                                extendedTimeOut: '1000',
                                showEasing: 'swing',
                                hideEasing: 'linear',
                                showMethod: 'fadeIn',
                                hideMethod: 'fadeOut',
                                tapToDismiss: !1,
                            })
                        }
                    },

                    cache: false,
                    contentType: false,
                    processData: false,
                })
            }, 130)
        })

        function formatRupiah(angka, prefix) {
            var number_string = angka.replace(/[^0-9\.]/g, '').toString(),
                titik = number_string.split('.'),
                split = titik[0].split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi)

            if (ribuan) {
                separator = sisa ? ',' : ''
                rupiah += separator + ribuan.join(',')
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah
            rupiah = titik[1] != undefined ? rupiah + '.' + titik[1] : rupiah
            return prefix == undefined ? rupiah : rupiah ? rupiah : ''
        }

        function changeStore(id) {
            window.location = '/m-ecommerce/change-session/' + id
        }
    </script>
</body>

</html>