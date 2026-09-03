@extends('layouts.app')
@section('content')
<div class="auth-page-wrapper">
    <!-- Top Navigation Link -->
    <div class="auth-nav-top auth-card-wide" style="max-width: 860px;">
        <a href="{{ route('home') }}" class="auth-link-back">
            <i class="fe fe-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="wrap-login100 auth-card-wide">
        <!-- Brand Header -->
        <div class="auth-brand-header text-start mb-3">
            <a href="{{ url('/') }}" class="d-inline-block text-decoration-none">
                <img src="{{ asset('images/logo.png') }}" class="auth-brand-logo" alt="POSHUB Enterprise">
            </a>
            <div>
                <span class="auth-brand-badge">Setup Awal Usaha &bull; Langkah 2 dari 2</span>
            </div>
        </div>

        <!-- Form Title -->
        <div class="mb-3">
            <h1 class="auth-form-title text-start mb-1">Registrasi Profil Usaha &amp; Toko</h1>
            <p class="auth-form-subtitle text-start mb-0">Lengkapi data identitas badan usaha dan preferensi modul akuntansi untuk outlet utama Anda.</p> 
        </div>

        <x-admin.validation-component></x-admin.validation-component>

        <form class="validate-form row g-3" method="POST" action="{{ route('business.register.create') }}">
            @csrf
            
            <div class="col-sm-12 col-lg-6">
                <label for="name" class="auth-label">Nama Bisnis / Perusahaan <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <input type="text" class="form-control auth-input-with-icon" name="name" value="{{ old('name') }}" id="name" required placeholder="contoh: PT Maju Bersama Ritel">
                    <span class="auth-field-icon">
                        <i class="fe fe-briefcase"></i>
                    </span>
                </div>
            </div>

            <div class="col-sm-12 col-lg-6">
                <label for="email" class="auth-label">Email Resmi Operasional <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <input type="email" class="form-control auth-input-with-icon" name="email" value="{{ old('email') }}" id="email" required placeholder="contoh: info@perusahaan.com">
                    <span class="auth-field-icon">
                        <i class="fe fe-mail"></i>
                    </span>
                </div>
            </div>

            <div class="col-sm-12 col-lg-6">
                <label for="phone" class="auth-label">Nomor WhatsApp / Kontak Bisnis <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <input type="tel" class="form-control auth-input-with-icon" name="phone" value="{{ old('phone') }}" id="phone" required placeholder="contoh: 081234567890">
                    <span class="auth-field-icon">
                        <i class="fe fe-phone"></i>
                    </span>
                </div>
            </div>

            <div class="col-sm-12 col-lg-6">
                <label class="auth-label">Modul Finansial &amp; Buku Besar <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <select class="form-control auth-input-with-icon" name="accountant_use" required>
                        <option value="yes">Aktifkan Akuntansi Penuh (Jurnal &amp; Laba Rugi)</option>
                        <option value="no">Kasir POS Standar Saja</option>
                    </select>
                    <span class="auth-field-icon">
                        <i class="fe fe-book"></i>
                    </span>
                </div>
            </div>

            <div class="col-sm-12 col-lg-6">
                <label class="auth-label">Pengaturan Pajak (PPN) <span class="text-danger">*</span></label>
                <div class="position-relative">
                    <select class="form-control auth-input-with-icon" name="tax_option" id="taxoption" required>
                        <option value="no">Tidak Menggunakan Pajak</option>
                        <option value="active">Gunakan Pajak Standar (PPN)</option>
                    </select>
                    <span class="auth-field-icon">
                        <i class="fe fe-percent"></i>
                    </span>
                </div>
            </div>

            <div class="col-sm-12 col-lg-6">
                <label for="zipCode" class="auth-label">Kode Pos Toko</label>
                <div class="position-relative">
                    <input type="text" class="form-control auth-input-with-icon" name="zip_code" id="zipCode" placeholder="contoh: 12345">
                    <span class="auth-field-icon">
                        <i class="fe fe-map-pin"></i>
                    </span>
                </div>
            </div>

            <div class="col-12">
                <label for="address" class="auth-label">Alamat Kantor / Outlet Utama <span class="text-danger">*</span></label>
                <div class="form-group mb-0">
                    <textarea class="form-control no-icon" name="address" id="address" rows="3" required placeholder="Masukkan alamat lengkap lokasi kantor pusat atau gerai cabang utama...">{{ old('address') }}</textarea>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="{{ route('home') }}" class="btn btn-auth-secondary px-4" style="text-decoration: none;">
                    <i class="fe fe-arrow-left me-1"></i> Batal
                </a>
                <button class="btn btn-auth-primary px-4" style="width: auto;" type="submit">
                    <i class="fe fe-check me-2"></i> Simpan Profil &amp; Buka Dashboard
                </button>
            </div>
        </form>
    </div>
</div>
@endsection