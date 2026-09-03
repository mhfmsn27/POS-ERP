@extends('layouts.app')
@section('content')
<div class="auth-page-wrapper">
    <!-- Top Navigation Link -->
    <div class="auth-nav-top" style="max-width: 480px;">
        <a href="{{ url()->previous() ?: route('login') }}" class="auth-link-back">
            <i class="fe fe-arrow-left"></i> Kembali ke Halaman Sebelumnya
        </a>
    </div>

    <div class="wrap-login100">
        <!-- Brand Header -->
        <div class="auth-brand-header text-center mb-3">
            <a href="{{ url('/') }}" class="d-inline-block text-decoration-none">
                <img src="{{ asset('images/logo.png') }}" class="auth-brand-logo" alt="POSHUB Enterprise">
            </a>
            <div>
                <span class="auth-brand-badge">Konfirmasi Keamanan Sesi</span>
            </div>
        </div>

        <!-- Form Title -->
        <div class="text-center mb-3">
            <h1 class="auth-form-title text-center mb-1">Konfirmasi Kata Sandi</h1>
            <p class="auth-form-subtitle text-center mb-0">Harap masukkan kata sandi Anda kembali sebelum mengakses area pengaturan dan data sensitif ini.</p>
        </div>

        <!-- Form -->
        <form class="validate-form" method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="wrap-input100 validate-input" data-validate="Kata sandi wajib diisi">
                <label for="password" class="auth-label">Kata Sandi Akun <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <input id="password" type="password" class="input100 auth-input-with-icon auth-input-with-toggle @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" autofocus placeholder="Masukkan kata sandi akun Anda">
                    <span class="symbol-input100">
                        <i class="fe fe-lock" aria-hidden="true"></i>
                    </span>
                    <button type="button" class="btn-toggle-password" data-target="password" title="Tampilkan kata sandi" tabindex="-1">
                        <i class="fe fe-eye"></i>
                    </button>
                </div>
                @error('password')
                    <span class="text-danger d-block mt-1" style="font-size: 12.5px;">
                        <i class="fe fe-alert-circle me-1"></i><strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="container-login100-form-btn mt-2">
                <button type="submit" class="btn-auth-primary">
                    <i class="fe fe-check-circle me-2"></i> Konfirmasi &amp; Lanjutkan
                </button>
            </div>

            <div class="d-flex justify-content-between align-items-center auth-footer-text text-start mt-4 pt-3 border-top">
                <a href="{{ url()->previous() ?: route('login') }}" class="auth-link text-muted small">
                    <i class="fe fe-x me-1"></i> Batal
                </a>
                @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="auth-link small fw-bold">
                    Lupa Sandi?
                </a>
                @endif
            </div>
        </form>
    </div>
</div>
@endsection
