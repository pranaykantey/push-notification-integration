<?php

function push_notification_register_api() {
    register_rest_route('push-notification/v1', '/send', array(
        'methods' => 'POST',
        'callback' => 'push_notification_send_api',
        'permission_callback' => function() { return current_user_can('manage_options'); },
        'args' => array(
            'title' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'body' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_textarea_field'
            ),
            'icon' => array(
                'sanitize_callback' => 'esc_url_raw'
            ),
            'image' => array(
                'sanitize_callback' => 'esc_url_raw'
            ),
            'action_title' => array(
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'action_url' => array(
                'sanitize_callback' => 'esc_url_raw'
            )
        )
    ));

    register_rest_route('push-notification/v1', '/track', array(
        'methods' => 'POST',
        'callback' => 'push_notification_track_api',
        'permission_callback' => '__return_true', // Allow tracking from frontend
        'args' => array(
            'notification_id' => array(
                'required' => true,
                'sanitize_callback' => 'intval'
            ),
            'event_type' => array(
                'required' => true,
                'sanitize_callback' => 'sanitize_text_field'
            ),
            'session_id' => array(
                'sanitize_callback' => 'sanitize_text_field'
            )
        )
    ));

    register_rest_route('push-notification/v1', '/email-fallback', array(
        'methods' => 'POST',
        'callback' => 'push_notification_email_fallback_api',
        'permission_callback' => 'is_user_logged_in', // Only for logged-in users
        'args' => array(
            'notification_id' => array(
                'required' => true,
                'sanitize_callback' => 'intval'
            )
        )
    ));

    register_rest_route('push-notification/v1', '/consent', array(
        'methods' => 'POST',
        'callback' => 'push_notification_consent_api',
        'permission_callback' => 'is_user_logged_in', // Only for logged-in users
    ));

    register_rest_route('push-notification/v1', '/get-cart-notification', array(
        'methods' => 'GET',
        'callback' => 'push_notification_get_cart_notification_api',
        'permission_callback' => '__return_true',
    ));
}

function push_notification_send_api($request) {
    $params = $request->get_params();
    $data = array(
        'title' => $params['title'],
        'body' => $params['body'],
        'icon' => $params['icon'] ?: '',
        'image' => $params['image'] ?: '',
        'action_title' => $params['action_title'] ?: '',
        'action_url' => $params['action_url'] ?: ''
    );

    // Set transient for API triggered notification
    set_transient('push_notification_api', $data, 300); // 5 minutes

    // Trigger hook for developers
    do_action('push_notification_triggered', $data);

    return array('success' => true, 'message' => 'Notification queued');
}

// WooCommerce integration
add_action('plugins_loaded', 'push_notification_woocommerce_integration');
function push_notification_woocommerce_integration() {
    if (class_exists('WooCommerce')) {

        error_log('Push Notification: WooCommerce detected');

        add_action('woocommerce_order_status_changed', 'push_notification_order_status_changed', 10, 4);
        add_action('woocommerce_add_to_cart', 'push_notification_cart_add', 10, 6);
        add_action('woocommerce_cart_updated', 'push_notification_track_cart_activity');
        add_action('wp_footer', 'push_notification_check_abandoned_cart');
        add_action('woocommerce_order_status_shipped', 'push_notification_order_shipped', 10, 2);
        add_action('post_updated', 'push_notification_check_price_drop', 10, 3);
        add_action('woocommerce_product_set_stock_status', 'push_notification_stock_status_changed', 10, 3);
        add_action('woocommerce_low_stock', 'push_notification_low_stock');
        add_filter('woocommerce_add_to_cart_fragments', 'push_notification_add_to_cart_fragments', 10, 1);

    } else {
        error_log('Push Notification: WooCommerce NOT detected - class_exists returned false');
    }
}

