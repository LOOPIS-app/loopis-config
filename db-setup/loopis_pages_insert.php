<?php
/**
 * Functions to create LOOPIS pages in the WordPress database.
 *
 * This function is called by main function 'loopis_db_setup'.
 * 
 * Corresponding function to remove the pages is called by 'loopis_db_cleanup'.
 *
 * @package LOOPIS_Config
 * @subpackage Database
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
} 

/**
 * Inserts pages into wp_posts
 *
 * @return void
 */
function loopis_pages_insert() {
    error_log('Running function loopis_pages_insert...');

    // Define the pages to create
    $pages_to_create = array(
        array(
            'post_title' => 'Frågor & svar',
            'post_name'  => 'faq',
        ),
        array(
            'post_title' => 'Logga in',
            'post_name'  => 'logga-in',
        ),
        array(
            'post_title' => 'Byt lösenord',
            'post_name'  => 'password-reset',
        ),
        array(
            'post_title' => 'Bli medlem',
            'post_name'  => 'bli-medlem',
        ),
        array(
            'post_title' => 'Inställningar',
            'post_name'  => 'profile-settings',
        ),
        array(
            'post_title' => 'Min profil',
            'post_name'  => 'profile',
        ),
        array(
            'post_title' => 'Kategorier',
            'post_name'  => 'kategorier',
        ),
        array(
            'post_title' => 'Ge bort',
            'post_name'  => 'submit',
        ),
        array(
            'post_title' => 'Integritetspolicy',
            'post_name'  => 'integritetspolicy',
        ),
        array(
            'post_title' => 'Favoriter',
            'post_name'  => 'favoriter',
        ),
        array(
            'post_title' => 'Mina gåvor',
            'post_name'  => 'mina-gavor',
        ),
        array(
            'post_title' => 'Bli ambassadör',
            'post_name'  => 'ambassador',
        ),
        array(
            'post_title' => 'Blogg',
            'post_name'  => 'blog',
        ),
        array(
            'post_title' => 'Om oss',
            'post_name'  => 'om-oss',
        ),
        array(
            'post_title' => 'Admin',
            'post_name'  => 'admin',
        ),
    );

    // Common values for all pages
    $common_values = array(
        'post_author'    => 1, // OBS: This need to be an existing user ID in your WP installation.
        'post_status'    => 'publish',
        'post_type'      => 'page',
        'ping_status'    => 'closed',
        'comment_status' => 'closed',
        'post_parent'    => 0, // No parent page for now.    
    );

    // The unique meta key and value to identify pages created by this plugin.
    $meta_key_to_add = '_loopis_config_page';
    $meta_value_to_add = '1';

    foreach ($pages_to_create as $page) {
        // Combine common values with page-specific values.
        $page_data = array_merge($page, $common_values);

        // Verify if the page already exists by its slug (post_name).
        $existing_page = get_page_by_path($page_data['post_name'], OBJECT, 'page');

        if ($existing_page == null) {
            // If the page does not exist, create it.
            $new_page_id = wp_insert_post($page_data);

            if (!is_wp_error($new_page_id)) {
                // Add the unique meta tag to the newly created page.
                add_post_meta($new_page_id, $meta_key_to_add, $meta_value_to_add, true);
            }
        }
    }
}