<?php
/**
 * Function to delete loopis-installed plugins.
 *
 * This function is an auxillary which may be called by main function 'loopis_admintool_cleanup'.
 * 
 * @package LOOPIS_Config
 * @subpackage Devtools
 */


if (!defined('ABSPATH')) { 
    exit; 
}

/**
 * Delete all loopis installed plugins from wp-content/plugins/
 * 
 * @return void
 */
function loopis_plugins_cleanup() {
    
    loopis_elog_function_start('loopis_plugins_cleanup');

    // Plugin main file in /wp-content/plugins
    $installed_plugins = [
        'post-smtp/postman-smtp.php',
        'wp-statistics/wp-statistics.php',
        'wp-user-manager/wp-user-manager.php',
        'ewww-image-optimizer/ewww-image-optimizer.php',
    ];

    // For each item in list deactivate and delete
    foreach ($installed_plugins as $plugin) {

        // Deactivate if active
        if (is_plugin_active($plugin)) {
            deactivate_plugins($plugin);
        }

        // Delete plugin if exists
        $plugin_path = WP_PLUGIN_DIR . '/' . $plugin; 
        if (file_exists($plugin_path)) {
            // Delete plugin
            $result = @delete_plugins([$plugin]);
            // Handle Error
            if (is_wp_error($result)) {
                loopis_elog_first_level(" Failed to uninstall $plugin: " . $result->get_error_message());
            } else {
                loopis_elog_first_level(" Successfully uninstalled $plugin");
            }
        }
    }
    loopis_elog_function_end_success('loopis_plugins_cleanup');
}
