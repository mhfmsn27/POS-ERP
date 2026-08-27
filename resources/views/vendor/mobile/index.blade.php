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

      @can("Penjualan 30")
      <div class="container">
            <div class="element-heading mt-3">
                  <h6>Penjualan 7 Hari Terakhir</h6>
            </div>
      </div>
      <div class="container">
            <div class="card shadow-sm">
                  <div class="card-body pb-2">
                        <div class="chart-wrapper">
                              <div id="penjualan2Week"></div>
                        </div>
                  </div>
            </div>
      </div>
      @endcan

      <div class="pt-3"></div>
      <div class="container direction-rtl">
            <div class="card mb-3">
                  <div class="card-body">
                        <div class="row g-3">
                              @can("POS")
                              <a href="{{ route('pos.index') }}" class="col-4">
                                    <div class="feature-card mx-auto text-center">
                                          <div class="card mx-auto bg-gray"><i style="font-size: 20px;" class="fas fa-desktop"></i></div>
                                          <p class="mb-0">{{__('general.pos')}}</p>
                                    </div>
                              </a>
                              @endcan
                              @can("Daftar Penjualan")
                              <a href="{{ route('m.sale_detail') }}" class="col-4">
                                    <div class="feature-card mx-auto text-center">
                                          <div class="card mx-auto bg-gray"><i style="font-size: 20px;" class="fas fa-shopping-cart"></i></div>
                                          <p class="mb-0">{{__('general.sell')}}</p>
                                    </div>
                              </a>
                              @endcan
                              @can("Daftar Purchase")
                              <a href="{{ route('m.purchase_detail') }}" class="col-4">
                                    <div class="feature-card mx-auto text-center">
                                          <div class="card mx-auto bg-gray"><i style="font-size: 20px;" class="fas fa-cart-plus"></i></div>
                                          <p class="mb-0">{{__('sidebar.purchase')}}</p>
                                    </div>
                              </a>
                              @endcan
                              @can("Laporan Hutang")
                              <a href="{{ route('m.due') }}" class="col-4">
                                    <div class="feature-card mx-auto text-center">
                                          <div class="card mx-auto bg-gray"><i style="font-size: 20px;" class="fas fa-list"></i></div>
                                          <p class="mb-0">{{__('sell.due')}}</p>
                                    </div>
                              </a>
                              @endcan
                              @can("Daftar Pengeluaran")
                              <a href="{{ route('m.expense') }}" class="col-4">
                                    <div class="feature-card mx-auto text-center">
                                          <div class="card mx-auto bg-gray"><i style="font-size: 20px;" class="fas fa-money-bill"></i></div>
                                          <p class="mb-0">Pengeluaran</p>
                                    </div>
                              </a>
                              @endcan
                              @can("Peringatan Stock")
                              <a href="{{ route('m.stock') }}" class="col-4">
                                    <div class="feature-card mx-auto text-center">
                                          <div class="card mx-auto bg-gray"><i style="font-size: 20px;" class="fas fa-cube"></i></div>
                                          <p class="mb-0">Stok</p>
                                    </div>
</a>
                              @endcan
                        </div>
                  </div>
            </div>
      </div>
      <div class="pt-2"></div>
      <div class="container direction-rtl">
            <div class="row">
                  @can("Daftar Penjualan")
                  <div class="col-12">
                        <a href="#" class="card">
                              <div class="card-body">
                                    <div class="row align-items-center">
                                          <div class="col text-center">
                                                <span class="h4">{{ my_currency($data['total_sell']) }}</span>
                                                <p>{{__('sell.total_sell')}}</p>
                                          </div>
                                    </div>
                              </div>
                        </a>
                  </div>
                  @endcan

                  @can("Daftar Laporan Pengeluaran")
                  <div class="col-12 mt-3">
                        <a href="#" class="card">
                              <div class="card-body">
                                    <div class="row align-items-center">
                                          <div class="col text-center">
                                                <span class="h4">{{ my_currency($data['total_expense']) }}</span>
                                                <p>{{__('expense.total_expense')}}</p>
                                          </div>
                                    </div>
                              </div>
                        </a>
                  </div>
                  @endcan

                  @can('Laporan Purchase')
                  <div class="col-12 mt-3">
                        <a href="#" class="card">
                              <div class="card-body">
                                    <div class="row align-items-center">
                                          <div class="col text-center">
                                                <span class="h4">{{ my_currency($data['total_purchase']) }}</span>
                                                <p>{{__('purchase.purchase_total')}}</p>
                                          </div>
                                    </div>
                              </div>
                        </a>
                  </div>
                  @endcan

                  @can('Laporan Hutang')
                  <div class="col-12 mt-3">
                        <a href="#" class="card">
                              <div class="card-body">
                                    <div class="row align-items-center">
                                          <div class="col text-center">
                                                <span class="h4">{{ my_currency($data['total_due']) }}</span>
                                                <p>{{__('sell.total_due')}}</p>
                                          </div>
                                    </div>
                              </div>
                        </a>
                  </div>
                  @endcan

            </div>
      </div>

</div>

<x-mobile.footer-component></x-mobile.footer-component>

@endsection

@section('scripts')
<script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendors/apexcharts/mobile_dashboard.js') }}"></script>
@endsection