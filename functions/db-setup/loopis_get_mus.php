<?php
/**
 * Function to install LOOPIS MU-plugins.
 *
 * This function is called by main function 'loopis_db_setup'.
 * 
 * @package LOOPIS_Config
 * @subpackage Plugins
 */

 /**
 * Installs a MU plugin from a ZIP URL into wp-content/mu-plugins
 *
 * @return void
 */
function loopis_mu_install() {
    loopis_elog_function_start('loopis_mu_install');
    $data = $_POST['data'] ?? [];
    $slug = sanitize_text_field($data['slug'] ?? '');
    $zip_url = sanitize_text_field($data['zip_url'] ?? '');

    if (empty($slug) || empty($zip_url)) {
        loopis_elog_first_level("Missing slug or zip URL");
        return;
    }

    $mu_dir = WPMU_PLUGIN_DIR;
    if (!file_exists($mu_dir)) {
        mkdir($mu_dir, 0755, true);
        loopis_elog_first_level("Created mu-plugins directory!");
    }

    // Download ZIP to a temporary file
    $tmp_file = download_url($zip_url);

    if (is_wp_error($tmp_file)) {
        loopis_elog_first_level("Failed to download ZIP: " . $tmp_file->get_error_message());
        return;
    }
    loopis_elog_first_level("Downloaded MU-ZIP!");
    // Extract ZIP
    $zip = new ZipArchive();
    if ($zip->open($tmp_file) === true) {
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $entry = $zip->getNameIndex($i);
            $dest_path = $mu_dir . '/' . basename($entry); 
            copy("zip://{$tmp_file}#{$entry}", $dest_path);
        }
        $zip->close();
        loopis_elog_first_level("Extracted $slug directly into MU plugins");
    } else {
        loopis_elog_first_level("Failed to open ZIP for $slug");
        @unlink($tmp_file);
        return;
    }

    @unlink($tmp_file);

    loopis_elog_first_level("MU plugins installed successfully");

    loopis_elog_function_end_success('loopis_mu_install');
}