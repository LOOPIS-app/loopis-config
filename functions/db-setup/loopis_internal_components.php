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
    $check = get_plugins();
    $path = $slug . '-staging/' . $slug . '.php';
    if (isset($check[$path])){
        return;
    }
    if (isset($check[$slug . '-main/' . $slug . '.php'])){
        return;
    }
    // Get upgrader
    $upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );
    $result = $upgrader->install( $zip_url ); // Install
    if ( is_wp_error( $result ) ) {
        loopis_elog_first_level( "Failed installing $slug" );
    }else{
        loopis_elog_first_level( "Installed: $slug!" );
        $installed_dir = '';
        if ( is_array( $result ) && ! empty( $result['destination'] ) ) {
            $installed_dir = basename( rtrim( $result['destination'], '/' ) );
        } else {
            $candidates = glob( WP_PLUGIN_DIR . '/' . $slug . '*', GLOB_ONLYDIR ) ?: [];
            $installed_dir = $candidates[0] ? basename( $candidates[0] ) : '';
        }
        
        if ( $installed_dir ) {
            $target_dir = $slug;

            if ( in_array( substr( $installed_dir, -8 ), array('-staging','-main') ) || $installed_dir !== $target_dir ) {
                $from = WP_PLUGIN_DIR . '/' . $installed_dir;
                $to   = WP_PLUGIN_DIR . '/' . $target_dir;

                // If target exists, append a numeric suffix
                $attempt = 1;
                $final_to = $to;
                while ( file_exists( $final_to ) ) {
                    $final_to = $to . '-' . $attempt;
                    $attempt++;
                }

                if ( @rename( $from, $final_to ) ) {
                    $installed_dir = basename( $final_to );
                    loopis_elog_first_level( "Renamed plugin dir to $installed_dir" );
                } else {
                    loopis_elog_first_level( "Failed to rename plugin dir $installed_dir" );
                }
            }

            // Build plugin main file path and activate if present
            $plugin_file = $installed_dir . '/' . $slug . '.php';
            if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_file ) ) {
                activate_plugin( $plugin_file, '', is_multisite() );
                loopis_elog_first_level( "Activated: $slug!" );
            } else {
                loopis_elog_first_level( "Plugin main file not found: $plugin_file" );
            }
        }
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
