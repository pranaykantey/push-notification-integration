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
        var notificationId = jQuery(this).data('id') || 0; // Need to add data-id
        trackEvent(notificationId, 'button_click');

        var data = {
            title: jQuery(this).data('title'),
            body: jQuery(this).data('body'),
            icon: jQuery(this).data('icon'),
            image: jQuery(this).data('image'),
            actionTitle: jQuery(this).data('action-title'),
            actionUrl: jQuery(this).data('action-url')
        };
        showPushNotification(data, notificationId);
    });

    // Show a notification
    window.showPushNotification = function(data, notificationId) {
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
            options.data = { url: data.actionUrl, notificationId: notificationId };
        }

        if (Notification.permission === 'granted') {
            var notification = new Notification(data.title || pushNotificationOptions.defaultTitle, options);
            trackEvent(notificationId, 'notification_shown');

            // Track action clicks
            notification.onclick = function() {
                trackEvent(notificationId, 'action_click');
                if (data.actionUrl) {
                    window.open(data.actionUrl);
                }
            };
        } else if (Notification.permission !== 'denied') {
            // Request permission
            Notification.requestPermission().then(function(permission) {
                if (permission === 'granted') {
                    console.log('Notification permission granted.');
                    var notification = new Notification(data.title || pushNotificationOptions.defaultTitle, options);
                    trackEvent(notificationId, 'notification_shown');

                    notification.onclick = function() {
                        trackEvent(notificationId, 'action_click');
                        if (data.actionUrl) {
                            window.open(data.actionUrl);
                        }
                    };
                } else {
                    console.log('Notification permission denied.');
                    alert('Notifications are blocked. Please enable notifications for this site in your browser settings.');
                }
            });
        } else {
            console.log('Notification permission was denied. Please enable notifications in your browser settings.');
            alert('Notifications are blocked. Please enable notifications for this site in your browser settings.');
            // Try email fallback
            sendEmailFallback(notificationId);
        }
    };

    function getCookie(name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length == 2) return parts.pop().split(";").shift();
    }

    function trackEvent(notificationId, eventType) {
        if (!notificationId) return;

        fetch('/wp-json/push-notification/v1/track', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                notification_id: notificationId,
                event_type: eventType,
                session_id: getSessionId()
            })
        }).catch(function(error) {
            console.log('Tracking error:', error);
        });
    }

    function getSessionId() {
        var sessionId = localStorage.getItem('push_notification_session');
        if (!sessionId) {
            sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            localStorage.setItem('push_notification_session', sessionId);
        }
        return sessionId;
    }

    function sendEmailFallback(notificationId) {
        fetch('/wp-json/push-notification/v1/email-fallback', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-WP-Nonce': wpApiSettings ? wpApiSettings.nonce : ''
            },
            body: JSON.stringify({
                notification_id: notificationId
            })
        }).then(function(response) {
            if (response.ok) {
                console.log('Email fallback sent');
            }
        }).catch(function(error) {
            console.log('Email fallback error:', error);
        });
    }
});