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

/* Let's not mess with user 1 for now, because that logs you out of WP Admin...
    // Access WordPress database object
    global $wpdb;

    // Configure default admin
    $user_1 = get_user_by('ID', 1);
    if ($user_1) {
        if ($user_1->user_login !== 'admin' && $user_1->user_login !== 'admin@loopis.app') {
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
                array('%s', '%s', '%s', '%s', '%s'),
                array('%d')
            );
        }
    }
 */

    // Insert admin users
    $admin_users = array(
        array(
            'user_login'    => 'LOOPIS',
            'user_nicename' => 'LOOPIS',
            'user_email'    => 'info@loopis.app',
            'user_pass'     => 'w3bmaster!',
            'role'          => 'administrator',
            'display_name'  => 'LOOPIS',
            'first_name'    => 'LOOPIS',
            'last_name'     => 'admin',
        ),
        array(
            'user_login'    => 'LOTTEN',
            'user_nicename' => 'LOTTEN',
            'user_email'    => 'lotten@loopis.app',
            'user_pass'     => 'w3bmaster!',
            'role'          => 'administrator',
            'display_name'  => 'LOTTEN',
            'first_name'    => 'LOTTEN',
            'last_name'     => 'robot',
        ),
        array(
            'user_login'    => 'NISSE',
            'user_nicename' => 'NISSE',
            'user_email'    => 'nisse@loopis.app',
            'user_pass'     => 'w3bmaster!',
            'role'          => 'administrator',
            'display_name'  => 'NISSE',
            'first_name'    => 'NISSE',
            'last_name'     => 'robot',
        ),
    );
    
    // Loop through and create/update users
    foreach ($admin_users as $user) {
        // Check if the user already exists
        $existing_user = get_user_by('login', $user['user_login']);
        
        if ($existing_user) {
            // User exists - update their role to make sure it's set correctly
            $user_obj = new WP_User($existing_user->ID);
            $user_obj->set_role($user['role']);
            loopis_elog_first_level('Updated existing user: ' . $user['user_login']);
            continue;
        }
        
        // Insert new user
        $user_id = wp_insert_user(array(
            'user_login'    => $user['user_login'],
            'user_pass'     => $user['user_pass'],
            'user_email'    => $user['user_email'],
            'display_name'  => $user['display_name'],
            'user_nicename' => $user['user_nicename'],
            'first_name'    => $user['first_name'],
            'role'          => $user['role'],
        ));

        if (is_wp_error($user_id)) {
            loopis_elog_first_level('Failed to create user ' . $user['user_login'] . ': ' . $user_id->get_error_message());
            continue;
        }

        loopis_elog_first_level('Created new user: ' . $user['user_login']);
    }
    
    loopis_elog_function_end_success('loopis_admins_insert');
}