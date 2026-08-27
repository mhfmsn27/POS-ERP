@extends('layouts.admin')
@section('content')
<div class="content-page">
    <div class="container-fluid">
        <x-admin.validation-component></x-admin.validation-component>
        <div class="card ">
            <div class="card-header d-flex justify-content-between">
                <div class="iq-header-title">
                    <h4 class="card-title">{{$page}}</h4>
                </div>
            </div>
            <div class="card-body">
                <div class="row invoice-info">
                    <div class="col-sm-4 invoice-col">
                        {{__('sidebar.customer')}} :
                        <address>
                            {{ $data->customer->name ?? '' }},
                            {{ $data->customer->city ?? '' . ' ' . $data->customer->address ?? '' }}
                            <br>{{__("general.phone")}}: {{ $data->customer->phone ?? '' }}
                        </address>
                    </div>

                    <div class="col-sm-4 invoice-col">
                        {{__('general.store')}} :
                        <address>
                            <strong>{{ $data->store->name ?? '' }},</strong>
                            <br> {{ $data->store->address }}
                        </address>
                    </div>

                    <div class="col-sm-4 invoice-col">
                        <b>{{__('general.ref_no')}}:</b> #{{ $data->ref_no }}<br>
                        <b>{{__('general.date')}}:</b> {{ my_date($data->created_at) }}<br>
                        <b>{{__('general.payment_status')}}:</b> {{ $status[$data->status] }}<br>
                    </div>
                </div>

                <br>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr style="background-color: #3c8dbc; border: 1px solid white" class="text-white">
                                        <th>#</th>
                                        <th>{{__('produk.name')}}</th>
                                        <th>{{__('purchase.quantity')}} </th>
                                        <th>{{__('general.sell_price')}}</th>
                                        <th class="text-right">{{__('general.subtotal')}}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                    $no = 1;
                                    $jumlah = 0;
                                    @endphp
                                    @foreach($data->sell as $gd )

                                    @php
                                    $subtotal = 0;
                                    $subtl = 0;

                                    $subtl += $gd->unit_price * $gd->qty;
                                    $jumlah += $subtl;
                                    $subtotal += $gd->unit_price * $gd->qty;
                                    @endphp
                                    <tr>
                                        <td>{{ $no++ }}</td>
                                        <td> {{$gd->variation->product->name ?? ''}} @if($gd->variation->name != 'no-name') {{ ' - '. $gd->variation->name ?? '' }} @endif </td>
                                        <td>
                                            <b>
                                                {{ $gd->qty}}
                                                @if($gd->unit_id)
                                                Atau ( {{$gd->qty_into_unit}} ) Dalam {{$gd->unit->name ?? ''}}
                                                @endif
                                            </b>
                                        </td>
                                        <td>{{ number_format($gd->unit_price) }} </td>
                                        <td>{{ number_format($subtotal) }}</td>
                                    </tr>
                                    @endforeach


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-12 col-xs-12">
                        <h4>{{__('general.payment_info')}}:</h4>
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tbody>
                                    <tr style="background-color: #3c8dbc; border: 1px solid white" class="text-white">
                                        <th>#</th>
                                        <th>{{__('general.date')}}</th>
                                        <th>{{__('general.ref_no')}}</th>
                                        <th>{{__('general.payment_total')}}</th>
                                        <th>{{__('general.payment_method')}}</th>
                                        <th>{{__('general.payment_note')}}</th>
                                    </tr>
                                    @php
                                    if(count($data->paycredit) == 0) {
                                    echo ' <tr>
                                        <td colspan="5" class="text-center">No payments found </td>
                                    </tr>';
                                    } else {
                                    foreach ($data->paycredit as $pay) {
                                    if($pay->method == 'cash') {
                                    $method = 'Cash';
                                    } else if($pay->method == 'bank_transfer') {
                                    $method = 'Bank Transfer';
                                    } else if($pay->method == 'card') {
                                    $method = 'Card';
                                    } else if($pay->method == 'other') {
                                    $method = __('report.other');
                                    }
                                    echo '<tr>
                                        <td>#</td>
                                        <td>'.$pay->created_at.'</td>
                                        <td>'.$data->ref_no.'</td>
                                        <td>'.number_format($pay->amount).'</td>
                                        <td>'.$method.'</td>
                                        <td>'.$pay->note.'</td>
                                    </tr>';
                                    }
                                    }
                                    @endphp


                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6 col-sm-12 col-xs-12 ">
                        <div class="table-responsive">
                            <table class="table">
                                <tbody>
                                    <tr>
                                        <th>{{__('purchase.net_total')}}: </th>
                                        <td></td>
                                        <td>{{number_format($jumlah)}}</td>
                                    </tr>
                                    <tr>
                                        <th>{{__('purchase.discount_total')}}:</th>
                                        <td>
                                            <b>(-)</b>
                                        </td>
                                        <td> {{ number_format($data->discount_amount) }} @if($data->discount_type != 'fixed') % @endif </td>
                                    </tr>
                                    <tr>
                                        <th>{{__('purchase.tax')}}:</th>
                                        <td><b>(+)</b></td>
                                        <td>
                                            {{number_format($data->tax_amount)}}%
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>{{__('purchase.shipping_cost')}}:</th>
                                        <td><b>(+)</b></td>
                                        <td>{{ number_format($data->shipping_charges) }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{__('purchase.other_payment')}}:</th>
                                        <td><b>(+)</b></td>
                                        <td>{{ number_format($data->other_charges) }}</td>
                                    </tr>

                                    @if($data->voucher != null)
                                    <tr>
                                        <th>Penggunaan Voucher: <br> {{$data->voucher->name ?? ''}} <br> <b> @if($data->voucher->type == 'price') Potongan Harga @else Potongan Ongkos Kirim @endif</b></th>
                                        <td><b>(-)</b></td>
                                        <td>{{number_format($data->voucher->amount)}}</td>
                                    </tr> 
                                    @endif
                                    <tr>
                                        <th>{{__('general.total')}}:</th>
                                        <td></td>
                                        <td>{{ number_format($data->final_total) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="row text-center mt-5 mb-3">
                    <div class="col-12">
                        @can("Print Penjualan")
                        <a target="_blank" href="{{ route('sell.print',$data->id) }}" class="btn btn-primary"><i class="fa fa-print"></i> {{__('general.print')}}</a>
                        @endcan
                        <a href="javascript:void(0)" id="{{ $data->id }}" onclick="printcepeipt(this.id)" class="btn btn-primary"><i class="fa fa-print"></i> {{__('pos.print')}}</a>
                        <a href="javascript:void(0)" id="{{ $data->id }}" onclick="printpage(this.id)" class="btn btn-primary"><i class="fa fa-print"></i> {{__('pos.print_page')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    function printcepeipt(id) {
        $.ajax({
            type: "GET",
            url: domainpath + "/pos/print/" + id,
            data: "",
            success: function(data) {

                if (data.status == 'error') {
                    toastr.error(data.message, data.errors, {
                        positionClass: "toast-top-right",
                        timeOut: 5e3,
                        closeButton: !0,
                        debug: !1,
                        newestOnTop: !0,
                        progressBar: !0,
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
                }

                if (data.message == 'onlineprinter') {
                    window.open(data.url)
                }

                if (data.message == 'offlineprinter') {
                    toastr.success(data.text, "On Process", {
                        positionClass: "toast-bottom-left",
                        timeOut: 5e3,
                        closeButton: !0,
                        debug: !1,
                        newestOnTop: !0,
                        progressBar: !0,
                        preventDuplicates: !0,
                        onclick: null,
                        showDuration: "300",
                        hideDuration: "1000",
                        extendedTimeOut: "1000",
                        showEasing: "swing",
                        hideEasing: "linear",
                        showMethod: "fadeIn",
                        hideMethod: "fadeOut",
                        tapToDismiss: !1,
                    });
                }
            },
        });
    }

    function printpage(id) {
        url = domain + domainpath + '/pos/print-page/' + id;
        window.open(url, '_blank', 'location=yes,height=870,width=420,scrollbars=yes,status=yes')
    }
</script>
@endsection