<?php 
/**
 * Cache buster function for LOOPIS setup process.
 * 
 * Temporarily included when function renaming seemed to cause problems.
 * 
 * @package LOOPIS_Config
 */

// Prevent direct access
if (!defined('ABSPATH')) { 
    exit; 
}

// Paste and uncomment the code below where you want to run the cachebuster.

// Clear cache (if needed)
// require_once LOOPIS_CONFIG_DIR . 'cache/loopis_cache_buster.php';
// loopis_cache_buster();

// Function to clear various caches
function loopis_cache_buster() {
    error_log('>>> LOOPIS clearing caches...');
    
    // Clear PHP OPcache if available
    if (function_exists('opcache_reset')) {
        opcache_reset();
        error_log('✓ PHP OPcache cleared');
    }
    
    // Clear WordPress object cache
    if (function_exists('wp_cache_flush')) {
        wp_cache_flush();
        error_log('✓ WordPress object cache cleared');
    }
    
    // Clear WordPress rewrite rules (often cached)
    delete_transient('rewrite_rules');
    flush_rewrite_rules(false);
    error_log('✓ Rewrite rules cleared');
    
    // Force reload specific files?
    if (function_exists('opcache_invalidate')) {
        $files_to_invalidate = [
            LOOPIS_CONFIG_DIR . 'functions/loopis-db-setup.php',
            LOOPIS_CONFIG_DIR . 'functions/loopis-config.php',
        ];
        
        foreach ($files_to_invalidate as $file) {
            if (file_exists($file)) {
                opcache_invalidate($file, true);
                error_log('✓ Invalidated cache for: ' . basename($file));
            }
        }
    }
    
    // Add a small delay to ensure cache clearing takes effect
    usleep(100000); // 0.1 seconds
    
    error_log('>>> LOOPIS cache clearing complete!');
}