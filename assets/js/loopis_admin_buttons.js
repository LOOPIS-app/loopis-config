/*
    This is a button handler script, it uses ajax as a $_POST fetcher proxy.
*/


// Jquery for 'do when the document is ready and loaded'
jQuery(document).ready(function ($) {

    // =========================
    // LOOPIS Setup / Cleanup
    // =========================

    // All function calls and category ids, ordered by key
    const All_functions = { 
        'Setup': [
        ['loopis_settings_create','loopis_settings'],
        ['loopis_settings_insert','loopis_settings'],
        ['loopis_lockers_create','loopis_lockers'],
        ['loopis_pages_insert','loopis_pages'],
        ['loopis_cats_insert','loopis_cats'],
        ['loopis_tags_insert','loopis_tags'],
        ['loopis_user_roles_set','loopis_user_roles'],
        ['loopis_users_insert','loopis_users'],
        ['loopis_plugins_delete','remove_plugins'],
        ['loopis_plugins_install','install_plugins'],
        ['','install_root_files'],
        ['loopis_wp_options_set','wp_options'],
        ['loopis_wp_screen_options_set','wp_screen_options'],
        ],
        'Cleanup': [
        ['loopis_plugins_cleanup','plugins'],
        ['loopis_users_delete','users'],
        ['loopis_user_roles_delete','user_roles'],
        ['loopis_tags_delete','tags'],
        ['loopis_categories_delete','categories'],
        ['loopis_pages_delete','pages'],
        ['loopis_admin_cleanup','databas'],
        ]
    };

    //====== stepFunction: main cleanup & setup function ======

    // Regressive ajax $_POST submission function 
    function stepFunction(key,index) {

        // Check if list ended
        if (index >= All_functions[key].length) {
            refreshRolesDisplay() // done at the end of all steps
            logToPhp(`=== End: Database ${key}! ===`);
            return
        } else if(index==0){
            logToPhp(`=== Start: Database ${key}! ===`);
        }

        // Define id and func_step
        const func_step = All_functions[key][index][0];
        const id = All_functions[key][index][1];

        // Set current step to 🔄 Running!
        $(`td[data-step='${id}'] .status`).html('🔄 Running!');

        // Do post with loopis ajax
        $.post(loopis_ajax.ajax_url, { 
            action: 'loopis_sp_handle_actions',               // Do php function loopis_sp_handle_actions
            nonce: loopis_ajax.nonce,                         // With our nonce
            func_step: func_step,                             // our function
            id: id                                            // and function id
        }, function (response) {                              // Afterwards
            const data = response.data;                       // Read the status JSON brought
            $(`td[data-step='${data.id}'] .status`).html(data.status);   // and set the status 

            // Check if JSON says success
            if (response.success) {

                // Continue to next step
                stepFunction(key, index + 1);
            } else {

                // Stop on error
                logToPhp(`=== End: Database ${key}! ===`);
            }
        });
    }

    //====== stepFunction subfunctions ======
    
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

    //====== Button Listeners ======
    
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

    // =========================
    // LOOPIS roles page
    // =========================

    // Safe binding for roles toggle
    const $toggleBtn = $('#toggle_debug_roles');
    const $refreshBtn = $('#refresh_debug_roles');
    const $container = $('#debug_roles_container');

    // Hides refresh and container on click using JQuery
    $toggleBtn.on('click', function () {
        const isHidden = $container.is(':hidden'); 
        $container.toggle(isHidden);
        $refreshBtn.toggle(isHidden);
        $toggleBtn.text(isHidden ? '🔐 Hide User Roles & Capabilities' : '🔐 View All User Roles & Capabilities');
    });

    // Runs refreshRolesDisplay on click
    $refreshBtn.on('click', refreshRolesDisplay);
});
