<?php
/**
 * Function to set the default WordPress settings in the 'wp_options' table.
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
 * Set options in 'wp_options'
 * 
 * @return void
 */
function loopis_wp_options_set() {
    loopis_elog_function_start('loopis_wp_options_set');
    
    // Get page IDs by slug
    $home_page_id = get_page_by_path('start');
    $blog_page_id = get_page_by_path('gifts');
    $privacy_page_id = get_page_by_path('privacy');
    
    // Define the options
    $wp_options = array(
        'blogname'              => 'LOOPIS',
        'blogdescription'       => 'Ge & få saker i ditt grannskap.',
        'admin_email'           => 'admin@loopis.app',
        'users_can_register'    => '1',
        'default_role'          => 'member_pending',
        'posts_per_page'        => '50',
        'date_format'           => 'Y-m-d',
        'time_format'           => 'H:i',
        'permalink_structure'   => '/%postname%/',
        'comment_registration'  => '1',
        'show_on_front'         => 'page',
        'page_on_front'         => $home_page_id ? $home_page_id->ID : 0,
        'page_for_posts'        => $blog_page_id ? $blog_page_id->ID : 0,
        'wp_page_for_privacy_policy' => $privacy_page_id ? $privacy_page_id->ID : 0,
        'thumbnail_size_w'      => '240',
        'thumbnail_size_h'      => '240',
        'large_size_w'          => '1920',
        'large_size_h'          => '1920',
        'thread_comments_depth' => '2',
        'comment_order'         => 'desc',
        'timezone_string'       => 'Europe/Stockholm',
        'WPLANG'                => 'sv_SE',
        'auto_update_core_major'=> 'disabled',
        'loopis_config_version' => LOOPIS_CONFIG_VERSION,
        'fresh_site'            => '0'
        // Add more options as needed
    );

    // Set the options
    foreach ($wp_options as $option_name => $option_value) {
        update_option($option_name, $option_value);
    }
    loopis_elog_function_end_success('loopis_wp_options_set');
}