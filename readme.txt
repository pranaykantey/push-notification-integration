=== Push Notification Integration ===

Contributors: pranaykanteysarker
Tags: push notifications, notifications, web push, browser notifications, pwa, analytics, a/b testing, crm integration, multi-language, woocommerce, e-commerce
Requires at least: 5.0
Tested up to: 6.4
Stable tag: 2.1.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Advanced WordPress push notification plugin with analytics, A/B testing, CRM integration, WooCommerce support, and multi-platform support.

== Description ==

Push Notification Integration is a comprehensive WordPress plugin that enables you to send rich, interactive push notifications to your users' browsers. Built with modern web standards, it offers enterprise-grade features for effective user engagement.

== WooCommerce E-Commerce Notifications ==

The plugin includes comprehensive WooCommerce integration for online stores:

* **Order Notifications**: Order received, completed, and shipped alerts
* **Cart Abandonment**: Recover lost sales with abandoned cart reminders
* **Price Drop Alerts**: Notify when wishlist items go on sale
* **Back in Stock**: Alert customers when out-of-stock items return
* **Restock Priority**: Priority alerts for cart abandoners when items restock
* **Payment Failed Recovery**: Urgent notifications for failed payments
* **Wishlist Sale + Stock**: Combined alerts for wishlist items on sale AND in stock
* **Weather-based Promotions**: Contextual offers based on local weather
* **Flash Sales**: Time-limited offers with countdown notifications
* **Holiday Campaigns**: Pre-scheduled notifications for major holidays
* **New Product Launch**: Announce new arrivals to subscribers
* **Sale Alerts**: Notify about products on sale with discount details
* **Review Reminder**: Ask customers to review completed purchases

== Key Features ==

* **Rich Notifications**: Custom titles, bodies, icons, images, and action buttons
* **Multi-Platform Support**: Web push, PWA, mobile browsers, desktop apps
* **Analytics Dashboard**: Track delivery rates, click-through rates, and user engagement
* **A/B Testing**: Compare notification variants to optimize performance
* **Segmentation & Targeting**: User roles, behavior, location, and device-based targeting
* **Multi-Language Support**: Auto-detect user language with localized notifications
* **CRM Integration**: Sync with Mailchimp, HubSpot, and custom CRMs
* **Automation**: Trigger-based notifications for e-commerce, content, memberships, and SaaS
* **GDPR Compliance**: Consent management with opt-in/opt-out controls
* **Page Builders**: Native support for Elementor and Gutenberg
* **REST API**: Programmatic notification sending for developers
* **WooCommerce**: Full e-commerce notification suite

== Use Cases ==

* **E-commerce**: Order confirmations, shipping updates, flash sales, abandoned cart recovery
* **Content Sites**: New blog posts, breaking news alerts
* **Membership Sites**: Renewal reminders, event announcements
* **SaaS Tools**: Feature updates, downtime alerts

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/push-notification-integration` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress.
3. Configure settings in **Settings > Push Notifications**.
4. For WooCommerce features, enable them in **Settings > Push Notifications > WooCommerce**.
5. Create notification posts in **Push Notifications > Add New**.
6. Use shortcodes, widgets, or API to display notifications.

== Frequently Asked Questions ==

= How do I create a notification? =

Go to Push Notifications > Add New in your WordPress admin. Fill in the title, body, and customize with images and actions.

= How do I display notifications on my site? =

Use the shortcode `[push_notification id="123"]` or add the Elementor/Gutenberg widget to any page.

= Can I send notifications automatically? =

Yes! Enable auto-notifications for new posts, or use the REST API to trigger notifications programmatically. WooCommerce notifications are automatic based on order events.

= Is it GDPR compliant? =

Yes, the plugin includes consent management and respects user preferences.

= Can I integrate with my CRM? =

Yes, configure Mailchimp settings to sync consented users, or use hooks for custom CRM integration.

= Does this work with WooCommerce? =

Yes! The plugin includes comprehensive WooCommerce integration with 14+ notification types for orders, carts, price drops, stock alerts, marketing campaigns, and more.

= How do I enable WooCommerce notifications? =

Go to Settings > Push Notifications > WooCommerce and enable the notification types you want. Each feature can be enabled/disabled individually.

== Screenshots ==

1. Analytics dashboard showing performance metrics
2. Notification editor with rich customization options
3. A/B testing results comparison
4. Settings page with CRM integration
5. Elementor widget in action
6. WooCommerce notification settings

== Changelog ==

= 2.1.0 =
* Complete rewrite with modular architecture
* Added analytics dashboard and tracking
* A/B testing capabilities
* Multi-language support
* CRM integration (Mailchimp)
* REST API for developers
* Page builder widgets
* PWA support
* Automation triggers
* GDPR compliance features
* **NEW**: WooCommerce order notifications (received, completed, shipped)
* **NEW**: Cart abandonment recovery with configurable delay
* **NEW**: Price drop alerts for wishlist products
* **NEW**: Back in stock notifications
* **NEW**: Low stock alerts for admins
* **NEW**: Review reminder notifications
* **NEW**: Coupon/promotion notifications
* **NEW**: New product launch alerts
* **NEW**: Sale/flash sale alerts
* **NEW**: Restock Priority for Cart Abandoners
* **NEW**: Payment Failed Recovery with urgency escalation
* **NEW**: Wishlist Sale + Stock Alert (combined notification)
* **NEW**: Weather-based Promotions (contextual marketing)
* **NEW**: Time-based Flash Sales with countdown
* **NEW**: Holiday/Seasonal Campaigns (Black Friday, Christmas, etc.)

= 2.0.0 =
* Complete rewrite with modular architecture
* Added analytics dashboard and tracking
* A/B testing capabilities
* Multi-language support
* CRM integration (Mailchimp)
* REST API for developers
* Page builder widgets
* PWA support
* Automation triggers
* GDPR compliance features

= 1.0.0 =
* Initial release with basic push notification functionality.

== Upgrade Notice ==

= 2.1.0 =
This major update adds comprehensive WooCommerce integration with 14+ new notification types. Upgrade to enable e-commerce notifications, cart abandonment recovery, price drop alerts, and advanced marketing campaigns.

== License ==

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
