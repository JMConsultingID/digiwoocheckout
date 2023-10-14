<?php
// Main plugin file
require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

function digiwoocheckout_create_rules_table() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'digiwoocheckout_rules';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        product mediumint(9) NOT NULL,
        addon mediumint(9) NOT NULL,
        program_id text NOT NULL,
        PRIMARY KEY (id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}


class DigiWooCheckout_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct([
            'singular' => __('Rule', 'digiwoocheckout'),
            'plural'   => __('Rules', 'digiwoocheckout'),
            'ajax'     => false
        ]);
    }

    public function get_columns() {
        $columns = array(
            'product_name' => __('Product', 'digiwoocheckout'),
            'addon_name'   => __('Addon', 'digiwoocheckout'),
            'program_id'   => __('Program ID', 'digiwoocheckout'),
            'actions'      => __('Actions', 'digiwoocheckout')
        );
        return $columns;
    }


    public function prepare_items() {
        $columns = $this->get_columns();
        $this->_column_headers = array($columns);
        
        $this->items = digiwoocheckout_get_rules();
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'product_name':
                return esc_html($item['product_name']);
            case 'addon_name':
                return esc_html($item['addon_name']);
            case 'program_id':
                return esc_html($item['program_id']);
            case 'actions':
                // Example: Add a delete link and an edit link. 
                $delete_link = sprintf('<a href="?page=%s&action=delete&rule_id=%s">Delete</a>', $_REQUEST['page'], $item['id']);
                $edit_link = sprintf('<a href="?page=%s&action=edit&rule_id=%s">Edit</a>', $_REQUEST['page'], $item['id']);
                return $edit_link . ' | ' . $delete_link;
            default:
                return print_r($item, true);  // For debugging purposes
        }
    }
}

function digiwoo_admin_menu() {
    add_menu_page(
        'Digiwoo Checkout Settings',
        'General Setting',
        'manage_options',
        'digiwoocheckout',
        'digiwoo_settings_page',
        'dashicons-shield', 
        22
    );
    add_submenu_page('digiwoocheckout', 'Setup Rule', 'Setup Rule', 'manage_options', 'digiwoocheckout-setup-rule', 'digiwoo_setup_rule');
}
add_action('admin_menu', 'digiwoo_admin_menu');

function digiwoo_settings_page() {
    ?>
    <div class="wrap">
        <h2>DigiWoo Checkout Settings</h2>
        <form method="post" action="options.php">
            <?php
                settings_fields('digiwoo_settings');
                do_settings_sections('digiwoocheckout');
                submit_button();
            ?>
        </form>
    </div>
    <?php
}

function digiwoo_setup_rule() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'digiwoocheckout_rules';

    // 1. Check $_GET for delete or edit actions
    if(isset($_GET['action']) && isset($_GET['rule_id'])) {
        if($_GET['action'] === 'delete') {
            $wpdb->delete($table_name, array('id' => $_GET['rule_id']));
            wp_redirect(admin_url('admin.php?page=digiwoocheckout-setup-rule'));
            exit;
        } elseif($_GET['action'] === 'edit') {
            // If edit action is clicked, populate the $current_rule with the existing values
            $current_rule = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_GET['rule_id']), ARRAY_A);
        }
    }

    // 2. Check if the user submitted a new rule or edits
    if(isset($_POST['product'], $_POST['addon'], $_POST['program_id'])) {
        $rule_data = array(
            'product'    => sanitize_text_field($_POST['product']),
            'addon'      => sanitize_text_field($_POST['addon']),
            'program_id' => sanitize_text_field($_POST['program_id'])
        );

        // If edit form is submitted, update the rule
        if(isset($_POST['rule_id']) && $_POST['rule_id']) {
            $wpdb->update($table_name, $rule_data, array('id' => $_POST['rule_id']));
            wp_redirect(admin_url('admin.php?page=digiwoocheckout-setup-rule'));
            exit;
        } else {
            // Otherwise, it's a new rule
            digiwoocheckout_add_rule($rule_data);
            wp_redirect(admin_url('admin.php?page=digiwoocheckout-setup-rule'));
            exit;
        }
    }

    $table = new DigiWooCheckout_List_Table();
    $table->prepare_items();

    $products = digiwoocheckout_get_woocommerce_products('exclude');
    $addons = digiwoocheckout_get_woocommerce_products('include');

    echo '<div class="wrap">';

    echo '<h1>' . __('DigiWooCheckout Rules', 'digiwoocheckout') . '</h1>';

    // Styles for the table layout
    echo '<style>
        .digiwoocheckout-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .digiwoocheckout-table th, .digiwoocheckout-table td {
            padding: 8px 12px;
            border: 1px solid #ddd;
        }
        .digiwoocheckout-table th {
            background-color: #f5f5f5;
            text-align: left;
        }
    </style>';

    // Check if we are editing a rule and populate the form
    $editing_rule = isset($current_rule) ? $current_rule : array('product' => '', 'addon' => '', 'program_id' => '');
    
    echo '<form method="post">';
    echo '<table class="digiwoocheckout-table">';
    echo '<thead>';
    echo '<tr>';
    echo '<th>' . __('Product', 'digiwoocheckout') . '</th>';
    echo '<th>' . __('Addon', 'digiwoocheckout') . '</th>';
    echo '<th>' . __('Program ID', 'digiwoocheckout') . '</th>';
    echo '<th>&nbsp;</th>'; // This is for the submit button column
    echo '</tr>';
    echo '</thead>';
    echo '<tbody>';
    echo '<tr>';
    
    // Product dropdown
    echo '<td>';
    echo '<select name="product" style="width: 100%;">';
    foreach($products as $product) {
        $selected = ($product->ID == $editing_rule['product']) ? 'selected' : '';
        echo '<option value="' . esc_attr($product->ID) . '" ' . $selected . '>' . esc_html($product->post_title) . '</option>';
    }
    echo '</select>';
    echo '</td>';

    // Addon dropdown
    echo '<td>';
    echo '<select name="addon" style="width: 100%;">';
    foreach($addons as $addon) {
        $selected = ($addon->ID == $editing_rule['addon']) ? 'selected' : '';
        echo '<option value="' . esc_attr($addon->ID) . '" ' . $selected . '>' . esc_html($addon->post_title) . '</option>';
    }
    echo '</select>';
    echo '</td>';

    // Program ID
    echo '<td>';
    echo '<input type="text" name="program_id" required style="width: 100%;" value="' . esc_attr($editing_rule['program_id']) . '">';
    echo '</td>';

    // Submit button
    echo '<td>';
    if (isset($current_rule)) {
        echo '<input type="hidden" name="rule_id" value="' . esc_attr($current_rule['id']) . '">';
        echo '<input type="submit" value="' . __('Update Rule', 'digiwoocheckout') . '">';
    } else {
        echo '<input type="submit" value="' . __('Add Rule', 'digiwoocheckout') . '">';
    }
    echo '</td>';
    
    echo '</tr>';
    echo '</tbody>';
    echo '</table>';
    echo '</form>';

    $table->display();
    echo '</div>';

}

