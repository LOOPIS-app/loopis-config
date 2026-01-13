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
 * Table truth for 'wp_loopis_config'
 * 
 * @return array<int,array{
 *     Unit: string,
 *     Place: string,
 *     Category: string,
 *     Config_Status: string,
 *     Config_Version: string,
 *     Config_function: string,
 *     Config_Data?: string,
 * }>
 */
 function loopis_config_get_table(){
    return array(
        array(
            'Unit' => 'LOOPIS config',
            'Place' => 'wp_loopis_config',
            'Config_Status' => 'Ok',
            'Config_Version' => LOOPIS_CONFIG_VERSION,
            'Config_function' => '',
            'Category' => 'Initialization'
        ),
        array(
            'Unit' => 'LOOPIS settings table',
            'Place' => 'wp_loopis_settings',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_settings_create',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'LOOPIS settings',
            'Place' => 'wp_loopis_settings',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_settings_insert',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'LOOPIS lockers',
            'Place' => 'wp_loopis_lockers',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_lockers_create',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'LOOPIS pages',
            'Place' => 'wp_posts',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_pages_insert',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'LOOPIS categories',
            'Place' => 'wp_terms',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_cats_insert',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'LOOPIS tags',
            'Place' => 'wp_terms',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_tags_insert',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'LOOPIS roles',
            'Place' => 'wp_options',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_roles_set',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'LOOPIS admins',
            'Place' => 'wp_users',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_admins_insert',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'Delete default WP plugins',
            'Place' => 'Plugins',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_wp_plugins_delete',
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
            'Config_function' => 'loopis_wp_options_set',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'WordPress admin screen options',
            'Place' => 'wp_usermeta',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_wp_screen_options_set',
            'Category' => 'Install'
        ),
        array(
            'Unit' => 'Loopis Admin',
            'Place' => 'wp_content',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_components_install',
            'Category' => 'Install',
            'Config_Data' => json_encode([
                'slug' => 'loopis-admin',
                'zip_url' => 'https://github.com/LOOPIS-app/loopis-admin/archive/refs/heads/main.zip',
                'ver_str' => 'LOOPIS_ADMIN_VERSION',
                ])
        ),
        array(
            'Unit' => 'Loopis Develooper',
            'Place' => 'wp_content',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_components_install',
            'Category' => 'Install',
            'Config_Data' => json_encode([
                'slug' => 'loopis-develooper',
                'zip_url' => 'https://github.com/LOOPIS-app/loopis-develooper/archive/refs/heads/main.zip',
                'ver_str' => 'LOOPIS_DEVELOOPER_VERSION',
                ])
        ),
        array(
            'Unit' => 'Loopis Themes',
            'Place' => 'wp_content',
            'Config_Status' => 'Nan',
            'Config_Version' => '0.0.0',
            'Config_function' => 'loopis_themes_configure',
            'Category' => 'Install',
            'Config_Data' => json_encode([
                'slug' => 'loopis-theme',
                'zip_url' => 'https://github.com/LOOPIS-app/loopis-theme/archive/refs/heads/main.zip',
                'ver_str' => 'LOOPIS_THEME_VERSION',
                ])
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
}

/**
 * Generate a unique hash key for a row based on Unit and Place.
 *
 * Used to compare expected rows with existing rows.
 *
 * @param array $row The row to generate a key for.
 * @return string MD5 hash of 'Unit|Place'
 */
function loopis_config_row_key(array $row): string {
    return md5($row['Unit'] . '|' . $row['Place']);
}

/**
 * Reconcile or fill table based on table truth
 * 
 * @return void
 */
function loopis_config_reconcile_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'loopis_config';
    $rows = loopis_config_get_table();
    $expected = [];
    // Make temp unique key for the expected table
    foreach ($rows as $row) {
        $key = loopis_config_row_key($row);
        $row['_loopis_key'] = $key;
        $expected[$key] = $row;
    }
    $existing = $wpdb->get_results(
        "SELECT * FROM $table",
        ARRAY_A
    );
    // Make temp keys for current
    $existing_map = [];
    foreach ($existing as $row) {
        $key = loopis_config_row_key($row);
        $existing_map[$key] = $row;
    }

    // For each expected
    foreach ($expected as $key => $row) {
        unset($row['_loopis_key']);
        // Check current
        if (!isset($existing_map[$key])) {
            // Insert new
            $wpdb->insert($table, $row);
        } else {
            // Update survivors
            $existing = $existing_map[$key];
            $changes  = loopis_row_comparison($row, $existing);
            // Only update if there are changes
            if (!empty($changes)) {
                $wpdb->update(
                    $table,
                    $changes,               
                    ['ID' => $existing['ID']]
                );
            }        
        }
    }
    //  Remove obsolete rows
    foreach ($existing_map as $key => $row) {
        if (!isset($expected[$key])) {
            $wpdb->delete($table, ['ID' => $row['ID']]);
        }
    }
    // Now the table is up to date
    $wpdb->update(
        $table,
        ['Config_Version' => LOOPIS_CONFIG_VERSION],
        ['Unit'  => 'LOOPIS config',
        'Place' => 'wp_loopis_config',],
        ['%s'],
        ['%s', '%s']
    );
}

/**
 * Compares rows, returns array of altered elements
 * * Excludes ID, Config_Status, and Config_Version from comparison.
 *
 * @param array $expected The expected row values.
 * @param array $existing The current database row values.
 * @return array<string,mixed> Columns and values that need updating.
 */
function loopis_row_comparison(array $expected, array $existing): array {
    $diff = [];

    // Columns we never want to update automatically
    $exclude = ['ID', 'Config_Version', 'Config_Status'];

    foreach ($expected as $column => $value) {
        if (in_array($column, $exclude, true)) {
            continue; // skip these columns
        }

        // Column not present in DB row
        if (!array_key_exists($column, $existing)) {
            continue;
        }

        // Normalize NULLs & strings
        $old = (string) ($existing[$column] ?? '');
        $new = (string) ($value ?? '');

        if ($old !== $new) {
            $diff[$column] = $value;
        }
    }

    return $diff;
}