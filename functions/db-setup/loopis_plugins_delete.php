<?php
/**
 * Function to delete unused default plugins.
 *
 * This function is called by main function 'loopis_db_setup'.
 * 
 * @package LOOPIS_Config
 * @subpackage Database
 */


if (!defined('ABSPATH')) { 
    exit; 
}

// Possibly necessary dependencies as WordPress does not always autoload the following functions
include_once(ABSPATH . 'wp-admin/includes/plugin.php'); //is_plugin_active(), deactivate_plugins()
include_once(ABSPATH . 'wp-admin/includes/file.php'); //delete_plugins()

/**
 * Delete default plugins
 * 
 * @return void
 */
function loopis_plugins_delete() {
    error_log('Running function loopis_plugins_delete...');

    // Plugin main file in /wp-content/plugins
    $undesired_plugins = [
        'akismet/akismet.php',
        'hello.php'
    ];
    // For each item in list deactivate and delete
    foreach ($undesired_plugins as $plugin) {
        // Deactivate if active
        if (is_plugin_active($plugin)) {
            deactivate_plugins($plugin);
        }

        // Delete plugin if exists
        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin; 
        if (file_exists($plugin_path)) {
            delete_plugins([$plugin]);
        }
    }
}