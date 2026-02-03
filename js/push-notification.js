// Push Notification Integration JavaScript with jQuery

jQuery(document).ready(function($) {
    // Show a notification
    window.showPushNotification = function(data, notificationId, variant) {
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
            trackEvent(notificationId, 'notification-shown', variant);

            // Track action clicks
            notification.onclick = function() {
                trackEvent(notificationId, 'action-click', variant);
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
                    trackEvent(notificationId, 'notification-shown', variant);

                    notification.onclick = function() {
                        trackEvent(notificationId, 'action-click', variant);
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

    // Consent handling
    jQuery(document).on('click', '#accept-notifications', function() {
        document.cookie = "push_notification_consent=accepted; path=/; max-age=31536000";
        jQuery('#push-notification-consent').hide();

        // Sync to CRM if logged in
        fetch('/wp-json/push-notification/v1/consent', {
            method: 'POST',
            headers: {
                'X-WP-Nonce': wpApiSettings ? wpApiSettings.nonce : ''
            }
        }).catch(function(error) {
            console.log('CRM sync error:', error);
        });
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
            var notificationKey = 'api_' + (apiNotification.timestamp || Date.now());
            if (!localStorage.getItem('push_notification_shown_' + notificationKey)) {
                showPushNotification(apiNotification);
                localStorage.setItem('push_notification_shown_' + notificationKey, 'true');
            }
        }
    }

    // Show cart add notification if available (for non-AJAX requests)
    if (typeof cartAddNotification !== 'undefined') {
        console.log('Push Notification: Cart add notification found', cartAddNotification);
        var consent = getCookie('push_notification_consent');
        if (consent === 'accepted') {
            console.log('Push Notification: User consented, showing cart notification');
            var cartNotificationKey = 'cart_' + (cartAddNotification.timestamp || Date.now());
            if (!localStorage.getItem('push_notification_shown_' + cartNotificationKey)) {
                showPushNotification(cartAddNotification);
                localStorage.setItem('push_notification_shown_' + cartNotificationKey, 'true');
            }
        } else {
            console.log('Push Notification: User has not consented to notifications');
        }
    } else {
        console.log('Push Notification: No cart add notification found');
    }

    // Show abandoned cart notification if available
    if (typeof abandonedCartNotification !== 'undefined') {
        console.log('Push Notification: Abandoned cart notification found', abandonedCartNotification);
        var consent = getCookie('push_notification_consent');
        if (consent === 'accepted') {
            console.log('Push Notification: User consented, showing abandoned cart notification');
            var abandonedKey = 'abandoned_' + (abandonedCartNotification.timestamp || Date.now());
            if (!localStorage.getItem('push_notification_shown_' + abandonedKey)) {
                showPushNotification(abandonedCartNotification);
                localStorage.setItem('push_notification_shown_' + abandonedKey, 'true');
            }
        } else {
            console.log('Push Notification: User has not consented to notifications');
        }
    } else {
        console.log('Push Notification: No abandoned cart notification found');
    }

    // Show price drop notification if available
    if (typeof priceDropNotification !== 'undefined') {
        console.log('Push Notification: Price drop notification found', priceDropNotification);
        var consent = getCookie('push_notification_consent');
        if (consent === 'accepted') {
            console.log('Push Notification: User consented, showing price drop notification');
            var priceKey = 'price_' + (priceDropNotification.timestamp || Date.now());
            if (!localStorage.getItem('push_notification_shown_' + priceKey)) {
                showPushNotification(priceDropNotification);
                localStorage.setItem('push_notification_shown_' + priceKey, 'true');
            }
        }
    }

    // Show back in stock notification if available
    if (typeof backInStockNotification !== 'undefined') {
        console.log('Push Notification: Back in stock notification found', backInStockNotification);
        var consent = getCookie('push_notification_consent');
        if (consent === 'accepted') {
            console.log('Push Notification: User consented, showing back in stock notification');
            var stockKey = 'stock_' + (backInStockNotification.timestamp || Date.now());
            if (!localStorage.getItem('push_notification_shown_' + stockKey)) {
                showPushNotification(backInStockNotification);
                localStorage.setItem('push_notification_shown_' + stockKey, 'true');
            }
        }
    }

    // Show low stock notification if available (admin only)
    if (typeof lowStockNotification !== 'undefined') {
        console.log('Push Notification: Low stock notification found', lowStockNotification);
        var consent = getCookie('push_notification_consent');
        if (consent === 'accepted') {
            console.log('Push Notification: User consented, showing low stock notification');
            var lowStockKey = 'lowstock_' + (lowStockNotification.timestamp || Date.now());
            if (!localStorage.getItem('push_notification_shown_' + lowStockKey)) {
                showPushNotification(lowStockNotification);
                localStorage.setItem('push_notification_shown_' + lowStockKey, 'true');
            }
        }
    }

    // Show review reminder notification if available
    if (typeof reviewReminderNotification !== 'undefined') {
        console.log('Push Notification: Review reminder notification found', reviewReminderNotification);
        var consent = getCookie('push_notification_consent');
        if (consent === 'accepted') {
            console.log('Push Notification: User consented, showing review reminder notification');
            var reviewKey = 'review_' + (reviewReminderNotification.timestamp || Date.now());
            if (!localStorage.getItem('push_notification_shown_' + reviewKey)) {
                showPushNotification(reviewReminderNotification);
                localStorage.setItem('push_notification_shown_' + reviewKey, 'true');
            }
        }
    }

    // Show coupon notification if available
    if (typeof couponNotification !== 'undefined') {
        console.log('Push Notification: Coupon notification found', couponNotification);
        var consent = getCookie('push_notification_consent');
        if (consent === 'accepted') {
            console.log('Push Notification: User consented, showing coupon notification');
            var couponKey = 'coupon_' + (couponNotification.timestamp || Date.now());
            if (!localStorage.getItem('push_notification_shown_' + couponKey)) {
                showPushNotification(couponNotification);
                localStorage.setItem('push_notification_shown_' + couponKey, 'true');
            }
        }
    }

    // Show restock priority notification if available
    if (typeof restockPriorityNotification !== 'undefined') {
        console.log('Push Notification: Restock priority notification found', restockPriorityNotification);
        var consent = getCookie('push_notification_consent');
        if (consent === 'accepted') {
            console.log('Push Notification: User consented, showing restock priority notification');
            var restockKey = 'restock_' + (restockPriorityNotification.timestamp || Date.now());
            if (!localStorage.getItem('push_notification_shown_' + restockKey)) {
                showPushNotification(restockPriorityNotification);
                localStorage.setItem('push_notification_shown_' + restockKey, 'true');
            }
        }
    }

    // Show payment failed notification if available
    if (typeof paymentFailedNotification !== 'undefined') {
        console.log('Push Notification: Payment failed notification found', paymentFailedNotification);
        var consent = getCookie('push_notification_consent');
        if (consent === 'accepted') {
            console.log('Push Notification: User consented, showing payment failed notification');
            var failedKey = 'failed_' + (paymentFailedNotification.timestamp || Date.now());
            if (!localStorage.getItem('push_notification_shown_' + failedKey)) {
                showPushNotification(paymentFailedNotification);
                localStorage.setItem('push_notification_shown_' + failedKey, 'true');
            }
        }
    }

    // Show wishlist alert notification if available
    if (typeof wishlistAlertNotification !== 'undefined') {
        console.log('Push Notification: Wishlist alert notification found', wishlistAlertNotification);
        var consent = getCookie('push_notification_consent');
        if (consent === 'accepted') {
            console.log('Push Notification: User consented, showing wishlist alert notification');
            var wishlistKey = 'wishlist_' + (wishlistAlertNotification.timestamp || Date.now());
            if (!localStorage.getItem('push_notification_shown_' + wishlistKey)) {
                showPushNotification(wishlistAlertNotification);
                localStorage.setItem('push_notification_shown_' + wishlistKey, 'true');
            }
        }
    }

    // Show weather promo notification if available
    if (typeof weatherPromoNotification !== 'undefined') {
        console.log('Push Notification: Weather promo notification found', weatherPromoNotification);
        var consent = getCookie('push_notification_consent');
        if (consent === 'accepted') {
            console.log('Push Notification: User consented, showing weather promo notification');
            var weatherKey = 'weather_' + (weatherPromoNotification.timestamp || Date.now());
            if (!localStorage.getItem('push_notification_shown_' + weatherKey)) {
                showPushNotification(weatherPromoNotification);
                localStorage.setItem('push_notification_shown_' + weatherKey, 'true');
            }
        }
    }

    // Show time offer notification if available
    if (typeof timeOfferNotification !== 'undefined') {
        console.log('Push Notification: Time offer notification found', timeOfferNotification);
        var consent = getCookie('push_notification_consent');
        if (consent === 'accepted') {
            console.log('Push Notification: User consented, showing time offer notification');
            var timeKey = 'time_' + (timeOfferNotification.timestamp || Date.now());
            if (!localStorage.getItem('push_notification_shown_' + timeKey)) {
                showPushNotification(timeOfferNotification);
                localStorage.setItem('push_notification_shown_' + timeKey, 'true');
            }
        }
    }

    // Show holiday notification if available
    if (typeof holidayNotification !== 'undefined') {
        console.log('Push Notification: Holiday notification found', holidayNotification);
        var consent = getCookie('push_notification_consent');
        if (consent === 'accepted') {
            console.log('Push Notification: User consented, showing holiday notification');
            var holidayKey = 'holiday_' + (holidayNotification.timestamp || Date.now());
            if (!localStorage.getItem('push_notification_shown_' + holidayKey)) {
                showPushNotification(holidayNotification);
                localStorage.setItem('push_notification_shown_' + holidayKey, 'true');
            }
        }
    }

    // Listen for WooCommerce AJAX cart add events
    // if (typeof jQuery !== 'undefined') {
    //     jQuery(document).on('added_to_cart', function(event, fragments, cart_hash, button) {
    //         console.log('Push Notification: WooCommerce added_to_cart event detected');
    //         console.log('Push Notification: Full fragments object:', fragments);

    //         // Check if notification data is in fragments
    //         if (fragments && fragments.push_notification) {
    //             console.log('Push Notification: Found notification in AJAX fragments', fragments.push_notification);
    //             var consent = getCookie('push_notification_consent');
    //             console.log('Push Notification: Consent cookie value:', consent);
    //             if (consent === 'accepted') {
    //                 console.log('Push Notification: User consented, showing AJAX cart notification');
    //                 showPushNotification(fragments.push_notification);
    //             } else {
    //                 console.log('Push Notification: User has not consented to notifications');
    //             }
    //         } else {
    //             console.log('Push Notification: No notification data in AJAX fragments');
    //         }
    //     });
    // }

    jQuery(document).on('added_to_cart', function(event, fragments, cart_hash, button) {
        console.log('Push Notification:', typeof jQuery);
        // console.log('fragments.push_notification:', fragments['div.widget_shopping_cart_content']);

        // Check if notification data is in fragments
        if (fragments && fragments.push_notification) {
            console.log('Push Notification: Found notification in AJAX fragments', fragments.push_notification);
            var consent = getCookie('push_notification_consent');
            console.log('Push Notification: Consent cookie value:', consent);
            if (consent === 'accepted') {
                console.log('Push Notification: User consented, showing AJAX cart notification');
                var ajaxNotificationKey = 'ajax_' + (fragments.push_notification.timestamp || Date.now());
                if (!localStorage.getItem('push_notification_shown_' + ajaxNotificationKey)) {
                    showPushNotification(fragments.push_notification);
                    localStorage.setItem('push_notification_shown_' + ajaxNotificationKey, 'true');
                }
            } else {
                console.log('Push Notification: User has not consented to notifications');
            }
        } else {
            console.log('Push Notification: No notification data in AJAX fragments');
        }
    });

    // Handle button clicks for push notifications
    jQuery(document).on('click', '.push-notification-btn', function() {
        var consent = getCookie('push_notification_consent');
        if (consent !== 'accepted') {
            alert('Please accept notifications consent first.');
            return;
        }
        var notificationId = jQuery(this).data('id') || 0;
        var variant = jQuery(this).data('variant') || 'A';
        trackEvent(notificationId, 'button-click', variant);

        var data = {
            title: jQuery(this).data('title'),
            body: jQuery(this).data('body'),
            icon: jQuery(this).data('icon'),
            image: jQuery(this).data('image'),
            actionTitle: jQuery(this).data('action-title'),
            actionUrl: jQuery(this).data('action-url')
        };
        showPushNotification(data, notificationId, variant);
    });


    function getCookie(name) {
        var value = "; " + document.cookie;
        var parts = value.split("; " + name + "=");
        if (parts.length == 2) return parts.pop().split(";").shift();
    }

    function trackEvent(notificationId, eventType, variant) {
        if (!notificationId) return;

        fetch('/wp-json/push-notification/v1/track', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                notification_id: notificationId,
                event_type: eventType,
                variant: variant || 'A',
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

    // Cleanup old notification tracking keys (keep only last 50)
    function cleanupNotificationKeys() {
        var keys = [];
        for (var i = 0; i < localStorage.length; i++) {
            var key = localStorage.key(i);
            if (key && key.indexOf('push_notification_shown_') === 0) {
                keys.push(key);
            }
        }
        // Keep only the last 50 keys
        if (keys.length > 50) {
            keys.sort();
            for (var j = 0; j < keys.length - 50; j++) {
                localStorage.removeItem(keys[j]);
            }
        }
    }
    cleanupNotificationKeys();

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