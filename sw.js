const CACHE_NAME = "portfolio-cache-v3";
const urlsToCache = [
  "/",
  "/index.html",
  "/css/styles.css",
  "/css/blog.css",
  "/blog/",
  "/blog/headless-wordpress-development-services-a-cto-guide-to-faster-scalable-websites/",
  "/blog/hire-an-enterprise-laravel-development-lead-to-accelerate-product-delivery/",
  "/blog/devops-infrastructure-migration-a-business-guide-to-lower-risk-and-faster-delivery/",
  "/blog/shopify-performance-optimization-for-higher-ecommerce-conversions/",
  "/js/scripts.js",
  "/assets/img/profile.jpg",
  "/assets/img/favicon.ico",
  "/offline.html"
];

// Install SW
self.addEventListener("install", event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(urlsToCache);
    })
  );
});

// Fetch with offline fallback
self.addEventListener("fetch", event => {
  event.respondWith(
    fetch(event.request).catch(() => {
      return caches.match(event.request).then(response => {
        return response || caches.match("/offline.html");
      });
    })
  );
});

// Activate SW and clean old caches
self.addEventListener("activate", event => {
  event.waitUntil(
    caches.keys().then(cacheNames =>
      Promise.all(
        cacheNames.map(name => {
          if (name !== CACHE_NAME) {
            return caches.delete(name);
          }
        })
      )
    )
  );
});
