<?php
error_log('LOOPIS Config db-setup: Nu körs snart funktionen loopis_db_setup ');
require_once __DIR__ . '/table-loopis_lockers_create_update.php';
require_once __DIR__ . '/table-loopis_settings_create_update.php';
require_once __DIR__ . '/table-loopis_settings_insert.php';

function loopis_db_setup() {
    error_log('LOOPIS Config db-setup: Nu körs funktionen loopis_db_setup ');

    // Skapa eller uppdatera tabeller:
    loopis_lockers_create_update();
    loopis_settings_create_update();
    
    // Fyll i information i tabellerna:
    loopis_settings_insert();
}