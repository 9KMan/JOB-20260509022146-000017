const CACHE_NAME = 'gps-attendance-v1';
const urlsToCache = [
  '/mobile/',
  '/mobile/index.html',
  '/mobile/js/app.js',
  '/mobile/manifest.json'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        if (response) {
          return response;
        }
        return fetch(event.request).then(response => {
          if (!response || response.status !== 200) {
            return response;
          }
          const responseToCache = response.clone();
          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, responseToCache);
          });
          return response;
        });
      })
  );
});

self.addEventListener('sync', event => {
  if (event.tag === 'sync-attendance') {
    event.waitUntil(syncAttendance());
  }
});

function syncAttendance() {
  return new Promise((resolve, reject) => {
    const queue = JSON.parse(localStorage.getItem('offlineQueue') || '[]');
    if (queue.length === 0) return resolve();

    const token = localStorage.getItem('token');
    let processed = 0;

    queue.forEach(async (item, index) => {
      try {
        const endpoint = item.type === 'checkout' ? '/checkout' : '/checkin';
        const response = await fetch('/api/routes' + endpoint, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + token
          },
          body: JSON.stringify(item.payload)
        });

        if (response.ok) {
          queue.splice(index, 1);
        }
      } catch (e) {
        console.error('Sync error:', e);
      }

      processed++;
      if (processed === queue.length) {
        localStorage.setItem('offlineQueue', JSON.stringify(queue));
        resolve();
      }
    });
  });
}