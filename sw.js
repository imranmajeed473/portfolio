const CACHE_NAME = "portfolio-cache-v4";
const urlsToCache = [
  "/",
  "/index.html",
  "/css/styles.css?ver=20261102",
  "/css/blog.css?ver=20261102",
  "/blog/",
  "/blog/headless-wordpress-development-services-a-cto-guide-to-faster-scalable-websites/",
  "/blog/hire-an-enterprise-laravel-development-lead-to-accelerate-product-delivery/",
  "/blog/devops-infrastructure-migration-a-business-guide-to-lower-risk-and-faster-delivery/",
  "/blog/shopify-performance-optimization-for-higher-ecommerce-conversions/",
  "/js/scripts.js?ver=20261102",
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
        if (response) return response;

        const url = new URL(event.request.url);
        url.search = "";
        return caches.match(url.href).then(cachedResponse => {
          return cachedResponse || caches.match("/offline.html");
        });
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
