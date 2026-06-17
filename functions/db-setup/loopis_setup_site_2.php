<?php
/**
 * Function to create a subsite in wp multisite, url: BASEURL/12845  name: Bagarmossen
 *
 * These functions are called in the install function implemented on the config page.
 * 
 * @package LOOPIS_Config
 * @subpackage Database
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
} 

/**
 * Create Bagarmossen
 * 
 * @return void
 */
function loopis_setup_site() {
    loopis_elog_function_start('loopis_setup_site');

    $network = get_network();
    $domain = $network->domain;
    $path = trailingslashit($network->path . '12845');
    $title = 'Bagarmossen';
    $blogname = 'Bagarmossen';
    $admin_id = 1;
   
    $new_site_id = wp_insert_site(array(
        'domain' => $domain,
        'path' => $path,
        'title' => $title,
        'user_id' => $admin_id,
        'options' => array(
            'blog_name' => $blogname,
        ),
    ));

    if(is_wp_error($new_site_id)){
        loopis_elog_first_level($new_site_id->get_error_message());
        loopis_elog_function_end_failure('loopis_setup_site');
    } else{
        loopis_elog_function_end_success('loopis_setup_site');
    }
    
}
