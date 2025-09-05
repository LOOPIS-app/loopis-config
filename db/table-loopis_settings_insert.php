<?php
// This file makes insert of default data into the loopis_lockers table in the database

function loopis_settings_insert() {
    error_log('LOOPIS Config table-settings insert');

    global $wpdb;
    $table = $wpdb->prefix . 'loopis_settings';

    // Lägg till/ta bort rader enkelt här:
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

    foreach ($defaults as $key => $value) {
        // Om nyckeln finns, uppdatera. Om inte, skapa.
        $exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE setting_key = %s", $key));
        if ($exists) {
            $wpdb->update($table, ['setting_value' => $value], ['setting_key' => $key]);
        } else {
            $wpdb->insert($table, ['setting_key' => $key, 'setting_value' => $value]);
        }
    }
}