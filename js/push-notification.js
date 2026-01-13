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

    // Show recent post notification if available
    if (typeof recentPostNotification !== 'undefined') {
        var consent = getCookie('push_notification_consent');
        if (consent === 'accepted') {
            showPushNotification({
                title: recentPostNotification.title,
                body: recentPostNotification.body,
                icon: recentPostNotification.icon,
                actionTitle: recentPostNotification.action_title,
                actionUrl: recentPostNotification.action_url
            });
        }
    }

    // Show API triggered notification if available
    if (typeof apiNotification !== 'undefined') {
        var consent = getCookie('push_notification_consent');
        if (consent === 'accepted') {
            showPushNotification(apiNotification);
        }
    }

    // Handle button clicks for push notifications
    jQuery(document).on('click', '.push-notification-btn', function() {
        var consent = getCookie('push_notification_consent');
        if (consent !== 'accepted') {
            alert('Please accept notifications consent first.');
            return;
        }
        var data = {
            title: jQuery(this).data('title'),
            body: jQuery(this).data('body'),
            icon: jQuery(this).data('icon'),
            image: jQuery(this).data('image'),
            actionTitle: jQuery(this).data('action-title'),
            actionUrl: jQuery(this).data('action-url')
        };
        showPushNotification(data);
    });

    // Show a notification
    window.showPushNotification = function(data) {
        if (!('Notification' in window)) {
            console.log('This browser does not support notifications.');
            return;
        }

        var options = {
            body: data.body || pushNotificationOptions.defaultBody,
            icon: data.icon || pushNotificationOptions.iconUrl
        };

        if (data.image) {
            options.image = data.image;
        }

        if (data.actionTitle) {
            options.actions = [{
                action: 'view',
                title: data.actionTitle
            }];
            options.data = { url: data.actionUrl };
        }

        if (Notification.permission === 'granted') {
            new Notification(data.title || pushNotificationOptions.defaultTitle, options);
        } else if (Notification.permission !== 'denied') {
            // Request permission
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    console.log('Notification permission granted.');
                    new Notification(data.title || pushNotificationOptions.defaultTitle, options);
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