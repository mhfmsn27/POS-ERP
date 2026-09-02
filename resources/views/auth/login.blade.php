@extends('layouts.app')
@section('content')
<div class="auth-page-wrapper">
    <div class="wrap-login100">
        <!-- Brand Header -->
        <div class="auth-brand-header">
            <a href="{{ url('/') }}" class="d-inline-block">
                <img src="{{ asset('images/logo.png') }}" class="auth-brand-logo" alt="POSHUB Enterprise">
            </a>
            <div>
                <span class="auth-brand-badge">Sistem Kasir &amp; Akuntansi Enterprise</span>
            </div>
        </div>

        <!-- Form Title -->
        <h1 class="auth-form-title">Masuk ke Akun Anda</h1>
        <p class="auth-form-subtitle">Silakan masukkan email dan kata sandi resmi untuk mengakses panel operasional.</p>

        <!-- Validation Errors -->
        <x-admin.validation-component></x-admin.validation-component>

        <!-- Login Form -->
        <form class="validate-form" method="POST" action="{{ route('login') }}">
            @csrf
            
            <!-- Email Input -->
            <div class="wrap-input100 validate-input" data-validate="Alamat email wajib diisi dengan benar">
                <label for="email" class="auth-label">Alamat Email</label>
                <div class="position-relative">
                    <input class="input100" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="contoh: admin@poshub.id">
                    <span class="symbol-input100">
                        <i class="fe fe-mail" aria-hidden="true"></i>
                    </span>
                </div>
            </div>

            <!-- Password Input -->
            <div class="wrap-input100 validate-input" data-validate="Kata sandi wajib diisi">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label for="password" class="auth-label mb-0">Kata Sandi</label>
                    @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" class="auth-link" style="font-size: 12.5px;">
                        Lupa Sandi?
                    </a>
                    @endif
                </div>
                <div class="position-relative">
                    <input class="input100" type="password" id="password" required name="password" placeholder="Masukkan kata sandi">
                    <span class="symbol-input100">
                        <i class="fe fe-lock" aria-hidden="true"></i>
                    </span>
                </div>
            </div>
            
            <!-- Submit Button (Solid Royal Blue) -->
            <div class="container-login100-form-btn mt-2">
                <button type="submit" class="btn-auth-primary">
                    <i class="fe fe-log-in me-2" style="font-size: 16px;"></i> Masuk ke Sistem
                </button>
            </div>

            <!-- Footer Text / Links -->
            <div class="auth-footer-text">
                <p class="mb-0">
                    Belum memiliki akun operasional? 
                    <a href="{{ route('register') }}" class="auth-link">Hubungi Administrator</a>
                </p>
                <small class="text-muted d-block mt-2" style="font-size: 11px;">
                    &copy; {{ date('Y') }} POSHUB ENTERPRISE. Hak Cipta Dilindungi.
                </small>
            </div>
        </form>
    </div>
</div>
@endsection