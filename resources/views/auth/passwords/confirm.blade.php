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
                <span class="auth-brand-badge">Konfirmasi Keamanan</span>
            </div>
        </div>

        <!-- Form Title -->
        <h1 class="auth-form-title">Konfirmasi Kata Sandi</h1>
        <p class="auth-form-subtitle">Harap konfirmasi kata sandi Anda sebelum melanjutkan ke area sensitif ini.</p>

        <!-- Form -->
        <form class="validate-form" method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="wrap-input100 validate-input" data-validate="Kata sandi wajib diisi">
                <label for="password" class="auth-label">Kata Sandi</label>
                <div class="position-relative">
                    <input id="password" type="password" class="input100 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi Anda">
                    <span class="symbol-input100">
                        <i class="fe fe-lock" aria-hidden="true"></i>
                    </span>
                </div>
                @error('password')
                    <span class="text-danger d-block mt-1" style="font-size: 12.5px;">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="container-login100-form-btn mt-2">
                <button type="submit" class="btn-auth-primary">
                    <i class="fe fe-check-circle me-2"></i> Konfirmasi &amp; Lanjutkan
                </button>
            </div>

            @if (Route::has('password.request'))
            <div class="auth-footer-text">
                <p class="mb-0">
                    <a href="{{ route('password.request') }}" class="auth-link">
                        Lupa Kata Sandi Anda?
                    </a>
                </p>
            </div>
            @endif
        </form>
    </div>
</div>
@endsection
