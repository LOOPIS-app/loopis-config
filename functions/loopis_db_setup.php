<?php
/**
 * Main function to configure the WordPress database for LOOPIS.
 *
 * This function is called in 'loopis-config_page.php'
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
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_cats_insert.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_tags_insert.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_user_roles_set.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_users_insert.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_plugins_delete.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_plugins_install.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_wp_options_set.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_wp_screen_options_set.php';

// Define the main function
function loopis_db_setup() {
    error_log('>>> LOOPIS db setup starting!');

    // Create (or update) LOOPIS custom table 'loopis_lockers'
    loopis_lockers_create();

    // Create (or update) LOOPIS custom table 'loopis_settings'
    loopis_settings_create();

    // Insert LOOPIS default values into custom table 'loopis_settings'
    loopis_settings_insert();

    // Insert LOOPIS default pages into 'wp_posts' (+ delete default WP pages and posts)
    loopis_pages_insert();

     // Insert LOOPIS default categories into 'wp_terms'
    loopis_cats_insert();

    // Insert LOOPIS default tags into 'wp_terms'
    loopis_tags_insert();

    // Set LOOPIS default user roles in 'wp_options' (before 'loopis_users_insert')
    loopis_user_roles_set();

    // Insert LOOPIS default tags into 'wp_terms'
    loopis_users_insert();

    // Delete default plugins
    loopis_plugins_delete();

    // Install necessary plugins
    loopis_plugins_install();

    // Set WordPress settings in 'wp_options' (after 'loopis_users_insert' and 'loopis_pages_insert')
    loopis_wp_options_set();

    // Set WordPress admin screen options in 'wp_usermeta'
    loopis_wp_screen_options_set();

    error_log('>>> LOOPIS db setup complete!');
}