<?php
/** 
 * Tool for cleaning up tables and data created by plugin 'LOOPIS Config' via the admin menu. 
 * 
 * Mimic of loopis_db_cleanup, to be deleted when obsolete.
 *
 * WARNING! This tool is intended for development purposes only.
 * Use with caution and only in a safe development environment!
 */


// Include the necessary files
require_once __DIR__ . '/../db-cleanup/loopis_pages_delete.php';
require_once __DIR__ . '/../db-cleanup/loopis_categories_delete.php';
require_once __DIR__ . '/../db-cleanup/loopis_tags_delete.php';
require_once __DIR__ . '/../db-cleanup/loopis_user_roles_delete.php';
require_once __DIR__ . '/../db-cleanup/loopis_users_delete.php';
require_once __DIR__ . '/../db-cleanup/loopis_plugins_cleanup.php';

// Define the function
function loopis_admin_cleanup() {

    // Access WordPress database object
    global $wpdb;

    // Define custom table names
    $lockers_table = $wpdb->prefix . 'loopis_lockers';
    $settings_table = $wpdb->prefix . 'loopis_settings';

    // Drop LOOPIS custom tables
    $wpdb->query("DROP TABLE IF EXISTS $lockers_table");
    $wpdb->query("DROP TABLE IF EXISTS $settings_table");
}
