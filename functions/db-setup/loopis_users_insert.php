<?php
/**
 * Function to create LOOPIS default users in the WordPress database.
 *
 * This function is called by main function 'loopis_db_setup'.
 *
 * @package LOOPIS_Config
 * @subpackage Database
 */

// This could be made much faster with batching or ajaxing...
// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Inserts users into wp_users
 * 
 * @return void
 */
function loopis_users_insert() {
    error_log('Running function loopis_users_insert...');

    // Access WordPress database object
    global $wpdb;

    // Base users
    $base_users = [
        [
            'user_login'    => 'LOOPIS',
            'user_nicename' => 'LOOPIS',
            'user_email'    => 'info@loopis.app',
            'user_pass'     => 'adm1n!',
            'role'          => ['administrator'],
            'display_name'  => 'LOOPIS',
            'first_name'    => 'LOOPIS',
            'last_name'     => 'admin',
        ],
        [
            'user_login'    => 'LOTTEN',
            'user_nicename' => 'LOTTEN',
            'user_email'    => 'lotten@loopis.app',
            'user_pass'     => 'adm1n!',
            'role'          => ['administrator'],
            'display_name'  => 'LOTTEN',
            'first_name'    => 'LOTTEN',
            'last_name'     => 'admin',
        ],
        [
            'user_login'    => 'gabby-giver',
            'user_nicename' => 'gabby-giver',
            'user_email'    => 'gabby-giver@loopis.app',
            'user_pass'     => 'memb3r',
            'role'          => ['member'],
            'display_name'  => 'Gabby-Giver',
            'first_name'    => 'Gabby',
            'last_name'     => 'Giver',
        ],
        [
            'user_login'    => 'fred-fetcher',
            'user_nicename' => 'fred-fetcher',
            'user_email'    => 'fred-fetcher@gmail.com',
            'user_pass'     => 'memb3r',
            'role'          => ['member'],
            'display_name'  => 'Fred-Fetcher',
            'first_name'    => 'Fred',
            'last_name'     => 'Fetcher',
        ],
        [
            'user_login'    => 'johan-hagvil',
            'user_nicename' => 'johan-hagvil',
            'user_email'    => 'johan.hagvil@gmail.com',
            'user_pass'     => 'develoop3r',
            'role'          => ['member','developer'],
            'display_name'  => 'Johan-Hagvil',
            'first_name'    => 'Johan',
            'last_name'     => 'Hagvil',
        ],
        [
            'user_login'    => 'johan-linger',
            'user_nicename' => 'johan-linger',
            'user_email'    => 'linger.konsult@gmail.com',
            'user_pass'     => 'develoop3r',
            'role'          => ['member','developer'],
            'display_name'  => 'Johan-Linger',
            'first_name'    => 'Johan',
            'last_name'     => 'Linger',
        ],
        [
            'user_login'    => 'hubert-hilborn',
            'user_nicename' => 'hubert-hilborn',
            'user_email'    => 'hubert.hilborn@hotmail.com',
            'user_pass'     => 'develoop3r',
            'role'          => ['member','developer'],
            'display_name'  => 'Hubert-Hilborn',
            'first_name'    => 'Hubert',
            'last_name'     => 'Hilborn',
        ],
        [
            'user_login'    => 'hanna-mustonen',
            'user_nicename' => 'hanna-mustonen',
            'user_email'    => 'mustonenhanna@icloud.com',
            'user_pass'     => 'develoop3r',
            'role'          => ['member','developer'],
            'display_name'  => 'Hanna-Mustonen',
            'first_name'    => 'Hanna',
            'last_name'     => 'Mustonen',
        ],
        [
            'user_login'    => 'develooper-5',
            'user_nicename' => 'develooper-5',
            'user_email'    => 'develooper-5@loopis.app',
            'user_pass'     => 'develoop3r',
            'role'          => 'developer',
            'display_name'  => 'develooper-5',
            'first_name'    => '',
            'last_name'     => '',
        ],
        [
            'user_login'    => 'develooper-6',
            'user_nicename' => 'develooper-6',
            'user_email'    => 'develooper-6@loopis.app',
            'user_pass'     => 'develoop3r',
            'role'          => ['developer'],
            'display_name'  => 'develooper-6',
            'first_name'    => '',
            'last_name'     => '',
        ],
        [
            'user_login'    => 'develooper-7',
            'user_nicename' => 'develooper-7',
            'user_email'    => 'develooper-7@loopis.app',
            'user_pass'     => 'develoop3r',
            'role'          => ['developer'],
            'display_name'  => 'develooper-7',
            'first_name'    => '',
            'last_name'     => '',
        ],
        [
            'user_login'    => 'develooper-8',
            'user_nicename' => 'develooper-8',
            'user_email'    => 'develooper-8@loopis.app',
            'user_pass'     => 'develoop3r',
            'role'          => ['developer'],
            'display_name'  => 'develooper-8',
            'first_name'    => '',
            'last_name'     => '',
        ],
        [
            'user_login'    => 'develooper-9',
            'user_nicename' => 'develooper-9',
            'user_email'    => 'develooper-9@loopis.app',
            'user_pass'     => 'develoop3r',
            'role'          => ['developer'],
            'display_name'  => 'develooper-9',
            'first_name'    => '',
            'last_name'     => '',
        ],
        [
            'user_login'    => 'develooper-10',
            'user_nicename' => 'develooper-10',
            'user_email'    => 'develooper-10@loopis.app',
            'user_pass'     => 'develoop3r',
            'role'          => ['developer'],
            'display_name'  => 'develooper-10',
            'first_name'    => '',
            'last_name'     => '',
        ],
        [
            'user_login'    => 'develooper-11',
            'user_nicename' => 'develooper-11',
            'user_email'    => 'develooper-11@loopis.app',
            'user_pass'     => 'develoop3r',
            'role'          => ['developer'],
            'display_name'  => 'develooper-11',
            'first_name'    => '',
            'last_name'     => '',
        ],
        [
            'user_login'    => 'develooper-12',
            'user_nicename' => 'develooper-12',
            'user_email'    => 'develooper-12@loopis.app',
            'user_pass'     => 'develoop3r',
            'role'          => ['developer'],
            'display_name'  => 'develooper-12',
            'first_name'    => '',
            'last_name'     => '',
        ],
        [
            'user_login'    => 'develooper-13',
            'user_nicename' => 'develooper-13',
            'user_email'    => 'develooper-13@loopis.app',
            'user_pass'     => 'develoop3r',
            'role'          => ['developer'],
            'display_name'  => 'develooper-13',
            'first_name'    => '',
            'last_name'     => '',
        ],
        [
            'user_login'    => 'develooper-14',
            'user_nicename' => 'develooper-14',
            'user_email'    => 'develooper-14@loopis.app',
            'user_pass'     => 'develoop3r',
            'role'          => ['developer'],
            'display_name'  => 'develooper-14',
            'first_name'    => '',
            'last_name'     => '',
        ],
    ];
    
    // Get user by id 1 (admin by default)
    $user_1 = get_user_by('ID', 1);

    // if they exist do:
    if ($user_1){

        // If user 1 isnt admin do:
        if ($user_1->user_login !=='admin' && $user_1->user_login !=='admin@loopis.app') {

            // Make user 1 admin
            $wpdb->update(
                $wpdb->users,
                array(
                    'user_login' => 'admin',
                    'display_name' => 'admin',
                    'user_nicename' => 'admin',
                    'user_email'=>'admin@loopis.app',
                    'user_pass'     => wp_hash_password('adm1n!')
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
            error_log('Failed to create user ' . $user['user_login'] . ': ' . $user_id->get_error_message());
            continue;
        }
        // Add admin capabilities
        $user_id = new WP_User($user_id);
        foreach($user['role'] as $role){
            $user_id->set_role($role);
        }
    }
}   