<?php
/**
 * Update framework for database changes version to version.
 * 
 * This file is read from main.
 *
 * @package LOOPIS_Config
 * @subpackage Database
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

 /**
 * Checks if the install is old, if so will run each successive update
 */
 function loopis_config_run_updates() {
    loopis_elog_function_start('loopis_config_run_updates');
    // Stores the option in a variable
    $stored_version = get_option('loopis_config_version');
    // Example code update with changes from version to version, add new version and function handle with main update.
    $updates = [
        '0.86' => 'loopis_config_update_to_0_86',
        // etc.
    ];
    // loops through and if the current version is less than the update version then it will run corresponding update
    foreach ($updates as $version => $function) {
        if (version_compare($stored_version, $version, '<')) {
            loopis_elog_first_level("Installing version: {$version}");
            $function();
            update_option('loopis_config_version', $version);
            $stored_version = $version;
            loopis_elog_first_level("Version {$version} installed!");
        }
    }
    loopis_elog_function_end_success('loopis_config_run_updates');
    return 'Loopis config updated successfully to version ' . LOOPIS_CONFIG_VERSION . '!';
}


 /**
 * Updates from 0.8.5 to 0.86, first to run patches from files
 */
function loopis_config_update_to_0_86() {
    // Informera
    loopis_elog_second_level("Running patches for 0.86");
    // Read patch
    loopis_config_include_folder(PATCH_FOLDER.'/0_86');
    loopis_elog_second_level(PATCH_FOLDER.'/0_86' );
    // Run functions
    // Update 
    loopis_settings_0_86();
    // Renew status
    $config = get_loopis_config_data();
    $config = array_filter($config, fn($r) => $r['Category'] === 'Install');
    foreach ($config as $row){
        if (empty($row['Config_Data'])){
            loopis_config_update(
                ['ID' => $row['ID']], 
                ['Config_Version' => '0.87']);        
        }
    }
    wp_cache_delete('loopis_config_data', 'loopis');
    $config = get_loopis_config_data();
}

