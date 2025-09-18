<?php
/**
 * Code For generating the loopis config setup page, migrate if admin menu diversifies w/ submenus
 * 
 * Warning: Contains development cleanup tool, to be used with caution and iff in a safe development enviroment.
 * 
 */

//Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

// Setup-page setup
function loopis_config_setup_page() {
    // Handle form actions early
    loopis_sp_handle_actions();
    ?>
    <div class="wrap">
        <h1>Plugin Setup</h1>
        
        <p>Här får ni en överblick på de olika momenten i er loopis installation:</p>

        <form method="post" action="">
            <?php wp_nonce_field('loopis_config_nonce', 'loopis_config_nonce_field'); ?>
            
            <p>Kör installation: 
                <input type="submit" name="run_loopis_config_installation" class="button button-primary" value="Installera!" />
            </p> 

            <div class="status-grid">
                <p>Databas loopis_settings: </p><p><span class="status"><?php echo loopis_sp_get_step_status('loopis_settings'); ?></span></p>
                <p>Databas loopis_lockers: </p><p><span class="status"><?php echo loopis_sp_get_step_status('loopis_lockers'); ?></span></p>
                <p>LOOPIS pages: </p><p><span class="status"><?php echo loopis_sp_get_step_status('loopis_pages'); ?></span></p>
                <p>LOOPIS categories: </p><p><span class="status"><?php echo loopis_sp_get_step_status('loopis_categories'); ?></span></p>
                <p>LOOPIS tags: </p><p><span class="status"><?php echo loopis_sp_get_step_status('loopis_tags'); ?></span></p>
                <p>LOOPIS users: </p><p><span class="status"><?php echo loopis_sp_get_step_status('loopis_users'); ?></span></p>
                <p>WordPress options: </p><p><span class="status"><?php echo loopis_sp_get_step_status('wp_options'); ?></span></p>
                <p>Ta bort plugins: </p><p><span class="status"><?php echo loopis_sp_get_step_status('remove_plugins'); ?></span></p>
                <p>Installera plugins: </p><p><span class="status"><?php echo loopis_sp_get_step_status('install_plugins'); ?></span></p>
                <p>Installera root files: </p><p><span class="status"><?php echo loopis_sp_get_step_status('install_root_files'); ?></span></p>
            </div>

                <p>Rensa Loopis: 
                    <input type="submit" name="run_loopis_db_cleanup" class="button button-primary" value="Städa bort!" />
                </p> 

            <div class="status-grid">
                <p>Users:</p><p> <span class="status"><?php echo loopis_sp_get_step_status('users'); ?></span></p>
                <p>Tags:</p><p> <span class="status"><?php echo loopis_sp_get_step_status('tags'); ?></span></p>
                <p>Categories:</p><p> <span class="status"><?php echo loopis_sp_get_step_status('categories'); ?></span></p>
                <p>Pages:</p><p> <span class="status"><?php echo loopis_sp_get_step_status('pages'); ?></span></p>
                <p>Databas:</p><p> <span class="status"><?php echo loopis_sp_get_step_status('databas'); ?></span></p>
            </div>
        </form>
    <?php
}


// Button handler
function loopis_sp_handle_actions() {
    // Nonce filter
    if (isset($_POST['loopis_config_nonce_field']) && wp_verify_nonce($_POST['loopis_config_nonce_field'], 'loopis_config_nonce')) {
        //Function-status_id pairs in order, add function calls as they are finshed
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

        //Clear all statuses on each button press
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
    //Calls functions in order, executes if possible, otherwise stops process
    foreach($list as [$function,$id]){
        //Checks if there exists a function call for the step, delete whenever pertinent
        if (!$function){
            loopis_sp_set_step_status($id, 'Nan');
            continue;
        }
        //Attempts function call, breaks setup and logs/displays errormessage upon error, status set accordingly
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
        return '✅ Ok!';
    } elseif ($status === 'Nan') {
        return '⬜ Saknar funktion!';
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