function push_notification_cart_add($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
    // Debug logging
    error_log('Push Notification: Cart add hook triggered for product ' . $product_id);

    // Check if setting is enabled
    $setting_enabled = get_option('push_notification_woocommerce_cart_add', '0');
    if (!$setting_enabled) {
        return;
    }

    // TEMPORARILY DISABLE CONSENT CHECK FOR DEBUGGING
    // Only proceed if user has consented to notifications
    // $consent = isset($_COOKIE['push_notification_consent']) ? $_COOKIE['push_notification_consent'] : 'not_set';
    // error_log('Push Notification: Consent cookie value: ' . $consent);
    // if ($consent !== 'accepted') {
    //     error_log('Push Notification: User has not consented to notifications');
    //     return;
    // }
    error_log('Push Notification: Consent check temporarily disabled for debugging');

    $product = wc_get_product($product_id);
    if (!$product) {
        return;
    }

    $data = array(
        'title' => 'Added to Cart',
        'body' => $product->get_name() . ' has been added to your cart.',
        'icon' => get_option('push_notification_icon', ''),
        'action_title' => 'View Cart',
        'action_url' => wc_get_cart_url(),
        'timestamp' => time()
    );

    // Store notification data for display
    $user_id = get_current_user_id();
    if ($user_id) {
        update_user_meta($user_id, '_push_notification_cart_add', $data);
    } else {
        if (!session_id()) {
            session_start();
        }
        $_SESSION['push_notification_cart_add'] = $data;
    }

    // For AJAX requests, also add to response fragments
    if (wp_doing_ajax()) {
        $key = $user_id ? 'user_' . $user_id : 'session_' . session_id();
        set_transient('push_notification_ajax_' . $key, $data, 30);
    }

    do_action('push_notification_triggered', $data);
}

function push_notification_add_to_cart_fragments($fragments) {
    // Check for pending notification from AJAX cart add
    $user_id = get_current_user_id();
    if (!$user_id) {
        if (!session_id()) {
            session_start();
        }
    }
    $key = $user_id ? 'user_' . $user_id : 'session_' . session_id();
    error_log('Push Notification: Checking for transient with key: ' . $key);
    $notification = get_transient('push_notification_ajax_' . $key);

    if ($notification) {
        // Add notification data to AJAX response
        $fragments['push_notification'] = $notification;
        delete_transient('push_notification_ajax_' . $key);
        error_log('Push Notification: Added notification to AJAX fragments');
    } else {
        error_log('Push Notification: No transient found for key: ' . $key);
    }

    return $fragments;
}

// Abandoned Cart Tracking
function push_notification_track_cart_activity() {
    if (!get_option('push_notification_abandoned_cart', '0')) {
        return;
    }

    if (!WC() || !WC()->cart) {
        return;
    }

    $cart = WC()->cart;
    if ($cart->is_empty()) {
        // Cart is empty, remove abandoned cart data
        $user_id = get_current_user_id();
        if ($user_id) {
            delete_user_meta($user_id, '_push_notification_abandoned_cart');
        } else {
            if (!session_id()) {
                session_start();
            }
            if (isset($_SESSION['_push_notification_abandoned_cart'])) {
                unset($_SESSION['_push_notification_abandoned_cart']);
            }
        }
        return;
    }

    $cart_total = $cart->get_total('edit');
    $cart_count = $cart->get_cart_contents_count();
    $cart_items = array();

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
        $cart_items[] = array(
            'product_id' => $cart_item['product_id'],
            'quantity' => $cart_item['quantity'],
            'name' => $cart_item['data']->get_name(),
            'price' => $cart_item['data']->get_price()
        );
    }

    $abandoned_cart_data = array(
        'cart_total' => $cart_total,
        'cart_count' => $cart_count,
        'cart_items' => $cart_items,
        'last_activity' => time(),
        'abandoned_shown' => false
    );

    $user_id = get_current_user_id();
    if ($user_id) {
        update_user_meta($user_id, '_push_notification_abandoned_cart', $abandoned_cart_data);
    } else {
        if (!session_id()) {
            session_start();
        }
        $_SESSION['_push_notification_abandoned_cart'] = $abandoned_cart_data;
    }
}

