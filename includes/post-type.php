<?php

function push_notification_register_post_type() {
    register_post_type('push_notification', array(
        'label' => 'Push Notifications',
        'public' => false,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_icon' => 'dashicons-bell',
        'supports' => array('title'),
        'labels' => array(
            'name' => 'Push Notifications',
            'singular_name' => 'Push Notification',
            'add_new' => 'Add New',
            'add_new_item' => 'Add New Push Notification',
            'edit_item' => 'Edit Push Notification',
            'new_item' => 'New Push Notification',
            'view_item' => 'View Push Notification',
            'search_items' => 'Search Push Notifications',
            'not_found' => 'No push notifications found',
            'not_found_in_trash' => 'No push notifications found in trash',
        ),
    ));
}

function push_notification_add_meta_boxes() {
    add_meta_box('push_notification_details', 'Notification Details', 'push_notification_meta_box_callback', 'push_notification', 'normal', 'high');
}

function push_notification_meta_box_callback($post) {
    wp_nonce_field('push_notification_meta_box', 'push_notification_meta_box_nonce');

    $body = get_post_meta($post->ID, '_push_notification_body', true);
    $icon = get_post_meta($post->ID, '_push_notification_icon', true);

    echo '<p><label for="push_notification_body">Body:</label></p>';
    echo '<textarea id="push_notification_body" name="push_notification_body" rows="3" style="width:100%;">' . esc_textarea($body) . '</textarea>';

    echo '<p><label for="push_notification_icon">Icon URL:</label></p>';
    echo '<input type="url" id="push_notification_icon" name="push_notification_icon" value="' . esc_attr($icon) . '" style="width:100%;" />';
}

function push_notification_save_meta_boxes($post_id) {
    if (!isset($_POST['push_notification_meta_box_nonce']) || !wp_verify_nonce($_POST['push_notification_meta_box_nonce'], 'push_notification_meta_box')) {
        return;
    }

    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['push_notification_body'])) {
        update_post_meta($post_id, '_push_notification_body', sanitize_textarea_field($_POST['push_notification_body']));
    }

    if (isset($_POST['push_notification_icon'])) {
        update_post_meta($post_id, '_push_notification_icon', esc_url_raw($_POST['push_notification_icon']));
    }
}

function push_notification_add_shortcode_column($columns) {
    $columns['shortcode'] = 'Shortcode';
    return $columns;
}

function push_notification_shortcode_column_content($column, $post_id) {
    if ($column === 'shortcode') {
        echo '<code>[push_notification id="' . $post_id . '"]</code>';
    }
}