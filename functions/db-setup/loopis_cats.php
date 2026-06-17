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
    loopis_elog_function_start('loopis_cats_insert');

    // Define the categories to insert
    if (is_multisite() && (get_current_blog_id()===1)){
        $uncat = ['name' =>'💚 Efterfrågat',     'slug' => 'requested', 'term_group' => 1];
        $categories = [
            ['name' =>'💚 Efterfrågat',     'slug' => 'requested'   ],
            ['name' =>'🧡 På gång',         'slug' => 'coming'      ],
            ['name' =>'❤ Aktivt',           'slug' => 'active'      ],
        ];
    } else{
        $uncat = ['name' =>'⏳ Lottning', 'slug' => 'new', 'term_group' => 1];
        $categories = [
            ['name' =>'⏳ Lottning',            'slug' => 'new'],
            ['name' =>'🟢 Först till kvarn',    'slug' => 'old'],
            ['name' =>'❤ Paxad',                'slug' => 'booked'],
            ['name' =>'🤎 Paxad',               'slug' => 'booked_custom'],
            ['name' =>'⏹ Skåpet',               'slug' => 'locker'],
            ['name' =>'☑ Hämtad',               'slug' => 'fetched'],
            ['name' =>'❌ Borttagen',           'slug' => 'removed'],
            ['name' =>'💢 Ej mottagen',         'slug' => 'disappeared'],
            ['name' =>'📦 Lager',               'slug' => 'storage'],
            ['name' =>'😎 Pausad',              'slug' => 'paused'],
            ['name' =>'⭕ Arkiverad',           'slug' => 'archived'], 
            ['name' =>'📌 Tips',                'slug' => 'tips'],
            ['name' =>'🗓 Låna',                'slug' => 'borrow'],
            ['name' =>'🎁 Saker att få',        'slug' => 'stuff'],
            ['name' =>'⛔ Hidden',              'slug' => 'hidden'],
        ];
    }
    $parent_map = [
        'tips'           => 'stuff',
        'old'            => 'stuff',
        'new'            => 'stuff',
        'booked'         => 'stuff',
        'booked_custom'  => 'stuff',
        'storage'        => 'hidden',
        'archived'       => 'hidden',
        'paused'         => 'hidden',
        'removed'        => 'hidden',
        'fetched'        => 'hidden',
        'locker'         => 'hidden',
        'disappeared'    => 'hidden',
    ];
    // Set term group for loopis cats
    $loopis_term_group = 1;

    // Access WordPress database object
    global $wpdb;
    $uncategorized = get_term_by('slug', 'uncategorized', 'category');
    $term_ids = [];

    if ($uncategorized && !is_wp_error($uncategorized)) {
        // Rename and update the slug

        $updated = wp_update_term(
            $uncategorized->term_id,
            'category',
            $uncat
        );

        if (is_wp_error($updated)) {
            loopis_elog_first_level('Error renaming Uncategorized: ' . $updated->get_error_message());
        } else {
            loopis_elog_first_level('Renamed Uncategorized to ⏳ Lottning');
            $term_ids['new'] = $uncategorized->term_id;
        }
    }
    // Insert each category if it doesn't already exist
    foreach ($categories as $category) {
        // Check if term already exists
        $term = get_term_by('slug', $category['slug'], 'category');
        if (!$term) {
            $result = wp_insert_term(
                $category['name'],
                'category',
                ['slug' => $category['slug']]
            );
            if (is_wp_error($result)) {
                loopis_elog_first_level('Error inserting category: ' . $result->get_error_message());
            } else {
                loopis_elog_first_level('Successfully inserted category: ' . $category['name']);
                $term_ids[$category['slug']] = $result['term_id'];
            }
        }else{
            $term_ids[$category['slug']] = $term->term_id;
        }
    }
    if (!(is_multisite() && (get_current_blog_id()===1))){
        foreach ($parent_map as $child_slug => $parent_slug) {
            $child_id  = $term_ids[$child_slug];
            $parent_id = $term_ids[$parent_slug];
            wp_update_term($child_id, 'category', ['parent' => $parent_id]);
            loopis_elog_first_level("Set parent of $child_slug to $parent_slug");
        }
    }

    foreach ($term_ids as $term_id) {
        $wpdb->update(
            $wpdb->terms,
            ['term_group' => 1],
            ['term_id' => $term_id],
            ['%d'],
            ['%d']
        );
    }
    
    // Set 'new' as default category
    $term = get_term_by('slug', $uncat['slug'], 'category');
    if ($term) {
        update_option('default_category', $term->term_id);
        loopis_elog_first_level('Set default category to: new');
    }
    
    loopis_elog_function_end_success('loopis_cats_insert');
}