<?php
/**
 * Function to delete LOOPIS user roles in the WordPress database.
 *
 * This function is called by main function 'loopis_db_cleanup'.
 * 
 * Deletes all LOOPIS user roles created by function 'loopis_user_roles_change'.
 *
 * @package LOOPIS_Config
 * @subpackage Dev-tools
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Delete all LOOPIS user roles
 * 
 * This function is called by main cleanup function.
 * Removes all LOOPIS-specific user roles created by loopis_user_roles_change.
 * 
 * @return bool True on success
 */
function loopis_user_roles_delete() {
    loopis_elog_function_start('loopis_user_roles_delete');
    
    // ===== CONFIGURATION - EASY TO MODIFY =====
    
    // 1. Remove LOOPIS capabilities from administrator role
    loopis_remove_capabilities_from_role('administrator', array('loopis_admin', 'loopis_support', 'loopis_economy'));
    
    // 2. Delete each LOOPIS role (one liner per role)
    loopis_delete_role('board');
    loopis_delete_role('member');
    loopis_delete_role('manager');
    loopis_delete_role('member_pending');
    loopis_delete_role('developer');
    
    // ===== END CONFIGURATION =====
    loopis_elog_function_end_success('loopis_user_roles_delete');
    return true;
}

/**
 * Remove one or more capabilities from an existing role
 * 
 * @param string $role_name Name of the role to remove capabilities from
 * @param array $capabilities Array of capabilities to remove
 * @return bool True on success
 */
function loopis_remove_capabilities_from_role($role_name, $capabilities) {
    $role = get_role($role_name);
    
    if (!$role) {
        loopis_elog_first_level(" Role '$role_name' not found, cannot remove capabilities");
        return false;
    }
    
    foreach ($capabilities as $cap) {
        if ($role->has_cap($cap)) {
            $role->remove_cap($cap);
            loopis_elog_first_level(" Removed capability '$cap' from role '$role_name'");
        } else {
            loopis_elog_first_level(" Capability '$cap' not found in role '$role_name', skipping");
        }
    }
    
    loopis_elog_first_level(" Processed " . count($capabilities) . " capabilities for role '$role_name'");
    return true;
}

/**
 * Delete an existing role
 * 
 * @param string $role_name Name of the role to delete
 * @return bool True on success
 */
function loopis_delete_role($role_name) {
    // Skip if role doesn't exist
    if (!get_role($role_name)) {
        loopis_elog_first_level(" Role '$role_name' does not exist, skipping deletion");
        return true;
    }
    
    // Don't delete administrator role for safety
    if ($role_name === 'administrator') {
        loopis_elog_first_level(" Skipping deletion of administrator role for safety");
        return true;
    }
    
    // Remove the role
    remove_role($role_name);
    loopis_elog_first_level(" Deleted role: $role_name");
    return true;
}