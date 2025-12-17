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
    // Plugin list get
    $config = wp_cache_get('loopis_config_data', 'loopis');
    $table_preinstall = array_filter($config, fn($r) => $r['Category'] === 'Component');
    $preinstall_data = [];
    foreach ($table_preinstall as $row) {
        $preinstall_data[] = array_merge(['ID' => $row['ID']], json_decode($row['Config_Data'], true));
    }

    foreach ($preinstall_data as $plugin){
        if (file_exists(WP_PLUGIN_DIR . '/' . $plugin['main'])){
            if (!is_plugin_active($plugin['slug'])) {
                loopis_elog_first_level("activating plugin: {$plugin['slug']}");
                activate_plugin($plugin['main'], $silent = true );
                loopis_config_update(['ID' => $plugin['ID']], 
                    ['Config_Status' => 'Ok',
                    'Config_Version' => LOOPIS_CONFIG_VERSION]);
            }
        } else{
            loopis_config_update(['ID' => $plugin['ID']], 
                    ['Config_Status' => 'Error',
                    'Config_Version' => LOOPIS_CONFIG_VERSION]);
        }
    }

    // Redirect killers
    delete_transient('_wpum_activation_redirect');
    delete_transient( 'fs_plugin_post-smtp_activated' );
    delete_transient( 'fs_post_smtp_activated' );

    // Set plugin specs in wp_options
    loopis_plugin_options_set();
    loopis_elog_function_end_success('loopis_plugins_activate');
}

/**
 * Alters options for dependencies
 * 
 * @return void
 */
function loopis_plugin_options_set(){
    // Options to set

    loopis_elog_first_level("Setting plugin options:");
    
    $wp_options = array(

        'ewww_image_optimizer_delete_originals' => 1,
        'ewww_image_optimizer_maxmediaheight'   => 1920,
        'ewww_image_optimizer_maxmediawidth'    => 1920,
        'wpum_email' => array(

            'registration_confirmation' => array(
                'title'   => 'Hej {firstname}!',
                'subject' => '🔔 Verifiera din e-postadress',
                'content' => '<p>Tryck på länken för att verifiera e-postadress:</p>
                              <p>{verification_link}</p>
                              <p>💡 När vi har kontrollerat din medlemsavgift får du ett mail och kan börja loopa!</p>
                              <p><strong><br />Ditt inlogg:</strong><br /><strong>📧</strong> {email}
                              <strong><br />🔑 </strong>{password}</p>',
                'footer'  => '<a href="{siteurl}">{sitename}</a>',
            ),

            'registration_admin_notification' => array(
                'title'   => 'Hej admin!',
                'subject' => '🥳 Ny användare!',
                'content' => '<p>{sitename} har fått en ny användare!</p>
                              <p><strong>Förnamn:</strong> {firstname}</p>
                              <p><strong>Efternamn:</strong> {lastname}</p>
                              <p><strong>E-postadress:</strong> {email}</p>',
            ),

            'password_recovery_request' => array(
                'title'   => 'Hej {firstname}!',
                'subject' => '🛠 Byta lösenord?',
                'content' => '<p>Tryck på länken för att byta lösenord på {sitename}:</p>
                              <p>{recovery_url}</p>
                              <p>💡 Vill du inte byta lösenord? Ignorera detta mail.</p>',
                'footer'  => '<a href="{siteurl}">{sitename}</a>',
            ),
        ),
    );

    // Set the options (this will serialize the arrays in wpum email like the previous entry)
    foreach ($wp_options as $option_name => $option_value) {
        loopis_elog_second_level("Setting option: {$option_name}!");
        update_option($option_name, $option_value);
    }
    loopis_elog_first_level("Options set!");
}

/**
 * A version getter incase we want to include internal version data
 * 
 * @return void
 */
function loopis_get_plugin_version( $plugin_slug ) {
    $plugins = get_plugins();
    foreach ( $plugins as $path => $data ) {
        if ( strpos( $path, $plugin_slug ) !== false ) {
            return $data['Version'];
        }
    }
    return false;
}
    