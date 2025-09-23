/*
    This is a button handler script, it uses ajax as a $_POST fetcher proxy.
*/
// JS for 'do when the document is ready and loaded'
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
                $(`td[data-step='${id}'] .status`).html(data.statuses[id]);
            }
        });
    }
    //Setup button listener
    $('#run_loopis_config_installation').on('click', function () {
        buttonRequest('setup');
    });
    //Cleanup button listener
    $('#run_loopis_db_cleanup').on('click', function () {
        buttonRequest('cleanup');
    });
});
