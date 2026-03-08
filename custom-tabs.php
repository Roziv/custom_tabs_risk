<?php
/**
 * Plugin Name: Custom Tabs
 * Description: A customized tabs plugin.
 * Version: 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * Register the main plugin admin menu.
 */
function custom_tabs_register_menu_page()
{
    add_menu_page(
        __('Custom Tabs', 'custom-tabs'),
        __('Custom Tabs', 'custom-tabs'),
        'manage_options',
        'custom-tabs-settings',
        'custom_tabs_render_settings_page',
        'dashicons-index-card',
        30
    );
}
add_action('admin_menu', 'custom_tabs_register_menu_page');

/**
 * Register plugin settings.
 */
function custom_tabs_register_settings()
{
    register_setting('custom_tabs_settings_group', 'custom_tabs_data');
    register_setting('custom_tabs_settings_group', 'custom_tabs_logos_data');
    register_setting('custom_tabs_settings_group', 'custom_tabs_logos_title');
}
add_action('admin_init', 'custom_tabs_register_settings');

/**
 * Enqueue admin scripts and styles.
 */
function custom_tabs_enqueue_admin_assets($hook)
{
    if ('toplevel_page_custom-tabs-settings' !== $hook) {
        return;
    }

    wp_enqueue_media(); // Needed for image uploads

    wp_enqueue_style(
        'custom-tabs-admin-css',
        plugin_dir_url(__FILE__) . 'assets/admin.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'custom-tabs-admin-js',
        plugin_dir_url(__FILE__) . 'assets/admin.js',
        array('jquery'), // We will write vanilla JS, but WP media uploader relies on jQuery
        '1.0.0',
        true
    );
}
add_action('admin_enqueue_scripts', 'custom_tabs_enqueue_admin_assets');

/**
 * Render the settings page.
 */
function custom_tabs_render_settings_page()
{
    if (!current_user_can('manage_options')) {
        return;
    }

    // Get existing data
    $tabs_data = get_option('custom_tabs_data', '[]');
    $logos_data = get_option('custom_tabs_logos_data', '[]');
    $logos_title = get_option('custom_tabs_logos_title', '');

    ?>
    <div class="wrap custom-tabs-wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>

        <form action="options.php" method="post" id="custom-tabs-form">
            <?php
            // Output security fields for the registered setting
            settings_fields('custom_tabs_settings_group');
            ?>

            <div id="custom-tabs-container">
                <!-- Javascript will render the interactive tabs here -->
            </div>

            <button type="button" class="button button-secondary" id="custom-tabs-add-tab">
                <?php esc_html_e('+ Add New Tab', 'custom-tabs'); ?>
            </button>

            <hr style="margin: 40px 0;">
            <h2>Global Logos Section</h2>

            <div style="margin-bottom: 20px;">
                <label for="custom_tabs_logos_title" style="display:block; font-weight:600; margin-bottom:5px;">Section
                    Title / Description</label>
                <textarea name="custom_tabs_logos_title" id="custom_tabs_logos_title" rows="2"
                    class="large-text"><?php echo esc_textarea($logos_title); ?></textarea>
            </div>

            <div id="custom-logos-container">
                <!-- Javascript will render the interactive logos here -->
            </div>

            <button type="button" class="button button-secondary" id="custom-logos-add">
                <?php esc_html_e('+ Add New Logo', 'custom-tabs'); ?>
            </button>
            <br><br>

            <!-- Hidden inputs to store the JSON data -->
            <input type="hidden" name="custom_tabs_data" id="custom_tabs_data"
                value="<?php echo esc_attr($tabs_data); ?>">
            <input type="hidden" name="custom_tabs_logos_data" id="custom_tabs_logos_data"
                value="<?php echo esc_attr($logos_data); ?>">

            <?php submit_button(__('Save Changes', 'custom-tabs')); ?>
        </form>
    </div>
    <?php
}

/**
 * Enqueue frontend scripts and styles.
 */
