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

// Define constants for paths
define('DIGIWOO_PATH', plugin_dir_path(__FILE__));
define('DIGIWOO_URL', plugin_dir_url(__FILE__));

// Include other necessary files and classes here.
require_once DIGIWOO_PATH . 'includes/admin-settings.php';
// Register the activation hook
register_activation_hook(__FILE__, 'digiwoocheckout_create_rules_table');


function add_digiwoocheckout_categories( $elements_manager ) {
    $elements_manager->add_category(
        'digiwoocheckout-category',
        [
            'title' => esc_html__( 'Digiwoo Checkout Widget', 'digiwoocheckout' ),
            'icon' => 'fa fa-plug',
        ]
    );
}
add_action( 'elementor/elements/categories_registered', 'add_digiwoocheckout_categories' );

function register_digiwoocheckout_widget( $widgets_manager ) {

    require_once DIGIWOO_PATH . 'widgets/digiwoo-checkout-elementor-widget.php';   

    $widgets_manager->register( new \Elementor_Digiwoo_Checkout_Elementor_Widget() );   
}
add_action( 'elementor/widgets/register', 'register_digiwoocheckout_widget' );