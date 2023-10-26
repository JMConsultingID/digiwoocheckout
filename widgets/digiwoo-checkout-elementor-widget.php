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
            'add_on_product_category',
            [
                'label' => __( 'Select Add-On Product Category', 'plugin-name' ),
                'type' => \Elementor\Controls_Manager::SELECT2,
                'options' => $this->get_product_categories_dropdown(),
                'label_block' => true,
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

    protected function render() {
        $settings = $this->get_settings_for_display();
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

        // Display products from the Add-On category
        $add_on_category_id = $settings['add_on_product_category'];
        $products = $this->get_products_by_category($add_on_category_id);

        echo '<h2>Add-On Products:</h2>';
        echo '<ul class="digiwoo-add-on-products">';
        foreach ($products as $product) {
            echo '<li>' . esc_html($product->get_name()) . '</li>';
        }
        echo '</ul>';
    }
}