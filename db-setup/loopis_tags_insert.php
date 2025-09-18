<?php
/**
 * Function to insert LOOPIS default tags in the WordPress database.
 *
 * This function is called by main function 'loopis_db_setup'.
 * 
 * Corresponding function to remove the tags is called by 'loopis_db_cleanup'.
 *
 * @package LOOPIS_Config
 * @subpackage Database
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Insert tags into 'wp_terms'
 */
function loopis_tags_insert() {
    error_log('Running function loopis_tags_insert...');
    // Define the tags to insert
    $tags = [
        ['name' => 'Accessoarer',      'slug' => 'accessoarer'],
        ['name' => 'Badrum',           'slug' => 'badrum'],
        ['name' => 'Barnböcker',       'slug' => 'barnbocker'],
        ['name' => 'Barnkläder',       'slug' => 'barnklader'],
        ['name' => 'Barnsaker',        'slug' => 'barnsaker'],
        ['name' => 'Bil & cykel',      'slug' => 'bil-cykel'],
        ['name' => 'Bygg & fix',       'slug' => 'bygg-fix'],
        ['name' => 'Böcker',           'slug' => 'bocker'],
        ['name' => 'Djursaker',        'slug' => 'djursaker'],
        ['name' => 'Elektronik',       'slug' => 'elektronik'],
        ['name' => 'Fest',             'slug' => 'fest'],
        ['name' => 'Förvaring',        'slug' => 'forvaring'],
        ['name' => 'Hobby & pyssel',   'slug' => 'hobby-pyssel'],
        ['name' => 'Hushåll',          'slug' => 'hushall'],
        ['name' => 'Hälsa',            'slug' => 'halsa'],
        ['name' => 'Inredning',        'slug' => 'inredning'],
        ['name' => 'Kläder',           'slug' => 'klader'],
        ['name' => 'Kontor',           'slug' => 'kontor'],
        ['name' => 'Kök',              'slug' => 'kok'],
        ['name' => 'Lampor',           'slug' => 'lampor'],
        ['name' => 'Leksaker',         'slug' => 'leksaker'],
        ['name' => 'Mat & dryck',      'slug' => 'mat-dryck'],
        ['name' => 'Mattor',           'slug' => 'mattor'],
        ['name' => 'Musik & kultur',   'slug' => 'musik-kultur'],
        ['name' => 'Möbler',           'slug' => 'mobler'],
        ['name' => 'Odling',           'slug' => 'odling'],
        ['name' => 'Skor',             'slug' => 'skor'],
        ['name' => 'Solglasögon',      'slug' => 'solglasogon'],
        ['name' => 'Skönhet',          'slug' => 'skonhet'],
        ['name' => 'Spel',             'slug' => 'spel'],
        ['name' => 'Sport & fritid',   'slug' => 'sport-fritid'],
        ['name' => 'Textil',           'slug' => 'textil'],
        ['name' => 'Tidningar',        'slug' => 'tidningar'],
        ['name' => 'Väskor',           'slug' => 'vaskor'],
        ['name' => 'Övrigt',           'slug' => 'ovrigt'],
    ];

    global $wpdb;
    $loopis_term_group = 10; // Unique group ID for LOOPIS tags

    // Insert each tag if it doesn't already exist
    foreach ($tags as $tag) {
        // Check if term already exists
        $existing = term_exists($tag['slug'], 'post_tag');
        
        if (!$existing) {
            $result = wp_insert_term(
                $tag['name'],    // term name
                'post_tag',      // taxonomy
                array(
                    'slug' => $tag['slug']
                )
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
}