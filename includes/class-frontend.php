<?php
namespace EBM;

class Frontend {
    private static $instance = null;

    private function __construct() {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function enqueue_scripts() {
        wp_enqueue_style('ebm-frontend', EBM_ASSETS_URL . 'css/frontend.css', array(), EBM_VERSION);
        wp_enqueue_script('ebm-frontend', EBM_ASSETS_URL . 'js/frontend.js', array('jquery'), EBM_VERSION, true);
    }
}