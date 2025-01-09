<?php
namespace EBM\Frontend;

class Shortcodes {
    public function __construct() {
        add_shortcode('ebm_roster', array($this, 'render_roster'));
        add_shortcode('ebm_player_profile', array($this, 'render_player_profile'));
        add_shortcode('ebm_team_stats', array($this, 'render_team_stats'));
        add_shortcode('ebm_recruitment_stats', array($this, 'render_recruitment_stats'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
    }

    public function enqueue_scripts() {
        wp_enqueue_style('ebm-frontend', EBM_ASSETS_URL . 'css/frontend.css', array(), EBM_VERSION);
        wp_enqueue_script('ebm-frontend', EBM_ASSETS_URL . 'js/frontend.js', array('jquery'), EBM_VERSION, true);
        
        wp_localize_script('ebm-frontend', 'ebmFrontend', array(
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('ebm-frontend-nonce')
        ));
    }

    public function render_roster($atts) {
        $atts = shortcode_atts(array(
            'team_id' => 0,
            'layout' => 'grid', // grid or table
            'show_stats' => 'yes'
        ), $atts);

        ob_start();
        
        if ($atts['team_id']) {
            $players = get_posts(array(
                'post_type' => 'ebm_player',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => '_ebm_team_id',
                        'value' => $atts['team_id']
                    )
                )
            ));

            include EBM_TEMPLATES_DIR . 'frontend/roster-' . $atts['layout'] . '.php';
        } else {
            echo '<p>' . __('Please specify a team ID.', 'elite-basketball-manager') . '</p>';
        }

        return ob_get_clean();
    }

    public function render_player_profile($atts) {
        $atts = shortcode_atts(array(
            'player_id' => 0,
            'show_stats' => 'yes',
            'show_media' => 'yes'
        ), $atts);

        ob_start();

        if ($atts['player_id']) {
            $player = get_post($atts['player_id']);
            if ($player && $player->post_type === 'ebm_player') {
                // Get player details
                $height = get_post_meta($player->ID, '_ebm_height', true);
                $weight = get_post_meta($player->ID, '_ebm_weight', true);
                $wingspan = get_post_meta($player->ID, '_ebm_wingspan', true);
                $vertical = get_post_meta($player->ID, '_ebm_vertical', true);
                $jersey = get_post_meta($player->ID, '_ebm_jersey_number', true);
                $class_year = get_post_meta($player->ID, '_ebm_class_year', true);

                // Get stats if needed
                if ($atts['show_stats'] === 'yes') {
                    global $wpdb;
                    $stats = $wpdb->get_results($wpdb->prepare(
                        "SELECT * FROM {$wpdb->prefix}ebm_game_stats WHERE player_id = %d ORDER BY game_date DESC",
                        $player->ID
                    ));
                }

                include EBM_TEMPLATES_DIR . 'frontend/player-profile.php';
            }
        } else {
            echo '<p>' . __('Please specify a player ID.', 'elite-basketball-manager') . '</p>';
        }

        return ob_get_clean();
    }

    public function render_team_stats($atts) {
        $atts = shortcode_atts(array(
            'team_id' => 0,
            'season' => '',
            'show_charts' => 'yes'
        ), $atts);

        ob_start();

        if ($atts['team_id']) {
            $team = get_post($atts['team_id']);
            if ($team && $team->post_type === 'ebm_team') {
                // Get team stats
                global $wpdb;
                $stats = $wpdb->get_results($wpdb->prepare(
                    "SELECT 
                        player_id,
                        SUM(points) as total_points,
                        SUM(assists) as total_assists,
                        SUM(rebounds) as total_rebounds,
                        COUNT(*) as games_played
                    FROM {$wpdb->prefix}ebm_game_stats 
                    WHERE player_id IN (
                        SELECT post_id FROM {$wpdb->postmeta} 
                        WHERE meta_key = '_ebm_team_id' 
                        AND meta_value = %d
                    )
                    GROUP BY player_id",
                    $team->ID
                ));

                include EBM_TEMPLATES_DIR . 'frontend/team-stats.php';
            }
        } else {
            echo '<p>' . __('Please specify a team ID.', 'elite-basketball-manager') . '</p>';
        }

        return ob_get_clean();
    }

    public function render_recruitment_stats($atts) {
        $atts = shortcode_atts(array(
            'show_map' => 'yes',
            'show_charts' => 'yes'
        ), $atts);

        ob_start();

        global $wpdb;
        $stats = $wpdb->get_results("
            SELECT 
                division,
                COUNT(*) as count
            FROM {$wpdb->prefix}ebm_recruitment
            GROUP BY division
            ORDER BY count DESC
        ");

        include EBM_TEMPLATES_DIR . 'frontend/recruitment-stats.php';

        return ob_get_clean();
    }
}