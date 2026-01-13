# Push Notification Integration for WordPress

A powerful, feature-rich WordPress plugin for sending interactive push notifications to engage your website visitors.

![Version](https://img.shields.io/badge/version-2.0.0-blue.svg)
![WordPress](https://img.shields.io/badge/wordpress-5.0+-blue.svg)
![PHP](https://img.shields.io/badge/php-7.0+-blue.svg)
![License](https://img.shields.io/badge/license-GPL--2.0+-green.svg)

## 🚀 Features

### Core Functionality
- ✅ **Rich Push Notifications** - Custom titles, bodies, icons, images, and action buttons
- ✅ **Progressive Web App (PWA)** - Installable web app with offline capabilities
- ✅ **Browser Compatibility** - Works on Chrome, Firefox, Edge, Safari, and mobile browsers
- ✅ **Service Worker Integration** - Background notification handling

### Advanced Features
- 📊 **Analytics Dashboard** - Track delivery rates, CTR, and user engagement
- 🧪 **A/B Testing** - Compare notification variants for optimization
- 🎯 **Smart Segmentation** - Target by user roles, behavior, location, and device
- 🌍 **Multi-Language Support** - Auto-detect and localize notifications
- 🤖 **Automation** - Trigger notifications for posts, orders, events
- 🔒 **GDPR Compliance** - Consent management and privacy controls
- 🔗 **CRM Integration** - Sync with Mailchimp, HubSpot, and custom CRMs
- 🛠️ **Developer API** - REST API and hooks for custom integrations

### Page Builder Support
- 🎨 **Elementor Widget** - Drag-and-drop notification placement
- 📝 **Gutenberg Block** - Native WordPress editor integration
- 🔧 **Shortcodes** - Flexible placement anywhere on your site

## 📋 Requirements

- WordPress 5.0+
- PHP 7.0+
- HTTPS (required for push notifications in production)

## 📦 Installation

1. Download the plugin zip file
2. Go to **WordPress Admin > Plugins > Add New**
3. Click **Upload Plugin** and select the zip file
4. Click **Install Now** and then **Activate**

### Manual Installation

1. Upload `push-notification-integration` folder to `/wp-content/plugins/`
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Configure settings in **Settings > Push Notifications**

## ⚙️ Configuration

### Basic Setup

1. Go to **Settings > Push Notifications**
2. Configure default notification settings
3. Set up consent banner preferences

### Advanced Configuration

- **Analytics**: Enable tracking in the settings
- **CRM Integration**: Add Mailchimp API credentials
- **Multi-Language**: Configure supported languages
- **Automation**: Enable triggers for content/events

## 📖 Usage

### Creating Notifications

1. Go to **Push Notifications > Add New**
2. Fill in title and content
3. Add images, actions, and A/B variants
4. Set targeting options

### Displaying Notifications

**Shortcode:**
```
[push_notification id="123" roles="subscriber,editor"]
```

**Elementor:**
- Drag the "Push Notification" widget to any page
- Select notification and configure options

**Gutenberg:**
- Add the "Push Notification" block
- Choose notification from dropdown

**API (for developers):**
```php
// Send notification programmatically
wp_remote_post('/wp-json/push-notification/v1/send', [
    'body' => json_encode([
        'title' => 'Hello World',
        'body' => 'This is a test notification',
        'action_title' => 'Learn More',
        'action_url' => 'https://example.com'
    ])
]);
```

## 🎨 Customization

### Styling
The plugin includes responsive CSS that can be customized:

```css
/* Custom notification button styles */
.push-notification-btn {
    background: #007cba;
    border-radius: 8px;
    /* Add your custom styles */
}
```

### Hooks for Developers

```php
// Custom notification trigger
add_action('push_notification_triggered', function($data) {
    // Your custom logic here
    error_log('Notification sent: ' . $data['title']);
});

// CRM integration
add_action('push_notification_consent_given', function($user_id) {
    // Sync to your CRM
});
```

## 📊 Analytics

Access the analytics dashboard at **Push Notifications > Analytics** to:

- View delivery and click-through rates
- Compare A/B test results
- Track user engagement over time
- Export performance data

## 🌍 Multi-Language Support

1. Set supported languages in settings
2. Add translations in notification editor
3. Plugin auto-detects user language
4. Falls back to default if translation missing

## 🔗 CRM Integration

### Mailchimp Setup

1. Get API key from Mailchimp account
2. Find Audience ID in Mailchimp
3. Enter credentials in plugin settings
4. Consented users automatically sync

### Custom CRM

Use the provided hooks to integrate with any CRM:

```php
add_action('push_notification_triggered', 'my_crm_integration');
function my_crm_integration($data) {
    // Send data to your CRM API
}
```

## 🛡️ GDPR Compliance

- Consent banner with clear opt-in/opt-out
- Cookie-based consent storage
- Email fallback for accessibility
- Data export capabilities
- User data deletion support

## 🐛 Troubleshooting

### Notifications Not Showing

1. Ensure site is HTTPS
2. Check browser console for errors
3. Verify user has granted permission
4. Clear browser cache and service worker

### Analytics Not Tracking

1. Check database table creation
2. Verify REST API permissions
3. Check for JavaScript errors

## 📄 Changelog

### Version 2.0.0
- Complete architecture overhaul
- Analytics and A/B testing
- Multi-language support
- CRM integrations
- PWA features
- REST API

### Version 1.0.0
- Initial release
- Basic push notification functionality

## 🤝 Contributing

We welcome contributions! Please see our contributing guidelines for details.

## 📞 Support

For support, please use the WordPress support forums or contact us directly.

## 📜 License

This plugin is licensed under the GPL v2 or later.

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

## 🙏 Credits

Developed by [Pranay Kantey Sarker](https://github.com/pranaykanteysarker)

Icons made by [Freepik](https://www.freepik.com) from [Flaticon](https://www.flaticon.com)