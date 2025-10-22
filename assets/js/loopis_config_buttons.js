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
        ['','loopis_config_table'],
        ['loopis_settings_create','loopis_settings_table'],
        ['loopis_settings_insert','loopis_settings_insert'],
        ['loopis_lockers_create','loopis_lockers_table'],
        ['loopis_pages_insert','loopis_pages'],
        ['loopis_cats_insert','loopis_cats'],
        ['loopis_tags_insert','loopis_tags'],
        ['loopis_roles_set','loopis_roles'],
        ['loopis_admins_insert','loopis_admins'],
        ['loopis_wp_plugins_delete','wp_plugins'],
        ['loopis_plugins_install','loopis_plugins'],
        ['','install_root_files'],
        ['loopis_wp_options_set','wp_options'],
        ['loopis_wp_screen_options_set','wp_screen_options'],
        ],
        'Cleanup': [
        ['loopis_plugins_cleanup','plugins'],
        ['loopis_users_delete','users'],
        ['loopis_roles_delete','roles'],
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
            $('#run_loopis_config_installation').hide();
            if (key=='Setup'){
                $('#run_loopis_config_update').show();
                localStorage.setItem('loopis_config_installed', '1');
            }else{
                localStorage.setItem('loopis_plugins_installed', '0');
                localStorage.setItem('loopis_config_installed', '0');
                $('#run_preinstaller').show();
                $('#run_loopis_config_installation').hide();
                $('#run_loopis_config_update').hide();
            }
            logToPhp(" ");
            logToPhp(`=========================== End: Database ${key}! ===========================`);
            logToPhp(" ");
            return
        } else if(index==0){
            logToPhp(" ");
            logToPhp(`=========================== Start: Database ${key}! ===========================`);
            logToPhp(" ");
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
                $('#run_loopis_config_installation').prop('disabled', false).text('Install Loopis')
                // Stop on error
                logToPhp(" ");
                logToPhp(`=========================== End: Database ${key}! ===========================`);
                logToPhp(" ");
            }
        });
    }

     //====== installPlugins: plugin dependency install function ======
     
     function installPlugins() {
        logToPhp(" ");
        logToPhp(`=========================== Start: Preinstaller! ===========================`);
        logToPhp(" ");

        // Set current step to 🔄 Running!
        $(`td[data-step='install_plugins'] .status`).html('🔄 Running!');

        // Do post with loopis ajax
        $.post(loopis_ajax.ajax_url, { 
            action: 'loopis_sp_handle_actions',               // Do php function loopis_sp_handle_actions
            nonce: loopis_ajax.nonce,                         // With our nonce
            func_step: 'loopis_plugins_install',              // our function
            id: 'install_plugins'                             // and function id
        }, function (response) {                              // Afterwards
            const data = response.data;                       // Read the status JSON brought

            $(`td[data-step='${data.id}'] .status`).html(data.status);   // and set the status 
            // Check if JSON says success
            if (response.success) {
                // Set button rememberer 
                localStorage.setItem('loopis_plugins_installed', '1');

                // Swap buttons
                $('#run_preinstaller').hide();
                $('#run_loopis_config_installation').show();

                // Simulate regular POST
                const form = document.createElement("form");
                form.method = "POST";
                form.action = "/wp-admin/admin-post.php";

                const input = document.createElement("input");
                input.type = "hidden"; // No UI
                input.name = "action";
                input.value = "activate_plugins"; // Hook into admin_post_activate_plugins

                form.appendChild(input);

                document.body.appendChild(form);

                form.submit(); // Submits like a regular POST

                // End log moved to loopis_config_page_handler/loopis_activate_plugins_handler
            } else {
                $('#run_preinstaller').prop('disabled', false).text('Install Plugins')
                // Stop on error but let button be 
                logToPhp(" ");
                logToPhp(`=========================== End: Preinstaller! ===========================`);
                logToPhp(" ");
            }
        });
    }
    //====== updateVersion: loopis configuration update ======
     
    function updateVersion() {
        logToPhp(" ");
        logToPhp(`=========================== Start: Update! ===========================`);
        logToPhp(" ");
        // Do post with loopis ajax
        $.post(loopis_ajax.ajax_url, { 
            action: 'loopis_sp_update_handler',               // Do php function loopis_sp_handle_actions
            nonce: loopis_ajax.nonce,                         // With our nonce
        }, function (response) {                              // Afterwards
            const data = response.data;
            logToPhp(data.message);
            if (response.success) {
                $('#run_loopis_config_update').prop('disabled', true).text('Up-to-date!')
                logToPhp(" ");
                logToPhp('=========================== End: Update! ===========================');
                logToPhp(" ");
            } else {
                $('#run_loopis_config_update').prop('disabled', false).text('Update')
                logToPhp(" ");
                logToPhp('=========================== End: Update! ===========================');
                logToPhp(" ");
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

    // Install Plugins Button Listener
    $('#run_preinstaller').on('click', function () {
        $(this).prop('disabled', true).text('🔄 Installing Plugins...');
        $('.status').html('⬜');
        clearStatus();
        // Do plugin install step via AJAX
        installPlugins()
    });

    // Setup button listener
    $('#run_loopis_config_installation').on('click', function () {
        $(this).prop('disabled', true).text('🔄 Installing Loopis...');
        stepFunction('Setup',0);
    });

    // Setup button listener
    $('#run_loopis_config_update').on('click', function () {
        $(this).prop('disabled', true).text('🔄 Updating Loopis...');
        updateVersion('Setup',0);
    });    

    // Cleanup button listener
    $('#run_loopis_db_cleanup').on('click', function () {
        $('.status').html('⬜');
        clearStatus()
        stepFunction('Cleanup',0);
    });

    // Button hider
    if (localStorage.getItem('loopis_config_installed') === '1') {
        $('#run_preinstaller').hide();
        $('#run_loopis_config_installation').hide();
        $('#run_loopis_config_update').show();
    }else {
        if (localStorage.getItem('loopis_plugins_installed') === '1'){
            $('#run_preinstaller').hide();
            $('#run_loopis_config_installation').show();
            $('#run_loopis_config_update').hide();
        }else{
            $('#run_preinstaller').show();
            $('#run_loopis_config_installation').hide();
            $('#run_loopis_config_update').hide();
        }
    }


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
