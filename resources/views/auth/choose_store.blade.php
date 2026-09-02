@extends('layouts.welcome')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
<style>
    .store-card {
        border-radius: var(--poshub-card-radius);
        border: 1px solid var(--poshub-border);
        background: #ffffff;
        transition: var(--poshub-transition);
        overflow: hidden;
    }
    .store-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--poshub-shadow-lg);
        border-color: var(--poshub-primary-border);
    }
    .badge-enterprise {
        background: var(--poshub-success-light);
        color: var(--poshub-success);
        border: 1px solid var(--poshub-success-border);
        font-weight: 700;
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }
    .btn-choose-store {
        background: var(--poshub-primary);
        border: none;
        color: #ffffff;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: var(--poshub-btn-radius);
        transition: var(--poshub-transition);
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }
    .btn-choose-store:hover {
        background: var(--poshub-primary-hover);
        color: #ffffff;
        box-shadow: 0 4px 10px rgba(30, 64, 175, 0.25);
    }
</style>
@endsection

@section('content')
<div class="container py-5">
    <div class="text-center mb-5">
        <img src="{{ asset('images/logo.png') }}" alt="POSHUB" style="max-height: 48px; width: auto; margin-bottom: 12px;">
        <h2 class="fw-bold text-dark mb-1" style="letter-spacing: -0.3px;">Pilih Cabang / Outlet Operasional</h2>
        <p class="text-muted">Silakan pilih cabang toko untuk memulai sesi kasir dan manajemen akuntansi</p>
    </div>

    <div class="row g-4 justify-content-center">
        @foreach($data as $d)
        <div class="col-md-6 col-lg-4">
            <div class="card store-card h-100 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 px-4">
                    <span class="badge-enterprise">
                        <i class="fe fe-check-circle"></i> Enterprise Aktif
                    </span>
                    <small class="text-muted fw-semibold">Cabang #{{ $d->id }}</small>
                </div>
                <div class="card-body text-center px-4 py-3">
                    <div class="avatar avatar-xxl rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 72px; height: 72px; background-color: var(--poshub-primary-light); color: var(--poshub-primary);">
                        <i class="fe fe-home" style="font-size: 30px;"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-2 text-dark">{{ $d->name }}</h4>
                    <p class="text-muted small mb-0">{{ $d->address ?? 'Alamat operasional kantor/cabang' }}</p>
                    @if(!empty($d->phone))
                    <p class="text-muted small mb-0 mt-1"><i class="fe fe-phone me-1"></i> {{ $d->phone }}</p>
                    @endif
                </div>
                <div class="card-footer bg-transparent border-0 text-center pb-4 px-4">
                    <a href="{{ route('choose.store', $d->id) }}" class="btn btn-choose-store">
                        <i class="fe fe-log-in me-1"></i> Buka Dashboard Cabang
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection