@extends('layouts.app')
@section('content')
<div class="auth-page-wrapper">
    <!-- Top Navigation Link -->
    <div class="auth-nav-top auth-split-container-wide">
        <a href="{{ url('/') }}" class="auth-link-back">
            <i class="fe fe-arrow-left"></i> Kembali ke Beranda Website
        </a>
    </div>

    <div class="auth-split-container auth-split-container-wide">
        <!-- Left Side: Form Panel -->
        <div class="auth-form-side">
            <!-- Brand Header -->
            <div class="auth-brand-header text-start mb-3">
                <a href="{{ url('/') }}" class="d-inline-block text-decoration-none">
                    <img src="{{ asset('images/logo.png') }}" class="auth-brand-logo" alt="POSHUB Enterprise">
                </a>
                <div>
                    <span class="auth-brand-badge">Pendaftaran Akun Baru</span>
                </div>
            </div>

            <!-- Form Title -->
            <div class="mb-3">
                <h1 class="auth-form-title text-start mb-1">Daftar Akun Enterprise</h1>
                <p class="auth-form-subtitle text-start mb-0">Buat akun staf atau pemilik baru untuk mengelola transaksi POS dan laporan keuangan terpadu.</p>
            </div>

            <!-- Validation Errors -->
            <x-admin.validation-component></x-admin.validation-component>

            <!-- Register Form -->
            <form class="validate-form row g-3" method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Nama Lengkap -->
                <div class="col-sm-12 col-md-6">
                    <div class="form-group mb-0">
                        <label for="regName" class="auth-label">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input class="form-control auth-input-with-icon" type="text" id="regName" name="name" value="{{ old('name') }}" required autofocus placeholder="contoh: Budi Pratama">
                            <span class="auth-field-icon">
                                <i class="fe fe-user"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Alamat Email -->
                <div class="col-sm-12 col-md-6">
                    <div class="form-group mb-0">
                        <label for="regEmail" class="auth-label">Alamat Email <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input class="form-control auth-input-with-icon" type="email" id="regEmail" name="email" value="{{ old('email') }}" required placeholder="contoh: budi@poshub.id">
                            <span class="auth-field-icon">
                                <i class="fe fe-mail"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Nomor WhatsApp / HP -->
                <div class="col-sm-12 col-md-6">
                    <div class="form-group mb-0">
                        <label for="regPhone" class="auth-label">Nomor WhatsApp / HP <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input class="form-control auth-input-with-icon" type="tel" id="regPhone" name="phone" value="{{ old('phone') }}" required placeholder="contoh: 081234567890">
                            <span class="auth-field-icon">
                                <i class="fe fe-phone"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Jenis Kelamin -->
                <div class="col-sm-12 col-md-6">
                    <div class="form-group mb-0">
                        <label for="regGender" class="auth-label">Jenis Kelamin</label>
                        <div class="position-relative">
                            <select class="form-control auth-input-with-icon" name="jk" id="regGender">
                                <option value="pria" {{ old('jk') == 'pria' ? 'selected' : '' }}>Laki-laki (Pria)</option>
                                <option value="wanita" {{ old('jk') == 'wanita' ? 'selected' : '' }}>Perempuan (Wanita)</option>
                            </select>
                            <span class="auth-field-icon">
                                <i class="fe fe-users"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Kata Sandi -->
                <div class="col-sm-12 col-md-6">
                    <div class="form-group mb-0">
                        <label for="regPassword" class="auth-label">Kata Sandi <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input class="form-control auth-input-with-icon auth-input-with-toggle" type="password" id="regPassword" name="password" required placeholder="Minimal 8 karakter">
                            <span class="auth-field-icon">
                                <i class="fe fe-lock"></i>
                            </span>
                            <button type="button" class="btn-toggle-password" data-target="regPassword" title="Tampilkan kata sandi" tabindex="-1">
                                <i class="fe fe-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Konfirmasi Kata Sandi -->
                <div class="col-sm-12 col-md-6">
                    <div class="form-group mb-0">
                        <label for="regPasswordConfirm" class="auth-label">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input class="form-control auth-input-with-icon auth-input-with-toggle" type="password" id="regPasswordConfirm" name="password_confirmation" required placeholder="Ulangi kata sandi">
                            <span class="auth-field-icon">
                                <i class="fe fe-shield"></i>
                            </span>
                            <button type="button" class="btn-toggle-password" data-target="regPasswordConfirm" title="Tampilkan kata sandi" tabindex="-1">
                                <i class="fe fe-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Terms & Conditions Checkbox -->
                <div class="col-12 mt-2">
                    <div class="form-check d-flex align-items-center">
                        <input class="form-check-input me-2" type="checkbox" id="agreeTerms" name="agree" required checked>
                        <label class="form-check-label" for="agreeTerms">
                            Saya menyetujui <a href="#" class="auth-link">Syarat &amp; Ketentuan</a> serta <a href="#" class="auth-link">Kebijakan Privasi</a> POSHUB
                        </label>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="col-12 mt-3">
                    <button type="submit" class="btn-auth-primary">
                        <i class="fe fe-user-plus me-2"></i> Daftar Akun Baru Sekarang
                    </button>
                </div>

                <!-- Footer Links -->
                <div class="col-12 auth-footer-text text-start mt-3 pt-2 border-top">
                    <p class="mb-1 text-muted" style="font-size: 13.5px;">
                        Sudah memiliki akun operasional? 
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
                        <i class="fe fe-check-circle text-success"></i> PENDAFTARAN CEPAT &amp; MUDAH
                    </span>
                </div>
                <h2 class="auth-showcase-title">Mulai Kelola Bisnis Anda dengan POSHUB Enterprise</h2>
                <p class="auth-showcase-subtitle">Satu akun terpadu untuk mengontrol seluruh cabang toko, kelola kasir POS, sinkronisasi gudang stok, dan otomatisasi akuntansi real-time.</p>

                <!-- Feature Grid -->
                <div class="auth-feature-grid">
                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">
                            <i class="fe fe-zap"></i>
                        </div>
                        <div class="auth-feature-heading">Setup Instan</div>
                        <p class="auth-feature-desc">Langsung aktif dalam hitungan detik setelah registrasi email terkonfirmasi.</p>
                    </div>

                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">
                            <i class="fe fe-users"></i>
                        </div>
                        <div class="auth-feature-heading">Multi-Staff &amp; Role</div>
                        <p class="auth-feature-desc">Atur hak akses kasir, manajer gudang, staf akuntan, dan owner.</p>
                    </div>

                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">
                            <i class="fe fe-database"></i>
                        </div>
                        <div class="auth-feature-heading">Cloud Terintegrasi</div>
                        <p class="auth-feature-desc">Data transaksi &amp; persediaan tersinkron otomatis real-time.</p>
                    </div>

                    <div class="auth-feature-item">
                        <div class="auth-feature-icon">
                            <i class="fe fe-shield"></i>
                        </div>
                        <div class="auth-feature-heading">Keamanan Terjamin</div>
                        <p class="auth-feature-desc">Enkripsi data berlapis dan proteksi sesi multi-tenant aman.</p>
                    </div>
                </div>
            </div>

            <!-- Showcase Footer -->
            <div class="auth-showcase-footer">
                <span class="auth-security-pill">
                    <i class="fe fe-shield"></i> Data Protection Guaranteed
                </span>
                <span class="text-white-50">Enterprise 99.9% Uptime SLA</span>
            </div>
        </div>
    </div>
</div>
@endsection