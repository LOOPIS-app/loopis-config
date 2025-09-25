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

/**
 * Delete all LOOPIS users but admin from wp_users
 * 
 * @return void
 */
function loopis_users_delete() {
    global $wpdb;
    // Get all users except user 1(admin)
    $users = get_users(['exclude' => [1]]);
    foreach ($users as $user) {
        // Delete each user
        wp_delete_user($user->ID);
    }
    // Resets user count
    $wpdb->query("ALTER TABLE {$wpdb->users} AUTO_INCREMENT = 1");
}