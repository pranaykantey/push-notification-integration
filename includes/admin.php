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
    register_setting('push_notification_settings', 'push_notification_email_fallback');
    register_setting('push_notification_settings', 'push_notification_supported_languages');
    register_setting('push_notification_settings', 'push_notification_woocommerce_cart_add');
    register_setting('push_notification_settings', 'push_notification_woocommerce_order_completed');
    register_setting('push_notification_settings', 'push_notification_woocommerce_order_received');
    register_setting('push_notification_settings', 'push_notification_abandoned_cart');
    register_setting('push_notification_settings', 'push_notification_abandoned_cart_delay');
    register_setting('push_notification_settings', 'push_notification_woocommerce_order_shipped');
    register_setting('push_notification_settings', 'push_notification_woocommerce_price_drop');
    register_setting('push_notification_settings', 'push_notification_woocommerce_back_in_stock');
    register_setting('push_notification_settings', 'push_notification_woocommerce_low_stock');
    register_setting('push_notification_settings', 'push_notification_post_exclude_author');
    register_setting('push_notification_settings', 'push_notification_post_target_roles');
    register_setting('push_notification_settings', 'push_notification_post_types');

    add_settings_section('push_notification_main', 'Main Settings', null, 'push-notification-settings');

    add_settings_field('push_notification_title', 'Default Notification Title', 'push_notification_title_field', 'push-notification-settings', 'push_notification_main');
    add_settings_field('push_notification_body', 'Default Notification Body', 'push_notification_body_field', 'push-notification-settings', 'push_notification_main');
    add_settings_field('push_notification_icon', 'Notification Icon URL', 'push_notification_icon_field', 'push-notification-settings', 'push-notification_main');
    add_settings_field('push_notification_auto_new_post', 'Auto-show notification for new posts', 'push_notification_auto_new_post_field', 'push-notification-settings', 'push_notification_main');
    add_settings_field('push_notification_post_exclude_author', 'Exclude post author from notifications', 'push_notification_post_exclude_author_field', 'push-notification-settings', 'push_notification_main');
    add_settings_field('push_notification_post_target_roles', 'Target specific user roles (leave empty for all)', 'push_notification_post_target_roles_field', 'push-notification-settings', 'push_notification_main');
    add_settings_field('push_notification_post_types', 'Post types to notify about (comma separated)', 'push_notification_post_types_field', 'push-notification-settings', 'push_notification_main');
    add_settings_field('push_notification_email_fallback', 'Email fallback for failed notifications', 'push_notification_email_fallback_field', 'push-notification-settings', 'push_notification_main');
    add_settings_field('push_notification_supported_languages', 'Supported Languages (comma separated)', 'push_notification_supported_languages_field', 'push-notification-settings', 'push_notification_main');

    add_settings_section('push_notification_automation', 'Automation Settings', null, 'push-notification-settings');
    add_settings_field('push_notification_woocommerce_cart_add', 'WooCommerce - Notify on cart add', 'push_notification_woocommerce_cart_add_field', 'push-notification-settings', 'push_notification_automation');
    add_settings_field('push_notification_woocommerce_order_completed', 'WooCommerce - Notify on order completed', 'push_notification_woocommerce_order_completed_field', 'push-notification-settings', 'push_notification_automation');
    add_settings_field('push_notification_woocommerce_order_received', 'WooCommerce - Notify on order received', 'push_notification_woocommerce_order_received_field', 'push-notification-settings', 'push_notification_automation');
    add_settings_field('push_notification_abandoned_cart', 'WooCommerce - Abandoned Cart Reminder', 'push_notification_abandoned_cart_field', 'push-notification-settings', 'push_notification_automation');
    add_settings_field('push_notification_abandoned_cart_delay', 'Abandoned Cart Delay (hours)', 'push_notification_abandoned_cart_delay_field', 'push-notification-settings', 'push_notification_automation');
    add_settings_field('push_notification_woocommerce_order_shipped', 'WooCommerce - Notify on order shipped', 'push_notification_woocommerce_order_shipped_field', 'push-notification-settings', 'push_notification_automation');
    add_settings_field('push_notification_woocommerce_price_drop', 'WooCommerce - Price Drop Alert', 'push_notification_woocommerce_price_drop_field', 'push-notification-settings', 'push_notification_automation');
    add_settings_field('push_notification_woocommerce_back_in_stock', 'WooCommerce - Back in Stock', 'push_notification_woocommerce_back_in_stock_field', 'push-notification-settings', 'push_notification_automation');
    add_settings_field('push_notification_woocommerce_low_stock', 'WooCommerce - Low Stock Alert (Admin)', 'push_notification_woocommerce_low_stock_field', 'push-notification-settings', 'push_notification_automation');

    add_settings_section('push_notification_crm', 'CRM Integration', null, 'push-notification-settings');
    add_settings_field('push_notification_mailchimp_api_key', 'Mailchimp API Key', 'push_notification_mailchimp_api_key_field', 'push-notification-settings', 'push_notification_crm');
    add_settings_field('push_notification_mailchimp_list_id', 'Mailchimp Audience ID', 'push_notification_mailchimp_list_id_field', 'push-notification-settings', 'push_notification_crm');
}

