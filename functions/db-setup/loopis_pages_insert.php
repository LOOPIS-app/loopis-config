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
    
    // Delete default WordPress default pages and posts
    loopis_delete_default_content();
    
    // Define the pages to create
    $pages_to_create = array(
        array(
            'post_title' => '🌈 Startsida',
            'post_name'  => 'start',
        ),
        array(
            'post_title' => '🎁 Saker att få',
            'post_name'  => 'gifts',
        ),
        array(
            'post_title' => '🔍 Sök',
            'post_name'  => 'search',
        ),
        array(
            'post_title' => '♻ Upptäck',
            'post_name'  => 'discover',
        ),
        array(
            'post_title' => '💚 Ge bort',
            'post_name'  => 'submit',
        ),
        array(
            'post_title' => '🗄 Integritetspolicy',
            'post_name'  => 'privacy',
        ),
        array(
            'post_title' => '💡 Frågor & svar',
            'post_name'  => 'faq',
        ),
        array(
            'post_title' => '👤 Logga in',
            'post_name'  => 'log-in',
        ),
        array(
            'post_title' => '📋 Bli medlem',
            'post_name'  => 'sign-up',
        ),
        array(
            'post_title' => '🔑 Byt lösenord',
            'post_name'  => 'password-reset',
        ),
        array(
            'post_title' => '👤 Min profil',
            'post_name'  => 'profile',
        ),
        array(
            'post_title' => '🐙 Admin',
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
                error_log('Created page: ' . $page_data['post_title']);
            }
        }
    }
}

/**
 * Delete default WordPress pages and posts
 *
 * @return void
 */
function loopis_delete_default_content() {
    error_log('Deleting default WordPress content...');
    
    // Default pages to delete
    $default_pages = array('privacy-policy', 'sample-page');
    
    // Default posts to delete  
    $default_posts = array('hello-world');
    
    // Delete default pages
    foreach ($default_pages as $page_slug) {
        $page = get_page_by_path($page_slug, OBJECT, 'page');
        if ($page) {
            wp_delete_post($page->ID, true); // true = force delete (bypass trash)
            error_log('Deleted default page: ' . $page_slug);
        }
    }
    
    // Delete default posts
    foreach ($default_posts as $post_slug) {
        $post = get_page_by_path($post_slug, OBJECT, 'post');
        if ($post) {
            wp_delete_post($post->ID, true); // true = force delete (bypass trash)
            error_log('Deleted default post: ' . $post_slug);
        }
    }
} 