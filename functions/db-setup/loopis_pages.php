<?php
/**
 * Functions to create LOOPIS pages in the WordPress database.
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
 * Inserts pages into wp_posts
 *
 * @return void
 */
function loopis_pages_insert() {
    loopis_elog_function_start('loopis_pages_insert');
    
    // First delete default WordPress default pages and posts
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
            'post_title' => '🗄 Integritet',
            'post_name'  => 'privacy',
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
            'post_title' => '💡 Frågor & svar',
            'post_name'  => 'faq',
        ),
        array(
            'post_title' => '📡 Nyheter',
            'post_name'  => 'news',
        ),
        array(
            'post_title' => '💞 Event',
            'post_name'  => 'event',
        ),
        array(
            'post_title' => '🔔 Min aktivitet',
            'post_name'  => 'activity',
        ),
        array(
            'post_title' => '🐙 Admin',
            'post_name'  => 'admin',
        ),
        // The pages below will be created by WPUM Plugin. Should be renamed later?
        /*
        array(
            'post_title' => '👤 Logga in',
            'post_name'  => 'log-in',
        ),
        array(
            'post_title' => '📋 Bli medlem',
            'post_name'  => 'register',
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
            'post_title' => '⚙ Inställningar',
            'post_name'  => 'account',
        ),
        */
    );

    // Common values for all pages
    $common_values = array(
        'post_author'    => 1,
        'post_status'    => 'publish',
        'post_type'      => 'page',
        'ping_status'    => 'closed',
        'comment_status' => 'closed',
        'post_parent'    => 0, // No parent pages for now.    
    );

    foreach ($pages_to_create as $page) {
        // Combine common values with page-specific values.
        $page_data = array_merge($page, $common_values);

        // Logging page creation
        loopis_elog_first_level('Creating page: ' . $page_data['post_title']);

        // Verify if the page already exists by its slug (post_name).
        $existing_page = get_page_by_path($page_data['post_name'], OBJECT, 'page');

        if ($existing_page == null) {
            // If the page does not exist, create it.
            $new_page_id = wp_insert_post($page_data);

            if (!is_wp_error($new_page_id)) {
                loopis_elog_first_level('Created page: ' . $page_data['post_title']);
            }
        }
    }
    loopis_elog_function_end_success('loopis_pages_insert');

}

/**
 * Alters WPUM pages for use in loopis
 * 
 * Runs post activation
 *
 * @return void
 */
// Another possibillity might be to stop the wpum page creation, and create them from scratch
function loopis_pages_rename() {
    loopis_elog_function_start('loopis_pages_rename');
    // Change array
    $pages = array(
        'log-in' => '👤 Logga in',
        'register' => '📋 Bli medlem',
        'password-reset' => '🔑 Byt lösenord',
        'profile' => '👤 Min profil',
        'account' => '⚙ Inställningar',
    );
    // Update pages
    foreach ($pages as $post_name => $new_title) {
        $page = get_page_by_path($post_name, OBJECT, 'page');
        if ($page) {
            wp_update_post(array(
                'ID' => $page->ID,
                'post_title' => $new_title,
            ));
        }
    }
    loopis_elog_function_end_success('loopis_pages_rename');
}

/**
 * Delete default WordPress pages and posts
 *
 * @return void
 */
function loopis_delete_default_content() {
    loopis_elog_first_level('Deleting default WordPress content...');
    
    // Default pages to delete
    $default_pages = array('privacy-policy', 'sample-page');
    
    // Default posts to delete  
    $default_posts = array('hello-world');

    // Delete default pages
    foreach ($default_pages as $page_slug) {
        $page = get_page_by_path($page_slug, OBJECT, 'page');
        if ($page) {
            wp_delete_post($page->ID, true); // true = force delete (bypass trash)
            loopis_elog_second_level('Deleted default page: ' . $page_slug);
        }
    }
    
    // Delete default posts
    foreach ($default_posts as $post_slug) {
        $post = get_page_by_path($post_slug, OBJECT, 'post');
        if ($post) {
            wp_delete_post($post->ID, true); // true = force delete (bypass trash)
            loopis_elog_second_level('Deleted default post: ' . $post_slug);
        }
    }
}