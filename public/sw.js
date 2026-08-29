/* ==========================================================================
   POSHUB ACCOUNTING & ERP - ENTERPRISE SERVICE WORKER (PWA ENGINE)
   Offline Caching, Background Sync, and Push Notification Handling
   ========================================================================== */

const CACHE_NAME = 'poshub-pwa-v1.0.0';
const STATIC_ASSETS = [
  '/',
  '/manifest.json',
  '/css/poshub-modern-ui.css',
  '/assets/css/bootstrap.css',
  '/assets/css/pos.css',
  '/assets/images/icon.png',
  '/images/logo.png',
  '/js/pos-hotkeys.js',
  '/js/pwa-manager.js',
  '/js/hardware-bridge.js',
  '/assets/vendors/fontawesome/all.min.css',
  '/assets/jquery-3.3.1.min.js',
  '/assets/js/bootstrap.bundle.min.js'
];

// 1. Install Event: Pre-cache core shell assets
self.addEventListener('install', (event) => {
  event.waitUntil(
    caches.open(CACHE_NAME).then((cache) => {
      console.log('[POSHUB SW] Pre-caching core PWA shell assets');
      return cache.addAll(STATIC_ASSETS).catch((err) => {
        console.warn('[POSHUB SW] Cache addAll warning (non-blocking):', err);
      });
    }).then(() => self.skipWaiting())
  );
});

// 2. Activate Event: Clean up outdated caches
self.addEventListener('activate', (event) => {
  event.waitUntil(
    caches.keys().then((cacheNames) => {
      return Promise.all(
        cacheNames.map((name) => {
          if (name !== CACHE_NAME) {
            console.log('[POSHUB SW] Deleting obsolete cache:', name);
            return caches.delete(name);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// 3. Fetch Event: Cache-First for static assets, Network-First for dynamic/API
self.addEventListener('fetch', (event) => {
  const url = new URL(event.request.url);

  // Ignore non-GET requests and browser extensions
  if (event.request.method !== 'GET' || !url.protocol.startsWith('http')) {
    return;
  }

  // A. Static Asset requests (Images, CSS, JS, Fonts): Cache-First
  if (
    url.pathname.match(/\.(css|js|png|jpg|jpeg|svg|webp|woff|woff2|ttf|eot)$/) ||
    STATIC_ASSETS.includes(url.pathname)
  ) {
    event.respondWith(
      caches.match(event.request).then((cachedResponse) => {
        if (cachedResponse) {
          // Fetch update in background (Stale-While-Revalidate)
          fetch(event.request).then((networkResponse) => {
            if (networkResponse && networkResponse.status === 200) {
              caches.open(CACHE_NAME).then((cache) => cache.put(event.request, networkResponse));
            }
          }).catch(() => {});
          return cachedResponse;
        }
        return fetch(event.request).then((networkResponse) => {
          if (networkResponse && networkResponse.status === 200) {
            const responseClone = networkResponse.clone();
            caches.open(CACHE_NAME).then((cache) => cache.put(event.request, responseClone));
          }
          return networkResponse;
        });
      })
    );
    return;
  }

  // B. Page Navigation & API: Network-First with Offline Fallback
  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request).then((cachedResponse) => {
        if (cachedResponse) return cachedResponse;
        if (event.request.mode === 'navigate') {
          return caches.match('/');
        }
        return new Response(JSON.stringify({ offline: true, message: 'Koneksi offline. Data lokal digunakan.' }), {
          headers: { 'Content-Type': 'application/json' }
        });
      });
    })
  );
});

// 4. Background Sync: Sync offline cashier transactions
self.addEventListener('sync', (event) => {
  if (event.tag === 'sync-pos-transactions') {
    console.log('[POSHUB SW] Background Sync Triggered: Syncing offline transactions');
    event.waitUntil(
      self.clients.matchAll().then((clients) => {
        clients.forEach((client) => {
          client.postMessage({ action: 'TRIGGER_OFFLINE_SYNC' });
        });
      })
    );
  }
});

// 5. Push Notification Event
self.addEventListener('push', (event) => {
  let payload = { title: 'POSHUB Notifikasi', body: 'Ada pembaruan transaksi baru.', url: '/pos' };
  if (event.data) {
    try {
      payload = event.data.json();
    } catch (e) {
      payload.body = event.data.text();
    }
  }

  const options = {
    body: payload.body,
    icon: '/assets/images/icon.png',
    badge: '/installer/img/favicon/favicon-96x96.png',
    vibrate: [100, 50, 100],
    data: { url: payload.url || '/' }
  };

  event.waitUntil(self.registration.showNotification(payload.title, options));
});

// 6. Notification Click Action
self.addEventListener('notificationclick', (event) => {
  event.notification.close();
  const urlToOpen = event.notification.data?.url || '/';
  event.waitUntil(
    clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
      for (const client of clientList) {
        if (client.url === urlToOpen && 'focus' in client) {
          return client.focus();
        }
      }
      if (clients.openWindow) return clients.openWindow(urlToOpen);
    })
  );
});
