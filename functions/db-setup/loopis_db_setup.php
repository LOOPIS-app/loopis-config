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
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_lockers_create.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_settings_create.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_settings_insert.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_pages_insert.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_categories_insert.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_tags_insert.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_user_roles.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_users_insert.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_wp_options_change.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_plugins_delete.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_plugins_install.php';

// Define the main function
function loopis_db_setup() {
    error_log('>>> Database Setup Starting!');

    // Create (or update) LOOPIS custom table 'loopis_lockers'
    loopis_lockers_create();

    // Create (or update) LOOPIS custom table 'loopis_settings'
    loopis_settings_create();

    // Insert LOOPIS default values into 'loopis_settings'
    loopis_settings_insert();

    // Insert LOOPIS default pages into 'wp_posts'
    loopis_pages_insert();

     // Insert LOOPIS default categories into 'wp_terms'
    loopis_categories_insert();

    // Insert LOOPIS default tags into 'wp_terms'
    loopis_tags_insert();

    // Insert LOOPIS default tags into 'wp_users'
    loopis_users_insert();

    // Change WordPress settings in 'wp_options'
    loopis_wp_options_change();

    // Delete default plugins
    loopis_plugins_delete();

    // Install necessary plugins
    loopis_plugins_install();


    error_log('>>> Database Setup Complete!');
}