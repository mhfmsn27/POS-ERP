@extends('layouts.rma')
@section('content')
<div class="poshub-glass-card p-4 p-md-5 mx-auto my-auto shadow-lg" style="max-width: 480px; width: 92%;">
    <div class="text-center mb-4">
        <img src="{{ asset('images/logo.png') }}" style="max-height: 52px; width: auto;" class="mb-3" alt="POSHUB Logo" />
        <h3 class="fw-bold mb-1" style="letter-spacing: -0.5px;">Tracking Servis & RMA</h3>
        <p class="text-muted small">Pantau status pengerjaan unit servis Anda secara real-time</p>
        <x-admin.validation-component></x-admin.validation-component>
    </div>
    
    <form class="mt-3" method="POST" action="{{ route('check.rma') }}">
        @csrf
        <div class="form-group mb-3">
            <label for="referensi" class="fw-semibold small text-secondary mb-1">Nomor Referensi RMA / Servis</label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0"><i class="fa fa-receipt text-primary"></i></span>
                <input type="text" id="referensi" name="referensi" placeholder="Contoh: RMA-2026-0012" value="{{ old('referensi') }}" class="form-control border-start-0 py-2" required autofocus>
            </div>
        </div>

        <div class="d-grid mt-4">
            <button type="submit" class="btn btn-primary py-2 fw-bold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
                <i class="fa fa-search"></i> Cek Status Sekarang
            </button>
        </div> 
    </form>
    
    <div class="text-center mt-4 pt-2 border-top">
        <small class="text-muted">Powered by <strong>POSHUB ACCOUNTING</strong></small>
    </div>
</div>
@endsection