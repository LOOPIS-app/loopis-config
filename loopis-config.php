<?php
/*
Plugin Name: LOOPIS Config
Plugin URI: https://github.com/LOOPIS-app/loopis-config
Description: Plugin for configuring a clean WP installation for LOOPIS.app
Version: 0.3
Author: develoopers
Author URI: https://loopis.org
*/

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

// Define plugin version
define('LOOPIS_CONFIG_VERSION', '0.3');

// Define plugin folder path constants
define('LOOPIS_CONFIG_DIR', plugin_dir_path(__FILE__)); // Server-side path to /wp-content/plugins/loopis-config/
define('LOOPIS_CONFIG_URL', plugin_dir_url(__FILE__)); // Client-side path to https://site.com/wp-content/plugins/loopis-config/

// Start of error log
error_log("===== Start: LOOPIS Config =====");
error_log("Plugin version: " . LOOPIS_CONFIG_VERSION);

// Include neccessary files
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_db_setup.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-cleanup/loopis_admintool_cleanup.php'; // Neccesary only while the cleanup button exists
require_once LOOPIS_CONFIG_DIR . 'pages/loopis_config_page.php';

// Admin menu hook
add_action('admin_menu', 'loopis_config_menu');

// Admin js hook
add_action('admin_enqueue_scripts', 'loopis_enqueue_admin_scripts');

// Admin style hook
add_action('admin_enqueue_scripts', 'loopis_enqueue_admin_styles');

// Setup admin menu
function loopis_config_menu() {
    //Render top level menu item
    add_menu_page(
        'LOOPIS Config',              // Page Title
        'LOOPIS Config',              // Menu Title
        'manage_options',             // Capability
        'loopis_config',              // Menu Slug
        'loopis_config_page',         // Function to display the page (change if submenus included)
        LOOPIS_CONFIG_URL . 'assets/img/loopis-dashboard-icon.png'   // LOOPIS Icon 
    );
}

// Enqueue admin menu style sheet 
function loopis_enqueue_admin_styles() {
    wp_enqueue_style(
        'loopis-config-admin-style', //Name
        LOOPIS_CONFIG_URL . 'assets/css/loopis_admin_menu_style.css', //URL
        [], //Dependencies
        '1.0' //Version
    );
}
// Enqueue admin js and AJAX
function loopis_enqueue_admin_scripts() {
    wp_enqueue_script(
        'loopis_admin_buttons_js',
        LOOPIS_CONFIG_URL . 'assets/js/loopis_admin_buttons.js',
        ['jquery'],
        '1.0',
        true 
    );

    wp_localize_script('loopis_admin_buttons_js', 'loopis_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('loopis_config_nonce')
    ]);
}

// End of error log
error_log("===== End: LOOPIS Config =====");