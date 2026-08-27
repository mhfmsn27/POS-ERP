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

<div class="login-wrapper d-flex align-items-center justify-content-center">
      <div class="custom-container">
        <div class="text-center px-4"><img class="login-intro-img" src="{{asset('assets/images/close_store.png')}}" alt=""></div>
        <!-- Register Form -->
        <div class="register-form mt-4">
          <h6 class="mb-3 text-center">Sepertinya Anda belum membuka Toko hari ini ? Aktifkan Fiture Open Register dan buka segera buka toko untuk melihat Analisis harian </h6>
         
           
        </div>
        
      </div>
    </div>

<x-mobile.footer-component></x-mobile.footer-component>

@endsection
 