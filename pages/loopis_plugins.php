<?php
/**
 * WP Admin page for handling compulsory WordPress plugins.
 * 
 * @package LOOPIS_Config
 * @subpackage Admin-page
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

// Function to render the page
function loopis_plugins_page() {
    ?>
    <div class="wrap">
        <!-- Page title and description-->
        <h1>🧩 LOOPIS plugins</h1>
        <p class="description">💡 This is where you install and update compulsory plugins for LOOPIS.</p>

        <!-- Page content-->
        <h2>Plugins to install</h2>
        <p><i>[Add list of compulsory plugins + button to install them.]</i></p>

        <h2>Installed plugins</h2>
        <p><i>[Add list of installed plugins + button to update them if tested by develoopers.]</i></p>
    </div>
<?php
}