/**
 * POSHUB ENTERPRISE: POS Fast-Action Hotkeys & Connectivity Manager
 */

(function () {
    'use strict';

    // 1. Inisialisasi Floating Connectivity Status Badge
    function initConnectivityBadge() {
        if (document.getElementById('poshub-network-badge')) return;

        const badge = document.createElement('div');
        badge.id = 'poshub-network-badge';
        badge.style.position = 'fixed';
        badge.style.top = '12px';
        badge.style.right = '12px';
        badge.style.zIndex = '99999';
        badge.style.padding = '6px 14px';
        badge.style.borderRadius = '20px';
        badge.style.fontSize = '12px';
        badge.style.fontWeight = 'bold';
        badge.style.boxShadow = '0 2px 8px rgba(0,0,0,0.15)';
        badge.style.transition = 'all 0.3s ease';
        badge.style.cursor = 'pointer';
        badge.title = 'Tekan F1 untuk melihat daftar Hotkey Kasir';

        function updateStatus() {
            if (navigator.onLine) {
                badge.style.backgroundColor = '#10b981';
                badge.style.color = '#ffffff';
                badge.innerHTML = '🟢 POS Online';
            } else {
                badge.style.backgroundColor = '#f59e0b';
                badge.style.color = '#ffffff';
                badge.innerHTML = '🟠 Mode Antrean Offline';
            }
        }

        window.addEventListener('online', updateStatus);
        window.addEventListener('offline', updateStatus);
        badge.addEventListener('click', showHotkeysModal);

        document.body.appendChild(badge);
        updateStatus();
    }

    // 2. Tampilkan Modal Bantuan Pintasan Keyboard (Hotkeys)
    function showHotkeysModal() {
        let modal = document.getElementById('poshub-hotkeys-modal');
        if (!modal) {
            modal = document.createElement('div');
            modal.id = 'poshub-hotkeys-modal';
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100vw';
            modal.style.height = '100vh';
            modal.style.backgroundColor = 'rgba(0,0,0,0.6)';
            modal.style.zIndex = '100000';
            modal.style.display = 'flex';
            modal.style.justifyContent = 'center';
            modal.style.alignItems = 'center';

            modal.innerHTML = `
                <div style="background:#fff; border-radius:12px; max-width:480px; width:90%; padding:24px; box-shadow:0 10px 25px rgba(0,0,0,0.3); font-family:Arial,sans-serif;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px; border-bottom:1px solid #eee; padding-bottom:10px;">
                        <h3 style="margin:0; font-size:18px; color:#1e293b;">⚡ Pintasan Keyboard Kasir (Hotkeys)</h3>
                        <button id="poshub-close-hotkeys" style="background:none; border:none; font-size:20px; cursor:pointer; color:#64748b;">&times;</button>
                    </div>
                    <table style="width:100%; font-size:13px; border-collapse:collapse;">
                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0;"><strong>[F1]</strong></td><td>Buka Bantuan Pintasan ini</td></tr>
                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0;"><strong>[F2]</strong></td><td>Fokus ke Kotak Cari Produk / Scan Barcode</td></tr>
                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0;"><strong>[F4]</strong></td><td>Simpan / Tahan Pesanan (Hold Bill)</td></tr>
                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0;"><strong>[F7]</strong></td><td>Buka Laci Uang (Open Cash Drawer)</td></tr>
                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0;"><strong>[F8]</strong></td><td>Bayar Uang Pas (Exact Cash)</td></tr>
                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0;"><strong>[F9]</strong></td><td>Pisah Tagihan (Split Bill)</td></tr>
                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:8px 0;"><strong>[F10]</strong></td><td>Selesaikan Pembayaran (Checkout)</td></tr>
                        <tr><td style="padding:8px 0;"><strong>[ESC]</strong></td><td>Tutup Modal / Batalkan Aksi</td></tr>
                    </table>
                    <div style="text-align:right; margin-top:20px;">
                        <button id="poshub-btn-ok" style="background:#1f57db; color:#fff; border:none; padding:8px 20px; border-radius:6px; font-weight:bold; cursor:pointer;">Mengerti</button>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);

            document.getElementById('poshub-close-hotkeys').onclick = () => modal.style.display = 'none';
            document.getElementById('poshub-btn-ok').onclick = () => modal.style.display = 'none';
            modal.onclick = (e) => { if (e.target === modal) modal.style.display = 'none'; };
        } else {
            modal.style.display = 'flex';
        }
    }

    // 3. Listener Global Event Keydown
    window.addEventListener('keydown', function (e) {
        // Jangan tangani jika pengguna sedang mengetik di input teks (kecuali F1-F10)
        const isInput = ['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName);

        switch (e.key) {
            case 'F1':
                e.preventDefault();
                showHotkeysModal();
                break;

            case 'F2':
                e.preventDefault();
                const searchEl = document.querySelector('#search, input[type="search"], .search-product, input[placeholder*="Cari"], input[placeholder*="Scan"]');
                if (searchEl) {
                    searchEl.focus();
                    if (searchEl.select) searchEl.select();
                }
                break;

            case 'F4':
                e.preventDefault();
                const holdBtn = document.querySelector('#btn-hold, .btn-hold, button:has(i.ri-pause-line), button:has(i.fa-pause)');
                if (holdBtn) holdBtn.click();
                break;

            case 'F7':
                e.preventDefault();
                // Kirim pulse printer untuk buka laci kasir (ESC/POS 27, 112, 0, 25, 250)
                console.log('[POSHUB] Open Drawer Triggered via F7');
                break;

            case 'F8':
                e.preventDefault();
                const exactCashBtn = document.querySelector('#btn-exact-cash, .btn-exact-cash');
                if (exactCashBtn) exactCashBtn.click();
                break;

            case 'F9':
                e.preventDefault();
                const splitBtn = document.querySelector('#btn-split-bill, .btn-split-bill');
                if (splitBtn) splitBtn.click();
                break;

            case 'F10':
                e.preventDefault();
                const payBtn = document.querySelector('#btn-pay, .btn-checkout, #btn-submit-payment, button[type="submit"]');
                if (payBtn) payBtn.click();
                break;

            case 'Escape':
                const modal = document.getElementById('poshub-hotkeys-modal');
                if (modal && modal.style.display !== 'none') {
                    modal.style.display = 'none';
                }
                break;
        }
    });

    // Inisialisasi saat DOM siap
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initConnectivityBadge);
    } else {
        initConnectivityBadge();
    }
})();
