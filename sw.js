// Service Worker for Push Notifications and PWA

const CACHE_NAME = 'push-notification-cache-v1';
const urlsToCache = [
    '/',
    '/wp-content/plugins/push-notification-integration/js/push-notification.js',
    '/wp-content/plugins/push-notification-integration/manifest.json'
];

self.addEventListener('install', function(event) {
    console.log('Service Worker installing.');
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(function(cache) {
                console.log('Opened cache');
                return cache.addAll(urlsToCache);
            })
    );
});

self.addEventListener('activate', function(event) {
    console.log('Service Worker activating.');
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheName !== CACHE_NAME) {
                        console.log('Deleting old cache:', cacheName);
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

self.addEventListener('fetch', function(event) {
    event.respondWith(
        caches.match(event.request)
            .then(function(response) {
                // Cache hit - return response
                if (response) {
                    return response;
                }
                return fetch(event.request);
            }
        )
    );
});

self.addEventListener('push', function(event) {
    console.log('Push message received.', event);

    var title = 'Push Notification';
    var body = 'You have a new message.';
    var icon = '/path/to/icon.png'; // Update with your icon path
    var tag = 'push-notification';

    event.waitUntil(
        self.registration.showNotification(title, {
            body: body,
            icon: icon,
            tag: tag
        })
    );
});

self.addEventListener('notificationclick', function(event) {
    console.log('Notification click received.');

    event.notification.close();

    if (event.action === 'view' && event.notification.data && event.notification.data.url) {
        event.waitUntil(
            clients.openWindow(event.notification.data.url)
        );
    } else {
        event.waitUntil(
            clients.openWindow('/') // Open the site when clicked
        );
    }
});