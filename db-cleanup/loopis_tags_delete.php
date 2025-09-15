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
 */
function loopis_tags_delete() {
    error_log('Running function loopis_tags_delete...');

    global $wpdb;

    // The specific term_group used for LOOPIS tags
    $term_group = 10;

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
}