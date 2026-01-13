<?php

function push_notification_add_admin_menu() {
    add_options_page('Push Notification Settings', 'Push Notifications', 'manage_options', 'push-notification-settings', 'push_notification_settings_page');
    add_submenu_page('edit.php?post_type=push_notification', 'Analytics', 'Analytics', 'manage_options', 'push-notification-analytics', 'push_notification_analytics_page');
}

function push_notification_register_settings() {
    register_setting('push_notification_settings', 'push_notification_title');
    register_setting('push_notification_settings', 'push_notification_body');
    register_setting('push_notification_settings', 'push_notification_icon');
    register_setting('push_notification_settings', 'push_notification_auto_new_post');

    add_settings_section('push_notification_main', 'Main Settings', null, 'push-notification-settings');

    add_settings_field('push_notification_title', 'Default Notification Title', 'push_notification_title_field', 'push-notification-settings', 'push_notification_main');
    add_settings_field('push_notification_body', 'Default Notification Body', 'push_notification_body_field', 'push-notification-settings', 'push_notification_main');
    add_settings_field('push_notification_icon', 'Notification Icon URL', 'push_notification_icon_field', 'push-notification-settings', 'push_notification_main');
    add_settings_field('push_notification_auto_new_post', 'Auto-show notification for new posts', 'push_notification_auto_new_post_field', 'push-notification-settings', 'push_notification_main');
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

function push_notification_analytics_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'push_notification_analytics';

    // Get date range
    $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : date('Y-m-d', strtotime('-30 days'));
    $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : date('Y-m-d');

    // Get stats
    $total_clicks = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE event_type = 'button_click' AND DATE(timestamp) BETWEEN %s AND %s", $start_date, $end_date));
    $total_shows = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE event_type = 'notification_shown' AND DATE(timestamp) BETWEEN %s AND %s", $start_date, $end_date));
    $total_actions = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE event_type = 'action_click' AND DATE(timestamp) BETWEEN %s AND %s", $start_date, $end_date));

    $delivery_rate = $total_clicks > 0 ? round(($total_shows / $total_clicks) * 100, 2) : 0;
    $ctr = $total_shows > 0 ? round(($total_actions / $total_shows) * 100, 2) : 0;

    ?>
    <div class="wrap">
        <h1>Push Notification Analytics</h1>

        <form method="get" action="">
            <input type="hidden" name="post_type" value="push_notification">
            <input type="hidden" name="page" value="push-notification-analytics">
            <label>Start Date: <input type="date" name="start_date" value="<?php echo esc_attr($start_date); ?>"></label>
            <label>End Date: <input type="date" name="end_date" value="<?php echo esc_attr($end_date); ?>"></label>
            <input type="submit" value="Filter" class="button">
        </form>

        <div class="analytics-cards" style="display: flex; gap: 20px; margin: 20px 0;">
            <div class="card" style="background: #fff; border: 1px solid #ddd; padding: 20px; border-radius: 5px; flex: 1;">
                <h3>Total Button Clicks</h3>
                <p style="font-size: 24px; font-weight: bold;"><?php echo $total_clicks; ?></p>
            </div>
            <div class="card" style="background: #fff; border: 1px solid #ddd; padding: 20px; border-radius: 5px; flex: 1;">
                <h3>Notifications Shown</h3>
                <p style="font-size: 24px; font-weight: bold;"><?php echo $total_shows; ?></p>
            </div>
            <div class="card" style="background: #fff; border: 1px solid #ddd; padding: 20px; border-radius: 5px; flex: 1;">
                <h3>Action Clicks</h3>
                <p style="font-size: 24px; font-weight: bold;"><?php echo $total_actions; ?></p>
            </div>
            <div class="card" style="background: #fff; border: 1px solid #ddd; padding: 20px; border-radius: 5px; flex: 1;">
                <h3>Delivery Rate</h3>
                <p style="font-size: 24px; font-weight: bold;"><?php echo $delivery_rate; ?>%</p>
            </div>
            <div class="card" style="background: #fff; border: 1px solid #ddd; padding: 20px; border-radius: 5px; flex: 1;">
                <h3>Click-Through Rate</h3>
                <p style="font-size: 24px; font-weight: bold;"><?php echo $ctr; ?>%</p>
            </div>
        </div>

        <h2>Top Performing Notifications</h2>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Notification</th>
                    <th>Clicks</th>
                    <th>Shows</th>
                    <th>Actions</th>
                    <th>CTR</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $notifications = $wpdb->get_results($wpdb->prepare("
                    SELECT
                        p.ID,
                        p.post_title,
                        COUNT(CASE WHEN a.event_type = 'button_click' THEN 1 END) as clicks,
                        COUNT(CASE WHEN a.event_type = 'notification_shown' THEN 1 END) as shows,
                        COUNT(CASE WHEN a.event_type = 'action_click' THEN 1 END) as actions
                    FROM {$wpdb->posts} p
                    LEFT JOIN $table_name a ON p.ID = a.notification_id AND DATE(a.timestamp) BETWEEN %s AND %s
                    WHERE p.post_type = 'push_notification' AND p.post_status = 'publish'
                    GROUP BY p.ID
                    ORDER BY clicks DESC
                    LIMIT 10
                ", $start_date, $end_date));

                foreach ($notifications as $notification) {
                    $ctr = $notification->shows > 0 ? round(($notification->actions / $notification->shows) * 100, 2) : 0;
                    echo '<tr>';
                    echo '<td><a href="' . get_edit_post_link($notification->ID) . '">' . esc_html($notification->post_title) . '</a></td>';
                    echo '<td>' . $notification->clicks . '</td>';
                    echo '<td>' . $notification->shows . '</td>';
                    echo '<td>' . $notification->actions . '</td>';
                    echo '<td>' . $ctr . '%</td>';
                    echo '</tr>';
                }
                ?>
            </tbody>
        </table>
    </div>
    <?php
}

function push_notification_auto_new_post_field() {
    $value = get_option('push_notification_auto_new_post', '');
    echo '<input type="checkbox" name="push_notification_auto_new_post" value="1" ' . checked(1, $value, false) . ' /> Enable automatic notifications for new posts';
}