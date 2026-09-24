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

    // Remove all default WordPress roles except administrator
    $default_roles_to_remove = array('editor', 'author', 'contributor', 'subscriber','member_cancelled');
    foreach ($default_roles_to_remove as $role) {
        if (get_role($role)) {
            remove_role($role);
        }
    }
    $admin_role = get_role('administrator');
    
    // Create custom LOOPIS roles
    $roles = array(
        'member_earlier' => array(
            'name' => 'Member_earlier',
            'capabilities' => array(
                'read' => true,
                'use_locker' => true,
            ),
        ),
        'member_archived' => array(
            'name' => 'Member_archived',
            'capabilities' => array(
                'read' => true,
                'use_locker' => true,
            ),
        ),
        'member_pending' => array(
            'name' => 'Member_pending',
            'capabilities' => array(
                'read' => true,
            ),
        ),
        'member_support' => array(
            'name' => 'Member_support',
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
                'use_locker' => true,
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
        'stocker' => array(
            'name' => 'Stocker',
            'capabilities' => array(
                'read_private_posts' => true,
                'edit_private_posts' => true,
            ),
        ),
    );
    // Create LOOPIS custom capabilities and which roles should have them
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
        'loopis_storage' => array(
            'administrator',
            'develooper',
            'manager',
            'board',
            'stocker',
        ),
        'use_locker' => array(
            'administrator',
            'develooper', 
            'member_earlier',
            'member_archived',            
            'member_pending',
            'member',
        ),
    );
    
    foreach ($roles as $slug => $data)
         {
        $role = get_role($slug);

        if (!$role) {
            $role = add_role(
                $slug,
                $data['name'],
                $data['capabilities']
            );
        } else {
            foreach ($data['capabilities'] as $capability => $grant) {
                if ($grant) {
                    $role->add_cap($capability);
                }
            }
        }
    }

    foreach ($loopis_capabilities as $capability => $role_slugs) {
        foreach ($role_slugs as $role_slug) {
            $role = get_role($role_slug);

            if ($role) {
                $role->add_cap($capability);
            }
        }
    }

    loopis_elog_function_end_success('loopis_roles_set');
    return true;
}