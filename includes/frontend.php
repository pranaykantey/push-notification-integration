<?php

function push_notification_enqueue_scripts() {
    wp_enqueue_style('push-notification-css', PUSH_NOTIFICATION_PLUGIN_URL . 'css/push-notification.css', array(), '1.0.0');
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
        if ($post && push_notification_should_show_post_notification($post)) {
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
        delete_transient('push_notification_api'); // Delete after reading to prevent duplicate notifications
    }

    // Check for user-specific cart add notification
    $cart_notification = null;
    $user_id = get_current_user_id();
    if ($user_id) {
        $cart_notification = get_user_meta($user_id, '_push_notification_cart_add', true);
        if ($cart_notification) {
            delete_user_meta($user_id, '_push_notification_cart_add');
            error_log('Push Notification: Retrieved cart notification from user meta for user ' . $user_id);
        }
    } else {
        if (!session_id()) {
            session_start();
        }
        if (isset($_SESSION['push_notification_cart_add'])) {
            $cart_notification = $_SESSION['push_notification_cart_add'];
            unset($_SESSION['push_notification_cart_add']);
            error_log('Push Notification: Retrieved cart notification from session for anonymous user');
        }
    }

    if ($cart_notification) {
        wp_localize_script('push-notification-js', 'cartAddNotification', $cart_notification);
        error_log('Push Notification: Localized cart notification for JavaScript');
    }

    // Check for abandoned cart notification
    $user_id = get_current_user_id();
    $abandoned_cart_notification = null;
    
    if ($user_id) {
        $abandoned_cart_notification = get_transient('push_notification_abandoned_cart_' . $user_id);
    } else {
        if (!session_id()) {
            session_start();
        }
        $session_id = session_id();
        if ($session_id) {
            $abandoned_cart_notification = get_transient('push_notification_abandoned_cart_session_' . $session_id);
        }
    }
    
    if ($abandoned_cart_notification) {
        wp_localize_script('push-notification-js', 'abandonedCartNotification', $abandoned_cart_notification);
        if ($user_id) {
            delete_transient('push_notification_abandoned_cart_' . $user_id);
        } else {
            if (!session_id()) {
                session_start();
            }
            $session_id = session_id();
            delete_transient('push_notification_abandoned_cart_session_' . $session_id);
        }
    }

    // Check for price drop notification
    if ($user_id && get_option('push_notification_woocommerce_price_drop', '0')) {
        $price_drop_notification = get_transient('push_notification_price_drop_' . $user_id);
        if ($price_drop_notification) {
            wp_localize_script('push-notification-js', 'priceDropNotification', $price_drop_notification);
            delete_transient('push_notification_price_drop_' . $user_id);
        }
    }

    // Check for back in stock notification
    if ($user_id && get_option('push_notification_woocommerce_back_in_stock', '0')) {
        $back_in_stock_notification = get_transient('push_notification_back_in_stock_' . $user_id);
        if ($back_in_stock_notification) {
            wp_localize_script('push-notification-js', 'backInStockNotification', $back_in_stock_notification);
            delete_transient('push_notification_back_in_stock_' . $user_id);
        }
    }

    // Check for low stock alert (for admins only)
    if ($user_id && current_user_can('manage_options') && get_option('push_notification_woocommerce_low_stock', '0')) {
        $low_stock_notification = get_transient('push_notification_low_stock_' . $user_id);
        if ($low_stock_notification) {
            wp_localize_script('push-notification-js', 'lowStockNotification', $low_stock_notification);
            delete_transient('push_notification_low_stock_' . $user_id);
        }
    }

    // Check for review reminder notification
    if ($user_id && get_option('push_notification_woocommerce_review_reminder', '0')) {
        $review_reminder_notification = get_transient('push_notification_review_reminder_' . $user_id);
        if ($review_reminder_notification) {
            wp_localize_script('push-notification-js', 'reviewReminderNotification', $review_reminder_notification);
            delete_transient('push_notification_review_reminder_' . $user_id);
        }
    }

    // Check for coupon notification
    $coupon_notification = get_transient('push_notification_coupon');
    if ($coupon_notification && get_option('push_notification_woocommerce_coupon', '0')) {
        wp_localize_script('push-notification-js', 'couponNotification', $coupon_notification);
        delete_transient('push_notification_coupon');
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

function push_notification_should_show_post_notification($post) {
    $user_id = get_current_user_id();

    // Check if user has consented to notifications
    if (!isset($_COOKIE['push_notification_consent']) || $_COOKIE['push_notification_consent'] !== 'accepted') {
        return false;
    }

    // Check post type filtering
    $allowed_types = array_map('trim', explode(',', get_option('push_notification_post_types', 'post')));
    if (!in_array($post->post_type, $allowed_types)) {
        return false;
    }

    // Exclude post author if setting is enabled
    if (get_option('push_notification_post_exclude_author') && $user_id == $post->post_author) {
        return false;
    }

    // Check role targeting
    $target_roles = get_option('push_notification_post_target_roles', '');
    if (!empty($target_roles)) {
        $target_roles = array_map('trim', explode(',', $target_roles));
        $user = wp_get_current_user();
        $user_roles = $user->roles;
        $has_target_role = false;

        foreach ($target_roles as $role) {
            if (in_array($role, $user_roles)) {
                $has_target_role = true;
                break;
            }
        }

        if (!$has_target_role) {
            return false;
        }
    }

    return true;
}

function push_notification_on_publish_post($post_id, $post) {
    if (get_option('push_notification_auto_new_post')) {
        // Check post type filtering before setting transient
        $allowed_types = array_map('trim', explode(',', get_option('push_notification_post_types', 'post')));
        if (in_array($post->post_type, $allowed_types)) {
            set_transient('push_notification_recent_post', $post_id, 3600);
        }
    }
}