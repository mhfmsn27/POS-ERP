<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Display - POSHUB ACCOUNTING</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/poshub-modern-ui.css') }}">
    <style>
        body {
            font-family: 'Outfit', 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #f8fafc;
            min-height: 100vh;
            overflow-x: hidden;
        }
        .display-card {
            background: rgba(30, 41, 59, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }
        .item-row {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.2s ease;
        }
        .item-row:last-child {
            border-bottom: none;
        }
        .total-banner {
            background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            border-radius: 16px;
        }
        .change-banner {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            border-radius: 16px;
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
<body class="p-3 p-md-4">
    <div class="container-fluid h-100">
        <!-- Top Branding Header -->
        <header class="d-flex justify-content-between align-items-center mb-4 px-3 py-2 display-card">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-primary text-white p-2 rounded-3">
                    <i class="fa fa-cash-register fa-2x"></i>
                </div>
                <div>
                    <h4 class="mb-0 fw-bold text-white tracking-wide">POSHUB ACCOUNTING</h4>
                    <span class="text-white-50 small">Customer Facing Display Screen</span>
                </div>
            </div>
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-success-subtle text-success px-3 py-2 rounded-pill fw-semibold">
                    <i class="fa fa-circle text-success me-1" style="font-size: 8px;"></i> Live Connected
                </span>
                <span id="liveClock" class="fw-bold text-white-50 ms-2">--:--:--</span>
            </div>
        </header>

        <div class="row g-4">
            <!-- Left Side: Active Cart Items -->
            <div class="col-lg-7">
                <div class="display-card p-4 h-100 d-flex flex-column" style="min-height: 68vh;">
                    <div class="d-flex justify-content-between align-items-center pb-3 border-bottom border-secondary">
                        <h5 class="mb-0 fw-bold text-white">
                            <i class="fa fa-shopping-basket text-primary me-2"></i>Daftar Belanja Anda
                        </h5>
                        <span id="itemCountBadge" class="badge bg-primary px-3 py-2 rounded-pill">0 Item</span>
                    </div>

                    <!-- Items Container -->
                    <div id="cartItemsContainer" class="flex-grow-1 overflow-auto py-2" style="max-height: 52vh;">
                        <div id="idleState" class="text-center py-5">
                            <div class="idle-pulse my-4">
                                <i class="fa fa-store fa-4x text-primary opacity-50 mb-3"></i>
                                <h3 class="fw-bold text-white mb-2">Selamat Datang!</h3>
                                <p class="text-white-50">Silakan letakkan barang belanjaan Anda di meja kasir.</p>
                            </div>
                        </div>
                        <div id="itemsList" class="d-none"></div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Totals & Promos -->
            <div class="col-lg-5">
                <div class="display-card p-4 h-100 d-flex flex-column justify-content-between" style="min-height: 68vh;">
                    <!-- Price Breakdown -->
                    <div>
                        <div class="d-flex justify-content-between py-2 border-bottom border-secondary text-white-50">
                            <span>Subtotal</span>
                            <span id="subtotalVal" class="fw-bold text-white">Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom border-secondary text-white-50">
                            <span>Diskon / Potongan</span>
                            <span id="discountVal" class="fw-bold text-warning">- Rp 0</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom border-secondary text-white-50">
                            <span>Pajak (PPN)</span>
                            <span id="taxVal" class="fw-bold text-white">Rp 0</span>
                        </div>
                    </div>

                    <!-- Grand Total Banner -->
                    <div class="my-4">
                        <div class="total-banner p-4 shadow-lg text-center">
                            <span class="text-uppercase fw-bold text-white-50 small tracking-wider">TOTAL TAGIHAN</span>
                            <h1 id="grandTotalVal" class="display-4 fw-extrabold text-white mb-0 mt-1">Rp 0</h1>
                        </div>

                        <div id="paymentResultCard" class="change-banner p-3 mt-3 shadow text-center d-none">
                            <span class="text-uppercase fw-bold text-white-50 small">UANG KEMBALIAN</span>
                            <h2 id="changeVal" class="fw-bold text-white mb-0 mt-1">Rp 0</h2>
                        </div>
                    </div>

                    <!-- Promo Footer -->
                    <div class="text-center p-3 bg-dark bg-opacity-50 rounded-3 border border-secondary">
                        <small class="text-white-50 d-block">
                            <i class="fa fa-shield-alt text-success me-1"></i> Transaksi Aman & Terverifikasi Otomatis
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Clock
        function updateClock() {
            const now = new Date();
            document.getElementById('liveClock').textContent = now.toLocaleTimeString('id-ID');
        }
        setInterval(updateClock, 1000);
        updateClock();

        function formatRupiah(num) {
            return 'Rp ' + Number(num || 0).toLocaleString('id-ID');
        }

        // Local Dual-Monitor Broadcast Channel + Polling
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
                    <div class="item-row py-3 d-flex justify-content-between align-items-center">
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
        }

        setInterval(fetchDisplayState, 2000);
        fetchDisplayState();
    </script>
</body>
</html>
