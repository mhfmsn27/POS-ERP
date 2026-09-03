@extends('layouts.app')
@section('content')
<div class="auth-page-wrapper">
    <!-- Top Navigation Link -->
    <div class="auth-nav-top">
        <a href="{{ route('login') }}" class="auth-link-back">
            <i class="fe fe-arrow-left"></i> Batal &amp; Kembali ke Login
        </a>
    </div>

    <div class="auth-split-container">
        <!-- Left Side: Form Panel -->
        <div class="auth-form-side">
            <!-- Brand Header -->
            <div class="auth-brand-header text-start mb-3">
                <a href="{{ url('/') }}" class="d-inline-block text-decoration-none">
                    <img src="{{ asset('images/logo.png') }}" class="auth-brand-logo" alt="POSHUB Enterprise">
                </a>
                <div>
                    <span class="auth-brand-badge">Atur Ulang Kata Sandi</span>
                </div>
            </div>

            <!-- Form Title -->
            <div class="mb-3">
                <h1 class="auth-form-title text-start mb-1">Buat Kata Sandi Baru</h1>
                <p class="auth-form-subtitle text-start mb-0">Silakan buat kombinasi kata sandi baru yang kuat untuk mengamankan akses akun operasional Anda.</p>
            </div>

            <!-- Validation Component -->
            <x-admin.validation-component></x-admin.validation-component>

            <!-- Reset Form -->
            <form class="validate-form" method="POST" action="{{ route('password.update') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                
                <!-- Email Input -->
                <div class="wrap-input100 validate-input" data-validate="Alamat email wajib diisi dengan benar">
                    <label for="email" class="auth-label">Alamat Email <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input class="input100 auth-input-with-icon" type="email" id="email" name="email" value="{{ $email ?? old('email') }}" required autofocus placeholder="contoh: user@poshub.id">
                        <span class="symbol-input100">
                            <i class="fe fe-mail" aria-hidden="true"></i>
                        </span>
                    </div>
                </div>

                <!-- New Password -->
                <div class="wrap-input100 validate-input" data-validate="Kata sandi baru wajib diisi">
                    <label for="password" class="auth-label">Kata Sandi Baru <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input class="input100 auth-input-with-icon auth-input-with-toggle" type="password" id="password" required name="password" placeholder="Minimal 8 karakter">
                        <span class="symbol-input100">
                            <i class="fe fe-lock" aria-hidden="true"></i>
                        </span>
                        <button type="button" class="btn-toggle-password" data-target="password" title="Tampilkan kata sandi" tabindex="-1">
                            <i class="fe fe-eye"></i>
                        </button>
                    </div>
                </div>

                <!-- Confirm New Password -->
                <div class="wrap-input100 validate-input" data-validate="Konfirmasi kata sandi wajib diisi">
                    <label for="password_confirmation" class="auth-label">Konfirmasi Kata Sandi Baru <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input class="input100 auth-input-with-icon auth-input-with-toggle" type="password" id="password_confirmation" required name="password_confirmation" placeholder="Ulangi kata sandi baru">
                        <span class="symbol-input100">
                            <i class="fe fe-shield" aria-hidden="true"></i>
                        </span>
                        <button type="button" class="btn-toggle-password" data-target="password_confirmation" title="Tampilkan kata sandi" tabindex="-1">
                            <i class="fe fe-eye"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="container-login100-form-btn mt-2">
                    <button type="submit" class="btn-auth-primary">
                        <i class="fe fe-check-circle me-2" style="font-size: 16px;"></i> Simpan &amp; Perbarui Kata Sandi
                    </button>
                </div> 

                <div class="auth-footer-text text-start mt-4 pt-2 border-top">
                    <p class="mb-1 text-muted" style="font-size: 13.5px;">
                        Ingat sandi lama Anda? 
                        <a href="{{ route('login') }}" class="auth-link fw-bold ms-1">Masuk ke Halaman Login &rarr;</a>
                    </p>
                    <small class="text-muted d-block mt-2" style="font-size: 11.5px;">
                        &copy; {{ date('Y') }} POSHUB ENTERPRISE. Hak Cipta Dilindungi.
                    </small>
                </div>
            </form>
        </div>

        <!-- Right Side: Security Tips Panel -->
        <div class="auth-showcase-side">
            <div>
                <div>
                    <span class="auth-showcase-badge">
                        <i class="fe fe-shield text-info"></i> PANDUAN KATA SANDI
                    </span>
                </div>
                <h2 class="auth-showcase-title">Kriteria Kata Sandi yang Kuat &amp; Aman</h2>
                <p class="auth-showcase-subtitle">Untuk melindungi integritas laporan keuangan bisnis dan kasir POS Anda, pastikan kata sandi memenuhi kriteria berikut:</p>

                <div class="d-flex flex-column gap-3 mb-4">
                    <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: rgba(30, 41, 59, 0.6); border: 1px solid #334155;">
                        <i class="fe fe-check-circle text-success fs-18"></i>
                        <span class="text-white-90 small">Minimal 8 karakter (disarankan &ge; 10 karakter)</span>
                    </div>

                    <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: rgba(30, 41, 59, 0.6); border: 1px solid #334155;">
                        <i class="fe fe-check-circle text-success fs-18"></i>
                        <span class="text-white-90 small">Kombinasi huruf besar (A-Z) dan kecil (a-z)</span>
                    </div>

                    <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: rgba(30, 41, 59, 0.6); border: 1px solid #334155;">
                        <i class="fe fe-check-circle text-success fs-18"></i>
                        <span class="text-white-90 small">Mengandung angka (0-9) atau simbol khusus (@#$%)</span>
                    </div>

                    <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: rgba(30, 41, 59, 0.6); border: 1px solid #334155;">
                        <i class="fe fe-check-circle text-success fs-18"></i>
                        <span class="text-white-90 small">Hindari nama toko, tanggal lahir, atau pola umum</span>
                    </div>
                </div>
            </div>

            <!-- Showcase Footer -->
            <div class="auth-showcase-footer">
                <span class="auth-security-pill">
                    <i class="fe fe-lock"></i> Argon2 / Bcrypt Encrypted
                </span>
                <span class="text-white-50">Zero Plain-text Storage</span>
            </div>
        </div>
    </div>
</div>
@endsection