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
        ['name' => 'Inredning',        'slug' => 'inredning'],
        ['name' => 'Böcker',           'slug' => 'bocker'],
        ['name' => 'Kök',              'slug' => 'kok'],
        ['name' => 'Leksaker',         'slug' => 'leksaker'],
        ['name' => 'Kläder',           'slug' => 'klader'],
        ['name' => 'Barnsaker',        'slug' => 'barnsaker'],
        ['name' => 'Fest',             'slug' => 'fest'],
        ['name' => 'Spel',             'slug' => 'spel'],
        ['name' => 'Musik & kultur',   'slug' => 'musik-kultur'],
        ['name' => 'Odling',           'slug' => 'odling'],
        ['name' => 'Skor',             'slug' => 'skor'],
        ['name' => 'Möbler',           'slug' => 'mobler'],
        ['name' => 'Sport & fritid',   'slug' => 'sport-fritid'],
        ['name' => 'Elektronik',       'slug' => 'elektronik'],
        ['name' => 'Lampor',           'slug' => 'lampor'],
        ['name' => 'Förvaring',        'slug' => 'forvaring'],
        ['name' => 'Bygg & fix',       'slug' => 'bygg-fix'],
        ['name' => 'Badrum',           'slug' => 'badrum'],
        ['name' => 'Kontor',           'slug' => 'kontor'],
        ['name' => 'Väskor',           'slug' => 'vaskor'],
        ['name' => 'Solglasögon',      'slug' => 'solglasogon'],
        ['name' => 'Hushåll',          'slug' => 'hushall'],
        ['name' => 'Tidningar',        'slug' => 'tidningar'],
        ['name' => 'Textil',           'slug' => 'textil'],
        ['name' => 'Djursaker',        'slug' => 'djursaker'],
        ['name' => 'Övrigt',           'slug' => 'ovrigt'],
        ['name' => 'Hälsa',            'slug' => 'halsa'],
        ['name' => 'Skönhet',          'slug' => 'skonhet'],
        ['name' => 'Mat & dryck',      'slug' => 'mat-dryck'],
        ['name' => 'Barnkläder',       'slug' => 'barnklader'],
        ['name' => 'Barnböcker',       'slug' => 'barnbocker'],
        ['name' => 'Accessoarer',      'slug' => 'accessoarer'],
        ['name' => 'Hobby & pyssel',   'slug' => 'hobby-pyssel'],
        ['name' => 'Bil & cykel',      'slug' => 'bil-cykel'],
    ];

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
                error_log('Successfully inserted tag: ' . $tag['name']);
            }
        }
    }
}