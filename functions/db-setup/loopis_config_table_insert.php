<?php
/**
 * Function to insert LOOPIS_CONFIG in the WordPress database.
 *
 *  This is ran as an activation hook
 * 
 * @package LOOPIS_Config
 * @subpackage Database
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}
/**
 * Create table 'wp_loopis_config'
 * 
 * @return void
 */
function loopis_config_table_insert() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'loopis_config';

    $table_exists = $wpdb->get_var( $wpdb->prepare(
        "SHOW TABLES LIKE %s", 
        $table_name
    ) );

    if ( $table_exists != $table_name ) {

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            ID mediumint(9) NOT NULL AUTO_INCREMENT,
            Unit varchar(100) NOT NULL,
            Place varchar(100) NOT NULL,
            Category varchar(50) DEFAULT 'Install' NOT NULL,
            Config_Status varchar(50) DEFAULT 'Nan' NOT NULL,
            Config_Version VARCHAR(10) DEFAULT '0.0.0' NOT NULL,
            Config_function varchar(100) DEFAULT '' NOT NULL,
            Config_Data LONGTEXT DEFAULT NULL,
            PRIMARY KEY  (ID)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

    }


    // Define the pages to create
    $rows_to_create =array(
        array(
            'Unit' => 'LOOPIS config',
            'Place' => 'wp_loopis_config',
            'Config_Status' => 'Ok',
            'Config_Version' => LOOPIS_CONFIG_VERSION,
            'Config_function' => '',
            'Category' => 'Initialization'
        ),
        array(
            'Unit' => 'LOOPIS settings',
            'Place' => 'wp_loopis_settings',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_settings_create',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'LOOPIS lockers',
            'Place' => 'wp_loopis_lockers',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_settings_insert',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'LOOPIS pages',
            'Place' => 'wp_posts',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_lockers_create',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'LOOPIS categories',
            'Place' => 'wp_terms',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_pages_insert',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'LOOPIS tags',
            'Place' => 'wp_terms',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_cats_insert',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'LOOPIS roles',
            'Place' => 'wp_options',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_tags_insert',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'LOOPIS admins',
            'Place' => 'wp_users',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_roles_set',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'Delete default WP plugins',
            'Place' => 'Plugins',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_admins_insert',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'WordPress root files',
            'Place' => 'Server',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_root_files_copy',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'WordPress options',
            'Place' => 'wp_options',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_wp_screen_options_set',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'WordPress admin screen options',
            'Place' => 'wp_usermeta',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_wp_options_set',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'Loopis Components',
            'Place' => 'wp_content',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_components_install',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'Loopis Themes',
            'Place' => 'wp_content',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_themes_configure',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'Post SMTP',
            'Place' => 'wp_content',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => '',
            'Category' => 'Component',
            'Config_Data' => json_encode([
                'slug' => 'post-smtp',
                'main' => 'post-smtp/postman-smtp.php'
                ])
        ),
        array(
            'Unit' => 'WP Statistics',
            'Place' => 'wp_content',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => '',
            'Category' => 'Component',
            'Config_Data' => json_encode([
                'slug' => 'wp-statistics',
                'main' => 'wp-statistics/wp-statistics.php'
                ])
        ),
        array(
            'Unit' => 'WP User Manager',
            'Place' => 'wp_content',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => '',
            'Category' => 'Component',
            'Config_Data' => json_encode([
                'slug' => 'wp-user-manager',
                'main' => 'wp-user-manager/wp-user-manager.php'
                ])
        ),
        array(
            'Unit' => 'EWWW Image Optimizer',
            'Place' => 'wp_content',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => '',
            'Category' => 'Component',
            'Config_Data' => json_encode([
                'slug' => 'ewww-image-optimizer',
                'main' => 'ewww-image-optimizer/ewww-image-optimizer.php'
                ])
        ),
    );

    foreach ($rows_to_create as $row) {
        $exists = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $table_name WHERE Unit = %s AND Place = %s",
            $row['Unit'], $row['Place']
        ) );

        if ( $exists == 0 ) {
            $wpdb->insert( $table_name, $row );
        }
    }

}