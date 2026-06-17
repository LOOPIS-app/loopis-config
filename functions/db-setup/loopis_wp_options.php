<?php
/**
 * Function to set the default WordPress settings in the 'wp_options' table.
 *
 * This function is called by main function 'loopis_db_setup'.
 *
 * @package LOOPIS_Config
 * @subpackage Database
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

/**
 * Set options in 'wp_options'
 * 
 * @return void
 */
function loopis_wp_options_set() {
    loopis_elog_function_start('loopis_wp_options_set');
    
    // Get page IDs by slug
    $home_page_id = get_page_by_path('start');
    $blog_page_id = get_page_by_path('gifts');
    $privacy_page_id = get_page_by_path('privacy');
    $gosmtp_options = array(
        'mailer' => array(
            0 => array(
              'mail_type' => 'smtp',
              'backup_connection' => '',
              'smtp_host' => 'mail.loopa.se',
              'encryption' => 'tls',
              'smtp_port' => '587',
              'smtp_auth' => 'Yes',
              'smtp_username' => 'admin@loopa.se',
              'smtp_password' => '',
              'disable_ssl_verification' => '',
            ),
        ),
        'from_email' => 'admin@loopa.se',
        'force_from_email' => 1,
        'from_name' => 'LOOPIS',
        'force_from_name' => 1,
        'return_path' => '',
        'logs' => array(
            'enable_logs' => 1,
            'log_attachments' => '',
            'retention_period' => '2628000',
            'log_columns' => array(
                'from' => 'on',
                'to' => 'on',
                'source' => 'on',
                'provider' => 'on',
            ),
        ),
        'weekly_reports' => array(
            'enable_weekly_reports' => '',
            'weekday' => '',
            'timestamp' => '',
        ),
    );
    $ewww_options = array(
        'medium_large' => true,
        '1536x1536' => true,
        '2048x2048' => true,
        'pdf-full' => true,
    );
    // Define the options
    $wp_options = array(
        'blogdescription'       => 'Ge & få saker i ditt grannskap.',
        'admin_email'           => 'admin@loopis.app',
        'users_can_register'    => '1',
        'default_role'          => 'member_pending',
        'posts_per_page'        => '50',
        'date_format'           => 'Y-m-d',
        'time_format'           => 'H:i',
        'permalink_structure'   => '/%postname%/',
        'comment_registration'  => '1',
        'show_on_front'         => 'page',
        'page_on_front'         => $home_page_id ? $home_page_id->ID : 0,
        'page_for_posts'        => $blog_page_id ? $blog_page_id->ID : 0,
        'wp_page_for_privacy_policy' => $privacy_page_id ? $privacy_page_id->ID : 0,
        'thumbnail_size_w'      => '240',
        'thumbnail_size_h'      => '240',
        'large_size_w'          => '1920',
        'large_size_h'          => '1920',
        'thread_comments_depth' => '2',
        'comment_order'         => 'desc',
        'timezone_string'       => 'Europe/Stockholm',
        'WPLANG'                => 'sv_SE',
        'auto_update_core_major'=> 'disabled',
        'loopis_config_version' => LOOPIS_CONFIG_VERSION,
        'fresh_site'            => '0',
        'upload_size_limit'     => 10000,     
        'gosmtp_options'        => $gosmtp_options,
        'ewww_image_optimizer_disable_resizes' => $ewww_options,
        'medium_size_w'           => 0,
        'medium_size_h'           => 0,
    );

    if (is_multisite() && (get_current_blog_id()===1)){
        $wp_options['blogname'] = 'LOOPIS';
        $wp_options['site_title'] = 'LOOPIS';    
        $wp_options['network_title'] = 'LOOPIS network'; 
        update_site_option( 'registration', 'user' );
        update_site_option( 'add_new_users', 1 );
        update_site_option( 'fileupload_maxk', 10000 ); 
        update_site_option( 'site_name', 'LOOPIS' );   
    }
    // Set the options
    foreach ($wp_options as $option_name => $option_value) {
        update_option($option_name, $option_value);
    }
    loopis_elog_function_end_success('loopis_wp_options_set');
}

