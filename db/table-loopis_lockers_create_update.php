<?php
// This file creates or updates the loopis_lockers table in the database
// - If you want to change the table structure, do it here and then reactivate the plugin
// - Do not change the table name or column names, as that may break existing installations 
// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
} 

function loopis_lockers_create_update() {
    error_log('LOOPIS Config table-lockers create');

    global $wpdb;
    $table = $wpdb->prefix . 'loopis_lockers';
    $charset_collate = $wpdb->get_charset_collate();

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