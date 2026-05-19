<?php
/**
 * Functions to fetch and install Loopis repos (Admin, Content, Develooper, Theme, and Users).
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
    }else{
        loopis_elog_first_level( "Installed: $slug!" );
    }
    $plugin_slug = $slug . '-main/' . $slug . '.php'; // if installed then activate
    if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_slug ) ) {
        activate_plugin( $plugin_slug , '', is_multisite());
        loopis_elog_first_level( "Activated: $slug!" );
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
    $themearray = array(
        sanitize_text_field($data['slug'] ?? '' ) => sanitize_text_field($data['zip_url'] ?? '' ),
        sanitize_text_field($data['HQ_slug'] ?? '' ) => sanitize_text_field($data['HQ_zip_url'] ?? '' )
    );
    //set upgrader and attempt install
    $upgrader = new Theme_Upgrader(new Automatic_Upgrader_Skin());

    foreach ($themearray as $slug => $zip_url) {
        $result = $upgrader->install($zip_url);

        if ( is_wp_error($result) ) {
            loopis_elog_first_level("Failed to install theme: " . $result->get_error_message());
        }else{
            loopis_elog_first_level( "Installed theme: {$slug}" );
        }
    }

    loopis_activate_theme('loopis theme hq');

    foreach (wp_get_themes() as $slug => $theme_obj) { // Get all themes

        if (preg_match('/^twenty/i', $slug) && get_stylesheet() !== $slug) { // Get non active default themes 

            delete_theme($slug);  
            loopis_elog_first_level( "Deleted theme: {$slug}" );
        }
    }
    loopis_elog_function_end_success('loopis_themes_configure');
}

function loopis_activate_theme($slug='loopis theme'){
    $installed_themes = wp_get_themes();
    $theme_stylesheet = null;

    foreach ( $installed_themes as $stylesheet => $theme_obj ) {
        if ( strtolower($theme_obj->get('Name')) === strtolower($slug) ) {
            $theme_stylesheet = $stylesheet;
            break;
        }
    }

    if ( $theme_stylesheet ) {
        if (is_multisite()) {
            $allowed_themes = get_site_option('allowedthemes', []);
            $allowed_themes[$theme_stylesheet] = true;
            update_site_option('allowedthemes', $allowed_themes);
            switch_theme($theme_stylesheet);
        } else {
            switch_theme($theme_stylesheet);
        }
        loopis_elog_first_level( "Activated theme: {$slug}" );
    } else {
        loopis_elog_first_level( "Theme installed but not registered." );
    }
}
