@extends('layouts.m')
@section('content')

    <!-- Login Wrapper Area -->
    <div class="login-wrapper d-flex align-items-center justify-content-center">
      <div class="custom-container">
        <div class="text-center px-4"><img class="login-intro-img" src="{{asset('assets/images/register_mobile.png')}}" alt=""></div>
        <!-- Register Form -->
        <div class="register-form mt-4">
          <h6 class="mb-3 text-center">{{ __('auth.welcome') }}</h6>
          <x-admin.validation-component></x-admin.validation-component>
          <form action="{{route('login')}}" method="POST">
                @csrf
            <div class="form-group">
              <input class="form-control" type="email" name="email" placeholder="Masukkan Email Anda">
            </div>
            <div class="form-group position-relative">
              <input class="form-control" id="psw-input" type="password" name="password" placeholder="Masukkan Password Anda">
              <div class="position-absolute" id="password-visibility"><i class="bi bi-eye"></i><i class="bi bi-eye-slash"></i></div>
            </div>
            <button class="btn btn-primary w-100" type="submit">Login</button>
          </form>
        </div>
        <!-- Login Meta -->
        <div class="login-meta-data text-center"><a class="stretched-link forgot-password d-block mt-3 mb-1" href="{{ route('password.request') }}">Lupa Password?</a> 
        </div>
      </div>
    </div>
@endsection