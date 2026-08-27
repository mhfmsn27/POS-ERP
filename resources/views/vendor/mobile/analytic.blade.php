@extends('layouts.m')
@section('content')

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
            <div class="card">
                  <div class="card-body p-3">
                        <p class="ps-2">Menu Analisis Penjualan</p>
                        @can("Profit Loss Report")
                        <a class="poshub-page-item" href="{{route('m.stock')}}">
                              <div class="icon-wrapper"><i class="fas fa-cube"></i></div>Daftar Stok Produk<i class="bi bi-chevron-right"></i>
                        </a>
                        @endcan
                        @can("Profit Loss Report")
                        <a class="poshub-page-item" href="{{route('m.profit')}}">
                              <div class="icon-wrapper"><i class="fas fa-balance-scale"></i></div>Profit Bersih & Pengeluaran<i class="bi bi-chevron-right"></i>
                        </a>
                        @endcan
                        @can("Pengeluaran dan Pendapatan")
                        <a class="poshub-page-item" href="{{route('m.chart_transaksi')}}">
                              <div class="icon-wrapper"><i class="fas fa-chart-pie"></i></div>Persentase Transaksi<i class="bi bi-chevron-right"></i>
                        </a>
                        @endcan
                        @can("Top Product")
                        <a class="poshub-page-item" href="{{route('m.trend_produk')}}">
                              <div class="icon-wrapper"><i class="fas fa-trophy"></i></div>Produk Ter-Laris<i class="bi bi-chevron-right"></i>
                        </a> 
                        @endcan
                        <a class="poshub-page-item" href="{{route('m.today')}}">
                              <div class="icon-wrapper"><i class="far fa-chart-bar"></i></div>Analisis Harian<i class="bi bi-chevron-right"></i>
                        </a> 
                  </div>
            </div>

            <div class="card mt-2">
                  <div class="card-body p-3">
                        <p class="ps-2">Pengeluaran</p>
                        @can("Daftar Pengeluaran")
                        <a class="poshub-page-item" href="{{route('m.expense')}}">
                              <div class="icon-wrapper"><i class="far fa-money-bill-alt"></i></div>Daftar Pengeluaran<i class="bi bi-chevron-right"></i>
                        </a>
                        @endcan
                        @can("Tambah Pengeluaran")
                        <a class="poshub-page-item" href="{{route('m.expense.create')}}">
                              <div class="icon-wrapper"><i class="fa fa-plus-circle"></i></div>Tambah Pengeluaran<i class="bi bi-chevron-right"></i>
                        </a> 
                        @endcan
                  </div>
            </div>

      </div>

</div>
<br>
<x-mobile.footer-component></x-mobile.footer-component>

@endsection