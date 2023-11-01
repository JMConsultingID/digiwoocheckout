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

        $this->add_control(
            'default_account_product_category',
            [
                'label' => __( 'Select Default Account Balance Category', 'plugin-name' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_product_categories_dropdown(),
                'label_block' => true,
            ]
        );

        $this->add_control(
            'add_on_product_category',
            [
                'label' => __( 'Select Add-On Product Category', 'plugin-name' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_product_categories_dropdown(),
                'label_block' => true,
            ]
        );

        $repeater = new \Elementor\Repeater();

        // Add-On Product Dropdown Control
        $repeater->add_control(
            'addon_product',
            [
                'label' => __('Add-On Product', 'text-domain'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_product_options(),  // You need to write this function to get product options
                'default' => '',
            ]
        );

         // To get product name based on the product ID for the title field
        $repeater->add_control(
            'addon_product_name',
            [
                'type' => \Elementor\Controls_Manager::HIDDEN,  // Hidden field
                'default' => '', // Default can be blank. The value will be populated when a product is selected
            ]
        );


        // Rule Dropdown Control
        $repeater->add_control(
            'rule_hide_on_category',
            [
                'label' => __('Hide Rule Based on Category', 'text-domain'),
                'type' => \Elementor\Controls_Manager::SELECT,
                'options' => $this->get_product_category_options(),  // Combining None option with categories
                'default' => 'none',
            ]
        );

        $this->add_control(
            'addon_products_list',
            [
                'label' => __('Add-On Products', 'text-domain'),
                'type' => \Elementor\Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'title_field' => '{{{ addon_product_name }}}',
            ]
        );

        $this->end_controls_section();
    }  
    

    protected function render() {
        $settings = $this->get_settings_for_display();

        $default_categories = $settings['product_categories_list'];

        // Display products from the Add-On category
        $default_account_category_id = $settings['default_account_product_category'];
        $default_products = $this->get_products_by_category($default_account_category_id);

        // Display products from the Add-On category
        $add_on_category_id = $settings['add_on_product_category'];
        $add_on_products = $this->get_products_by_category($add_on_category_id);

        echo '<form class="digiwoo-selected-product-categories">';

        if (!empty($default_categories)) {
            echo '<h4>Start a New Challenge:</h4>';            
            foreach ($settings['product_categories_list'] as $item) {
                $term = get_term_by('id', $item['product_category'], 'product_cat');
                if ($term && !is_wp_error($term)) {
                    echo '<label>';
                    echo '<input type="radio" name="product_category" value="' . esc_attr($term->term_id) . '">' . esc_html($term->name);
                    echo '</label><br>';
                }
            }
        }

        if (!empty($default_products)) {
            echo '<h4>Account Balance:</h4>';
            echo '<div id="products-container" class="products-container">'; // Wrapping div
            foreach ($default_products as $default_product) {
                $product = wc_get_product($default_product->ID);
                $price = $product ? $product->get_price() : 'N/A';
                echo '<label>';
                echo '<input type="radio" name="default_product" value="' . esc_attr($default_product->ID) . '" data-price="' . esc_attr($price) . '">' . esc_html($default_product->post_title) . ' (' . get_woocommerce_currency_symbol() . $price . ')';
                echo '</label><br>';
            }
            echo '</div>'; // Closing wrapping div
        }

        // if (!empty($add_on_products)) {
        //     echo '<h4>Add-On Products:</h4>';
        //     foreach ($add_on_products as $add_on_product) {
        //         echo '<label>';
        //         echo '<input type="radio" name="add_on_product" value="' . esc_attr($add_on_product->ID) . '">' . esc_html($add_on_product->post_title);
        //         echo '</label><br>';
        //     }
        // }

        if (!empty($settings['addon_products_list'])) {
            echo '<div class="digiwoo-add-on-products">';
            echo '<h4>Add-On Products:</h4>';
            foreach ($settings['addon_products_list'] as $addon) {
                $product = wc_get_product($addon['addon_product']);
                
                if ($product) {
                    $price = $product->get_price();
                    $percentage = $price / 100;

                    echo '<div class="addon-product" data-hide-rule="' . esc_attr($addon['rule_hide_on_category']) . '">';
                    echo '<label>';
                    echo '<input type="checkbox" name="addon_product" value="' . esc_attr($product->get_id()) . '" data-percentage="' . esc_attr($percentage) . '">'. esc_html($product->get_name());
                    echo '</label>';
                    echo '</div>';
                }
            }
            echo '</div>';

            echo '<div class="digiwoo-total-price">';
            echo '<h4>Total</h4>';
            echo '<div id="total-price">Total Price: <span>0</span></div>';
            echo '</div>';

        }


        echo '</form>';

    }

    private function get_product_categories_dropdown() {
        $categories = get_terms('product_cat');
        $dropdown = [];
        foreach ($categories as $category) {
            $dropdown[$category->term_id] = $category->name;
        }
        return $dropdown;
    }

    private function get_products_by_category($category_id) {
        $args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'tax_query' => array(
                array(
                    'taxonomy' => 'product_cat',
                    'field'    => 'id',
                    'terms'    => $category_id,
                    'operator' => 'IN',
                ),
            ),
        );
        $query = new WP_Query($args);
        return $query->posts;
    }

    // Sample function to get product options
    protected function get_product_options() {
        $products = get_posts(['post_type' => 'product', 'numberposts' => -1]);
        $options = [];
        foreach ($products as $product) {
            $options[$product->ID] = $product->post_title;
        }
        return $options;
    }

    // Sample function to get product category options
    protected function get_product_category_options() {
        $categories = get_terms(['taxonomy' => 'product_cat']);
        $options = ['none' => 'None'];  // Initialize with the None option
        foreach ($categories as $category) {
            $options[$category->term_id] = $category->name;
        }
        return $options;
    }

}