# Service Worker and Offline Website Guide

A service worker is a browser script that runs separately from the website. It can cache files and serve them when the internet is unavailable.

## How It Works

1. The browser downloads and installs `/sw.js`.
2. The service worker downloads the files listed in `urlsToCache`.
3. Later, the fetch listener can return cached files when a network request fails.
4. `offline.html` is shown only when a requested page is not available in the cache.

A service worker requires HTTPS. Localhost is also supported for development.

## Basic Static Website Installation

Place `sw.js` in the website root and `offline.html` in an accessible folder.

```html
<script>
  if ("serviceWorker" in navigator) {
    navigator.serviceWorker.register("/sw.js")
      .catch(error => console.error("Service worker registration failed:", error));
  }
</script>
```

The registration code should use `register("/sw.js")` for a domain-root site. If the site is hosted in a subdirectory, use the directory path consistently.

Example:

```javascript
const CACHE_NAME = "site-cache-v1";

const urlsToCache = [
  "/",
  "/index.html",
  "/css/styles.css",
  "/js/scripts.js",
  "/offline.html"
];

self.addEventListener("install", event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener("activate", event => {
  event.waitUntil(
    caches.keys().then(cacheNames => Promise.all(
      cacheNames
        .filter(cacheName => cacheName !== CACHE_NAME)
        .map(cacheName => caches.delete(cacheName))
    ))
  );
});

self.addEventListener("fetch", event => {
  if (event.request.mode !== "navigate") {
    return;
  }

  event.respondWith(
    fetch(event.request).catch(async () => {
      const cachedPage = await caches.match(event.request);
      return cachedPage || caches.match("/offline.html");
    })
  );
});
```

Note that navigation fallback should not return an HTML page for CSS, images, scripts, fonts, API requests, or form submissions.

## Updating the Cache

Change `CACHE_NAME`, for example from `site-cache-v1` to `site-cache-v2`, whenever cached files change.

Increasing the name causes the new cache to be installed and the old cache to be deleted during activation. Existing visitors may continue using the old worker until the update activates and the page is reopened.

## Testing Offline Mode

1. Open the site while connected to the internet.
2. Open browser Developer Tools.
3. Open **Application > Service Workers**.
4. Confirm the worker has status `activated and running`.
5. Open **Application > Cache Storage** and verify the expected files were cached.
6. Enable **Offline** in Developer Tools or disconnect the internet.
7. Refresh the page or open a random uncached URL.
8. A valid cached page should load from cache; otherwise, `/offline.html` should appear.

## Installing It in WordPress

### Option 1: Use a Service Worker Plugin

A reputable PWA or offline plugin can generate and update the service worker for you. However, plugins differ in WordPress compatibility and caching behavior.

Before selecting one:

- Verify current WordPress and PHP compatibility.
- Avoid plugins from unknown or abandoned sources.
- Test login, checkout, admin, API, search, and contact forms.
- Exclude `wp-admin`, WordPress AJAX URLs, REST API responses, cookies, and authenticated sessions from caching unless specifically intended.

### Option 2: Add a Small Service Worker Manually

Create `/wp-content/plugins/site-offline/sw.js`? This is usually a poor location because plugin folders and URLs may be reorganized or inaccessible from your theme.

For WordPress, use a file at the domain root, such as `/sw.js`. It can be supplied from a theme, plugin, or by uploading the file through hosting file management. WordPress must serve it at:

```text
https://yourdomain.com/sw.js
```

Register it from a theme footer or plugin hook:

```php
add_action('wp_footer', function () {
    if (!is_admin()) {
        ?>
        <script>
          if ("serviceWorker" in navigator) {
            navigator.serviceWorker.register("/sw.js", { scope: "/" })
              .catch(error => console.error("Service worker registration failed:", error));
          }
        </script>
        <?php
    }
});
```

For an actual WordPress plugin, enqueuing the registration script with `wp_enqueue_script` is generally cleaner than outputting it directly in the footer.

### WordPress Caching Precautions

Do not blindly cache these URLs:

