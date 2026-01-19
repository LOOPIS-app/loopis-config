<?php
/**
 * Function to create (or update) the custom database table 'loopis_settings' as well as 
 *  a function to insert default settings into the custom table 'loopis_settings'.
 *
 * These functions are called in the install function implemented on the config page.
 * 
 * Change the table structure or settings here as needed, then reinstall.
 * Do not change the table name or column names, as that may break existing installations.
 * Do not change the column names, as that may break existing installations.
 *
 * @package LOOPIS_Config
 * @subpackage Database
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
} 

/**
 * Create (or update) table 'loopis_settings'
 * 
 * @return void
 */
function loopis_settings_create() {
    loopis_elog_function_start('loopis_settings_create');

    // Access WordPress database object
    global $wpdb;

    // Define table name with WordPress prefix
    $table = $wpdb->prefix . 'loopis_settings';
    $charset_collate = $wpdb->get_charset_collate();

    // Include WordPress database upgrade functions
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Create the table (or update if columns are missing)
    $sql = "CREATE TABLE $table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        setting_key varchar(64) NOT NULL,
        setting_value longtext NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY setting_key (setting_key)
    ) $charset_collate;";

    dbDelta($sql);
    loopis_elog_function_end_success('loopis_settings_create');
}

/**
 * Insert values into 'loopis_settings'
 * 
 * @return void
 */
function loopis_settings_insert() {
    loopis_elog_function_start('loopis_settings_insert');

    // Access WordPress database object
    global $wpdb;

    // Define table name with WordPress prefix
    $table = $wpdb->prefix . 'loopis_settings';

    // Add or remove records here:
    $defaults = [
        'welcome_email_subject' => '💚 Välkommen!',
        'welcome_email_greeting' => 'Hej [user_first_name]!',
        'welcome_email_message' => '<div style="padding: 10px;text-align: center;font-size: 18px;background: #f5f5f5;border-radius: 10px"><p>🎉 Ditt konto på LOOPIS.app är nu aktiverat.<br>→  Logga in med din vanliga webbläsare.</p><p><a href="/faq/tips-till-ny-medlem/">📌 Tips till ny medlem!</a></p></div>',
        'welcome_email_footer' => '<table style="border-collapse: collapse"><tr><td style="vertical-align: middle;padding-right: 5px"><img src="/wp-content/images/LOOPIS_icon.png" alt="LOOPIS_logo" style="height: 32px"></td><td style="vertical-align: middle;width: 275px"><p style="font-size: 11px;font-style: italic;margin: 0;line-height: 1.2">Information från <a href="/">LOOPIS.app</a> <br> angående ditt användarkonto.</p></td></tr></table>',
        'locker_fetch_warning' => '⚠ Det är mycket saker i skåpen just nu! <br>🐎 Hämta gärna så snabbt som möjligt.',
        'locker_leave_warning' => '⚠ Det är mycket saker i skåpen just nu! <br>🐌 Vänta gärna någon dag med att lämna.',
        'event_name' => '🛸 LOOPIS HQ',
        'event_name_history' => serialize(['🌳 LOOPIS på torget', '🛸 LOOPIS HQ']),
    ];

    // Insert or update each default setting
    foreach ($defaults as $key => $value) {
        // If the key exists, update it, otherwise insert it.
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE setting_key = %s", $key));
        if ($exists) {
            $wpdb->update($table, ['setting_value' => $value], ['setting_key' => $key]);
        } else {
            $wpdb->insert($table, ['setting_key' => $key, 'setting_value' => $value]);
        }
    }
    loopis_elog_function_end_success('loopis_settings_insert');
}