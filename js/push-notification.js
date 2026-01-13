// Push Notification Integration JavaScript with jQuery

jQuery(document).ready(function($) {
    // Check if browser supports notifications
    if ('Notification' in window) {
        // Request permission
        Notification.requestPermission().then(function(permission) {
            if (permission === 'granted') {
                console.log('Notification permission granted.');
            } else {
                console.log('Notification permission denied.');
            }
        });
    }

    // Handle button clicks for push notifications
    jQuery(document).on('click', '.push-notification-btn', function() {
        var title = jQuery(this).data('title');
        var body = jQuery(this).data('body');
        var icon = jQuery(this).data('icon');
        showPushNotification(title, body, icon);
    });

    // Example: Show a notification on button click (you can add a button in your theme)
    // This is just a demo; integrate as needed
    window.showPushNotification = function(title, body, icon) {
        if (Notification.permission === 'granted') {
            title = title || pushNotificationOptions.defaultTitle;
            body = body || pushNotificationOptions.defaultBody;
            icon = icon || pushNotificationOptions.iconUrl;
            new Notification(title, {
                body: body,
                icon: icon
            });
        }
    };
});