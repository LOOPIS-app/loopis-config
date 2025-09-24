<?php
/**
 * Function to delete unused default plugins.
 *
 * This function is called by main function 'loopis_db_setup'.
 * 
 * @package LOOPIS_Config
 * @subpackage Database
 * 
 */


if (!defined('ABSPATH')) { 
    exit; 
}

require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/misc.php';


function loopis_plugins_install(){
    error_log('Running function loopis_plugins_install...');

    $plugins = [
        [
            'slug' => 'post-smtp',
            'main' => 'post-smtp/postman-smtp.php',
        ],
        [
            'slug' => 'wp-statistics',
            'main' => 'wp-statistics/wp-statistics.php',
        ],
        [
            'slug' => 'wp-user-manager',
            'main' => 'wp-user-manager/wp-user-manager.php',
        ],
        [
            'slug' => 'ewww-image-optimizer',
            'main' => 'ewww-image-optimizer/ewww-image-optimizer.php',
        ],
    ];

    // Helps avoid interal installer wp_die
    if (!class_exists('Loopis_Silent_Skin')) {
        class Loopis_Skin extends WP_Upgrader_Skin {
            public function header() {}
            public function footer() {}
            public function feedback($string, ...$args) {}
            public function error($errors) {}
            public function before() {}
            public function after() {}
        }
    }

    foreach ( $plugins as $plugin ) {
        
        // Redefinitions because its good practice
        $slug     = $plugin['slug'];
        $main     = $plugin['main'];
        $plugin_dir = WP_PLUGIN_DIR . '/' . $slug;

        error_log("INSTALLING: {$slug}...");

        // Is it running already?
        if ( ! is_plugin_active( $main ) ) {

            // Does it exist already?
            if ( ! file_exists( $plugin_dir ) ) {

                // Get download link from WordPress
                $api = plugins_api( 'plugin_information', [
                    'slug'   => $slug,
                    'fields' => [ 'sections' => false ], // No extra stuff like readmes etc.
                ] );

                // Could we get the wordpress download link?
                if ( ! is_wp_error( $api ) && isset( $api->download_link ) ) {
                    // Get upgrader tool
                    $upgrader = new Plugin_Upgrader( new Loopis_Skin() );
                    $result = $upgrader->install( $api->download_link );
                
                }
            }
        }
    }

}


