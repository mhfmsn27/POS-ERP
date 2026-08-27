@extends('layouts.register')

@section('style')
<link rel="stylesheet" media="screen, print" href="{{asset('assets/vendors/statistic/css/c3/c3.css')}}">
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
@endsection

@section('content')
<div class="row">
    <div class="col-12 p-1 m-1 ">
        <div class="card register-report-card">

            <div class="card-body">
                <div class="row mt-1">

                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-header header-modal">
                                <h5 class="card-title text-white" style="margin-top: -10px">Rangkuman Transaksi Shift Register</h5>
                            </div>
                            <div class="card-body mt-2">
                                <div id="summaryTransaction" style="width:100%; height:300px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <div class="card">
                            <div class="card-header header-modal">
                                <h5 class="card-title text-white" style="margin-top: -10px">Transaksi Berdasarkan Methode Bayar</h5>
                            </div>
                            <div class="card-body mt-2">
                                <div id="payment" style="width:100%; height:300px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-12 col-md-12 col-sm-12">
                        <div class="card">
                            <div class="card-header header-modal">
                                <h5 class="card-title text-white" style="margin-top: -10px">10 Produk Terpopular Hari ini</h5>
                            </div>
                            <div class="card-body mt-2">
                                <div id="popularproduct" style="width:100%; height:400px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <table class="table table-bordered">
                            <thead>
                                <tr style="background-color: #3c8dbc; border: 1px solid white" class="text-white">
                                    <th>#</th>
                                    <th>Nama Transaksi</th>
                                    <th>Value ( + / - ) </th>
                                    <th class="text-right">Total</th>
                                </tr>
                            </thead>
                            <tbody>

                                <tr>
                                    <td>1</td>
                                    <td> Open Shift Register / Cash in Hand </td>
                                    <td> ( + ) </td>
                                    <td>{{number_format($getShift->opening_shift)}}</td>
                                </tr>

                                <tr>
                                    <td>2</td>
                                    <td> Penjualan Cash </td>
                                    <td> ( + ) </td>
                                    <td>{{number_format($getShift->sell_cash_transaction)}}</td>
                                </tr>

                                <tr>
                                    <td>3</td>
                                    <td> Penjualan Via Bank / Debit </td>
                                    <td> ( + ) </td>
                                    <td>{{number_format($getShift->sell_bank_transaction)}}</td>
                                </tr>

                                <tr>
                                    <td>4</td>
                                    <td> Penjualan Dengan Payment Lainnya </td>
                                    <td> ( + ) </td>
                                    <td>{{number_format($getShift->sell_other_transaction)}}</td>
                                </tr>

                                <tr>
                                    <td>5</td>
                                    <td> Pengeluaran </td>
                                    <td> ( - ) </td>
                                    <td>{{number_format($getShift->expense_transaction)}}</td>
                                </tr>

                                <tr>
                                    <td>6</td>
                                    <td> Return Penjualan </td>
                                    <td> ( - ) </td>
                                    <td>{{number_format($getShift->return_transaction)}}</td>
                                </tr>
                                <tr style="background-color: #5cb85c; color:white">
                                    <td colspan="3">
                                        <b>Total Cash di Tangan</b>
                                    </td>
                                    <td>
                                        <b>
                                            {{number_format($getShift->cash_in_hand)}}
                                        </b>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('assets/vendors/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/datatables.js') }}"></script>

<script src="{{asset('assets/vendors/statistic/js/d3/d3.js')}}"></script>
<script src="{{asset('assets/vendors/statistic/js/c3/c3.js')}}"></script>
<script src="{{asset('assets/vendors/statistic/js/flot/flot.bundle.js')}}"></script>

<script src="{{ asset('assets/vendors/amcharts4/core.js') }}"></script>
<script src="{{ asset('assets/vendors/amcharts4/charts.js') }}"></script>
<script src="{{ asset('assets/vendors/amcharts4/animated.js') }}"></script>
<script src="{{ asset('assets/vendors/amcharts4/popular_product.js') }}"></script>
<script src="{{ asset('assets/vendors/amcharts4/summary_report.js') }}"></script>
@endsection