function push_notification_check_abandoned_cart() {
    if (!get_option('push_notification_abandoned_cart', '0')) {
        return;
    }

    if (!WC() || !WC()->cart || WC()->cart->is_empty()) {
        return;
    }

    $user_id = get_current_user_id();
    $abandoned_cart = null;
    $session_id = null;

    if ($user_id) {
        $abandoned_cart = get_user_meta($user_id, '_push_notification_abandoned_cart', true);
    } else {
        if (!session_id()) {
            session_start();
        }
        if (isset($_SESSION['_push_notification_abandoned_cart'])) {
            $abandoned_cart = $_SESSION['_push_notification_abandoned_cart'];
            $session_id = session_id();
        }
    }

    if (!$abandoned_cart || empty($abandoned_cart['cart_items'])) {
        return;
    }

    // Check if notification already shown
    if (!empty($abandoned_cart['abandoned_shown'])) {
        return;
    }

    // Check delay time
    $delay_hours = get_option('push_notification_abandoned_cart_delay', '1');
    $delay_seconds = $delay_hours * 3600;
    $time_since_activity = time() - $abandoned_cart['last_activity'];

    if ($time_since_activity < $delay_seconds) {
        return;
    }

    // Check if cart is still non-empty
    if (!WC() || !WC()->cart || WC()->cart->is_empty()) {
        return;
    }

    // Mark as shown to prevent duplicate
    $abandoned_cart['abandoned_shown'] = true;

    if ($user_id) {
        update_user_meta($user_id, '_push_notification_abandoned_cart', $abandoned_cart);
    } else {
        if (!session_id()) {
            session_start();
        }
        $_SESSION['_push_notification_abandoned_cart'] = $abandoned_cart;
    }

    // Build notification data
    $item_count = $abandoned_cart['cart_count'];
    $first_item_name = !empty($abandoned_cart['cart_items'][0]['name']) ? $abandoned_cart['cart_items'][0]['name'] : 'Your items';
    $total = wc_price($abandoned_cart['cart_total']);

    if ($item_count == 1) {
        $body = $first_item_name . ' is waiting in your cart. Total: ' . $total;
    } else {
        $body = $item_count . ' items in your cart. Total: ' . $total . ' - Complete your purchase!';
    }

    $data = array(
        'title' => 'You left something behind!',
        'body' => $body,
        'icon' => get_option('push_notification_icon', ''),
        'action_title' => 'View Cart',
        'action_url' => wc_get_cart_url(),
        'timestamp' => time()
    );

    // Store for frontend (use session ID for guest users)
    $storage_key = $user_id ? 'push_notification_abandoned_cart_' . $user_id : 'push_notification_abandoned_cart_session_' . $session_id;
    set_transient($storage_key, $data, 300);
}

function push_notification_order_status_changed($order_id, $old_status, $new_status, $order) {
    // Order received notification (processing or on-hold)
    if (in_array($new_status, array('processing', 'on-hold')) && get_option('push_notification_woocommerce_order_received', '1')) {
        $data = array(
            'title' => 'Order Received',
            'body' => 'We have received your order #' . $order_id . '. Thank you!',
            'icon' => get_option('push_notification_icon', ''),
            'action_title' => 'View Order',
            'action_url' => wc_get_endpoint_url('orders', '', wc_get_page_permalink('myaccount'))
        );

        set_transient('push_notification_api', $data, 300);
        do_action('push_notification_triggered', $data);
    }

    // Order completed notification
    if ($new_status === 'completed' && get_option('push_notification_woocommerce_order_completed', '1')) {
        $data = array(
            'title' => 'Order Completed',
            'body' => 'Your order #' . $order_id . ' has been completed.',
            'icon' => get_option('push_notification_icon', ''),
            'action_title' => 'View Order',
            'action_url' => wc_get_endpoint_url('orders', '', wc_get_page_permalink('myaccount'))
        );

        set_transient('push_notification_api', $data, 300);
        do_action('push_notification_triggered', $data);
    }
}

// Order Shipped Notification
function push_notification_order_shipped($order_id, $order) {
    if (!get_option('push_notification_woocommerce_order_shipped', '1')) {
        return;
    }

    // Get tracking info if available
    $tracking_number = '';
    $tracking_url = '';
    $tracking_provider = '';

    // Check for common shipping plugins
    if (function_exists('wc_st_add_tracking_number')) {
        $tracking_number = get_post_meta($order_id, '_tracking_number', true);
        $tracking_provider = get_post_meta($order_id, '_tracking_provider', true);
    }

    // Build body message
    $body = 'Your order #' . $order_id . ' has been shipped!';
    if ($tracking_number) {
        $body .= ' Tracking: ' . $tracking_number;
    }

    $data = array(
        'title' => 'Order Shipped',
        'body' => $body,
        'icon' => get_option('push_notification_icon', ''),
        'action_title' => 'Track Order',
        'action_url' => $order->get_view_order_url()
    );

    set_transient('push_notification_api', $data, 300);
    do_action('push_notification_triggered', $data);
}

