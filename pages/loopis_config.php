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

// Include page functions
require_once LOOPIS_CONFIG_DIR . 'functions/loopis_config.php';
require_once LOOPIS_CONFIG_DIR . 'functions/loopis_db_setup.php';

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
        <h1>⚙ LOOPIS configuration</h1>
        <p class="description">💡 This is where you configure (and update) a WordPress installation for LOOPIS.</p>

        <!-- Page content-->
        <h2>Configure WordPress</h2>
        <p>  
            <button id="run_loopis_config_installation" class="button button-primary" value="Start">Start!</button>
        </p> 
        
        <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column">Component</th>
                        <th scope="col" class="manage-column">Location</th>
                        <th scope="col" class="manage-column">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="column-component">LOOPIS settings</td>
                        <td class="column-place">wp_loopis_settings</td>
                        <td class="column-status" data-step="loopis_settings"><span class="status"><?php echo loopis_sp_get_step_status('loopis_settings'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS lockers</td>
                        <td class="column-place">wp_loopis_lockers</td>
                        <td class="column-status" data-step="loopis_lockers"><span class="status"><?php echo loopis_sp_get_step_status('loopis_lockers'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS pages</td>
                        <td class="column-place">wp_posts</td>
                        <td class="column-status" data-step="loopis_pages"><span class="status"><?php echo loopis_sp_get_step_status('loopis_pages'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS categories</td>
                        <td class="column-place">wp_terms</td>
                        <td class="column-status" data-step="loopis_cats"><span class="status"><?php echo loopis_sp_get_step_status('loopis_cats'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS tags</td>
                        <td class="column-place">wp_terms</td>
                        <td class="column-status" data-step="loopis_tags"><span class="status"><?php echo loopis_sp_get_step_status('loopis_tags'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS user roles</td>
                        <td class="column-place">wp_options</td>
                        <td class="column-status" data-step="loopis_user_roles"><span class="status"><?php echo loopis_sp_get_step_status('loopis_user_roles'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS users</td>
                        <td class="column-place">wp_users</td>
                        <td class="column-status" data-step="loopis_users"><span class="status"><?php echo loopis_sp_get_step_status('loopis_users'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">Delete plugins</td>
                        <td class="column-place">Plugins</td>
                        <td class="column-status" data-step="remove_plugins"><span class="status"><?php echo loopis_sp_get_step_status('remove_plugins'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">Install plugins</td>
                        <td class="column-place">Plugins</td>
                        <td class="column-status" data-step="install_plugins"><span class="status"><?php echo loopis_sp_get_step_status('install_plugins'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">WordPress root files</td>
                        <td class="column-place">Server</td>
                        <td class="column-status" data-step="install_root_files"><span class="status"><?php echo loopis_sp_get_step_status('install_root_files'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">WordPress options</td>
                        <td class="column-place">wp_options</td>
                        <td class="column-status" data-step="wp_options"><span class="status"><?php echo loopis_sp_get_step_status('wp_options'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">WordPress admin screen options</td>
                        <td class="column-place">wp_usermeta</td>
                        <td class="column-status" data-step="wp_screen_options"><span class="status"><?php echo loopis_sp_get_step_status('wp_screen_options'); ?></span></td>
                    </tr>
                    
                </tbody>
        </table>
<?php }