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
                <span class="auth-brand-badge">Verifikasi Email Akun</span>
            </div>
        </div>

        <!-- Form Title -->
        <h1 class="auth-form-title">Verifikasi Alamat Email</h1>
        <p class="auth-form-subtitle">Sebelum melanjutkan, periksa kotak masuk atau folder spam email Anda untuk tautan verifikasi resmi.</p>

        @if (session('resent'))
        <div class="alert alert-success" role="alert" style="background-color: var(--poshub-success-light); border: 1px solid var(--poshub-success-border); color: var(--poshub-success); font-size: 13.5px; border-radius: 8px; margin-bottom: 20px;">
            <i class="fe fe-check-circle me-1"></i> Tautan verifikasi baru telah berhasil dikirim ke alamat email Anda.
        </div>
        @endif

        <div style="background-color: #f8fafc; border: 1px solid var(--poshub-border); border-radius: 8px; padding: 18px; margin-bottom: 24px;">
            <p style="font-size: 13.5px; color: var(--poshub-slate); margin-bottom: 0; line-height: 1.6;">
                Jika Anda belum menerima email verifikasi, Anda dapat meminta sistem untuk mengirim ulang email aktivasi baru.
            </p>
        </div>

        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <div class="container-login100-form-btn">
                <button type="submit" class="btn-auth-primary">
                    <i class="fe fe-mail me-2"></i> Kirim Ulang Email Verifikasi
                </button>
            </div>
        </form>

        <div class="auth-footer-text">
            <p class="mb-0">
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="auth-link">
                    <i class="fe fe-log-out me-1"></i> Keluar / Ganti Akun
                </a>
            </p>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</div>
@endsection