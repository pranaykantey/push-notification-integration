// Push Notification Integration JavaScript with jQuery

jQuery(document).ready(function($) {
    // Consent handling
    jQuery(document).on('click', '#accept-notifications', function() {
        document.cookie = "push_notification_consent=accepted; path=/; max-age=31536000";
        jQuery('#push-notification-consent').hide();
    });

    jQuery(document).on('click', '#decline-notifications', function() {
        document.cookie = "push_notification_consent=declined; path=/; max-age=31536000";
        jQuery('#push-notification-consent').hide();
    });

    // Handle button clicks for push notifications
    jQuery(document).on('click', '.push-notification-btn', function() {
        var consent = getCookie('push_notification_consent');
        if (consent !== 'accepted') {
            alert('Please accept notifications consent first.');
            return;
        }
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
                    alert('Notifications are blocked. Please enable notifications for this site in your browser settings.');
                }
            });
        } else {
            console.log('Notification permission was denied. Please enable notifications in your browser settings.');
            alert('Notifications are blocked. Please enable notifications for this site in your browser settings.');
        }
    };

    function getCookie(name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length == 2) return parts.pop().split(";").shift();
    }
});