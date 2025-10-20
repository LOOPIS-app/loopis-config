<?php
/**
 * Function to create LOOPIS default users in the WordPress database.
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
 * Insert admin users into wp_users
 * 
 * @return void
 */
function loopis_admins_insert() {
    loopis_elog_function_start('loopis_admins_insert');

    // Access WordPress database object
    global $wpdb;

    // Configure default admin
    $user_1 = get_user_by('ID', 1);
    if ($user_1){

        // If user 1 isnt admin do:
        if ($user_1->user_login !=='admin' && $user_1->user_login !=='admin@loopis.app') {

            // Make user 1 admin
            $wpdb->update(
                $wpdb->users,
                array(
                    'user_login'    => 'admin',
                    'display_name'  => 'admin',
                    'user_nicename' => 'admin',
                    'user_email'    => 'admin@loopis.app',
                    'user_pass'     => wp_hash_password('w3bmaster!')
                ),
                array('ID' => 1),
                array('%s, %s, %s, %s, %s'),
                array('%d')
            );

        }
    } 

    // Insert admin users
    $admin_users = [
        [
            'user_login'    => 'LOOPIS',
            'user_nicename' => 'LOOPIS',
            'user_email'    => 'info@loopis.app',
            'user_pass'     => 'w3bmaster!',
            'role'          => ['administrator'],
            'display_name'  => 'LOOPIS',
            'first_name'    => 'LOOPIS',
            'last_name'     => 'admin',
        ],
        [
            'user_login'    => 'LOTTEN',
            'user_nicename' => 'LOTTEN',
            'user_email'    => 'lotten@loopis.app',
            'user_pass'     => 'w3bmaster!',
            'role'          => ['administrator'],
            'display_name'  => 'LOTTEN',
            'first_name'    => 'LOTTEN',
            'last_name'     => 'admin',
        ],
        [
            'user_login'    => 'admin-4',
            'user_nicename' => 'admin-4',
            'user_email'    => 'admin-4@loopis.app',
            'user_pass'     => 'w3bmaster!',
            'role'          => ['administrator'],
            'display_name'  => 'admin-4',
            'first_name'    => 'admin-4',
            'last_name'     => 'reserved',
        ],
        [
            'user_login'    => 'admin-5',
            'user_nicename' => 'admin-5',
            'user_email'    => 'admin-5@loopis.app',
            'user_pass'     => 'w3bmaster!',
            'role'          => ['administrator'],
            'display_name'  => 'admin-5',
            'first_name'    => 'admin-5',
            'last_name'     => 'reserved',
        ],
    ];
    
    // Loop through and create users if they do not exist
    foreach ($admin_users as $user){

        // Check if the user already exists
        if (username_exists($user['user_login'])) {
            continue;
        }
        // Insert user
        $user_id = wp_insert_user([
            'user_login'    => $user['user_login'],
            'user_pass'     => $user['user_pass'],
            'user_email'    => $user['user_email'],
            'display_name'  => $user['display_name'],
            'user_nicename' => $user['user_nicename'],
            'first_name'    => $user['first_name'],
            'last_name'     => $user['last_name']
        ]);

        if (is_wp_error($user_id)) {
            loopis_elog_first_level('Failed to create user ' . $user['user_login'] . ': ' . $user_id->get_error_message());
            continue;
        }

        // Add admin capabilities
        $user_obj = new WP_User($user_id);
        foreach($user['role'] as $role){
            $user_obj->set_role($role);
        }
    }
    loopis_elog_function_end_success('loopis_admins_insert');
}