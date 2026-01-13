// Service Worker for Push Notifications

self.addEventListener('install', function(event) {
    console.log('Service Worker installing.');
    // Perform install steps
});

self.addEventListener('activate', function(event) {
    console.log('Service Worker activating.');
    // Perform activate steps
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

    event.waitUntil(
        clients.openWindow('/') // Open the site when clicked
    );
});