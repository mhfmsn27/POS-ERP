@extends('layouts.welcome')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
<style>
    .store-select-page {
        min-height: calc(100vh - 120px);
        padding: 40px 16px;
    }
    .store-hero-card {
        background: #ffffff;
        border: 1px solid var(--poshub-border);
        border-radius: var(--poshub-card-radius);
        padding: 24px 28px;
        box-shadow: var(--poshub-shadow-sm);
        margin-bottom: 28px;
    }
</style>
@endsection

@section('content')
<div class="store-select-page">
    <div class="store-select-wrapper">
        <!-- Hero & Action Header Bar -->
        <div class="store-hero-card">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge-enterprise">
                            <i class="fe fe-check-circle"></i> Sesi Enterprise Aktif
                        </span>
                        <span class="text-muted small">&bull; {{ auth()->user()->name ?? 'Pengguna' }} ({{ auth()->user()->email ?? '' }})</span>
                    </div>
                    <h2 class="h4 fw-bold text-dark mb-1" style="letter-spacing: -0.3px;">Pilih Cabang / Outlet Operasional</h2>
                    <p class="text-muted small mb-0">Silakan pilih salah satu cabang outlet untuk memulai transaksi kasir POS dan manajemen akuntansi.</p>
                </div>

                <div class="d-flex align-items-center gap-2 flex-wrap">
                    @if(Route::has('store.create'))
                    <a href="{{ route('store.create') }}" class="btn btn-auth-secondary" style="height: 40px; font-size: 13.5px; padding: 0 16px;">
                        <i class="fe fe-plus-circle me-1 text-primary"></i> Tambah Toko Baru
                    </a>
                    @endif
                    <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-outline-danger d-inline-flex align-items-center" style="height: 40px; font-size: 13px; border-radius: 8px; padding: 0 14px;">
                        <i class="fe fe-log-out me-1"></i> Keluar
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>

            <!-- Search Filter Bar -->
            <div class="row mt-4 pt-3 border-top align-items-center justify-content-between g-2">
                <div class="col-md-6 col-lg-5">
                    <div class="store-search-box">
                        <i class="fe fe-search search-icon"></i>
                        <input type="text" id="storeSearchInput" class="form-control" placeholder="Cari nama atau alamat cabang..." autocomplete="off">
                    </div>
                </div>
                <div class="col-auto text-muted small">
                    Menampilkan <strong id="visibleStoreCount">{{ count($data) }}</strong> cabang outlet
                </div>
            </div>
        </div>

        <!-- Store Grid -->
        <div class="row g-4" id="storeCardGrid">
            @forelse($data as $d)
            <div class="col-md-6 col-lg-4 store-grid-item" data-name="{{ strtolower($d->name) }}" data-address="{{ strtolower($d->address ?? '') }}">
                <div class="card store-card h-100">
                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 px-4">
                        <span class="badge-enterprise">
                            <i class="fe fe-check-circle"></i> Outlet Aktif
                        </span>
                        <span class="badge bg-light text-muted fw-semibold border">ID #{{ $d->id }}</span>
                    </div>
                    <div class="card-body text-center px-4 py-3">
                        <div class="avatar avatar-xxl rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 68px; height: 68px; background-color: var(--poshub-primary-light); color: var(--poshub-primary); box-shadow: 0 4px 10px rgba(30, 64, 175, 0.12);">
                            <i class="fe fe-home" style="font-size: 28px;"></i>
                        </div>
                        <h3 class="h5 fw-bold mb-2 text-dark">{{ $d->name }}</h3>
                        <p class="text-muted small mb-1" style="min-height: 38px; line-height: 1.4;">
                            <i class="fe fe-map-pin me-1 text-secondary"></i> {{ $d->address ?: 'Alamat operasional gerai/cabang belum diisi' }}
                        </p>
                        @if(!empty($d->phone))
                        <p class="text-muted small mb-0">
                            <i class="fe fe-phone me-1 text-secondary"></i> {{ $d->phone }}
                        </p>
                        @endif
                    </div>
                    <div class="card-footer bg-transparent border-0 text-center pb-4 px-4 mt-auto">
                        <a href="{{ route('choose.store', $d->id) }}" class="btn btn-choose-store">
                            <i class="fe fe-log-in me-1"></i> Buka Dashboard Cabang &rarr;
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="p-5 bg-white rounded-3 border">
                    <i class="fe fe-alert-circle text-warning mb-3" style="font-size: 48px;"></i>
                    <h4 class="fw-bold">Belum Ada Cabang Toko</h4>
                    <p class="text-muted mb-4">Anda belum memiliki gerai/cabang toko yang terdaftar. Silakan buat cabang pertama Anda sekarang.</p>
                    <a href="{{ route('store.create') }}" class="btn btn-auth-primary px-4 d-inline-flex align-items-center" style="width: auto;">
                        <i class="fe fe-plus me-1"></i> Buat Toko Baru Sekarang
                    </a>
                </div>
            </div>
            @endforelse
        </div>

        <!-- No Results Fallback -->
        <div id="noStoreFound" class="col-12 text-center py-5 d-none">
            <div class="p-4 bg-white rounded-3 border text-center">
                <i class="fe fe-search text-muted mb-2" style="font-size: 32px;"></i>
                <h5 class="fw-bold text-dark">Cabang tidak ditemukan</h5>
                <p class="text-muted small mb-0">Tidak ada cabang toko yang sesuai dengan kata kunci pencarian Anda.</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        var searchInput = document.getElementById("storeSearchInput");
        var items = document.querySelectorAll(".store-grid-item");
        var countEl = document.getElementById("visibleStoreCount");
        var noFoundEl = document.getElementById("noStoreFound");

        if (searchInput) {
            searchInput.addEventListener("input", function(e) {
                var query = e.target.value.toLowerCase().trim();
                var visibleCount = 0;

                items.forEach(function(item) {
                    var name = item.getAttribute("data-name") || "";
                    var addr = item.getAttribute("data-address") || "";
                    if (name.includes(query) || addr.includes(query)) {
                        item.classList.remove("d-none");
                        visibleCount++;
                    } else {
                        item.classList.add("d-none");
                    }
                });

                if (countEl) countEl.textContent = visibleCount;
                if (noFoundEl) {
                    if (visibleCount === 0 && items.length > 0) {
                        noFoundEl.classList.remove("d-none");
                    } else {
                        noFoundEl.classList.add("d-none");
                    }
                }
            });
        }
    });
</script>
@endsection