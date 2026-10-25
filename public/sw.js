// Service Worker for GENTAPANGAN PWA
// Cache-first strategy for static assets, network-first for API

const CACHE_NAME = 'gentapangan-v1';
const STATIC_ASSETS = [
  '/',
  '/manifest.json',
  '/build/assets/app-6jSCucsl.css',
  '/build/assets/app-BvRk9kiK.js',
];

const CACHE_STRATEGIES = {
  // Cache first - for static assets
  cacheFirst: async (request, cache) => {
    const cached = await cache.match(request);
    if (cached) return cached;
    try {
      const response = await fetch(request);
      if (response.ok) cache.put(request, response.clone());
      return response;
    } catch {
      return new Response('Offline', { status: 503 });
    }
  },

  // Network first - for API calls
  networkFirst: async (request, cache) => {
    try {
      const response = await fetch(request);
      if (response.ok) cache.put(request, response.clone());
      return response;
    } catch {
      const cached = await cache.match(request);
      return cached || new Response('Offline', { status: 503 });
    }
  },

  // Stale while revalidate - for images
  staleWhileRevalidate: async (request, cache) => {
    const cached = await cache.match(request);
    const fetchPromise = fetch(request).then(response => {
      if (response.ok) cache.put(request, response.clone());
      return response;
    }).catch(() => cached);
    return cached || fetchPromise;
  }
};

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(STATIC_ASSETS))
  );
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => 
      Promise.all(keys.filter(k => k !== CACHE_NAME).map(k => caches.delete(k)))
    )
  );
  self.clients.claim();
});

self.addEventListener('fetch', event => {
  const { request } = event;
  const url = new URL(request.url);

  // Skip non-GET requests
  if (request.method !== 'GET') return;

  // Skip cross-origin requests (CDN fonts, etc.)
  if (url.origin !== location.origin) {
    event.respondWith(
      caches.open(CACHE_NAME).then(cache => CACHE_STRATEGIES.staleWhileRevalidate(request, cache))
    );
    return;
  }

  // API routes - network first
  if (url.pathname.startsWith('/api/') || url.pathname.startsWith('/absensi/') || url.pathname.startsWith('/cctvs/') || url.pathname.startsWith('/dapurs/')) {
    event.respondWith(
      caches.open(CACHE_NAME).then(cache => CACHE_STRATEGIES.networkFirst(request, cache))
    );
    return;
  }

  // Static assets (JS, CSS, images) - cache first
  if (url.pathname.startsWith('/build/') || url.pathname.match(/\.(js|css|png|jpg|jpeg|svg|woff2?|ico)$/)) {
    event.respondWith(
      caches.open(CACHE_NAME).then(cache => CACHE_STRATEGIES.cacheFirst(request, cache))
    );
    return;
  }

  // HTML pages - network first with offline fallback
  if (request.headers.get('accept')?.includes('text/html')) {
    event.respondWith(
      caches.open(CACHE_NAME).then(cache => CACHE_STRATEGIES.networkFirst(request, cache))
    );
    return;
  }

  // Default - stale while revalidate
  event.respondWith(
    caches.open(CACHE_NAME).then(cache => CACHE_STRATEGIES.staleWhileRevalidate(request, cache))
  );
});

// Background sync for offline absensi (future enhancement)
self.addEventListener('sync', event => {
  if (event.tag === 'sync-absensi') {
    event.waitUntil(syncAbsensi());
  }
});

async function syncAbsensi() {
  // Future: sync pending absensi when online
  console.log('Sync absensi triggered');
}

// Push notification handler (future)
self.addEventListener('push', event => {
  if (!event.data) return;
  
  const data = event.data.json();
  const options = {
    body: data.body,
    icon: '/icons/icon-192.png',
    badge: '/icons/icon-72.png',
    vibrate: [200, 100, 200],
    data: data.url || '/',
    actions: [
      { action: 'open', title: 'Buka' },
      { action: 'close', title: 'Tutup' }
    ]
  };

  event.waitUntil(
    self.registration.showNotification(data.title, options)
  );
});

self.addEventListener('notificationclick', event => {
  event.notification.close();
  
  if (event.action === 'open') {
    event.waitUntil(clients.openWindow(event.notification.data));
  }
});