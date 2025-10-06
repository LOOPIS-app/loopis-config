<?php
/**
 * Function to delete LOOPIS pages in the WordPress database.
 *
 * This function is called by main function 'loopis_db_cleanup'.
 * 
 * Deletes all LOOPIS pages in 'wp_posts' created by function 'loopis_pages_create'.
 *
 * @package LOOPIS_Config
 * @subpackage Dev-tools
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
} 

/**
 * Delete pages in 'wp_posts'
 *
 * @return void
 */
function loopis_pages_delete() {
    loopis_elog_function_start('loopis_pages_delete');

    // Define the same unique meta key that was used during creation.
    $meta_key_to_delete = '_loopis_config_page';

    // Create a query to find all pages with the specific meta key.
    $pages_to_delete = new WP_Query(array(
        'post_type'  => 'page',
        'meta_query' => array(
            array(
                'key'   => $meta_key_to_delete,
                'value' => '1',
            ),
        ),
        'fields'     => 'ids', 
        'posts_per_page' => -1, // <-- Fetch all matching pages!
    ));

    // If there are pages to delete, loop through them and delete.
    if ($pages_to_delete->have_posts()) {
        foreach ($pages_to_delete->posts as $post_id) {
            // Force delete the page (bypass trash).
            loopis_elog_first_level(" loopis_delete_pages: Deleting page ID $post_id");

            wp_delete_post($post_id, true);
        }
    }
    loopis_elog_function_end_success('loopis_pages_delete');
}