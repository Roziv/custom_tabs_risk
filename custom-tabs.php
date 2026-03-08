<?php
/**
 * Plugin Name: Custom Tabs
 * Description: A customized tabs plugin.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Register the main plugin admin menu.
 */
function custom_tabs_register_menu_page() {
    add_menu_page(
        __( 'Custom Tabs', 'custom-tabs' ),
        __( 'Custom Tabs', 'custom-tabs' ),
        'manage_options',
        'custom-tabs-settings',
        'custom_tabs_render_settings_page',
        'dashicons-index-card',
        30
    );
}
add_action( 'admin_menu', 'custom_tabs_register_menu_page' );

/**
 * Register plugin settings.
 */
function custom_tabs_register_settings() {
    register_setting( 'custom_tabs_settings_group', 'custom_tabs_data' );
}
add_action( 'admin_init', 'custom_tabs_register_settings' );

/**
 * Enqueue admin scripts and styles.
 */
function custom_tabs_enqueue_admin_assets( $hook ) {
    if ( 'toplevel_page_custom-tabs-settings' !== $hook ) {
        return;
    }

    wp_enqueue_media(); // Needed for image uploads

    wp_enqueue_style(
        'custom-tabs-admin-css',
        plugin_dir_url( __FILE__ ) . 'assets/admin.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'custom-tabs-admin-js',
        plugin_dir_url( __FILE__ ) . 'assets/admin.js',
        array( 'jquery' ), // We will write vanilla JS, but WP media uploader relies on jQuery
        '1.0.0',
        true
    );
}
add_action( 'admin_enqueue_scripts', 'custom_tabs_enqueue_admin_assets' );

/**
 * Render the settings page.
 */
function custom_tabs_render_settings_page() {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    
    // Get existing data
    $tabs_data = get_option( 'custom_tabs_data', '[]' );
    
    ?>
    <div class="wrap custom-tabs-wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        
        <form action="options.php" method="post" id="custom-tabs-form">
            <?php
            // Output security fields for the registered setting
            settings_fields( 'custom_tabs_settings_group' );
            ?>
            
            <div id="custom-tabs-container">
                <!-- Javascript will render the interactive tabs here -->
            </div>
            
            <button type="button" class="button button-secondary" id="custom-tabs-add-tab">
                <?php esc_html_e( '+ Add New Tab', 'custom-tabs' ); ?>
            </button>
            
            <!-- Hidden input to store the JSON data -->
            <input type="hidden" name="custom_tabs_data" id="custom_tabs_data" value="<?php echo esc_attr( $tabs_data ); ?>">
            
            <?php submit_button( __( 'Save Changes', 'custom-tabs' ) ); ?>
        </form>
    </div>
    <?php
}
