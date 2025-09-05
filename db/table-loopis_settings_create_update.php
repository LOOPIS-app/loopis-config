<?php
// This file creates or updates the loopis_settings table in the database
// - If you want to change the table structure, do it here and then reactivate the plugin
// - Do not change the table name or column names, as that may break existing installations 

function loopis_settings_create_update() {
    error_log('LOOPIS Config table_settings create or update');

    global $wpdb;
    $table = $wpdb->prefix . 'loopis_settings';
    $charset_collate = $wpdb->get_charset_collate();

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Skapa tabellen (eller uppdatera om kolumner saknas)
    $sql = "CREATE TABLE $table (
        id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
        setting_key varchar(64) NOT NULL,
        setting_value longtext NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY setting_key (setting_key)
    ) $charset_collate;";

    dbDelta($sql);
}