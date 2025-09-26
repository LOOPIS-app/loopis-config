<?php
/**
 * Function to configure LOOPIS default user roles.
 * 
 * This function is called by main function 'loopis_db_setup'.
 * 
 * Handles the creation and management of LOOPIS user roles
 * including copying capabilities from existing roles and adding LOOPIS-specific capabilities.
 * 
 * @package LOOPIS_Config
 * @subpackage Database
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

/**
 * Main function to change user roles for LOOPIS
 * Creates new roles with LOOPIS capabilities and removes old ones
 */
function loopis_user_roles_change() {
    error_log("Starting LOOPIS user roles change...");
    
    // ===== CONFIGURATION - EASY TO MODIFY =====
    
    // 1. Create new roles (copied from existing roles)
    loopis_copy_role('editor', 'board', 'Board', 'board');
    loopis_copy_role('author', 'member', 'Member', 'member'); 
    loopis_copy_role('contributor', 'manager', 'Manager', 'manager');
    loopis_copy_role('subscriber', 'member_pending', 'Member pending', 'member_pending');
    loopis_create_new_role('developer', 'Developer', 'developer'); // New role without copying
    
    // 2. Add LOOPIS capabilities per role
    loopis_add_capabilities('administrator', array('loopis_admin', 'loopis_support', 'loopis_economy'));
    loopis_add_capabilities('board', array('loopis_admin', 'loopis_support', 'loopis_economy'));
    loopis_add_capabilities('manager', array('loopis_admin', 'loopis_support', 'loopis_economy'));
    loopis_add_capabilities('developer', array('loopis_admin'));
    // member and member_pending get no extra capabilities
    
    // 3. Skip this to be able to roolback: Delete old roles (after users have been moved)
    // loopis_delete_role('editor');
    // loopis_delete_role('author');
    // loopis_delete_role('contributor');
    // loopis_delete_role('subscriber');
    
    // ===== END CONFIGURATION =====
    
    error_log("LOOPIS user roles change completed successfully!");
    return true;
}

/**
 * Copy capabilities from an existing role and create a new role
 * 
 * @param string $old_role_name Name of the existing role to copy from (null if creating new)
 * @param string $new_role_name Slug of the new role to create
 * @param string $display_name Display name for the new role
 * @param string $slug_name Slug version of the role name
 */
function loopis_copy_role($old_role_name, $new_role_name, $display_name, $slug_name) {
    global $wp_roles;
    
    if (!isset($wp_roles)) {
        $wp_roles = new WP_Roles();
    }
    
    // Get capabilities from old role if it exists
    $capabilities = array();
    if ($old_role_name) {
        $old_role = get_role($old_role_name);
        if (!$old_role) {
            error_log("Role '$old_role_name' not found, creating empty role instead...");
            $capabilities = array('read' => true); // Basic capability
        } else {
            $capabilities = $old_role->capabilities;
            error_log("Copying capabilities from '$old_role_name' to '$new_role_name'");
        }
    } else {
        $capabilities = array('read' => true); // Basic capability for new roles
    }
    
    // Remove the new role if it already exists
    if (get_role($new_role_name)) {
        remove_role($new_role_name);
        error_log("Removed existing role: $new_role_name");
    }
    
    // Create the new role
    $result = add_role($new_role_name, $display_name, $capabilities);
    
    if ($result) {
        error_log("Created new role: $new_role_name ($display_name)");
        
        // Move users from old role to new role if old role exists
        if ($old_role_name && get_role($old_role_name)) {
            loopis_move_users_to_new_role($old_role_name, $new_role_name);
        }
        
        return true;
    } else {
        error_log("Failed to create role: $new_role_name");
        return false;
    }
}

/**
 * Create a completely new role with basic capabilities
 * 
 * @param string $role_name Slug of the new role
 * @param string $display_name Display name for the role
 * @param string $slug_name Slug version of the role name
 */
function loopis_create_new_role($role_name, $display_name, $slug_name) {
    // Base capabilities for new roles (similar to author)
    $base_capabilities = array(
        'read' => true,
        'edit_posts' => true,
        'edit_published_posts' => true,
        'publish_posts' => true,
        'delete_posts' => true,
        'delete_published_posts' => true,
        'upload_files' => true,
        'edit_pages' => true,
        'edit_published_pages' => true,
        'publish_pages' => true,
        'delete_pages' => true,
        'delete_published_pages' => true,
    );
    
    // Remove the role if it already exists
    if (get_role($role_name)) {
        remove_role($role_name);
        error_log("Removed existing role: $role_name");
    }
    
    // Create the new role
    $result = add_role($role_name, $display_name, $base_capabilities);
    
    if ($result) {
        error_log("Created new role: $role_name ($display_name)");
        return true;
    } else {
        error_log("Failed to create new role: $role_name");
        return false;
    }
}

/**
 * Add one or more capabilities to an existing role
 * 
 * @param string $role_name Name of the role to add capabilities to
 * @param array $capabilities Array of capabilities to add
 */
function loopis_add_capabilities($role_name, $capabilities) {
    $role = get_role($role_name);
    
    if (!$role) {
        error_log("Role '$role_name' not found, cannot add capabilities");
        return false;
    }
    
    foreach ($capabilities as $cap) {
        $role->add_cap($cap);
        error_log("Added capability '$cap' to role '$role_name'");
    }
    
    error_log("Added " . count($capabilities) . " capabilities to role '$role_name'");
    return true;
}

/**
 * Move all users from old role to new role
 * 
 * @param string $old_role_name Name of the old role
 * @param string $new_role_name Name of the new role
 */
function loopis_move_users_to_new_role($old_role_name, $new_role_name) {
    $users = get_users(array('role' => $old_role_name));
    
    foreach ($users as $user) {
        $user_obj = new WP_User($user->ID);
        $user_obj->remove_role($old_role_name);
        $user_obj->add_role($new_role_name);
        error_log("Moved user {$user->user_login} from $old_role_name to $new_role_name");
    }
    
    error_log("Moved " . count($users) . " users from $old_role_name to $new_role_name");
}

/**
 * AJAX handler for user roles change
 */
function loopis_handle_user_roles_change() {
    // Verify nonce
    if (!wp_verify_nonce($_POST['nonce'], 'loopis_config_nonce')) {
        wp_die('Security check failed');
    }
    
    // Check user permissions
    if (!current_user_can('manage_options')) {
        wp_die('Insufficient permissions');
    }
    
    try {
        $result = loopis_user_roles_change();
        
        if ($result) {
            wp_send_json_success('User roles updated successfully!');
        } else {
            wp_send_json_error('Failed to update user roles');
        }
    } catch (Exception $e) {
        error_log('LOOPIS user roles change error: ' . $e->getMessage());
        wp_send_json_error('Error: ' . $e->getMessage());
    }
}

