<?php
/**
 * Function to create (or update) the custom database table 'loopis_lockers'.
 *
 * This function is called by main function 'loopis_db_setup'.
 * 
 * Change the table structure here if needed, then reactivate the plugin.
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
 * Create (or update) table 'loopis_lockers'
 * 
 * @return void
 */
function loopis_lockers_create() {
    error_log('Running function loopis_lockers_create...');

    // Access WordPress database object
    global $wpdb;

    // Define table name with WordPress prefix
    $table = $wpdb->prefix . 'loopis_lockers';
    $charset_collate = $wpdb->get_charset_collate();

    // Include WordPress database upgrade functions
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    
    // Create the table (or update if columns are missing)
    $sql = "CREATE TABLE $table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        locker_id varchar(32) NOT NULL,
        locker_name varchar(128) DEFAULT NULL,
        postal_code varchar(16) NOT NULL,
        code varchar(32) DEFAULT NULL,
        fetch_warning tinyint(1) DEFAULT 0,
        leave_warning tinyint(1) DEFAULT 0,
        PRIMARY KEY (id),
        UNIQUE KEY locker_id (locker_id)
    ) $charset_collate;";

    dbDelta($sql);
}