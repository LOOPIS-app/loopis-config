<?php
// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
} 
require_once __DIR__ . '/loopis_lockers_create_update.php';
require_once __DIR__ . '/loopis_settings_create_update.php';
require_once __DIR__ . '/loopis_settings_insert.php';
require_once __DIR__ . '/loopis_pages_create_delete.php';
require_once __DIR__ . '/loopis_options_change.php';
require_once __DIR__ . '/loopis_tags_insert_delete.php';

function loopis_db_setup() {

    error_log('LOOPIS Config db-setup started...');

    // Create or update tables:
    loopis_lockers_create_update();
    loopis_settings_create_update();
    
    // Insert default data into tables:
    loopis_settings_insert();
    loopis_pages_create();
    loopis_options_change();
    loopis_tags_insert();     

}