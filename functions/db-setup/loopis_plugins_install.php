<?php
/**
 * Function to install LOOPIS plugin dependencies.
 *
 * This function is called by main function 'loopis_db_setup'.
 * 
 * @package LOOPIS_Config
 * @subpackage Plugins
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

// Possibly necessary dependencies as WordPress does not always autoload the following functions
require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
require_once ABSPATH . 'wp-admin/includes/plugin-install.php';
require_once ABSPATH . 'wp-admin/includes/file.php';
require_once ABSPATH . 'wp-admin/includes/misc.php';

/**
 * Installs plugins in wp-content/plugins/
 * 
 * @return void
 */
function loopis_plugins_install(){
    loopis_elog_function_start('loopis_plugins_install');
    $slug = sanitize_text_field( $_POST['slug'] ?? '' );
    $main = sanitize_text_field( $_POST['main'] ?? '' );
    $plugin_dir = WP_PLUGIN_DIR . '/' . $slug;



    // Helps avoid internal installer wp_die
    if (!class_exists('Loopis_Skin')) {
        class Loopis_Skin extends WP_Upgrader_Skin {
            public function header() {}
            public function footer() {}
            public function feedback($string, ...$args) {}
            public function error($errors) {}
            public function before() {}
            public function after() {}
        }
    }
    // Get upgrader
    $upgrader = new Plugin_Upgrader( new Loopis_Skin() );

    // Skip if already installed
    if ( file_exists( $plugin_dir ) ) {
        loopis_elog_function_end_success('loopis_plugins_install');
        wp_send_json_success([ 'slug' => $slug, 'status' => 'Already installed' ]);
    }

    // Fetch download link
    $api = plugins_api( 'plugin_information', [
        'slug'   => $slug,
        'fields' => [ 'sections' => false ],
    ] );

    if ( is_wp_error( $api ) || empty( $api->download_link ) ) {
        loopis_elog_function_end_failure('loopis_plugins_install');
        wp_send_json_error([ 'slug' => $slug, 'status' => 'Failed to get API info' ]);
    }

    // Install plugin
    loopis_elog_first_level("Installing plugin: {$slug}");
    $result = $upgrader->install( $api->download_link );

    if ( is_wp_error( $result ) ) {
        loopis_elog_function_end_failure('loopis_plugins_install');
        wp_send_json_error([ 'slug' => $slug, 'status' => $result->get_error_message() ]);
    }

    loopis_elog_function_end_success('loopis_plugins_install');
    wp_send_json_success([ 'slug' => $slug, 'status' => 'Installed successfully' ]);
}

/**
 * Activates plugins installed by in wp-content/plugins/
 * 
 * @return void
 */
 
function loopis_plugins_activate(){
    loopis_elog_function_start('loopis_plugins_activate');
    // Plugin list
    $plugins = [
        [
            'slug' => 'post-smtp',
            'main' => 'post-smtp/postman-smtp.php',
            'Unit' => 'Post SMTP',
        ],
        [
            'slug' => 'wp-statistics',
            'main' => 'wp-statistics/wp-statistics.php',
            'Unit' => 'WP Statistics',
        ],
        [
            'slug' => 'wp-user-manager',
            'main' => 'wp-user-manager/wp-user-manager.php',
            'Unit' => 'WP User Manager',
        ],
        [
            'slug' => 'ewww-image-optimizer',
            'main' => 'ewww-image-optimizer/ewww-image-optimizer.php',
            'Unit' => 'EWWW Image Optimizer',
        ],
    ];

    foreach ($plugins as $plugin){
        if (file_exists(WP_PLUGIN_DIR . '/' . $plugin['main'])){
            if (!is_plugin_active($plugin['slug'])) {
                loopis_elog_first_level("activating plugin: {$plugin['slug']}");
                activate_plugin($plugin['main'], $silent = true );
                loopis_config_update(['Unit' => $plugin['Unit']], 
                    ['Config_Status' => 'Ok',
                    'Config_Version' => LOOPIS_CONFIG_VERSION]);
            }
        } else{
            loopis_config_update(['Unit' => $plugin['Unit']], 
                    ['Config_Status' => 'Error',
                    'Config_Version' => LOOPIS_CONFIG_VERSION]);
        }
    }
    //Redirect killers
    delete_transient('_wpum_activation_redirect');
    delete_transient( 'fs_plugin_post-smtp_activated' );
    delete_transient( 'fs_post_smtp_activated' );
    loopis_elog_function_end_success('loopis_plugins_activate');
}

function get_plugin_version( $plugin_slug ) {
    $plugins = get_plugins();
    foreach ( $plugins as $path => $data ) {
        if ( strpos( $path, $plugin_slug ) !== false ) {
            return $data['Version'];
        }
    }
    return false;
}
    