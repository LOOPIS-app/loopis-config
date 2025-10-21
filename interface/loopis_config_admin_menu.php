<?php
/**
 * Add admin menu items for the plugin.
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function loopis_config_admin_menu() {
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
        'Components',                 // Page title
        'Components',                 // Menu title
        'manage_options',             // Capability
        'loopis_components',          // Menu slug
        'loopis_components_page'      // Function
    );

    // Hide the main menu page (but keep the icon and submenus)
    remove_submenu_page('loopis_config_main', 'loopis_config_main');
}