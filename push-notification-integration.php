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
        add_action('init', array($this, 'register_post_type'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_boxes'));
        add_shortcode('push_notification', array($this, 'push_notification_shortcode'));
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

    public function register_post_type() {
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

    public function add_meta_boxes() {
        add_meta_box('push_notification_details', 'Notification Details', array($this, 'meta_box_callback'), 'push_notification', 'normal', 'high');
    }

    public function meta_box_callback($post) {
        wp_nonce_field('push_notification_meta_box', 'push_notification_meta_box_nonce');

        $body = get_post_meta($post->ID, '_push_notification_body', true);
        $icon = get_post_meta($post->ID, '_push_notification_icon', true);

        echo '<p><label for="push_notification_body">Body:</label></p>';
        echo '<textarea id="push_notification_body" name="push_notification_body" rows="3" style="width:100%;">' . esc_textarea($body) . '</textarea>';

        echo '<p><label for="push_notification_icon">Icon URL:</label></p>';
        echo '<input type="url" id="push_notification_icon" name="push_notification_icon" value="' . esc_attr($icon) . '" style="width:100%;" />';
    }

    public function save_meta_boxes($post_id) {
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

    public function push_notification_shortcode($atts) {
        $atts = shortcode_atts(array('id' => ''), $atts, 'push_notification');
        if (empty($atts['id']) || !is_numeric($atts['id'])) {
            return '';
        }

        $post = get_post($atts['id']);
        if (!$post || $post->post_type !== 'push_notification') {
            return '';
        }

        $title = $post->post_title;
        $body = get_post_meta($post->ID, '_push_notification_body', true);
        $icon = get_post_meta($post->ID, '_push_notification_icon', true);

        return '<button class="push-notification-btn" data-title="' . esc_attr($title) . '" data-body="' . esc_attr($body) . '" data-icon="' . esc_attr($icon) . '">Show Notification</button>';
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