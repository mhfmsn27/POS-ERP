@extends('ecommerce::layouts.mobile')

@section('styles')

@endsection
@section('content')
<div class="header fixed-top line4-bt">
    <div class="left">
        <a href="{{ url()->previous() }}" class="icon back-btn"><i class="icon-left-btn"></i></a>
    </div>
    <h6>Checkout Pesanan</h6>
</div>
<div class="app-content">
    <div id="map" class="d-none"></div>
    <input type="hidden" id="storeLang" value="<?= $stores->lang; ?>">
    <input type="hidden" id="storeLong" value="<?= $stores->long; ?>">
    <input type="hidden" id="manualKurirActivation" value="<?= $settings->kurir_manual; ?>">
    <input type="hidden" id="pricePerKm" value="<?= (int)$settings->price_per_km; ?>">

    <!-- Daftar Produk -->
    <div class="py-16 line4-bt">
        <div class="tf-container">
            <h5 class="mb-16">Daftar Produk</h5>
            @foreach($carts['carts'] as $cart)
            <div class="box-cart-select pb-12 line-bt cartitems" id="cartid{{$cart->id}}">
                <label for="chooseCart{{$cart->id}}" class="inner">
                    <div class="img">
                        <input type="hidden" value="{{$cart->id}}" id="cartIdVariation" name="cart_id[]">
                        <input type="hidden" value="{{$cart->variation_id}}" id="variationIdCart" name="variation_id[]">
                        <img src="{{asset($cart->variation->product->default_image ?? '')}}" style="width:80px; height:auto" class="rounded" alt="img">
                    </div>
                    <div class="content">
                        <h6>
                            {{$cart->variation->product->name ?? ''}} @if($cart->variation->product->type != 'single') - {{$cart->variation->name ?? ''}} @endif
                        </h6>
                        <input type="hidden" id="productPrice" value="{{(int)$cart->variation->selling_price}}">
                        <p class="mt-4 text-caption text-black">Stok Tersedia : {{number_format($cart->variation->stock_in_website->sum('qty_available'))}}</p>
                        <h6 class="mt-4">Rp {{number_format((int)$cart->variation->selling_price)}} </h6>
                        @php
                        $subtotal = (int)$cart->variation->selling_price * $cart->quantity;
                        @endphp
                        <input type="hidden" id="subtotalPriceCart" value="{{$subtotal}}" datacart="{{$cart->id}}">
                    </div>

                </label>
            </div>
            @endforeach
        </div>
    </div>
    <!-- End Produk -->

    <div class="py-16 line4-bt">
        <div class="tf-container">
            <h5>Alamat Pengiriman</h5>

            <div class="box-address chosedAddress mt-16 @if($dAddress == null) d-none @endif">
                <input type="hidden" name="address_option" id="addressOption" value="<?= $dAddress != null ? $dAddress->id : ''; ?>" />
                <input type="hidden" id="addressLang" value="<?= $dAddress != null ? $dAddress->lang : ''; ?>">
                <input type="hidden" id="addressLong" value="<?= $dAddress != null ? $dAddress->long : ''; ?>">
                <label for="address">
                    <h6 id="addressName"><?= $dAddress != null ? $dAddress->name : ''; ?></h6>
                    <p class="mt-4 text-caption text-black" id="addressPhone"><?= $dAddress != null ? $dAddress->phone : ''; ?></p>
                    <p class="mt-4 text-caption text-black" id="addressAddress"><?= $dAddress != null ? $dAddress->address : ''; ?> </p>
                </label>
            </div>
        </div>
    </div>
    <div class="line4-bt">
        <div class="tf-container">
            <a href="{{route('ecommerce.mobile.checkout_checked')}}#modalLocation" data-bs-toggle="modal" class="py-16 d-flex justify-content-between align-items-center">
                <span class="fw-bold">Pilih Alamat Pengiriman</span>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                        <path d="M10.4748 8.96197L6.45183 12.975C6.19592 13.2304 5.78101 13.2304 5.52522 12.975C5.26941 12.7198 5.26941 12.306 5.52522 12.0508L9.08494 8.49985L5.52532 4.94903C5.26952 4.69374 5.26952 4.2799 5.52532 4.02472C5.78113 3.76943 6.19603 3.76943 6.45194 4.02472L10.4749 8.03784C10.6028 8.16549 10.6667 8.33262 10.6667 8.49983C10.6667 8.66712 10.6027 8.83438 10.4748 8.96197Z" fill="#787982" />
                    </svg>
                </span>
            </a>
        </div>
    </div>


    <input type="hidden" id="subtotalCart" value="{{$carts['subtotal']}}">
    <input type="hidden" id="subtotalTax" value="{{$tax_total}}">
    <input type="hidden" id="sp" name="shipping_price">
    <input type="hidden" id="sc" name="shipping_code">
    <input type="hidden" id="ss" name="shipping_service">

    <div class="py-16 line4-bt">
        <div class="tf-container">
            <h5>Metode Pengiriman</h5>

            <div class="box-address chooseShipping mt-16 d-none">
                <input type="hidden" name="currier_option" id="shippingOption" value="" />
                <label for="address">
                    <h6 id="shippingName"></h6>
                    <p class="mt-4 text-caption text-black" id="shippingService"></p>
                    <p class="mt-4 text-caption text-black" id="shippingPrice"> </p>
                </label>
            </div>
        </div>
    </div>
    <div class="line4-bt">
        <div class="tf-container">
            <a href="{{route('ecommerce.mobile.checkout_checked')}}#modalShipping" data-bs-toggle="modal" class="py-16 d-flex justify-content-between align-items-center">
                <span class="fw-bold">Pilih Metode Pengiriman</span>
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17" fill="none">
                        <path d="M10.4748 8.96197L6.45183 12.975C6.19592 13.2304 5.78101 13.2304 5.52522 12.975C5.26941 12.7198 5.26941 12.306 5.52522 12.0508L9.08494 8.49985L5.52532 4.94903C5.26952 4.69374 5.26952 4.2799 5.52532 4.02472C5.78113 3.76943 6.19603 3.76943 6.45194 4.02472L10.4749 8.03784C10.6028 8.16549 10.6667 8.33262 10.6667 8.49983C10.6667 8.66712 10.6027 8.83438 10.4748 8.96197Z" fill="#787982" />
                    </svg>
                </span>
            </a>
        </div>
    </div>

    <div class="py-16 line4-bt">
        <div class="tf-container">

            <ul class="mt-32 pb-12 line-bt-dashed">
                <li class="d-flex justify-content-between">
                    <span>Subtotal</span>
                    <span class="fw-6 text-black subtotalCart">Rp {{number_format($carts['subtotal'])}}</span>
                </li>
                <li class="d-flex justify-content-between mt-15">
                    <span>Pajak (PPN)</span>
                    <span class="fw-6 text-black taxtotalCart">Rp {{number_format($tax_total)}} ({{number_format($stores->tax)}} %)</span>
                </li>
                <li class="d-flex justify-content-between mt-15">
                    <span>Ongkos Kirim</span>
                    <span class="fw-6 text-black shippingCost">Rp 0</span>
                </li>

            </ul>
            <div class="d-flex justify-content-between align-items-center mb-8 mt-10">
                <span>Grand Total</span>
                <h5 class="grandTotal">Rp {{number_format($grandTotal)}}</h5>
            </div>
        </div>
    </div>
    <div class="py-16">
        <div class="tf-container">
            <div class="pt-16 pb-16">

                <div class="footer-fixed p-16">
                    <button type="button" id="getPayment" class="tf-btn primary">Buat Pesanan</button>
                </div>
            </div>
        </div>
    </div>



