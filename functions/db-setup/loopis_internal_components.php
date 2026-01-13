<?php
/**
 * Functions to fetch and install Loopis repos Admin, Theme and Develooper.
 *
 * @package LOOPIS_Config
 * @subpackage Database
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
} 

/**
 * Installs plugins in wp-content
 * 
 * @return void
 */
function loopis_components_install(){
    loopis_elog_function_start('loopis_components_install');
    //Get install data
    $data = $_POST['data'];
    $slug = sanitize_text_field($data['slug'] ?? '' );
    $zip_url = sanitize_text_field($data['zip_url'] ?? '' );
    // Get upgrader
    $upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
    $result = $upgrader->install( $zip_url ); // Install

    if ( is_wp_error( $result ) ) {
        loopis_elog_first_level( "Failed installing $slug" );
    }

    $plugin_slug = $slug . '-main/' . $slug . '.php'; // if installed then activate
    if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_slug ) ) {
        activate_plugin( $plugin_slug );
    }
    // will not run yet
    if (function_exists('github_updater_register_plugin')) {
        github_updater_register_plugin(array(
            'slug'   => $slug,
            'uri'    => $zip_url,
            'type'   => 'plugin',
            'update' => true,
        ));
    }

    
    loopis_elog_function_end_success('loopis_components_install');
}

/**
 * Installs loopis theme and deletes default themes from wp-content
 * 
 * @return void
 */
function loopis_themes_configure() {
    loopis_elog_function_start('loopis_themes_configure');
    $data = $_POST['data'];
    $slug = sanitize_text_field($data['slug'] ?? '' );
    $zip_url = sanitize_text_field($data['zip_url'] ?? '' );
    //set upgrader and attempt install
    $upgrader = new Theme_Upgrader(new Automatic_Upgrader_Skin());
    $result = $upgrader->install($zip_url);

    if ( is_wp_error($result) ) {
        loopis_elog_first_level("Failed to install theme: " . $result->get_error_message());
    }else{
        loopis_elog_first_level( "Installed theme: {$slug}" );

        $installed_themes = wp_get_themes();
        $theme_stylesheet = null;

        foreach ( $installed_themes as $stylesheet => $theme_obj ) {
            if ( strtolower($theme_obj->get('Name')) === strtolower('LOOPIS Theme') ) {
                $theme_stylesheet = $stylesheet;
                break;
            }
        }

        if ( $theme_stylesheet ) {
            switch_theme( $theme_stylesheet );
            loopis_elog_first_level( "Activated theme: {$slug}" );
        } else {
            loopis_elog_first_level( "Theme installed but not registered." );
        }

    }

    foreach (wp_get_themes() as $slug => $theme_obj) { // Get all themes

        if (preg_match('/^twenty/i', $slug) && get_stylesheet() !== $slug) { // Get non active default themes 

            delete_theme($slug);  
            loopis_elog_first_level( "Deleted theme: {$slug}" );
        }
    }
    loopis_elog_function_end_success('loopis_themes_configure');
}