- `/wp-admin/`
- `/wp-login.php`
- `/wp-json/`
- `/wp-admin/admin-ajax.php`
- Payment or checkout endpoints
- URLs using logged-in cookies
- URLs that return personalized or frequently changing content

A good WordPress rule is to cache static homepage assets and public pages, but use the network for dynamic and authenticated requests.

## Cache Storage Limits

There is no fixed Cache Storage size common to every browser or website.

Typical browser behavior is:

- Storage is origin-based, not only service-worker-based.
- Desktop and mobile browsers have different quotas.
- Each origin can normally store a significant amount, often hundreds of megabytes or more, but the exact amount depends on the browser, device storage, and quota policy.
- Private browsing can have temporary or smaller limits.
- Safari and other browsers can be more restrictive.
- Browsers may automatically remove origin storage when the device is low on space or after long inactivity.

You should not rely on guaranteed offline storage. The browser controls eviction and permission.

### What You Can Cache

Suitable content:

- HTML pages
- CSS files
- JavaScript files
- Fonts
- Icons and logos
- Small images
- Offline fallback page
- Videos or downloads only when deliberately added and size-tested

Avoid adding large media by default, especially full galleries and video libraries. Calculate realistic needs before choosing a cache strategy.

Examples:

| Content | Example Size | Reasonable Service Worker Cache? |
|---|---:|---|
| Portfolio page and assets | 1-5 MB | Yes |
| Image gallery thumbnails | 20-100 MB | Possibly, after testing |
| Web page articles | 10-50 MB | Possibly, if limited |
| Product images | Variable | Cache selective or frequently requested files |
| Full video library | Hundreds of MB or more | Usually not in a single precache list |

Do not add thousands of large files to `addAll()`. One missing file causes the complete installation operation to fail.

## Safe Caching Strategies

### Precache

List only essential files in `urlsToCache` during installation.

Use for:

- Homepage
- Stylesheets
- Main scripts
- Logo and profile images
- Offline page

### Runtime Cache

Cache files as they are successfully downloaded. This is useful for images and pages that may be visited later.

Example concept:

```javascript
fetch(event.request)
  .then(response => {
    if (response.ok) {
      caches.open(CACHE_NAME).then(cache => cache.put(event.request, response.clone()));
    }
    return response;
  });
```

### Network Strategies

- Static assets: cache first or stale while revalidate.
- Public articles: network first with cache fallback.
- Images: cache first if the content rarely changes.
- Admin or checkout pages: network only.
- Search and API data: network only unless stale data is explicitly acceptable.

## Security and Privacy

- Never cache passwords, tokens, private payment data, or authenticated responses as public content.
- If user-specific pages are cached, include account and logout behavior in the cache policy.
- Use cache names that distinguish public and private data.
- Clear the old cache when changing a site’s privacy or authentication behavior.
- Do not use online-only secrets in JavaScript.

## Troubleshooting

### Offline page does not appear

- Confirm the service worker is activated.
- Confirm `/offline.html` exists at the website root or use the correct path.
- Confirm `/offline.html` was included in `urlsToCache`.
- Check that the browser received the service worker over HTTPS.
- Clear an old cache and update the cache version.

### The complete service worker fails to install

- One URL in `addAll()` is missing.
- One URL returns a redirect or server error.
- A service worker cannot intercept a third-party file, although third-party resources can be cached only under conditions supported by the browser and CORS.
- Cross-origin assets require valid CORS or should be avoided in precaching.

### Visitors still see old content

- Update `CACHE_NAME`.
- Refresh the service worker in browser Developer Tools.
- Test in an incognito/private window.
- Check that hosting/CDN caching is not serving an old `sw.js` file.

## Short Recommendation

For this static portfolio, keep the precache list small:

- Homepage and CSS
- JavaScript
- Profile and favicon images
- Offline fallback page

For WordPress, use a maintained PWA/offline plugin or a narrowly designed service worker that caches public assets but never caches admin, login, checkout, REST API, search, or personalized responses.
