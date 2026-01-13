<?php
/**
 * Functions for running "loopis_db_setup()" on WP Admin page "loopis-config-page.php".
 * 
 * This file is included from the WP Admin page.
 * 
 * @package LOOPIS_Config
 * @subpackage Configuration
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

/**
 * ======================== Button Handlers ========================
 */


/**
 * AJAX handler for backend step processing via stepFunction.
 *
 * Process:
 *  - Retrieves the function handle and associated LOOPIS step ID from the request.
 *  - Executes the specified function if it exists and is callable.
 *  - Updates the LOOPIS step status accordingly.
 *  - Returns a JSON response for dynamic UI updates.
 *
 * @return void Sends JSON response for UI.
 */

function loopis_sp_handle_actions() {
    // Check nonce
    check_ajax_referer('loopis_config_nonce', 'nonce');

    // Get function and id
    $function = isset($_POST['func_step']) ? sanitize_text_field($_POST['func_step']) : '';
    $id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : '';
    $data = $_POST['data'] ?? '';
    $ver_str = sanitize_text_field($data['ver_str'] ?? '' );
    $slug = sanitize_text_field( $_POST['slug'] ?? '' );
    $main = sanitize_text_field( $_POST['main'] ?? '' );
    $version = LOOPIS_CONFIG_VERSION;
    // if function call does not exist
    if (!$function){
        // No function
        loopis_config_update(
            ['ID' => $id], 
            ['Config_Status' => 'N/a',
            'Config_Version' => $version]); // Set status to not applicable
        wp_send_json_success([                 // Send back JSON with success and id, and statustext
            'id' => $id,
            'status' =>  '⬜ Function missing.',
            'version' => $version,
        ]);
    }else{
        try {
            // Attempts to run function
            ob_start();                             // Output buffer
            $function();                            // Function call
            ob_get_clean();
            if (!empty($ver_str) && defined($ver_str)) {
                $version = constant($ver_str);
            }
            loopis_config_update(
                ['ID' => $id], 
                ['Config_Status' => 'Ok',
                'Config_Version' => $version]);  // Set status to Ok
            wp_send_json_success([                  // Send back JSON with success and id, and statustext
                'id' => $id,
                'status' => '✅ OK!',
                'version' => $version,
            ]);
        } catch (Throwable $e) {
            // Function failed
            error_log("End: Error in function call {$function}:  {$e->getMessage()}");
            error_log('Terminating process.');
            loopis_config_update(
                ['ID' => $id], 
                ['Config_Status' => 'Error',
                'Config_Version' => '-']); // Set status to Error
            wp_send_json_error([                     // Send back JSON with error and id, and statustext
                'id' => $id,    
                'status' =>  '⚠️ Failed! Could not run.',
                'version' => '-',
            ]);
        }
    }

    wp_die();
}

/**
 * Post function helper for activate plugins
 *
 * @return void
 */
function loopis_sp_activate_plugins_handler() {
    // Activate your plugins
    ob_start();                             
    loopis_plugins_activate();
    ob_get_clean();

    loopis_pages_rename();
    // Completes error log
    error_log('');
    error_log('=========================== End: Loopis Installer! ===========================');
    error_log('');
    // Redirect after activation
    wp_redirect(admin_url('admin.php?page=loopis_config&activated=1'));
    exit;
}


/**
 * AJAX handler for updating version, runs loopis_config_run_updates if possible.
 * 
 *  @return void Sends JSON response for UI.
 */
function loopis_sp_update_handler() {
    // Check nonce
    check_ajax_referer('loopis_config_nonce', 'nonce');

    // Get saved option
    $current_version = get_option('loopis_config_version');

    // If up to date then no update
    if ($current_version == LOOPIS_CONFIG_VERSION) {
        wp_send_json_success(['message' => 'Loopis installation is up to date!']);
    }else{
        try {     // Try to run updates
            ob_start(); 
            $result = loopis_config_run_updates();
            ob_get_clean();
            wp_send_json_success(['message' => $result]);
        } catch (Exception $e) {
            wp_send_json_error(['message' => 'Update failed: ' . $e->getMessage()]);
        }
    }

    wp_die();
}


/**
 * ======================== logger-aid ========================
 */

/**
 *  Send error log from JS.
 *  @return void
 */
function loopis_log_message() {

    // Check nonce
    check_ajax_referer('loopis_config_nonce', 'nonce');

    // Get text and log it if it exists
    // $error_log = sanitize_text_field($_POST['error_log'] ?? '');
    $error_log = $_POST['error_log'];
    if ($error_log) {
        error_log($error_log);
    }

    wp_die();
}

/**
 * ======================== Status Functions ========================
 */

/**
 * Status text getter.
 * 
 * @return string Status text for config page table.
 */
function loopis_sp_get_status_text($status) {
    // For specific status respond with message
    if ($status === 'Ok') {
        return '✅ OK!';
    } elseif ($status === 'N/a') {
        return '⬜ Function missing.';
    } elseif ($status === 'Error') {
        return '⚠️ Failed! Could not run.';
    } else {
        return '⬜';
    }
}


/**
 * ======================== Loopis Config Table ========================
 */
/**
 * Insert a new row into the loopis_config table.
 *
 * @param array $data Array of column => value pairs to insert.
 * @return void
 */
function loopis_config_insert($data) {
    global $wpdb;
    $table = $wpdb->prefix . 'loopis_config';

    $wpdb->insert($table, $data);

    // Reset the cache
    wp_cache_delete('loopis_config_data', 'loopis');
    $config = get_loopis_config_data();
}

/**
* Update rows in the loopis_config table.
*
* @param array $data  Associative array of column => new value pairs.
* @param array $where Associative array of column => value pairs used for the WHERE clause.
*                     Only rows matching these conditions will be updated.
* @return void
*/
function loopis_config_update($data, $where) {
    global $wpdb;
    $table = $wpdb->prefix . 'loopis_config';

    $wpdb->update($table, $where,  $data);

    // Reset the cache
    wp_cache_delete('loopis_config_data', 'loopis');
    $config = get_loopis_config_data();
}

/**
 * Delete rows from the loopis_config table.
 *
 * @param array $where Associative array of column => value pairs used for the WHERE clause.
 *                     Only rows matching these conditions will be deleted.
 * @return void
 */
function loopis_config_delete($where) {
    global $wpdb;
    $table = $wpdb->prefix . 'loopis_config';

    $wpdb->delete($table, $where);

    // Reset the cache
    wp_cache_delete('loopis_config_data', 'loopis');
    $config = get_loopis_config_data();
}
