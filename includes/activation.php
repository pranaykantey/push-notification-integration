<?php

function push_notification_activation() {
    // Create analytics table
    global $wpdb;
    $table_name = $wpdb->prefix . 'push_notification_analytics';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        notification_id bigint(20) NOT NULL,
        event_type varchar(50) NOT NULL,
        user_id bigint(20) DEFAULT 0,
        user_ip varchar(45) DEFAULT '',
        user_agent text DEFAULT '',
        timestamp datetime DEFAULT CURRENT_TIMESTAMP,
        session_id varchar(100) DEFAULT '',
        PRIMARY KEY (id),
        KEY notification_id (notification_id),
        KEY event_type (event_type),
        KEY timestamp (timestamp)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

function push_notification_deactivation() {
    // Add deactivation code here if needed
}