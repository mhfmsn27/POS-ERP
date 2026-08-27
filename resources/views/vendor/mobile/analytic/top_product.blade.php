@extends('layouts.m')
@section('content')

@section('styles')
<link rel="stylesheet" href="{{ asset('assets/vendors/apexcharts/apexcharts.css') }}">
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
            <div class="element-heading mt-3">
                  <h6>Graphic Produk Ter-Laris</h6>
            </div>
      </div>
      <div class="container">
            <div class="card shadow-sm">
                  <div class="card-body pb-2">
                        <div class="chart-wrapper">
                              <div id="topProduk"></div>
                        </div>
                  </div>
            </div>
      </div> 
      <div class="container">
            <div class="element-heading mt-3">
                  <h6>Daftar 30 Produk Ter-Laris</h6>
            </div>
      </div>
      <div class="container">
            <div class="card invoice-card shadow">
                  <div class="card-body"> 
                        <div class="invoice-table">
                              <div class="table-responsive">
                                    <table class="table table-bordered caption-top">
                                          <caption>Daftar Transaksi</caption>
                                          <thead class="table-light">
                                                <tr>
                                                      <th>No</th>
                                                      <th>Produk</th>
                                                      <th>Harga</th>
                                                      <th>Terjual</th> 
                                                </tr>
                                          </thead>
                                          <tbody>
                                                @php 
                                                $no = 1;
                                                @endphp 
                                               @foreach($product as $d)
                                                <tr>
                                                      <td>{{$no++}}</td>
                                                      <td>{{$d['name']}}</td>
                                                      <td> {{ number_format($d['unit_price'])}} </td>
                                                      <td> {{ number_format($d['selling']) }} </td> 
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

<x-mobile.footer-component></x-mobile.footer-component>

@endsection

@section('scripts')
<script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendors/apexcharts/top_produk.js') }}"></script>
@endsection