const CACHE_NAME = 'saefl-cache-v1.1.2';
const urlsToCache = [
  '/',
  '/styles.css',
  '/app.js',
  '/icon.png',
  '/icon_48.png',
  '/icon_128.png',
  '/icon_192.png',
  '/icon_512.png'
];

// self.addEventListener('install', (event) => {
//     event.waitUntil(
//         caches.open(CACHE_NAME)
//         .then((cache) => cache.addAll(urlsToCache))
//     );
// });

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request)
        .then((response) => response || fetch(event.request))
    );
});

self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            return Promise.all(
                urlsToCache.map(url => 
                    fetch(url, { mode: 'no-cors' }) // Modo sin CORS
                        .then(response => cache.put(url, response))
                        .catch(err => console.error(`Error cacheando ${url}:`, err))
                )
            );
        })
    );
});
