<?php
/**
 * This file filters error reporting levels for the whole site!
 * 
 * Why filter error reporting?
 * Primarily to prevent the WPUM plugin from flooding debug.log 
 * 
 * Why in the folder mu-plugins?
 * Because it is must-load so it runs early and reliably,
 * and it is not a plugin so it cannot be deactivated by accident.
 * 
 * What still shows in debug.log?
 * - Errors: E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR
 * - Warnings: E_WARNING, E_CORE_WARNING, E_COMPILE_WARNING, E_USER_WARNING
 * - Recoverable errors: E_RECOVERABLE_ERROR
 * - Strict standards: E_STRICT
 * - Notices: E_NOTICE
 *
 * What did we want to hide from debug.log?
 * E_DEPRECATED: All the deprecated PHP functions used by WPUM 
 * E_USER_NOTICE: "Function _load_textdomain_just_in_time was called incorrectly"
 *
 */
error_reporting(E_ALL & ~E_DEPRECATED & ~E_USER_NOTICE);