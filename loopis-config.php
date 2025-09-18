<?php
/*
Plugin Name: LOOPIS Config
Plugin URI: https://github.com/LOOPIS-app/loopis-config
Description: Plugin for configuring a clean WP installation for LOOPIS.app
Version: 0.1
Author: develoopers
Author URI: https://loopis.org
*/

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

// Define plugin version
define('LOOPIS_CONFIG_VERSION', '0.1');

// Define plugin folder path constants
define('LOOPIS_CONFIG_DIR', plugin_dir_path(__FILE__)); // Server-side path to /wp-content/plugins/loopis-config/
define('LOOPIS_CONFIG_URL', plugin_dir_url(__FILE__)); // Client-side path to https://site.com/wp-content/plugins/loopis-config/

// Start of error log
error_log("===== Start: LOOPIS Config =====");
error_log("Plugin version: " . LOOPIS_CONFIG_VERSION);

// Include neccessary files
require_once LOOPIS_CONFIG_DIR . 'db-setup/loopis_db_setup.php';
require_once LOOPIS_CONFIG_DIR . 'db-cleanup/loopis_admintool_cleanup.php'; //Neccesary only while the cleanup button exists
require_once LOOPIS_CONFIG_DIR . 'admin/loopis_admin_menu.php';

// Call the main setup function upon plugin activation
//register_activation_hook(__FILE__, 'loopis_db_setup');

// Admin menu hook
add_action('admin_menu', 'loopis_config_menu');

// Setup admin menu
function loopis_config_menu() {
    //Render top level menu item
    add_menu_page(
        'Loopis Setup',           // Page Title
        'Loopis Setup',           // Menu Title
        'manage_options',         // Capability
        'loopis_config_setup',        // Menu Slug
        'loopis_config_setup_page',   // Function to display the page(change if submenus included)
        LOOPIS_CONFIG_DIR . 'assets/images/loopis-icon-20x20.png'   // Loopis Icon 
    );
}

//Admin style hook
add_action('admin_enqueue_scripts', 'loopis_config_admin_styles');

//Enqueue admin menu style sheet 
function loopis_config_admin_styles() {
    wp_enqueue_style(
        'loopis-config-admin-style', //Name
        LOOPIS_CONFIG_URL . 'admin/loopis_admin_menu_style.css', //URL
        [], //Dependencies
        '1.0' //Version
    );
}

// End of error log
error_log("===== End: LOOPIS Config =====");