<?php
class Elementor_Digiwoo_Checkout_Elementor_Widget extends \Elementor\Widget_Base {

    public function get_name() {
        return 'digiwoo_checkout';
    }

    public function get_title() {
        return __('DigiWoo Checkout', 'digiwoocheckout');
    }

    public function get_icon() {
        return 'eicon-image-hotspot';
    }

    public function get_categories() {
		return [ 'digiwoocheckout-category' ];
	}

	public function get_keywords() {
		return [ 'digiwoo', 'checkout' ];
	}

    protected function register_controls() {


        $this->start_controls_section(
            'content_section',
            [
                'label' => esc_html__('Content', 'digiwoocheckout'),
                'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
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
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $product_options,
	            'default' => 'internal',
                'default' => ''
            ]
        );

        $this->end_controls_section();
    }

    protected function render() {
        $settings = $this->get_settings_for_display();?>

        <section id="digiwoo-checkout-section" class="digiwoo-checkout-section my-3" style="margin-top:50px;">
        <div class="row">
        	<p class="text-center">
		        <?php       
		        if (!empty($settings['selected_product'])) {
		            // Display product data and other checkout functionalities.
		            echo "Selected Product ID: " . $settings['selected_product'];
		            // You can expand upon this, add add-to-cart function, and more.
		        }
		        ?>
    		</p>
    		<?php
    		$product = wc_get_product($settings['selected_product']);
			if ($product) {
			    echo $product->get_image()."<br/>"; // Displays the product image
			    echo $product->get_name()."<br/>"; // Product title
			    echo $product->get_price_html()."<br/>"; // Price
			}
    		?>
    	</div>
    	</section>
        <?php
    }
}