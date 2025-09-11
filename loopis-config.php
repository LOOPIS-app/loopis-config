<?php
/*
Plugin Name: LOOPIS Config
Version: 0.2
Author: LOOPIS Developers
Author URI: https://loopis.org
Description: Plugin for configuring a clean WP installation for LOOPIS.app
*/

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

error_log("Start of Loopis Config activation: ==================================================================");

require_once __DIR__ . '/db/loopis_db_setup.php';

register_activation_hook(__FILE__, 'loopis_db_setup');

error_log("End of Loopis Config: ==================================================================");


