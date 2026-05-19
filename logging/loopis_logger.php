<?php 
/**
 * Logging functions so that all non specifics can be changed simultaneaously
 * 
 * @package LOOPIS_Config
 * @subpackage Dev-tools
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

function loopis_elog_get_timestamp(){
    if (function_exists('wp_date')) {
        return wp_date('Y-m-d H:i:s T');
    }

    return gmdate('Y-m-d H:i:s') . ' UTC';
}

function loopis_elog_write($message){
    error_log('[' . loopis_elog_get_timestamp() . '] ' . (string) $message);
}

function loopis_elog_function_start($function_handle){
    loopis_elog_write("Running: function {$function_handle} ...");
}

function loopis_elog_function_end_success($function_handle){
    loopis_elog_write("End: function {$function_handle} completed successfully!");
    error_log("");
}

function loopis_elog_function_end_failure($function_handle){
    loopis_elog_write("End: function {$function_handle} fatal failure!");
    error_log("");
}

function loopis_elog_first_level($message){
    loopis_elog_write("     {$message}");
}
function loopis_elog_second_level($message){
    loopis_elog_write("         {$message}");
}
