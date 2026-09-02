@extends('layouts.app')
@section('content')
<div class="auth-page-wrapper">
    <div class="auth-split-container">
        <!-- Left Side: Form Panel -->
        <div class="auth-form-side">
            <!-- Brand Header -->
            <div class="auth-brand-header text-start mb-4">
                <a href="{{ url('/') }}" class="d-inline-block">
                    <img src="{{ asset('images/logo.png') }}" class="auth-brand-logo" alt="POSHUB Enterprise">
                </a>
                <div>
                    <span class="auth-brand-badge">Sistem Kasir &amp; Akuntansi Enterprise</span>
                </div>
            </div>

            <!-- Form Title -->
            <h1 class="auth-form-title text-start mb-1">Masuk ke Akun Anda</h1>
            <p class="auth-form-subtitle text-start mb-4">Silakan masukkan email dan kata sandi resmi untuk mengakses panel operasional.</p>

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
                <div class="auth-footer-text text-start">
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

        <!-- Right Side: Feature Showcase Panel -->
        <div class="auth-showcase-side">
            <div>
                <div>
                    <span class="auth-showcase-badge">
                        <i class="fe fe-shield text-info"></i> POSHUB ENTERPRISE ECOSYSTEM
                    </span>
                </div>
                <h2 class="auth-showcase-title">Solusi Terpadu Kasir (POS), Akuntansi &amp; Omnichannel</h2>
                <p class="auth-showcase-subtitle">Platform all-in-one untuk efisiensi transaksi kasir, otomasi pembukuan finansial, sinkronisasi stok multi-gudang, dan e-commerce.</p>

                <!-- Feature Grid -->
                <div class="auth-feature-grid">
                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">
                            <i class="fe fe-shopping-bag"></i>
                        </div>
                        <div class="auth-feature-heading">Point of Sale Kasir</div>
                        <p class="auth-feature-desc">Kasir cepat, kitchen &amp; customer display, receipt thermal dan WhatsApp.</p>
                    </div>

                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">
                            <i class="fe fe-file-text"></i>
                        </div>
                        <div class="auth-feature-heading">Akuntansi Otomatis</div>
                        <p class="auth-feature-desc">Jurnal otomatis, neraca, laba rugi real-time, dan rekonsiliasi kas/bank.</p>
                    </div>

                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">
                            <i class="fe fe-box"></i>
                        </div>
                        <div class="auth-feature-heading">Multi-Gudang &amp; Cabang</div>
                        <p class="auth-feature-desc">Pantau stok real-time, transfer inventori, dan opname terpusat.</p>
                    </div>

                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">
                            <i class="fe fe-globe"></i>
                        </div>
                        <div class="auth-feature-heading">Omnichannel Storefront</div>
                        <p class="auth-feature-desc">Toko online e-commerce langsung tersinkron ke inventori POS &amp; payment.</p>
                    </div>
                </div>
            </div>

            <!-- Showcase Footer -->
            <div class="auth-showcase-footer">
                <span class="auth-security-pill">
                    <i class="fe fe-lock"></i> Bank-Grade 256-Bit Data Security
                </span>
                <span class="text-white-50">High-Availability Cloud Architecture</span>
            </div>
        </div>
    </div>
</div>
@endsection