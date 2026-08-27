@extends('layouts.app')
@section('content')
<div class="">
    <div class="col col-login mx-auto mt-7">
        <div class="text-center">
            <a href="{{url('index')}}">
                <img src="{{asset('assets/images/logo-signin.png')}}" class="header-brand-img" alt="logo">
            </a>
        </div>
    </div>
    <div class="container-login100">
        <div class="wrap-login100 p-6" style="width: 1000px;">
          
            <x-admin.validation-component></x-admin.validation-component>
            <form class="validate-form row" method="POST" action="{{ route('auth.register.create') }}">
               
                
                
            </form>
        </div>
    </div>
</div>
@endsection