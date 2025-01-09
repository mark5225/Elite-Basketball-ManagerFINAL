<?php
/**
 * Plugin Name: Elite Basketball Manager
 * Description: Plugin to manage basketball related activities
 * Version: 1.0.0
 * Author: ABW Finest Web Design
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

// Define plugin constants
define('EBM_VERSION', '1.0.0');
define('EBM_FILE', __FILE__);
define('EBM_BASENAME', plugin_basename(EBM_FILE));
define('EBM_PATH', plugin_dir_path(EBM_FILE));
define('EBM_URL', plugin_dir_url(EBM_FILE));

// Autoloader
spl_autoload_register(function ($class_name) {
    if (strpos($class_name, 'EBM\\') !== 0) {
        return;
    }
    $class_path = str_replace('EBM\\', '', $class_name);
    $class_path = str_replace('\\', DIRECTORY_SEPARATOR, $class_path);
    $file = EBM_PATH . 'includes/' . $class_path . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Initialize plugin
function ebm_init() {
    $plugin = \EBM\Core\Plugin::get_instance();
}

add_action('plugins_loaded', 'ebm_init');

// Register activation hook
register_activation_hook(EBM_FILE, function() {
    $init = new \EBM\Init();
    $init->activate(false);
});

// Register deactivation hook
register_deactivation_hook(EBM_FILE, function() {
    $init = new \EBM\Init();
    $init->deactivate();
});