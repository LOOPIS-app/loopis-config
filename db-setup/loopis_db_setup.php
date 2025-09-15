<?php
/**
 * Main function to configure the WordPress database for LOOPIS.
 *
 * This function is called in 'loopis-config.php' upon plugin activation.
 *
 * @package LOOPIS_Config
 * @subpackage Database
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
} 

// Include necessary files
require_once LOOPIS_CONFIG_DIR . 'db-setup/loopis_lockers_create_update.php';
require_once LOOPIS_CONFIG_DIR . 'db-setup/loopis_settings_create_update.php';
require_once LOOPIS_CONFIG_DIR . 'db-setup/loopis_settings_insert.php';
require_once LOOPIS_CONFIG_DIR . 'db-setup/loopis_pages_insert.php';
require_once LOOPIS_CONFIG_DIR . 'db-setup/loopis_tags_insert.php';
require_once LOOPIS_CONFIG_DIR . 'db-setup/loopis_wp_options_change.php';

// Define the main function
function loopis_db_setup() {
    error_log('>>> Start of main function loopis_db_setup');

    // Create (or update) LOOPIS custom table 'loopis_lockers'
    loopis_lockers_create_update();

    // Create (or update) LOOPIS custom table 'loopis_settings'
    loopis_settings_create_update();

    // Insert LOOPIS default values into 'loopis_settings'
    loopis_settings_insert();

    // Insert LOOPIS default pages into 'wp_posts'
    loopis_pages_insert();

    // Insert LOOPIS default tags into 'wp_terms'
    loopis_tags_insert();

    // Change WordPress settings in 'wp_options'
    loopis_wp_options_change();

    error_log('>>> End of main function loopis_db_setup');
}