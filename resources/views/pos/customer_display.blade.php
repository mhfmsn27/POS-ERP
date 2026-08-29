<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Customer Display - POSHUB ACCOUNTING</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
    <style>
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #0f172a;
            color: #f8fafc;
            min-height: 100vh;
            min-height: -webkit-fill-available;
            padding: clamp(10px, 2vw, 24px);
            overflow-x: hidden;
        }

        .cds-card {
            background: #1e293b;
            border: 1px solid #334155;
            border-radius: 14px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
        }

        .cds-item-row {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.2s ease;
        }
        .cds-item-row:last-child {
            border-bottom: none;
        }

        .cds-total-banner {
            background: #1e40af;
            border: 1px solid #3b82f6;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(30, 64, 175, 0.3);
        }

        .cds-change-banner {
            background: #065f46;
            border: 1px solid #10b981;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(6, 95, 70, 0.3);
        }

        .cds-qris-box {
            background: #ffffff;
            border-radius: 12px;
            padding: 16px;
            color: #0f172a;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .idle-pulse {
            animation: pulseGlow 2.5s infinite;
        }
        @keyframes pulseGlow {
            0% { transform: scale(1); opacity: 0.9; }
            50% { transform: scale(1.02); opacity: 1; }
            100% { transform: scale(1); opacity: 0.9; }
        }
    </style>
