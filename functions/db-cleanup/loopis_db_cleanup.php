<?php
/** 
 * Tool for cleaning up tables and data created by plugin 'LOOPIS Config'.
 *
 * This function is accessed via URL.
 * 
 * WARNING! The cleanup tool is intended for development purposes only.
 * Use with caution and only in a safe development environment!
 * 
 * @package LOOPIS_Config
 * @subpackage Dev-tools
 */

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Load WordPress environment
require_once('../../../../../wp-load.php'); // Adjust path as needed

// Include the necessary files
require_once __DIR__ . '/loopis_pages_delete.php';
require_once __DIR__ . '/loopis_tags_delete.php';

// Only run if you are logged in as admin
if (!current_user_can('administrator')) {
    exit('Not allowed.');
}

// Announce start
echo "Database cleanup starting!<br><br>";

// Define the function
function loopis_db_cleanup() {
    error_log('=== Start: loopis_db_cleanup ===');

    global $wpdb;
    $lockers_table = $wpdb->prefix . 'loopis_lockers';
    $settings_table = $wpdb->prefix . 'loopis_settings';
    
    //Select what to do with LOOPIS custom tables: 1. Empty or 2. Drop

    // Option 1: Empty LOOPIS custom tables
    //echo ">>> Deleting content in the LOOPIS custom tables.<br>";
    //$wpdb->query("DELETE FROM $lockers_table");
    //$wpdb->query("DELETE FROM $settings_table");
    
    // Option 2: Drop LOOPIS custom tables
    echo "• Dropping the LOOPIS custom tables...<br>";
    $wpdb->query("DROP TABLE IF EXISTS $lockers_table");
    $wpdb->query("DROP TABLE IF EXISTS $settings_table");

    // Remove values inserted into WordPress tables
    echo "• Deleting values inserted into WordPress tables...<br>";
    loopis_pages_delete();
    loopis_tags_delete(); 

    error_log("=== End: loopis_db_cleanup ===");
}

// Call the function
loopis_db_cleanup();

// Announce completion
echo "<br>Database cleanup done!<br>";