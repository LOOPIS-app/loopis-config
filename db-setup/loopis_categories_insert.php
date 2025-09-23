<?php
/**
 * Function to insert LOOPIS default caregories in the WordPress database.
 *
 * This function is called by main function 'loopis_db_setup'.
 * 
 * Corresponding function to remove the categories is called by 'loopis_db_cleanup'.
 *
 * @package LOOPIS_Config
 * @subpackage Database
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}


/**
 * Insert categories into 'wp_terms'
 */
function loopis_categories_insert() {
    error_log('Running function loopis_categories_insert...');

    global $wpdb;

    // Use WordPress table prefix
    $terms_table = $wpdb->prefix . 'terms';
    $taxonomy_table = $wpdb->prefix . 'term_taxonomy';
    $relationships_table = $wpdb->prefix . 'term_relationships';

    // Disable key checks so that we are allowed to clear db
    $wpdb->query('SET FOREIGN_KEY_CHECKS = 0');

    // Truncate
    $wpdb->query('TRUNCATE TABLE $terms_table');
    $wpdb->query('TRUNCATE TABLE $taxonomy_table');
    $wpdb->query('TRUNCATE TABLE $relationships_table');

    // Re-enable key checks
    $wpdb->query('SET FOREIGN_KEY_CHECKS = 1');

    // Define the categories to insert
    $categories = [
        ['name' =>'⏳ Lottning',            'slug' => 'new'],
        ['name' =>'🟢 Först till kvarn',    'slug' => 'old'],
        ['name' =>'❤ Paxad',               'slug' => 'booked'],
        ['name' =>'❤ Paxad',               'slug' => 'booked_custom'],
        ['name' =>'⏹ Skåpet',               'slug' =>'locker'],
        ['name' =>'☑ Hämtad',              'slug' => 'fetched'],
        ['name' =>'❌ Borttagen',           'slug' => 'removed'],
        ['name' =>'💢 Ej mottagen',         'slug' => 'disappeared'],
        ['name' =>'📦 Lager',               'slug' => 'storage'],
        ['name' =>'😎 Pausad',              'slug' => 'paused'],
        ['name' =>'⭕ Arkiverad',           'slug' => 'archived'],
        ['name' =>'📌 Tips',                'slug' => 'tips'],
        ['name' =>'Reserved_1',             'slug' => 'reserved_1'],
        ['name' =>'Reserved_2',             'slug' => 'reserved_2'],
        ['name' =>'Reserved_3',             'slug' => 'reserved_3'],
        ['name' =>'Reserved_4',             'slug' => 'reserved_4'],
        ['name' =>'Reserved_5',             'slug' => 'reserved_5'],
        ['name' =>'Reserved_6',             'slug' => 'reserved_6'],
        ['name' =>'Reserved_7',             'slug' => 'reserved_7'],
        ['name' =>'Reserved_8',             'slug' => 'reserved_8'],
    ];

    // Group ID for LOOPIS categories
    $loopis_term_group = 20;
    if (!True){
        return;
    }
    // Insert each category if it doesn't already exist
    foreach ($categories as $category) {
    // Check if term already exists
        if (!term_exists($category['slug'], 'category')) {
            $result = wp_insert_term(
                $category['name'],
                'category',
                ['slug' => $category['slug']]
            );
            if (is_wp_error($result)) {
                error_log('Error inserting tag: ' . $result->get_error_message());
            } else {
                // Update the term_group to mark it as a LOOPIS tag
                $term_id = $result['term_id'];
                $wpdb->update(
                    $wpdb->terms,
                    array('term_group' => $loopis_term_group),
                    array('term_id' => $term_id),
                    array('%d'),
                    array('%d')
                );
            }
        }
    }
    
    //Update default
    $term = get_term_by('slug', 'new', 'category');
    if ($term) {
        update_option('default_category', $term->term_id);
    }
}
