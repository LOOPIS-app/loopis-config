/*
    This is a button handler script, it uses ajax as a $_POST fetcher proxy.
*/
// JS for "do when the document is ready and loaded"
jQuery(document).ready(function ($) {
    // Ajax $_POST submission function
    function buttonRequest(buttonId) {
        //Do post with loopis ajax
        $.post(loopis_ajax.ajax_url, {
            action: 'loopis_sp_handle_actions',     // Do php function loopis_sp_handle_actions
            nonce: loopis_ajax.nonce,               // With our nonce
            button_id: buttonId                     // and our button ID(from the listener)
        }, function (response) {                    // Afterwards
            const data = JSON.parse(response);      // Read the statuses JSON brought
            for (const id in data.statuses) {       // and set the statuses on the corresponding data-step
                $(`td[data-step="${id}"] .status`).html(data.statuses[id]);
            }
            
            // Refresh user roles display if it's currently visible
            refreshRolesDisplay();
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
    //Setup button listener
    $('#run_loopis_config_installation').on('click', function () {
        buttonRequest('setup');
    });
    //Cleantup button listener
    $('#run_loopis_db_cleanup').on('click', function () {
        buttonRequest('cleanup');
    });
});
