<?php 
/**
 * Logging functions so that all non specifics can be changed simultaneaously
 * 
 * @package LOOPIS_Config
 * @subpackage Error logging functions
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

function loopis_elog_function_start($function_handle){
    error_log("Running: function {$function_handle} ...");
}

function loopis_elog_function_end_success($function_handle){
    error_log("End: function {$function_handle} completed successfully!");
    error_log("");
}

function loopis_elog_function_end_failure($function_handle){
    error_log("End: function {$function_handle} fatal failure!");
    error_log("");
}

function loopis_elog_first_level($message){
    error_log("     {$message}");
}
function loopis_elog_second_level($message){
    error_log("         {$message}");
}
