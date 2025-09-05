<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Only run if you are logged in as admin

require_once('../../../../wp-load.php'); // Adjust path as needed
echo "Starting up dev-cleanup to remove/empty all tables!<br><br>";

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
    echo ">>> Now dropping the whole tables!<br>";
    $wpdb->query("DROP TABLE IF EXISTS $lockers_table");
    $wpdb->query("DROP TABLE IF EXISTS $settings_table");
}

loopis_db_cleanup();
echo "<br>Cleanup done!<br>";