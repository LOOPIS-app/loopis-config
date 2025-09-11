<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Inserts tags into wp_terms with a specific term_group.
 */
function loopis_tags_insert() {
    global $wpdb;

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

    $term_group = 10;

    foreach ($tags as $tag) {
        // Check if term already exists
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT term_id FROM {$wpdb->terms} WHERE slug = %s", $tag['slug']
        ));

        if (!$existing) {
            $wpdb->insert(
                $wpdb->terms,
                [
                    'name'       => $tag['name'],
                    'slug'       => $tag['slug'],
                    'term_group' => $term_group,
                ]
            );
        }
    }
}

/**
 * Deletes all tags in wp_terms with the specific term_group.
 */
function loopis_tags_delete() {
    global $wpdb;
    $term_group = 10;

    // Get all term_ids with this group
    $term_ids = $wpdb->get_col($wpdb->prepare(
        "SELECT term_id FROM {$wpdb->terms} WHERE term_group = %d", $term_group
    ));

    if (!empty($term_ids)) {
        $in = implode(',', array_map('intval', $term_ids));
        // Remove from term_taxonomy first (to avoid orphaned rows)
        $wpdb->query("DELETE FROM {$wpdb->term_taxonomy} WHERE term_id IN ($in)");
        // Remove from terms table
        $wpdb->query("DELETE FROM {$wpdb->terms} WHERE term_id IN ($in)");
    }
}