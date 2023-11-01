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
add_action('wp_ajax_fetch_products_by_category', 'dgc_fetch_products_by_category');
add_action('wp_ajax_nopriv_fetch_products_by_category', 'dgc_fetch_products_by_category');
