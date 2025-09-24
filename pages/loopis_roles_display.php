<?php
/**
 * User roles display script to show all WordPress user roles and their capabilities
 * 
 * This file provides functionality to display current user roles and capabilities
 * in a formatted, easy-to-read table format for administrative purposes.
 * 
 * @package LOOPIS_Config
 * @subpackage Pages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display all user roles and their capabilities in a formatted table (inline version)
 * 
 * Shows the current state of all WordPress user roles including their capabilities
 * and highlights LOOPIS-specific capabilities in a separate section.
 */
function loopis_display_user_roles_inline() {
    // This function is called from within the admin page, so permissions are already checked
    $roles = wp_roles()->get_names();
    $role_objects = wp_roles()->role_objects;
    
    echo "<div style='margin: 20px; font-family: Arial, sans-serif;'>";
    echo "<h2>👥 Current WordPress User Roles and Capabilities</h2>";
    echo "<p style='color: #666; margin-bottom: 20px;'>This table shows the current state of all user roles in your WordPress installation, including any LOOPIS-specific modifications.</p>";
    echo "<style>
        .roles-table { border-collapse: collapse; width: 100%; margin-bottom: 20px; }
        .roles-table th, .roles-table td { border: 1px solid #ddd; padding: 12px; text-align: left; }
        .roles-table th { background-color: #f2f2f2; font-weight: bold; }
        .roles-table tr:nth-child(even) { background-color: #f9f9f9; }
        .cap-granted { color: #008000; }
        .cap-denied { color: #cc0000; }
        .role-name { font-weight: bold; font-size: 16px; }
        .cap-list { margin: 0; padding-left: 20px; max-height: 200px; overflow-y: auto; }
        .cap-list li { margin: 2px 0; }
    </style>";
    
    echo "<table class='roles-table'>";
    echo "<thead><tr><th style='width: 15%;'>Role Key</th><th style='width: 20%;'>Display Name</th><th style='width: 65%;'>Capabilities</th></tr></thead>";
    echo "<tbody>";
    
    foreach ($roles as $role_key => $role_name) {
        $role = get_role($role_key);
        if ($role) {
            echo "<tr>";
            echo "<td><span class='role-name'>$role_key</span></td>";
            echo "<td>$role_name</td>";
            echo "<td>";
            
            $capabilities = $role->capabilities;
            ksort($capabilities); // Sort alphabetically
            
            $granted_caps = array_filter($capabilities, function($v) { return $v === true; });
            $denied_caps = array_filter($capabilities, function($v) { return $v === false; });
            
            echo "<strong>Active Capabilities (" . count($granted_caps) . "):</strong><br>";
            echo "<ul class='cap-list'>";
            foreach ($granted_caps as $cap => $granted) {
                echo "<li class='cap-granted'>✅ $cap</li>";
            }
            echo "</ul>";
            
            if (!empty($denied_caps)) {
                echo "<strong>Disabled Capabilities (" . count($denied_caps) . "):</strong><br>";
                echo "<ul class='cap-list'>";
                foreach ($denied_caps as $cap => $granted) {
                    echo "<li class='cap-denied'>❌ $cap</li>";
                }
                echo "</ul>";
            }
            
            echo "</td>";
            echo "</tr>";
        }
    }
    echo "</tbody></table>";
    
    // Show LOOPIS specific capabilities
    echo "<h3>🎯 LOOPIS Specific Capabilities Overview</h3>";
    echo "<p style='color: #666; margin-bottom: 15px;'>This section shows which roles have LOOPIS-specific capabilities enabled.</p>";
    echo "<table class='roles-table'>";
    echo "<thead><tr><th>Role</th><th>loopis_admin</th><th>loopis_support</th><th>loopis_economy</th></tr></thead>";
    echo "<tbody>";
    
    foreach ($roles as $role_key => $role_name) {
        $role = get_role($role_key);
        if ($role) {
            echo "<tr>";
            echo "<td><strong>$role_key</strong></td>";
            
            $loopis_admin = isset($role->capabilities['loopis_admin']) && $role->capabilities['loopis_admin'] ? '✅' : '❌';
            $loopis_support = isset($role->capabilities['loopis_support']) && $role->capabilities['loopis_support'] ? '✅' : '❌';
            $loopis_economy = isset($role->capabilities['loopis_economy']) && $role->capabilities['loopis_economy'] ? '✅' : '❌';
            
            echo "<td style='text-align: center;'>$loopis_admin</td>";
            echo "<td style='text-align: center;'>$loopis_support</td>";
            echo "<td style='text-align: center;'>$loopis_economy</td>";
            echo "</tr>";
        }
    }
    echo "</tbody></table>";
    echo "</div>";
}

/**
 * Display all user roles and their capabilities (version with permission check)
 * 
 * This function includes permission checking and can be called from URL parameters
 * or other contexts where permission verification is needed.
 */
function loopis_display_user_roles() {
    if (!current_user_can('manage_options')) {
        wp_die('Insufficient permissions');
    }
    
    // Call the inline version
    loopis_display_user_roles_inline();
}

/**
 * Handle URL parameter for displaying roles
 * 
 * Allows viewing roles by adding ?show_roles=1 to any WordPress admin page URL
 */
function loopis_handle_show_roles() {
    if (isset($_GET['show_roles']) && $_GET['show_roles'] == '1' && current_user_can('manage_options')) {
        add_action('admin_notices', 'loopis_display_user_roles');
    }
}
add_action('admin_init', 'loopis_handle_show_roles');

/**
 * Add roles display link to admin bar
 * 
 * Adds a quick access link in the WordPress admin bar to view user roles
 */
function loopis_add_roles_display_link($wp_admin_bar) {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    $wp_admin_bar->add_node(array(
        'id' => 'show-roles',
        'title' => '👥 User Roles',
        'href' => admin_url('admin.php?page=loopis-config'),
        'meta' => array('title' => 'Go to LOOPIS Config page to view user roles information')
    ));
}
add_action('admin_bar_menu', 'loopis_add_roles_display_link', 100);