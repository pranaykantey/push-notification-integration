<?php
/**
 * New WooCommerce Notification Features
 * Features: Restock Priority, Payment Failed Recovery, Wishlist Alert, Weather Promo, Time Offer, Holiday Campaign
 */

// ===== FEATURE 6: Restock Priority for Cart Abandoners =====
function push_notification_restock_priority($product_id) {
    if (!get_option('push_notification_woocommerce_restock_priority', '0')) {
        return;
    }

    $product = wc_get_product($product_id);
    if (!$product || $product->get_stock_status() !== 'instock') {
        return;
    }

    // Find users who abandoned carts with this product
    $user_ids = array();
    $abandoned_users = get_posts(array(
        'post_type' => 'push_notification_abandoned_cart',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_abandoned_product_id',
                'value' => $product_id
            )
        )
    ));

    foreach ($abandoned_users as $abandoned_post) {
        $user_ids[] = $abandoned_post->post_author;
    }

    $user_ids = array_unique($user_ids);

    foreach ($user_ids as $user_id) {
        $user = get_user_by('id', $user_id);
        if (!$user) continue;

        $data = array(
            'title' => '🔥 Priority Restock!',
            'body' => 'Good news! ' . $product->get_name() . ' is back in stock. As a priority customer, you get first access!',
            'icon' => get_option('push_notification_icon', ''),
            'action_title' => 'Buy Now',
            'action_url' => get_permalink($product_id),
            'timestamp' => time()
        );

        set_transient('push_notification_restock_priority_' . $user_id, $data, 300);
    }
}

// ===== FEATURE 7: Payment Failed Recovery =====
function push_notification_payment_failed($order_id, $old_status, $new_status, $order) {
    if (!get_option('push_notification_woocommerce_payment_failed', '0')) {
        return;
    }

    if ($new_status !== 'failed') {
        return;
    }

    $user_id = $order->get_user_id();
    if (!$user_id) {
        return;
    }

    $order_number = $order->get_order_number();
    $failed_attempts = $order->get_meta('_push_notification_failed_attempts', true);
    $failed_attempts = $failed_attempts ? $failed_attempts + 1 : 1;
    $order->update_meta_data('_push_notification_failed_attempts', $failed_attempts);
    $order->save();

    // Increase urgency with each failed attempt
    $urgency_text = 'Please try again';
    if ($failed_attempts >= 2) {
        $urgency_text = 'Your cart may expire soon!';
    }
    if ($failed_attempts >= 3) {
        $urgency_text = 'Final attempt before cart expires!';
    }

    $data = array(
        'title' => '⚠️ Payment Failed',
        'body' => 'Order #' . $order_number . ' payment failed. ' . $urgency_text . ' Click to retry.',
        'icon' => get_option('push_notification_icon', ''),
        'action_title' => 'Retry Payment',
        'action_url' => $order->get_checkout_payment_url(),
        'timestamp' => time()
    );

    set_transient('push_notification_payment_failed_' . $user_id, $data, 300);
    do_action('push_notification_triggered', $data);
}

// ===== FEATURE 8: Wishlist Sale + Stock Alert =====
function push_notification_wishlist_sale_stock_alert($product_id) {
    if (!get_option('push_notification_woocommerce_wishlist_alert', '0')) {
        return;
    }

    $product = wc_get_product($product_id);
    if (!$product) {
        return;
    }

    // Only trigger if product is both on sale AND in stock
    if ($product->get_stock_status() !== 'instock' || !$product->is_on_sale()) {
        return;
    }

    $user_ids = array();

    // Check YITH Wishlist
    if (class_exists('YITH_WCWL')) {
        global $wpdb;
        $wishlist_items = $wpdb->get_results($wpdb->prepare(
            "SELECT user_id FROM {$wpdb->prefix}yith_wcwl WHERE prod_id = %d AND user_id != 0",
            $product_id
        ));
        foreach ($wishlist_items as $item) {
            $user_ids[] = $item->user_id;
        }
    }

    // Check custom wishlist meta
    $wishlist_users = get_posts(array(
        'post_type' => 'push_notification_wishlist',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_wishlist_product_id',
                'value' => $product_id
            )
        )
    ));

    foreach ($wishlist_users as $wishlist_post) {
        $user_ids[] = $wishlist_post->post_author;
    }

    $user_ids = array_unique($user_ids);

    $regular_price = wc_price($product->get_regular_price());
    $sale_price = wc_price($product->get_sale_price());
    $discount = round(100 - ($product->get_sale_price() / $product->get_regular_price() * 100), 0);

    foreach ($user_ids as $user_id) {
        $user = get_user_by('id', $user_id);
        if (!$user) continue;

        $data = array(
            'title' => '💎 Your Wishlist Item is on Sale!',
            'body' => $product->get_name() . ' is back in stock AND on sale! ' . $discount . '% OFF - was ' . $regular_price . ', now ' . $sale_price,
            'icon' => get_option('push_notification_icon', ''),
            'action_title' => 'Shop Now',
            'action_url' => get_permalink($product_id),
            'timestamp' => time()
        );

        set_transient('push_notification_wishlist_alert_' . $user_id, $data, 300);
    }
}

