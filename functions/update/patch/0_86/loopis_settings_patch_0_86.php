<?php
/**
* Settings patch: merges the tables fetch_warning and leave_warning into a single column: locker_full
* Change code to locker_code in lockers table
*/

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
* Includes a column for locker_full in loopis lockers, and merges and drops fetch_warning and leave_warning
* @return void
*/
function loopis_settings_0_86(){
    global $wpdb;
    // Update settings
    $table = $wpdb->prefix . 'loopis_settings';
    // Update warning in settings
    $full_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE setting_key = %s", 'locker_full_warning'));
    // if full does not exist, create it, if it does make sure its ancestors are dead
    if (!$full_exists) {
        $fetch_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE setting_key = %s", 'locker_fetch_warning'));
        // if fetch exists replace it with full and reset value, else create full and set value
        if ($fetch_exists){
            $wpdb->update(
                $table,[
                    'setting_key' => 'locker_full_warning',
                    'setting_value' =>'⚠ Det är mycket saker i skåpen just nu! <br>🐎 Hämta dina saker så snabbt som möjligt.<br> 🐌 Vänta någon dag med att lämna stora saker.',
                ],
                ['setting_key' => 'locker_fetch_warning']
            );
        }else{
            $wpdb->insert($table, [
                'setting_key' => 'locker_full_warning',
                'setting_value' =>'⚠ Det är mycket saker i skåpen just nu! <br>🐎 Hämta dina saker så snabbt som möjligt.<br> 🐌 Vänta någon dag med att lämna stora saker.',
            ]);
        }
        // if leave then no leave
        $leave_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE setting_key = %s", 'locker_leave_warning'));
        if ($leave_exists) {
            $wpdb->delete($table, ['setting_key' => 'locker_leave_warning']);
        }
    }else{
        // if fetch then no fetch
        $fetch_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE setting_key = %s", 'locker_fetch_warning'));
        if ($fetch_exists) {
            $wpdb->delete($table, ['setting_key' => 'locker_fetch_warning']);
        }
        // if leave then no leave
        $leave_exists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(*) FROM $table WHERE setting_key = %s", 'locker_leave_warning'));
        if ($leave_exists) {
            $wpdb->delete($table, ['setting_key' => 'locker_leave_warning']);
        }
    }
    // Update lockers, code from JH in loopis admin
    $table = $wpdb->prefix . 'loopis_lockers';
    $columns = $wpdb->get_col("SHOW COLUMNS FROM $table");
    $has_full = in_array('locker_full', $columns, true);
    $has_fetch = in_array('fetch_warning', $columns, true);
    $has_leave = in_array('leave_warning', $columns, true);
    $has_code = in_array('code', $columns, true);
    $has_locker_code = in_array('locker_code', $columns, true);
    
    // if no has full add locker_full
    if (!$has_full && ($has_fetch || $has_leave)) {
        $wpdb->query("ALTER TABLE $table ADD COLUMN locker_full tinyint(1) DEFAULT 0");
        $has_full = true;
    }
    // if has full and fetch or leave, make sure all positives are transfered to locker full, then delete fetch and leave
    if ($has_full && ($has_fetch || $has_leave)) {
        if ($has_fetch && $has_leave) {
            $wpdb->query("UPDATE $table SET locker_full = IF((fetch_warning + leave_warning) > 0, 1, 0)");
        } elseif ($has_fetch) {
            $wpdb->query("UPDATE $table SET locker_full = IF(fetch_warning > 0, 1, 0)");
        } else {
            $wpdb->query("UPDATE $table SET locker_full = IF(leave_warning > 0, 1, 0)");
        }

        if ($has_fetch) {
            $wpdb->query("ALTER TABLE $table DROP COLUMN fetch_warning");
        }
        if ($has_leave) {
            $wpdb->query("ALTER TABLE $table DROP COLUMN leave_warning");
        }
    }
    // update code column name to locker code
    if ($has_code && !$has_locker_code){
        $wpdb->query(
            "ALTER TABLE $table RENAME COLUMN code TO locker_code"
        );
    }
}