<?php
/**
 * Plugin Name: WPUM category fix
 * Description: Stops the category from resetting on edit.
 */

function is_post_new(int $post_id){
    $post = get_post($post_id);
    // no post yet? new
    if (!$post) return true;
    return ($post->post_date===$post->post_modified); // the initial date and last modification is the same = new, else old
}


// Save the cats!
add_action('wpumfr_before_post_end', function($post_id){
    // Don't do anything in case of new post 
    if (is_post_new($post_id)) return;
    // Get the cats
    $cats = wp_get_post_terms($post_id,'category', ['fields'=>'ids']);
    // Put em in the box
    if ($cats) update_post_meta($post_id, '_cat_box', $cats);
}, 10, 1);

// Bring the cats back #petsemetary
add_action('wpumfr_after_post', function($post_id){
    // Don't do anything in case of new post
    if (is_post_new($post_id)) return;
    // Open the box and get the cats
    $cats = get_post_meta($post_id, '_cat_box', true);
    // Put the cats back in the house
    if ($cats) wp_set_post_terms($post_id, $cats, 'category', false);
    // Put the box in the trash
    delete_post_meta($post_id, '_cat_box');
}, 10, 1);