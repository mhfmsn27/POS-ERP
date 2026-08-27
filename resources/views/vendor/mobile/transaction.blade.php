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
                        <p class="ps-2">Penjualan & Return</p>
                        @can("Daftar Penjualan")
                        <a class="poshub-page-item" href="{{route('m.sale')}}">
                              <div class="icon-wrapper"><i class="fas fa-cart-plus"></i></div>Penjualan By Transaksi<i class="bi bi-chevron-right"></i>
                        </a>
                        @endcan
                        @can("Daftar Penjualan")
                        <a class="poshub-page-item" href="{{route('m.sale_detail')}}">
                              <div class="icon-wrapper"><i class="fas fa-shopping-cart"></i></div>Penjualan Detail<i class="bi bi-chevron-right"></i>
                        </a>
                        @endcan
                        @can("Return Penjualan")
                        <a class="poshub-page-item" href="{{route('m.return_sales')}}">
                              <div class="icon-wrapper"><i class="fas fa-cart-arrow-down"></i></div>Return Penjualan Detail<i class="bi bi-chevron-right"></i>
                        </a>
                        @endcan
                  </div>
            </div>

            <div class="card mt-2">
                  <div class="card-body p-3">
                        <p class="ps-2">PO / Pembelian & Return PO</p>
                        @can("Laporan Purchase")
                        <a class="poshub-page-item" href="{{route('m.purchase')}}">
                              <div class="icon-wrapper"><i class="fas fa-truck"></i></div>PO Berdasarkan Transaksi<i class="bi bi-chevron-right"></i>
                        </a>
                        @endcan
                        @can("Laporan Purchase")
                        <a class="poshub-page-item" href="{{route('m.purchase_detail')}}">
                              <div class="icon-wrapper"><i class="fas fa-truck-loading"></i></div>PO Detail<i class="bi bi-chevron-right"></i>
                        </a>
                        @endcan
                        @can("Laporan Return")
                        <a class="poshub-page-item" href="{{route('m.return_po')}}">
                              <div class="icon-wrapper"><i class="fas fa-truck-pickup"></i></div>Return PO Detail<i class="bi bi-chevron-right"></i>
                        </a>
                        @endcan
                  </div>
            </div>

            <div class="card mt-2">
                  <div class="card-body p-3">
                        <p class="ps-2">Lainnya</p>
                        @can("Peringatan Stock")
                        <a class="poshub-page-item" href="{{route('m.due')}}">
                              <div class="icon-wrapper"><i class="fas fa-money-check-alt"></i></div>Piutang Pelanggan<i class="bi bi-chevron-right"></i>
                        </a>
                        @endcan
                        @can("Laporan Stock Transfer")
                        <a class="poshub-page-item" href="{{route('m.transfer')}}">
                              <div class="icon-wrapper"><i class="fas fa-random"></i></div>Transfer Stok<i class="bi bi-chevron-right"></i>
                        </a>
                        @endcan
                        @can("Laporan Stock Adjustment")
                        <a class="poshub-page-item" href="{{route('m.adjustment')}}">
                              <div class="icon-wrapper"><i class="fas fa-cubes"></i></div>Adjustment Stok<i class="bi bi-chevron-right"></i>
                        </a>
                        @endcan
                  </div>
            </div>
      </div>

</div>
<br>
<x-mobile.footer-component></x-mobile.footer-component>

@endsection