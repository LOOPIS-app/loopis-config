<?php
/**
 * Functions for configuring a new loopis 'blog' installation.
 * 
 * @package LOOPIS_Config
 * @subpackage Admin-page
 */

/**
 * Function: loopis_initialize_database
 * Description:
 *              Function to configure site database.
 *
 * @return void Sends JSON response for UI.
 */

function loopis_initialize_database( WP_Site $new_site ){
    $blog_id = (int) ( $new_site->blog_id ?? $new_site->id ?? 0 );
    // Go to sub-site    
    if ( $blog_id <= 0 ) {
        return; 
    }
    switch_to_blog( $blog_id );
    // Terms
    loopis_cats_insert();
    // Options
    loopis_settings_create();
    loopis_settings_insert();
    loopis_wp_options_set();
    loopis_roles_set();
    loopis_tags_insert();
    loopis_delete_default_content();
    
    //lockers
    loopis_lockers_create();

    loopis_activate_theme();
    // Back to main
    restore_current_blog();
}
/**
 * Function: loopis_configure_site_database
 * Description:
 *              Function to configure site database.
 *
 * @return void Sends JSON response for UI.
 */

function loopis_configure_site_database(){
    // Terms
    loopis_cats_insert();
    // Options
    loopis_settings_create();
    loopis_settings_insert();
    loopis_wp_options_set();
    loopis_roles_set();
    loopis_tags_insert();
    //lockers?
    loopis_lockers_create();
    loopis_activate_theme($slug='loopis theme');
}

