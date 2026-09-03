@extends('layouts.app')
@section('content')
<div class="auth-page-wrapper">
    <!-- Top Navigation Link -->
    <div class="auth-nav-top">
        <a href="{{ route('login') }}" class="auth-link-back">
            <i class="fe fe-arrow-left"></i> Kembali ke Halaman Masuk
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
                    <span class="auth-brand-badge">Pemulihan Kata Sandi</span>
                </div>
            </div>

            <!-- Form Title -->
            <div class="mb-3">
                <h1 class="auth-form-title text-start mb-1">Lupa Kata Sandi?</h1>
                <p class="auth-form-subtitle text-start mb-0">Masukkan alamat email akun operasional Anda. Kami akan mengirimkan tautan resmi untuk mengatur ulang kata sandi.</p>
            </div>

            <!-- Status & Validation -->
            @if (session('status'))
                <div class="alert alert-success d-flex align-items-center mb-3" role="alert" style="background-color: var(--poshub-success-light); border: 1px solid var(--poshub-success-border); color: var(--poshub-success); font-size: 13.5px; border-radius: 8px;">
                    <i class="fe fe-check-circle me-2 fs-16"></i>
                    <div>{{ session('status') }}</div>
                </div>
            @endif
            <x-admin.validation-component></x-admin.validation-component>

            <!-- Password Request Form -->
            <form class="validate-form" method="POST" action="{{ route('password.email') }}">
                @csrf
                
                <div class="wrap-input100 validate-input" data-validate="Alamat email wajib diisi dengan benar">
                    <label for="email" class="auth-label">Alamat Email Terdaftar <span class="text-danger">*</span></label>
                    <div class="position-relative">
                        <input class="input100 auth-input-with-icon" type="email" id="email" name="email" value="{{ old('email') }}" required autofocus placeholder="contoh: admin@poshub.id">
                        <span class="symbol-input100">
                            <i class="fe fe-mail" aria-hidden="true"></i>
                        </span>
                    </div>
                </div> 

                <div class="container-login100-form-btn mt-2">
                    <button type="submit" class="btn-auth-primary">
                        <i class="fe fe-send me-2" style="font-size: 15px;"></i> Kirim Tautan Reset Kata Sandi
                    </button>
                </div>

                <div class="auth-footer-text text-start mt-4 pt-2 border-top">
                    <p class="mb-1 text-muted" style="font-size: 13.5px;">
                        Sudah ingat kata sandi Anda? 
                        <a href="{{ route('login') }}" class="auth-link fw-bold ms-1">Masuk ke Akun &rarr;</a>
                    </p>
                    <small class="text-muted d-block mt-2" style="font-size: 11.5px;">
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
                        <i class="fe fe-shield text-info"></i> KEAMANAN AKUN TERPADU
                    </span>
                </div>
                <h2 class="auth-showcase-title">Pemulihan Akun Terverifikasi &amp; Aman</h2>
                <p class="auth-showcase-subtitle">Proses reset kata sandi POSHUB menggunakan token unik terenkripsi dengan masa kedaluwarsa otomatis demi menjaga integritas data finansial Anda.</p>

                <!-- Security Steps -->
                <div class="d-flex flex-column gap-3 mb-4">
                    <div class="d-flex align-items-start gap-3 p-3 rounded" style="background: rgba(30, 41, 59, 0.6); border: 1px solid #334155;">
                        <div class="auth-feature-icon flex-shrink-0 mb-0">
                            <span class="fw-bold">1</span>
                        </div>
                        <div>
                            <div class="auth-feature-heading">Masukkan Email Terdaftar</div>
                            <p class="auth-feature-desc">Gunakan alamat email resmi yang terdaftar pada sistem bisnis Anda.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 p-3 rounded" style="background: rgba(30, 41, 59, 0.6); border: 1px solid #334155;">
                        <div class="auth-feature-icon flex-shrink-0 mb-0">
                            <span class="fw-bold">2</span>
                        </div>
                        <div>
                            <div class="auth-feature-heading">Cek Kotak Masuk / Spam</div>
                            <p class="auth-feature-desc">Klik tautan pemulihan instan yang dikirimkan oleh sistem dalam beberapa detik.</p>
                        </div>
                    </div>

                    <div class="d-flex align-items-start gap-3 p-3 rounded" style="background: rgba(30, 41, 59, 0.6); border: 1px solid #334155;">
                        <div class="auth-feature-icon flex-shrink-0 mb-0">
                            <span class="fw-bold">3</span>
                        </div>
                        <div>
                            <div class="auth-feature-heading">Buat Kata Sandi Baru</div>
                            <p class="auth-feature-desc">Tentukan kombinasi sandi baru minimal 8 karakter lalu masuk kembali.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Showcase Footer -->
            <div class="auth-showcase-footer">
                <span class="auth-security-pill">
                    <i class="fe fe-lock"></i> 256-Bit Encrypted Link
                </span>
                <span class="text-white-50">Token Expire in 60 Minutes</span>
            </div>
        </div>
    </div>
</div>
@endsection