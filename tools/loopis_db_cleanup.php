<?php

 // Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Only run if you are logged in as admin

require_once('../../../../wp-load.php'); // Adjust path as needed

// Include the file that contains the function to delete pages
require_once __DIR__ . '/../db/loopis_pages_create_delete.php';
require_once __DIR__ . '/../db/loopis_tags_insert_delete.php';

echo "Starting up dev-cleanup to remove/empty all tables. now!<br><br>";
error_log('Starting up dev-cleanup ');

if (!current_user_can('administrator')) {
    exit('Not allowed');
}

function loopis_db_cleanup() {
    global $wpdb;
    $lockers_table = $wpdb->prefix . 'loopis_lockers';
    $settings_table = $wpdb->prefix . 'loopis_settings';
    
    //Select which of the two options you want to use: 1. Empty tables or 2. Drop tables

    // Option 1: Empty tables
    //echo ">>> Now just deleting content in the tables!<br>";
    //$wpdb->query("DELETE FROM $lockers_table");
    //$wpdb->query("DELETE FROM $settings_table");
    
    // Option 2: Drop tables
    echo ">>> Now dropping the all Loopis tables!<br>";
    $wpdb->query("DROP TABLE IF EXISTS $lockers_table");
    $wpdb->query("DROP TABLE IF EXISTS $settings_table");

    // Remove the inserted values into wordpress tables as well
    echo ">>> Remove the inserted values into wordpress tables as well!<br>";
    loopis_pages_delete();
    loopis_tags_delete(); 
    
    echo ">>> Loopis DB Clean: Deleted all Loopis content in the tables that was created with activation of Loopis Config!<br>";
    error_log(">>> Loopis DB Clean: Deleted all Loopis content in the tables that was created with activation of Loopis Config!");

}


loopis_db_cleanup();
echo "<br>Cleanup done!<br>";