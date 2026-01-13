<?php

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Push_Notification_Elementor_Widget')) {

    class Push_Notification_Elementor_Widget extends \Elementor\Widget_Base {

        public function get_name() {
            return 'push_notification';
        }

        public function get_title() {
            return __('Push Notification', 'push-notification-integration');
        }

        public function get_icon() {
            return 'eicon-bell';
        }

        public function get_categories() {
            return ['general'];
        }

        protected function _register_controls() {
            $this->start_controls_section(
                'content_section',
                [
                    'label' => __('Notification Settings', 'push-notification-integration'),
                    'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
                ]
            );

            $this->add_control(
                'notification_id',
                [
                    'label' => __('Notification ID', 'push-notification-integration'),
                    'type' => \Elementor\Controls_Manager::NUMBER,
                    'description' => __('Enter the ID of the push notification post', 'push-notification-integration'),
                ]
            );

            $this->add_control(
                'button_text',
                [
                    'label' => __('Button Text', 'push-notification-integration'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'default' => __('Show Notification', 'push-notification-integration'),
                ]
            );

            $this->add_control(
                'roles',
                [
                    'label' => __('User Roles (comma separated)', 'push-notification-integration'),
                    'type' => \Elementor\Controls_Manager::TEXT,
                    'description' => __('Limit to specific roles, leave empty for all', 'push-notification-integration'),
                ]
            );

            $this->end_controls_section();

            $this->start_controls_section(
                'style_section',
                [
                    'label' => __('Button Style', 'push-notification-integration'),
                    'tab' => \Elementor\Controls_Manager::TAB_STYLE,
                ]
            );

            $this->add_control(
                'button_color',
                [
                    'label' => __('Button Color', 'push-notification-integration'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'default' => '#007cba',
                    'selectors' => [
                        '{{WRAPPER}} .push-notification-btn' => 'background-color: {{VALUE}};',
                    ],
                ]
            );

            $this->add_control(
                'button_text_color',
                [
                    'label' => __('Text Color', 'push-notification-integration'),
                    'type' => \Elementor\Controls_Manager::COLOR,
                    'default' => '#ffffff',
                    'selectors' => [
                        '{{WRAPPER}} .push-notification-btn' => 'color: {{VALUE}};',
                    ],
                ]
            );

            $this->end_controls_section();
        }

        protected function render() {
            $settings = $this->get_settings_for_display();

            if (empty($settings['notification_id'])) {
                echo '<p>' . __('Please select a notification.', 'push-notification-integration') . '</p>';
                return;
            }

            $atts = array(
                'id' => $settings['notification_id'],
                'roles' => $settings['roles']
            );

            echo push_notification_shortcode($atts);
        }
    }

    // Register widget
    function register_push_notification_widget($widgets_manager) {
        $widgets_manager->register(new Push_Notification_Elementor_Widget());
    }

    if (did_action('elementor/loaded')) {
        add_action('elementor/widgets/register', 'register_push_notification_widget');
    }
}