</div>

<!-- Modal Change Location -->
<div class="modal fade modalRight" id="modalLocation">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="header fixed-top">
                <div class="left" data-bs-dismiss="modal">
                    <a href="javascript:void(0);" class="icon"><i class="icon-left-btn"></i></a>
                </div>
                <h6>Ganti Alamat Pengiriman</h6>
            </div>
            <div class="overflow-auto app-content style-7">
                <div class="tf-container">

                    <ul class="mt-20">
                        @foreach ($address as $address)
                        <li class="d-flex align-items-center gap-20 py-16 line-bt">
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="25" viewBox="0 0 24 25" fill="none">
                                    <g clip-path="url(#clip0_1_5925)">
                                        <path d="M21 10.5C21 17.5 12 23.5 12 23.5C12 23.5 3 17.5 3 10.5C3 8.11305 3.94821 5.82387 5.63604 4.13604C7.32387 2.44821 9.61305 1.5 12 1.5C14.3869 1.5 16.6761 2.44821 18.364 4.13604C20.0518 5.82387 21 8.11305 21 10.5Z" stroke="#787982" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M12 13.5C13.6569 13.5 15 12.1569 15 10.5C15 8.84315 13.6569 7.5 12 7.5C10.3431 7.5 9 8.84315 9 10.5C9 12.1569 10.3431 13.5 12 13.5Z" stroke="#787982" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip0_1_5925">
                                            <rect width="24" height="24" fill="white" transform="translate(0 0.5)" />
                                        </clipPath>
                                    </defs>
                                </svg>
                            </div>
                            <a class="locationlist">
                                <h6 id="aListName">{{$address->name}}</h6>
                                <input type="hidden" id="aListId" value="<?= $address->id; ?>">
                                <input type="hidden" id="aListLong" value="<?= $address->long; ?>">
                                <input type="hidden" id="aListLang" value="<?= $address->lang; ?>">
                                <p class="mt-4 text-caption" id="aListPhone">{{$address->phone}} </p>
                                <p class="mt-2 text-caption" id="aListAddress">{{$address->address}} </p>
                            </a>
                        </li>
                        @endforeach


                    </ul>

                </div>
            </div>

        </div>
    </div>
