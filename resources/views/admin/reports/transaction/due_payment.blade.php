@extends('layouts.admin')
@section('content')

<div class="content-page">
    <div class="container-fluid">
        <x-admin.validation-component></x-admin.validation-component>
        <div class="row">
            <div class="col-md-12 col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">{{$page}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <input type="hidden" id="identity" value="{{ $data->id }}">
                            <div class="col-sm-12 col-md-6 mb-3">
                                <label class="control-label">{{__('general.start_date')}}</label>
                                <div class="input-group">
                                    <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}">
                                </div>
                            </div>

                            <div class="col-sm-12 col-md-6 mb-3">
                                <label class="control-label">{{__('general.end_date')}}</label>
                                <div class="input-group">
                                    <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" onclick="searchProduct()"><i class="fa fa-search"></i></button>
                                    </div>
                                </div>
                            </div>
                            <hr style="border: 2px solid black">
                            <div class="col-12">
                                <h4>{{__('report.general_info')}}</h4>
                                <table class="table">
                                    <tr>
                                        <th>{{__('customer.name')}} : </th>
                                        <th>{{ $data->customer->name ?? '' }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{__('purchase.date')}} : </th>
                                        <th>{{ my_date($data->created_at) }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{__('sell.due_total')}} : </th>
                                        <th> {{ my_currency($data->final_total) }}</th>
                                    </tr>
                                    <tr>
                                        <th>{{__('report.product_purchase')}} : </th>
                                        <th>
                                            <ul>
                                                @foreach($data->sell as $s)
                                                <li> {{$s->variation->product->name ?? ''}} @if($s->variation->name != 'no-name') {{ ' - '. $s->variation->name ?? '' }} @endif - ({{ number_format($s->unit_price) }})</li>
                                                @endforeach

                                            </ul>
                                        </th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            @can("Tambah Pembayaran Hutang")
                            <a href="javascript:void(0)" id="{{ $data->id }}" onclick="getpaymentmodal(this.id)" class="btn btn-sm btn-success float-end" style="margin-top: -5px; margin-right:5px; border: 2px solid white"><i class="fa fa-plus-circle"></i> {{__('general.add_payment')}}</a>
                            @endcan 
                        </div>
                    </div>
                    <div class="card-body" id="dueContent">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{__('general.payment_date')}}</th>
                                        <th>{{__('general.payment_total')}}</th>
                                        <th>{{__('general.payment_note')}}</th>
                                        <th>{{__('general.note')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payment as $d)
                                    <tr class="purchase_order">
                                        @php
                                        $method = '';
                                        if ($d->method == 'cash') {
                                        $method = 'Cash';
                                        } elseif ($d->method == 'bank_transfer') {
                                        $method = 'Bank Transfer';
                                        } elseif ($pay->method == 'card') {
                                        $method = 'Card';
                                        } elseif ($d->method == 'other') {
                                        $method = 'Lainnya';
                                        }
                                        @endphp
                                        <td> {{ $d->created_at }} </td>
                                        <td> {{ number_format($d->amount) }} </td>
                                        <td> {{ $method }} </td>
                                        <td> {{ $d->note }} </td>

                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr style="background-color: #5cb85c; border: 1px solid white" class="text-white">
                                        <th colspan="3" style="height: 50px; font-size:25px">{{__('general.sell_due_amount')}} : </th>
                                        <th style="font-size: 30px">{{ $data->pay_total }}</th>
                                    </tr>
                                    <tr style="background-color: #5cb85c; border: 1px solid white" class="text-white">
                                        <th colspan="3" style="height: 50x; font-size:25px">{{__('report.remaining_debt')}} : </th>
                                        <th style="font-size: 30px">{{ number_format($data->due_total) }}</th>
                                    </tr>
                                </tfoot>
                            </table> 
                        </div> 
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 
<x-admin.modal.add-payment-purchase></x-admin.modal.add-payment-purchase>
@section('scripts')
<script>
    $("form#addPaymentPurchase").on("submit", function(e) {
        spinner.show();
        e.preventDefault();
        var formData = new FormData(this);
        setTimeout(function() {
            $.ajax({
                url: domain + domainpath + "/pos-admin/purchase/add-pay",
                type: "POST",
                data: formData,
                success: function(data, json, errorThrown) {
                    if (data.message == "error") {
                        var errorsHtml = "";
                        $.each(data.errors, function(index, value) {
                            errorsHtml +=
                                '<ul class="list-group"><li class="list-group-item alert alert-danger">' +
                                value +
                                "</li></ul>";
                        });
                        Swal.fire({
                                title: data.message + " " + data.status,  
                                html: errorsHtml,
                                width: "auto",
                                confirmButtonText: try_again,
                                cancelButtonText: "Cancel",
                                showCancelButton: false,
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    $("#openModal").on("click");  
                                }
                            }
                        );
                        spinner.hide();
                    } else if(data.message == "nothing") {
                        Swal.fire({
                                title: "Peringatan",  
                                text: data.errors,
                                width: "auto",
                                confirmButtonText: "Ok, Saya Mengerti",
                                cancelButtonText: "Cancel",
                                showCancelButton: false,
                            },
                            function(isConfirm) {
                                if (isConfirm) {
                                    $("#openModal").on("click");  
                                }
                            }
                        );
                        spinner.hide();
                    } else {
                        toastr.success(success, {
                            timeOut: 5e3,
                            closeButton: !0,
                            debug: !1,
                            newestOnTop: !0,
                            progressBar: !0,
                            positionClass: "toast-top-right",
                            preventDuplicates: !0,
                            onclick: null,
                            showDuration: "100",
                            hideDuration: "1000",
                            extendedTimeOut: "1000",
                            showEasing: "swing",
                            hideEasing: "linear",
                            showMethod: "fadeIn",
                            hideMethod: "fadeOut",
                            tapToDismiss: !1,
                        });
                        spinner.hide();
                        $("#addpay").modal('toggle');
                        $("#payment_amount").val(0);
                        $("#paymentnote").val("");
                        playSound(domainpath + '/public/sound/connection')
                        location.reload();
                    }
                },
                cache: false,
                contentType: false,
                processData: false,
            });
        }, 130);
    });
  
  
    function getpaymentmodal(id) {
        $.ajax({
            url: domain + domainpath + "/pos-admin/report/transaction/sell-element/" + id,
            type: 'GET',
            data: '',
            success: function(data, json, errorThrown) {
                $("#tri").val(id);
                $("#maxPayment").val(data.max_amount); 
                $("#addpay").modal("show")
            },

            cache: false,
            contentType: false,
            processData: false
        }); 
    }

    var start = null;
    var end = null;

    function searchProduct() {
        var start = $("#start_date").val();
        var end = $("#end_date").val();
        var identity = $("#identity").val();
        var url = domainpath + '/pos-admin/report/transaction/due/payment/' + identity + '?start_date=' + start + '&end_date=' + end + '';
        console.log(url);
        spinner.show();
        setTimeout(function() {
            $.ajax({
                url: url,
                dataType: "html",
                success: function(result) {
                    $('#dueContent').html(result);

                }
            });
            spinner.hide();
        }, 130);
    }
</script>
@endsection
@endsection