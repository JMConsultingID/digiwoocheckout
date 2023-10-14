<?php
class Elementor_Digiwoo_Checkout_Elementor_Widget extends \Elementor\Widget_Base {

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
		return [ 'digiwoocheckout-category' ];
	}

	public function get_keywords() {
		return [ 'digiwoo', 'checkout' ];
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
        $settings = $this->get_settings_for_display();?>

        <section id="digiwoo-checkout-section" class="digiwoo-checkout-section my-3" style="margin-top:50px;">
        <div class="container">
        	<p class="text-center">
		        <?php       
		        if (!empty($settings['selected_product'])) {
		            // Display product data and other checkout functionalities.
		            echo "Selected Product ID: " . $settings['selected_product'];
		            // You can expand upon this, add add-to-cart function, and more.
		        }
		        ?>
    		</p>
    	</div>
    	</section>
        <?php
    }
}