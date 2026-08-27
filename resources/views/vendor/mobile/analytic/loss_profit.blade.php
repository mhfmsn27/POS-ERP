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
                  <h6>Pengeluaran x Keuntungan Bersih</h6>
            </div>
      </div>
      <div class="container">
            <div class="card shadow-sm">
                  <div class="card-body pb-2">
                        <div class="chart-wrapper">
                              <div id="profitLoss"></div>
                        </div>
                  </div>
            </div>
      </div> 

      <div class="pt-3"></div>
      <div class="container direction-rtl">
            <div class="row"> 
                  <div class="col-12">
                        <a href="#" class="card">
                              <div class="card-body">
                                    <div class="row align-items-center">
                                          <div class="col text-center">
                                                <span class="h4">{{ my_currency($data['total_profit']) }}</span>
                                                <p>Keuntungan Bersih</p>
                                          </div>
                                    </div>
                              </div>
                        </a>
                  </div> 
 
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

            </div>
      </div>

</div>

<x-mobile.footer-component></x-mobile.footer-component>

@endsection

@section('scripts')
<script src="{{ asset('assets/vendors/apexcharts/apexcharts.min.js') }}"></script>
<script src="{{ asset('assets/vendors/apexcharts/profit.js') }}"></script>
@endsection