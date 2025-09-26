<?php
/**
 * Function to insert LOOPIS default categories in the WordPress database.
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
 * Insert categories into 'wp_terms'
 * 
 * @return void
 */
function loopis_cats_insert() {
    error_log('Starting function: loopis_cats_insert()...');

    // Delete default category 'uncategorized' first
    $uncategorized = get_term_by('slug', 'uncategorized', 'category');
    if ($uncategorized) {
        wp_delete_term($uncategorized->term_id, 'category');
        error_log('Deleted uncategorized category');
    }

    // Define the categories to insert
    $categories = [
        ['name' =>'⏳ Lottning',            'slug' => 'new'],
        ['name' =>'🟢 Först till kvarn',    'slug' => 'old'],
        ['name' =>'❤ Paxad',               'slug' => 'booked'],
        ['name' =>'🤎 Paxad',               'slug' => 'booked_custom'],
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
    ];

    // Access WordPress database object
    global $wpdb;

    // Set term_group ID for LOOPIS categories
    $loopis_term_group = 1;

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
                error_log('Error inserting category: ' . $result->get_error_message());
            } else {
                error_log('Successfully inserted category: ' . $category['name']);
                
                // Update the term_group to mark it as a LOOPIS category
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
    
    // Update default category to 'new' - do this last
    $term = get_term_by('slug', 'new', 'category');
    if ($term) {
        update_option('default_category', $term->term_id);
        error_log('Set default category to: new');
    }
}