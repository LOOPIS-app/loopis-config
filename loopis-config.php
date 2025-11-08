<?php
/*
Plugin Name: LOOPIS Config
Plugin URI: https://github.com/LOOPIS-app/loopis-config
Description: Plugin for configuring a clean WP installation for LOOPIS.app
Version: 0.7
Version: 0.7
Author: LOOPIS Develoopers
Author URI: https://loopis.org
*/

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

// Define plugin version
define('LOOPIS_CONFIG_VERSION', '0.7.0');

// Define plugin folder path constants
define('LOOPIS_CONFIG_DIR', plugin_dir_path(__FILE__));     // Server-side path to /wp-content/plugins/loopis-config/
define('LOOPIS_CONFIG_URL', plugin_dir_url(__FILE__));      // Client-side path to https://site.com/wp-content/plugins/loopis-config/

// Define folders to include
function loopis_config_load_files() {
    // Admin?
    if (!current_user_can('administrator')) { return; } // Exit early

    loopis_config_include_folder('logging');

    // Admin area?
    if (is_admin()) {
        loopis_config_include_folder('interface');
        loopis_config_include_folder('pages');
    }
}

// Function to include all PHP files in a folder
function loopis_config_include_folder($folder_name) {
    $absolute_path = LOOPIS_CONFIG_DIR . '/' . $folder_name;
    if (is_dir($absolute_path)) {
        foreach (glob($absolute_path . '/*.php') as $file) {
            include_once $file;
        }
    } else {
        error_log("loopis-config: Failed to include folder from loopis-config.php: {$folder_name}");
    }
}

// Enqueue style sheet
function loopis_config_enqueue_styles() {
    wp_enqueue_style(
        'loopis-admin-style', // Name
        LOOPIS_CONFIG_URL . 'assets/css/loopis_config_style.css', // URL
        [],     // Dependencies
        '1.0'   // Version
    );
}

// Plugin activation log
function loopis_log_on_activation() {
    error_log(" ");
    error_log("===== ACTIVATED! LOOPIS Config =====");
    error_log("Plugin version: " . LOOPIS_CONFIG_VERSION);
    error_log("===== END ACTIVATION LOG =====");
    error_log(" ");
}

// Admin session periodic logger
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

function get_loopis_config_data() {
    $cache_key = 'loopis_config_data';

    // Try to get cached version first
    $config = wp_cache_get($cache_key, 'loopis');
    if ($config !== false) {
        return $config;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'loopis_config';
    $config = $wpdb->get_results("SELECT * FROM $table", ARRAY_A);

    // Cache it indefinitely (until explicitly cleared)
    wp_cache_set($cache_key, $config, 'loopis');

    return $config;
}

// Admin menu hook
add_action('admin_menu', 'loopis_config_admin_menu');

// Admin style hook
add_action('admin_enqueue_scripts', 'loopis_config_enqueue_styles');

// Log on plugin activation
register_activation_hook(__FILE__, 'loopis_log_on_activation');

// Log admin load
add_action('admin_init', 'loopis_log_admin_load');

// Hook to load files when plugins are loaded
add_action('plugins_loaded','loopis_config_load_files');

// Loopis Config table is created on plugin activation
register_activation_hook(__FILE__, function(){
    include_once LOOPIS_CONFIG_DIR . 'functions/db-setup/loopis_config_table_insert.php';
    loopis_config_table_insert();
});

// Cache table data
add_action('admin_init', function() {
    if (!current_user_can('administrator')) { return;} 
    $config = get_loopis_config_data();
});
