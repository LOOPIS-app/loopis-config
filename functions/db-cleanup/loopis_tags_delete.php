<?php
/**
 * Function to delete LOOPIS tags in the WordPress database.
 *
 * This function is called by main function 'loopis_db_cleanup'.
 * 
 * Deletes all LOOPIS tags in 'wp_terms' created by function 'loopis_tags_insert'.
 *
 * @package LOOPIS_Config
 * @subpackage Dev-tools
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Delete tags in 'wp_terms'
 * 
 * @return void
 */
function loopis_tags_delete() {
    loopis_elog_function_start('loopis_tags_delete');

    // Access WordPress database object
    global $wpdb;

    // The specific term_group used for LOOPIS tags
    $term_group = 2;

    // Get all term_ids with this group
    $term_ids = $wpdb->get_col($wpdb->prepare(
        "SELECT term_id FROM {$wpdb->terms} WHERE term_group = %d", $term_group
    ));

    // If tags are found, delete them
    if (!empty($term_ids)) {
        $in = implode(',', array_map('intval', $term_ids));
        // Remove from term_taxonomy first (to avoid orphaned rows)
        $wpdb->query("DELETE FROM {$wpdb->term_taxonomy} WHERE term_id IN ($in)");
        // Remove from terms table
        $wpdb->query("DELETE FROM {$wpdb->terms} WHERE term_id IN ($in)");
    }
    loopis_elog_function_end_success('loopis_tags_delete');
}