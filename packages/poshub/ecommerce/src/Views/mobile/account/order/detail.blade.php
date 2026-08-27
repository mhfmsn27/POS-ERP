@extends('ecommerce::layouts.mobile')
@section('content')
<div class="header fixed-top line4-bt">
    <div class="left">
        <a href="{{ url()->previous() }}" class="icon back-btn"><i class="icon-left-btn"></i></a>
    </div>
    <h6>Detail Pesanan</h6>
</div>
<div class="app-content">
    <div class="tf-container">
        <div class="mt-16 time-line-order">
            <div class="icon @if($transaction->status == 'hold') completed @endif" >
                <i class="icon-check"></i>
                <span class="progress-order">Di Bayar</span>
            </div>
            <div class="line"></div>
            <div class="icon @if($transaction->status == 'hold' || $transaction->status == 'ordered') completed @endif">
                <i class="icon-check"></i>

                <span class="progress-order">Di Kirim</span>
            </div>
            <div class="line"></div>
            <div class="icon @if($transaction->status == 'hold' || $transaction->status == 'ordered' || $transaction->status == 'transit') completed @endif">
                <i class="icon-check"></i>

                <span class="progress-order ">Di Terima</span>
            </div>

        </div>
        <div class="py-16 mt-40 line4-bt line-top">
            <div class="tf-container">
                <h5>Informasi Toko / Cabang</h5>
                <div class="box-address chosedAddress mt-16">
                    <label for="address">
                        <h6><?= $transaction->store->name ?? ''; ?> </h6>
                        <p class="mt-4 text-caption text-black"><?= $transaction->store->phone ?? ''; ?></p>
                        <p class="mt-4 text-caption text-black"><?= $transaction->store->address ?? ''; ?> </p>
                    </label>
                </div>
            </div>
        </div>
        @if($transaction->shipping_detail)
        <div class="py-16 line4-bt ">
            <div class="tf-container">
                <h5>Informasi Jasa Antar</h5>
                <div class="box-address chosedAddress mt-16">
                    <label for="address">
                        <h6><?= $transaction->shipping_detail->curir_name ?? ''; ?> </h6>
                        <p class="mt-4 text-caption text-black"><?= $transaction->shipping_detail->curir_service ?? ''; ?></p>
                        <p class="mt-4 text-caption text-black"><?= number_format($transaction->shipping_charges); ?> </p>
                    </label>
                </div>
            </div>
        </div>
        <div class="py-16 line4-bt ">
            <div class="tf-container">
                <h5>Tujuan Pengiriman</h5>
                <div class="box-address mt-16">
                    <label for="address">
                        <h6><?= $transaction->shipping_detail->name ?? ''; ?> </h6>
                        <p class="mt-4 text-caption text-black"><?= $transaction->shipping_detail->phone ?? ''; ?></p>
                        <p class="mt-4 text-caption text-black"><?= $transaction->shipping_detail->address_detail ?? ''; ?> </p>
                    </label>
                </div>
            </div>
        </div>
        @endif
        <div class="py-16 line4-bt ">
            <div class="tf-container">
                <h5>Informasi Produk</h5>
                @foreach ($transaction->sell as $sell)
                <a href="{{route('ecommerce.mobile.shop_detail',$sell->product_id)}}" class="order-item py-12">
                    <div class="img">
                        <img src="{{asset($sell->product->default_image ?? 'uploads/image.jpg')}}" class="rounded" alt="img">
                    </div>
                    <div class="content">
                        <div class="left">
                            <h6>{{substr($sell->variation->full_name ?? '',0,20)}}... </h6>
                            <span class="text-caption">Qty: {{number_format($sell->qty)}} {{$sell->unit->name ?? ''}}</span>
                            <br />
                            <span class="text-caption">Harga: {{number_format($sell->unit_price)}}</span>
                        </div>
                        <span class="price">
                            Rp {{number_format($sell->subtotal)}}
                        </span>
                    </div>
                </a>
                @endforeach

            </div>
        </div>

        <div class="py-16 line4-bt ">
            <div class="tf-container">
                <ul class="line-bt pb-16">
                    <li class="d-flex justify-content-between text-caption">
                        <span>Subtotal</span>
                        <span>Rp {{number_format($transaction->total_before_tax)}} </span>
                    </li>
                    <li class="d-flex justify-content-between mt-8 text-caption">
                        <span>Pajak</span>
                        <span>Rp {{number_format($transaction->tax_final)}} </span>
                    </li>
                    <li class="d-flex justify-content-between mt-8 text-caption">
                        <span>Ongkos Kirim</span>
                        <span>Rp {{number_format($transaction->shipping_charges)}} </span>
                    </li>
                    <li class="d-flex justify-content-between mt-8 text-caption">
                        <span>Grand Total</span>
                        <span>Rp {{number_format($transaction->final_total)}} </span>
                    </li>

                </ul>
            </div>
        </div>


    </div>
