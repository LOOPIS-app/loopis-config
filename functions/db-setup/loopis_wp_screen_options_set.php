<?php
/**
 * Function to set default screen options for WordPress admin Dashboard.
 * 
 * WILL BE ADJUSTED TO SHOW PLUGIN-SPECIFIC WIDGETS...
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
function loopis_wp_screen_options_set() {
    loopis_elog_function_start('loopis_wp_screen_options_set');
    
    // Get all users
    $users = get_users();
    
    foreach ($users as $user) {
        // Hide Welcome panel
        update_user_meta($user->ID, 'show_welcome_panel', 0);
        
        // Uncheck dashboard widgets in Screen Options
        update_user_meta($user->ID, 'metaboxhidden_dashboard', array(
            'dashboard_primary',        // WordPress Events and News
            'dashboard_quick_press',    // Quick Draft
            
            // KEEP these widgets (don't hide):
            // 'dashboard_site_health',    // Site Health Status
            // 'dashboard_right_now',      // At a Glance  
            // 'dashboard_activity',       // Activity
        ));
        
        // Set posts per page in admin lists to 100
        update_user_meta($user->ID, 'edit_post_per_page', 100);
        update_user_meta($user->ID, 'edit_page_per_page', 100);
        update_user_meta($user->ID, 'users_per_page', 100);
        update_user_meta($user->ID, 'edit_comments_per_page', 100);
        
        loopis_elog_first_level('Set screen options for user: ' . $user->user_login);
    }
    
    loopis_elog_first_level('Dashboard widget screen options updated for all users');
    loopis_elog_function_end_success('loopis_wp_screen_options_set');
}