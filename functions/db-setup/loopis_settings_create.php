<?php
/**
 * Function to create (or update) the custom database table 'loopis_settings'.
 *
 * This function is called by main function 'loopis_db_setup'.
 * 
 * Change the table structure here if needed, then reactivate the plugin. (?)
 * Do not change the table name or column names, as that may break existing installations.
 *
 * @package LOOPIS_Config
 * @subpackage Database
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
} 

/**
 * Create (or update) table 'loopis_settings'
 * 
 * @return void
 */
function loopis_settings_create() {
    loopis_elog_function_start('loopis_settings_create');

    // Access WordPress database object
    global $wpdb;

    // Define table name with WordPress prefix
    $table = $wpdb->prefix . 'loopis_settings';
    $charset_collate = $wpdb->get_charset_collate();

    // Include WordPress database upgrade functions
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Create the table (or update if columns are missing)
    $sql = "CREATE TABLE $table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        setting_key varchar(64) NOT NULL,
        setting_value longtext NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY setting_key (setting_key)
    ) $charset_collate;";

    dbDelta($sql);
    loopis_elog_function_end_success('loopis_settings_create');
}