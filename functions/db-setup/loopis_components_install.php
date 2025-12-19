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
 * Installs plugins and themes in wp-content
 * 
 * @return void
 */
function loopis_components_install(){
    loopis_elog_function_start('loopis_components_install');
    // Loopis repositories' zip-clone-links
    $repos = array(
        'loopis-develooper' => 'https://github.com/LOOPIS-app/loopis-develooper/archive/refs/heads/main.zip',
        'loopis-admin' => 'https://github.com/LOOPIS-app/loopis-admin/archive/refs/heads/main.zip',
    );
    // Get upgrader
    $upgrader = new Plugin_Upgrader( new Automatic_Upgrader_Skin() );

    foreach ( $repos as $slug => $zip_url ) {
        $result = $upgrader->install( $zip_url ); // Install
        if ( is_wp_error( $result ) ) {
            loopis_elog_first_level( "Failed installing $slug" );
            continue;
        }
        $plugin_slug = $slug . '-main/' . $slug . '.php'; // if installed then activate
        if ( file_exists( WP_PLUGIN_DIR . '/' . $plugin_slug ) ) {
            activate_plugin( $plugin_slug );
        }

    }
    loopis_elog_function_end_success('loopis_components_install');
}

/**
 * Delete default themes
 * 
 * @return void
 */
function loopis_themes_configure() {
    loopis_elog_function_start('loopis_themes_configure');

    $theme = array(
        'name' => 'loopis-theme',
        'zip_url' => 'https://github.com/LOOPIS-app/loopis-theme/archive/refs/heads/main.zip',
    );

    $theme_zip = download_url( $theme['zip_url'] ); // Download theme
    if ( ! is_wp_error( $theme_zip ) ) {
        $result = unzip_file( $theme_zip, get_theme_root() ); // Install theme
        loopis_elog_first_level( "Installed theme: {$theme['name']}" );
        if ( file_exists( $theme_zip ) ) {
            if ( unlink( $theme_zip ) ) { // if the zip file exists delete
            } else {
                loopis_elog_first_level( "Failed to delete temp zip: {$theme_zip}" );
            }
        }
        if ( ! is_wp_error( $result ) ) { // If install worked, activate loopis thrmr
            $theme_folder = $theme['name'] . '-main';
            switch_theme( $theme_folder );
            loopis_elog_first_level( "Activated theme: {$theme['name']}" );
        }
    }

    // List of all themes
    $default_themes = [];
    // Get Default themes
    foreach (wp_get_themes() as $slug => $theme) {
        if (preg_match('/^twenty/', $slug)) {
            $default_themes[] = $slug;
        }
    }

    foreach ($default_themes as $theme) {
        if (wp_get_theme($theme)->exists()) {  // Delete theme if it exixtst an is not active
            if (get_stylesheet() !== $theme) {
                delete_theme($theme);
                loopis_elog_first_level( "Deleted theme: {$theme}" );
            }
        }
    }
    
    loopis_elog_function_end_success('loopis_themes_configure');
}