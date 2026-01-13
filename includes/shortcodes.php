<?php

function push_notification_shortcode($atts) {
    $atts = shortcode_atts(array('id' => ''), $atts, 'push_notification');
    if (empty($atts['id']) || !is_numeric($atts['id'])) {
        return '';
    }

    $post = get_post($atts['id']);
    if (!$post || $post->post_type !== 'push_notification') {
        return '';
    }

    $title = $post->post_title;
    $body = get_post_meta($post->ID, '_push_notification_body', true);
    $icon = get_post_meta($post->ID, '_push_notification_icon', true);
    $image = get_post_meta($post->ID, '_push_notification_image', true);
    $action_title = get_post_meta($post->ID, '_push_notification_action_title', true);
    $action_url = get_post_meta($post->ID, '_push_notification_action_url', true);

    return '<button class="push-notification-btn" data-title="' . esc_attr($title) . '" data-body="' . esc_attr($body) . '" data-icon="' . esc_attr($icon) . '" data-image="' . esc_attr($image) . '" data-action-title="' . esc_attr($action_title) . '" data-action-url="' . esc_attr($action_url) . '">Show Notification</button>';
}

function push_notifications_list_shortcode() {
    $args = array(
        'post_type' => 'push_notification',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );
    $notifications = get_posts($args);

    if (empty($notifications)) {
        return '<p>No push notifications found.</p>';
    }

    $output = '<table class="push-notifications-table" style="width:100%; border-collapse:collapse;">';
    $output .= '<thead><tr><th style="border:1px solid #ddd; padding:8px;">Title</th><th style="border:1px solid #ddd; padding:8px;">Body</th><th style="border:1px solid #ddd; padding:8px;">Action</th></tr></thead>';
    $output .= '<tbody>';

    foreach ($notifications as $notification) {
        $title = $notification->post_title;
        $body = get_post_meta($notification->ID, '_push_notification_body', true);
        $icon = get_post_meta($notification->ID, '_push_notification_icon', true);
        $image = get_post_meta($notification->ID, '_push_notification_image', true);
        $action_title = get_post_meta($notification->ID, '_push_notification_action_title', true);
        $action_url = get_post_meta($notification->ID, '_push_notification_action_url', true);

        $output .= '<tr>';
        $output .= '<td style="border:1px solid #ddd; padding:8px;">' . esc_html($title) . '</td>';
        $output .= '<td style="border:1px solid #ddd; padding:8px;">' . esc_html($body) . '</td>';
        $output .= '<td style="border:1px solid #ddd; padding:8px;"><button class="push-notification-btn" data-title="' . esc_attr($title) . '" data-body="' . esc_attr($body) . '" data-icon="' . esc_attr($icon) . '" data-image="' . esc_attr($image) . '" data-action-title="' . esc_attr($action_title) . '" data-action-url="' . esc_attr($action_url) . '">Show Notification</button></td>';
        $output .= '</tr>';
    }

    $output .= '</tbody></table>';

    return $output;
}