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