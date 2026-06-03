<?php
/**
 * Profile economy for LOOPIS user.
 *
 * Included for everyone in functions.php
 */
 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function loopis_ledger_setup(){

    loopis_ledger_create_table();

    $users = get_users(array('fields' => array('ID')));
    $blog_id = get_current_blog_id();

    foreach ($users as $user) {
        $uid = $user->ID;
        loopis_ledger_create_account($uid,$blog_id);
    }
}

function loopis_ledger_create_table(){
    global $wpdb; 

    $table_name = $wpdb->base_prefix . 'loopis_ledger';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        post_id BIGINT(20) UNSIGNED DEFAULT NULL,
        blog_id BIGINT(20) UNSIGNED DEFAULT NULL,
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

    $payments = get_user_meta($uid, 'wpum_payments', true);
    $balance = 0;
    $clovers = 0;

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
                    'description' => $payment_method,
                    'payment' =>  $payment,
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
                    'coins' => $stars,
                    'type' => $reason,
                    'description' => $description,
                ),
                array(
                    '%d', 
                    '%s',
                    '%s', 
                    '%d', 
                )
            );
            $balance += $stars;
        }
    }

    $booked_posts = get_posts(array(
        'post_type' => 'post',
        'meta_key' => 'fetcher',
        'meta_value' => $uid,
        'fields' => 'ids',
        'posts_per_page' => -1,
    ));
    
    foreach ($booked_posts as $post_id) {
        $booked_date  = get_post_meta($post_id, 'book_date', true);
        $fetch_date  = get_post_meta($post_id, 'fetch_date', true);
        $wpdb->insert(
            $table_name,
            array(
                'user_id' => $uid,
                'post_id' => $post_id,
                'blog_id' => $blog_id,
                'timestamp' => date('Y-m-d H:i:s', strtotime($booked_date)),
                'event' => 'booked',
                'coins' => -1,
            ),
            array(
                '%d', 
                '%d',                   
                '%d', 
                '%s',
                '%s', 
                '%d', 
            )
        );
        $balance += -1;
        if (!empty($fetch_date)){
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
                ),
                array(
                    '%d', 
                    '%d',                   
                    '%d', 
                    '%s',
                    '%s', 
                    '%d', 
                    '%d',
                )
            );
            $clovers += 1;
        }
    }

    $submitted_posts = get_posts(array(
        'post_type' => 'post',
        'author' => $uid,
        'fields' => 'ids',
        'posts_per_page' => -1,
    ));

    foreach ($submitted_posts as $post_id) {
        $remove_date = get_post_meta($post_id, 'remove_date', true);
        $fetch_date  = get_post_meta($post_id, 'fetch_date', true);
        $post_date = get_post_field('post_date', $post_id);

        if (!empty($fetch_date)){
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
                ),
                array(
                    '%d',                     
                    '%d',                   
                    '%d', 
                    '%s',
                    '%s', 
                    '%d', 
                )
            );
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
            ),
            array(
                '%d',                     
                '%d',                   
                '%d', 
                '%s',
                '%s', 
                '%d', 
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
            ),
            array(
                '%d',                     
                '%d',                   
                '%d', 
                '%s',
                '%s', 
                '%d', 
                '%d'
            )
        );
        $clovers += 1;
        $balance += $coins;
    }
    $balance += floor($clovers/10);
    update_user_meta($uid, 'loopis_clovers', $clovers);
    update_user_meta($uid, 'loopis_balance', $balance);
}

