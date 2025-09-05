<?php
// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
} 
error_log('LOOPIS Config db-setup: Nu körs snart funktionen loopis_db_setup ');
require_once __DIR__ . '/table-loopis_lockers_create_update.php';
require_once __DIR__ . '/table-loopis_settings_create_update.php';
require_once __DIR__ . '/table-loopis_settings_insert.php';
require_once __DIR__ . '/loopis-insert-pages.php';

function loopis_db_setup() {

    error_log('LOOPIS Config db-setup: Nu körs funktionen loopis_db_setup ');

    // Create or update tables:
    loopis_lockers_create_update();
    loopis_settings_create_update();
    
    // Insert default data into tables:
    loopis_settings_insert();
    loopis_insert_pages();

}