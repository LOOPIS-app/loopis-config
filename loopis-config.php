<?php
/*
Plugin Name: LOOPIS Config
Plugin URI: https://github.com/LOOPIS-app/loopis-config
Description: Plugin for configuring a clean WP installation for LOOPIS.app
Version: 0.4
Author: develoopers
Author URI: https://loopis.org
*/

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

// Define plugin version
define('LOOPIS_CONFIG_VERSION', '0.4');

// Define plugin folder path constants
define('LOOPIS_CONFIG_DIR', plugin_dir_path(__FILE__)); // Server-side path to /wp-content/plugins/loopis-config/
define('LOOPIS_CONFIG_URL', plugin_dir_url(__FILE__)); // Client-side path to https://site.com/wp-content/plugins/loopis-config/


// Include neccessary files
require_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_db_setup.php';
require_once LOOPIS_CONFIG_DIR . 'functions/db-cleanup/loopis_admintool_cleanup.php'; // Will be moved to plugin "LOOPIS Develoopers"
require_once LOOPIS_CONFIG_DIR . 'functions/loopis_config_page_functions.php';
require_once LOOPIS_CONFIG_DIR . 'functions/loopis_logger.php';

require_once LOOPIS_CONFIG_DIR . 'pages/loopis_config_page.php';
require_once LOOPIS_CONFIG_DIR . 'pages/loopis_roles_display.php'; // Will be moved to plugin "LOOPIS Develoopers"

// Admin menu hook
add_action('admin_menu', 'loopis_config_menu');

// Admin js hook
add_action('admin_enqueue_scripts', 'loopis_enqueue_admin_scripts');

// Admin style hook
add_action('admin_enqueue_scripts', 'loopis_enqueue_admin_styles');

// Log on plugin activation
register_activation_hook(__FILE__, 'loopis_log_on_activation');

// Log admin load
add_action('admin_init', 'loopis_log_admin_load');


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

// Enqueue admin menu style sheet(currently dead)
function loopis_enqueue_admin_styles() {
    wp_enqueue_style(
        'loopis-config-admin-style', //Name
        LOOPIS_CONFIG_URL . 'assets/css/loopis_admin_menu_style.css', //URL
        [], //Dependencies
        '1.0' //Version
    );
}

// Enqueue admin js and AJAX
function loopis_enqueue_admin_scripts($hook) {
    // Optimisation if you are not on the loopis config page this wont load
    if ($hook !== 'toplevel_page_loopis_config') {
        return;
    }
    // Enqueue JS file
    wp_enqueue_script(
        'loopis_admin_buttons_js',
        LOOPIS_CONFIG_URL . 'assets/js/loopis_admin_buttons.js',
        ['jquery'],
        '1.0',
        true 
    );
    // Ajax localisation
    wp_localize_script('loopis_admin_buttons_js', 'loopis_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('loopis_config_nonce')
    ]);
}

function loopis_log_on_activation() {
    error_log(" ");
    error_log("===== ACTIVATED! LOOPIS Config =====");
    error_log("Plugin version: " . LOOPIS_CONFIG_VERSION);
    error_log("===== END ACTIVATION LOG =====");
    error_log(" ");
}

function loopis_log_admin_load() {
    if (!get_transient('loopis_logger_flag')) {
        error_log(" ");
        error_log("===== ADMIN SESSION ! =====");
        error_log("Plugin version: " . LOOPIS_CONFIG_VERSION);
        error_log("===== ADMIN SESSION ! =====");
        error_log(" ");
        set_transient('loopis_logger_flag', true,  10 * MINUTE_IN_SECONDS);
    }
}
