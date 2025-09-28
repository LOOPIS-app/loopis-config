<?php
/**
 * Functions to create LOOPIS pages (with page templates) in the WordPress database.
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
    
    // Delete default WordPress default pages and posts
    loopis_delete_default_content();
    
    // Define the pages to create
    $pages_to_create = array(
        array(
            'post_title' => '🌈 Startsida',
            'post_name'  => 'start',
            'page_template' => 'start.php'
        ),
        array(
            'post_title' => '🎁 Saker att få',
            'post_name'  => 'gifts',
            'page_template' => 'gifts.php'
        ),
        array(
            'post_title' => '🗄 Integritetspolicy',
            'post_name'  => 'privacy',
            'page_template' => 'privacy.php'
        ),
        array(
            'post_title' => '🔍 Sök',
            'post_name'  => 'search',
            'page_template' => 'search.php'
        ),
        array(
            'post_title' => '♻ Upptäck',
            'post_name'  => 'discover',
            'page_template' => 'discover.php'
        ),
        array(
            'post_title' => '💚 Ge bort',
            'post_name'  => 'submit',
            'page_template' => 'submit.php'
        ),
        array(
            'post_title' => '💡 Frågor & svar',
            'post_name'  => 'faq',
            'page_template' => 'faq.php'
        ),
        // These will be created by WPUM Plugin, but should be renamed like below.
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
        array(
            'post_title' => '🐙 Admin',
            'post_name'  => 'admin',
            'page_template' => 'admin.php'
        ),
    );

    // Common values for all pages
    $common_values = array(
        'post_author'    => 1,
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
        // Extract page template before merging
        $page_template = isset($page['page_template']) ? $page['page_template'] : '';
        unset($page['page_template']); // Remove from page data as it's not a wp_insert_post parameter
        
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
                // Add the unique meta tag to the newly created page.
                add_post_meta($new_page_id, $meta_key_to_add, $meta_value_to_add, true);
                
                // Add page template if specified
                if (!empty($page_template)) {
                    add_post_meta($new_page_id, '_wp_page_template', $page_template, true);
                    loopis_elog_first_level('Created page: ' . $page_data['post_title'] . ' with template: ' . $page_template);
                } else {
                    loopis_elog_first_level('Created page: ' . $page_data['post_title']);
                }

                // Set front page in wp_options
                if ($page_data['post_name'] === 'start' && !is_wp_error($new_page_id)) {
                    // Set this page as front page
                    update_option('show_on_front', 'page');
                    update_option('page_on_front', $new_page_id);
                    loopis_elog_first_level('Set start page as front page: ' . $new_page_id);
                }

                // Set posts page in wp_options
                if ($page_data['post_name'] === 'gifts' && !is_wp_error($new_page_id)) {
                    // Set this page as posts page
                    update_option('page_for_posts', $new_page_id);
                    loopis_elog_first_level('Set gifts page as posts page: ' . $new_page_id);
                }

                // Set privacy policy page in wp_options
                if ($page_data['post_name'] === 'privacy' && !is_wp_error($new_page_id)) {
                    // Set this page as privacy policy page
                    update_option('wp_page_for_privacy_policy', $new_page_id);
                    loopis_elog_first_level('Set privacy page as privacy policy page: ' . $new_page_id);
                }
            }
        }
    }
    loopis_elog_function_end_success('loopis_pages_insert');

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