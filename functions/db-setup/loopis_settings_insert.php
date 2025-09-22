<?php
/**
 * Function to insert default settings into the 'loopis_settings' table.
 *
 * This function is called by main function 'loopis_db_setup'.
 * 
 * Modify the default settings here if needed, then reactivate the plugin.
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
 * Insert values into 'loopis_settings'
 * 
 * @return void
 */
function loopis_settings_insert() {
    error_log('Running function loopis_settings_insert...');

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
}