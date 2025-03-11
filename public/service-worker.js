const CACHE_NAME = "pwa-cache-v1";
const urlsToCache = [
    "index.php",
    "style.css",
    "script.js",
    "manifest.json",
    "images/icon-192x192.png",
    "images/icon-512x512.png"
];

self.addEventListener("install", (event) => {
    console.log("Service Worker: Installing...");
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then((cache) => {
                console.log("Service Worker: Caching files");
                return cache.addAll(urlsToCache);
            })
            .then(() => {
                console.log("Service Worker: Installed ✅");
                self.skipWaiting();
            })
            .catch((err) => console.error("Cache Failed:", err))
    );
});

self.addEventListener("activate", (event) => {
    console.log("Service Worker: Activating...");
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((cache) => cache !== CACHE_NAME)
                    .map((cache) => caches.delete(cache))
            );
        }).then(() => {
            console.log("Service Worker: Active ✅");
            return self.clients.claim();
        })
    );
});

self.addEventListener("fetch", (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});