function custom_tabs_enqueue_frontend_assets()
{
    wp_enqueue_style(
        'custom-tabs-front-css',
        plugin_dir_url(__FILE__) . 'assets/front.css',
        array(),
        '1.0.0'
    );

    wp_enqueue_script(
        'custom-tabs-front-js',
        plugin_dir_url(__FILE__) . 'assets/front.js',
        array(),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'custom_tabs_enqueue_frontend_assets');

/**
 * Render the Custom Tabs shortcode.
 */
function custom_tabs_shortcode($atts)
{
    $tabs_data_json = get_option('custom_tabs_data', '[]');
    $tabs_data = json_decode($tabs_data_json, true);

    $logos_data_json = get_option('custom_tabs_logos_data', '[]');
    $logos_data = json_decode($logos_data_json, true);

    $logos_title = get_option('custom_tabs_logos_title', '');

    if (empty($tabs_data) || !is_array($tabs_data)) {
        return '';
    }

    ob_start();
    ?>
    <div class="custom-tabs-wrapper">
        <!-- Tabs Navigation -->
        <div class="custom-tabs-nav">
            <?php foreach ($tabs_data as $index => $tab): ?>
                <button class="custom-tab-button <?php echo $index === 0 ? 'active' : ''; ?>"
                    data-tab="custom-tab-<?php echo esc_attr($index); ?>">
                    <?php echo esc_html($tab['title'] ?? ''); ?>
                </button>
            <?php endforeach; ?>
        </div>

        <!-- Tabs Content Panels -->
        <div class="custom-tabs-content">
            <?php foreach ($tabs_data as $index => $tab): ?>
                <div class="custom-tab-panel <?php echo $index === 0 ? 'active' : ''; ?>"
                    id="custom-tab-<?php echo esc_attr($index); ?>">

                    <div class="custom-tab-layout-row">
                        <div class="custom-tab-layout-col custom-tab-layout-left">
                            <?php if (!empty($tab['section1'])):
                                $s1 = $tab['section1']; ?>
                                <div class="custom-tab-section custom-tab-section-1">
                                    <?php if (!empty($s1['quote'])): ?>
                                        <div class="custom-tab-quote">"<?php echo esc_html($s1['quote']); ?>"</div>
                                    <?php endif; ?>

                                    <?php if (!empty($s1['image'])): ?>
                                        <img src="<?php echo esc_url($s1['image']); ?>" class="custom-tab-image" alt="Tab Image">
                                    <?php endif; ?>

                                    <?php if (!empty($s1['name']) || !empty($s1['title']) || !empty($s1['logo'])): ?>
                                        <div class="custom-tab-profile">
                                            <?php if (!empty($s1['logo'])): ?>
                                                <img src="<?php echo esc_url($s1['logo']); ?>" class="custom-tab-logo" alt="Tab Logo">
                                            <?php endif; ?>
                                            <div>
                                                <p class="custom-tab-name"><?php echo esc_html($s1['name'] ?? ''); ?></p>
                                                <p class="custom-tab-job-title"><?php echo esc_html($s1['title'] ?? ''); ?></p>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="custom-tab-layout-col custom-tab-layout-right">
                            <?php if (!empty($tab['section2'])):
                                $s2 = $tab['section2']; ?>
                                <div class="custom-tab-section custom-tab-section-2">
                                    <?php if (!empty($s2['box1'])): ?>
                                        <p><?php echo esc_html($s2['box1']); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($s2['box2'])): ?>
                                        <p><?php echo esc_html($s2['box2']); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($s2['content']) && empty($s2['box1']) && empty($s2['box2'])): ?>
                                        <p><?php echo esc_html($s2['content']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($tab['section3'])):
                                $s3 = $tab['section3']; ?>
                                <div class="custom-tab-section custom-tab-section-3">
                                    <?php if (!empty($s3['box1'])): ?>
                                        <p><?php echo esc_html($s3['box1']); ?></p>
                                    <?php endif; ?>
                                    <?php if (!empty($s3['content']) && empty($s3['box1'])): ?>
                                        <p><?php echo esc_html($s3['content']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <?php if (!empty($logos_data) && is_array($logos_data)): ?>
        <?php if (!empty($logos_title)): ?>
            <div class="custom-tab-global-title"><?php echo nl2br(esc_html($logos_title)); ?></div>
        <?php endif; ?>
        <div class="custom-tabs-global-logos">
            <?php foreach ($logos_data as $logo): ?>
                <?php if (!empty($logo['url'])): ?>
                    <img src="<?php echo esc_url($logo['url']); ?>" class="custom-tab-global-logo" alt="Global Logo">
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php

    return ob_get_clean();
}
add_shortcode('custom_tabs', 'custom_tabs_shortcode');
