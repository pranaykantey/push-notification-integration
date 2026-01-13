<?php

function push_notification_enqueue_scripts() {
    wp_enqueue_script('push-notification-js', PUSH_NOTIFICATION_PLUGIN_URL . 'js/push-notification.js', array('jquery'), '1.0.0', true);
    wp_localize_script('push-notification-js', 'pushNotificationOptions', array(
        'defaultTitle' => get_option('push_notification_title', 'Push Notification'),
        'defaultBody' => get_option('push_notification_body', 'You have a new message.'),
        'iconUrl' => get_option('push_notification_icon', ''),
    ));
}

function push_notification_add_service_worker() {
    echo '<script>
        if ("serviceWorker" in navigator) {
            navigator.serviceWorker.register("' . PUSH_NOTIFICATION_PLUGIN_URL . 'sw.js")
            .then(function(registration) {
                console.log("Service Worker registered with scope:", registration.scope);
            }).catch(function(error) {
                console.log("Service Worker registration failed:", error);
            });
        }
    </script>';
}