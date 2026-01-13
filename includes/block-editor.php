<?php

if (!defined('ABSPATH')) {
    exit;
}

function push_notification_register_block() {
    wp_register_script(
        'push-notification-block',
        PUSH_NOTIFICATION_PLUGIN_URL . 'js/push-notification-block.js',
        array('wp-blocks', 'wp-element', 'wp-editor', 'wp-components'),
        '1.0.0',
        true
    );

    register_block_type('push-notification-integration/notification', array(
        'editor_script' => 'push-notification-block',
        'render_callback' => 'push_notification_render_block',
        'attributes' => array(
            'notificationId' => array(
                'type' => 'number',
                'default' => 0,
            ),
            'buttonText' => array(
                'type' => 'string',
                'default' => 'Show Notification',
            ),
            'roles' => array(
                'type' => 'string',
                'default' => '',
            ),
        ),
    ));
}

function push_notification_render_block($attributes) {
    if (empty($attributes['notificationId'])) {
        return '<p>' . __('Please select a notification in the block settings.', 'push-notification-integration') . '</p>';
    }

    $atts = array(
        'id' => $attributes['notificationId'],
        'roles' => $attributes['roles']
    );

    return push_notification_shortcode($atts);
}

add_action('init', 'push_notification_register_block');