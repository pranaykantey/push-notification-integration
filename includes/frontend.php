<?php

function push_notification_enqueue_scripts() {
    wp_enqueue_script('push-notification-js', PUSH_NOTIFICATION_PLUGIN_URL . 'js/push-notification.js', array('jquery'), '1.0.0', true);
    wp_localize_script('push-notification-js', 'pushNotificationOptions', array(
        'defaultTitle' => get_option('push_notification_title', 'Push Notification'),
        'defaultBody' => get_option('push_notification_body', 'You have a new message.'),
        'iconUrl' => get_option('push_notification_icon', ''),
    ));

    // Check for recent post notification
    $recent_post_id = get_transient('push_notification_recent_post');
    if ($recent_post_id && get_option('push_notification_auto_new_post')) {
        $post = get_post($recent_post_id);
        if ($post) {
            wp_localize_script('push-notification-js', 'recentPostNotification', array(
                'title' => 'New Post: ' . $post->post_title,
                'body' => wp_trim_words(strip_tags($post->post_content), 20),
                'icon' => get_option('push_notification_icon', ''),
                'action_title' => 'Read More',
                'action_url' => get_permalink($post->ID)
            ));
        }
    }

    // Check for API triggered notification
    $api_data = get_transient('push_notification_api');
    if ($api_data) {
        wp_localize_script('push-notification-js', 'apiNotification', $api_data);
    }
}

function push_notification_add_manifest() {
    echo '<link rel="manifest" href="' . PUSH_NOTIFICATION_PLUGIN_URL . 'manifest.json">';
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

function push_notification_consent_banner() {
    if (!isset($_COOKIE['push_notification_consent'])) {
        echo '<div id="push-notification-consent" style="position:fixed;bottom:0;left:0;right:0;background:#000;color:#fff;padding:10px;text-align:center;z-index:1000;">
            <p>We use push notifications to keep you updated. <button id="accept-notifications" style="background:#007cba;color:#fff;border:none;padding:5px 10px;margin-left:10px;">Accept</button> <button id="decline-notifications" style="background:#ccc;color:#000;border:none;padding:5px 10px;">Decline</button></p>
        </div>';
    }
}

function push_notification_on_publish_post($post_id, $post) {
    if (get_option('push_notification_auto_new_post')) {
        set_transient('push_notification_recent_post', $post_id, 3600);
    }
}