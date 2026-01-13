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
function push_notification_trigger($data) {
    set_transient('push_notification_api', $data, 300);
    do_action('push_notification_triggered', $data);
}