<?php
/**
 * Function to delete unused default plugins.
 *
 * This function is called by main function 'loopis_db_setup'.
 * 
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

    global $wpdb;

    // Base users not loopis
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

    // Assimilate admin into LOOPIS
    // Note that this is usually not something that is reccomended
    $current_user = wp_get_current_user();
    if (!($current_user->ID ==1)){
        change_user_id($current_user->ID, 1);
    }
    if (!($current_user->user_login =='LOOPIS')&& !(username_exists('LOOPIS'))) {
        $wpdb->update(
            $wpdb->users,
            array('user_login' => 'LOOPIS',
            'display_name' => 'LOOPIS',
            'user_nicename' => 'LOOPIS',
            'user_email'=>'info@loopis.app',
            ),
            array('ID' => 1),           
            array('%s'),                      
            array('%d')     
        );
    }


    // Loop through and create users if they do not exist
    foreach ($base_users as $user){
        // Check if the user already exists
        if (username_exists($user['user_login'])) {
            return;
        }

        // Create the user
        $user_id = wp_create_user($user['user_login'],'password', $user['email']);
        if (is_wp_error($user_id)) {
            error_log('Oh no, something broke in loopis users insert!');
            return;
        }

        // Set alternative userlist
        wp_update_user(array(
            'ID' => $user_id,
            'display_name' => $user['display_name'],
            'user_nicename' => $user['user_login']
        ));

        // Add admin capabilities
        $user = new WP_User($user_id);
        $user->set_role('administrator'); // Make this user an administrator
    }
}   

function change_user_id($old_user_id, $new_user_id) {
    global $wpdb;

    // Check if the new ID already exists to prevent conflicts
    if (get_user_by('id', $new_user_id)) {
        return new WP_Error('user_exists', 'User ID already exists.');
    }

    // Update wp_users table
    $wpdb->update(
        $wpdb->users,
        array('ID' => $new_user_id),
        array('ID' => $old_user_id),
        array('%d'),
        array('%d')
    );

    // Update wp_usermeta table
    $wpdb->update(
        $wpdb->prefix . 'usermeta',
        array('user_id' => $new_user_id),
        array('user_id' => $old_user_id),
        array('%d'),
        array('%d')
    );

    // Update wp_posts table 
    $wpdb->update(
        $wpdb->prefix . 'posts',
        array('post_author' => $new_user_id),
        array('post_author' => $old_user_id),
        array('%d'),
        array('%d')
    );

    // Update wp_comments table
    $wpdb->update(
        $wpdb->prefix . 'comments',
        array('user_id' => $new_user_id),
        array('user_id' => $old_user_id),
        array('%d'),
        array('%d')
    );
}