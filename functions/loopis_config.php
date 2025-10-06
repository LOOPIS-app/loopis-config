<?php
/**
 * Functions for using the LOOPIS configuration page in WP-Admin.
 * 
 * This file is included by main function 'loopis_db_setup'.
 * 
 * @package LOOPIS_Config
 * @subpackage Configuration
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

/**
 * AJAX handler for backend step processing via stepFunction.
 *
 * Process:
 *  - Retrieves the function handle and associated LOOPIS step ID from the request.
 *  - Executes the specified function if it exists and is callable.
 *  - Updates the LOOPIS step status accordingly.
 *  - Returns a JSON response for dynamic UI updates.
 *
 * @return void
 */
function loopis_sp_handle_actions() {
    // Check nonce
    check_ajax_referer('loopis_config_nonce', 'nonce');

    // Get function and id
    $function = isset($_POST['func_step']) ? sanitize_text_field($_POST['func_step']) : '';
    $id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : '';

    // if function call does not exist
    if (!$function){
        // No function
        loopis_sp_set_step_status($id, 'N/a'); // Set status to not applicable
        wp_send_json_success([                 // Send back JSON with success and id, and statustext
            'id' => $id,
            'status' =>  '⬜ Function missing.'
        ]);
    }else{
        try {
            // Attempts to run function
            ob_start();                             // Output buffer
            $function();                            // Function call
            ob_get_clean();
            loopis_sp_set_step_status($id, 'Ok');   // Set status to Ok
            wp_send_json_success([                  // Send back JSON with success and id, and statustext
                'id' => $id,
                'status' => '✅ OK!',
            ]);
        } catch (Throwable $e) {
            // Function failed
            error_log("End: Error in function call {$function}:  {$e->getMessage()}");
            error_log('Terminating process.');
            loopis_sp_set_step_status($id, 'Error'); // Set status to Ok
            wp_send_json_error([                     // Send back JSON with error and id, and statustext
                'id' => $id,    
                'status' =>  '⚠️ Failed! Could not run.'
            ]);
        }
    }

    wp_die();
}

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
 *  Clear all statuses.
 *  @return void
 */ 
function loopis_sp_clear_step_status(){

    // All steps
    $steps = [
        'loopis_settings',
        'loopis_settings',
        'loopis_lockers',
        'loopis_pages',
        'loopis_cats',
        'loopis_tags',
        'loopis_user_roles',
        'loopis_users',
        'remove_plugins',
        'install_plugins',
        'install_root_files',
        'wp_options',
        'wp_screen_options',
        'users',
        'user_roles',
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

/**
 * Loads status from wp_option whenever loopis config page runs.
 * 
 * Returns status-texts to render on html at data-step $step.
 * @return void
 */
function loopis_sp_get_step_status($step) {
    // For specific status respond with message
    $status = get_option('loopis_step_status_' . $step, 'not_finished');
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
 * Sets 'loopis_step_status'es in wp_options.
 * @return void
 */
function loopis_sp_set_step_status($step, $status) {
    // Sets option of step to status
    update_option('loopis_step_status_' . $step, $status);
}

/**
 * AJAX handler for refreshing user roles display.
 * @return void
 */
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