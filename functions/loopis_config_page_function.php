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

// Step handler
function loopis_sp_handle_actions() {
    // Check nonce
    check_ajax_referer('loopis_config_nonce', 'nonce');

    // Get function and id
    $function = isset($_POST['func_step']) ? sanitize_text_field($_POST['func_step']) : '';
    $id = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : '';

    // if function call does not exist
    if (!$function){

        loopis_sp_set_step_status($id, 'N/a'); // Set status to not applicable
        wp_send_json_success([                 // Send back JSON with success and id, and statustext
            'id' => $id,
            'status' =>  '⬜ Funktion saknas.'
        ]);

    }else{
        try {

            ob_start();                             // Output buffer
            $function();                            // Function call
            ob_get_clean();
            loopis_sp_set_step_status($id, 'Ok');   // Set status to Ok
            wp_send_json_success([                  // Send back JSON with success and id, and statustext
                'id' => $id,
                'status' => '✅ OK!',
            ]);

        } catch (Throwable $e) {

            loopis_sp_set_step_status($id, 'Error'); // Set status to Ok
            wp_send_json_error([                     // Send back JSON with error and id, and statustext
                'id' => $id,    
                'status' =>  '⚠️ Fel! Kunde inte köras.'
            ]);

            error_log("Error in function call {$function}:  {$e->getMessage()}"); //log error
            error_log('Terminating process.');

        }
    }

    wp_die();
}

// Send error log from JS
function loopis_log_message() {

    // Check nonce
    check_ajax_referer('loopis_config_nonce', 'nonce');

    // Get text and log it if it exists
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

// Loads status from option whenever loopis config page html runs
function loopis_sp_get_step_status($step) {
    // For specific status respond with message
    $status = get_option('loopis_step_status_' . $step, 'not_finished');
    if ($status === 'Ok') {
        return '✅ OK!';
    } elseif ($status === 'N/a') {
        return '⬜ Funktion saknas.';
    } elseif ($status === 'Error') {
        return '⚠️ Fel! Kunde inte köras.';
    } else {
        return '⬜';
    }
}

// Set step status
function loopis_sp_set_step_status($step, $status) {
    // Sets option of step to status
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