// Price Drop Alert for Wishlist Products
function push_notification_check_price_drop($post_id, $post_after, $post_before) {
    if (!get_option('push_notification_woocommerce_price_drop', '0')) {
        return;
    }

    // Only check products
    if ($post_after->post_type !== 'product') {
        return;
    }

    $product = wc_get_product($post_id);
    if (!$product) {
        return;
    }

    $new_price = $product->get_price();
    $old_price = get_post_meta($post_id, '_push_notification_last_price', true);

    // Store current price for future comparison
    update_post_meta($post_id, '_push_notification_last_price', $new_price);

    // Skip if no old price or price didn't drop
    if (!$old_price || $new_price >= $old_price) {
        return;
    }

    // Calculate discount
    $discount_percent = round(100 - ($new_price / $old_price * 100), 1);

    // Find users with this product in wishlist
    // Support for YITH Wishlist, WooCommerce Wishlist, and default meta
    $user_ids = array();

    // Check YITH Wishlist
    if (class_exists('YITH_WCWL')) {
        global $wpdb;
        $wishlist_items = $wpdb->get_results($wpdb->prepare(
            "SELECT user_id FROM {$wpdb->prefix}yith_wcwl WHERE prod_id = %d AND user_id != 0",
            $post_id
        ));
        foreach ($wishlist_items as $item) {
            $user_ids[] = $item->user_id;
        }
    }

    // Check for users who clicked "Add to Wishlist" button (stores in meta)
    $wishlist_meta_users = get_posts(array(
        'post_type' => 'push_notification_wishlist',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_wishlist_product_id',
                'value' => $post_id
            )
        )
    ));

    foreach ($wishlist_meta_users as $wishlist_post) {
        $user_ids[] = $wishlist_post->post_author;
    }

    // Remove duplicates
    $user_ids = array_unique($user_ids);

    // Notify each user
    foreach ($user_ids as $user_id) {
        $user = get_user_by('id', $user_id);
        if (!$user) continue;

        $data = array(
            'title' => 'Price Drop!',
            'body' => $product->get_name() . ' is now ' . wc_price($new_price) . ' (was ' . wc_price($old_price) . '). Save ' . $discount_percent . '%!',
            'icon' => get_option('push_notification_icon', ''),
            'action_title' => 'View Product',
            'action_url' => get_permalink($post_id),
            'timestamp' => time()
        );

        set_transient('push_notification_price_drop_' . $user_id, $data, 300);
    }
}

// Back in Stock Notification
function push_notification_stock_status_changed($product_id, $status, $product) {
    if (!get_option('push_notification_woocommerce_back_in_stock', '0')) {
        return;
    }

    if ($status !== 'instock') {
        return;
    }

    // Find users who subscribed to back in stock notifications
    $user_ids = array();

    // Check WooCommerce native back in stock subscriptions
    $subscriptions = get_posts(array(
        'post_type' => 'shop_subscription',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => '_product_id',
                'value' => $product_id
            )
        )
    ));

    foreach ($subscriptions as $subscription) {
        $user_ids[] = $subscription->post_author;
    }

    // Also check custom meta for back in stock subscribers
    $stock_subscribers = get_posts(array(
        'post_type' => 'push_notification_stock_sub',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => '_stock_product_id',
                'value' => $product_id
            ),
            array(
                'key' => '_stock_notified',
                'compare' => 'NOT EXISTS'
            )
        )
    ));

    foreach ($stock_subscribers as $subscriber) {
        $email = get_post_meta($subscriber->ID, '_stock_email', true);
        if ($email) {
            $user = get_user_by('email', $email);
            if ($user) {
                $user_ids[] = $user->ID;
            }
        }
        // Mark as notified
        update_post_meta($subscriber->ID, '_stock_notified', '1');
    }

    // Remove duplicates
    $user_ids = array_unique($user_ids);

    // Notify each user
    foreach ($user_ids as $user_id) {
        $user = get_user_by('id', $user_id);
        if (!$user) continue;

        $data = array(
            'title' => 'Back in Stock!',
            'body' => $product->get_name() . ' is back in stock! Don\'t miss out.',
            'icon' => get_option('push_notification_icon', ''),
            'action_title' => 'Buy Now',
            'action_url' => get_permalink($product_id),
            'timestamp' => time()
        );

        set_transient('push_notification_back_in_stock_' . $user_id, $data, 300);
    }
}