function push_notification_settings_page() {
    ?>
    <div class="wrap push-notification-admin">
        <div class="push-notification-header">
            <div class="push-notification-logo">
                <span class="dashicons dashicons-bell"></span>
                <h1>Push Notification Integration</h1>
            </div>
            <div class="push-notification-version">
                <span class="version">Version 2.1.0</span>
            </div>
        </div>

        <div class="push-notification-nav">
            <a href="#main-settings" class="nav-tab nav-tab-active">Main Settings</a>
            <a href="#automation" class="nav-tab">Automation</a>
            <a href="#crm" class="nav-tab">CRM Integration</a>
        </div>

        <form method="post" action="options.php" class="push-notification-form">
            <?php
            settings_fields('push_notification_settings');
            do_settings_sections('push-notification-settings');
            submit_button('Save Settings', 'primary', 'submit', false, array('class' => 'push-notification-save-btn'));
            ?>
        </form>

        <div class="push-notification-footer">
            <p>
                <strong>Need help?</strong>
                <a href="#" target="_blank">📖 Documentation</a> |
                <a href="#" target="_blank">🎥 Video Tutorials</a> |
                <a href="#" target="_blank">💬 Support</a>
            </p>
        </div>
    </div>

    <style>
    .push-notification-admin {
        max-width: 1200px;
    }

    .push-notification-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .push-notification-logo {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    .push-notification-logo .dashicons {
        font-size: 40px;
        width: 40px;
        height: 40px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        padding: 8px;
    }

    .push-notification-logo h1 {
        margin: 0;
        font-size: 28px;
        font-weight: 300;
    }

    .push-notification-version {
        text-align: right;
    }

    .version {
        background: rgba(255,255,255,0.2);
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 14px;
    }

    .push-notification-nav {
        margin-bottom: 30px;
        border-bottom: 1px solid #ccc;
    }

    .push-notification-nav .nav-tab {
        background: #f1f1f1;
        border: none;
        padding: 12px 24px;
        margin-right: 5px;
        border-radius: 6px 6px 0 0;
        text-decoration: none;
        color: #666;
        transition: all 0.3s ease;
    }

    .push-notification-nav .nav-tab-active {
        background: #fff;
        color: #007cba;
        border-bottom: 3px solid #007cba;
        margin-bottom: -1px;
    }

    .push-notification-form {
        background: #fff;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 30px;
    }

    .push-notification-save-btn {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        padding: 12px 30px;
        border-radius: 6px;
        color: white;
        font-size: 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    }

    .push-notification-save-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
    }

    .push-notification-footer {
        text-align: center;
        padding: 20px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    }

    .push-notification-footer a {
        color: #007cba;
        text-decoration: none;
        margin: 0 10px;
        font-weight: 500;
    }

    .push-notification-footer a:hover {
        text-decoration: underline;
    }

    /* Form styling */
    .form-table th {
        width: 250px;
        padding: 20px 0 20px 0;
        font-weight: 600;
        color: #333;
    }

    .form-table td {
        padding: 15px 0;
    }

    .regular-text {
        width: 400px;
        max-width: 100%;
        padding: 8px 12px;
        border: 2px solid #e1e1e1;
        border-radius: 4px;
        transition: border-color 0.3s ease;
    }

    .regular-text:focus {
        border-color: #007cba;
        outline: none;
    }

    /* Section styling */
    .push-notification-form h2 {
        color: #333;
        font-size: 18px;
        font-weight: 600;
        margin: 40px 0 20px 0;
        padding-bottom: 10px;
        border-bottom: 2px solid #007cba;
    }

    /* Checkbox styling */
    input[type="checkbox"] {
        width: 18px;
        height: 18px;
        margin-right: 10px;
    }

    /* Description styling */
    .description {
        color: #666;
        font-style: italic;
        margin-top: 5px;
        display: block;
    }
    </style>

    <script>
    jQuery(document).ready(function($) {
        // Tab navigation
        $('.push-notification-nav .nav-tab').on('click', function(e) {
            e.preventDefault();
            var target = $(this).attr('href');

            $('.push-notification-nav .nav-tab').removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active');

            // Scroll to section
            if ($(target).length) {
                $('html, body').animate({
                    scrollTop: $(target).offset().top - 100
                }, 500);
            }
        });
    });
    </script>
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

    // Check if table exists
    if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
        echo '<div class="notice notice-error"><p>Analytics table not found. Please deactivate and reactivate the plugin to create the table.</p></div>';
        return;
    }

    // Get date range
    $start_date = isset($_GET['start_date']) ? sanitize_text_field($_GET['start_date']) : date('Y-m-d', strtotime('-30 days'));
    $end_date = isset($_GET['end_date']) ? sanitize_text_field($_GET['end_date']) : date('Y-m-d');

    // Get stats with error handling
    $total_clicks = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE event_type = 'button-click' AND DATE(timestamp) BETWEEN %s AND %s", $start_date, $end_date));
    if ($total_clicks === null) $total_clicks = 0;

    $total_shows = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE event_type = 'notification-shown' AND DATE(timestamp) BETWEEN %s AND %s", $start_date, $end_date));
    if ($total_shows === null) $total_shows = 0;

    $total_actions = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table_name WHERE event_type = 'action-click' AND DATE(timestamp) BETWEEN %s AND %s", $start_date, $end_date));
    if ($total_actions === null) $total_actions = 0;

    // Fix delivery rate calculation: (notifications shown / button clicks) * 100
    $delivery_rate = $total_clicks > 0 ? round(($total_shows / $total_clicks) * 100, 2) : 0;
    $ctr = $total_shows > 0 ? round(($total_actions / $total_shows) * 100, 2) : 0;

    // Debug logging
    error_log("Analytics Debug: Clicks: $total_clicks, Shows: $total_shows, Actions: $total_actions, Delivery: $delivery_rate, CTR: $ctr");

    // Check if we should add sample data for demo
    if (isset($_GET['add_sample_data']) && current_user_can('manage_options')) {
        push_notification_add_sample_analytics_data();
        echo '<div class="notice notice-success"><p>Sample analytics data added successfully!</p></div>';
    }

    ?>
    <div class="wrap">
        <h1>Push Notification Analytics</h1>

        <div style="margin-bottom: 20px;">
            <a href="<?php echo add_query_arg('add_sample_data', '1'); ?>" class="button button-secondary" onclick="return confirm('This will add sample analytics data for demonstration. Continue?')">Add Sample Data</a>
            <p class="description">Click to add sample analytics data so you can see how the dashboard works.</p>
        </div>

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

        <h2>A/B Testing Results</h2>
        <?php
        // Check if there are any notifications with variants
        $has_variants = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key LIKE '_push_notification_%_b'");

        if ($has_variants > 0) {
            $ab_results = $wpdb->get_results($wpdb->prepare("
                SELECT
                    p.ID,
                    p.post_title,
                    a.variant,
                    COUNT(CASE WHEN a.event_type = 'button-click' THEN 1 END) as clicks,
                    COUNT(CASE WHEN a.event_type = 'notification-shown' THEN 1 END) as shows,
                    COUNT(CASE WHEN a.event_type = 'action-click' THEN 1 END) as actions
                FROM {$wpdb->posts} p
                LEFT JOIN $table_name a ON p.ID = a.notification_id AND DATE(a.timestamp) BETWEEN %s AND %s
                WHERE p.post_type = 'push_notification' AND p.post_status = 'publish' AND a.variant IS NOT NULL
                GROUP BY p.ID, a.variant
                ORDER BY p.ID, a.variant
                LIMIT 20
            ", $start_date, $end_date));

            if (!empty($ab_results)) {
                ?>
                <table class="wp-list-table widefat fixed striped">
                    <thead>
                        <tr>
                            <th>Notification</th>
                            <th>Variant</th>
                            <th>Clicks</th>
                            <th>Shows</th>
                            <th>Actions</th>
                            <th>CTR</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($ab_results as $result) {
                            $ctr = $result->shows > 0 ? round(($result->actions / $result->shows) * 100, 2) : 0;
                            echo '<tr>';
                            echo '<td><a href="' . get_edit_post_link($result->ID) . '">' . esc_html($result->post_title) . '</a></td>';
                            echo '<td>' . esc_html($result->variant) . '</td>';
                            echo '<td>' . intval($result->clicks) . '</td>';
                            echo '<td>' . intval($result->shows) . '</td>';
                            echo '<td>' . intval($result->actions) . '</td>';
                            echo '<td>' . $ctr . '%</td>';
                            echo '</tr>';
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            } else {
                echo '<p>No A/B testing data available for the selected date range. Create notifications with variants and test them to see results here.</p>';
            }
        } else {
            echo '<p>No notifications with A/B testing variants found. Create notifications with Variant B content to enable A/B testing.</p>';
        }
        ?>
    </div>
    <?php
}

function push_notification_auto_new_post_field() {
    $value = get_option('push_notification_auto_new_post', '');
    echo '<input type="checkbox" name="push_notification_auto_new_post" value="1" ' . checked(1, $value, false) . ' /> Enable automatic notifications for new posts';
}

function push_notification_post_exclude_author_field() {
    $value = get_option('push_notification_post_exclude_author', '');
    echo '<input type="checkbox" name="push_notification_post_exclude_author" value="1" ' . checked(1, $value, false) . ' /> Don\'t show post notifications to the post author';
}

function push_notification_post_target_roles_field() {
    $value = get_option('push_notification_post_target_roles', '');
    echo '<input type="text" name="push_notification_post_target_roles" value="' . esc_attr($value) . '" class="regular-text" placeholder="subscriber,editor,author" />';
    echo '<p class="description">Comma separated list of user roles. Leave empty to show to all users.</p>';
}

function push_notification_post_types_field() {
    $value = get_option('push_notification_post_types', 'post');
    echo '<input type="text" name="push_notification_post_types" value="' . esc_attr($value) . '" class="regular-text" placeholder="post,page" />';
    echo '<p class="description">Comma separated list of post types to trigger notifications for.</p>';
}

function push_notification_email_fallback_field() {
    $value = get_option('push_notification_email_fallback', '');
    echo '<input type="checkbox" name="push_notification_email_fallback" value="1" ' . checked(1, $value, false) . ' /> Send email fallback when push notifications fail (for logged-in users)';
}

function push_notification_supported_languages_field() {
    $value = get_option('push_notification_supported_languages', 'en');
    echo '<input type="text" name="push_notification_supported_languages" value="' . esc_attr($value) . '" class="regular-text" placeholder="en,es,fr" />';
}

function push_notification_mailchimp_api_key_field() {
    $value = get_option('push_notification_mailchimp_api_key', '');
    echo '<input type="password" name="push_notification_mailchimp_api_key" value="' . esc_attr($value) . '" class="regular-text" />';
    echo '<p class="description">Enter your Mailchimp API key to sync users who consent to notifications.</p>';
}

function push_notification_mailchimp_list_id_field() {
    $value = get_option('push_notification_mailchimp_list_id', '');
    echo '<input type="text" name="push_notification_mailchimp_list_id" value="' . esc_attr($value) . '" class="regular-text" />';
    echo '<p class="description">Enter the Audience/List ID to add consented users to.</p>';
}

function push_notification_woocommerce_cart_add_field() {
    $value = get_option('push_notification_woocommerce_cart_add', '');
    echo '<input type="checkbox" name="push_notification_woocommerce_cart_add" value="1" ' . checked(1, $value, false) . ' /> Enable notifications when products are added to cart';
    echo '<p class="description">Requires WooCommerce to be installed and active.</p>';
}

function push_notification_woocommerce_order_completed_field() {
    $value = get_option('push_notification_woocommerce_order_completed', '1');
    echo '<input type="checkbox" name="push_notification_woocommerce_order_completed" value="1" ' . checked(1, $value, false) . ' /> Enable notifications when orders are completed';
    echo '<p class="description">Requires WooCommerce to be installed and active.</p>';
}

function push_notification_woocommerce_order_received_field() {
    $value = get_option('push_notification_woocommerce_order_received', '1');
    echo '<input type="checkbox" name="push_notification_woocommerce_order_received" value="1" ' . checked(1, $value, false) . ' /> Enable notifications when orders are received (processing/on-hold)';
    echo '<p class="description">Requires WooCommerce to be installed and active.</p>';
}

function push_notification_abandoned_cart_field() {
    $value = get_option('push_notification_abandoned_cart', '0');
    echo '<input type="checkbox" name="push_notification_abandoned_cart" value="1" ' . checked(1, $value, false) . ' /> Enable abandoned cart reminders';
    echo '<p class="description">Remind users about items left in their cart when they return to the site.</p>';
}

function push_notification_abandoned_cart_delay_field() {
    $value = get_option('push_notification_abandoned_cart_delay', '1');
    echo '<input type="number" name="push_notification_abandoned_cart_delay" value="' . esc_attr($value) . '" min="1" max="72" step="1" /> hours';
    echo '<p class="description">How long to wait after cart activity before showing the reminder (1-72 hours).</p>';
}

function push_notification_woocommerce_order_shipped_field() {
    $value = get_option('push_notification_woocommerce_order_shipped', '1');
    echo '<input type="checkbox" name="push_notification_woocommerce_order_shipped" value="1" ' . checked(1, $value, false) . ' /> Enable notifications when orders are shipped';
    echo '<p class="description">Includes tracking information when available. Requires WooCommerce.</p>';
}

function push_notification_woocommerce_price_drop_field() {
    $value = get_option('push_notification_woocommerce_price_drop', '0');
    echo '<input type="checkbox" name="push_notification_woocommerce_price_drop" value="1" ' . checked(1, $value, false) . ' /> Enable price drop alerts for wishlisted products';
    echo '<p class="description">Notifies users when products in their wishlist go on sale. Requires WooCommerce.</p>';
}

function push_notification_woocommerce_back_in_stock_field() {
    $value = get_option('push_notification_woocommerce_back_in_stock', '0');
    echo '<input type="checkbox" name="push_notification_woocommerce_back_in_stock" value="1" ' . checked(1, $value, false) . ' /> Enable back in stock notifications';
    echo '<p class="description">Notifies users when out-of-stock products they subscribed to are restocked. Requires WooCommerce.</p>';
}

function push_notification_woocommerce_low_stock_field() {
    $value = get_option('push_notification_woocommerce_low_stock', '0');
    echo '<input type="checkbox" name="push_notification_woocommerce_low_stock" value="1" ' . checked(1, $value, false) . ' /> Enable low stock alerts for admins';
    echo '<p class="description">Sends push notification to admins when product stock is running low. Requires WooCommerce.</p>';
}

function push_notification_add_sample_analytics_data() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'push_notification_analytics';

    // Get existing notifications
    $notifications = get_posts(array(
        'post_type' => 'push_notification',
        'posts_per_page' => 5,
        'post_status' => 'publish'
    ));

    if (empty($notifications)) {
        // Create a sample notification if none exist
        $notification_id = wp_insert_post(array(
            'post_title' => 'Sample Notification',
            'post_type' => 'push_notification',
            'post_status' => 'publish'
        ));
        $notifications = array(get_post($notification_id));
    }

    // Add sample data for the last 30 days
    for ($i = 0; $i < 30; $i++) {
        $date = date('Y-m-d H:i:s', strtotime("-{$i} days"));
        $notification = $notifications[array_rand($notifications)];

        // Add different types of events
        $events = array('button-click', 'notification-shown', 'action-click');
        foreach ($events as $event_type) {
            $count = rand(1, 10); // Random number of events
            for ($j = 0; $j < $count; $j++) {
                $wpdb->insert(
                    $table_name,
                    array(
                        'notification_id' => $notification->ID,
                        'event_type' => $event_type,
                        'user_id' => rand(0, 5), // Mix of logged in and anonymous
                        'user_ip' => '192.168.1.' . rand(1, 255),
                        'user_agent' => 'Sample User Agent',
                        'timestamp' => $date,
                        'session_id' => 'sample_session_' . rand(1000, 9999),
                        'variant' => rand(0, 1) ? 'A' : 'B' // Random variants
                    ),
                    array('%d', '%s', '%d', '%s', '%s', '%s', '%s', '%s')
                );
            }
        }
    }
}