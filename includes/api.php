<?php

function push_notification_register_api() {
    register_rest_route('push-notification/v1', '/send', array(
        'methods' => 'POST',
        'callback' => 'push_notification_send_api',
        'permission_callback' => function() { return current_user_can('manage_options'); },
        'args' => array(
            'title' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'body' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_textarea_field'
            ),
            'icon' => array(
                'sanitize_callback' => 'esc_url_raw'
            ),
            'image' => array(
                'sanitize_callback' => 'esc_url_raw'
            ),
            'action_title' => array(
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'action_url' => array(
                'sanitize_callback' => 'esc_url_raw'
            )
        )
    ));

    register_rest_route('push-notification/v1', '/track', array(
        'methods' => 'POST',
        'callback' => 'push_notification_track_api',
        'permission_callback' => '__return_true', // Allow tracking from frontend
        'args' => array(
            'notification_id' => array(
                'required' => true,
                'sanitize_callback' => 'intval'
            ),
            'event_type' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'session_id' => array(
                'sanitize_callback' => 'sanitize_text_field'
            )
        )
    ));

    register_rest_route('push-notification/v1', '/email-fallback', array(
        'methods' => 'POST',
        'callback' => 'push_notification_email_fallback_api',
        'permission_callback' => 'is_user_logged_in', // Only for logged-in users
        'args' => array(
            'notification_id' => array(
                'required' => true,
                'sanitize_callback' => 'intval'
            )
        )
    ));

    register_rest_route('push-notification/v1', '/consent', array(
        'methods' => 'POST',
        'callback' => 'push_notification_consent_api',
        'permission_callback' => 'is_user_logged_in', // Only for logged-in users
    ));
}

function push_notification_send_api($request) {
    $params = $request->get_params();
    $data = array(
        'title' => $params['title'],
        'body' => $params['body'],
        'icon' => $params['icon'] ?: '',
        'image' => $params['image'] ?: '',
        'action_title' => $params['action_title'] ?: '',
        'action_url' => $params['action_url'] ?: ''
    );

    // Set transient for API triggered notification
    set_transient('push_notification_api', $data, 300); // 5 minutes

    // Trigger hook for developers
    do_action('push_notification_triggered', $data);

    return array('success' => true, 'message' => 'Notification queued');
}

// WooCommerce integration
if (class_exists('WooCommerce')) {
    add_action('woocommerce_order_status_changed', 'push_notification_order_status_changed', 10, 4);
}

function push_notification_order_status_changed($order_id, $old_status, $new_status, $order) {
    if ($new_status === 'completed') {
        $data = array(
            'title' => 'Order Completed',
            'body' => 'Your order #' . $order_id . ' has been completed.',
            'icon' => get_option('push_notification_icon', ''),
            'action_title' => 'View Order',
            'action_url' => $order->get_view_order_url()
        );

        set_transient('push_notification_api', $data, 300);
        do_action('push_notification_triggered', $data);
    }
}

// Hook for custom triggers
function push_notification_track_api($request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'push_notification_analytics';

    $params = $request->get_params();

    $wpdb->insert(
        $table_name,
        array(
            'notification_id' => $params['notification_id'],
            'event_type' => $params['event_type'],
            'user_id' => get_current_user_id(),
            'user_ip' => push_notification_get_user_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'session_id' => $params['session_id'] ?? '',
            'variant' => $params['variant'] ?? 'A',
        ),
        array('%d', '%s', '%d', '%s', '%s', '%s', '%s')
    );

    return array('success' => true);
}

function push_notification_email_fallback_api($request) {
    $params = $request->get_params();
    push_notification_send_email_fallback($params['notification_id']);
    return array('success' => true);
}

function push_notification_consent_api($request) {
    $user_id = get_current_user_id();
    if (!$user_id) {
        return new WP_Error('not_logged_in', 'User must be logged in', array('status' => 401));
    }

    // Sync to CRM
    push_notification_sync_to_crm($user_id);

    return array('success' => true);
}

function push_notification_sync_to_crm($user_id) {
    $api_key = get_option('push_notification_mailchimp_api_key');
    $list_id = get_option('push_notification_mailchimp_list_id');

    if (!$api_key || !$list_id) {
        return;
    }

    $user = get_user_by('id', $user_id);
    if (!$user) return;

    $data_center = substr($api_key, strpos($api_key, '-') + 1);
    $url = 'https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/';

    $member_id = md5(strtolower($user->user_email));

    $data = array(
        'email_address' => $user->user_email,
        'status' => 'subscribed',
        'merge_fields' => array(
            'FNAME' => $user->first_name,
            'LNAME' => $user->last_name
        )
    );

    $response = wp_remote_post($url . $member_id, array(
        'method' => 'PUT',
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode('user:' . $api_key),
            'Content-Type' => 'application/json'
        ),
        'body' => json_encode($data)
    ));

    if (is_wp_error($response)) {
        error_log('Mailchimp sync error: ' . $response->get_error_message());
    }
}

function push_notification_send_email_fallback($notification_id, $user_id = null) {
    if (!get_option('push_notification_email_fallback')) {
        return;
    }

    $user_id = $user_id ?: get_current_user_id();
    if (!$user_id) {
        return; // Only for logged-in users
    }

    $user = get_user_by('id', $user_id);
    if (!$user) {
        return;
    }

    $post = get_post($notification_id);
    if (!$post) {
        return;
    }

    $title = $post->post_title;
    $body = get_post_meta($post->ID, '_push_notification_body', true);
    $icon = get_post_meta($post->ID, '_push_notification_icon', true);
    $action_url = get_post_meta($post->ID, '_push_notification_action_url', true);

    $subject = 'Push Notification: ' . $title;
    $message = "You have a new notification:\n\n";
    $message .= "Title: $title\n";
    $message .= "Message: $body\n";
    if ($action_url) {
        $message .= "Action: $action_url\n";
    }

    wp_mail($user->user_email, $subject, $message);
}

function push_notification_get_user_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }
}

function push_notification_trigger($data) {
    set_transient('push_notification_api', $data, 300);
    do_action('push_notification_triggered', $data);
}