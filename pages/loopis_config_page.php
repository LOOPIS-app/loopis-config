<?php
/**
 * WP Admin page for configuring a new WordPress installation before installing LOOPIS.
 * 
 * Migrate if admin menu diversifies w/ submenus.
 * 
 * WARNING! The cleanup tool is intended for development purposes only.
 * Use with caution and only in a safe development environment!
 * 
 * @package LOOPIS_Config
 * @subpackage Admin-page
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

// Function to render the page
function loopis_config_page() {
    ?>
    <div class="wrap">
        <!-- Page title and description-->
        <h1>⚙ LOOPIS Config</h1>
        <p class="description">💡 Här konfigurerar du en ny WordPress-installation inför installation av LOOPIS.</p>

        <!-- Page content-->
        <h2>Konfigurera WordPress</h2>
        <p>  
            <button id="run_loopis_config_installation" class="button button-primary" value="Start" />Starta</button>
        </p> 
        
        <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column">Komponent</th>
                        <th scope="col" class="manage-column">Plats</th>
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
                        <td class="column-status" data-step="loopis_categories"><span class="status"><?php echo loopis_sp_get_step_status('loopis_categories'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS tags</td>
                        <td class="column-place">wp_terms</td>
                        <td class="column-status" data-step="loopis_tags"><span class="status"><?php echo loopis_sp_get_step_status('loopis_tags'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS user roles</td>
                        <td class="column-place">wp_user_roles</td>
                        <td class="column-status" data-step="loopis_user_roles"><span class="status"><?php echo loopis_sp_get_step_status('loopis_user_roles'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS users</td>
                        <td class="column-place">wp_users</td>
                        <td class="column-status" data-step="loopis_users"><span class="status"><?php echo loopis_sp_get_step_status('loopis_users'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">WordPress options</td>
                        <td class="column-place">wp_options</td>
                        <td class="column-status" data-step="wp_options"><span class="status"><?php echo loopis_sp_get_step_status('wp_options'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">WordPress root files</td>
                        <td class="column-place">Server</td>
                        <td class="column-status" data-step="install_root_files"><span class="status"><?php echo loopis_sp_get_step_status('install_root_files'); ?></span></td>
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
                </tbody>
        </table>
        
        <p>&nbsp;</p><!-- Spacer -->

        <h2>Återställ WordPress</h2>
        <p class="description">⚠ Varning! Endast avsedd för test i utvecklingsmiljö.</p>
        <p>
            <button id="run_loopis_db_cleanup" class="button button-primary" value="Återställ" />Återställ</button>
        </p>
        <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column">Komponent</th>
                        <th scope="col" class="manage-column">Plats</th>
                        <th scope="col" class="manage-column">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="column-component">LOOPIS tables</td>
                        <td class="column-place">wp_loopis_lockers/settings</td>
                        <td class="column-status" data-step="databas"><span class="status"><?php echo loopis_sp_get_step_status('databas'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS pages</td>
                        <td class="column-place">wp_posts</td>
                        <td class="column-status" data-step="pages"><span class="status"><?php echo loopis_sp_get_step_status('pages'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS categories</td>
                        <td class="column-place">wp_terms</td>
                        <td class="column-status" data-step="categories"><span class="status"><?php echo loopis_sp_get_step_status('categories'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS tags</td>
                        <td class="column-place">wp_terms</td>
                        <td class="column-status" data-step="tags"><span class="status"><?php echo loopis_sp_get_step_status('tags'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS users</td>
                        <td class="column-place">wp_users</td>
                        <td class="column-status" data-step="users"><span class="status"><?php echo loopis_sp_get_step_status('users'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS users</td>
                        <td class="column-place">wp_plugins</td>
                        <td class="column-status" data-step="plugins"><span class="status"><?php echo loopis_sp_get_step_status('plugins'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS user roles</td>
                        <td class="column-place">wp_user_roles</td>
                        <td class="column-status" data-step="user_roles"><span class="status"><?php echo loopis_sp_get_step_status('user_roles'); ?></span></td>
                    </tr>
                </tbody>
        </table>

        <p>&nbsp;</p><!-- Spacer -->
        
        <!-- Installation Information Section -->
        <h2>� Installation information</h2>
        <p class="description">Use below buttons to show specific information about the installation right now.</p>
        <p>
            <button id="toggle_debug_roles" class="button" type="button">🔐 View All User Roles & Capabilities</button>
            <button id="refresh_debug_roles" class="button" type="button" style="display: none;">🔄 Refresh Data</button>
        </p>
        
        <div id="debug_roles_container" style="display: none; margin-top: 20px;">
            <?php loopis_display_user_roles_inline(); ?>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('toggle_debug_roles').addEventListener('click', function() {
                var container = document.getElementById('debug_roles_container');
                var refreshBtn = document.getElementById('refresh_debug_roles');
                if (container.style.display === 'none') {
                    container.style.display = 'block';
                    refreshBtn.style.display = 'inline-block';
                    this.textContent = '🔐 Hide User Roles & Capabilities';
                } else {
                    container.style.display = 'none';
                    refreshBtn.style.display = 'none';
                    this.textContent = '🔐 View All User Roles & Capabilities';
                }
            });
            
            document.getElementById('refresh_debug_roles').addEventListener('click', function() {
                // Manual refresh of user roles display data
                if (typeof jQuery !== 'undefined') {
                    jQuery.post(loopis_ajax.ajax_url, {
                        action: 'loopis_refresh_roles_display',
                        nonce: loopis_ajax.nonce
                    }, function (response) {
                        document.getElementById('debug_roles_container').innerHTML = response;
                    });
                }
            });
        });
        </script>

    </div>
    <?php
}

// Ajax handler hooks
add_action('wp_ajax_loopis_sp_handle_actions', 'loopis_sp_handle_actions');
add_action('wp_ajax_loopis_log_message', 'loopis_log_message');
add_action('wp_ajax_loopis_sp_clear_step_status', 'loopis_sp_clear_step_status');
add_action('wp_ajax_loopis_refresh_roles_display', 'loopis_refresh_roles_display_ajax');

// Step handler
function loopis_sp_handle_actions() {
    //Nonce-filter
    check_ajax_referer('loopis_config_nonce', 'nonce');
    //Get function
    $function = isset($_POST['func_step']) ? sanitize_text_field($_POST['func_step']) : '';
    $id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : '';
    
    if (!$function){
        loopis_sp_set_step_status($id, 'Nan');
        wp_send_json_success([
            'id' => $id,
            'status' =>  '⬜ Funktion saknas.'
        ]);
    }else{
        try {
            ob_start();
            $function();
            ob_get_clean();
            loopis_sp_set_step_status($id, 'Ok');
            wp_send_json_success([
                'id' => $id,
                'status' => '✅ OK!',
            ]);
        } catch (Throwable $e) {
            loopis_sp_set_step_status($id, 'Error');
            wp_send_json_error([
                'id' => $id,
                'status' =>  '⚠️ Fel! Kunde inte köras.'
            ]);
            error_log("Error in function call {$function}:  {$e->getMessage()}");
            error_log('Terminating process.');
        }
    }
    wp_die();
}

function loopis_log_message() {
    check_ajax_referer('loopis_config_nonce', 'nonce');

    $error_log = sanitize_text_field($_POST['error_log'] ?? '');
    if ($error_log) {
        error_log($error_log);
    }

    wp_die();
}

// Clear all statuses
function loopis_sp_clear_step_status(){

    // All steps
    $steps = [
        'loopis_settings',
        'loopis_settings',
        'loopis_lockers',
        'loopis_pages',
        'loopis_categories',
        'loopis_tags',
        'loopis_user_roles',
        'loopis_users',
        'wp_options',
        'remove_plugins',
        'install_plugins',
        'install_root_files',
        'users',
        'users_roles',
        'tags',
        'categories',
        'pages',
        'databas',
        'plugins',
    ];

    // Set status to null
    foreach($steps as $id){
        loopis_sp_set_step_status($id, '');
    }
    
    wp_die();
}

// Get step status
function loopis_sp_get_step_status($step) {
    $status = get_option('loopis_step_status_' . $step, 'not_finished');
    if ($status === 'Ok') {
        return '✅ OK!';
    } elseif ($status === 'Nan') {
        return '⬜ Funktion saknas.';
    } elseif ($status === 'Error') {
        return '⚠️ Fel! Kunde inte köras.';
    } else {
        return '⬜';
    }
}

// Set step status
function loopis_sp_set_step_status($step, $status) {
    update_option('loopis_step_status_' . $step, $status);
}

// AJAX handler for refreshing user roles display
function loopis_refresh_roles_display_ajax() {
    // Verify nonce
    check_ajax_referer('loopis_config_nonce', 'nonce');
    
    // Check user permissions
    if (!current_user_can('manage_options')) {
        wp_die('Insufficient permissions');
    }
    
    // Output the refreshed user roles display
    ob_start();
    loopis_display_user_roles_inline();
    $output = ob_get_clean();
    
    echo $output;
    wp_die();
}