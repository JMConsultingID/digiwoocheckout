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

        // For simplicity, I'm using dummy data here. 
        // In a real-world scenario, you'd fetch this data from your database.
        $dummy_data = [
            ['product' => 'Evaluation 5000', 'addon' => 'Raw Spreads', 'program_id' => '342c98a659a174b'],
            ['product' => 'Evaluation 10000', 'addon' => 'No Time Limit', 'program_id' => '423498a659a174b']
        ];

        $this->items = $dummy_data;
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
    $table = new DigiWooCheckout_List_Table();
    $table->prepare_items();
    ?>
    <div class="wrap">
        <h2>DigiWoo Checkout Setup Rule</h2>
        <?php $table->display(); ?>
    </div>
    <?php
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
