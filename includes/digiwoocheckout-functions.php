<?php
/*
Plugin Name: DigiWooCheckout
Description: Custom Fast Checkout page for WooCommerce using Elementor.
Version: 1.0.1
Author: Ardika JM-Consulting
*/

// Exit if accessed directly.
if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_dgc_fetch_products_by_category', 'dgc_fetch_products_by_category');
add_action('wp_ajax_nopriv_dgc_fetch_products_by_category', 'dgc_fetch_products_by_category');

function dgc_fetch_products_by_category() {
    $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
    $args = array(
        'post_type' => 'product',
        'tax_query' => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'id',
                'terms'    => $category_id,
            ),
        ),
    );
    $products = get_posts($args);
    $output = '';
    foreach ($products as $product_post) {
        $product = wc_get_product($product_post->ID);
        $price = $product ? $product->get_price() : 'N/A';
        $output .= '<label>';
        $output .= '<input type="radio" name="default_product" value="' . esc_attr($product_post->ID) . '" data-price="' . esc_attr($price) . '">' . esc_html($product_post->post_title) . ' (' . get_woocommerce_currency_symbol() . $price . ')';
        $output .= '</label><br>';
    }
    echo $output;
    wp_die();
}

add_action('wp_ajax_create_order', 'create_order_callback');
add_action('wp_ajax_nopriv_create_order', 'create_order_callback');

function create_order_callback() {
    $product_id = intval($_POST['product_id']);
    $addon_products = isset($_POST['addon_products']) ? array_map('intval', $_POST['addon_products']) : [];
    $total_price = floatval($_POST['total_price']);

    // Create a new WooCommerce order
    $order = wc_create_order();
    $order->add_product(get_product($product_id), 1); // Adding the main product to order

    // Loop through and add addon products
    foreach($addon_products as $addon_id) {
        $order->add_product(get_product($addon_id), 1);
    }

    $order->set_total($total_price); // Setting the total price
    $order->save(); // Saving the order

    // Send a response back
    echo json_encode(array('success' => true, 'order_id' => $order->get_id()));
    wp_die();
}

