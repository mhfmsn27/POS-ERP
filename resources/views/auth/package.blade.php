@extends('layouts.welcome')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
<style>
    .pricing-page-wrapper {
        min-height: calc(100vh - 120px);
        padding: 40px 16px;
    }
    .pricing-card-modern {
        border-radius: var(--poshub-card-radius);
        border: 1px solid var(--poshub-border);
        background: #ffffff;
        box-shadow: var(--poshub-shadow-sm);
        transition: var(--poshub-transition);
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
    }
    .pricing-card-modern:hover {
        transform: translateY(-4px);
        box-shadow: var(--poshub-shadow-lg);
        border-color: var(--poshub-primary);
    }
    .pricing-badge {
        background: var(--poshub-primary-light);
        color: var(--poshub-primary);
        font-weight: 700;
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endsection

@section('content')
<div class="pricing-page-wrapper container">
    <!-- Header -->
    <div class="text-center mb-5">
        <div class="d-inline-flex align-items-center gap-2 mb-2">
            <span class="auth-brand-badge">Paket Langganan POSHUB Enterprise</span>
        </div>
        <h2 class="fw-bold text-dark mb-2">Pilih Paket Layanan Bisnis Anda</h2>
        <p class="text-muted" style="max-width: 600px; margin: 0 auto;">Tingkatkan efisiensi kasir, laporan akuntansi otomatis, dan kontrol stok multi-cabang tanpa batasan.</p>
    </div>

    <div class="row g-4 justify-content-center">
        @foreach ($packages as $package)
        <div class="col-sm-12 col-md-6 col-lg-4">
            <div class="pricing-card-modern p-4">
                <div class="mb-3">
                    <span class="pricing-badge">{{ $package->name }}</span>
                    <div class="mt-3">
                        <span class="h3 fw-bold text-dark">Rp {{ number_format($package->price) }}</span>
                        <span class="text-muted small"> / {{ number_format($package->limit_day) }} Hari</span>
                    </div>
                    <p class="text-muted small mt-2 mb-0">{{ $package->description }}</p>
                </div>

                <hr class="my-3">

                <div class="mb-4 flex-grow-1">
                    <h6 class="fw-bold text-dark small mb-3">Fitur Termasuk:</h6>
                    <ul class="list-unstyled mb-0">
                        @foreach ($package->details as $detail)
                        <li class="d-flex align-items-center gap-2 mb-2 text-dark small">
                            <i class="fe fe-check-circle text-success fs-15 flex-shrink-0"></i>
                            <span>{{ $detail->name }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="mt-auto pt-2">
                    <a href="{{ route('store.choose') }}" class="btn btn-auth-primary">
                        <i class="fe fe-check me-1"></i> Pilih Paket Ini
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection