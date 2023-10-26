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

        $repeater = new \Elementor\Repeater();

        // Add a dropdown to select WooCommerce product categories in each repeater item
        $repeater->add_control(
            'product_category',
            [
                'label' => __( 'Select Product Category', 'plugin-name' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_product_categories_dropdown(),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'product_categories_list',
            [
                'label' => __( 'Product Categories', 'plugin-name' ),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ product_category }}}',
            ]
        );

        $this->end_controls_section();
    }

    private function get_product_categories_dropdown() {
        $categories = get_terms('product_cat');
        $dropdown = [];
        foreach ($categories as $category) {
            $dropdown[$category->term_id] = $category->name;
        }
        return $dropdown;
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

        // Check if WooCommerce is active
        if (!class_exists('WooCommerce')) {
            echo 'WooCommerce is not active';
            return;
        }

        // Fetch product categories
        $args = array(
            'taxonomy' => 'product_cat',
            'hide_empty' => false,
        );
        $product_categories = get_terms($args);

        // Check if there are product categories
        if (empty($product_categories) || is_wp_error($product_categories)) {
            echo 'No product categories found';
            return;
        }

        // Display the product categories
        echo '<ul class="digiwoo-product-categories">';
        foreach ($product_categories as $product_category) {
            echo '<li>' . esc_html($product_category->name) . '</li>';
        }
        echo '</ul>';


        if (!empty($settings['product_categories_list'])) {
            echo '<ul class="digiwoo-selected-product-categories">';
            foreach ($settings['product_categories_list'] as $item) {
                $term = get_term_by('id', $item['product_category'], 'product_cat');
                if ($term && !is_wp_error($term)) {
                    echo '<li>' . esc_html($term->name) . '</li>';
                }
            }
            echo '</ul>';
        }
    }
}