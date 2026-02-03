# Push Notification Integration for WordPress

**Version 2.1.0** | **WordPress 5.0+** | **PHP 7.0+**

A comprehensive WordPress plugin for sending rich, interactive push notifications to engage your website visitors with advanced analytics, A/B testing, CRM integration, and multi-platform support.

![Push Notification Integration](https://via.placeholder.com/800x400/007cba/white?text=Push+Notification+Integration)

## 📋 Table of Contents

- [Features](#-features)
- [Requirements](#-requirements)
- [Installation](#-installation)
- [Quick Start](#-quick-start)
- [Configuration](#-configuration)
- [WooCommerce Notifications](#-woocommerce-notifications)
- [Usage Guide](#-usage-guide)
- [Advanced Features](#-advanced-features)
- [API Reference](#-api-reference)
- [Troubleshooting](#-troubleshooting)
- [Changelog](#-changelog)
- [Credits](#-credits)

## 🚀 Features

### Core Functionality
- ✅ **Rich Push Notifications** - Custom titles, bodies, icons, images, and action buttons
- ✅ **Progressive Web App (PWA)** - Installable web app with offline capabilities
- ✅ **Browser Compatibility** - Works on Chrome, Firefox, Edge, Safari, and mobile browsers
- ✅ **Service Worker Integration** - Background notification handling
- ✅ **GDPR Compliance** - Consent management with privacy controls

### Advanced Features
- 📊 **Analytics Dashboard** - Track delivery rates, CTR, and user engagement
- 🧪 **A/B Testing** - Compare notification variants for optimization
- 🎯 **Smart Segmentation** - Target by user roles, behavior, and device
- 🌍 **Multi-Language Support** - Auto-detect and localize notifications
- 🤖 **Automation** - Trigger notifications for posts, orders, events
- 🔗 **CRM Integration** - Sync with Mailchimp, HubSpot, and custom CRMs
- 🛠️ **Developer API** - REST API and hooks for custom integrations

### WooCommerce E-commerce Features
- 🛒 **Order Notifications** - Order received, completed, and shipped alerts
- 🛒 **Cart Abandonment** - Recover lost sales with cart reminder notifications
- 🛒 **Price Drop Alerts** - Notify users when wishlist items go on sale
- 🛒 **Back in Stock** - Alert customers when out-of-stock items return
- 🛒 **Restock Priority** - Priority alerts for cart abandoners when items restock
- 🛒 **Payment Failed Recovery** - Urgent notifications for failed payments
- 🛒 **Wishlist Sale + Stock** - Combined alerts for wishlist items on sale AND in stock
- 🛒 **Weather-based Promotions** - Contextual offers based on local weather
- 🛒 **Flash Sales** - Time-limited offers with countdown notifications
- 🛒 **Holiday Campaigns** - Pre-scheduled notifications for major holidays

### Page Builder Support
- 🎨 **Elementor Widget** - Drag-and-drop notification placement
- 📝 **Gutenberg Block** - Native WordPress editor integration
- 🔧 **Shortcodes** - Flexible placement anywhere on your site

### Professional Features
- 📅 **Notification Scheduling** - Send at optimal times
- 🎨 **Custom Templates** - Professional notification designs
- 📈 **Advanced Analytics** - Conversion tracking and ROI measurement
- 👥 **User Management** - Subscriber lists and preferences
- 🔄 **Auto Updates** - Regular feature updates and improvements

## 📋 Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.0 or higher
- **HTTPS**: Required for push notifications in production
- **MySQL**: 5.6 or higher
- **Browser Support**: Modern browsers with Notification API support

### Recommended
- **WooCommerce**: For e-commerce notification features
- **Elementor**: For visual page building
- **Mailchimp**: For CRM integration

## 📦 Installation

### Method 1: WordPress Admin Installation

1. **Download** the plugin zip file from ThemeForest
2. **Login** to your WordPress admin dashboard
3. **Navigate** to **Plugins > Add New**
4. **Click** **Upload Plugin** and select the zip file
5. **Click** **Install Now** and then **Activate**

### Method 2: Manual Installation

1. **Download** and unzip the plugin files
2. **Upload** the `push-notification-integration` folder to `/wp-content/plugins/`
3. **Activate** the plugin through **Plugins** menu in WordPress

### Method 3: FTP Installation

1. **Extract** the plugin files to your computer
2. **Connect** to your server via FTP
3. **Upload** the `push-notification-integration` folder to `/wp-content/plugins/`
4. **Activate** the plugin in WordPress admin

## ⚡ Quick Start

### 1. Basic Setup (5 minutes)

1. **Activate** the plugin
2. **Go to** **Settings > Push Notifications**
3. **Configure** default notification settings
4. **Enable** automatic notifications for new posts
5. **Test** by publishing a new post

### 2. Advanced Setup (15 minutes)

1. **Set up** notification consent banner
2. **Configure** analytics tracking
3. **Add** CRM integration (optional)
4. **Create** custom notification templates
5. **Test** all notification types

### 3. WooCommerce Setup (10 minutes)

1. **Install/Activate** WooCommerce
2. **Go to** **Settings > Push Notifications > WooCommerce**
3. **Enable** desired notification types
4. **Configure** abandoned cart timing
5. **Test** order notifications

## ⚙️ Configuration

### Basic Settings

Navigate to **Settings > Push Notifications** to configure:

#### Main Settings
- **Default Title**: Fallback notification title
- **Default Body**: Fallback notification message
- **Icon URL**: Default notification icon
- **Auto new posts**: Enable automatic post notifications
- **Email fallback**: Send emails when push fails
- **Supported languages**: Comma-separated language codes

#### Automation Settings
- **WooCommerce cart add**: Notify on product additions
- **WooCommerce order completed**: Notify on order completion
- **Post exclude author**: Don't notify post authors
- **Target user roles**: Limit to specific roles
- **Post types**: Which content types trigger notifications

#### CRM Integration
- **Mailchimp API Key**: Connect to Mailchimp
- **Mailchimp List ID**: Target specific audience

### Advanced Configuration

#### Analytics Setup
```php
// Enable advanced tracking
add_filter('push_notification_analytics_enabled', '__return_true');
```

#### Custom Notification Triggers
```php
// Custom automation
add_action('my_custom_event', function($data) {
    do_action('push_notification_triggered', $data);
});
```

## 🛒 WooCommerce Notifications

The plugin includes comprehensive WooCommerce integration for e-commerce stores. Configure these settings in **Settings > Push Notifications > WooCommerce**.

### Order Notifications

#### Order Received (Processing/On-Hold)
Notify customers when their order is received.
- **Default Message**: "We have received your order #[order_id]. Thank you!"
- **Action Button**: View Order
- **Setting**: `push_notification_woocommerce_order_received`

#### Order Completed
Alert customers when their order is fully processed.
- **Default Message**: "Your order #[order_id] has been completed."
- **Action Button**: View Order
- **Setting**: `push_notification_woocommerce_order_completed`

#### Order Shipped
Notify when orders are dispatched with tracking info.
- **Default Message**: "Your order #[order_id] has been shipped!"
- **Includes**: Tracking number if available
- **Action Button**: Track Order
- **Setting**: `push_notification_woocommerce_order_shipped`

### Cart & Abandonment Notifications

#### Cart Add Notification
Alert users when products are added to cart.
- **Default Message**: "[Product Name] has been added to your cart."
- **Action Button**: View Cart
- **Setting**: `push_notification_woocommerce_cart_add`

#### Abandoned Cart Recovery
Remind users about items left in their cart.
- **Default Message**: "You left something behind!"
- **Configurable Delay**: 1-24 hours
- **Action Button**: View Cart
- **Setting**: `push_notification_abandoned_cart`
- **Related**: `push_notification_abandoned_cart_delay`

#### Restock Priority (Cart Abandoners)
Priority alerts for users who abandoned carts when items restock.
- **Default Message**: "Good news! [Product] is back in stock. As a priority customer, you get first access!"
- **Target**: Users who had product in abandoned cart
- **Action Button**: Buy Now
- **Setting**: `push_notification_woocommerce_restock_priority`

### Price & Stock Notifications

#### Price Drop Alerts
Notify when wishlist items go on sale.
- **Default Message**: "[Product] is now [new price] (was [old price]). Save [discount]%!"
- **Target**: Users with product in wishlist
- **Action Button**: View Product
- **Setting**: `push_notification_woocommerce_price_drop`

#### Back in Stock
Alert customers when out-of-stock products are replenished.
- **Default Message**: "[Product] is back in stock! Don't miss out."
- **Target**: Users who subscribed to back-in-stock notifications
- **Action Button**: Buy Now
- **Setting**: `push_notification_woocommerce_back_in_stock`

#### Low Stock Alert (Admin)
Notify administrators when inventory is running low.
- **Default Message**: "[Product] is running low on stock. Current quantity: [quantity]"
- **Target**: Admin users
- **Action Button**: Edit Product
- **Setting**: `push_notification_woocommerce_low_stock`

#### Wishlist Sale + Stock Alert
Combined notification when wishlist items are both on sale AND in stock.
- **Default Message**: "[Product] is back in stock AND on sale! [discount]% OFF - was [regular], now [sale]"
- **Target**: Users with product in wishlist
- **Action Button**: Shop Now
- **Setting**: `push_notification_woocommerce_wishlist_alert`

### Payment Recovery

#### Payment Failed Recovery
Send urgent notifications when payment fails, with increasing urgency.
- **Default Message**: "Order #[order_id] payment failed. Please try again."
- **Escalation**: 
  - 1st failure: "Please try again"
  - 2nd failure: "Your cart may expire soon!"
  - 3rd failure: "Final attempt before cart expires!"
- **Action Button**: Retry Payment
- **Setting**: `push_notification_woocommerce_payment_failed`

### Marketing & Promotions

#### Coupon/Promotion Notifications
Send custom coupon codes and promotional offers.
- **Default Message**: "Use code [CODE] for [discount]. [description]"
- **Action Button**: Shop Now
- **Setting**: `push_notification_woocommerce_coupon`

#### New Product Launch
Alert customers about new product arrivals.
- **Default Message**: "Check out [Product] - Now available for [price]"
- **Target**: All subscribed users
- **Action Button**: Shop Now
- **Setting**: `push_notification_woocommerce_new_product`

#### Sale/Flash Sale Alert
Notify about products on sale with discount details.
- **Default Message**: "[Product] is [discount]% OFF! Was [regular], now [sale]"
- **Action Button**: Grab Deal
- **Setting**: `push_notification_woocommerce_sale_alert`

#### Review Reminder
Ask customers to review their completed purchases.
- **Default Message**: "Thanks for order #[order]! We'd love to hear your feedback."
- **Trigger**: Order completed
- **Action Button**: Write Review
- **Setting**: `push_notification_woocommerce_review_reminder`

### Advanced Marketing Features

#### Weather-based Promotions
Send contextual promotions based on local weather conditions.
- **Weather Mappings**:
  - ☀️ Sunny: Outdoor gear and summer essentials
  - 🌧️ Rainy: Indoor essentials and home products
  - ❄️ Cold: Winter wear and cozy items
  - 🔥 Hot: Cooling products and summer sales
- **Action Button**: Shop Now
- **Setting**: `push_notification_woocommerce_weather_promo`
- **Note**: Requires weather API integration for full functionality

#### Time-based Flash Sales
Limited-time offers with countdown urgency.
- **Default Message**: "Limited Time Only! [discount] on selected items. Sale ends in [hours] hours!"
- **Scheduling**: Daily automatic notifications at configurable time
- **Duration**: Configurable sale length (default: 4 hours)
- **Action Button**: Shop Flash Sale
- **Setting**: `push_notification_woocommerce_time_offer`

#### Holiday/Seasonal Campaigns
Pre-scheduled notifications for major holidays.
- **Supported Campaigns**:
  - 🛒 Black Friday: Up to 70% OFF
  - 🎄 Christmas: Holiday gifts and deals
  - 🎉 New Year: Fresh start promotions
  - 💕 Valentine's: Romantic gifts
  - ☀️ Summer: Seasonal essentials
- **Default Message**: "[Campaign message] Use code: HOLIDAY[discount] for extra savings!"
- **Action Button**: Shop Now
- **Setting**: `push_notification_woocommerce_holiday`

### WooCommerce Configuration Example

```php
// Enable all WooCommerce notifications
add_filter('push_notification_woocommerce_order_received', '__return_true');
add_filter('push_notification_woocommerce_order_completed', '__return_true');
add_filter('push_notification_woocommerce_order_shipped', '__return_true');
add_filter('push_notification_abandoned_cart', '__return_true');
add_filter('push_notification_abandoned_cart_delay', function() { return 2; }); // 2 hours
add_filter('push_notification_woocommerce_price_drop', '__return_true');
add_filter('push_notification_woocommerce_back_in_stock', '__return_true');
add_filter('push_notification_woocommerce_restock_priority', '__return_true');
add_filter('push_notification_woocommerce_payment_failed', '__return_true');
add_filter('push_notification_woocommerce_wishlist_alert', '__return_true');
add_filter('push_notification_woocommerce_weather_promo', '__return_true');
add_filter('push_notification_woocommerce_time_offer', '__return_true');
add_filter('push_notification_woocommerce_holiday', '__return_true');
```

## 📖 Usage Guide

### Creating Notifications

#### Method 1: Custom Post Type
1. **Go to** **Push Notifications > Add New**
2. **Enter** title and content
3. **Add** images, icons, and action buttons
4. **Configure** A/B testing variants
5. **Set** targeting options
6. **Publish**

#### Method 2: Shortcodes
```php
// Basic notification
[push_notification id="123"]

// Role-restricted
[push_notification id="123" roles="subscriber,editor"]

// List all notifications
[push_notifications_list]
```

#### Method 3: Elementor Widget
1. **Drag** "Push Notification" widget to any page
2. **Select** notification from dropdown
3. **Configure** button text and restrictions
4. **Style** with Elementor's design tools

#### Method 4: Gutenberg Block
1. **Add** "Push Notification" block
2. **Choose** notification
3. **Set** parameters
4. **Publish** page

### API Usage

#### Send Custom Notification
```bash
curl -X POST /wp-json/push-notification/v1/send \
  -H "Content-Type: application/json" \
  -u "admin:password" \
  -d '{
    "title": "Breaking News",
    "body": "Something important happened!",
    "icon": "https://example.com/icon.png",
    "action_title": "Read More",
    "action_url": "https://example.com/news"
  }'
```

#### Track Events
```javascript
// Frontend tracking
fetch('/wp-json/push-notification/v1/track', {
  method: 'POST',
  headers: { 'Content-Type': 'application/json' },
  body: JSON.stringify({
    notification_id: 123,
    event_type: 'button-click',
    variant: 'A'
  })
});
```

## 🎨 Advanced Features

### A/B Testing

1. **Create** notification with multiple variants
2. **Set** different titles, content, or actions
3. **Publish** and let the system randomly distribute
4. **View** results in **Analytics** dashboard
5. **Optimize** based on performance data

### Multi-Language Support

1. **Configure** supported languages in settings
2. **Add** translations in notification editor
3. **Plugin** auto-detects user language
4. **Falls back** to default if translation missing

### CRM Integration

#### Mailchimp Setup
1. **Get** API key from Mailchimp account
2. **Find** Audience ID in Mailchimp
3. **Enter** credentials in plugin settings
4. **Consented users** automatically sync

#### Custom CRM
```php
add_action('push_notification_consent_given', 'my_crm_integration');
function my_crm_integration($user_id) {
    // Send user data to your CRM
}
```

### Analytics & Reporting

Access **Push Notifications > Analytics** to:
- View delivery and click-through rates
- Compare A/B test results
- Track user engagement over time
- Export performance data
- Monitor conversion attribution

## 🔧 API Reference

### REST Endpoints

#### `POST /wp-json/push-notification/v1/send`
Send a custom notification (admin only)
```json
{
  "title": "Notification Title",
  "body": "Notification content",
  "icon": "https://example.com/icon.png",
  "image": "https://example.com/image.jpg",
  "action_title": "Click Here",
  "action_url": "https://example.com/action"
}
```

#### `POST /wp-json/push-notification/v1/track`
Track user interactions
```json
{
  "notification_id": 123,
  "event_type": "button-click|notification-shown|action-click",
  "variant": "A|B",
  "session_id": "session_123"
}
```

#### `POST /wp-json/push-notification/v1/consent`
Record user consent (logged-in users)
```json
{
  "consent": true
}
```

### PHP Hooks

#### Actions
```php
// Custom notification trigger
do_action('push_notification_triggered', $notification_data);

// User consented to notifications
do_action('push_notification_consent_given', $user_id);

// Analytics event recorded
do_action('push_notification_event_tracked', $event_data);
```

#### Filters
```php
// Modify notification data
add_filter('push_notification_data', function($data) {
    $data['title'] = 'Modified: ' . $data['title'];
    return $data;
});

// Custom permission check
add_filter('push_notification_can_send', function($can_send, $user_id) {
    return $can_send && user_can($user_id, 'receive_notifications');
}, 10, 2);
```

### JavaScript Events

```javascript
// Notification shown
document.addEventListener('pushNotificationShown', function(e) {
    console.log('Notification displayed:', e.detail);
});

// Notification clicked
document.addEventListener('pushNotificationClicked', function(e) {
    console.log('Notification clicked:', e.detail);
});
```

## 🔍 Troubleshooting

### Notifications Not Showing

#### Browser Issues
- **HTTPS Required**: Ensure site uses HTTPS
- **Permission Denied**: Check browser notification settings
- **Service Worker**: Clear browser cache and service worker

#### WordPress Issues
- **Plugin Active**: Verify plugin is activated
- **Settings**: Check notification settings are configured
- **User Consent**: Ensure consent banner is accepted

#### Debug Steps
1. Open browser developer tools
2. Check console for JavaScript errors
3. Verify service worker registration
4. Test notification permission: `Notification.permission`

### Analytics Not Tracking

- **Database Table**: Ensure `wp_push_notification_analytics` table exists
- **REST API**: Verify API endpoints are accessible
- **JavaScript**: Check for tracking code errors

### WooCommerce Notifications Not Working

1. **Verify WooCommerce is active**
2. **Check settings are enabled** in admin panel
3. **Clear plugin cache** if using caching
4. **Check user consent** for push notifications
5. **Verify transients** are being created
6. **Test with debug mode** enabled

### Common Issues

#### "Notifications blocked"
```
Solution: Guide users to enable notifications in browser settings
```

#### "Service worker failed"
```
Solution: Check for HTTPS and clear browser cache
```

#### "Permission denied"
```
Solution: User must click "Allow" on consent banner
```

## 📝 Changelog

### Version 2.1.0 (Current)
- ✅ Enhanced ThemeForest compatibility
- ✅ Improved documentation and setup guides
- ✅ Professional UI/UX enhancements
- ✅ Advanced user segmentation
- ✅ Notification scheduling system
- ✅ Enhanced analytics dashboard
- ✅ **NEW**: Restock Priority for Cart Abandoners
- ✅ **NEW**: Payment Failed Recovery notifications
- ✅ **NEW**: Wishlist Sale + Stock Alert
- ✅ **NEW**: Weather-based Promotions
- ✅ **NEW**: Time-based Flash Sales
- ✅ **NEW**: Holiday/Seasonal Campaigns

### Version 2.0.0
- ✅ Complete architecture overhaul
- ✅ Analytics and A/B testing
- ✅ Multi-language support
- ✅ CRM integrations
- ✅ REST API implementation
- ✅ Page builder widgets

### Version 1.0.0
- ✅ Initial release
- ✅ Basic push notification functionality
- ✅ Browser compatibility
- ✅ Service worker integration

## 📞 Support

### Getting Help

1. **Documentation**: Check this guide first
2. **ThemeForest Comments**: Post in item comments
3. **WordPress Forums**: Community support
4. **Email Support**: Contact author directly

### Before Contacting Support

Please provide:
- WordPress version
- Plugin version
- PHP version
- Browser and version
- Detailed description of the issue
- Steps to reproduce
- Screenshots if applicable

## 🙏 Credits

**Developed by**: Pranay Kantey Sarker

**Contributors**: WordPress community

**Icons**: Font Awesome, Flaticon

**Testing**: Various beta testers and users

## 📜 License

This plugin is licensed under the **GPL v2 or later**.

```
Copyright (C) 2024 Pranay Kantey Sarker

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.
```

---

**Thank you for choosing Push Notification Integration!** 🎉

For more information, visit the [ThemeForest item page](https://demo.net/item).
