<?php
/**
 * Profile economy for LOOPIS user.
 *
 * Included for everyone in functions.php
 */
 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


function loopis_cat_b($slug) {
    // Get category by slug
    $category = get_category_by_slug($slug);
    // Return ID if found, otherwise return false
    return $category ? $category->term_id : false;
}



function loopis_ledger_setup(){

    loopis_ledger_create_table();

    $blog_id = get_current_blog_id();
    $paged = 1;
    $per_page = 200;

    while ( true ) {
        $user_query = new WP_User_Query( array(
            'fields' => array('ID'),
            'number' => $per_page,
            'paged'  => $paged,
        ) );
        $users = $user_query->get_results();
        if ( empty( $users ) ) {
            break;
        }
        foreach ( $users as $user ) {
            loopis_ledger_create_account( $user->ID, $blog_id );
            wp_cache_delete( $user->ID, 'users' );
        }
        $paged++;
        if ( function_exists('wp_defer_term_counting') ) {}
    }
}

function loopis_ledger_create_table(){
    global $wpdb; 

    $table_name = $wpdb->base_prefix . 'loopis_ledger';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        post_id BIGINT(20) UNSIGNED DEFAULT 0,
        blog_id BIGINT(20) UNSIGNED DEFAULT 0,
        location VARCHAR(50) NOT NULL DEFAULT 'unknown',
        event VARCHAR(50) NOT NULL DEFAULT 'custom',
        description VARCHAR(50) DEFAULT '',
        type VARCHAR(50) DEFAULT '',
        coins TINYINT NOT NULL,
        clover TINYINT DEFAULT 0,
        payment SMALLINT DEFAULT 0,
        timestamp DATETIME NOT NULL,
        PRIMARY KEY (id),
        KEY user_id_idx (user_id),
        KEY timestamp_idx (timestamp)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );
}

