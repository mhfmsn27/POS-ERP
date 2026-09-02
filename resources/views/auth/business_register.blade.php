@extends('layouts.app')
@section('content')
<div class="auth-page-wrapper">
    <div class="wrap-login100 auth-card-wide">
        <!-- Brand Header -->
        <div class="auth-brand-header">
            <a href="{{ url('/') }}" class="d-inline-block">
                <img src="{{ asset('images/logo.png') }}" class="auth-brand-logo" alt="POSHUB Enterprise">
            </a>
            <div>
                <span class="auth-brand-badge">Pendaftaran Profil Usaha</span>
            </div>
        </div>

        <!-- Form Title -->
        <h1 class="auth-form-title">Registrasi Profil Bisnis &amp; Toko</h1>
        <p class="auth-form-subtitle">Lengkapi identitas badan usaha dan preferensi sistem akuntansi untuk toko baru Anda.</p> 

        <x-admin.validation-component></x-admin.validation-component>

        <form class="validate-form row g-3" method="POST" action="{{ route('business.register.create') }}">
            @csrf
            
            <div class="col-sm-12 col-lg-6">
                <label for="name" class="auth-label">Nama Bisnis / Perusahaan <span class="text-danger">*</span></label>
                <div class="form-group mb-0">
                    <input type="text" class="form-control no-icon" name="name" value="{{ old('name') }}" id="name" required placeholder="contoh: PT Maju Bersama">
                </div>
            </div>

            <div class="col-sm-12 col-lg-6">
                <label for="email" class="auth-label">Email Operasional Bisnis <span class="text-danger">*</span></label>
                <div class="form-group mb-0">
                    <input type="email" class="form-control no-icon" name="email" value="{{ old('email') }}" id="email" required placeholder="contoh: info@perusahaan.com">
                </div>
            </div>

            <div class="col-sm-12 col-lg-6">
                <label for="phone" class="auth-label">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                <div class="form-group mb-0">
                    <input type="tel" class="form-control no-icon" name="phone" value="{{ old('phone') }}" id="phone" required placeholder="contoh: 081234567890">
                </div>
            </div>

            <div class="col-sm-12 col-lg-6">
                <label class="auth-label">Modul Akuntansi &amp; Buku Besar <span class="text-danger">*</span></label>
                <div class="form-group mb-0">
                    <select class="form-control no-icon" name="accountant_use" required>
                        <option value="yes">Aktifkan Modul Akuntansi Penuh</option>
                        <option value="no">Non-Aktifkan (Mode Kasir POS Saja)</option>
                    </select>
                </div>
            </div>

            <div class="col-sm-12 col-lg-6">
                <label class="auth-label">Pengaturan Pajak (PPN) <span class="text-danger">*</span></label>
                <div class="form-group mb-0">
                    <select class="form-control no-icon" name="tax_option" id="taxoption" required>
                        <option value="no">Tidak Menggunakan Pajak</option>
                        <option value="active">Gunakan Pajak Standar</option>
                    </select>
                </div>
            </div>

            <div class="col-12">
                <label for="address" class="auth-label">Alamat Kantor / Outlet Utama <span class="text-danger">*</span></label>
                <div class="form-group mb-0">
                    <textarea class="form-control no-icon" name="address" id="address" rows="3" required placeholder="Masukkan alamat lengkap kantor atau lokasi toko utama">{{ old('address') }}</textarea>
                </div>
            </div>

            <div class="col-12 d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                <a href="{{ route('home') }}" class="btn-auth-secondary d-inline-flex align-items-center justify-content-center px-4" style="text-decoration: none;">
                    <i class="fe fe-arrow-left me-1"></i> Batal
                </a>
                <button class="btn-auth-primary px-4" style="width: auto;" type="submit">
                    <i class="fe fe-check me-2"></i> Simpan &amp; Lanjutkan Setup Toko
                </button>
            </div>
        </form>
    </div>
</div>
@endsection