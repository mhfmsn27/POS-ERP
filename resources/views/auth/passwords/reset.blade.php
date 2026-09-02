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
                <span class="auth-brand-badge">Atur Ulang Kata Sandi</span>
            </div>
        </div>

        <!-- Form Title -->
        <h1 class="auth-form-title">Buat Kata Sandi Baru</h1>
        <p class="auth-form-subtitle">Silakan buat kata sandi baru yang kuat untuk mengamankan akses akun Anda.</p>

        <!-- Validation Component -->
        <x-admin.validation-component></x-admin.validation-component>

        <!-- Reset Form -->
        <form class="validate-form" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            
            <!-- Email Input -->
            <div class="wrap-input100 validate-input" data-validate="Alamat email wajib diisi dengan benar">
                <label for="email" class="auth-label">Alamat Email</label>
                <div class="position-relative">
                    <input class="input100" type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required autofocus placeholder="contoh: user@poshub.id">
                    <span class="symbol-input100">
                        <i class="fe fe-mail" aria-hidden="true"></i>
                    </span>
                </div>
            </div>

            <!-- New Password -->
            <div class="wrap-input100 validate-input" data-validate="Kata sandi baru wajib diisi">
                <label for="password" class="auth-label">Kata Sandi Baru</label>
                <div class="position-relative">
                    <input class="input100" type="password" id="password" required name="password" placeholder="Minimal 8 karakter">
                    <span class="symbol-input100">
                        <i class="fe fe-lock" aria-hidden="true"></i>
                    </span>
                </div>
            </div>

            <!-- Confirm New Password -->
            <div class="wrap-input100 validate-input" data-validate="Konfirmasi kata sandi wajib diisi">
                <label for="password_confirmation" class="auth-label">Konfirmasi Kata Sandi Baru</label>
                <div class="position-relative">
                    <input class="input100" type="password" id="password_confirmation" required name="password_confirmation" placeholder="Ulangi kata sandi baru">
                    <span class="symbol-input100">
                        <i class="fe fe-shield" aria-hidden="true"></i>
                    </span>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div class="container-login100-form-btn mt-2">
                <button type="submit" class="btn-auth-primary">
                    <i class="fe fe-check-circle me-2" style="font-size: 16px;"></i> Simpan Kata Sandi Baru
                </button>
            </div> 

            <div class="auth-footer-text">
                <p class="mb-0">
                    <a href="{{ route('login') }}" class="auth-link">
                        <i class="fe fe-arrow-left me-1"></i> Kembali ke Halaman Masuk
                    </a>
                </p>
            </div>
        </form>
    </div>
</div>
@endsection