</div>
<!-- End Modal -->

<!-- Modal Choose Shipping -->
<div class="modal fade modalRight" id="modalShipping">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="header fixed-top">
                <div class="left" data-bs-dismiss="modal">
                    <a href="javascript:void(0);" class="icon"><i class="icon-left-btn"></i></a>
                </div>
                <h6>Pilih Metode Pengiriman</h6>
            </div>
            <div class="overflow-auto app-content style-7">
                <div class="tf-container">
                    <ul class="mt-20">
                        <li class="d-none d-flex align-items-center gap-20 py-16 line-bt mkurir">
                            <div>
                                <img src="<?= asset('images/instant.png'); ?>" style="max-width: 100px;" class="rounded">
                            </div>
                            <a href="javascript:void(0);" onclick="instantKurir()">
                                <h6>Pengiriman Instant</h6>
                                <input type="hidden" id="priceM" value="0">
                                <p class="mt-4 text-caption" id="priceKurirM">Rp 0</p>
                            </a>
                        </li>
                    </ul>
                    <ul class="mt-20 listcostshipping">


                    </ul>

                </div>
            </div>

        </div>
    </div>
</div>
<!-- End Choose Shipping -->

@endsection

@section('scripts')
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    $(document).ready(function() {


        // Initialize the map
        const map = L.map('map').setView([0, 0], 2);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: ''
        }).addTo(map);

        setTimeout(function() {
            getShipping($("#addressOption").val())
        }, 1000)


        $('#getPayment').on('click', function() {
            setTimeout(function() {
                var sendData = data()
                $.ajax({
                    url: domain + domainpath + '/m-ecommerce/shop/checkout/transactions',
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
                            return false
                        } else {
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

                            window.location = '/m-ecommerce/account/orders/hold'
                        }
                    },

                    cache: false,
                    contentType: false,
                    processData: false,
                })
            }, 130)
        })

        setTimeout(function() {
            if ($("#manualKurirActivation").val() == 'yes') {
                calCulateLocation();
            }

        }, 1500)
    })

    function calCulateLocation() {

        var storeLang = $("#storeLang").val();
        var storeLong = $("#storeLong").val();
        var addressLang = $("#addressLang").val();
        var addressLong = $("#addressLong").val();

        console.log(addressLang,addressLong)

        if (storeLang != '' && storeLong != '' && addressLang != '' && addressLong != '') {

            var startPoint = L.latLng(addressLang, addressLong);
            var endPoint = L.latLng(storeLang, storeLong);

            var distance = startPoint.distanceTo(endPoint) / 1000; // Convert to kilometers
            var distanceFix = parseInt(distance.toFixed(2));
            if (distanceFix > 0) {
                var pricePerKm = $("#pricePerKm").val();
                var total = parseInt(pricePerKm) * distanceFix;

                $(".mkurir").removeClass("d-none");
                $("#priceKurirM").html("Rp " + formatRupiah(total.toString()))
                $("#priceM").val(total);
                console.log(total,pricePerKm,distanceFix,distance.toFixed(2));

            }
        }

    }


    function getShipping(id) {
        $('.listcostshipping').html('')

        setTimeout(function() {
            $.ajax({
                url: domain +
                    domainpath +
                    '/m-ecommerce/shop/checkout/get-shipping-cost?address_id=' +
                    id,
                type: 'GET',
                success: function(data, json, errorThrown) {
                    var shippingData = ''
                    if (data.status == true) {
                        let no = 1
                        $.each(data.data, function(index, value) {
                            var defaultData = ''

                            var idNumber = no++

                            shippingData +=

                                `<li class="d-flex align-items-center gap-20 py-16 line-bt">
                            <div>
                                 <img src="` + value.image + `" style="max-width: 100px;" class="rounded">
                            </div>
                            <a class="shippinglist" href="javascript:void(0);">
                                <h6 id="sListName">` + value.name + `</h6>
                                <input type="hidden" id="sListId" value="` + idNumber + `">
                                <input type="hidden" id="sListPriceData" value="` + value.price + `">
                                <input type="hidden" id="sListCode" value="` + value.code + `">
                                <p class="mt-4 text-caption" id="sListService">` + value.service + `</p>
                                <p class="mt-2 text-caption" >Estimasi Pengiriman ` + value.etd + ` </p>
                                <p class="mt-2 text-caption" id="sListPrice">Rp ` + formatRupiah(value.price.toString()) + ` </p>
                                
                            </a>
                        </li>`
                        })

                        $('.listcostshipping').html(shippingData)
                    }
                },
            })
        }, 130)
    }

    $('.listcostshipping').on('click', '.shippinglist', function() {

        var newId = $(this).find("#sListId").val();
        var newNAme = $(this).find("#sListName").html();
        var newPrice = $(this).find("#sListPriceData").val();
        var newService = $(this).find("#sListService").html();
        var newCode = $(this).find("#sListCode").val();

        $(".chooseShipping").removeClass("d-none");

        $("#shippingOption").val(newId);
        $("#sc").val(newCode)
        $("#sp").val(newPrice);
        $("#ss").val(newService);
        $("#shippingName").html(newNAme)
        $("#shippingService").html(newService);
        $("#shippingPrice").html("Rp " + formatRupiah((newPrice.toString())))

        var subtotal = $('#subtotalCart').val()
        var taxTotal = $('#subtotalTax').val()

        if (newPrice == undefined) {
            price = 0
        }


        var grandTotal = parseInt(newPrice) + parseInt(subtotal) + parseInt(taxTotal)

        $('.shippingCost').html('Rp ' + formatRupiah(newPrice.toString()))
        $('.grandTotal').html('Rp ' + formatRupiah(grandTotal.toString()))

        $("#modalShipping").modal("toggle");
    });

    function instantKurir() {
        var newPrice = $("#priceM").val();
        

        $(".chooseShipping").removeClass("d-none");
        $("#shippingOption").val("kurir");
        $("#sc").val("kurir")
        $("#sp").val(newPrice);
        $("#ss").val("kurir");
        $("#shippingName").html("Intant Kurir")
        $("#shippingService").html("");
        $("#shippingPrice").html("Rp " + formatRupiah((newPrice.toString())))

        var subtotal = $('#subtotalCart').val()
        var taxTotal = $('#subtotalTax').val()

        if (newPrice == undefined) {
            price = 0
        }


        var grandTotal = parseInt(newPrice) + parseInt(subtotal) + parseInt(taxTotal)

        $('.shippingCost').html('Rp ' + formatRupiah(newPrice.toString()))
        $('.grandTotal').html('Rp ' + formatRupiah(grandTotal.toString()))

        $("#modalShipping").modal("toggle");
    }

    $(".locationlist").on("click", function() {
        var newId = $(this).find("#aListId").val();
        var newNAme = $(this).find("#aListName").html();
        var newPhone = $(this).find("#aListPhone").html();
        var newAddress = $(this).find("#aListAddress").html();
        var newLong = $(this).find("#aListLong").val();
        var newLang = $(this).find("#aListLang").val(); 

        $(".chosedAddress").removeClass("d-none");

        $("#addressOption").val(newId);
        $("#addressName").html(newNAme)
        $("#addressPhone").html(newPhone);
        $("#addressAddress").html(newAddress)
        $("#addressLang").val(newLang);
        $("#addressLong").val(newLong);

        $("#modalLocation").modal("toggle");

        resetShipping();
 
        if ($("#manualKurirActivation").val() == 'yes') { 
            calCulateLocation();
        }
    });

    function resetShipping() {
        $(".chooseShipping").addClass("d-none");

        $("#shippingOption").val("");
        $("#sc").val("")
        $("#sp").val(0);
        $("#ss").val("");
        $("#shippingName").html("")
        $("#shippingService").html("");
        $("#shippingPrice").html("Rp 0")

        var subtotal = $('#subtotalCart').val()
        var taxTotal = $('#subtotalTax').val()

        var grandTotal = parseInt(subtotal) + parseInt(taxTotal)

        $('.shippingCost').html('Rp 0')
        $('.grandTotal').html('Rp ' + formatRupiah(grandTotal.toString()))
    }

    function data() {
        var items = []

        var ongkir = {
            price: $('#sp').val(),
            code: $('#sc').val(),
            service: $('#ss').val(),
            from: $('#addressOption').val(),
        }

        $($('input#cartIdVariation')).each(function() {
            items.push({
                cart: $(this).val(),
            })
        })

        var data = {
            details: items,
            ongkir: ongkir,
        }

        return data
    }
</script>
@endsection