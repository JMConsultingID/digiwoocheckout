<?php
// Main plugin file
require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');

class DigiWooCheckout_List_Table extends WP_List_Table {

    public function __construct() {
        parent::__construct([
            'singular' => __('Rule', 'digiwoocheckout'),
            'plural'   => __('Rules', 'digiwoocheckout'),
            'ajax'     => false
        ]);
    }

    public function get_columns() {
        return [
            'product'    => __('Product', 'digiwoocheckout'),
            'addon'      => __('Addon', 'digiwoocheckout'),
            'program_id' => __('Program ID', 'digiwoocheckout'),
            'actions'    => __('Actions', 'digiwoocheckout')
        ];
    }

    public function prepare_items() {
        $columns = $this->get_columns();
        $this->_column_headers = array($columns);

        $this->items = digiwoocheckout_get_rules();
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'product':
            case 'addon':
            case 'program_id':
                return $item[$column_name];
            case 'actions':
                return sprintf('<a href="?page=%s&action=delete&rule_id=%s">%s</a>', $_REQUEST['page'], $item['program_id'], __('Delete', 'digiwoocheckout'));
            default:
                return print_r($item, true);
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
    // Check if the user submitted a new rule
    if(isset($_POST['product'], $_POST['addon'], $_POST['program_id'])) {
        $new_rule = array(
            'product'    => sanitize_text_field($_POST['product']),
            'addon'      => sanitize_text_field($_POST['addon']),
            'program_id' => sanitize_text_field($_POST['program_id'])
        );
        digiwoocheckout_add_rule($new_rule);
    }

    $table = new DigiWooCheckout_List_Table();
    $table->prepare_items();

    echo '<div class="wrap">';
    echo '<h1>' . __('DigiWooCheckout Rules', 'digiwoocheckout') . '</h1>';

    // Input form for new rules
    echo '<form method="post">';
    echo '<label for="product">' . __('Product:', 'digiwoocheckout') . '</label>';
    echo '<input type="text" name="product" required>';
    
    echo '<label for="addon">' . __('Addon:', 'digiwoocheckout') . '</label>';
    echo '<input type="text" name="addon" required>';
    
    echo '<label for="program_id">' . __('Program ID:', 'digiwoocheckout') . '</label>';
    echo '<input type="text" name="program_id" required>';

    echo '<input type="submit" value="' . __('Add Rule', 'digiwoocheckout') . '">';
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

    // add_settings_field(
    //     'digiwoo_title',
    //     'Title',
    //     'digiwoo_title_callback',
    //     'digiwoocheckout',
    //     'digiwoo_main_section'
    // );
    // register_setting('digiwoo_settings', 'digiwoo_title');

    // add_settings_field(
    //     'digiwoo_description',
    //     'Description',
    //     'digiwoo_description_callback',
    //     'digiwoocheckout',
    //     'digiwoo_main_section'
    // );
    // register_setting('digiwoo_settings', 'digiwoo_description');

    add_settings_field(
        'digiwoo_enable',
        'Enable Plugin',
        'digiwoo_enable_callback',
        'digiwoocheckout',
        'digiwoo_main_section'
    );
    register_setting('digiwoo_settings', 'digiwoo_enable');
}

add_action('admin_init', 'digiwoo_settings_init');

function digiwoo_title_callback() {
    // $title = get_option('digiwoo_title', '');
    // echo "<input type='text' name='digiwoo_title' value='$title' />";
}

function digiwoo_description_callback() {
    // $description = get_option('digiwoo_description', '');
    // echo "<textarea name='digiwoo_description' rows='5' cols='40'>$description</textarea>";
}

function digiwoo_enable_callback() {
    $checked = get_option('digiwoo_enable', '0') == '1' ? 'checked' : '';
    echo "<input type='checkbox' name='digiwoo_enable' value='1' $checked />";
}

function digiwoocheckout_get_rules() {
    return get_option('digiwoocheckout_rules', array());
}

function digiwoocheckout_add_rule($rule) {
    $rules = digiwoocheckout_get_rules();
    $rules[] = $rule;
    update_option('digiwoocheckout_rules', $rules);
}
