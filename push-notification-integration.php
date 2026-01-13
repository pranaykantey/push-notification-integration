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

// Define plugin constants
define('PUSH_NOTIFICATION_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('PUSH_NOTIFICATION_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include files
require_once plugin_dir_path(__FILE__) . 'includes/class-push-notification-integration.php';
require_once plugin_dir_path(__FILE__) . 'includes/frontend.php';
require_once plugin_dir_path(__FILE__) . 'includes/admin.php';
require_once plugin_dir_path(__FILE__) . 'includes/post-type.php';
require_once plugin_dir_path(__FILE__) . 'includes/shortcodes.php';
require_once plugin_dir_path(__FILE__) . 'includes/activation.php';

// Initialize the plugin
new Push_Notification_Integration();

// Activation hook
register_activation_hook(__FILE__, 'push_notification_activation');

// Deactivation hook
register_deactivation_hook(__FILE__, 'push_notification_deactivation');
?>