<?php
/**
 * Functions to copy root files into WordPress installation.
 *
 * This function is called by main function 'loopis_db_setup'.
 *
 * @package LOOPIS_Config
 * @subpackage Database
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
} 

/**
 * Recursively copy files and directories
 * Works for nested directories
 * Will overwrite existing files with the same name
 *
 * @param string $source Source directory path
 * @param string $dest Destination directory path
 * @return bool True on success, false on failure
 */
function loopis_recursive_copy($source, $dest) {
    // Check if source exists
    if (!file_exists($source)) {
        loopis_elog_error("Source does not exist: {$source}");
        return false;
    }

    // If source is a file, copy it directly
    if (is_file($source)) {
        if (!copy($source, $dest)) {
            loopis_elog_error("Failed to copy file: {$source} to {$dest}");
            return false;
        }
        loopis_elog_first_level("Copied file: " . basename($source));
        return true;
    }

    // Create destination directory if it doesn't exist
    if (!is_dir($dest)) {
        if (!mkdir($dest, 0755, true)) {
            loopis_elog_error("Failed to create directory: {$dest}");
            return false;
        }
        loopis_elog_first_level("Created directory: " . basename($dest));
    }

    // Get directory contents
    $files = scandir($source);
    if ($files === false) {
        loopis_elog_error("Failed to read directory: {$source}");
        return false;
    }

    $success = true;

    // Copy each item
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        }

        $source_path = $source . '/' . $file;
        $dest_path = $dest . '/' . $file;

        // Recursively copy subdirectories and files
        if (!loopis_recursive_copy($source_path, $dest_path)) {
            $success = false;
        }
    }

    return $success;
}

/**
 * Copies root files and folders into WordPress installation
 *
 * @return void
 */
function loopis_root_files_copy() {
    loopis_elog_function_start('loopis_root_files_copy');

    // Define source and destination paths
    $source_dir = LOOPIS_CONFIG_DIR . 'assets/root_files';
    $dest_dir = ABSPATH;

    // Remove trailing slash if present
    $source_dir = rtrim($source_dir, '/');
    $dest_dir = rtrim($dest_dir, '/');

    // Check if source directory exists
    if (!is_dir($source_dir)) {
        loopis_elog_error("Source directory does not exist: {$source_dir}");
        loopis_elog_function_end_fail('loopis_root_files_copy');
        return;
    }

    loopis_elog_first_level("Copying from: {$source_dir}");
    loopis_elog_first_level("Copying to: {$dest_dir}");

    // Get all items in the source directory
    $items = scandir($source_dir);
    if ($items === false) {
        loopis_elog_error("Failed to read source directory: {$source_dir}");
        loopis_elog_function_end_fail('loopis_root_files_copy');
        return;
    }

    $success = true;

    // Copy each item (file or directory) to the destination
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }

        $source_path = $source_dir . '/' . $item;
        $dest_path = $dest_dir . '/' . $item;

        if (!loopis_recursive_copy($source_path, $dest_path)) {
            $success = false;
        }
    }

    if ($success) {
        loopis_elog_function_end_success('loopis_root_files_copy');
    } else {
        loopis_elog_function_end_fail('loopis_root_files_copy');
    }
}