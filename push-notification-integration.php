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
    }

    public function enqueue_scripts() {
        wp_enqueue_script('push-notification-js', plugin_dir_url(__FILE__) . 'js/push-notification.js', array('jquery'), '1.0.0', true);
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