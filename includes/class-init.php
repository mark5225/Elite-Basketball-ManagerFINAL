<?php
namespace EBM;

class Init {
    private static $instance = null;

    private function __construct() {
        add_action('init', array($this, 'init'));
        add_action('admin_menu', array($this, 'admin_menu'));
        register_activation_hook(EBM_PLUGIN_DIR . 'elite-basketball-manager.php', array($this, 'activate'));
        register_deactivation_hook(EBM_PLUGIN_DIR . 'elite-basketball-manager.php', array($this, 'deactivate'));
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function init() {
        // Load text domain
        load_plugin_textdomain('elite-basketball-manager', false, dirname(plugin_basename(EBM_PLUGIN_DIR)) . '/languages');

        // Initialize components after WordPress init
        $this->init_components();
    }

    private function init_components() {
        // Initialize post types
        PostTypes::get_instance();

        // Initialize admin if in admin
        if (is_admin()) {
            Admin::get_instance();
        }

        // Initialize frontend
        Frontend::get_instance();
    }

    public function admin_menu() {
        Admin::get_instance()->register_menus();
    }

    public function activate() {
        // Create/update database tables
        Database::get_instance()->create_tables();
        
        // Register post types for rewrite rules
        PostTypes::get_instance()->register_post_types();
        
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    public function deactivate() {
        flush_rewrite_rules();
    }
}