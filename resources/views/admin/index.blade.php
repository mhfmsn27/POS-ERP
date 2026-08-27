@extends('layouts.admin')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/vendors/apexcharts/apexcharts.css') }}">
@endsection

@section("content")
<div class="content-page">
    <div class="container-fluid">

        <div class="row">
            <div class="col-md-6 col-lg-8">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div id="sellMonth" style="height: 500px;"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="col-lg-12">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-body relative-background">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle card-icon iq-bg-primary mr-3"> <i class="ri-exchange-dollar-line"></i></div>
                                <div class="text-left">
                                    <h4 class="">{{ my_currency($data['total_purchase']) }}</h4>
                                    <h5 class="">{{__('purchase.purchase_total')}}</h5>
                                </div>
                            </div>
                            <div class="background-image">
                                <img src="{{asset('assets/images/po.svg')}}" style="width: 80px;" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-body relative-background">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle card-icon iq-bg-primary mr-3"> <i class="ri-exchange-dollar-line"></i></div>
                                <div class="text-left">
                                    <h4 class="">{{ my_currency($data['total_sell']) }}</h4>
                                    <h5 class="">{{__('sell.total_sell')}}</h5>
                                </div>
                            </div>
                            <div class="background-image">
                                <img src="{{asset('assets/images/sales.svg')}}" style="width: 80px;" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-body relative-background">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle card-icon iq-bg-primary mr-3"> <i class="ri-exchange-dollar-line"></i></div>
                                <div class="text-left">
                                    <h4 class="">{{ my_currency($data['total_expense']) }}</h4>
                                    <h5 class="">{{__('expense.total_expense')}}</h5>
                                </div>
                            </div>
                            <div class="background-image">
                                <img src="{{asset('assets/images/expense.svg')}}" style="width: 80px;" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="card card-block card-stretch card-height">
                        <div class="card-body relative-background">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle card-icon iq-bg-primary mr-3"> <i class="ri-exchange-dollar-line"></i></div>
                                <div class="text-left">
                                    <h4 class="">{{ my_currency($data['total_due']) }}</h4>
                                    <h5 class="">{{__('sell.total_due')}}</h5>
                                </div>
                            </div>
                            <div class="background-image">
                                <img src="{{asset('assets/images/due.svg')}}" style="width: 80px;" class="img-fluid">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-body">
                        <div id="transactiondata" style="height:350px"></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-6">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Pendapatan vs Pengeluaran</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="incomeExpense" style="height:350px"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Log Aktivitas</h4>
                        </div>

                    </div>
                    <div class="card-body">
                        <ul class="iq-timeline">
                            @foreach ($logs as $log)
                            <li>
                                <div class="timeline-dots"></div>
                                <h6 class="float-left mb-1"><?= $log->log_name; ?> ( <?=$log->causer->name ?? '';?> )</h6>
                                <small class="float-right mt-1"><?= $log->created_at->format('Y-m-d'); ?></small>
                                <div class="d-inline-block w-100">
                                    <p><?= $log->description; ?></p>
                                </div>
                            </li>
                            @endforeach

                           
                        </ul>
                    </div>
                </div>
                
            </div>

            <div class="col-lg-8">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">10 Produk Terpopuler</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="popularproduct" style="height:350px"></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-12">
                <div class="card card-block card-stretch card-height">
                    <div class="card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">{{__('general.new_activity')}}</h4>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <a class="nav-link active" id="sell-tab" data-toggle="tab" href="#sell" role="tab" aria-controls="sell" aria-selected="true">{{__('general.sell')}}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="purchase-tab" data-toggle="tab" href="#purchase" role="tab" aria-controls="purchase" aria-selected="false">{{__('sidebar.purchase')}}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="transfer-tab" data-toggle="tab" href="#transfer" role="tab" aria-controls="transfer" aria-selected="false">{{__('sidebar.r_stock_transfer')}}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="adjust-tab" data-toggle="tab" href="#adjust" role="tab" aria-controls="adjust" aria-selected="false">{{__('sidebar.stock_adjs')}}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="return-tab" data-toggle="tab" href="#return" role="tab" aria-controls="return" aria-selected="false">{{__('sidebar.return')}}</a>
                            </li>
                            <li class="nav-item" role="presentation">
                                <a class="nav-link" id="returnsell-tab" data-toggle="tab" href="#returnsell" role="tab" aria-controls="returnsell" aria-selected="false">{{__('sell.return_sell')}}</a>
                            </li>
                        </ul>
                        <div class="tab-content" id="myTabContent">
                            <div class="tab-pane fade show active" id="sell" role="tabpanel" aria-labelledby="sell-tab">
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{__('general.date')}}</th>
                                                <th>{{__('general.ref_no')}}</th>
                                                <th>{{__('customer.name')}}</th>
                                                <th>{{__('purchase.net_total')}}</th>
                                                <th>{{__('general.payment_total')}}</th>
                                                <th>{{__('sell.due_total')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data['act_sell'] as $as)
                                            <tr>
                                                <td> {{my_date($as->created_at)}}</td>
                                                <td>{{ $as->ref_no }}</td>
                                                <td>{{ $as->customer->name ?? '' }}</td>
                                                <td>{{ number_format($as->final_total) }}</td>
                                                <td>{{ $as->pay_total }}</td>
                                                <td>{{ number_format($as->due_total ?? $as->final_total) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="purchase" role="tabpanel" aria-labelledby="purchase-tab">
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{__("general.date")}}</th>
                                                <th>{{__('general.ref_no')}}</th>
                                                <th>{{__('supplier.name')}}</th>
                                                <th>{{__('purchase.net_total')}}</th>
                                                <th>{{__('general.payment_total')}}</th>
                                                <th>{{__('general.po_due')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data['act_purchase'] as $ap)
                                            <tr>
                                                <td>{{my_date($ap->created_at)}}</td>
                                                <td>{{ $ap->ref_no }}</td>
                                                <td>{{ $ap->supplier->name ?? '' }}</td>
                                                <td>{{ number_format($ap->final_total) }}</td>
                                                <td>{{ $ap->pay_total }}</td>
                                                <td>{{ number_format($ap->due_total ?? $ap->final_total) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="transfer" role="tabpanel" aria-labelledby="transfer-tab">
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{__('general.date')}}</th>
                                                <th>{{__('general.ref_no')}}</th>
                                                <th>{{__('transfer.from')}}</th>
                                                <th>{{__('transfer.to')}}</th>
                                                <th>{{__('purchase.shipping_cost')}}</th>
                                                <th>{{__('purchase.net_total')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data['act_stransfer'] as $at)
                                            <tr>
                                                <td>{{ my_date($at->created_at) }}</td>
                                                <td>{{ $at->ref_no }}</td>
                                                <td> {{ $at->transfer->fromstore->name ?? '' }} </td>
                                                <td> {{ $at->transfer->tostore->name ?? '' }} </td>
                                                <td>{{ number_format($at->shipping_charges) }}</td>
                                                <td>{{ number_format($at->final_total) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="adjust" role="tabpanel" aria-labelledby="adjust-tab">
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{__('general.date')}}</th>
                                                <th>{{__('general.ref_no')}}</th>
                                                <th>{{__('purchase.net_total')}}</th>
                                                <th>{{__('adjustment.amount_recovered')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data['act_sadjustment'] as $aa)
                                            <tr>
                                                <td>{{ my_date($aa->created_at) }}</td>
                                                <td>{{ $aa->ref_no }}</td>
                                                <td>{{ number_format($aa->final_total) }}</td>
                                                <td> {{ number_format($aa->total_amount_recovered) }} </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="return" role="tabpanel" aria-labelledby="return-tab">
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{__('general.date')}}</th>
                                                <th>{{__("general.ref_no")}}</th>
                                                <th>{{__("purchase.parent_transaction")}}</th>
                                                <th>{{__('supplier.name')}}</th>
                                                <th>{{__('sell.total_return')}}</th>
                                                <th>{{__('general.total')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data['act_return'] as $ar)
                                            <tr>
                                                <td> {{ my_date($ar->created_at) }}</td>
                                                <td> {{ $ar->ref_no }} </td>
                                                <td> {{ $ar->transaction->ref_no ?? '' }} </td>
                                                <td> {{ $ar->supplier->name ?? '' }} </td>
                                                <td> {{ $ar->qty_return }} Qty Return </td>
                                                <td> {{ number_format($ar->final_total) }} </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="returnsell" role="tabpanel" aria-labelledby="returnsell-tab">
                                <div class="table-responsive mt-3">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>{{__('general.date')}}</th>
                                                <th>{{__("general.ref_no")}}</th>
                                                <th>{{__("purchase.parent_transaction")}}</th>
                                                <th>{{__('customer.name')}}</th>
                                                <th>{{__('sell.total_return')}}</th>
                                                <th>{{__('general.total')}}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($data['act_returnsell'] as $rs)
                                            <tr>
                                                <td> {{ my_date($rs->created_at) }}</td>
                                                <td> {{ $rs->ref_no }} </td>
                                                <td> {{ $rs->transaction->ref_no ?? '' }} </td>
                                                <td> {{ $rs->customer->name ?? '' }} </td>
                                                <td> {{ count($rs->sellreturn) }} Qty Return </td>
                                                <td> {{ number_format($rs->final_total) }} </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script src="{{ asset('assets/vendors/amcharts4/core.js') }}"></script>
<script src="{{ asset('assets/vendors/amcharts4/charts.js') }}"></script>
<script src="{{ asset('assets/vendors/amcharts4/animated.js') }}"></script>
<script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>

<script src="{{asset('theme/js/highcharts.js')}}"></script>
<script src="{{asset('theme/js/highcharts-3d.js')}}"></script>
<script src="{{asset('theme/js/highcharts-more.js')}}"></script>

<script src="{{ asset('assets/vendors/amcharts4/popular_product.js') }}"></script>
<script src="{{ asset('js/transactiondata.js') }}"></script>
@endsection