// Low Stock Alert (Admin Notification)
function push_notification_low_stock($product) {
    if (!get_option('push_notification_woocommerce_low_stock', '0')) {
        return;
    }

    $product_id = $product->get_id();
    $product_name = $product->get_name();
    $stock = $product->get_stock_quantity();

    // Get admin users
    $admin_users = get_users(array(
        'role' => 'administrator',
        'fields' => 'ID'
    ));

    foreach ($admin_users as $admin_id) {
        $data = array(
            'title' => 'Low Stock Alert',
            'body' => $product_name . ' is running low on stock. Current quantity: ' . $stock,
            'icon' => get_option('push_notification_icon', ''),
            'action_title' => 'Edit Product',
            'action_url' => get_edit_post_link($product_id),
            'timestamp' => time()
        );

        set_transient('push_notification_low_stock_' . $admin_id, $data, 300);
    }
}

// Hook for custom triggers
function push_notification_track_api($request) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'push_notification_analytics';

    $params = $request->get_params();

    $wpdb->insert(
        $table_name,
        array(
            'notification_id' => $params['notification_id'],
            'event_type' => $params['event_type'],
            'user_id' => get_current_user_id(),
            'user_ip' => push_notification_get_user_ip(),
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'session_id' => $params['session_id'] ?? '',
            'variant' => $params['variant'] ?? 'A',
        ),
        array('%d', '%s', '%d', '%s', '%s', '%s', '%s')
    );

    return array('success' => true);
}

function push_notification_email_fallback_api($request) {
    $params = $request->get_params();
    push_notification_send_email_fallback($params['notification_id']);
    return array('success' => true);
}

function push_notification_consent_api($request) {
    $user_id = get_current_user_id();
    if (!$user_id) {
        return new WP_Error('not_logged_in', 'User must be logged in', array('status' => 401));
    }

    // Sync to CRM
    push_notification_sync_to_crm($user_id);

    return array('success' => true);
}

function push_notification_sync_to_crm($user_id) {
    $api_key = get_option('push_notification_mailchimp_api_key');
    $list_id = get_option('push_notification_mailchimp_list_id');

    if (!$api_key || !$list_id) {
        return;
    }

    $user = get_user_by('id', $user_id);
    if (!$user) return;

    $data_center = substr($api_key, strpos($api_key, '-') + 1);
    $url = 'https://' . $data_center . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/';

    $member_id = md5(strtolower($user->user_email));

    $data = array(
        'email_address' => $user->user_email,
        'status' => 'subscribed',
        'merge_fields' => array(
            'FNAME' => $user->first_name,
            'LNAME' => $user->last_name
        )
    );

    $response = wp_remote_post($url . $member_id, array(
        'method' => 'PUT',
        'headers' => array(
            'Authorization' => 'Basic ' . base64_encode('user:' . $api_key),
            'Content-Type' => 'application/json'
        ),
        'body' => json_encode($data)
    ));

    if (is_wp_error($response)) {
        error_log('Mailchimp sync error: ' . $response->get_error_message());
    }
}

function push_notification_send_email_fallback($notification_id, $user_id = null) {
    if (!get_option('push_notification_email_fallback')) {
        return;
    }

    $user_id = $user_id ?: get_current_user_id();
    if (!$user_id) {
        return; // Only for logged-in users
    }

    $user = get_user_by('id', $user_id);
    if (!$user) {
        return;
    }

    $post = get_post($notification_id);
    if (!$post) {
        return;
    }

    $title = $post->post_title;
    $body = get_post_meta($post->ID, '_push_notification_body', true);
    $icon = get_post_meta($post->ID, '_push_notification_icon', true);
    $action_url = get_post_meta($post->ID, '_push_notification_action_url', true);

    $subject = 'Push Notification: ' . $title;
    $message = "You have a new notification:\n\n";
    $message .= "Title: $title\n";
    $message .= "Message: $body\n";
    if ($action_url) {
        $message .= "Action: $action_url\n";
    }

    wp_mail($user->user_email, $subject, $message);
}

function push_notification_get_user_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? '';
    }
}

function push_notification_trigger($data) {
    set_transient('push_notification_api', $data, 300);
    do_action('push_notification_triggered', $data);
}

