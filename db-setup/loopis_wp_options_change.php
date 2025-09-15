<?php
/**
 * Function to change the default WordPress settings in the 'wp_options' table.
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
 * Change settings in 'wp_options'
 */
function loopis_wp_options_change() {
    error_log('Running function loopis_wp_options_change...');
    
    $options_to_set = array(
        'blogname'              => 'LOOPIS',
        'blogdescription'       => '–',
        'users_can_register'    => '1',
        'posts_per_page'        => '50',
        'date_format'           => 'Y-m-d',
        'time_format'           => 'H:i',
        'permalink_structure'   => '/%postname%/',
        'comment_registration'  => '1',
        'show_on_front'         => 'page',
        'thumbnail_size_w'      => '240',
        'thumbnail_size_h'      => '240',
        'large_size_w'          => '1920',
        'large_size_h'          => '1920',
        'thread_comments_depth' => '2',
        'comment_order'         => 'desc',
        'timezone_string'       => 'Europe/Stockholm',
        'auto_update_core_major'=> 'disabled',
        // Add more options as needed
    );

    foreach ($options_to_set as $option_name => $option_value) {
        update_option($option_name, $option_value);
    }
}