@extends('layouts.app')
@section('content')
<div class="auth-page-wrapper">
    <div class="wrap-login100" style="max-width: 520px !important;">
        <!-- Brand Header -->
        <div class="auth-brand-header text-center mb-3">
            <a href="{{ url('/') }}" class="d-inline-block text-decoration-none">
                <img src="{{ asset('images/logo.png') }}" class="auth-brand-logo" alt="POSHUB Enterprise">
            </a>
            <div>
                <span class="auth-brand-badge">Verifikasi Email Akun</span>
            </div>
        </div>

        <!-- Form Title -->
        <div class="text-center mb-3">
            <h1 class="auth-form-title text-center mb-1">Verifikasi Alamat Email Anda</h1>
            <p class="auth-form-subtitle text-center mb-0">Sebelum melanjutkan ke sistem kasir &amp; akuntansi, kami telah mengirimkan tautan aktivasi ke:</p>
        </div>

        <!-- User Email Highlight Box -->
        <div class="text-center p-3 mb-3 rounded" style="background-color: var(--poshub-primary-light); border: 1px solid var(--poshub-primary-border);">
            <i class="fe fe-mail text-primary me-1 fs-15"></i>
            <span class="fw-bold text-dark" style="font-size: 14.5px;">{{ auth()->user()->email ?? 'Alamat Email Anda' }}</span>
        </div>

        @if (session('resent'))
        <div class="alert alert-success d-flex align-items-center mb-3" role="alert" style="background-color: var(--poshub-success-light); border: 1px solid var(--poshub-success-border); color: var(--poshub-success); font-size: 13.5px; border-radius: 8px;">
            <i class="fe fe-check-circle me-2 fs-16"></i>
            <div>Tautan verifikasi baru telah berhasil dikirimkan ke kotak masuk email Anda.</div>
        </div>
        @endif

        <div class="p-3 mb-4 rounded" style="background-color: #f8fafc; border: 1px solid var(--poshub-border);">
            <p class="mb-0 text-muted" style="font-size: 12.5px; line-height: 1.6;">
                <i class="fe fe-info text-info me-1"></i> Periksa folder <strong>Inbox</strong> atau folder <strong>Spam/Junk</strong> email Anda. Jika belum menerima email dalam beberapa menit, klik tombol di bawah untuk meminta pengiriman ulang.
            </p>
        </div>

        <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
            @csrf
            <div class="container-login100-form-btn">
                <button type="submit" class="btn-auth-primary">
                    <i class="fe fe-send me-2"></i> Kirim Ulang Tautan Verifikasi
                </button>
            </div>
        </form>

        <div class="d-flex justify-content-between align-items-center auth-footer-text text-start mt-4 pt-3 border-top">
            <a href="{{ url('/') }}" class="auth-link text-muted small">
                <i class="fe fe-home me-1"></i> Beranda
            </a>
            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="auth-link text-danger small fw-semibold">
                <i class="fe fe-log-out me-1"></i> Keluar / Ganti Akun
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                @csrf
            </form>
        </div>
    </div>
</div>
@endsection