// New Product Launch Notification
function push_notification_new_product($post_id, $post) {
    if (!get_option('push_notification_woocommerce_new_product', '0')) {
        return;
    }

    // Only trigger for new products being published
    if ($post->post_type !== 'product' || $post->post_status !== 'publish') {
        return;
    }

    // Check if this is a new product (not an update)
    $product = wc_get_product($post_id);
    if (!$product) {
        return;
    }

    $product_name = $product->get_name();
    $product_url = get_permalink($post_id);
    $price = wc_price($product->get_price());

    // Get product categories for targeting
    $categories = get_the_terms($post_id, 'product_cat');
    $category_names = array();
    if ($categories && !is_wp_error($categories)) {
        foreach ($categories as $category) {
            $category_names[] = $category->name;
        }
    }

    $category_text = !empty($category_names) ? ' in ' . implode(', ', $category_names) : '';

    $data = array(
        'title' => 'New Product Launch!',
        'body' => 'Check out ' . $product_name . ' - Now available for ' . $price . $category_text,
        'icon' => get_option('push_notification_icon', ''),
        'action_title' => 'Shop Now',
        'action_url' => $product_url,
        'timestamp' => time()
    );

    set_transient('push_notification_api', $data, 300);
    do_action('push_notification_triggered', $data);
}

// Sale/Flash Sale Alert Notification
function push_notification_sale_alert($post_id, $post) {
    if (!get_option('push_notification_woocommerce_sale_alert', '0')) {
        return;
    }

    // Only trigger for products on sale
    if ($post->post_type !== 'product' || $post->post_status !== 'publish') {
        return;
    }

    $product = wc_get_product($post_id);
    if (!$product || !$product->is_on_sale()) {
        return;
    }

    $product_name = $product->get_name();
    $regular_price = wc_price($product->get_regular_price());
    $sale_price = wc_price($product->get_sale_price());
    $discount = round(100 - ($product->get_sale_price() / $product->get_regular_price() * 100), 0);

    $data = array(
        'title' => '🔥 Flash Sale!',
        'body' => $product_name . ' is ' . $discount . '% OFF! Was ' . $regular_price . ', now ' . $sale_price,
        'icon' => get_option('push_notification_icon', ''),
        'action_title' => 'Grab Deal',
        'action_url' => get_permalink($post_id),
        'timestamp' => time()
    );

    set_transient('push_notification_api', $data, 300);
    do_action('push_notification_triggered', $data);
}

// Review Reminder Notification
function push_notification_review_reminder($order_id, $old_status, $new_status, $order) {
    if (!get_option('push_notification_woocommerce_review_reminder', '0')) {
        return;
    }

    // Send review reminder when order is completed
    if ($new_status === 'completed') {
        $user_id = $order->get_user_id();
        if (!$user_id) {
            return;
        }

        $order_number = $order->get_order_number();

        $data = array(
            'title' => 'How was your purchase?',
            'body' => 'Thanks for order #' . $order_number . '! We\'d love to hear your feedback.',
            'icon' => get_option('push_notification_icon', ''),
            'action_title' => 'Write Review',
            'action_url' => wc_get_endpoint_url('orders', '', wc_get_page_permalink('myaccount')),
            'timestamp' => time()
        );

        set_transient('push_notification_review_reminder_' . $user_id, $data, 300);
    }
}

// Coupon/Promotion Notification
function push_notification_send_coupon($coupon_code, $coupon_amount, $coupon_type, $coupon_description) {
    if (!get_option('push_notification_woocommerce_coupon', '0')) {
        return;
    }

    $coupon = new WC_Coupon($coupon_code);
    if (!$coupon->get_id()) {
        return;
    }

    $discount_text = '';
    if ($coupon_type === 'percent') {
        $discount_text = $coupon_amount . '% OFF';
    } else {
        $discount_text = wc_price($coupon_amount) . ' OFF';
    }

    $data = array(
        'title' => '🎁 Special Offer!',
        'body' => 'Use code ' . strtoupper($coupon_code) . ' for ' . $discount_text . '. ' . $coupon_description,
        'icon' => get_option('push_notification_icon', ''),
        'action_title' => 'Shop Now',
        'action_url' => get_permalink(wc_get_page_id('shop')),
        'timestamp' => time()
    );

    set_transient('push_notification_coupon', $data, 300);
    do_action('push_notification_triggered', $data);
}

// Register new hooks for additional features
add_action('publish_product', 'push_notification_new_product', 10, 2);
add_action('save_post_product', 'push_notification_sale_alert', 10, 2);
add_action('woocommerce_order_status_changed', 'push_notification_review_reminder', 10, 4);