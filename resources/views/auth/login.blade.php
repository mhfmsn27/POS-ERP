@extends('layouts.app')
@section('content')
<div class="">
    <!-- CONTAINER OPEN -->
    <div class="col col-login mx-auto mt-7">
        <div class="text-center">
            <a href="{{url('index')}}">
                <img src="{{asset('assets/images/logo-signin.png')}}" class="header-brand-img" alt="logo">
            </a>
        </div>
    </div>
    <div class="container-login100">
        <div class="wrap-login100 p-6">
            <form class="login100-form validate-form" method="POST" action="{{ route('login') }}">
                @csrf
                <span class="login100-form-title">
                    Login
                </span>
                <x-admin.validation-component></x-admin.validation-component>
                <div class="wrap-input100 validate-input mb-4" data-validate="Valid email is required: ex@abc.xyz">
                    <input class="input100" type="text" name="email" required placeholder="Email">
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <i class="fe fe-mail" aria-hidden="true"></i>
                    </span>
                </div>
                <div class="wrap-input100 validate-input" data-validate="Password is required">
                    <input class="input100" type="password" required name="password" placeholder="Password">
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <i class="fe fe-lock" aria-hidden="true"></i>
                    </span>
                </div>
                
                <div class="container-login100-form-btn">
                    <button type="submit" class="login100-form-btn btn-primary">
                        Login
                    </button>
                </div> 
            </form>
        </div>
    </div>
    <!-- CONTAINER CLOSED -->
</div>
@endsection