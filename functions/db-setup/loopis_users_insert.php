<?php
/**
 * Function to delete unused default plugins.
 *
 * This function is called by main function 'loopis_db_setup'.
 * 
 * WARNING: if you are not the user with ID 1 then you will be locked out when deletion happens
 *
 * @package LOOPIS_Config
 * @subpackage Database
 */


// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

function loopis_users_insert() {
    error_log('Running function loopis_users_insert...');

    // Access WordPress database object
    global $wpdb;

    // Base users not LOOPIS
    $base_users = [
        ['user_login'=>'develooper-1',	'user_nicename'=>'develooper-1',    'email'=>'develooper-1@loopis.app', 'display_name'=>'develooper-1'],
        ['user_login'=>'develooper-2',  'user_nicename'=>'develooper-2',	'email'=>'develooper-2@loopis.app',	'display_name'=>'develooper-2'],
        ['user_login'=>'develooper-3',	'user_nicename'=>'develooper-3',	'email'=>'develooper-3@loopis.app',	'display_name'=>'develooper-3'],
        ['user_login'=>'develooper-4',	'user_nicename'=>'develooper-4',	'email'=>'develooper-4@loopis.app',	'display_name'=>'develooper-4'],
        ['user_login'=>'develooper-5',	'user_nicename'=>'develooper-5',	'email'=>'develooper-5@loopis.app',	'display_name'=>'develooper-5'],
        ['user_login'=>'develooper-6',	'user_nicename'=>'develooper-6',	'email'=>'develooper-6@loopis.app',	'display_name'=>'develooper-6'],
        ['user_login'=>'develooper-7',	'user_nicename'=>'develooper-7',	'email'=>'develooper-7@loopis.app',	'display_name'=>'develooper-7'],
        ['user_login'=>'develooper-8',	'user_nicename'=>'develooper-8',	'email'=>'develooper-8@loopis.app',	'display_name'=>'develooper-8'],
        ['user_login'=>'develooper-9',	'user_nicename'=>'develooper-9',	'email'=>'develooper-9@loopis.app',	'display_name'=>'develooper-9'],
        ['user_login'=>'LOTTEN',	    'user_nicename'=>'LOTTEN',	        'email'=>'lotten@loopis.app',	    'display_name'=>'LOTTEN']
    ];

    // Get user by id 1
    $user_1 = get_user_by('ID', 1);

    // if they exist do:
    if ($user_1){

        // If user 1 isnt LOOPIS and there is no LOOPIS do:
        if ($user_1->user_login !=='LOOPIS' && !(username_exists('LOOPIS'))) {

            // Make user 1 LOOPIS
            $wpdb->update(
                $wpdb->users,
                array(
                    'user_login' => 'LOOPIS',
                    'display_name' => 'LOOPIS',
                    'user_nicename' => 'LOOPIS',
                    'user_email'=>'info@loopis.app',
                ),
                array('ID' => 1),           
                array('%s'),                      
                array('%d')     
            );

        }
    } 

    // Loop through and create users if they do not exist
    foreach ($base_users as $user){

        // Check if the user already exists
        if (username_exists($user['user_login'])) {
            return;
        }

        // Generate safe password 
        $password = wp_generate_password(12, FALSE);

        // Create the user
        $user_id = wp_create_user($user['user_login'], $password, $user['email']);
        if (is_wp_error($user_id)) {
            error_log('Failed to create user ' . $user['user_login'] . ': ' . $user_id->get_error_message());
            return;
        }

        // For testing purposes in case you want to log in as develooper or others:
        //error_log('User ' . $user['user_login'] . ' has password: ' . $password);

        // Set alternative userlist
        wp_update_user(array(
            'ID' => $user_id,
            'display_name' => $user['display_name'],
            'user_nicename' => $user['user_login']
        ));

        // Add admin capabilities(Should this be done here or in a set admin script in menu?)
        $user = new WP_User($user_id);
        $user->set_role('administrator'); // Make this user an administrator
    }
}   