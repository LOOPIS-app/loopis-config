<?php
/**
 * Function to delete LOOPIS categories in the WordPress database.
 *
 * This function is called by main function 'loopis_db_cleanup'.
 * 
 * Deletes all LOOPIS categories in 'wp_terms' created by function 'loopis_categories_insert'.
 *
 * @package LOOPIS_Config
 * @subpackage Dev-tools
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Delete categories in 'wp_terms'
 * 
 * @return void
 */
function loopis_categories_delete() {
    loopis_elog_function_start('loopis_categories_delete');

    // Access WordPress database object
    global $wpdb;

    // The specific term_group used for LOOPIS categories
    $term_group = 1;

    // Get all term_ids with this group
    $term_ids = $wpdb->get_col($wpdb->prepare(
        "SELECT term_id FROM {$wpdb->terms} WHERE term_group = {$term_group}"
    ));

    // If no categories are found: return.
    if (empty($term_ids)) {
        loopis_elog_function_end_success('loopis_categories_delete');
    }
     // Delete categories
    foreach ( $term_ids as $term_id ) {
        wp_delete_term( (int) $term_id, 'category' );
    }
    
    // Make new into uncat
    $wpdb->update(
        $wpdb->terms,
        [
            'name'       => 'Uncategorized',
            'slug'       => 'uncategorized',
            'term_group' => 0,
        ],
        ['term_id' => 1],
        ['%s', '%s', '%d'],
        ['%d']
    );

    // Make new into uncat
    $wpdb->update(
        $wpdb->term_taxonomy,
        [
            'description' => '',
            'parent'      => 0,
            'count'       => 0,
        ],
        ['term_id' => 1],
        ['%s', '%d', '%d'],
        ['%d']
    );
    // Clear cache
    clean_term_cache(1, 'category');
    loopis_elog_function_end_success('loopis_categories_delete');
}
    