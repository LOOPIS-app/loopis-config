<?php
/**
 * Functions for configuring a new loopis 'blog' installation.
 * 
 * @package LOOPIS_Config
 * @subpackage Admin-page
 */


function loopis_initialize_database($blog_id){
    // Go to sub-site
    switch_to_blog( $blog_id );
    // Terms
    loopis_cats_insert();
    // Pages
    loopis_pages_insert();
    loopis_pages_rename();
    // Options
    loopis_settings_create();
    loopis_settings_insert();
    loopis_wp_options_set();
    loopis_roles_set();
    loopis_tags_insert();
    // Back to main
    restore_current_blog();
}

