@extends('layouts.admin')
@section('content')
<div class="content-page">
    <div class="container-fluid">
        <x-admin.validation-component></x-admin.validation-component>
        <div class="row">

            <div class="col-md-12 col-12">
                <div class="card">
                    <div class="accordion" id="accordionSearching">
                        <div class="accordion-item rounded">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed fw-semibold" type="button" data-toggle="collapse" data-target="#searchdata" aria-expanded="false" aria-controls="searchdata">
                                    <i class="fa fa-search" style="margin-right: 5px;"></i> {{__('general.search')}}
                                </button>
                            </h2>
                            <div id="searchdata" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-parent="#accordionSearching">
                                <div class="accordion-body">
                                    <form action="{{route('profit.loss')}}" method="GET" class="row">
                                        <div class="col-sm-12 col-md-4 mb-3">
                                            <label class="control-label">{{__('store.choose_store')}}</label>
                                            <div class="input-group">
                                                <select class="form-control" id="store" name="store">
                                                    <option value="">{{__('store.choose_store')}}</option>
                                                    @foreach ($store as $st)
                                                    <option value="{{ $st->id }}" @if (isset($_GET['store'])) @if ($st->id==$_GET['store']) selected @endif @endif>{{ $st->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-4 mb-3">
                                            <label class="control-label">{{__('general.start_date')}}</label>
                                            <div class="input-group">
                                                <input type="date" name="start_date" id="start_date" class="form-control" value="{{ old('start_date') }}">
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-4 mb-3">
                                            <label class="control-label">{{__('general.end_date')}}</label>
                                            <div class="input-group">
                                                <input type="date" name="end_date" id="end_date" class="form-control" value="{{ old('end_date') }}">
                                                <div class="input-group-append">
                                                    <button class="btn btn-primary" type="submit"><i class="fa fa-search"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Purchase atau modal -->
            <div class="col-sm-12 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <span class="h4">{{ my_currency($data['total_purchase']->total ?? 0) }}</span>
                                <h6 class="text-uppercase text-muted mt-2 m-0">{{__('report.total_purchase')}}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Penjualan -->
            <div class="col-sm-12 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <span class="h4">{{ my_currency($data['total_sell']->total ?? 0) }}</span>
                                <h6 class="text-uppercase text-muted mt-2 m-0">{{__('report.total_sell')}}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Biaya Kirim Saat PO -->
            <div class="col-sm-12 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <span class="h4">{{ my_currency($data['purchase_shipping']) }}</span>
                                <h6 class="text-uppercase text-muted mt-2 m-0">{{__('report.shipping_purchase')}}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Diskon Saat PO -->
            <div class="col-sm-12 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <span class="h4">{{ my_currency($data['purchase_discount']) }}</span>
                                <h6 class="text-uppercase text-muted mt-2 m-0">{{__('report.purchase_discount')}}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Diskon Saat Penjualan -->
            <div class="col-sm-12 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <span class="h4">{{ my_currency($data['sell_discount']) }}</span>
                                <h6 class="text-uppercase text-muted mt-2 m-0">{{__('report.sell_discount')}}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Biaya Kirim Saat Penjualan -->
            <div class="col-sm-12 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <span class="h4">{{ my_currency($data['sell_shipping']) }}</span>
                                <h6 class="text-uppercase text-muted mt-2 m-0">{{__('report.shipping_sell')}}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Adjusment atau perapihan Stock -->
            <div class="col-sm-12 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <span class="h4">{{ my_currency($data['stock_adjustment']->total ?? 0) }}</span>
                                <h6 class="text-uppercase text-muted mt-2 m-0">Total Perapihan Stok</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Dana dipulihkan -->
            <div class="col-sm-12 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <span class="h4">{{ my_currency($data['amount_recovered']) }}</span>
                                <h6 class="text-uppercase text-muted mt-2 m-0">{{__('report.total_recovered')}}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Pengeluaran -->
            <div class="col-sm-12 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <span class="h4">{{ my_currency($data['total_expense']) }}</span>
                                <h6 class="text-uppercase text-muted mt-2 m-0">{{__('report.total_expense')}}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Ongkos Kirim Saat Transfer Stock -->
            <div class="col-sm-12 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <span class="h4">{{ my_currency($data['transfer_shipping']) }}</span>
                                <h6 class="text-uppercase text-muted mt-2 m-0">{{__('report.shipping_transfer')}}</h6>
                                <p>Ongkos Kirim Transfer </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Modal Terpakai -->
            <div class="col-sm-12 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <span class="h4">{{ my_currency($profitsell->modal) }}</span>
                                <h6 class="text-uppercase text-muted mt-2 m-0">{{__('report.used_capital')}}</h6>
                                <p>Modal Terpakai ( Purchase Order )</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Ongkos Kirim Saat Transfer Stock -->
            <div class="col-sm-12 col-lg-4 col-xl-4">
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col text-center">
                                <span class="h4">{{ my_currency($profitsell->terjual) }}</span>
                                <h6 class="text-uppercase text-muted mt-2 m-0">{{__('report.total_sell')}}</h6>
                                <p>( Setelah Dipotong Harga Pembelian )</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-12">
                <div class="card ">
                    <div class="card-content" id="profitContent">
                        <div class="card-header d-flex justify-content-between">
                            <div class="iq-header-title">
                                <a href="javascript:void(0)" data-toggle="modal" data-target="#download" class="btn btn-sm btn-success float-end" style="margin-top: -13px; border: 2px solid white; margin-top: -5px"><i class="fa fa-download"></i> Download Laporan </a>
                            </div>
                        </div>
                        <div class="card-body">

                            <br>
                            <div class="row">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <tbody>
                                                <tr style="background-color: #3c8dbc; border: 1px solid white" class="text-white">
                                                    <th>{{__('report.profitloss')}} </th>
                                                    <th></th>
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <td> {{__('report.total_return_after_po')}} </td>
                                                    <td class="text-right">: {{ my_currency($profitsell->dikembalikan) }}</td>
                                                    <td class="text-right">(-)</td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size:12px">{{__('report.total_adjustment')}} + {{__('report.total_expense')}} + {{__('report.shipping_purchase')}} + {{__('report.shipping_transfer')}} + {{__('report.sell_discount')}}</td>
                                                    @php
                                                    $adjustment = $data['stock_adjustment']->total ?? 0;
                                                    $jumlah = $adjustment + $data['total_expense'] + $data['purchase_shipping'] + $data['transfer_shipping'] + $data['sell_discount'];
                                                    @endphp
                                                    <td class="text-right">: {{ my_currency($jumlah) }}</td>
                                                    <td class="text-right">(-)</td>
                                                </tr>
                                                <tr>
                                                    <td style="font-size:12px">{{__('report.shipping_sell')}} + {{__('report.total_recovered')}} + {{__('report.purchase_discount')}}</td>
                                                    @php
                                                    $jml = $data['sell_shipping'] + $data['amount_recovered'] + $data['purchase_discount'];
                                                    @endphp
                                                    <td class="text-right">: {{ my_currency($jml) }}</td>
                                                    <td class="text-right">(+)</td>
                                                </tr>
                                                @php
                                                $profiitbersih = ($profitsell->terjual - $profitsell->dikembalikan) - $jumlah + $jml;
                                                @endphp
                                                <tr style="background-color: #5cb85c;" class="text-white">
                                                    <td> {{__('report.net_profit')}} </td>
                                                    <td class="text-right">: {{ my_currency($profiitbersih) }}</td>
                                                    <td class="text-right"></td>
                                                </tr>
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
</div>

<div class="modal fade" id="download" tabindex="-1" role="dialog" aria-labelledby="paymodal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-full modal-xl download" role="document">
        <form method="GET" target="_blank" action="{{ route('profitloss.download') }}" class="modal-content" style="height: 90vh;">
            <div class="modal-header header-modal" style="height: 5vh;">
                <h5 class="modal-title" id="">Download Laporan Laba Rugi</h5>
                <a href="javascript:void(0)" class="close" data-dismiss="modal" aria-label="Close">
                    <i class="fa fa-times text-danger"></i>
                </a>
            </div>
            <div class="modal-body" style="overflow: hidden;">
                <div class="row"> 
                    <div class="col-md-4 col-sm-12 mb-2">
                        <label>Pilih Toko</label>
                        <div class="input-group" style="height: 6vh;">
                            <select class="form-control" name="store">
                                <option value="">{{ __('store.choose_store') }}</option>
                                @foreach ($store as $st)
                                <option value="{{ $st->id }}" @if (isset($_GET['store'])) @if ($st->id == $_GET['store']) selected @endif @endif>
                                    {{ $st->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>  
                    <div class="col-md-4 col-sm-12 mb-2">
                        <label>Tanggal Awal</label>
                        <div class="input-group" style="height: 6vh;">
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                        </div>
                    </div> 
                    <div class="col-md-4 col-sm-12 mb-2">
                        <label>Sampai Tanggal</label>
                        <div class="input-group" style="height: 6vh;">
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                        </div>
                    </div> 
                </div> 
                <div class="row">
                    <div class="col-12 m-4 p-4">
                        <table style="width:100%">
                            <tr>
                                <td style="width:50%; text-align:right">
                                    <button class="btn btn-primary btn-large text-center" type="submit" name="excel" value="true">
                                        <img class="p-4" src="{{ asset('assets/icon/excel.png') }}" style="width:200px;">
                                        <p> Download Excel</p>
                                    </button>
                                </td>
                                <td>
                                    <button class="btn btn-primary btn-large text-center" type="submit" name="excel" value="false">
                                        <img class="p-4" src="{{ asset('assets/icon/pdf.png') }}" style="width:165px">
                                        <p> Download PDF</p>
                                    </button>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" style="width:100%" data-dismiss="modal" class="btn btn-lg btn-block btn-danger">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block"><i class="far fa-hand-paper"></i> Batalkan</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection