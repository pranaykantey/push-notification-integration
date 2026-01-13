// Push Notification Integration JavaScript with jQuery

jQuery(document).ready(function($) {
    // Handle button clicks for push notifications
    jQuery(document).on('click', '.push-notification-btn', function() {
        var title = jQuery(this).data('title');
        var body = jQuery(this).data('body');
        var icon = jQuery(this).data('icon');
        showPushNotification(title, body, icon);
    });

    // Show a notification
    window.showPushNotification = function(title, body, icon) {
        if (!('Notification' in window)) {
            console.log('This browser does not support notifications.');
            return;
        }

        if (Notification.permission === 'granted') {
            title = title || pushNotificationOptions.defaultTitle;
            body = body || pushNotificationOptions.defaultBody;
            icon = icon || pushNotificationOptions.iconUrl;
            new Notification(title, {
                body: body,
                icon: icon
            });
        } else if (Notification.permission !== 'denied') {
            // Request permission
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    console.log('Notification permission granted.');
                    title = title || pushNotificationOptions.defaultTitle;
                    body = body || pushNotificationOptions.defaultBody;
                    icon = icon || pushNotificationOptions.iconUrl;
                    new Notification(title, {
                        body: body,
                        icon: icon
                    });
                } else {
                    console.log('Notification permission denied.');
                }
            });
        } else {
            console.log('Notification permission was denied. Please enable notifications in your browser settings.');
        }
    };
});