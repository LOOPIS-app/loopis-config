<?php
/**
 * Function to create (or update) the custom database table 'loopis_lockers'.
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
 * Create (or update) table 'loopis_lockers'
 * 
 * @return void
 */
function loopis_lockers_create() {
    loopis_elog_function_start('loopis_lockers_create');

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
        locker_code varchar(32) DEFAULT NULL,
        locker_full tinyint(1) DEFAULT 0,
        PRIMARY KEY (id),
        UNIQUE KEY locker_id (locker_id)
    ) $charset_collate;";

    dbDelta($sql);
    loopis_elog_function_end_success('loopis_lockers_create');
}

function loopis_lockers_reconfigure() {
    global $wpdb;

    $table  = $wpdb->prefix . 'loopis_lockers';
    $table2 = $wpdb->prefix . 'loopis_settings';

    $entries = $wpdb->get_results("SELECT * FROM {$table}");
    if (empty($entries)) return;

    $values_sql = [];
    $params = [];

    foreach ($entries as $index => $list) {
        $settings = [
            "locker_{$index}_id"      => $list->locker_id,
            "locker_{$index}_name"    => $list->locker_name,
            "locker_{$index}_code"    => $list->locker_code,
            "locker_{$index}_privacy" => 'false',
            "locker_{$index}_warning_info" => '⚠ Det är mycket saker i skåpen just nu! <br>🐎 Hämta dina saker så snabbt som möjligt.<br> 🐌 Vänta någon dag med att lämna stora saker.',
            "locker_{$index}_warning_header" => '⚠ Mycket saker i skåpen!',
        ];

        foreach ($settings as $key => $val) {
            $values_sql[] = "(%s, %s)";
            $params[] = $key;
            $params[] = $val;
        }
    }

    $sql = "INSERT INTO {$table2} (setting_key, setting_value) VALUES " . implode(', ', $values_sql);
    $prepared = $wpdb->prepare($sql, ...$params);

    return $wpdb->query($prepared);
}