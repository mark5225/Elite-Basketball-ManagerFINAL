<?php
namespace EBM;

class Admin {
    private static $instance = null;

    private function __construct() {
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function register_menus() {
        add_menu_page(
            __('Basketball Manager', 'elite-basketball-manager'),
            __('Basketball Manager', 'elite-basketball-manager'),
            'manage_options',
            'ebm-dashboard',
            array($this, 'render_dashboard'),
            'dashicons-groups',
            30
        );
    }

    public function enqueue_scripts($hook) {
        if (strpos($hook, 'ebm') !== false) {
            wp_enqueue_style('ebm-admin', EBM_ASSETS_URL . 'css/admin.css', array(), EBM_VERSION);
            wp_enqueue_script('ebm-admin', EBM_ASSETS_URL . 'js/admin.js', array('jquery'), EBM_VERSION, true);
        }
    }

    public function render_dashboard() {
        include EBM_TEMPLATES_DIR . 'admin/dashboard.php';
    }
}