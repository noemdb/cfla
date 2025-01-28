const CACHE_NAME = 'pwa-saefl-cache-v1.1.1'; // Cambia el número de versión
const urlsToCache = [
    '/',
    '/index.html',
    '/css/styles.css',
    '/js/script.js',
    '/icon.png'
];

// Instalar Service Worker y actualizar caché
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return cache.addAll(urlsToCache);
        })
    );
});

// Eliminar cachés antiguas cuando se active el nuevo SW
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.filter(cache => cache !== CACHE_NAME)
                    .map(cache => caches.delete(cache))
            );
        })
    );
});

// Interceptar peticiones y responder con caché
self.addEventListener('fetch', event => {
    event.respondWith(
        caches.match(event.request).then(response => {
            return response || fetch(event.request);
        })
    );
});


self.addEventListener('message', (event) => {
    if (event.data === 'skipWaiting') {
        self.skipWaiting();
    }
});
