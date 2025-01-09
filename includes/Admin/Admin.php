<?php
namespace EBM\Admin;

class Admin {
    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu_pages'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post', array($this, 'save_meta_box_data'));
    }

    public function add_menu_pages() {
        add_menu_page(
            __('Basketball Manager', 'elite-basketball-manager'),
            __('Basketball Manager', 'elite-basketball-manager'),
            'manage_options',
            'ebm-dashboard',
            array($this, 'render_dashboard'),
            'dashicons-groups',
            30
        );

        add_submenu_page(
            'ebm-dashboard',
            __('Teams', 'elite-basketball-manager'),
            __('Teams', 'elite-basketball-manager'),
            'manage_options',
            'edit.php?post_type=ebm_team'
        );

        add_submenu_page(
            'ebm-dashboard',
            __('Players', 'elite-basketball-manager'),
            __('Players', 'elite-basketball-manager'),
            'manage_options',
            'edit.php?post_type=ebm_player'
        );

        add_submenu_page(
            'ebm-dashboard',
            __('Stats', 'elite-basketball-manager'),
            __('Stats', 'elite-basketball-manager'),
            'manage_options',
            'ebm-stats',
            array($this, 'render_stats_page')
        );

        add_submenu_page(
            'ebm-dashboard',
            __('Settings', 'elite-basketball-manager'),
            __('Settings', 'elite-basketball-manager'),
            'manage_options',
            'ebm-settings',
            array($this, 'render_settings_page')
        );
    }

    public function enqueue_scripts($hook) {
        if (strpos($hook, 'ebm') !== false) {
            wp_enqueue_style('ebm-admin', EBM_ASSETS_URL . 'css/admin.css', array(), EBM_VERSION);
            wp_enqueue_script('ebm-admin', EBM_ASSETS_URL . 'js/admin.js', array('jquery'), EBM_VERSION, true);
            
            wp_localize_script('ebm-admin', 'ebmAdmin', array(
                'ajaxUrl' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ebm-admin-nonce')
            ));
        }
    }

    public function add_meta_boxes() {
        // Player Details Meta Box
        add_meta_box(
            'ebm_player_details',
            __('Player Details', 'elite-basketball-manager'),
            array($this, 'render_player_details_meta_box'),
            'ebm_player',
            'normal',
            'high'
        );

        // Player Stats Meta Box
        add_meta_box(
            'ebm_player_stats',
            __('Player Stats', 'elite-basketball-manager'),
            array($this, 'render_player_stats_meta_box'),
            'ebm_player',
            'normal',
            'high'
        );

        // Team Details Meta Box
        add_meta_box(
            'ebm_team_details',
            __('Team Details', 'elite-basketball-manager'),
            array($this, 'render_team_details_meta_box'),
            'ebm_team',
            'normal',
            'high'
        );
    }

    public function render_player_details_meta_box($post) {
        wp_nonce_field('ebm_player_details', 'ebm_player_details_nonce');

        $height = get_post_meta($post->ID, '_ebm_height', true);
        $weight = get_post_meta($post->ID, '_ebm_weight', true);
        $wingspan = get_post_meta($post->ID, '_ebm_wingspan', true);
        $vertical = get_post_meta($post->ID, '_ebm_vertical', true);
        $jersey = get_post_meta($post->ID, '_ebm_jersey_number', true);
        $class_year = get_post_meta($post->ID, '_ebm_class_year', true);

        include EBM_TEMPLATES_DIR . 'admin/player-details-meta-box.php';
    }

    public function render_player_stats_meta_box($post) {
        wp_nonce_field('ebm_player_stats', 'ebm_player_stats_nonce');
        
        global $wpdb;
        $stats = $wpdb->get_results($wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}ebm_game_stats WHERE player_id = %d ORDER BY game_date DESC",
            $post->ID
        ));

        include EBM_TEMPLATES_DIR . 'admin/player-stats-meta-box.php';
    }

    public function render_team_details_meta_box($post) {
        wp_nonce_field('ebm_team_details', 'ebm_team_details_nonce');

        $season = get_post_meta($post->ID, '_ebm_season', true);
        $coach = get_post_meta($post->ID, '_ebm_coach', true);
        $record = get_post_meta($post->ID, '_ebm_record', true);

        include EBM_TEMPLATES_DIR . 'admin/team-details-meta-box.php';
    }

    public function save_meta_box_data($post_id) {
        // Add save logic for each meta box
        if (isset($_POST['ebm_player_details_nonce']) && wp_verify_nonce($_POST['ebm_player_details_nonce'], 'ebm_player_details')) {
            $fields = array('height', 'weight', 'wingspan', 'vertical', 'jersey_number', 'class_year');
            foreach ($fields as $field) {
                if (isset($_POST['ebm_' . $field])) {
                    update_post_meta($post_id, '_ebm_' . $field, sanitize_text_field($_POST['ebm_' . $field]));
                }
            }
        }

        if (isset($_POST['ebm_team_details_nonce']) && wp_verify_nonce($_POST['ebm_team_details_nonce'], 'ebm_team_details')) {
            $fields = array('season', 'coach', 'record');
            foreach ($fields as $field) {
                if (isset($_POST['ebm_' . $field])) {
                    update_post_meta($post_id, '_ebm_' . $field, sanitize_text_field($_POST['ebm_' . $field]));
                }
            }
        }
    }

    public function render_dashboard() {
        include EBM_TEMPLATES_DIR . 'admin/dashboard.php';
    }

    public function render_stats_page() {
        include EBM_TEMPLATES_DIR . 'admin/stats.php';
    }

    public function render_settings_page() {
        include EBM_TEMPLATES_DIR . 'admin/settings.php';
    }
}