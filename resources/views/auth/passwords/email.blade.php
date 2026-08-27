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
            <form class="login100-form validate-form" method="POST" action="{{ route('password.email') }}">
                @csrf
                <span class="login100-form-title">
                    Minta Reset Password
                </span>
                <x-admin.validation-component></x-admin.validation-component>
                <div class="wrap-input100 validate-input mb-4" data-validate="Valid email is required: ex@abc.xyz">
                    <input class="input100" type="text" name="email" required placeholder="Masukkan Alamat Email">
                    <span class="focus-input100"></span>
                    <span class="symbol-input100">
                        <i class="zmdi zmdi-email" aria-hidden="true"></i>
                    </span>
                </div> 
                <div class="container-login100-form-btn">
                    <button type="submit" class="login100-form-btn btn-primary">
                        Minta Reset Password
                    </button>
                </div>
                <div class="text-center pt-3">
                    <p class="text-dark mb-0">Ingat Password ?<a href="{{route('login')}}" class="text-primary mx-1">Ke Halaman Login</a></p>
                </div>
            </form>
        </div>
    </div> 
</div>
@endsection