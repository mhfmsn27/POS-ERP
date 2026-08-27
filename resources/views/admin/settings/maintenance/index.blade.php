@extends('layouts.admin')

@section('content')
<div id="content-page" class="content-page">
    <div class="container-fluid">
        <div class="row">
            <!-- Ringkasan Server -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="iq-card shadow-sm h-100">
                    <div class="iq-card-header d-flex justify-content-between">
                        <h5 class="card-title"><i class="ri-server-line text-primary me-2"></i> Status Server</h5>
                    </div>
                    <div class="iq-card-body">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Aplikasi:</span>
                                <strong class="text-primary">{{ $metrics['app_name'] }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>PHP Version:</span>
                                <strong>{{ $metrics['php_version'] }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Database Engine:</span>
                                <strong>{{ $metrics['database_engine'] }}</strong>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Memori Terpakai:</span>
                                <span class="badge badge-success">{{ $metrics['memory_usage_mb'] }} MB</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between">
                                <span>Sistem Operasi:</span>
                                <small class="text-muted">{{ $metrics['server_os'] }}</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Kapasitas Harddisk / Storage -->
            <div class="col-lg-4 col-md-6 mb-3">
                <div class="iq-card shadow-sm h-100">
                    <div class="iq-card-header d-flex justify-content-between">
                        <h5 class="card-title"><i class="ri-hard-drive-2-line text-info me-2"></i> Kapasitas Ruang Disk</h5>
                    </div>
                    <div class="iq-card-body text-center">
                        <div class="mb-3">
                            <h2 class="text-primary font-weight-bold">{{ $metrics['storage']['free_gb'] }} GB</h2>
                            <p class="text-muted mb-0">Ruang Kosong Tersedia ({{ $metrics['storage']['free_percent'] }}%)</p>
                        </div>
                        <div class="progress" style="height: 15px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: {{ $metrics['storage']['free_percent'] }}%;" aria-valuenow="{{ $metrics['storage']['free_percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                        <small class="text-muted mt-2 d-block">Total Kapasitas: {{ $metrics['storage']['total_gb'] }} GB</small>
                    </div>
                </div>
            </div>

            <!-- Status Ekstensi PHP -->
            <div class="col-lg-4 col-md-12 mb-3">
                <div class="iq-card shadow-sm h-100">
                    <div class="iq-card-header d-flex justify-content-between">
                        <h5 class="card-title"><i class="ri-shield-check-line text-success me-2"></i> Ekstensi PHP Krusial</h5>
                    </div>
                    <div class="iq-card-body">
                        <div class="row">
                            @foreach($metrics['extensions'] as $ext => $loaded)
                            <div class="col-6 mb-2">
                                <div class="p-2 border rounded d-flex justify-content-between align-items-center">
                                    <span class="small font-weight-bold">{{ strtoupper($ext) }}</span>
                                    @if($loaded)
                                    <span class="badge badge-success">OK</span>
                                    @else
                                    <span class="badge badge-danger">MISSING</span>
                                    @endif
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aksi Toolkit Pemeliharaan -->
            <div class="col-12">
                <div class="iq-card shadow-sm">
                    <div class="iq-card-header">
                        <h4 class="card-title"><i class="ri-tools-line text-warning me-2"></i> Toolkit Perawatan & Akselerasi Sistem</h4>
                        <p class="text-muted mb-0 small">Jalankan pembersihan cache dan pemadatan basis data untuk menjaga aplikasi tetap cepat dan responsif.</p>
                    </div>
                    <div class="iq-card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="card border p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="ri-brush-line text-danger display-5 me-3"></i>
                                        <div>
                                            <h5 class="mb-1">1-Click Bersihkan Cache Aplikasi</h5>
                                            <p class="text-muted small mb-0">Membersihkan cache views Blade, cache route, cache data, dan file log usang.</p>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-danger btn-block mt-2" id="btnClearCache" onclick="runClearCache()">
                                        <i class="ri-delete-bin-line"></i> Bersihkan Seluruh Cache Sekarang
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="card border p-3">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="ri-speed-up-line text-primary display-5 me-3"></i>
                                        <div>
                                            <h5 class="mb-1">1-Click Optimasi Basis Data</h5>
                                            <p class="text-muted small mb-0">Menata ulang indeks dan memadatkan tabel transaksi besar agar query laporan secepat kilat.</p>
                                        </div>
                                    </div>
                                    <button type="button" class="btn btn-outline-primary btn-block mt-2" id="btnOptimizeDb" onclick="runOptimizeDb()">
                                        <i class="ri-flashlight-line"></i> Optimasi Tabel Database Sekarang
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function runClearCache() {
    if (!confirm('Apakah Anda ingin membersihkan seluruh cache aplikasi sekarang?')) return;

    const btn = document.getElementById('btnClearCache');
    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line spin"></i> Sedang Membersihkan...';

    fetch('{{ url("/api/system/maintenance/clear-cache") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-delete-bin-line"></i> Bersihkan Seluruh Cache Sekarang';
        if (res.status) {
            alert('Sukses: ' + res.message);
        } else {
            alert('Gagal: ' + res.message);
        }
    })
    .catch(e => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-delete-bin-line"></i> Bersihkan Seluruh Cache Sekarang';
        alert('Gagal menghubungi server.');
    });
}

function runOptimizeDb() {
    if (!confirm('Apakah Anda ingin menjalankan optimasi tabel database sekarang?')) return;

    const btn = document.getElementById('btnOptimizeDb');
    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line spin"></i> Sedang Mengoptimasi...';

    fetch('{{ url("/api/system/maintenance/optimize-db") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-flashlight-line"></i> Optimasi Tabel Database Sekarang';
        if (res.status) {
            alert('Sukses: ' + res.message + '\n(' + res.tables_optimized.join(', ') + ')');
        } else {
            alert('Gagal: ' + res.message);
        }
    })
    .catch(e => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-flashlight-line"></i> Optimasi Tabel Database Sekarang';
        alert('Gagal menghubungi server.');
    });
}
</script>
@endsection
