@extends('layouts.welcome')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
<style>
    .order-page-wrapper {
        min-height: calc(100vh - 120px);
        padding: 30px 16px;
    }
    .auth-nav-pills .nav-link {
        border-radius: var(--poshub-btn-radius);
        padding: 10px 18px;
        font-weight: 600;
        font-size: 13.5px;
        color: var(--poshub-slate);
        border: 1px solid var(--poshub-border);
        background: #ffffff;
        transition: var(--poshub-transition);
        text-align: center;
    }
    .auth-nav-pills .nav-link.active {
        background-color: var(--poshub-primary) !important;
        color: #ffffff !important;
        border-color: var(--poshub-primary) !important;
    }
    .package-select-card {
        border-radius: var(--poshub-card-radius);
        border: 1px solid var(--poshub-border);
        background: #ffffff;
        box-shadow: var(--poshub-shadow-sm);
        transition: var(--poshub-transition);
        margin-bottom: 16px;
        overflow: hidden;
    }
    .package-select-card:hover {
        border-color: var(--poshub-primary-border);
        box-shadow: var(--poshub-shadow-md);
    }
    .package-select-card.active-selected {
        border-color: var(--poshub-primary) !important;
        box-shadow: 0 0 0 2px var(--poshub-primary) !important;
    }
</style>
@endsection

@section('content')
<div class="order-page-wrapper container">
    <!-- Navigation Tabs -->
    <div class="card mb-4 border shadow-sm" style="border-radius: var(--poshub-card-radius);">
        <div class="card-body p-3">
            <ul class="nav auth-nav-pills row g-2">
                <li class="nav-item col-md-4">
                    <a class="nav-link w-100" href="{{ route('store.choose') }}">
                        <i class="fe fe-home me-1"></i> 1. Pilih Cabang Toko
                    </a>
                </li>
                <li class="nav-item col-md-4">
                    <a class="nav-link w-100 active" href="javascript:void(0);">
                        <i class="fe fe-box me-1"></i> 2. Pilihan Paket Langganan
                    </a>
                </li>
                <li class="nav-item col-md-4">
                    <a class="nav-link w-100" href="{{ route('package.order') }}">
                        <i class="fe fe-clock me-1"></i> 3. Riwayat Transaksi Paket
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="row g-4">
        <!-- Packages List -->
        <div class="col-lg-8">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="fw-bold text-dark mb-0">Pilih Paket untuk Outlet: {{ $store->name }}</h4>
                <span class="badge-enterprise"><i class="fe fe-check-circle"></i> Outlet #{{ $store->id }}</span>
            </div>

            <x-admin.validation-component></x-admin.validation-component>

            @foreach ($packages as $package)
            <div class="package-select-card card" id="package_{{ $package->id }}">
                <div class="card-body p-4">
                    <div class="d-flex flex-column flex-sm-row align-items-sm-center justify-content-between gap-3">
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h5 class="fw-bold text-dark mb-0" id="packagename">{{ $package->name }}</h5>
                                <span class="badge bg-success-light text-success fw-semibold small">{{ number_format($package->limit_day) }} Hari</span>
                            </div>
                            <input type="hidden" id="packageprice" value="{{ (int)$package->price }}">
                            <input type="hidden" id="idPackage" value="{{ $package->id }}">
                            <h4 class="fw-bold text-primary mb-1 mt-2">Rp {{ number_format($package->price) }}</h4>
                            <p class="text-muted small mb-0">{{ $package->description }}</p>
                        </div>
                        <div class="text-sm-end">
                            <button type="button" onclick="choosePackage({{ $package->id }})" class="btn btn-auth-secondary px-4 packagebutton">
                                <i class="fe fe-check me-1"></i> Pilih Paket
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Order Summary Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm border" style="border-radius: var(--poshub-card-radius); position: sticky; top: 90px;">
                <div class="card-header bg-white border-bottom p-4">
                    <h5 class="fw-bold text-dark mb-0">Ringkasan Pembelian</h5>
                </div>
                <form action="{{ route('package.order.store', $store->id) }}" method="POST" class="card-body p-4">
                    @csrf
                    <div class="d-flex justify-content-between align-items-center mb-2 small">
                        <span class="text-muted">Cabang Toko</span>
                        <span class="fw-bold text-dark">{{ $store->name }}</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 small">
                        <span class="text-muted">Paket Dipilih</span>
                        <span class="fw-bold text-primary packagename">-</span>
                    </div>
                    
                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center mb-2 small">
                        <span class="text-muted">Harga Dasar</span>
                        <span class="fw-semibold text-dark packageprice">Rp 0</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3 small">
                        <span class="text-muted">Pajak ({{ (int)$settings->tax }}%)</span>
                        <span class="text-muted packagetax">Rp 0</span>
                    </div>

                    <hr class="my-3">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <span class="h6 fw-bold text-dark mb-0">Total Tagihan</span>
                        <span class="h5 fw-bold text-primary mb-0 packagetotal">Rp 0</span>
                    </div>

                    <input type="hidden" name="package" value="" id="packageId">
                    <input type="hidden" id="taxrateId" value="{{ (int)$settings->tax }}">
                    
                    <button type="submit" class="btn btn-auth-primary w-100" id="btnSubmitOrder" disabled>
                        <i class="fe fe-shopping-cart me-2"></i> Lanjutkan ke Pembayaran
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function choosePackage(id) {
        var packageCore = $("#package_" + id);
        var packagePricing = parseInt(packageCore.find("#packageprice").val()) || 0;
        var packageName = packageCore.find("#packagename").text();
        var packageId = packageCore.find("#idPackage").val();
        var taxrate = parseInt($("#taxrateId").val()) || 0;
        var taxtotal = taxrate > 0 ? Math.round(taxrate / 100 * packagePricing) : 0;
        var totalPrice = packagePricing + taxtotal;

        $(".package-select-card").removeClass("active-selected");
        packageCore.addClass("active-selected");

        $(".packagebutton").html('<i class="fe fe-check me-1"></i> Pilih Paket').removeClass("btn-primary text-white").addClass("btn-auth-secondary");
        packageCore.find(".packagebutton").html('<i class="fe fe-check-circle me-1"></i> Terpilih').removeClass("btn-auth-secondary").addClass("btn-primary text-white");

        $("#packageId").val(packageId);
        $(".packagename").text(packageName);
        $(".packageprice").text("Rp " + formatRupiah(packagePricing.toString()));
        $(".packagetax").text("Rp " + formatRupiah(taxtotal.toString()));
        $(".packagetotal").text("Rp " + formatRupiah(totalPrice.toString()));

        $("#btnSubmitOrder").prop("disabled", false);
    }

    function formatRupiah(angka) {
        var number_string = angka.replace(/[^.\d]/g, '').toString(),
            split = number_string.split(','),
            sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);

        if (ribuan) {
            var separator = sisa ? '.' : '';
            rupiah += separator + ribuan.join('.');
        }
        return split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
    }
</script>
@endsection