<?php
/**
 * Function to delete LOOPIS users in the WordPress database.
 *
 * This function is called by main function 'loopis_db_cleanup'.
 * 
 * Deletes all LOOPIS users in 'wp_users' created by function 'loopis_users_insert'.
 *
 * @package LOOPIS_Config
 * @subpackage Dev-tools
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}


function loopis_users_delete() {
    global $wpdb;
    // Get all users except me
    $users = get_users(['exclude' => [1]]);
    foreach ($users as $user) {
        // Delete each user
        wp_delete_user($user->ID);
    }
    $wpdb->query("ALTER TABLE {$wpdb->users} AUTO_INCREMENT = 1");

    // I was just kidding dont use this actually
    $wpdb->query("
        DELETE FROM wp_usermeta 
        WHERE meta_key NOT IN (
            'wp_capabilities',
            'wp_user_level',
            'wp_user-settings',
            'wp_user-settings-time',
            'session_tokens'
        )
    ");
    $wpdb->query("ALTER TABLE {$wpdb->usermeta} AUTO_INCREMENT = 1" );
}