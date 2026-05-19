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
        'site_language'         => 'svenska',
        // Add more options as needed
    );

    if (is_multisite() && (get_current_blog_id()===1)){
        $wp_options['blogname'] = 'LOOPIS HQ';
        $wp_options['site_title'] = 'LOOPIS HQ';    
    }
    loopis_set_site_icon();
    // Set the options
    foreach ($wp_options as $option_name => $option_value) {
        update_option($option_name, $option_value);
    }
    loopis_elog_function_end_success('loopis_wp_options_set');
}


function loopis_set_site_icon() {
    if ( get_option( 'site_icon' ) ) {
        return; 
    }

    $src_file_path = LOOPIS_CONFIG_DIR . '/assets/img/site-icon.png';

    $file_path = WP_CONTENT_DIR . '/uploads/site-icon.png'; 

    if ( ! file_exists( $src_file_path ) && ! file_exists( $file_path ) ) {
        return;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    if (! file_exists( $file_path ) ) {
        $file_contents = file_get_contents( $src_file_path );

        if ( false === $file_contents ) {
            return;
        }

        $upload = wp_upload_bits(
            'site-icon.png',
            null,
            $file_contents
        );

        if ( ! empty( $upload['error'] ) ) {
            return;
        }
    }else{

        if ( ! is_readable( $file_path ) ) {
            return;
        }

        $filetype = wp_check_filetype($file_path);

        if ( empty( $filetype['type'] ) ) {
            return;
        }

        $upload = array(
            'file'      => $file_path,                 
            'type'      => $filetype['type'],
            'error'     => '',
        );
    }


    // Prepare attachment.
    $attachment = array(
        'post_mime_type' => wp_check_filetype( $upload['file'] )['type'],
        'post_title'     => sanitize_file_name( pathinfo( $upload['file'], PATHINFO_FILENAME ) ),
        'post_content'   => '',
        'post_status'    => 'inherit',
    );

    // Insert attachment.
    $attach_id = wp_insert_attachment(
        $attachment,
        $upload['file']
    );

    if ( is_wp_error( $attach_id ) || ! $attach_id ) {
        return;
    }

    // Generate metadata.
    $attach_data = wp_generate_attachment_metadata(
        $attach_id,
        $upload['file']
    );

    wp_update_attachment_metadata(
        $attach_id,
        $attach_data
    );

    // Set as site icon
    update_option( 'site_icon', (int) $attach_id );
}
