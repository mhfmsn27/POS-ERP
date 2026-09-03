@extends('layouts.welcome')

@section('styles') 
<link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables/datatables.min.css') }}">
<link rel="stylesheet" href="{{ asset('assets/vendors/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.min.css') }}">
<style>
    .trans-page-wrapper {
        min-height: calc(100vh - 120px);
        padding: 30px 16px;
    }
</style>
@endsection

@section('content')
<div class="trans-page-wrapper container">
    <div class="row justify-content-center"> 
        <div class="col-12">
            <div class="card shadow-sm border" style="border-radius: var(--poshub-card-radius); overflow: hidden;">
                <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <span class="auth-brand-badge mb-1">Riwayat Langganan</span>
                        <h4 class="fw-bold text-dark mb-0">Daftar Histori Transaksi Paket</h4>
                    </div>
                    <div>
                        <a href="{{ route('store.choose') }}" class="btn btn-auth-secondary" style="height: 38px; font-size: 13px;">
                            <i class="fe fe-home me-1"></i> Kembali ke Toko
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="table-1">
                            <thead class="table-light">
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Toko / Cabang</th>
                                    <th>Paket</th>
                                    <th>Grand Total</th>
                                    <th>Status Pembayaran</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transactions as $transaction)
                                <tr>
                                    <td class="small fw-semibold text-muted">
                                        <i class="fe fe-calendar me-1"></i> {{ $transaction->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="fw-bold text-dark">
                                        {{ $transaction->store->name ?? '-' }}
                                    </td>
                                    <td>
                                        <span class="badge bg-primary-light text-primary fw-bold">{{ $transaction->package->name ?? '-' }}</span>
                                    </td>
                                    <td class="fw-bold text-dark">
                                        Rp {{ number_format($transaction->grand_total) }}
                                    </td>
                                    <td>
                                        @if($transaction->status == 'pending')
                                        <span class="badge bg-warning text-dark fw-bold"><i class="fe fe-clock me-1"></i> Menunggu Pembayaran</span>
                                        @elseif($transaction->status == 'process')
                                        <span class="badge bg-info text-white fw-bold"><i class="fe fe-loader me-1"></i> Diproses</span>
                                        @else
                                        <span class="badge bg-success text-white fw-bold"><i class="fe fe-check-circle me-1"></i> Selesai &amp; Aktif</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if($transaction->status == 'pending')
                                        <div class="d-inline-flex gap-1">
                                            <a href="javascript:void(0);" onclick="payTransaction({{ $transaction->id }})" class="btn btn-sm btn-primary" title="Bayar Sekarang">
                                                <i class="fe fe-credit-card me-1"></i> Bayar
                                            </a>
                                            <a href="{{ route('package.order.delete', $transaction->id) }}" class="btn btn-sm btn-outline-danger deletebutton" title="Batalkan Tagihan">
                                                <i class="fe fe-trash"></i>
                                            </a>
                                        </div>
                                        @else
                                        <span class="text-muted small"><i class="fe fe-check text-success"></i> Terbayar</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('assets/vendors/datatables/datatables.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.min.js') }}"></script>
<script src="{{ asset('assets/vendors/datatables/datatables.js') }}"></script>
<script src="https://app.midtrans.com/snap/snap.js" data-client-key="{{ $settings->midtrans_client }}"></script>

<script>
    function payTransaction(id) {
        setTimeout(function() {
            $.ajax({
                url: '/pos-admin/transaction-package/add-payment/' + id,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    timeout: 0,
                },
                data: '',
                success: function(data) {
                    if (data.status == false) {
                        Swal.fire({
                            title: 'Error',
                            html: data.message,
                            icon: 'error',
                            confirmButtonText: 'Ok, Saya Mengerti',
                        });
                        return false;
                    } else {
                        snap.pay(data.snap, {
                            onSuccess: function(result) { location.reload(); },
                            onPending: function(result) { location.reload(); },
                            onError: function(result) { location.reload(); },
                            onClose: function(result) { location.reload(); },
                        });
                    }
                },
                cache: false,
                contentType: false,
                processData: false,
            });
        }, 130);
    }
</script>
@endsection