<?php
/**
 * Set up LOOPIS custom user roles.
 * 
 * This function is called by main function 'loopis_db_setup'.
 * 
 * @package LOOPIS_Config
 * @subpackage Database
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Set up LOOPIS custom user roles.
 * 
 * @return bool true
 */
function loopis_roles_set() {
    loopis_elog_function_start('loopis_roles_set');

    // Get current roles
    $current_roles = get_option('wp_user_roles');

    // Remove all default WordPress roles except administrator
    $default_roles_to_remove = array('editor', 'author', 'contributor', 'subscriber');
    foreach ($default_roles_to_remove as $role) {
        if (get_role($role)) {
            remove_role($role);
        }
    }
    $admin_role = get_role('administrator');
    $roles = array(
        'member_canceled' => array(
            'name' => 'Member_canceled',
            'capabilities' => array(
                'read' => true,
            ),
        ),
        'member_archived' => array(
            'name' => 'Member_archived',
            'capabilities' => array(
                'read' => true,
            ),
        ),
        'member_pending' => array(
            'name' => 'Member_pending',
            'capabilities' => array(
                'read' => true,
            ),
        ),
        'member' => array(
            'name' => 'Member',
            'capabilities' => array(
                'read' => true,
                'edit_posts' => true,
                'publish_posts' => true,
                'edit_published_posts' => true,
                'upload_files' => true,
                'unfiltered_html' => true,
            ),
        ),
        'board' => array(
            'name' => 'Board',
            'capabilities' => array(
                'read' => true,
                'edit_posts' => true,
                'publish_posts' => true,
                'edit_published_posts' => true,
                'upload_files' => true,
                'unfiltered_html' => true,
                'read_private_posts' => true,
                'edit_private_posts' => true,
                'edit_others_posts' => true,
            ),
        ),
        'manager' => array(
            'name' => 'Manager',
            'capabilities' => array(
                'read' => true,
                'edit_posts' => true,
                'publish_posts' => true,
                'edit_published_posts' => true,
                'upload_files' => true,
                'unfiltered_html' => true,
                'read_private_posts' => true,
                'edit_private_posts' => true,
                'edit_others_posts' => true,
                'delete_posts' => true,
                'delete_published_posts' => true,
                'delete_private_posts' => true,
                'delete_others_posts' => true,
            ),
        ),
        'develooper' => array(
            'name'         => 'Develooper',
            'capabilities' => $admin_role ? $admin_role->capabilities : array()
        ),
    );
    // Define LOOPIS custom capabilities and which roles should have them
    $loopis_capabilities = array(
        'loopis_admin' => array(
            'administrator',
            'develooper',
            'manager',
            'board',
        ),
        'loopis_cron' => array(
            'administrator',
            'develooper',
        ),
        'loopis_support' => array(
            'administrator',
            'develooper',
            'manager',
            'board',
        ),
        'loopis_economy' => array(
            'administrator',
            'develooper',
            'manager',
            'board',
        ),
        'loopis_storage_submit' => array(
            'administrator',
            'develooper',
            'manager',
            'board',
        ),
        'loopis_storage_book' => array(
            'administrator',
            'develooper',
            'manager',
            'board',
        ),
    );
    foreach ($roles as $role => $role_info) {
        if (!get_role($role)) {
            add_role($role, $role_info['name'], $role_info['capabilities']);
        }
    }
    // Apply LOOPIS custom capabilities to roles
    foreach($loopis_capabilities as $cap => $role_list){
        foreach($role_list as $role_name){
            $r = get_role($role_name);
            if($r){
                $r->add_cap($cap);
            }
        }
    }
    // Save updated roles
    update_option('wp_user_roles', $roles);

    loopis_elog_function_end_success('loopis_roles_set');
    return true;
}