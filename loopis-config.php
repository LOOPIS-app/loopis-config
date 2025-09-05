<?php
/*
Plugin Name: LOOPIS Config
Version: 0.1
Author: develoopers Johan*2
Author URI: https://loopis.org
Description: Plugin for configuring a clean WP installation for LOOPIS.app
*/

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

error_log("trace 1 Loopis Config: ==================================================================");

require_once __DIR__ . '/db/db-setup.php';

register_activation_hook(__FILE__, 'loopis_db_setup');

error_log("trace END of Loopis Config: ==================================================================");

