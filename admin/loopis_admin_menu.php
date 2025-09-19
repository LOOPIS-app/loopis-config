<?php
/**
 * WP Admin page for configuring a new WordPress installation before installing LOOPIS.
 * 
 * Migrate if admin menu diversifies w/ submenus.
 * 
 * WARNING! The cleanup tool is intended for development purposes only.
 * Use with caution and only in a safe development environment!
 * 
 * @package LOOPIS_Config
 * @subpackage Admin-page
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

// Function to render the page
function loopis_config_setup_page() {
    // Handle form actions early
    loopis_sp_handle_actions();
    ?>
    <div class="wrap">

        <!-- Page title and description-->
        <h1>⚙ LOOPIS Config</h1>
        <p class="description">💡 Här konfigurerar du en ny WordPress-installation inför installation av LOOPIS.</p>

        <!-- Page content-->
        <h2>Konfigurera WordPress</h2>
        <form method="post" action="">
            <?php wp_nonce_field('loopis_config_nonce', 'loopis_config_nonce_field'); ?>
            
            <p>
                <input type="submit" name="run_loopis_config_installation" class="button button-primary" value="Starta" />
            </p> 

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column">Komponent</th>
                        <th scope="col" class="manage-column">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="column-component">LOOPIS DB (loopis_settings):</td>
                        <td class="column-status"><span class="status"><?php echo loopis_sp_get_step_status('loopis_settings'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS DB (loopis_lockers):</td>
                        <td class="column-status"><span class="status"><?php echo loopis_sp_get_step_status('loopis_lockers'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS pages:</td>
                        <td class="column-status"><span class="status"><?php echo loopis_sp_get_step_status('loopis_pages'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS categories:</td>
                        <td class="column-status"><span class="status"><?php echo loopis_sp_get_step_status('loopis_categories'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS tags:</td>
                        <td class="column-status"><span class="status"><?php echo loopis_sp_get_step_status('loopis_tags'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS users:</td>
                        <td class="column-status"><span class="status"><?php echo loopis_sp_get_step_status('loopis_users'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">WordPress options:</td>
                        <td class="column-status"><span class="status"><?php echo loopis_sp_get_step_status('wp_options'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">Delete default plugins:</td>
                        <td class="column-status"><span class="status"><?php echo loopis_sp_get_step_status('remove_plugins'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">Install new plugins:</td>
                        <td class="column-status"><span class="status"><?php echo loopis_sp_get_step_status('install_plugins'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">Install WordPress root files:</td>
                        <td class="column-status"><span class="status"><?php echo loopis_sp_get_step_status('install_root_files'); ?></span></td>
                    </tr>
                </tbody>
            </table>
            
            <p>&nbsp;</p> <!-- Spacer -->

            <h2>Återställ WordPress</h2>
            <p class="description">⚠ Varning! Endast avsedd för test i utvecklingsmiljö.</p>
            <p>
                <input type="submit" name="run_loopis_db_cleanup" class="button button-primary" value="Återställ" />
            </p>

            <table class="wp-list-table widefat fixed striped">
                <thead>
                    <tr>
                        <th scope="col" class="manage-column">Komponent</th>
                        <th scope="col" class="manage-column">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="column-component">LOOPIS DB:</td>
                        <td class="column-status"><span class="status"><?php echo loopis_sp_get_step_status('databas'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS pages:</td>
                        <td class="column-status"><span class="status"><?php echo loopis_sp_get_step_status('pages'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS categories:</td>
                        <td class="column-status"><span class="status"><?php echo loopis_sp_get_step_status('categories'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS tags:</td>
                        <td class="column-status"><span class="status"><?php echo loopis_sp_get_step_status('tags'); ?></span></td>
                    </tr>
                    <tr>
                        <td class="column-component">LOOPIS users:</td>
                        <td class="column-status"><span class="status"><?php echo loopis_sp_get_step_status('users'); ?></span></td>
                    </tr>
                </tbody>
            </table>
        </form>
    </div>
    <?php
}

// Button handler
function loopis_sp_handle_actions() {
    // Nonce filter
    if (isset($_POST['loopis_config_nonce_field']) && wp_verify_nonce($_POST['loopis_config_nonce_field'], 'loopis_config_nonce')) {
        // Function-status_id pairs in order, add function calls as they are finshed
        $setup_functions = [
            ['loopis_settings_create','loopis_settings'],
            ['loopis_settings_insert','loopis_settings'],
            ['loopis_lockers_create','loopis_lockers'],
            ['loopis_pages_insert','loopis_pages'],
            ['','loopis_categories'],
            ['loopis_tags_insert','loopis_tags'],
            ['','loopis_users'],
            ['','wp_options'],
            ['loopis_plugins_delete','remove_plugins'],
            ['','install_plugins'],
            ['','install_root_files']
        ];
        $cleanup_functions = [
            ['','users'],
            ['loopis_tags_delete','tags'],
            ['','categories'],
            ['loopis_pages_delete','pages'],
            ['loopis_admin_cleanup','databas'],
        ];

        // Clear all statuses on each button press
        foreach($setup_functions as [$function,$id]){
            loopis_sp_set_step_status($id, '');
        }
        foreach($cleanup_functions as [$function,$id]){
            loopis_sp_set_step_status($id, '');
        }

        // Setup button
        if (isset($_POST['run_loopis_config_installation'])) {
            error_log('=== Start: Database Setup! ===');
            loopis_sp_run_funcidlist($setup_functions);
            error_log('=== End: Database Setup! ===');
        }

        // Cleanup button
        if (isset($_POST['run_loopis_db_cleanup'])) {
            error_log('=== Start: Database Cleanup! ===');
            loopis_sp_run_funcidlist($cleanup_functions);
            error_log('=== End: Database Cleanup! ===');

        }
    }
}

// Run function-id list
function loopis_sp_run_funcidlist($list){
    // Calls functions in order, executes if possible, otherwise stops process
    foreach($list as [$function,$id]){
        // Checks if there exists a function call for the step, delete whenever pertinent
        if (!$function){
            loopis_sp_set_step_status($id, 'Nan');
            continue;
        }
        // Attempts function call, breaks setup and logs/displays errormessage upon error, status set accordingly
        try {
            $function(); 
            loopis_sp_set_step_status($id, 'Ok');
        } catch (Throwable $e) {
            echo "Caught error in function call {$function} :  {$e->getMessage()}";
            error_log("Error in function call {$function}:  {$e->getMessage()}");
            error_log('Terminating process.');
            loopis_sp_set_step_status($id, 'Error');  
            break;
        }
    }
}

// Get step status
function loopis_sp_get_step_status($step) {
    $status = get_option('loopis_step_status_' . $step, 'not_finished');
    if ($status === 'Ok') {
        return '✅ OK!';
    } elseif ($status === 'Nan') {
        return '⬜ Funktion saknas.';
    } elseif ($status === 'Error') {
        return '⚠️ Fel! Kunde inte köras.';
    } else {
        return '⬜';
    }
}

// Set step status
function loopis_sp_set_step_status($step, $status) {
    update_option('loopis_step_status_' . $step, $status);
}