<?php

class Push_Notification_Integration {

    public function __construct() {
        add_action('wp_enqueue_scripts', 'push_notification_enqueue_scripts');
        add_action('wp_head', 'push_notification_add_manifest');
        add_action('wp_footer', 'push_notification_add_service_worker');
        add_action('wp_footer', 'push_notification_consent_banner');
        add_action('publish_post', 'push_notification_on_publish_post', 10, 2);
        add_action('admin_menu', 'push_notification_add_admin_menu');
        add_action('admin_init', 'push_notification_register_settings');
        add_action('init', 'push_notification_register_post_type');
        add_action('add_meta_boxes', 'push_notification_add_meta_boxes');
        add_action('save_post', 'push_notification_save_meta_boxes');
        add_shortcode('push_notification', 'push_notification_shortcode');
        add_shortcode('push_notifications_list', 'push_notifications_list_shortcode');
        add_filter('manage_push_notification_posts_columns', 'push_notification_add_shortcode_column');
        add_action('manage_push_notification_posts_custom_column', 'push_notification_shortcode_column_content', 10, 2);
    }

}