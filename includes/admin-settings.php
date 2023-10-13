<?php
function digiwoo_admin_menu() {
    add_options_page(
        'DigiWooCheckout Settings',
        'DigiWooCheckout',
        'manage_options',
        'digiwoocheckout',
        'digiwoo_settings_page'
    );
}
add_action('admin_menu', 'digiwoo_admin_menu');

function digiwoo_settings_page() {
    ?>
    <div class="wrap">
        <h2>DigiWooCheckout Settings</h2>
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

function digiwoo_settings_init() {
    add_settings_section(
        'digiwoo_main_section',
        'Main Settings',
        null,
        'digiwoocheckout'
    );

    add_settings_field(
        'digiwoo_title',
        'Title',
        'digiwoo_title_callback',
        'digiwoocheckout',
        'digiwoo_main_section'
    );
    register_setting('digiwoo_settings', 'digiwoo_title');

    add_settings_field(
        'digiwoo_description',
        'Description',
        'digiwoo_description_callback',
        'digiwoocheckout',
        'digiwoo_main_section'
    );
    register_setting('digiwoo_settings', 'digiwoo_description');

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
    $title = get_option('digiwoo_title', '');
    echo "<input type='text' name='digiwoo_title' value='$title' />";
}

function digiwoo_description_callback() {
    $description = get_option('digiwoo_description', '');
    echo "<textarea name='digiwoo_description' rows='5' cols='40'>$description</textarea>";
}

function digiwoo_enable_callback() {
    $checked = get_option('digiwoo_enable', '0') == '1' ? 'checked' : '';
    echo "<input type='checkbox' name='digiwoo_enable' value='1' $checked />";
}