</div>
@if($transaction->status == 'hold')
<div class="footer-fixed p-16 d-flex justify-content-between">
    <a href="javascript:void(0);" onclick="payTransaction(<?= $transaction->id; ?>,'bank');" class="tf-btn primary me-3">Manual Transfer</a>
    <a href="javascript:void(0);" onclick="payTransaction(<?= $transaction->id; ?>,'midtrans');" class="tf-btn primary">E-Wallet</a>
</div>
@endif

@if($transaction->status == 'transit')
<div class="footer-fixed p-16 d-flex justify-content-between">
    <a href="javascript:void(0);" onclick="trackingPesanan(<?= $transaction->id; ?>);" class="tf-btn primary me-3">Tracking Pesanan</a>
    <a href="javascript:void(0);" onclick="receivedOrder(<?= $transaction->id; ?>);" class="tf-btn primary">Terima Pesanan</a>
</div>
@endif
<input type="hidden" id="idTransaction" value="<?= $transaction->id; ?>">
<!-- Modal For Add Payment -->
<div class="modal fade modalRight" id="modalPayment">
    <form id="addpayEcommercementPurchase" method="POST" class="modal-dialog" role="document">
        @csrf
        <div class="modal-content">
            <div class="header fixed-top">
                <div class="left" data-bs-dismiss="modal">
                    <a href="javascript:void(0);" class="icon"><i class="icon-left-btn"></i></a>
                </div>
                <h6>Tambah Pembayaran</h6>
            </div>
            <div class="overflow-auto app-content style-7">
                <div class="tf-container">
                    <fieldset class="mt-20 input-fill">
                        <label>Dari Bank <span class="required">*</span></label>
                        <select class="form-control" name="from_bank">
                            <option value=""></option>
                            @foreach($banks as $b)
                            <option value="{{$b->id}}">{{$b->bank_name}}</option>
                            @endforeach
                        </select>
                    </fieldset>

                    <fieldset class="mt-20 input-fill">
                        <label>Ke Bank <span class="required">*</span></label>
                        <select class="form-control" name="to_bank">
                            <option value=""></option>
                            @foreach($ecommercebank as $ebanks)
                            <option value="{{$ebanks->id}}">{{$ebanks->bank_name}}</option>
                            @endforeach
                        </select>
                    </fieldset>
                    <fieldset class="mt-20 input-fill">
                        <label>Nomor Rekening <span class="required">*</span></label>
                        <input type="text" required class="form-control" name="no_rek" id="no_rek">
                    </fieldset>
                    <fieldset class="mt-20 input-fill">
                        <label>Nominal Transfer <span class="required">*</span></label>
                        <input type="text" class="form-control" value="0" id="payment_amount" name="amount">
                    </fieldset>
                    <fieldset class="mt-20 input-fill">
                        <label>Upload Bukti Pembayaran <span class="required">*</span></label>
                        <input type="file" class="form-control" name="file">
                    </fieldset>

                </div>
            </div>
            <div class="footer-fixed d-flex justify-content-between p-16">
                <button onclick="showPayment();" type="button" class="tf-btn primary me-3">Daftar Bank</button>
                <button type="submit" class="tf-btn primary">Kirim Bukti</button>
            </div>
        </div>

    </form>
</div>
<!-- End Add Payment -->

<!-- Bank List -->
<div class="modal fade modalRight" id="bankList">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="header fixed-top">
                <div class="left" data-bs-dismiss="modal">
                    <a href="javascript:void(0);" class="icon"><i class="icon-left-btn"></i></a>
                </div>
                <h6>Daftar Bank</h6>
            </div>
            <div class="overflow-auto app-content style-7">
                <div class="tf-container">

                    <ul class="mt-20">
                        @foreach ($ecommercebank as $ebank)
                        <li class="d-flex align-items-center gap-20 py-16 line-bt">
                            <div>
                                <img src="<?= asset($ebank->logo); ?>" class="rounded" style="width:80px;">
                            </div>
                            <a class="locationlist">
                                <h6>{{$ebank->bank_name}}</h6>
                                <p class="mt-4 text-caption" id="aListPhone">Nomor Rekening : {{$ebank->no_rek}} </p>
                                <p class="mt-2 text-caption" id="aListAddress">A/N : {{$ebank->an}} </p>
                            </a>
                        </li>
                        @endforeach
                    </ul>

                </div>
            </div>
            <div class="footer-fixed d-flex justify-content-between p-16">
                <a href="{{route('ecommerce.mobile.order_detail',$transaction->id)}}#modalPayment" data-bs-toggle="modal" class="tf-btn primary">Kirim Bukti Pembayaran</a>
            </div>
        </div>

    </div>
</div>
<!-- End Bank List -->

<!-- Modal Tracking Pesanan -->
<div class="modal fade modalRight" id="trackingPesanan">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="header fixed-top">
                <div class="left" data-bs-dismiss="modal">
                    <a href="javascript:void(0);" class="icon"><i class="icon-left-btn"></i></a>
                </div>
                <h6>Tracking Pesanan Saya</h6>
            </div>
            <div class="overflow-auto app-content style-7">
                <div class="tf-container">

                    <ul class="mt-20">
                        <div class="mt-12" id="trackingListData">


                        </div>
                    </ul>

                </div>
            </div>
        </div>

    </div>
