<?php
/**
 * Function to set default screen options for WordPress admin Dashboard.
 * 
 * WILL BE ADJUSTED TO SHOW PLUGIN-SPECIFIC WIDGETS.
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
 * Set default screen options for WordPress admin Dashboard
 *
 * @return void
 */
function loopis_screen_options_set() {
    error_log('Running function loopis_screen_options_set...');
    
    // Get all users
    $users = get_users();
    
    foreach ($users as $user) {
        // Hide specific dashboard widgets (keeping Site Health Status, At a Glance, Activity)
        update_user_meta($user->ID, 'metaboxhidden_dashboard', array(
            'welcome-panel',              // Welcome
            'dashboard_primary',          // WordPress Events and News
            'dashboard_quick_press'       // Quick Draft
        ));
        
        // Set posts per page in admin lists to 100
        update_user_meta($user->ID, 'edit_post_per_page', 100);
        update_user_meta($user->ID, 'edit_page_per_page', 100);
        update_user_meta($user->ID, 'users_per_page', 100);
        update_user_meta($user->ID, 'edit_comments_per_page', 100);
        
        error_log('Set screen options for user: ' . $user->user_login);
    }
    
    // Set screen options for new users automatically
    add_action('user_register', 'loopis_set_new_user_screen_options');
}

/**
 * Set screen options for newly registered users
 *
 * @param int $user_id User ID of the new user
 * @return void
 */
function loopis_set_new_user_screen_options($user_id) {
    // Dashboard settings - hide unwanted widgets
    update_user_meta($user_id, 'metaboxhidden_dashboard', array(
        'welcome-panel',              // Welcome
        'dashboard_primary',          // WordPress Events and News
        'dashboard_quick_press'       // Quick Draft
    ));
    
    // Posts per page set to 100
    update_user_meta($user_id, 'edit_post_per_page', 100);
    update_user_meta($user_id, 'edit_page_per_page', 100);
    update_user_meta($user_id, 'users_per_page', 100);
    update_user_meta($user_id, 'edit_comments_per_page', 100);
    
    error_log('Set screen options for new user ID: ' . $user_id);
}