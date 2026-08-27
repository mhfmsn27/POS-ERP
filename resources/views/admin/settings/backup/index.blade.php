@extends('layouts.admin')

@section('content')
<div id="content-page" class="content-page">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="iq-card shadow-sm">
                    <div class="iq-card-header d-flex justify-content-between align-items-center">
                        <div class="iq-header-title">
                            <h4 class="card-title"><i class="ri-database-2-line text-primary me-2"></i> Pencadangan Basis Data (Database Backup)</h4>
                            <p class="text-muted mb-0 small">Cadangkan seluruh data transaksi, keuangan, stok, dan akun secara aman dalam format SQL.</p>
                        </div>
                        <button type="button" class="btn btn-primary" id="btnCreateBackup" onclick="triggerBackup()">
                            <i class="ri-add-line"></i> Buat Snapshot Backup Sekarang
                        </button>
                    </div>
                    <div class="iq-card-body">
                        <div id="backupAlertArea"></div>

                        <div class="table-responsive">
                            <table class="table table-striped table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nama Berkas (.sql)</th>
                                        <th>Ukuran File</th>
                                        <th>Waktu Pembuatan</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody id="backupTableBody">
                                    @forelse($backups as $index => $b)
                                    <tr id="row-{{ $loop->index }}">
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <i class="ri-file-code-line text-primary me-1"></i>
                                            <strong>{{ $b['filename'] }}</strong>
                                        </td>
                                        <td><span class="badge badge-info">{{ $b['size'] }}</span></td>
                                        <td>{{ $b['created_at'] }}</td>
                                        <td class="text-center">
                                            <a href="{{ url('/api/system/backups/download/' . $b['filename']) }}" class="btn btn-sm btn-success me-1" title="Unduh File">
                                                <i class="ri-download-2-line"></i> Unduh
                                            </a>
                                            <button type="button" class="btn btn-sm btn-danger" onclick="deleteBackup('{{ $b['filename'] }}')" title="Hapus File">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="ri-inbox-line display-4 d-block mb-2 text-secondary"></i>
                                            Belum ada berkas backup yang dibuat. Klik tombol di atas untuk membuat snapshot pertama.
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function triggerBackup() {
    const btn = document.getElementById('btnCreateBackup');
    btn.disabled = true;
    btn.innerHTML = '<i class="ri-loader-4-line spin"></i> Sedang Mencadangkan...';

    fetch('{{ url("/api/system/backups/create") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-add-line"></i> Buat Snapshot Backup Sekarang';
        if (res.status) {
            alert('Sukses: ' + res.message);
            window.location.reload();
        } else {
            alert('Gagal: ' + (res.message || 'Terjadi kesalahan'));
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ri-add-line"></i> Buat Snapshot Backup Sekarang';
        alert('Gagal menghubungi server.');
    });
}

function deleteBackup(filename) {
    if (!confirm('Apakah Anda yakin ingin menghapus file backup ini?')) return;

    fetch('{{ url("/api/system/backups") }}/' + filename, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(r => r.json())
    .then(res => {
        if (res.status) {
            window.location.reload();
        } else {
            alert('Gagal menghapus file: ' + res.message);
        }
    })
    .catch(e => alert('Gagal menghubungi server.'));
}
</script>
@endsection
