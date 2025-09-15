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

// Call the main setup function upon plugin activation
register_activation_hook(__FILE__, 'loopis_db_setup');

// End of error log
error_log("===== End: LOOPIS Config =====");