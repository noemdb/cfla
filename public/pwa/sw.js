const CACHE_NAME = 'saefl-cache-v1';
const urlsToCache = [
  '/',
  '/styles.css',
  '/app.js',
  '/icon_48.png',
  '/icon_128.png'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(CACHE_NAME)
        .then((cache) => cache.addAll(urlsToCache))
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request)
        .then((response) => response || fetch(event.request))
    );
});