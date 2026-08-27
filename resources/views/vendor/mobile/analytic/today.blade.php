@extends('layouts.m')
@section('content')

@section('style')
<link rel="stylesheet" media="screen, print" href="{{asset('assets/vendors/statistic/css/c3/c3.css')}}">
@endsection

<div class="header-area" id="headerArea">
      <div class="container">
            <div class="header-content header-style-five position-relative d-flex align-items-center justify-content-between">
                  <div class="logo-wrapper"><a href="{{route('m.index')}}"><img src="{{asset('uploads/logo2.png')}}" alt=""></a></div>
                  <div class="page-heading"> </div>
                  <div>
                        <h6 class="mb-0">{{$page}}</h6>
                  </div>
            </div>
      </div>
</div>


<div class="page-content-wrapper py-3">
      <x-mobile.alert-component></x-mobile.alert-component>

      <div class="container">
            <div class="card invoice-card shadow">
                  <div class="card-body">
                        <div class="invoice-info text-end mb-4">
                              <h6 class="mb-1 fz-14">Tanggal {{substr($getShift->created_at,0,10) }}</h6>
                              <h6 class="mb-1 fz-14">Jam Buka {{substr($getShift->created_at,11,5) }}</h6>
                        </div>
                        <div class="invoice-table">
                              <div class="table-responsive">
                                    <table class="table table-bordered caption-top">
                                          <caption>Ringkasan</caption>
                                          <thead class="table-light">
                                                <tr>
                                                      <th>Nama Transaksi</th>
                                                      <th>(+/-) </th>
                                                      <th class="text-right">Total</th>
                                                </tr>
                                          </thead>
                                          <tbody>
                                                <tr>
                                                      <td> Open Shift Register / Cash in Hand </td>
                                                      <td> ( + ) </td>
                                                      <td>{{number_format($getShift->opening_shift)}}</td>
                                                </tr>

                                                <tr>
                                                      <td> Penjualan Cash </td>
                                                      <td> ( + ) </td>
                                                      <td>{{number_format($getShift->sell_cash_transaction)}}</td>
                                                </tr>

                                                <tr>
                                                      <td> Penjualan Via Bank / Debit </td>
                                                      <td> ( + ) </td>
                                                      <td>{{number_format($getShift->sell_bank_transaction)}}</td>
                                                </tr>

                                                <tr>
                                                      <td> Penjualan Dengan Payment Lainnya </td>
                                                      <td> ( + ) </td>
                                                      <td>{{number_format($getShift->sell_other_transaction)}}</td>
                                                </tr>

                                                <tr>
                                                      <td> Pengeluaran </td>
                                                      <td> ( - ) </td>
                                                      <td>{{number_format($getShift->expense_transaction)}}</td>
                                                </tr>

                                                <tr>
                                                      <td> Return Penjualan </td>
                                                      <td> ( - ) </td>
                                                      <td>{{number_format($getShift->return_transaction)}}</td>
                                                </tr>
                                                <tr>

                                          </tbody>
                                          <tfoot class="table-light">
                                                <tr>
                                                      <td class="text-end" colspan="2">Total Cash Ditangan :</td>
                                                      <td class="text-end">{{number_format($getShift->cash_in_hand)}}</td>
                                                </tr>
                                          </tfoot>
                                    </table>
                              </div>
                        </div>
                  </div>
            </div>
      </div>

      <div class="container">
            <div class="element-heading mt-3">
                  <h6>Rangkuman Transaksi Hari ini</h6>
            </div>
      </div>
      <div class="container">
            <div class="card">
                  <div class="card-body">
                        <div id="summaryTransaction"></div>
                  </div>
            </div>
      </div>

      <div class="container">
            <div class="element-heading mt-3">
                  <h6>Transaksi Berdasarkan Metode Bayar</h6>
            </div>
      </div>
      <div class="container">
            <div class="card">
                  <div class="card-body">
                        <div id="payment"></div>
                  </div>
            </div>
      </div>

      <div class="container mt-3">
            <div class="card">
                  <div class="card-body">
                        <div class="row">
                              <div class="col-12 text-center">
                                    <a class="btn btn-lg btn-block btn-danger" href="{{route('m.today_close')}}">Tutup Toko Hari ini</a>
                              </div>
                        </div>
                  </div>
            </div>
      </div>



</div>

<x-mobile.footer-component></x-mobile.footer-component>

@endsection

@section('scripts')

<script src="{{asset('assets/vendors/statistic/js/d3/d3.js')}}"></script>
<script src="{{asset('assets/vendors/statistic/js/c3/c3.js')}}"></script>
<script src="{{asset('assets/vendors/statistic/js/flot/flot.bundle.js')}}"></script>

<script src="{{ asset('assets/vendors/amcharts4/core.js') }}"></script>
<script src="{{ asset('assets/vendors/amcharts4/charts.js') }}"></script>
<script src="{{ asset('assets/vendors/amcharts4/animated.js') }}"></script>
<script src="{{ asset('assets/vendors/amcharts4/summary_report_mobile.js') }}"></script>
@endsection