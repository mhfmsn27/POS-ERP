/**
 * POSHUB ENTERPRISE: POS Fast-Action Hotkeys, Offline-First Engine & Auto-Sync Manager
 */

(function (window, document) {
    'use strict';

    const QUEUE_STORAGE_KEY = 'poshub_offline_transaction_queue';

    // =========================================================================
    // 1. POSHUB OFFLINE QUEUE MANAGER
    // =========================================================================
    const PoshubOfflineManager = {
        /**
         * Ambil daftar transaksi offline yang belum tersinkronisasi.
         */
        getQueue: function () {
            try {
                const raw = localStorage.getItem(QUEUE_STORAGE_KEY);
                return raw ? JSON.parse(raw) : [];
            } catch (e) {
                console.error('[POSHUB Offline] Gagal membaca storage:', e);
                return [];
            }
        },

        /**
         * Simpan transaksi ke antrean offline lokal.
         */
        enqueue: function (transactionData) {
            const queue = this.getQueue();
            const offlineUuid = 'OFFLINE-' + Date.now() + '-' + Math.floor(Math.random() * 10000);
            
            transactionData.offline_uuid = offlineUuid;
            transactionData.created_at = transactionData.created_at || new Date().toISOString();
            transactionData.is_offline = true;

            queue.push(transactionData);
            localStorage.setItem(QUEUE_STORAGE_KEY, JSON.stringify(queue));

            this.updateBadge();
            this.showToast('⚠️ Transaksi disimpan ke antrean Offline (' + offlineUuid + ')', 'warning');
            return transactionData;
        },

        /**
         * Hapus transaksi yang telah sukses disinkronkan.
         */
        removeSynced: function (syncedCount) {
            let queue = this.getQueue();
            if (syncedCount >= queue.length) {
                queue = [];
            } else {
                queue = queue.slice(syncedCount);
            }
            localStorage.setItem(QUEUE_STORAGE_KEY, JSON.stringify(queue));
            this.updateBadge();
        },

        /**
         * Jalankan sinkronisasi antrean transaksi ke backend secara otomatis.
         */
        sync: function (silent = false) {
            const queue = this.getQueue();
            if (!queue || queue.length === 0) {
                if (!silent) this.showToast('✅ Semua transaksi kasir sudah tersinkronisasi dengan server.', 'success');
                this.updateBadge();
                return Promise.resolve({ status: true, synced_count: 0 });
            }

            if (!navigator.onLine) {
                if (!silent) this.showToast('🟠 Perangkat masih dalam kondisi Offline. Sinkronisasi ditunda.', 'warning');
                return Promise.resolve({ status: false, message: 'Offline' });
            }

            const badge = document.getElementById('poshub-network-badge');
            if (badge) {
                badge.style.backgroundColor = '#3b82f6';
                badge.innerHTML = `🔄 Menyinkronkan (${queue.length})...`;
            }

            const csrfToken = document.querySelector('meta[name="csrf-token"]') 
                ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') 
                : '';

            return fetch('/api/pos/offline-sync', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ transactions: queue })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status) {
                    const synced = data.synced_count || queue.length;
                    this.removeSynced(synced);
                    this.showToast(`🎉 Sukses! ${synced} transaksi offline berhasil disinkronkan ke Back-Office.`, 'success');
                } else {
                    console.warn('[POSHUB Offline] Sebagian sinkronisasi gagal:', data);
                    if (!silent) this.showToast('⚠️ Gagal menyinkronkan: ' + (data.message || 'Periksa koneksi server.'), 'error');
                }
                this.updateBadge();
                return data;
            })
            .catch(err => {
                console.error('[POSHUB Offline Sync Error]:', err);
                this.updateBadge();
                if (!silent) this.showToast('❌ Gagal menghubungi server. Transaksi tetap aman di lokal.', 'error');
                return { status: false, error: err };
            });
        },

        /**
         * Update tampilan status badge koneksi & antrean.
         */
        updateBadge: function () {
            const badge = document.getElementById('poshub-network-badge');
            if (!badge) return;

            const queue = this.getQueue();
            const count = queue.length;

            if (navigator.onLine) {
                if (count > 0) {
                    badge.style.backgroundColor = '#f59e0b';
                    badge.style.color = '#ffffff';
                    badge.innerHTML = `🟡 Online (${count} Antrean Pending - Klik Sync)`;
                } else {
                    badge.style.backgroundColor = '#10b981';
                    badge.style.color = '#ffffff';
                    badge.innerHTML = '🟢 POS Online (Tersinkron)';
                }
            } else {
                badge.style.backgroundColor = '#ef4444';
                badge.style.color = '#ffffff';
                badge.innerHTML = `🔴 Mode Offline (${count} Transaksi Tersimpan)`;
            }
        },

        /**
         * Tampilkan notifikasi toast elegan di layar kasir.
         */
        showToast: function (message, type = 'info') {
            let container = document.getElementById('poshub-toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'poshub-toast-container';
                container.style.position = 'fixed';
                container.style.bottom = '20px';
                container.style.right = '20px';
                container.style.zIndex = '999999';
                container.style.display = 'flex';
                container.style.flexDirection = 'column';
                container.style.gap = '10px';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            const bgColors = {
                success: '#10b981',
                warning: '#f59e0b',
                error: '#ef4444',
                info: '#1f57db'
            };
            toast.style.background = bgColors[type] || '#1f57db';
            toast.style.color = '#ffffff';
            toast.style.padding = '12px 18px';
            toast.style.borderRadius = '8px';
            toast.style.fontSize = '13px';
            toast.style.fontWeight = 'bold';
            toast.style.boxShadow = '0 8px 20px rgba(0,0,0,0.2)';
            toast.style.fontFamily = 'Arial, sans-serif';
            toast.style.transition = 'all 0.3s ease';
            toast.style.maxWidth = '360px';
            toast.innerHTML = message;

            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(10px)';
                setTimeout(() => toast.remove(), 300);
            }, 4500);
        }
    };

    // Ekspor ke window global
    window.PoshubOfflineManager = PoshubOfflineManager;

    // =========================================================================
    // 2. CONNECTIVITY BADGE & EVENT LISTENERS
    // =========================================================================
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
        badge.style.fontFamily = 'Arial, sans-serif';
        badge.title = 'Klik untuk melihat status antrean offline & tombol hotkey (F1)';

        badge.addEventListener('click', function () {
            const queue = PoshubOfflineManager.getQueue();
            if (queue.length > 0 && navigator.onLine) {
                PoshubOfflineManager.sync(false);
            } else {
                showHotkeysModal();
            }
        });

        document.body.appendChild(badge);
        PoshubOfflineManager.updateBadge();

        // Listener Event Online / Offline Otomatis
        window.addEventListener('online', function () {
            PoshubOfflineManager.showToast('🟢 Koneksi Internet Pulih! Memulai sinkronisasi otomatis...', 'info');
            PoshubOfflineManager.updateBadge();
            // Otomatis sinkronkan saat koneksi kembali online
            setTimeout(() => PoshubOfflineManager.sync(true), 1500);
        });

        window.addEventListener('offline', function () {
            PoshubOfflineManager.showToast('🔴 Koneksi Terputus. Sistem beralih ke Mode Kasir Offline Aman.', 'warning');
            PoshubOfflineManager.updateBadge();
        });

        // Heartbeat sinkronisasi berkala setiap 30 detik jika ada antrean tertunda
        setInterval(function () {
            if (navigator.onLine && PoshubOfflineManager.getQueue().length > 0) {
                PoshubOfflineManager.sync(true);
            }
        }, 30000);
    }

    // =========================================================================
    // 3. HOTKEYS MODAL & KEYBOARD HANDLER
    // =========================================================================
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
                <div style="background:#fff; border-radius:12px; max-width:520px; width:90%; padding:24px; box-shadow:0 10px 25px rgba(0,0,0,0.3); font-family:Arial,sans-serif;">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:1px solid #eee; padding-bottom:10px;">
                        <h3 style="margin:0; font-size:17px; color:#1e293b;">⚡ Pintasan Keyboard & Status Kasir</h3>
                        <button id="poshub-close-hotkeys" style="background:none; border:none; font-size:20px; cursor:pointer; color:#64748b;">&times;</button>
                    </div>
                    
                    <div style="background:#f8fafc; border-radius:8px; padding:12px; margin-bottom:16px; font-size:12.5px;">
                        <div style="display:flex; justify-content:space-between; align-items:center;">
                            <span>Status Koneksi: <strong id="modal-conn-status">${navigator.onLine ? '🟢 Online' : '🔴 Offline'}</strong></span>
                            <button id="modal-btn-sync" style="background:#1f57db; color:#fff; border:none; padding:4px 10px; border-radius:4px; font-size:11px; cursor:pointer; font-weight:bold;">🚀 Sync Sekarang</button>
                        </div>
                    </div>

                    <table style="width:100%; font-size:12.5px; border-collapse:collapse;">
                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:6px 0;"><strong>[F1]</strong></td><td>Buka Bantuan Pintasan ini</td></tr>
                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:6px 0;"><strong>[F2]</strong></td><td>Fokus ke Kotak Cari Produk / Scan Barcode</td></tr>
                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:6px 0;"><strong>[F4]</strong></td><td>Simpan / Tahan Pesanan (Hold Bill)</td></tr>
                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:6px 0;"><strong>[F7]</strong></td><td>Buka Laci Uang (Open Cash Drawer)</td></tr>
                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:6px 0;"><strong>[F8]</strong></td><td>Bayar Uang Pas (Exact Cash)</td></tr>
                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:6px 0;"><strong>[F9]</strong></td><td>Pisah Tagihan (Split Bill)</td></tr>
                        <tr style="border-bottom:1px solid #f1f5f9;"><td style="padding:6px 0;"><strong>[F10]</strong></td><td>Selesaikan Pembayaran (Checkout)</td></tr>
                        <tr><td style="padding:6px 0;"><strong>[ESC]</strong></td><td>Tutup Modal / Batalkan Aksi</td></tr>
                    </table>
                    
                    <div style="text-align:right; margin-top:18px;">
                        <button id="poshub-btn-ok" style="background:#1f57db; color:#fff; border:none; padding:8px 20px; border-radius:6px; font-weight:bold; cursor:pointer;">Tutup</button>
                    </div>
                </div>
            `;

            document.body.appendChild(modal);

            document.getElementById('poshub-close-hotkeys').onclick = () => modal.style.display = 'none';
            document.getElementById('poshub-btn-ok').onclick = () => modal.style.display = 'none';
            document.getElementById('modal-btn-sync').onclick = () => {
                PoshubOfflineManager.sync(false);
            };
            modal.onclick = (e) => { if (e.target === modal) modal.style.display = 'none'; };
        } else {
            const statusEl = document.getElementById('modal-conn-status');
            if (statusEl) statusEl.innerHTML = navigator.onLine ? '🟢 Online' : '🔴 Offline';
            modal.style.display = 'flex';
        }
    }

    // Listener Global Event Keydown
    window.addEventListener('keydown', function (e) {
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
                const holdBtn = document.querySelector('#btn-hold, .btn-hold');
                if (holdBtn) holdBtn.click();
                break;
            case 'F7':
                e.preventDefault();
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

})(window, document);
