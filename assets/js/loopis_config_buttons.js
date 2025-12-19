/*
    This is a button handler script, it uses ajax as a $_POST fetcher proxy.
*/


// Jquery for 'do when the document is ready and loaded'
jQuery(document).ready(function ($) {

    // =========================
    // LOOPIS Setup 
    // =========================

    // All function calls and category ids
    const Setup_functions = loopis_ajax.setup_functions;
    const plugins = loopis_ajax.preinstall_data;
    const version= loopis_ajax.version;
    const outofdate= loopis_ajax.outofdate;

    //====== installPlugins: plugin dependency install function ======

    function installPlugins() {
        logToPhp(" ");
        logToPhp(`=========================== Start: Preinstaller! ===========================`);
        logToPhp(" ");

        const total = plugins.length;
        let index = 0;
    
        function installNext() {
            if (index >= total) {
                // Set button rememberer 
                localStorage.setItem('loopis_config_installed', '1');

                // Swap buttons
                $('#run_loopis_config_installation').hide();
                $('#run_loopis_config_update').show();

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
                return;
            }
            const plugin = plugins[index];
            $(`td[data-step=${plugin.ID}] .status`).html(`🔄 Installing ${plugin.slug}...`);
            $.post(loopis_ajax.ajax_url, {
                action: 'loopis_sp_handle_actions',
                nonce: loopis_ajax.nonce,
                func_step: 'loopis_ext_plugins_install',
                slug: plugin.slug,
                main: plugin.main,
            }).done(function (response) {
                if (response.success) {
                    $(`td[data-step=${plugin.ID}] .status`).html(`🔄 Installed: ${plugin.slug}...`);
                    $(`td[data-step=${plugin.ID}] .version`).html(`${version}`);
                    index++;
                    installNext();
                } else {
                    $('#run_preinstaller').prop('disabled', false).text('Install Plugins')
                    logToPhp(" ");
                    logToPhp(`=========================== End: Preinstaller! ===========================`);
                    logToPhp(" ");
                }
            });
        }
    
        installNext(); // Start first install
    }

    //====== stepFunction: main setup function ======

    // Regressive ajax $_POST submission function 
    function stepFunction(index) {
        // Check if list ended
        if (index >= Setup_functions.length) {
            logToPhp(" ");
            logToPhp(`=========================== End: Database Setup! ===========================`);
            logToPhp(" ");
            return
        } else if(index==0){
            logToPhp(" ");
            logToPhp(`=========================== Start: Database Setup! ===========================`);
            logToPhp(" ");
        }

        // Define id and func_step
        const func_step = Setup_functions[index][0];
        const id = Setup_functions[index][1];

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
            $(`td[data-step='${data.id}'] .version`).html(version);   // and set the version
            // Check if JSON says success
            if (response.success) {
                // Continue to next step
                stepFunction(index + 1);
            } else {
                $('#run_loopis_config_installation').prop('disabled', false).text('Install Loopis')
                // Stop on error
                logToPhp(" ");
                logToPhp(`=========================== End: Database Setup! ===========================`);
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

     //====== updatePlugins: loopis configuration update ======

    function updatePlugins() {
        logToPhp(" ");
        logToPhp(`=========================== Start: Update! ===========================`);
        logToPhp(" ");
        // Do post with loopis ajax
        $.post(loopis_ajax.ajax_url, { 
            action: 'loopis_plugins_update_handler',          // Do php function loopis_sp_handle_actions
            nonce: loopis_ajax.nonce,                         // With our nonce
        }, function (response) {                              // Afterwards
            const data = response.data;
            logToPhp(data.message);
            if (response.success) {
                $('#run_plupdate').prop('disabled', true).text('Up-to-date!')
                logToPhp(" ");
                logToPhp('=========================== End: Update! ===========================');
                logToPhp(" ");
            } else {
                $('#run_plupdate').prop('disabled', false).text('Update')
                logToPhp(" ");
                logToPhp('=========================== End: Update! ===========================');
                logToPhp(" ");
            }
        });
    }

    //====== subfunctions ======

    // PHP Error logger
    function logToPhp(error_log) {
        $.post(loopis_ajax.ajax_url, {
            action: 'loopis_log_message',
            nonce: loopis_ajax.nonce,
            error_log: error_log
        });
    }

    //====== Button Listeners ======

    // Setup button listener
    $('#run_loopis_config_installation').on('click', function () {
        $(this).prop('disabled', true).text('🔄 Installing Loopis...');
        installPlugins();
        stepFunction(0);
    });

    // Update button listener
    $('#run_loopis_config_update').on('click', function () {
        $(this).prop('disabled', true).text('🔄 Updating Loopis...');
        updateVersion();
    });    

    // Update button listener
    $('#run_plupdate').on('click', function () {
        $(this).prop('disabled', true).text('🔄 Updating Plugins...');
        updatePlugins();
        $(this).prop('disabled', true).text('Up-to-date!');
    });    

    // Button Enabler
    if (localStorage.getItem('loopis_config_installed') === '1') {//Enable update if installed and not versioncoherent
        $('#run_loopis_config_installation').prop('disabled', true).text('Installed!');
        if (outofdate){
            $('#run_loopis_config_update').prop('disabled', false).text('Update loopis');
        }else{
            $('#run_loopis_config_update').prop('disabled', true).text('Up-to-date!');
        };
        if (outofdate){
            $('#run_plupdate').prop('disabled', false).text('Update plugins');
        }else{
            $('#run_plupdate').prop('disabled', true).text('Up-to-date!');
        };
    }else { // If not installed, disable update enable install
        $('#run_loopis_config_installation').prop('disabled', false).text('Install loopis');
        $('#run_loopis_config_update').prop('disabled', true).text('Update loopis');
        $('#run_plupdate').prop('disabled', true).text('Update plugins');
    };


});