function digiwoo_settings_init() {
    add_settings_section(
        'digiwoo_main_section',
        'Main Settings',
        null,
        'digiwoocheckout'
    );

    add_settings_field(
        'digiwoo_enable',
        'Enable Plugin',
        'digiwoo_enable_callback',
        'digiwoocheckout',
        'digiwoo_main_section'
    );
    register_setting('digiwoo_settings', 'digiwoo_enable');

    add_settings_field(
        'digiwoo_category',
        'Select Addon Category',
        'digiwoo_get_category_callback',
        'digiwoocheckout',
        'digiwoo_main_section'
    );
    register_setting('digiwoo_settings', 'digiwoo_category');
}

add_action('admin_init', 'digiwoo_settings_init');


function digiwoo_enable_callback() {
    $checked = get_option('digiwoo_enable', '0') == '1' ? 'checked' : '';
    echo "<input type='checkbox' name='digiwoo_enable' value='1' $checked />";
}

function digiwoo_get_category_callback() {
    // Get the saved category ID from the WordPress options
    $saved_category_id = get_option('digiwoo_category', '');

    // Fetch WooCommerce product categories
    $args = array(
        'taxonomy'   => 'product_cat',
        'orderby'    => 'name',
        'show_count' => 0,
        'pad_counts' => 0,
        'hierarchical' => 1,
        'title_li'   => '',
        'hide_empty' => 0
    );
    
    $product_categories = get_categories($args);

    echo '<select name="digiwoo_category">';
    foreach ($product_categories as $category) {
        $selected = ($category->term_id == $saved_category_id) ? 'selected="selected"' : '';
        echo '<option value="' . esc_attr($category->term_id) . '" ' . $selected . '>' . esc_html($category->name) . '</option>';
    }
    echo '</select>';
}


function digiwoocheckout_get_rules() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'digiwoocheckout_rules';

    $results = $wpdb->get_results("SELECT * FROM $table_name", ARRAY_A);

    // Enrich results with product and addon names.
    foreach($results as $index => $result) {
        $results[$index]['product_name'] = get_the_title($result['product']);
        $results[$index]['addon_name'] = get_the_title($result['addon']);
    }

    return $results;
}


function digiwoocheckout_add_rule($rule) {
    global $wpdb;

    // Insert rule into the database. $wpdb->insert will handle the auto-increment for the ID.
    $wpdb->insert(
        "{$wpdb->prefix}digiwoocheckout_rules",
        array(
            'product'    => $rule['product'],
            'addon'      => $rule['addon'],
            'program_id' => $rule['program_id']
        ),
        array('%d', '%d', '%s')  // data format for product, addon, and program_id respectively
    );
}

function digiwoocheckout_get_woocommerce_products($type = 'exclude') {
    // Get the saved category ID to be included or excluded
    $category_id = get_option('digiwoo_category', '');

    // Setting tax_query based on type 
    $tax_query = array(
        'taxonomy' => 'product_cat',
        'field'    => 'term_id',
        'terms'    => array($category_id),
    );

    if ($type === 'exclude') {
        $tax_query['operator'] = 'NOT IN';
    } elseif ($type === 'include') {
        $tax_query['operator'] = 'IN';
    }

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'tax_query'      => array($tax_query),
    );

    $products = get_posts($args);
    return $products;
}

function digiwoo_delete_rule($rule_id) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'digiwoocheckout_rules';
    
    // Delete rule from the table based on the ID
    $wpdb->delete($table_name, array('id' => $rule_id));
}

function digiwoo_edit_rule($rule_id, $data) {
    global $wpdb;
    $table_name = $wpdb->prefix . 'digiwoocheckout_rules';
    
    // Update the table based on the ID
    $wpdb->update($table_name, $data, array('id' => $rule_id));
}
