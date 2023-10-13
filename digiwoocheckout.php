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

// More code for widget registration, etc. will come here later.
