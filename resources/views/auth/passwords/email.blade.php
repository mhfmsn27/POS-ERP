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
                <span class="auth-brand-badge">Pemulihan Kata Sandi</span>
            </div>
        </div>

        <!-- Form Title -->
        <h1 class="auth-form-title">Lupa Kata Sandi?</h1>
        <p class="auth-form-subtitle">Masukkan alamat email akun Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.</p>

        <!-- Status & Validation -->
        @if (session('status'))
            <div class="alert alert-success" role="alert" style="background-color: var(--poshub-success-light); border: 1px solid var(--poshub-success-border); color: var(--poshub-success); font-size: 13.5px; border-radius: 8px;">
                <i class="fe fe-check-circle me-1"></i> {{ session('status') }}
            </div>
        @endif
        <x-admin.validation-component></x-admin.validation-component>

        <!-- Password Request Form -->
        <form class="validate-form" method="POST" action="{{ route('password.email') }}">
            @csrf
            
            <div class="wrap-input100 validate-input" data-validate="Alamat email wajib diisi dengan benar">
                <label for="email" class="auth-label">Alamat Email Terdaftar</label>
                <div class="position-relative">
                    <input class="input100" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="contoh: user@poshub.id">
                    <span class="symbol-input100">
                        <i class="fe fe-mail" aria-hidden="true"></i>
                    </span>
                </div>
            </div> 

            <div class="container-login100-form-btn mt-2">
                <button type="submit" class="btn-auth-primary">
                    <i class="fe fe-send me-2" style="font-size: 15px;"></i> Kirim Tautan Reset
                </button>
            </div>

            <div class="auth-footer-text">
                <p class="mb-0">
                    Sudah ingat kata sandi Anda? 
                    <a href="{{ route('login') }}" class="auth-link">Kembali ke Halaman Masuk</a>
                </p>
                <small class="text-muted d-block mt-2" style="font-size: 11px;">
                    &copy; {{ date('Y') }} POSHUB ENTERPRISE. Hak Cipta Dilindungi.
                </small>
            </div>
        </form>
    </div> 
</div>
@endsection