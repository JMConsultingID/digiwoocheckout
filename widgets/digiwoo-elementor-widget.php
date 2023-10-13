<?php
use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class DigiWooCheckout_Widget extends Widget_Base {

    public function get_name() {
        return 'digiwoocheckout';
    }

    public function get_title() {
        return __('DigiWooCheckout', 'digiwoocheckout');
    }

    public function get_icon() {
        return 'eicon-woocommerce'; // For example, use the WooCommerce icon. You can pick other icons from Elementor's icon list.
    }

    public function get_categories() {
        return ['general']; // Category where the widget will reside in Elementor's panel
    }

    protected function _register_controls() {
        $this->start_controls_section(
            'content_section',
            [
                'label' => __('Content', 'digiwoocheckout'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );

        // Add product selection control
        $products = get_posts(['post_type' => 'product', 'numberposts' => -1]);
        $product_options = [];
        foreach ($products as $product) {
            $product_options[$product->ID] = $product->post_title;
        }

        $this->add_control(
            'selected_product',
            [
                'label' => __('Select Product', 'digiwoocheckout'),
                'type' => Controls_Manager::SELECT,
                'options' => $product_options,
                'default' => ''
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();
        
        if (!empty($settings['selected_product'])) {
            // Display product data and other checkout functionalities.
            echo "Selected Product ID: " . $settings['selected_product'];
            // You can expand upon this, add add-to-cart function, and more.
        }
    }
}

// Register the widget when Elementor is loaded
add_action('elementor/widgets/widgets_registered', function($widget_manager){
    require_once DIGIWOO_PATH . 'widgets/digiwoo-elementor-widget.php';
    $widget_manager->register_widget_type(new DigiWooCheckout_Widget());
});
