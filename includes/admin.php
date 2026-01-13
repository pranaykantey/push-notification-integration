<?php

function push_notification_add_admin_menu() {
    add_options_page('Push Notification Settings', 'Push Notifications', 'manage_options', 'push-notification-settings', 'push_notification_settings_page');
}

function push_notification_register_settings() {
    register_setting('push_notification_settings', 'push_notification_title');
    register_setting('push_notification_settings', 'push_notification_body');
    register_setting('push_notification_settings', 'push_notification_icon');

    add_settings_section('push_notification_main', 'Main Settings', null, 'push-notification-settings');

    add_settings_field('push_notification_title', 'Default Notification Title', 'push_notification_title_field', 'push-notification-settings', 'push_notification_main');
    add_settings_field('push_notification_body', 'Default Notification Body', 'push_notification_body_field', 'push-notification-settings', 'push_notification_main');
    add_settings_field('push_notification_icon', 'Notification Icon URL', 'push_notification_icon_field', 'push-notification-settings', 'push_notification_main');
}

function push_notification_settings_page() {
    ?>
    <div class="wrap">
        <h1>Push Notification Settings</h1>
        <form method="post" action="options.php">
            <?php
            settings_fields('push_notification_settings');
            do_settings_sections('push-notification-settings');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

function push_notification_title_field() {
    $value = get_option('push_notification_title', 'Push Notification');
    echo '<input type="text" name="push_notification_title" value="' . esc_attr($value) . '" class="regular-text" />';
}

function push_notification_body_field() {
    $value = get_option('push_notification_body', 'You have a new message.');
    echo '<input type="text" name="push_notification_body" value="' . esc_attr($value) . '" class="regular-text" />';
}

function push_notification_icon_field() {
    $value = get_option('push_notification_icon', '');
    echo '<input type="url" name="push_notification_icon" value="' . esc_attr($value) . '" class="regular-text" />';
}