<?php
/**
 * WP Admin page for configuring the WordPress installation.
 * 
 * @package LOOPIS_Config
 * @subpackage Admin-page
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

// Clear cache (if needed)
require_once LOOPIS_CONFIG_DIR . 'cache/loopis_cache_buster.php';
loopis_cache_buster();

// Include functions
require_once LOOPIS_CONFIG_DIR . 'functions/loopis_config.php';
require_once LOOPIS_CONFIG_DIR . 'functions/loopis_db_setup.php';

// Enqueue scripts
function loopis_config_enqueue_scripts($hook) {
    // Enqueue JS file
    wp_enqueue_script(
        'loopis_config_buttons_js',
        LOOPIS_CONFIG_URL . 'assets/js/loopis_config_buttons.js',
        ['jquery'],
        '1.0',
        true 
    );
    // Ajax localisation
    wp_localize_script('loopis_config_buttons_js', 'loopis_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('loopis_config_nonce')
    ]);
}

// Config js hook
add_action('admin_enqueue_scripts', 'loopis_config_enqueue_scripts');

// Ajax handler hooks
add_action('wp_ajax_loopis_sp_handle_actions', 'loopis_sp_handle_actions');
add_action('wp_ajax_loopis_log_message', 'loopis_log_message');
add_action('wp_ajax_loopis_sp_clear_step_status', 'loopis_sp_clear_step_status');
add_action('wp_ajax_loopis_refresh_roles_display', 'loopis_refresh_roles_display_ajax');

// Function to render the page
function loopis_config_page() {
    ?>
    <div class="wrap">
        <!-- Page title and description-->
        <h1>⚙ LOOPIS Config</h1>
        <p class="description">💡 This is where you configure (and update) a WordPress installation for LOOPIS.</p>

        <!-- Page content-->
        <h2>LOOPIS version</h2>
        <p>Your installed version of "LOOPIS Config": <strong><?php echo LOOPIS_CONFIG_VERSION; ?></strong></p>

        <h2>Configuration of WordPress</h2>
        <p><button id="run_loopis_config_installation" class="button button-primary" value="Start">Start!</button></p> 
        <p><i>[Change button to Update! when done + grey out if all units are OK.].</i></p>
        
        <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="manage-column">Unit</th>
                <th scope="col" class="manage-column">Location</th>
                <th scope="col" class="manage-column">Status</th>
                <th scope="col" class="manage-column">Version</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="column-unit">LOOPIS config</td>
                <td class="column-place">wp_loopis_config</td>
                <td class="column-status" data-step="loopis_config_table"><span class="status"><?php echo loopis_sp_get_step_status('loopis_config_table'); ?></span></td>
                <td class="column-version"></td>
            </tr>
            <tr>
                <td class="column-unit">LOOPIS settings</td>
                <td class="column-place">wp_loopis_settings</td>
                <td class="column-status" data-step="loopis_settings_table"><span class="status"><?php echo loopis_sp_get_step_status('loopis_settings_table'); ?></span></td>
                <td class="column-version"></td>
            </tr>
            <tr>
                <td class="column-unit">LOOPIS lockers</td>
                <td class="column-place">wp_loopis_lockers</td>
                <td class="column-status" data-step="loopis_lockers_table"><span class="status"><?php echo loopis_sp_get_step_status('loopis_lockers_table'); ?></span></td>
                <td class="column-version"></td>
            </tr>
            <tr>
                <td class="column-unit">LOOPIS pages</td>
                <td class="column-place">wp_posts</td>
                <td class="column-status" data-step="loopis_pages"><span class="status"><?php echo loopis_sp_get_step_status('loopis_pages'); ?></span></td>
                <td class="column-version"></td>
            </tr>
            <tr>
                <td class="column-unit">LOOPIS categories</td>
                <td class="column-place">wp_terms</td>
                <td class="column-status" data-step="loopis_cats"><span class="status"><?php echo loopis_sp_get_step_status('loopis_cats'); ?></span></td>
                <td class="column-version"></td>
            </tr>
            <tr>
                <td class="column-unit">LOOPIS tags</td>
                <td class="column-place">wp_terms</td>
                <td class="column-status" data-step="loopis_tags"><span class="status"><?php echo loopis_sp_get_step_status('loopis_tags'); ?></span></td>
                <td class="column-version"></td>
            </tr>
            <tr>
                <td class="column-unit">LOOPIS roles</td>
                <td class="column-place">wp_options</td>
                <td class="column-status" data-step="loopis_roles"><span class="status"><?php echo loopis_sp_get_step_status('loopis_roles'); ?></span></td>
                <td class="column-version"></td>
            </tr>
            <tr>
                <td class="column-unit">LOOPIS admins</td>
                <td class="column-place">wp_users</td>
                <td class="column-status" data-step="loopis_admins"><span class="status"><?php echo loopis_sp_get_step_status('loopis_admins'); ?></span></td>
                <td class="column-version"></td>
            </tr>
            <tr>
                <td class="column-unit">Delete default WP plugins</td>
                <td class="column-place">Plugins</td>
                <td class="column-status" data-step="wp_plugins"><span class="status"><?php echo loopis_sp_get_step_status('wp_plugins'); ?></span></td>
                <td class="column-version"></td>
            </tr>
            <tr>
                <td class="column-unit">Install LOOPIS plugins</td>
                <td class="column-place">Plugins</td>
                <td class="column-status" data-step="loopis_plugins"><span class="status"><?php echo loopis_sp_get_step_status('loopis_plugins'); ?></span></td>
                <td class="column-version"></td>
            </tr>
            <tr>
                <td class="column-unit">WordPress root files</td>
                <td class="column-place">Server</td>
                <td class="column-status" data-step="install_root_files"><span class="status"><?php echo loopis_sp_get_step_status('install_root_files'); ?></span></td>
                <td class="column-version"></td>
            </tr>
            <tr>
                <td class="column-unit">WordPress options</td>
                <td class="column-place">wp_options</td>
                <td class="column-status" data-step="wp_options"><span class="status"><?php echo loopis_sp_get_step_status('wp_options'); ?></span></td>
                <td class="column-version"></td>
            </tr>
            <tr>
                <td class="column-unit">WordPress admin screen options</td>
                <td class="column-place">wp_usermeta</td>
                <td class="column-status" data-step="wp_screen_options"><span class="status"><?php echo loopis_sp_get_step_status('wp_screen_options'); ?></span></td>
                <td class="column-version"></td>
            </tr>
            
        </tbody>
</table>
    </div>
    <?php 
}