function loopis_ledger_create_account($uid, $blog_id){
    global $wpdb; 
    $table_name = $wpdb->base_prefix . 'loopis_ledger';
    $fetch_id = loopis_cat_b('fetched');

    $payments = get_user_meta($uid, 'wpum_payments', true);
    $balance = 0;
    $clovers = 0;
    $per_page = 200;
    if (!empty($payments) && is_array($payments)) {
        foreach ($payments as $row) {
            $payment_type  = !empty($row['wpum_payment_type'][0]['value'])
                ? $row['wpum_payment_type'][0]['value']
                : 'money';
            $payment_method  = !empty($row['wpum_payment_method'][0]['value'])
                ? $row['wpum_payment_method'][0]['value']
                : 'money';
            $coins = isset($row['wpum_received_coins'][0]['value'])
                ? (int)$row['wpum_received_coins'][0]['value']
                : 5;
            $payment = isset($row['wpum_payment_amount'][0]['value'])
                ? (int)$row['wpum_payment_amount'][0]['value']
                : 50;
            $date  = !empty($row['wpum_payment_date'][0]['value'])
                ? strtotime($row['wpum_payment_date'][0]['value'])
                : time();
            
            $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $uid,
                    'timestamp' => date('Y-m-d H:i:s', $date),
                    'description' => strtolower($payment_method),
                    'payment' =>  $payment,
                    'location' => 'digital',
                    'blog_id' => 1,
                    'event' => 'payment',
                    'type' => strtolower($payment_type),
                    'coins' => $coins,
                ),
                array(
                    '%d', 
                    '%s',
                    '%s', 
                    '%d', 
                    '%s', 
                    '%d', 
                    '%s', 
                    '%s', 
                    '%d', 
                )
            );
            $balance += $coins;
        }
    }

    $rewards = get_user_meta($uid, 'wpum_rewards', true);

    if (!empty($rewards) && is_array($rewards)) {
        foreach ($rewards as $row) {

            $stars = isset($row['wpum_received_stars'][0]['value'])
                ? (int)$row['wpum_received_stars'][0]['value']
                : 1;

            $date = !empty($row['wpum_reward_date'][0]['value'])
                ? strtotime($row['wpum_reward_date'][0]['value'])
                : time();

            $reason  = !empty($row['wpum_reward_reason'][0]['value'])
                ? $row['wpum_reward_reason'][0]['value']
                : 'unknown';
            
            $description  = !empty($row['wpum_reward_description'][0]['value'])
                ? $row['wpum_reward_description'][0]['value']
                : 'free money';

            $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $uid,
                    'timestamp' => date('Y-m-d H:i:s', $date),
                    'event' => 'reward',
                    'blog_id' => 1,
                    'location' => 'digital',
                    'coins' => $stars,
                    'type' => $reason,
                    'description' => $description,
                ),
                array(
                    '%d', 
                    '%s',
                    '%s', 
                    '%d', 
                    '%s', 
                    '%d', 
                    '%s', 
                    '%s', 
                )
            );
            $balance += $stars;
        }
    }
    $paged = 1;
    while ( true ) {
        $q = new WP_Query( array(
            'post_type' => 'post',
            'meta_key' => 'fetcher',
            'meta_value' => $uid,
            'fields' => 'ids',
            'posts_per_page' => $per_page,
            'paged' => $paged,
            'no_found_rows' => true,
        ) );
        if ( empty( $q->posts ) ) break;
        foreach ( $q->posts as $post_id ) {
            // same insert logic, but fetch only needed meta:
            $terms = wp_get_post_terms( $post_id, 'category', array( 'fields' => 'ids' ) );
            $booked_date = get_post_meta($post_id,'book_date',true);
            $fetch_date = get_post_meta($post_id,'fetch_date',true);
            $location = get_post_meta($post_id,'location',true) ?: 'unknown';
            $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $uid,
                    'post_id' => $post_id,
                    'blog_id' => $blog_id,
                    'timestamp' => date('Y-m-d H:i:s', strtotime($booked_date)),
                    'event' => 'booked',
                    'coins' => -1,
                    'location' => $location,
                ),
                array(
                    '%d', 
                    '%d',                   
                    '%d', 
                    '%s',
                    '%s', 
                    '%d', 
                    '%s', 
                )
            );
            $balance += -1;
            if (!empty($fetch_date) && in_array($fetch_id, $terms)){
                $wpdb->insert(
                    $table_name,
                    array(
                        'user_id' => $uid,
                        'post_id' => $post_id,
                        'blog_id' => $blog_id,
                        'timestamp' => date('Y-m-d H:i:s', strtotime($fetch_date)),
                        'event' => 'fetched',
                        'coins' => 0,
                        'clover' => 1,
                        'location' => $location,
                    ),
                    array(
                        '%d', 
                        '%d',                   
                        '%d', 
                        '%s',
                        '%s', 
                        '%d', 
                        '%d',
                        '%s',
                    )
                );
                $clovers += 1;
            }
        }
        $paged++;
        wp_reset_postdata();
    }

    
    $paged = 1;
    while ( true ) {
         wp_reset_postdata();
        $q = new WP_Query( array(
            'post_type' => 'post',
            'author' => $uid,
            'fields' => 'ids',
            'posts_per_page' => $per_page,
            'paged' => $paged,
            'no_found_rows' => true,
        ) );
        $paged++;
        if ( empty( $q->posts ) ) break;
        foreach ( $q->posts as $post_id ) {
            $remove_date = get_post_meta($post_id, 'remove_date', true);
            $fetch_date  = get_post_meta($post_id, 'fetch_date', true);
            $fetcher = get_post_meta($post_id, 'fetcher', true);
            $post_date = get_post_field('post_date', $post_id);
            $location  = get_post_meta($post_id, 'location', true) ?? 'unknown';
            $type = filter_var(get_post_meta($post_id,'previous_post',true), FILTER_VALIDATE_INT) ? 'forwarded' : ''; 
            if (!empty($fetch_date)&&!empty($fetcher)){
                $coins = 1;
                $date = strtotime($fetch_date);
                $event = 'given';
                $wpdb->insert(
                    $table_name,
                    array(
                        'user_id' => $uid,
                        'post_id' => $post_id,
                        'blog_id' => $blog_id,
                        'timestamp' => date('Y-m-d H:i:s', $date),
                        'event' => $event,
                        'coins' => $coins,
                        'location' => $location,
                    ),
                    array(
                        '%d',                     
                        '%d',                   
                        '%d', 
                        '%s',
                        '%s', 
                        '%d', 
                        '%s',
                    )
                );
                $balance += $coins;
            }
            if (!empty($remove_date)) {
                $coins = 0;
                $date = strtotime($remove_date);
                $event = 'removed';
                $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $uid,
                    'post_id' => $post_id,
                    'blog_id' => $blog_id,
                    'timestamp' => date('Y-m-d H:i:s', $date),
                    'event' => $event,
                    'coins' => $coins,
                    'location' => $location,
                ),
                array(
                    '%d',                     
                    '%d',                   
                    '%d', 
                    '%s',
                    '%s', 
                    '%d', 
                    '%s',
                )
            );
            }
            $coins = 0;
            $date = strtotime($post_date);
            $event = 'submitted';
            $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $uid,
                    'post_id' => $post_id,
                    'blog_id' => $blog_id,
                    'timestamp' => date('Y-m-d H:i:s', $date),
                    'event' => $event,
                    'coins' => $coins,
                    'clover' => 1,
                    'location' => $location,
                    'type' => $type,
                ),
                array(
                    '%d',                     
                    '%d',                   
                    '%d', 
                    '%s',
                    '%s', 
                    '%d', 
                    '%d',
                    '%s',
                    '%s',
                )
            );
            $clovers += 1;
        }
    }
    $balance += floor($clovers/10);
    update_user_meta($uid, 'loopis_clovers', $clovers);
    update_user_meta($uid, 'loopis_balance', $balance);
}

