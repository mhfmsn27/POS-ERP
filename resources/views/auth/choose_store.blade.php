@extends('layouts.welcome')

@section('styles')
<style>
    .store-card {
        border-radius: 16px;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        border: 1px solid rgba(226, 232, 240, 0.8);
        overflow: hidden;
    }
    .store-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.1), 0 6px 12px -4px rgba(0, 0, 0, 0.06);
    }
    .badge-enterprise {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #fff;
        font-weight: 600;
        font-size: 11px;
        padding: 5px 12px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .btn-choose-store {
        background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        border: none;
        color: #fff;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 10px;
        transition: all 0.2s ease;
    }
    .btn-choose-store:hover {
        background: linear-gradient(135deg, #2563eb 0%, #1e40af 100%);
        color: #fff;
        box-shadow: 0 4px 12px rgba(37, 99, 235, 0.35);
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="text-center mb-4">
        <h2 class="fw-bold text-dark mb-1">Pilih Cabang / Toko</h2>
        <p class="text-muted">Silakan pilih cabang atau outlet operasional internal untuk melanjutkan</p>
    </div>

    <div class="row g-4 justify-content-center">
        @foreach($data as $d)
        <div class="col-md-6 col-lg-4">
            <div class="card store-card h-100 shadow-sm">
                <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 px-4">
                    <span class="badge-enterprise">
                        <i class="fe fe-check-circle"></i> Enterprise Aktif
                    </span>
                    <small class="text-muted">Cabang #{{ $d->id }}</small>
                </div>
                <div class="card-body text-center px-4 py-3">
                    <div class="avatar avatar-xxl rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3 shadow-sm" style="width: 80px; height: 80px;">
                        <i class="fe fe-home text-primary" style="font-size: 36px;"></i>
                    </div>
                    <h4 class="h5 fw-bold mb-2 text-dark">{{ $d->name }}</h4>
                    <p class="text-muted small mb-0">{{ $d->address ?? 'Alamat operasional kantor/cabang' }}</p>
                    @if(!empty($d->phone))
                    <p class="text-muted small mb-0 mt-1"><i class="fe fe-phone me-1"></i> {{ $d->phone }}</p>
                    @endif
                </div>
                <div class="card-footer bg-transparent border-0 text-center pb-4 px-4">
                    <a href="{{ route('choose.store', $d->id) }}" class="btn btn-choose-store w-100">
                        <i class="fe fe-log-in me-2"></i> Buka Dashboard Cabang
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection