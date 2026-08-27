@extends('ecommerce::layouts.mobile')
@section('content')
<div class="header fixed-top line4-bt">
    <div class="left">
        <a href="{{ url()->previous() }}" class="icon back-btn"><i class="icon-left-btn"></i></a>
    </div>
    <h6>Keranjang Saya</h6>
    <a onclick="removeAll()" href="javascript:void(0);" class="right">
        <i class="fa fa-trash text-danger"></i>
    </a>
</div>
<form action="{{route('ecommerce.mobile.checkout_checked')}}" method="post">
    <div class="app-content style-6 cartdata">
        <input type="hidden" id="taxstores" value="{{(int)$stores->tax}}">
        <div class="py-16 line4-bt">
            <div class="tf-container cartalldata">
                @foreach($carts['carts'] as $cart)
                <div class="box-cart-select pb-12 line-bt cartitems" id="cartid{{$cart->id}}">
                    <input type="hidden" id="cartIdData" value="{{$cart->id}}">
                    <input class="cb-rounded checkItem" name="choose_cart[]" type="checkbox" id="chooseCart{{$cart->id}}" value="{{$cart->id}}" checked>
                    <label for="chooseCart{{$cart->id}}" class="inner">
                        <div class="img">
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
                        <div class="d-flex justify-content-center">
                            <div class="tf-stepper round-2 sm surface">
                                <input name="quantity[]" class="stepper qty-val" min="1" max="{{(int)$cart->variation->stock_in_website->sum('qty_available')}}" type="number" value="{{(int)$cart->quantity}}">
                            </div>
                            <a href="javascript:void(0);" onclick="removeCart(<?= $cart->id; ?>)">
                                <i class="fa fa-trash text-danger"></i>
                            </a>
                        </div>

                    </label>
                </div>
                @endforeach
            </div>
        </div>
        <div class="bg-white line4-bt">
            <div class="tf-container">
                <a href="{{route('ecommerce.mobile.shop')}}" class="d-flex py-16 text-lg text-primary fw-6">Kembali Berbelanja</a>
            </div>
        </div>
        <div class="py-16 line4-bt">
            <div class="tf-container">
                <!-- <h5>Add Coupon</h5>
            <div class="d-flex gap-12 mt-16">
                <input type="text" placeholder="Enter  Voucher  Code" class="flex-grow-1">
                <a href="{{route('ecommerce.mobile.cart')}}#" class="tf-btn primary btn-apply">Apply</a>
            </div> -->
                <ul class="mt-32 pb-12 line-bt-dashed">
                    <li class="d-flex justify-content-between">
                        <span>Subtotal</span>
                        <span class="fw-6 text-black subtotalCart">Rp {{number_format($carts['subtotal'])}}</span>
                    </li>
                    <li class="d-flex justify-content-between mt-15">
                        <span>Pajak (PPN)</span>
                        <span class="fw-6 text-black taxtotalCart">Rp {{number_format($tax_total)}} ({{number_format($stores->tax)}} %)</span>
                    </li>

                </ul>
                <div class="d-flex justify-content-between align-items-center mb-8 mt-10">
                    <span>Grand Total</span>
                    <h5 class="grandTotalCart">Rp {{number_format($grandTotal)}}</h5>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-fixed p-16">
        @if(count($carts['carts']) > 0)
        <button type="submit" class="tf-btn primary">Check Out</button>
        @endif
    </div>
</form>

@endsection

@section('scripts')
<script>
    $('body').on('change', '.cartitems', function() {
        var price = $(this).find('input#productPrice').val()
        var qty = $(this).find('input.qty-val').val()
        var maxqty = $(this).find('input.qty-val').attr('max')
        var cartid = $(this).find('input#cartIdData').val()
        var subtotal = 0


        if (qty == null || qty == '') {
            qty = 1
            $(this).find('input.qty-val').val(1)
        }

        if (parseInt(qty) > parseInt(maxqty)) {
            $(this).find('input.qty-val').val(maxqty)
            qty = maxqty
        }

        subtotal = parseInt(qty) * parseInt(price);

        console.log(price, qty, subtotal)

        $(this)
            .find('.text-brand')
            .html('Rp ' + formatRupiah(subtotal.toString()))
        $(this).find('input#subtotalPriceCart').val(subtotal)

        setTimeout(function() {
            var sendData = {
                quantity: qty,
            }

            $.ajax({
                url: domain + domainpath + '/m-ecommerce/account/cart/update/' + cartid,
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
                    } else {}
                },

                cache: false,
                contentType: false,
                processData: false,
            })
        }, 130)

        $('.cartdata').trigger('change')
    })

    $('.cartdata').on('change', function() {
        var subtotal = 0
        var tax = $('#taxstores').val()
        var tax_total = 0
        var grandTotal = 0

        $(this)
            .find('input#subtotalPriceCart')
            .each(function() {
                var cartid = $(this).attr('datacart')
                if ($('#chooseCart' + cartid + ':checked').val() != undefined) {
                    subtotal += parseInt(
                        $(this)
                        .val()
                        .replace(/[^0-9]/g, '')
                        .toString(),
                    )
                }
            })

        if (parseInt(tax) > 0) {
            tax_total = (parseInt(tax) / 100) * parseInt(subtotal)
        }

        grandTotal = parseInt(subtotal) + parseInt(tax_total)

        $('.subtotalCart').html('Rp ' + formatRupiah(subtotal.toString()))
        $('.taxtotalCart').html(
            'Rp ' + formatRupiah(tax_total.toString()) + ' (' + tax + ' %)',
        )
        $('.grandTotalCart').html('Rp ' + formatRupiah(grandTotal.toString()))
    })

    function removeCart(id) {
        setTimeout(function() {
            $.ajax({
                url: domain + domainpath + '/m-ecommerce/account/cart/delete/' + id,
                type: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    timeout: 0,
                },
                success: function(data, json, errorThrown) {
                    if (data.status == true) {


                        $('#cartid' + id).remove()
                        $('.cartdata').trigger('change')

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
    }

    function removeAll() {
        Swal.fire({
            title: 'Apakah Anda Yakin ?',
            text: 'Keranjang akan di kosongkan seluruhnya',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Asyiaap',
        }).then((result) => {
            if (result.value) {
                setTimeout(function() {
                    $.ajax({
                        url: domain + domainpath + '/m-ecommerce/account/cart/delete-all',
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                            timeout: 0,
                        },
                        success: function(data, json, errorThrown) {
                            if (data.status == true) {
                                $('#totalinCart').html(formatRupiah(data.subtotal.toString()))

                                $(".cartalldata").html('');
                                $('.cartdata').trigger('change')

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
            }
        })
    }
</script>
@endsection