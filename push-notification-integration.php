<?php
/**
 * Plugin Name: Push Notification Integration
 * Plugin URI: https://themeforest.net/item/push-notification-integration/123456789
 * Description: Professional WordPress plugin for advanced push notifications with analytics, A/B testing, CRM integration, and multi-platform support. Perfect for e-commerce, blogs, and membership sites.
 * Version: 2.1.0
 * Author: Pranay Kantey Sarker
 * Author URI: https://themeforest.net/user/pranaykanteysarker
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: push-notification-integration
 * Domain Path: /languages
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.0
 * Tags: push notifications, notifications, web push, browser notifications, pwa, analytics, a/b testing, crm integration, multi-language, elementor, gutenberg
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
require_once plugin_dir_path(__FILE__) . 'includes/api.php';
require_once plugin_dir_path(__FILE__) . 'includes/api_new_features.php';
if (did_action('elementor/loaded')) {
    require_once plugin_dir_path(__FILE__) . 'includes/elementor-widget.php';
}
require_once plugin_dir_path(__FILE__) . 'includes/block-editor.php';

// Initialize the plugin
new Push_Notification_Integration();

// Activation hook
register_activation_hook(__FILE__, 'push_notification_activation');

// Deactivation hook
register_deactivation_hook(__FILE__, 'push_notification_deactivation');
?>