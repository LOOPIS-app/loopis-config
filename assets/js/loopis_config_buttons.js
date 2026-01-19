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
    const version = loopis_ajax.version;
    let outofdate = loopis_ajax.outofdate;
    let installed = loopis_ajax.installed;
    updateButtons();
    //====== installPlugins: plugin dependency install function ======

    function installPlugins() {
        return new Promise((resolve, reject) => {
            const total = plugins.length;
            let index = 0;
            
            function installNext() {
                if (index >= total) {
                    resolve(); // promise fulfilled
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
                    const data = response.data;
                    if (response.success) {
                        $(`td[data-step=${plugin.ID}] .status`).html(`🔄 Installed: ${plugin.slug}...`);
                        $(`td[data-step='${plugin.ID}'] .version`).html(data.version);
                        index++;
                        installNext();
                    } else {
                        reject("Plugin install failed");
                    }
                }).fail(() => reject("Database setup failed"));
            }
        
            installNext(); // Start first install
        });
    }


    //====== stepFunction: main setup function ======

    // Regressive ajax $_POST submission function 
    function stepFunction(index) {
        return new Promise((resolve, reject) => {
            // Check if list ended
            if (index >= Setup_functions.length) {
                resolve()
                return
            } 

            // Define id and func_step
            const step = Setup_functions[index];

            // Set current step to 🔄 Running!
            $(`td[data-step='${step.ID}'] .status`).html('🔄 Running!');

            // Do post with loopis ajax
            $.post(loopis_ajax.ajax_url, { 
                action: 'loopis_sp_handle_actions',               // Do php function loopis_sp_handle_actions
                nonce: loopis_ajax.nonce,                         // With our nonce
                func_step: step.func_step,                             // our function,
                id: step.ID,                                           // function id
                data: step.cdata,                                       // and data
            }, function (response) {                              // Afterwards
                const data = response.data;                       // Read the status JSON brought
                $(`td[data-step='${data.id}'] .status`).html(data.status);   // and set the status 
                $(`td[data-step='${data.id}'] .version`).html(data.version);   // and set the version
                // Check if JSON says success
                if (response.success) {
                    // Continue to next step
                    stepFunction(index + 1).then(resolve).catch(reject);;
                } else {
                    reject("Database setup failed");
                }
            }).fail(() => reject("Database setup failed"));
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

    //====== subfunctions ======

    // PHP Error logger
    function installButton() {
        logToPhp(" ");
        logToPhp(`=========================== Start: Loopis Installer! ===========================`);
        logToPhp(" ");
        Promise.all([ //run simultaneously
            installPlugins(), 
            stepFunction(0)     
        ])
            .then(() => {
                // when both are finished, submit
                refreshLoopisState()

                $('#run_loopis_config_installation').hide();
                $('#run_loopis_config_update').show();
    
                const form = document.createElement("form");
                form.method = "POST";
                form.action = "/wp-admin/admin-post.php";
    
                const input = document.createElement("input");
                input.type = "hidden";
                input.name = "action";
                input.value = "activate_plugins";
    
                form.appendChild(input);
                document.body.appendChild(form);
                form.submit();
            })
            .catch(err => {
                console.error(err);
                $('#run_loopis_config_installation')
                    .prop('disabled', false)
                    .text('Install Loopis');
                logToPhp(err);
                logToPhp(" ");
                logToPhp(`=========================== End: Loopis Installer! ===========================`);
                logToPhp(" ");
            });
    }


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
        installButton();
        $(this).prop('disabled', true).text('🔄 Installing Loopis...');
    });
    // Update button listener
    $('#run_loopis_config_update').on('click', function () {
        $(this).prop('disabled', true).text('🔄 Updating Loopis...');
        updateVersion();
        //TEMPORARY
        installButton();
    });    

    // Button to db fix
    function refreshLoopisState() {
        return $.post(loopis_ajax.ajax_url, {
            action: 'loopis_get_status',
            nonce: loopis_ajax.nonce
        }).done(response => {
            if (!response.success) return;
    
            installed = response.data.installed;
            outofdate = response.data.outofdate;
    
            updateButtons();
        });
    }

    function updateButtons(){
       if (installed) {//Enable update if installed and not versioncoherent
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
    }
});

