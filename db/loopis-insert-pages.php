<?php
/**
 * The file that creates initial pages in WordPress
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
} 

/**
 * Inserts pages into the WordPress database if they do not already exist.
 *
 * @param array $pages_data An array of associative arrays, each containing 'post_title', 'post_name', and 'icon' keys.
 * @return void
 */
function loopis_insert_pages() {
    error_log('LOOPIS Config insert pages');
    $pages_to_create = array(
        array(
            'post_title' => 'Frågor & svar',
            'post_name'  => 'faq',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9.879 7.519c1.18-.57 2.478-.892 3.821-.892 2.052 0 3.992.836 5.485 2.222m-2.14 8.783c.969-1.298 1.488-2.825 1.488-4.595 0-3.003-1.077-5.556-2.527-6.938m-1.953 3.655a3.655 3.655 0 01-5.744-4.225m6.878 5.61a3.655 3.655 0 01-5.61-6.878m5.61 6.878l-5.61-5.61M12 21a9 9 0 100-18 9 9 0 000 18z" /></svg>',
        ),
        array(
            'post_title' => 'Logga in',
            'post_name'  => 'logga-in',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 19.5a12.75 12.75 0 012.768-3.998 8.25 8.25 0 0110.963 0A12.75 12.75 0 0119.5 19.5h-15z" /></svg>',
        ),
        array(
            'post_title' => 'Byt lösenord',
            'post_name'  => 'password-reset',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zM12 15.75h.007v.008H12z" /></svg>',
        ),
        array(
            'post_title' => 'Bli medlem',
            'post_name'  => 'bli-medlem',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        ),
        array(
            'post_title' => 'Inställningar',
            'post_name'  => 'profile-settings',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.75 2.924-1.75 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.75.426 1.75 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.75-2.924 1.75-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.75-.426-1.75-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>',
        ),
        array(
            'post_title' => 'Min profil',
            'post_name'  => 'profile',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" /></svg>',
        ),
        array(
            'post_title' => 'Kategorier',
            'post_name'  => 'kategorier',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M9.529 8.288a3.864 3.864 0 01.373-1.066M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>',
        ),
        array(
            'post_title' => 'Ge bort',
            'post_name'  => 'submit',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.938 0-3.593 1.103-4.331 2.75-.85-1.921-2.91-3.25-5.274-3.25C4.119 3 2 5.015 2 7.5c0 3.016 2.997 6.002 8.583 11.258a1 1 0 00.917 0c5.586-5.256 8.583-8.242 8.583-11.25z" /></svg>',
        ),
        array(
            'post_title' => 'Integritetspolicy',
            'post_name'  => 'integritetspolicy',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21L11.5 21l2.5-10.5z" /></svg>',
        ),
        array(
            'post_title' => 'Admin',
            'post_name'  => 'admin',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a3 3 0 100-6 3 3 0 000 6z" /></svg>',
        ),
        array(
            'post_title' => 'Favoriter',
            'post_name'  => 'favoriter',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.938 0-3.593 1.103-4.331 2.75-.85-1.921-2.91-3.25-5.274-3.25C4.119 3 2 5.015 2 7.5c0 3.016 2.997 6.002 8.583 11.258a1 1 0 00.917 0c5.586-5.256 8.583-8.242 8.583-11.25z" /></svg>',
        ),
        array(
            'post_title' => 'Mina gåvor',
            'post_name'  => 'mina-gavor',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M10.125 4.5H13.875M6.651 9.497l1.042-3.126M17.348 9.497l-1.042-3.126M9.135 18.5H4.14C3.018 18.5 2 17.5 2 16.5l-.25-11.5c-.01-1.12.92-2.13 2.05-2.13h15.9c1.13 0 2.06 1.01 2.05 2.13l-.25 11.5c0 1-1.018 2-2.14 2H14.865" /><path stroke-linecap="round" stroke-linejoin="round" d="M12.52 14.5l-4.75 4.75m4.75-4.75L17.27 19.25m-4.75-4.75v-2" /></svg>',
        ),
        array(
            'post_title' => 'Bli ambassadör',
            'post_name'  => 'ambassador',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a3 3 0 100-6 3 3 0 000 6z" /></svg>',
        ),
        array(
            'post_title' => 'Blogg',
            'post_name'  => 'blog',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 9h-15m15 0a2.25 2.25 0 01-2.25 2.25H4.5A2.25 2.25 0 012.25 9m0 0a2.25 2.25 0 012.25-2.25h15m-18 4.5v5.25a2.25 2.25 0 002.25 2.25h15m-18-7.5a2.25 2.25 0 012.25-2.25h15m-18 4.5a2.25 2.25 0 002.25 2.25h15m-18-4.5v-5.25a2.25 2.25 0 002.25-2.25h15m-18 4.5a2.25 2.25 0 012.25-2.25h15m-18 4.5v5.25a2.25 2.25 0 002.25 2.25h15m-18-7.5a2.25 2.25 0 012.25-2.25h15m-18 4.5a2.25 2.25 0 002.25 2.25h15m-18 4.5v-5.25a2.25 2.25 0 002.25-2.25h15m-18 4.5a2.25 2.25 0 012.25-2.25h15m-18 4.5v5.25a2.25 2.25 0 002.25 2.25h15m-18-7.5a2.25 2.25 0 012.25-2.25h15m-18 4.5a2.25 2.25 0 002.25 2.25h15m-18 4.5v-5.25a2.25 2.25 0 002.25-2.25h15m-18 4.5a2.25 2.25 0 012.25-2.25h15m-18 4.5v5.25a2.25 2.25 0 002.25 2.25h15m-18-7.5a2.25 2.25 0 012.25-2.25h15m-18 4.5a2.25 2.25 0 002.25 2.25h15m-18 4.5v-5.25a2.25 2.25 0 002.25-2.25h15m-18 4.5a2.25 2.25 0 012.25-2.25h15m-18 4.5v5.25a2.25 2.25 0 002.25 2.25h15" /></svg>',
        ),
        array(
            'post_title' => 'Om oss',
            'post_name'  => 'om-oss',
            'icon'       => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9 9 0 100-18 9 9 0 000 18z" /><path stroke-linecap="round" stroke-linejoin="round" d="M12 12a3 3 0 100-6 3 3 0 000 6z" /></svg>',
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

/**
 * Function to delete pages created by loopis_insert_pages()
 *
 * @return void
 */
function loopis_delete_pages() {
    // Define the same unique meta key that was used during creation.
    $meta_key_to_delete = '_loopis_config_page';

    // Create a query to find all pages with the specific meta key.
    $pages_to_delete = new WP_Query(array(
        'post_type'  => 'page',
        'meta_query' => array(
            array(
                'key'   => $meta_key_to_delete,
                'value' => '1',
            ),
        ),
        'fields'     => 'ids', 
    ));

    // If there are pages to delete, loop through them and delete.
    if ($pages_to_delete->have_posts()) {
        foreach ($pages_to_delete->posts as $post_id) {
            // Force delete the page (bypass trash).
            wp_delete_post($post_id, true);
        }
    }
}
