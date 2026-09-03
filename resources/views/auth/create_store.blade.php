@extends('layouts.welcome')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/maps/leaflet.css') }}" />
@endsection

@section('content')
<div class="container py-4">
    <!-- Back Button Bar -->
    <div class="mb-4">
        <a href="{{ route('store.choose') }}" class="auth-link-back">
            <i class="fe fe-arrow-left"></i> Kembali ke Pemilihan Cabang
        </a>
    </div>

    <div class="row justify-content-center">
        <div class="col-sm-12 col-lg-9">
            <div class="card shadow-sm border" style="border-radius: var(--poshub-card-radius); overflow: hidden;">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="auth-brand-badge">Setup Cabang Baru</span>
                    </div>
                    <h3 class="h4 fw-bold text-dark mb-1">Buat Toko / Cabang Baru</h3>
                    <p class="text-muted small mb-0">Lengkapi data identitas gerai dan preferensi operasional cabang baru Anda.</p>
                </div>

                <div class="card-body p-4">
                    <x-admin.validation-component></x-admin.validation-component>

                    <form method="POST" class="row g-3" action="{{ route('store.add') }}">
                        @csrf

                        <!-- Nama Toko -->
                        <div class="col-md-6">
                            <label for="name" class="auth-label">Nama Cabang / Toko <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="text" name="name" value="{{ old('name') }}" id="name" class="form-control auth-input-with-icon" required placeholder="contoh: POSHUB Cabang Sudirman">
                                <span class="auth-field-icon"><i class="fe fe-home"></i></span>
                            </div>
                        </div>

                        <!-- Email Toko -->
                        <div class="col-md-6">
                            <label for="email" class="auth-label">Email Cabang <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="email" name="email" value="{{ old('email') }}" id="email" class="form-control auth-input-with-icon" required placeholder="contoh: sudirman@poshub.id">
                                <span class="auth-field-icon"><i class="fe fe-mail"></i></span>
                            </div>
                        </div>

                        <!-- Telepon -->
                        <div class="col-md-6">
                            <label for="phone" class="auth-label">Nomor Telepon / WhatsApp <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="tel" name="phone" value="{{ old('phone') }}" id="phone" class="form-control auth-input-with-icon" required placeholder="contoh: 081234567890">
                                <span class="auth-field-icon"><i class="fe fe-phone"></i></span>
                            </div>
                        </div>

                        <!-- Kode Pos -->
                        <div class="col-md-6">
                            <label for="zip_code" class="auth-label">Kode Pos Cabang <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <input type="text" name="zip_code" value="{{ old('zip_code') }}" id="zip_code" class="form-control auth-input-with-icon" required placeholder="contoh: 12190">
                                <span class="auth-field-icon"><i class="fe fe-map-pin"></i></span>
                            </div>
                        </div>

                        <!-- Opsi Akuntansi -->
                        <div class="col-md-6">
                            <label class="auth-label">Modul Akuntansi <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <select class="form-control auth-input-with-icon" name="accountant_use" required>
                                    <option value="yes">Aktifkan Modul Akuntansi Penuh</option>
                                    <option value="no">Kasir POS Standar Saja</option>
                                </select>
                                <span class="auth-field-icon"><i class="fe fe-book"></i></span>
                            </div>
                        </div>

                        <!-- Opsi Pajak -->
                        <div class="col-md-6">
                            <label class="auth-label">Opsi Pajak (PPN) <span class="text-danger">*</span></label>
                            <div class="position-relative">
                                <select class="form-control auth-input-with-icon" name="tax_option" id="taxoption" required>
                                    <option value="no">Tidak Gunakan Pajak</option>
                                    <option value="active">Gunakan Pajak Standar (PPN)</option>
                                </select>
                                <span class="auth-field-icon"><i class="fe fe-percent"></i></span>
                            </div>
                        </div>

                        <!-- Shift Register -->
                        <div class="col-md-6">
                            <label class="auth-label">Gunakan Shift Kasir (Shift Register)</label>
                            <div class="position-relative">
                                <select class="form-control auth-input-with-icon" name="shift_register" id="shift_register">
                                    <option value="active">Aktif (Wajib Buka/Tutup Kasir)</option>
                                    <option value="no">Non-Aktif (Transaksi Tanpa Shift)</option>
                                </select>
                                <span class="auth-field-icon"><i class="fe fe-clock"></i></span>
                            </div>
                        </div>

                        <!-- Gudang Default -->
                        <div class="col-md-6">
                            <label class="auth-label">Gudang Inventori Utama</label>
                            <div class="position-relative">
                                <select class="form-control auth-input-with-icon" name="warehouse_default_id">
                                    <option value="">Gudang Utama Cabang Ini</option>
                                </select>
                                <span class="auth-field-icon"><i class="fe fe-box"></i></span>
                            </div>
                        </div>

                        <!-- Alamat Lengkap -->
                        <div class="col-12">
                            <label for="address" class="auth-label">Alamat Lengkap Cabang <span class="text-danger">*</span></label>
                            <div class="form-group mb-0">
                                <textarea class="form-control no-icon" name="address" id="address" rows="3" required placeholder="Masukkan alamat lengkap lokasi gerai, nomor gedung, kelurahan, kecamatan, kota...">{{ old('address') }}</textarea>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="col-12 d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('store.choose') }}" class="btn btn-auth-secondary px-4">
                                <i class="fe fe-x me-1"></i> Batal
                            </a>
                            <button type="submit" class="btn btn-auth-primary px-4" style="width: auto;">
                                <i class="fe fe-check me-2"></i> Simpan Cabang Baru
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/vendors/maps/store_create.js') }}"></script>
@endsection