</div>
<!-- End Tracking -->

@endsection

@section('scripts')
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{$settings->client_key}}"></script>
<script>
    function payTransaction(id, method) {
        var ecommerceMethode = method
        if (ecommerceMethode == 'midtrans') {
            setTimeout(function() {
                $.ajax({
                    url: domain + domainpath + '/m-ecommerce/account/orders/pay-transaction/' + id,
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        timeout: 0,
                    },
                    data: '',
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
                            snap.pay(data.snap, {
                                onSuccess: function(result) {
                                    window.location = '/m-ecommerce/account/orders/hold'
                                },
                                onPending: function(result) {
                                    location.reload();
                                },
                                onError: function(result) {
                                    location.reload();
                                },
                                onClose: function(result) {
                                    location.reload();
                                },
                            })
                        }
                    },

                    cache: false,
                    contentType: false,
                    processData: false,
                })
            }, 130)
        } else {
            showPaymentModal(id)
        }
    }

    function showPaymentModal(id) {

        $('#modalPayment').modal('toggle')
    }

    function showPayment() {
        console.log("hallo");
        $('#modalPayment').modal('toggle')
        $('#bankList').modal('toggle')
    }

    function bankModal() {
        $('#addpayEcommerce').modal('toggle')
        $('#bank_modal').modal('show')
    }

    function closePayment() {
        $('#addpayEcommerce').modal('toggle')
    }

    function closeBank() {
        $('#bank_modal').modal('toggle')
    }

    function backToList() {
        $('#orders').addClass('active show')
        $('#orderDetails').removeClass('active show')
        $('#trackingOrders').removeClass('active show')
    }

    function trackingPesanan(id) {
        $.ajax({
            url: domain + domainpath + '/m-ecommerce/account/orders/get-tracking/' + id,
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                Accept: 'application/json',
                'Content-Type': 'application/json',
                timeout: 0,
            },
            data: '',
            success: function(data, json, errorThrown) {
                if (data.status == true) {
                    $('#listTracking').html('')

                    var itemTrack = ''
                    $.each(data.trackings, function(index, item) {
                        itemTrack +=
                            ` <div class="timeline-tracking">
                                <div class="time-day">
                                    <h6 class="fw-6 text-caption text-black">` + item.date + `</h6>
                                    <p class="text-sm mt-8">` + item.time + `</p>
                                </div>
                                <div class="process-track">
                                    <h6 class="fw-6 text-caption text-black">` + item.city + `</h6>
                                    <p class="mt-4 text-black text-sm-start">` + item.desc + `</p>

                                </div>
                            </div>`
                    })

                    $('#trackingListData').html($itemTrack)
                    $("#trackingPesanan").modal("toggle");
                } else {
                    Swal.fire({
                        title: 'Error',
                        html: data.message,
                        width: 'auto',
                        confirmButtonText: 'Ok Saya Mengerti',
                        showCancelButton: false,
                    })
                    return false
                }
            },

            cache: false,
            contentType: false,
            processData: false,
        })

    }

    function receivedOrder(id) {
        Swal.fire({
            title: 'Apakah Anda Yakin ?',
            text: 'Klik Konfirmasi untuk konfirmasi penerimaan pesanan ini!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Konfirmasi',
        }).then((result) => {
            if (result.value) {
                setTimeout(function() {
                    $.ajax({
                        url: domain + domainpath + '/m-ecommerce/account/orders/confirmation/' + id,
                        type: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            Accept: 'application/json',
                            'Content-Type': 'application/json',
                            timeout: 0,
                        },
                        success: function(data, json, errorThrown) {
                            if (data.status == true) {
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

                                setTimeout(function() {
                                    window.location = '/m-ecommerce/account/orders/received'
                                }, 1000)
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    html: data.message,
                                    width: 'auto',
                                    confirmButtonText: 'Ok Saya Mengerti',
                                    showCancelButton: false,
                                })
                                return false
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

    $('#payment_amount').on('keyup', function() {
        var nominal = $('#payment_amount').val()
        $('#payment_amount').val(formatRupiah(nominal.toString()))
    })

    $('form#addpayEcommercementPurchase').on('submit', function(e) {
        e.preventDefault()
        var formData = new FormData(this)
        setTimeout(function() {
            $.ajax({
                url: domain +
                    domainpath +
                    '/m-ecommerce/account/orders/add-payment/' +
                    $('#idTransaction').val(),
                type: 'POST',
                data: formData,
                success: function(data, json, errorThrown) {
                    if (data.status == false) {

                        Swal.fire({
                                title: 'Peringatan',
                                text: data.message,
                                width: 'auto',
                                confirmButtonText: 'Ok, Saya Mengerti',
                                showCancelButton: false,
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    $('#openModal').on('click')
                                }
                            },
                        )
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
                        $('#modalPayment').modal('toggle')
                        $('#no_rek').val('')
                        $('#payment_amount').val(0)
                    }
                },

                cache: false,
                contentType: false,
                processData: false,
            })
        }, 130)
    })
</script>
@endsection