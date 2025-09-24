/*
    This is a button handler script, it uses ajax as a $_POST fetcher proxy.
*/

// JS for 'do when the document is ready and loaded'
jQuery(document).ready(function ($) {

    const All_functions = { 'Setup': [
        ['loopis_settings_create','loopis_settings'],
        ['loopis_settings_insert','loopis_settings'],
        ['loopis_lockers_create','loopis_lockers'],
        ['loopis_pages_insert','loopis_pages'],
        ['loopis_categories_insert','loopis_categories'],
        ['loopis_tags_insert','loopis_tags'],
        ['loopis_users_insert','loopis_users'],
        ['loopis_wp_options_change','wp_options'],
        ['','install_root_files'],
        ['loopis_plugins_delete','remove_plugins'],
        ['loopis_plugins_install','install_plugins'],
    ],
    'Cleanup': [
        ['loopis_plugins_cleanup','plugins'],
        ['loopis_users_delete','users'],
        ['loopis_tags_delete','tags'],
        ['loopis_categories_delete','categories'],
        ['loopis_pages_delete','pages'],
        ['loopis_admin_cleanup','databas'],
    ]};
    

    
    // Regressive ajax $_POST submission function 
    function stepFunction(key,index) {
        // Check if list ended
        if (index >= All_functions[key].length) {
            logToPhp(`=== End: Database ${key}! ===`);
            return
        } else if(index==0){
            logToPhp(`=== Start: Database ${key}! ===`);
        }

        //Define id and func_step
        const func_step = All_functions[key][index][0];
        const id = All_functions[key][index][1];


        $(`td[data-step='${All_functions[key][index][1]}'] .status`).html('🔄 Running!');

        // Do post with loopis ajax
        $.post(loopis_ajax.ajax_url, {
            action: 'loopis_sp_handle_actions',     // Do php function loopis_sp_handle_actions
            nonce: loopis_ajax.nonce,               // With our nonce
            button_id: buttonId                     // and our button ID(from the listener)
        }, function (response) {                    // Afterwards
            const data = JSON.parse(response);      // Read the statuses JSON brought
            for (const id in data.statuses) {       // and set the statuses on the corresponding data-step
                $(`td[data-step="${id}"] .status`).html(data.statuses[id]);
            }
        });
    }
    
    // Function to refresh user roles display data
    function refreshRolesDisplay() {
        const rolesContainer = $('#debug_roles_container');
        if (rolesContainer.is(':visible')) {
            // Reload the roles display data via AJAX
            $.post(loopis_ajax.ajax_url, {
                action: 'loopis_refresh_roles_display',
                nonce: loopis_ajax.nonce
            }, function (response) {
                rolesContainer.html(response);
            });
        }
    }

    // PHP Error logger
    function logToPhp(error_log) {
        $.post(loopis_ajax.ajax_url, {
            action: 'loopis_log_message',
            nonce: loopis_ajax.nonce,
            error_log: error_log
        });
    }

    // PHP status clearer
    function clearStatus() {
        $.post(loopis_ajax.ajax_url, {
            action: 'loopis_sp_clear_step_status',
            nonce: loopis_ajax.nonce,
        });
    }

    
    // Setup button listener
    $('#run_loopis_config_installation').on('click', function () {
        $('.status').html('⬜');
        clearStatus()
        stepFunction('Setup',0);
    });

    // Cleanup button listener
    $('#run_loopis_db_cleanup').on('click', function () {
        $('.status').html('⬜');
        clearStatus()
        stepFunction('Cleanup',0);
    });
});
