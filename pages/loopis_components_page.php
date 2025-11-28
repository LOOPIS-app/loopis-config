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
    // get components
    $config =  wp_cache_get('loopis_config_data', 'loopis');
    $table = array_filter($config, fn($r) => $r['Category'] === 'Component');
    ?>
    <div class="wrap">
        <!-- Page title and description-->
        <h1>🧩 LOOPIS Components <span class="h1-right">Version <?php echo esc_html(LOOPIS_CONFIG_VERSION); ?></span></h1>
        <p class="description">💡 This is where you install and update compulsory components.</p>

        <!-- Page content-->
        <h2>Components</h2>

        <p>
            <button id="run_plupdate" class="button button-primary" value="Update plugins" disabled>Update plugins</button>
        </p>
        
        <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th scope="col" class="manage-column">Component</th>
                <th scope="col" class="manage-column">Status</th>
                <th scope="col" class="manage-column">Version</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($table as $row): ?>
                <tr>
                    <td class="column-unit"><?php echo htmlspecialchars($row['Unit']); ?></td>
                    <td class="column-status" data-step="<?php echo htmlspecialchars($row['ID']); ?>">
                        <span class="status"> <?php echo loopis_sp_get_status_text($row['Config_Status']); ?> </span>
                    </td>
                    <td class="column-version" data-step="<?php echo htmlspecialchars($row['ID']); ?>">
                    <span class="version"><?php echo htmlspecialchars($row['Config_Version']); ?></span>
                </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
</table>

    </div>
<?php
}