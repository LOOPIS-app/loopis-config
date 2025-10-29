<?php
/**
 * WP Admin page for handling compulsory LOOPIS components.
 * 
 * @package LOOPIS_Config
 * @subpackage Admin-page
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

// Function to render the page
function loopis_components_page() {
    ?>
    <div class="wrap">
        <!-- Page title and description-->
        <h1>🧩 LOOPIS Components <span class="h1-right">Version <?php echo esc_html(LOOPIS_CONFIG_VERSION); ?></span></h1>
        <p class="description">💡 This is where you install and update compulsory components.</p>

        <!-- Page content-->
        <h2>Components installed</h2>
        <p><i>[Add a list of installed components (plugins + theme) + button to update if approved by develoopers.]</i></p>
        
        <h2>Components to install</h2>
        <p><i>[Add list of compulsory components (plugins + theme) + button to install them.]</i></p>

    </div>
<?php
}