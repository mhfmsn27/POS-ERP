<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aktivasi & Lisensi - POSHUB ACCOUNTING</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .license-card {
            background: #ffffff;
            color: #0f172a;
            border-radius: 16px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2);
            max-width: 520px;
            width: 100%;
            padding: 36px;
        }
    </style>
</head>
<body>
    <div class="license-card text-center">
        <!-- Logo -->
        <div class="mb-4">
            <img src="{{ asset('images/logo.png') }}" style="max-height: 52px; width: auto;" alt="POSHUB Logo">
        </div>

        <!-- Lock Icon Badge -->
        <div class="d-inline-flex p-3 bg-danger-subtle text-danger rounded-circle mb-3">
            <i class="fa fa-shield-halved fa-3x"></i>
        </div>

        <h4 class="fw-bold mb-1" style="letter-spacing: -0.5px;">Domain Belum Terlisensi</h4>
        <p class="text-secondary small mb-4">
            Akses ke sistem <strong>POSHUB ACCOUNTING</strong> pada domain ini belum terdaftar dalam sistem lisensi resmi enterprise.
        </p>

        <!-- Domain Diagnostics Box -->
        <div class="bg-light p-3 rounded-3 text-start mb-4 border">
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary small">Domain Terdeteksi:</span>
                <strong class="text-primary font-monospace">{{ $domain }}</strong>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-secondary small">Status Verifikasi:</span>
                <span class="badge bg-danger">Tidak Terdaftar</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="text-secondary small">Waktu Pengecekan:</span>
                <span class="text-dark small">{{ date('d M Y H:i:s') }}</span>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-grid gap-2 mb-3">
            <button id="btnRefresh" onclick="refreshLicense()" class="btn btn-primary py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                <i class="fa fa-rotate"></i> Cek Ulang Status Lisensi
            </button>
            <a href="https://wa.me/6281234567890?text=Halo%20Admin%20POSHUB,%20saya%20ingin%20mendaftarkan%20lisensi%20domain:%20{{ $domain }}" target="_blank" class="btn btn-outline-secondary py-2 fw-semibold d-flex align-items-center justify-content-center gap-2">
                <i class="fab fa-whatsapp text-success"></i> Hubungi Dukungan Lisensi
            </a>
        </div>

        <div class="text-center pt-3 border-top">
            <small class="text-muted">POSHUB Enterprise License Security System</small>
        </div>
    </div>

    <script>
        async function refreshLicense() {
            const btn = document.getElementById('btnRefresh');
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Memvalidasi ke server...';
            btn.disabled = true;

            try {
                const res = await fetch('/api/license/refresh', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' }
                });
                const data = await res.json();

                if (data.success) {
                    alert('Selamat! Lisensi domain Anda telah aktif. Halaman akan dialihkan.');
                    window.location.href = '/';
                } else {
                    alert('Domain ' + (data.data?.domain || '') + ' masih belum terdaftar di Google Sheet lisensi.');
                }
            } catch (e) {
                alert('Gagal menghubungi server lisensi. Silakan periksa koneksi internet Anda.');
            } finally {
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            }
        }
    </script>
</body>
</html>