</head>
<body>
    <div class="container-fluid h-100 p-0">
        <!-- Top Branding Header -->
        <header class="d-flex justify-content-between align-items-center mb-3 px-3 py-2 cds-card">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary text-white p-2 rounded-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                    <i class="fa fa-cash-register fa-lg"></i>
                </div>
                <div>
                    <h5 class="mb-0 fw-bold text-white tracking-wide">POSHUB ACCOUNTING</h5>
                    <span class="text-white-50 small">Customer Facing Display</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold shadow-sm">
                    <i class="fa fa-circle text-success me-1" style="font-size: 8px;"></i> Live Connected
                </span>
                <span id="liveClock" class="fw-bold text-white-50 ms-2 d-none d-sm-inline">--:--:--</span>
            </div>
        </header>

        <div class="row g-3 g-lg-4">
            <!-- Left Side: Active Cart Items -->
            <div class="col-lg-7">
                <div class="cds-card p-3 p-md-4 h-100 d-flex flex-column" style="min-height: 65vh;">
                    <div class="d-flex justify-content-between align-items-center pb-3 border-bottom border-secondary">
                        <h6 class="mb-0 fw-bold text-white fs-5">
                            <i class="fa fa-shopping-basket text-primary me-2"></i>Daftar Belanja Anda
                        </h6>
                        <span id="itemCountBadge" class="badge bg-primary px-3 py-2 rounded-pill fw-bold">0 Item</span>
                    </div>

                    <!-- Items Container -->
                    <div id="cartItemsContainer" class="flex-grow-1 overflow-auto py-2" style="max-height: 52vh;">
                        <div id="idleState" class="text-center py-5">
                            <div class="idle-pulse my-4">
                                <i class="fa fa-store fa-4x text-primary opacity-50 mb-3"></i>
                                <h3 class="fw-bold text-white mb-2 cds-fluid-title">Selamat Datang!</h3>
                                <p class="text-white-50">Silakan letakkan barang belanjaan Anda di meja kasir.</p>
                            </div>
                        </div>
                        <div id="itemsList" class="d-none"></div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Totals, QRIS & Result -->
            <div class="col-lg-5">
                <div class="cds-card p-3 p-md-4 h-100 d-flex flex-column justify-content-between" style="min-height: 65vh;">
                    <!-- Price Breakdown -->
                    <div class="bg-black bg-opacity-30 p-3 rounded-3 border border-secondary mb-3">
                        <div class="d-flex justify-content-between py-1 border-bottom border-secondary text-white-50 small">
                            <span>Subtotal</span>
                            <span id="subtotalVal" class="fw-bold text-white">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 border-bottom border-secondary text-white-50 small">
                            <span>Diskon / Potongan</span>
                            <span id="discountVal" class="fw-bold text-warning">- Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between py-1 text-white-50 small">
                            <span>Pajak (PPN)</span>
                            <span id="taxVal" class="fw-bold text-white">Rp 0</span>
                        </div>
                    </div>

                    <!-- Grand Total Banner -->
                    <div>
                        <div class="cds-total-banner p-3 p-md-4 text-center">
                            <span class="text-uppercase fw-bold text-white-50 small tracking-wider">TOTAL TAGIHAN</span>
                            <h1 id="grandTotalVal" class="cds-fluid-total fw-extrabold text-white mb-0 mt-1">Rp 0</h1>
                        </div>

                        <!-- Change / Result Banner -->
                        <div id="paymentResultCard" class="cds-change-banner p-3 mt-3 text-center d-none">
                            <span class="text-uppercase fw-bold text-white-50 small">UANG KEMBALIAN</span>
                            <h2 id="changeVal" class="fw-bold text-white mb-0 mt-1">Rp 0</h2>
                        </div>

                        <!-- Dynamic QRIS Scan Box (Optional Trigger) -->
                        <div id="qrisPaymentBox" class="cds-qris-box text-center mt-3 d-none">
                            <h6 class="fw-bold mb-1"><i class="fa fa-qrcode text-primary me-1"></i> Scan QRIS Dinamis</h6>
                            <small class="text-muted d-block mb-2">BCA, Mandiri, GoPay, OVO, ShopeePay, Dana</small>
                            <img src="{{ asset('images/logo.png') }}" style="max-height: 110px; max-width: 110px;" class="img-thumbnail my-1" alt="QRIS Code" />
                        </div>
                    </div>

                    <!-- Trust Badge Footer -->
                    <div class="text-center p-2 mt-3 bg-dark bg-opacity-40 rounded-3 border border-secondary">
                        <small class="text-white-50" style="font-size: 11px;">
                            <i class="fa fa-shield-check text-success me-1"></i> Transaksi Resmi & Terverifikasi <strong>POSHUB</strong>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Live Clock
        function updateClock() {
            const now = new Date();
            const el = document.getElementById('liveClock');
            if (el) el.textContent = now.toLocaleTimeString('id-ID');
        }
        setInterval(updateClock, 1000);
        updateClock();

        function formatRupiah(num) {
            return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
        }

        // Local Dual-Monitor Broadcast Channel + Polling Fallback
        const channel = new BroadcastChannel('poshub_customer_display');
        channel.onmessage = (event) => {
            if (event.data) renderState(event.data);
        };

        async function fetchDisplayState() {
            try {
                const res = await fetch('/api/customer-display-state/CDS-STORE-1');
                if (res.ok) {
                    const data = await res.json();
                    renderState(data);
                }
            } catch (e) {}
        }

        function renderState(data) {
            const items = data.items || [];
            const idle = document.getElementById('idleState');
            const list = document.getElementById('itemsList');
            const badge = document.getElementById('itemCountBadge');

            if (items.length === 0) {
                idle.classList.remove('d-none');
                list.classList.add('d-none');
                badge.textContent = '0 Item';
            } else {
                idle.classList.add('d-none');
                list.classList.remove('d-none');
                badge.textContent = items.length + ' Item';

                list.innerHTML = items.map(item => `
                    <div class="cds-item-row py-3 d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1 fw-bold text-white">${item.name || 'Produk'}</h6>
                            <small class="text-white-50">${item.qty || 1}x @ ${formatRupiah(item.unit_price || item.selling_price || 0)}</small>
                        </div>
                        <div class="text-end">
                            <span class="fw-bold text-primary fs-5">${formatRupiah(item.subtotal || ((item.qty||1)*(item.unit_price||0)))}</span>
                        </div>
                    </div>
                `).join('');
            }

            document.getElementById('subtotalVal').textContent = formatRupiah(data.subtotal || 0);
            document.getElementById('discountVal').textContent = '- ' + formatRupiah(data.discount_total || 0);
            document.getElementById('taxVal').textContent = formatRupiah(data.tax_total || 0);
            document.getElementById('grandTotalVal').textContent = formatRupiah(data.grand_total || 0);

            const paymentCard = document.getElementById('paymentResultCard');
            if (data.change_amount > 0 || data.pay_amount > 0) {
                paymentCard.classList.remove('d-none');
                document.getElementById('changeVal').textContent = formatRupiah(data.change_amount || 0);
            } else {
                paymentCard.classList.add('d-none');
            }

            const qrisBox = document.getElementById('qrisPaymentBox');
            if (data.payment_method === 'qris' && (data.grand_total > 0)) {
                qrisBox.classList.remove('d-none');
            } else {
                qrisBox.classList.add('d-none');
            }
        }

        setInterval(fetchDisplayState, 2000);
        fetchDisplayState();
    </script>
</body>
</html>
