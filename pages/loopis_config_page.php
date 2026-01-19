<?php
/**
 * WP Admin page for configuring the WordPress installation.
 * 
 * @package LOOPIS_Config
 * @subpackage Admin-page
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

// Include functions
require_once LOOPIS_CONFIG_DIR . 'functions/loopis_config_functions.php';
require_once LOOPIS_CONFIG_DIR . 'functions/loopis_db_setup.php';

// Enqueue scripts
function loopis_config_enqueue_scripts($hook) {
    // Enqueue JS file
    wp_enqueue_script(
        'loopis_config_buttons_js',
        LOOPIS_CONFIG_URL . 'assets/js/loopis_config_buttons.js',
        ['jquery'],
        '1.0',
        true 
    );

    // Get cached table data
    $config = wp_cache_get('loopis_config_data', 'loopis');
    $table_install = array_filter($config, fn($r) => $r['Category'] === 'Install');
    $table_preinstall = array_filter($config, fn($r) => $r['Category'] === 'Component');

    // Build Setup_functions array for JS
    $setup_functions = [];
    foreach ($table_install as $row) {
        $setup_functions[] =[
            'func_step' =>$row['Config_function'],
            'ID' => $row['ID'],
            'cdata'=> !empty($row['Config_Data']) ? json_decode($row['Config_Data'], true) : []
        ];
    }
    
    $preinstall_data = [];
    foreach ($table_preinstall as $row) {
        $preinstall_data[] = array_merge(['ID' => $row['ID']], json_decode($row['Config_Data'], true));
    }
    $installed = get_option('loopis_config_version') ? true : false;
    $outofdate = LOOPIS_CONFIG_VERSION !== get_option('loopis_config_version');

    // Ajax + dynamic functions localization
    wp_localize_script('loopis_config_buttons_js', 'loopis_ajax', [
        'ajax_url'        => admin_url('admin-ajax.php'),
        'nonce'           => wp_create_nonce('loopis_config_nonce'),
        'setup_functions' => $setup_functions, 
        'preinstall_data' => $preinstall_data,
        'version' => LOOPIS_CONFIG_VERSION,
        'outofdate' => $outofdate,
        'installed' => $installed,
    ]);
}

// Button updater auxillary 
add_action('wp_ajax_loopis_get_status', function () {   
    $installed = get_option('loopis_config_version') ? true : false;
    $outofdate = LOOPIS_CONFIG_VERSION !== get_option('loopis_config_version');

    wp_send_json_success([
        'installed' => $installed,
        'outofdate' => $outofdate,
    ]);
});

// Config js hook
add_action('admin_enqueue_scripts', 'loopis_config_enqueue_scripts');

// Ajax handler hooks
add_action('wp_ajax_loopis_sp_handle_actions', 'loopis_sp_handle_actions');
add_action('wp_ajax_loopis_log_message', 'loopis_log_message');
add_action('wp_ajax_loopis_sp_update_handler', 'loopis_sp_update_handler');
add_action('admin_post_activate_plugins', 'loopis_sp_activate_plugins_handler');

/**
 * Renders loopis config page.
 * 
 * @return void
 */
function loopis_config_page() {
    $config =  wp_cache_get('loopis_config_data', 'loopis');
    $table_init = array_filter($config, fn($r) => $r['Category'] === 'Initialization');
    $table_preinstall = array_filter($config, fn($r) => $r['Category'] === 'Component');
    $table_install = array_filter($config, fn($r) => $r['Category'] === 'Install');
    ?>
    <div class="wrap">
        <!-- Page title and description-->
        <h1>⚙ LOOPIS Config <span class="h1-right">Version <?php echo esc_html(LOOPIS_CONFIG_VERSION); ?></span></h1>
        <p class="description">💡 This is where you configure (and update) a WordPress installation for LOOPIS.</p>

        <!-- Page content-->
        <h2>Konfigurera WordPress</h2>
        <p>  
            <button id="run_loopis_config_installation" class="button button-primary" value="Start" disabled>Install loopis</button>
            <button id="run_loopis_config_update" class="button button-primary" value="Update" disabled>Update</button>
        </p> 

        <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="manage-column">Unit</th>
                <th scope="col" class="manage-column">Location</th>
                <th scope="col" class="manage-column">Status</th>
                <th scope="col" class="manage-column">Version</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($table_init as $row): ?>
                <tr>
                    <td class="column-unit"><?php echo htmlspecialchars($row['Unit']); ?></td>
                    <td class="column-place"><?php echo htmlspecialchars($row['Place']); ?></td>
                    <td class="column-status" data-step="<?php echo htmlspecialchars($row['ID']); ?>">
                        <span class="status"> <?php echo loopis_sp_get_status_text($row['Config_Status']); ?> </span>
                    </td>
                    <td class="column-version" data-step="<?php echo htmlspecialchars($row['ID']); ?>">
                    <span class="version"><?php echo htmlspecialchars($row['Config_Version']); ?></span>
                    </td>
                </tr>
        <?php endforeach; ?>
        <?php foreach ($table_install as $row): ?>
                <tr>
                    <td class="column-unit"><?php echo htmlspecialchars($row['Unit']); ?></td>
                    <td class="column-place"><?php echo htmlspecialchars($row['Place']); ?></td>
                    <td class="column-status" data-step="<?php echo htmlspecialchars($row['ID']); ?>">
                    <span class="status"> <?php echo loopis_sp_get_status_text($row['Config_Status']); ?> </span>
                    </td>
                    <td class="column-version" data-step="<?php echo htmlspecialchars($row['ID']); ?>">
                    <span class="version"><?php echo htmlspecialchars($row['Config_Version']); ?></span>
                    </td>
                </tr>
        <?php endforeach; ?>
        <?php foreach ($table_preinstall as $row): ?>
                <tr>
                    <td class="column-unit"><?php echo htmlspecialchars($row['Unit']); ?></td>
                    <td class="column-place"><?php echo htmlspecialchars($row['Place']); ?></td>
                    <td class="column-status" data-step="<?php echo htmlspecialchars($row['ID']); ?>">
                        <span class="status"> <?php echo loopis_sp_get_status_text($row['Config_Status']); ?> </span>
                    </td>
                    <td class="column-version" data-step="<?php echo htmlspecialchars($row['ID']); ?>">
                    <span class="version"><?php echo htmlspecialchars($row['Config_Version']); ?></span>
                    </td>
                </tr>
        <?php endforeach; ?>
        </tbody>
</table>
    </div>
    <?php 
}