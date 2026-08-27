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
            <form class="login100-form validate-form" method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <span class="login100-form-title">
                    Verifikasi Alamat Email
                </span>
                @if (session('resent'))
                <div class="alert alert-success" role="alert">
                    Tautan verifikasi baru telah dikirim ke alamat email Anda.
                </div>
                @endif
                <p>
                    Sebelum melanjutkan, silakan periksa email Anda untuk tautan verifikasi. Jika Anda tidak menerima email tersebut,
                </p>
                <form class="d-inline" method="POST" action="">
                    @csrf
                    <button type="submit" class="btn btn-link p-0 m-0 align-baseline">klik di sini untuk meminta yang lain</button>.
                </form>
            </form>
        </div>
    </div>
    <!-- CONTAINER CLOSED -->
</div>
@endsection