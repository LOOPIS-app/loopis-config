<?php
/*
Plugin Name: LOOPIS Config
Plugin URI: https://github.com/LOOPIS-app/loopis-config
Description: Plugin for configuring a clean WP installation for LOOPIS.app
Version: 0.7
Author: LOOPIS Develoopers
Author URI: https://loopis.org
*/

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

// Define plugin version
define('LOOPIS_CONFIG_VERSION', '0.7');

// Define plugin folder path constants
define('LOOPIS_CONFIG_DIR', plugin_dir_path(__FILE__)); // Server-side path to /wp-content/plugins/loopis-config/
define('LOOPIS_CONFIG_URL', plugin_dir_url(__FILE__)); // Client-side path to https://site.com/wp-content/plugins/loopis-config/

// Include custom error logging
require_once LOOPIS_CONFIG_DIR . 'functions/loopis_logger.php';

// Include wp admin pages
require_once LOOPIS_CONFIG_DIR . 'pages/loopis_plugins_page.php';
require_once LOOPIS_CONFIG_DIR . 'pages/loopis_config_page.php';

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
    // Render top level menu item
    add_menu_page(
        'LOOPIS Config',              // Page Title
        'LOOPIS Config',              // Menu Title
        'manage_options',             // Capability
        'loopis_config_main',         // Menu Slug
        'loopis_config_page',         // Function to display the page
        LOOPIS_CONFIG_URL . 'assets/img/loopis-dashboard-icon.png'   // Dashboard icon 
    );
    
    // Add submenus
    add_submenu_page(
        'loopis_config_main',         // Parent slug
        'Configuration',              // Page title
        'Configuration',              // Menu title
        'manage_options',             // Capability
        'loopis_config',              // Menu slug (now loopis_config as you wanted)
        'loopis_config_page'          // Function
    );

    add_submenu_page(
        'loopis_config_main',         // Parent slug
        'Plugins',                    // Page title
        'Plugins',                    // Menu title
        'manage_options',             // Capability
        'loopis_plugins',             // Menu slug
        'loopis_plugins_page'         // Function
    );

    // Hide the main menu page (but keep the icon and submenus)
    remove_submenu_page('loopis_config_main', 'loopis_config_main');
}

// Enqueue admin menu style sheet (currently empty)
function loopis_enqueue_admin_styles() {
    wp_enqueue_style(
        'loopis-config-admin-style', // Name
        LOOPIS_CONFIG_URL . 'assets/css/loopis_admin_menu_style.css', // URL
        [],     // Dependencies
        '1.0'   // Version
    );
}

// Enqueue admin js and AJAX
function loopis_enqueue_admin_scripts($hook) {
    // Optimisation – if you are not on the loopis config page this wont load
    if (strpos($hook, 'loopis_config') === false) {
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

    if (defined('DOING_AJAX') && DOING_AJAX) return;
    if (defined('DOING_CRON') && DOING_CRON) return;

    if (!get_transient('loopis_logger_flag')) {

        error_log(" ");
        error_log("===== ADMIN SESSION ! =====");
        error_log("Plugin version: " . LOOPIS_CONFIG_VERSION);
        error_log("===== ADMIN SESSION ! =====");
        error_log(" ");

        set_transient('loopis_logger_flag', true,  1 * MINUTE_IN_SECONDS);
    }
}
