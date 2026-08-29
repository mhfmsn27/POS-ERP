@extends('layouts.rma')

@section('content')
<div class="container py-4 my-auto">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-9">
            <div class="poshub-glass-card p-4 p-md-5 shadow-lg">
                <!-- Header Toko & Identitas RMA -->
                <div class="d-flex flex-wrap justify-content-between align-items-center pb-4 mb-4 border-bottom gap-3">
                    <div>
                        <img src="{{ asset('images/logo.png') }}" style="max-height: 48px; width: auto;" class="mb-2" alt="POSHUB Logo" />
                        <h4 class="fw-bold mb-0 text-dark">{{ $transaction->store->name ?? 'POSHUB SERVICE CENTER' }}</h4>
                        <small class="text-muted"><i class="fa fa-map-marker-alt me-1"></i>{{ $transaction->store->address ?? 'Indonesia' }}</small>
                    </div>
                    <div class="text-md-end">
                        <span class="badge bg-primary px-3 py-2 rounded-pill fs-6 mb-1">No. Ref: {{ $transaction->ref_no }}</span>
                        <div class="small text-muted"><i class="fa fa-calendar me-1"></i>Tanggal: {{ $transaction->created_at->format('d M Y') }}</div>
                    </div>
                </div>

                <!-- Customer & Estimate Grid -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded-3 h-100 border">
                            <h6 class="fw-bold text-secondary text-uppercase small mb-2"><i class="fa fa-user me-1 text-primary"></i>Informasi Pelanggan</h6>
                            <div class="fw-bold text-dark">{{ $transaction->customer_name ?? ($transaction->customer->name ?? 'Pelanggan Umum') }}</div>
                            <div class="small text-muted"><i class="fa fa-phone me-1"></i>{{ $transaction->phone ?? ($transaction->customer->phone ?? '-') }}</div>
                            <div class="small text-muted"><i class="fa fa-map-pin me-1"></i>{{ $transaction->customer->address ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="bg-light p-3 rounded-3 h-100 border">
                            <h6 class="fw-bold text-secondary text-uppercase small mb-2"><i class="fa fa-clock me-1 text-primary"></i>Estimasi & Biaya</h6>
                            <div class="small text-muted">Estimasi Selesai: <strong class="text-dark">{{ substr($transaction->estimate_date, 0, 10) ?: '-' }}</strong></div>
                            <div class="small text-muted">Estimasi Biaya: <strong class="text-success fs-6">Rp {{ number_format($transaction->estimate_price) }}</strong></div>
                        </div>
                    </div>
                </div>

                <!-- Items & Complaints Table -->
                <div class="mb-4">
                    <h6 class="fw-bold mb-3"><i class="fa fa-tools text-primary me-2"></i>Daftar Unit Diservis</h6>
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Nama Unit / Barang</th>
                                    <th>Keluhan Kerusakan</th>
                                    <th>Kelengkapan</th>
                                    <th class="text-center">Status Pengerjaan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($transaction->details as $detail)
                                <tr>
                                    <td class="fw-bold text-dark">{{ $detail->product_name }}</td>
                                    <td class="text-secondary small">{{ $detail->complaint }}</td>
                                    <td class="text-secondary small">{{ $detail->completeness }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm" type="button" data-toggle="modal" data-target="#track{{ $detail->id }}">
                                            <i class="fa fa-history me-1"></i> Lihat Progress
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                @if(!empty($transaction->note))
                <div class="p-3 bg-warning-subtle text-warning-emphasis border border-warning rounded-3 mb-4">
                    <strong class="d-block mb-1"><i class="fa fa-info-circle me-1"></i>Catatan Teknisi:</strong>
                    <span class="small">{{ $transaction->note }}</span>
                </div>
                @endif

                <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                    <a href="{{ route('rma') }}" class="btn btn-outline-secondary btn-sm px-3 rounded-pill">
                        <i class="fa fa-arrow-left me-1"></i> Kembali ke Pencarian
                    </a>
                    <small class="text-muted">POSHUB RMA Live Tracker</small>
                </div>
            </div>
        </div>
    </div>
</div>

@foreach ($transaction->details as $detail)
<div class="modal fade" id="track{{ $detail->id }}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-bottom px-4 py-3">
                <h5 class="modal-title fw-bold">Riwayat Progress: {{ $detail->product_name }}</h5>
                <button type="button" class="close btn" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="timeline">
                    @forelse ($detail->record as $record)
                    <div class="d-flex gap-3 mb-3">
                        <div class="text-center">
                            <span class="badge bg-primary rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                <i class="fa fa-check text-white"></i>
                            </span>
                        </div>
                        <div class="bg-light p-3 rounded-3 flex-grow-1 border">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <h6 class="fw-bold mb-0 text-primary">{{ $record->status_name }}</h6>
                                <small class="text-muted">{{ $record->created_at->format('d/m/Y H:i') }}</small>
                            </div>
                            <p class="mb-0 text-secondary small">{{ $record->subject }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted">
                        <i class="fa fa-clock fa-2x mb-2 text-secondary opacity-50"></i>
                        <p class="mb-0">Belum ada pembaruan log progress teknisi.</p>
                    </div>
                    @endforelse
                </div>
            </div>
            <div class="modal-footer border-top px-4 py-2">
                <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill" data-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection