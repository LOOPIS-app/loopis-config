<?php
/**
 * Profile economy for LOOPIS user.
 *
 * Included for everyone in functions.php
 */
 
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

function get_all_transactions() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'loopis_ledger';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT(20) UNSIGNED NOT NULL,
        post_id BIGINT(20) UNSIGNED DEFAULT NULL,
        timestamp DATETIME NOT NULL,
        delta TINYINT NOT NULL,
        type VARCHAR(50) NOT NULL DEFAULT 'Nan',
        payment_type VARCHAR(50) DEFAULT NULL,
        balance SMALLINT DEFAULT 0,
        clovers TINYINT DEFAULT 0,
        PRIMARY KEY (id),
        KEY user_id_idx (user_id),
        KEY timestamp_idx (timestamp)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql );

    $users = get_users(array('fields' => array('ID')));

    foreach ($users as $user) {

        $uid = $user->ID;
        $payments = get_user_meta($uid, 'wpum_payments', true);

        if (!empty($payments) && is_array($payments)) {
            foreach ($payments as $row) {

                $payment_type  = !empty($row['wpum_payment_type'][0]['value'])
                    ? $row['wpum_payment_type'][0]['value']
                    : 'money';
                $coins = isset($row['wpum_received_coins'][0]['value'])
                    ? (int)$row['wpum_received_coins'][0]['value']
                    : 5;
                $date  = !empty($row['wpum_payment_date'][0]['value'])
                    ? strtotime($row['wpum_payment_date'][0]['value'])
                    : time();
                
                $wpdb->insert(
                    $table_name,
                    array(
                        'user_id' => $uid,
                        'timestamp' => date('Y-m-d H:i:s', $date),
                        'payment_type' => $payment_type,
                        'type' => 'Payment',
                        'delta' => $coins,
                    ),
                    array(
                        '%d', 
                        '%s',
                        '%s', 
                        '%s', 
                        '%d', 
                    )
                );
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

                $wpdb->insert(
                    $table_name,
                    array(
                        'user_id' => $uid,
                        'timestamp' => date('Y-m-d H:i:s', $date),
                        'type' => 'Reward',
                        'delta' => $stars,
                    ),
                    array(
                        '%d', 
                        '%s',
                        '%s', 
                        '%d', 
                    )
                );
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
            $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $uid,
                    'post_id' => $post_id,
                    'timestamp' => date('Y-m-d H:i:s', strtotime($booked_date)),
                    'type' => 'Booking',
                    'delta' => -1,
                ),
                array(
                    '%d', 
                    '%d', 
                    '%s',
                    '%s', 
                    '%d', 
                )
            );
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
                $delta = 1;
                $date = strtotime($fetch_date);
                $type = 'Given';
            } elseif (!empty($remove_date)) {
                $delta = 0;
                $date = strtotime($remove_date);
                $type = 'Removed';
            }else{
                $delta = 0;
                $date = strtotime($post_date);
                $type = 'Submitted';
            }
            $wpdb->insert(
                $table_name,
                array(
                    'user_id' => $uid,
                    'post_id' => $post_id,
                    'timestamp' => date('Y-m-d H:i:s', $date),
                    'type' => $type,
                    'delta' => $delta,
                ),
                array(
                    '%d',                     
                    '%d', 
                    '%s',
                    '%s', 
                    '%d', 
                )
            );

        }
    }
}

function build_loopis_ledger() {
    global $wpdb;
    $users = get_users(array('fields' => array('ID')));
    $table_name = $wpdb->prefix . 'loopis_ledger';

    foreach ($users as $user) {
        $uid = $user->ID;
        $transactions = $wpdb->get_results(
            $wpdb->prepare(
                    "SELECT `id`,`type`, `delta`,`timestamp` FROM $table_name WHERE `user_id`= %d ORDER BY `timestamp` ASC",
                $uid
            ), ARRAY_A
        );
        $balance = 0;
        $clover = 0;
        foreach ($transactions as $t){
            $balance += $t['delta'];

            if (in_array($t['type'], array('Given','Submitted','Removed','Booking'))) {
                $clover++;
                $wpdb->update(
                    $table_name,
                    array(
                        'balance' => $balance,
                        'clovers' => $clover,
                    ),
                    array(
                        'id' => $t['id'],
                    )
                );
                if ($clover >= 10) {
                    $balance += 1;
                    $clover -= 10;
                    $wpdb->insert(
                        $table_name,
                        array(
                            'user_id' => $uid,
                            'timestamp' => $t['timestamp'],
                            'type' => 'Clover',
                            'delta' => 1,
                            'balance' => $balance,
                            'clovers' => $clover,
                        ),
                        array(
                            '%d', 
                            '%s',
                            '%s', 
                            '%d',
                            '%d', 
                            '%d', 
                        )
                    );
                }
            } else{
                $wpdb->update(
                    $table_name,
                    array(
                        'balance' => $balance,
                        'clovers' => $clover,
                    ),
                    array(
                        'id' => $t['id'],
                    )
                );
            }
        }
        update_user_meta($uid, 'loopis_balance', $balance);
        update_user_meta($uid, 'loopis_clovers', $clover);
    }
}

