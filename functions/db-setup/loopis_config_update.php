<?php
/**
 * Update framework for database changes version to version.
 * 
 * This file is read from main.
 *
 * @package LOOPIS_Config
 * @subpackage Database
 */


 /**
 * Checks if the install is old, if so will run each successive update
 */
 function loopis_config_run_updates() {
    loopis_elog_function_start('loopis_config_run_updates');
    // Stores the option in a variable
    $stored_version = get_option('loopis_config_version');
    // Example code update with changes from version to version, add new version and function handle with main update.
    $updates = [
        '0.8.0' => 'loopis_config_update_to_0_8_0',
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
 * Updates from 0.7.0 to 0.8.0 PLACEHOLDER EXAMPLE
 */
function loopis_config_update_to_0_8_0() {
    // update logic goes here
    loopis_elog_second_level("Doing stuff!");
    // Do stuff
}

