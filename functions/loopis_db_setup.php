<?php
/**
 * Main function to configure the WordPress database for LOOPIS.
 *
 * This function is called from 'loopis_config.php'.
 *
 * @package LOOPIS_Config
 * @subpackage Configuration
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
} 

// Function to include all PHP files in folder "db-setup"
function loopis_include_db_setup_files() {
    $db_setup_dir = LOOPIS_CONFIG_DIR . 'functions/db-setup/';

    $php_files = glob($db_setup_dir . '*.php');
    
    foreach ($php_files as $file) {
        require_once $file;
        error_log("loopis_db_setup: Included " . basename($file));
    }
    
    return true;
}

// Include db-setup functions files
loopis_include_db_setup_files();

// Define the main function
function loopis_db_setup() {
    error_log('>>> LOOPIS db setup starting!');

    // Create (or update) table 'loopis_lockers'
    loopis_lockers_create();

    // Create (or update) table 'loopis_settings'
    loopis_settings_create();

    // Insert values to 'loopis_settings'
    loopis_settings_insert();

    // Insert pages to 'wp_posts' (includes deleting default WP pages and posts)
    loopis_pages_insert();

    // Insert categories to 'wp_terms'
    loopis_cats_insert();

    // Insert tags to 'wp_terms'
    loopis_tags_insert();

    // Set user roles in 'wp_options' (run before 'loopis_admins_insert')
    loopis_roles_set();

    // Insert admin users in 'wp_users'
    loopis_admins_insert();

    // Delete WP default plugins
    loopis_wp_plugins_delete();

    // Install necessary plugins
    loopis_plugins_install();

    // Set WP options in 'wp_options' (run after 'loopis_admins_insert' and 'loopis_pages_insert')
    loopis_wp_options_set();

    // Set WP admin screen options in 'wp_usermeta'
    loopis_wp_screen_options_set();

    error_log('>>> LOOPIS db setup complete!');
}