// ===== FEATURE 9: Weather-based Promotions =====
function push_notification_weather_based_promo($location = 'default') {
    if (!get_option('push_notification_woocommerce_weather_promo', '0')) {
        return;
    }

    // Weather mapping to promotions
    $weather_promos = array(
        'rain' => array(
            'title' => '🌧️ Rainy Day Deals',
            'body' => 'It\'s raining outside! Stay dry and shop from home with exclusive indoor essentials.',
            'category' => 'indoor-gear'
        ),
        'sunny' => array(
            'title' => '☀️ Sunny Day Special',
            'body' => 'Perfect weather! Get outdoor gear and summer essentials at sunny prices.',
            'category' => 'outdoor-gear'
        ),
        'cold' => array(
            'title' => '❄️ Cozy Cold Deals',
            'body' => 'Bundle up! Stay warm with our cold weather collection at heated prices.',
            'category' => 'winter-wear'
        ),
        'hot' => array(
            'title' => '🔥 Hot Summer Savings',
            'body' => 'Beat the heat! Cool down with our summer clearance and refreshments.',
            'category' => 'summer-essentials'
        )
    );

    // Get weather (placeholder - would need weather API integration)
    $current_weather = get_transient('push_notification_current_weather');
    if (!$current_weather) {
        $current_weather = 'sunny'; // Default fallback
    }

    if (!isset($weather_promos[$current_weather])) {
        return;
    }

    $promo = $weather_promos[$current_weather];

    $data = array(
        'title' => $promo['title'],
        'body' => $promo['body'],
        'icon' => get_option('push_notification_icon', ''),
        'action_title' => 'Shop Now',
        'action_url' => get_permalink(wc_get_page_id('shop')),
        'timestamp' => time()
    );

    set_transient('push_notification_weather_promo', $data, 300);
    do_action('push_notification_triggered', $data);
}

// ===== FEATURE 10: Time-based Flash Sales =====
function push_notification_time_based_offer($offer_id = 'flash_sale') {
    if (!get_option('push_notification_woocommerce_time_offer', '0')) {
        return;
    }

    // Flash sale configuration
    $flash_sale_config = get_option('push_notification_flash_sale_config', array(
        'title' => '⚡ Flash Sale!',
        'discount' => '50% OFF',
        'duration_hours' => 4
    ));

    $data = array(
        'title' => $flash_sale_config['title'],
        'body' => '⏰ Limited Time Only! ' . $flash_sale_config['discount'] . ' on selected items. Sale ends in ' . $flash_sale_config['duration_hours'] . ' hours!',
        'icon' => get_option('push_notification_icon', ''),
        'action_title' => 'Shop Flash Sale',
        'action_url' => get_permalink(wc_get_page_id('shop')) . '?flash-sale=true',
        'timestamp' => time()
    );

    set_transient('push_notification_time_offer', $data, $flash_sale_config['duration_hours'] * 3600);
    do_action('push_notification_triggered', $data);
}

// Schedule daily flash sale notification
if (get_option('push_notification_woocommerce_time_offer', '0')) {
    add_action('push_notification_daily_flash_sale', 'push_notification_time_based_offer');
    if (!wp_next_scheduled('push_notification_daily_flash_sale')) {
        wp_schedule_event(strtotime('10:00 AM'), 'daily', 'push_notification_daily_flash_sale');
    }
}

// ===== FEATURE 18: Holiday/Seasonal Campaigns =====
function push_notification_holiday_campaign($holiday = 'general') {
    if (!get_option('push_notification_woocommerce_holiday', '0')) {
        return;
    }

    $holiday_campaigns = array(
        'black_friday' => array(
            'title' => '🛒 Black Friday Sale!',
            'body' => 'BIGGEST sale of the year! Up to 70% OFF on all products. Don\'t miss out!',
            'discount' => 'up_to_70'
        ),
        'christmas' => array(
            'title' => '🎄 Merry Christmas!',
            'body' => 'Spread joy with festive deals! Perfect gifts for your loved ones.',
            'discount' => '20'
        ),
        'new_year' => array(
            'title' => '🎉 Happy New Year!',
            'body' => 'Start the year fresh! New Year resolutions fulfilled with our deals.',
            'discount' => '25'
        ),
        'valentine' => array(
            'title' => '💕 Valentine\'s Day',
            'body' => 'Show you care! Special gifts for your special someone.',
            'discount' => '15'
        ),
        'summer' => array(
            'title' => '☀️ Summer Sale',
            'body' => 'Soak up the sun! Summer essentials at unbeatable prices.',
            'discount' => '30'
        ),
        'general' => array(
            'title' => '🎯 Special Offer',
            'body' => 'Exclusive deals just for you! Limited time only.',
            'discount' => '10'
        )
    );

    if (!isset($holiday_campaigns[$holiday])) {
        $holiday = 'general';
    }

    $campaign = $holiday_campaigns[$holiday];

    $data = array(
        'title' => $campaign['title'],
        'body' => $campaign['body'] . ' Use code: HOLIDAY' . $campaign['discount'] . ' for extra savings!',
        'icon' => get_option('push_notification_icon', ''),
        'action_title' => 'Shop Now',
        'action_url' => get_permalink(wc_get_page_id('shop')) . '?holiday=' . $holiday,
        'timestamp' => time()
    );

    set_transient('push_notification_holiday_' . $holiday, $data, 86400); // 24 hours
    do_action('push_notification_triggered', $data);
}

// Schedule holiday campaigns
$holiday_schedule = array(
    'black_friday' => 'fourth_thursday_november', // Week of Thanksgiving
    'christmas' => 'december_20',
    'new_year' => 'december_28',
    'valentine' => 'february_10',
    'summer' => 'june_15'
);

foreach ($holiday_schedule as $holiday => $trigger_date) {
    add_action('push_notification_holiday_' . $holiday, function() use ($holiday) {
        push_notification_holiday_campaign($holiday);
    });
}
