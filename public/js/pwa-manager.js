/**
 * ==========================================================================
 * POSHUB ACCOUNTING - PWA CLIENT CONTROLLER
 * Service Worker Registration, Install Banner, and Offline Integration
 * ==========================================================================
 */

(function () {
    'use strict';

    let deferredPrompt = null;

    // 1. Register Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker
                .register('/sw.js')
                .then((registration) => {
                    console.log('[POSHUB PWA] Service Worker registered with scope:', registration.scope);

                    // Check for updates
                    registration.addEventListener('updatefound', () => {
                        const newWorker = registration.installing;
                        newWorker.addEventListener('statechange', () => {
                            if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                                console.log('[POSHUB PWA] New update available.');
                            }
                        });
                    });
                })
                .catch((err) => {
                    console.warn('[POSHUB PWA] Service Worker registration skipped/failed:', err);
                });
        });
    }

    // 2. Listen to beforeinstallprompt event
    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault();
        deferredPrompt = e;
        showInstallPromotion();
    });

    // 3. UI: Elegant Floating Install Banner
    function showInstallPromotion() {
        if (localStorage.getItem('poshub_pwa_dismissed') === 'true') {
            return;
        }

        const bannerId = 'poshub-pwa-install-banner';
        if (document.getElementById(bannerId)) return;

        const banner = document.createElement('div');
        banner.id = bannerId;
        banner.innerHTML = `
            <div style="
                position: fixed;
                bottom: 16px;
                left: 16px;
                right: 16px;
                max-width: 440px;
                margin: 0 auto;
                background: #0f172a;
                color: #ffffff;
                border: 1px solid #334155;
                border-radius: 12px;
                padding: 12px 16px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.35);
                z-index: 99999;
                display: flex;
                align-items: center;
                justify-content: space-between;
                font-family: -apple-system, BlinkMacSystemFont, 'Inter', sans-serif;
                animation: slideUpFade 0.3s ease-out;
            ">
                <div style="display: flex; align-items: center; gap: 12px;">
                    <img src="/assets/images/icon.png" alt="POSHUB" style="width: 36px; height: 36px; border-radius: 8px;" />
                    <div>
                        <div style="font-weight: 700; font-size: 13px; line-height: 1.2;">Install Aplikasi POSHUB</div>
                        <div style="font-size: 11px; color: #94a3b8;">Akses kasir lebih cepat & offline-ready</div>
                    </div>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <button id="poshub-btn-install" style="
                        background: #1e40af;
                        color: #ffffff;
                        border: none;
                        border-radius: 6px;
                        padding: 6px 14px;
                        font-weight: 600;
                        font-size: 12px;
                        cursor: pointer;
                    ">Install</button>
                    <button id="poshub-btn-dismiss" style="
                        background: transparent;
                        color: #94a3b8;
                        border: none;
                        font-size: 16px;
                        cursor: pointer;
                        padding: 4px 6px;
                    ">&times;</button>
                </div>
            </div>
            <style>
                @keyframes slideUpFade {
                    from { transform: translateY(20px); opacity: 0; }
                    to { transform: translateY(0); opacity: 1; }
                }
            </style>
        `;

        document.body.appendChild(banner);

        document.getElementById('poshub-btn-install').addEventListener('click', async () => {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                const { outcome } = await deferredPrompt.userChoice;
                console.log('[POSHUB PWA] User install choice:', outcome);
                deferredPrompt = null;
                banner.remove();
            }
        });

        document.getElementById('poshub-btn-dismiss').addEventListener('click', () => {
            banner.remove();
            localStorage.setItem('poshub_pwa_dismissed', 'true');
        });
    }

    // 4. Listen to App Installed event
    window.addEventListener('appinstalled', () => {
        console.log('[POSHUB PWA] POSHUB was successfully installed.');
        const banner = document.getElementById('poshub-pwa-install-banner');
        if (banner) banner.remove();
        deferredPrompt = null;
    });

    // Expose Global Helper
    window.PoshubPwa = {
        promptInstall: () => {
            if (deferredPrompt) deferredPrompt.prompt();
        },
        isStandalone: () => {
            return window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone === true;
        }
    };
})();
