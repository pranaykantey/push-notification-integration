<?php
/**
 * Plugin Name: Push Notification Integration
 * Plugin URI: https://example.com/push-notification-integration
 * Description: A WordPress plugin to integrate push notifications into your site.
 * Version: 1.0.0
 * Author: PranayKanteySarker
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: push-notification-integration
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Main plugin class
class Push_Notification_Integration {

    public function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'add_service_worker'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function enqueue_scripts() {
        wp_enqueue_script('push-notification-js', plugin_dir_url(__FILE__) . 'js/push-notification.js', array('jquery'), '1.0.0', true);
        wp_localize_script('push-notification-js', 'pushNotificationOptions', array(
            'defaultTitle' => get_option('push_notification_title', 'Push Notification'),
            'defaultBody' => get_option('push_notification_body', 'You have a new message.'),
            'iconUrl' => get_option('push_notification_icon', ''),
        ));
    }

    public function add_admin_menu() {
        add_options_page('Push Notification Settings', 'Push Notifications', 'manage_options', 'push-notification-settings', array($this, 'settings_page'));
    }

    public function register_settings() {
        register_setting('push_notification_settings', 'push_notification_title');
        register_setting('push_notification_settings', 'push_notification_body');
        register_setting('push_notification_settings', 'push_notification_icon');

        add_settings_section('push_notification_main', 'Main Settings', null, 'push-notification-settings');

        add_settings_field('push_notification_title', 'Default Notification Title', array($this, 'title_field'), 'push-notification-settings', 'push_notification_main');
        add_settings_field('push_notification_body', 'Default Notification Body', array($this, 'body_field'), 'push-notification-settings', 'push_notification_main');
        add_settings_field('push_notification_icon', 'Notification Icon URL', array($this, 'icon_field'), 'push-notification-settings', 'push_notification_main');
    }

    public function settings_page() {
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

    public function title_field() {
        $value = get_option('push_notification_title', 'Push Notification');
        echo '<input type="text" name="push_notification_title" value="' . esc_attr($value) . '" class="regular-text" />';
    }

    public function body_field() {
        $value = get_option('push_notification_body', 'You have a new message.');
        echo '<input type="text" name="push_notification_body" value="' . esc_attr($value) . '" class="regular-text" />';
    }

    public function icon_field() {
        $value = get_option('push_notification_icon', '');
        echo '<input type="url" name="push_notification_icon" value="' . esc_attr($value) . '" class="regular-text" />';
    }

    public function add_service_worker() {
        echo '<script>
            if ("serviceWorker" in navigator) {
                navigator.serviceWorker.register("' . plugin_dir_url(__FILE__) . 'sw.js")
                .then(function(registration) {
                    console.log("Service Worker registered with scope:", registration.scope);
                }).catch(function(error) {
                    console.log("Service Worker registration failed:", error);
                });
            }
        </script>';
    }
}

// Initialize the plugin
new Push_Notification_Integration();

// Activation hook
register_activation_hook(__FILE__, 'push_notification_activation');
function push_notification_activation() {
    // Add activation code here if needed
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'push_notification_deactivation');
function push_notification_deactivation() {
    // Add deactivation code here if needed
}
?>