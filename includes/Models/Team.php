<?php
namespace EBM\Models;

class Team {
    private $id;
    private $wpdb;
    private $table_prefix;

    public function __construct($team_id = null) {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_prefix = $wpdb->prefix . 'ebm_';
        $this->id = $team_id;
    }

    public function get_roster() {
        return get_posts(array(
            'post_type' => 'ebm_player',
            'posts_per_page' => -1,
            'meta_query' => array(
                array(
                    'key' => '_ebm_team_id',
                    'value' => $this->id
                )
            ),
            'orderby' => 'menu_order',
            'order' => 'ASC'
        ));
    }

    public function get_team_stats() {
        return $this->wpdb->get_row($this->wpdb->prepare("
            SELECT 
                COUNT(DISTINCT game_date) as games_played,
                AVG(team_points) as ppg,
                AVG(opponent_points) as opponent_ppg,
                AVG(team_rebounds) as rpg,
                AVG(team_assists) as apg,
                COUNT(CASE WHEN team_points > opponent_points THEN 1 END) as wins,
                COUNT(CASE WHEN team_points < opponent_points THEN 1 END) as losses
            FROM {$this->table_prefix}game_stats
            WHERE team_id = %d
        ", $this->id));
    }

    public function get_player_averages() {
        return $this->wpdb->get_results($this->wpdb->prepare("
            SELECT 
                p.ID as player_id,
                p.post_title as player_name,
                COUNT(DISTINCT gs.game_date) as games_played,
                AVG(gs.points) as ppg,
                AVG(gs.rebounds) as rpg,
                AVG(gs.assists) as apg,
                AVG(gs.steals) as spg,
                AVG(gs.blocks) as bpg,
                SUM(gs.fg_made) as fg_made,
                SUM(gs.fg_attempted) as fg_attempted,
                SUM(gs.three_made) as three_made,
                SUM(gs.three_attempted) as three_attempted,
                SUM(gs.ft_made) as ft_made,
                SUM(gs.ft_attempted) as ft_attempted
            FROM {$this->wpdb->posts} p
            LEFT JOIN {$this->table_prefix}game_stats gs ON p.ID = gs.player_id
            WHERE p.post_type = 'ebm_player'
            AND p.ID IN (
                SELECT post_id FROM {$this->wpdb->postmeta}
                WHERE meta_key = '_ebm_team_id'
                AND meta_value = %d
            )
            GROUP BY p.ID
            ORDER BY ppg DESC
        ", $this->id));
    }

    public function add_player($player_id, $order = null) {
        if ($order === null) {
            $max_order = $this->wpdb->get_var($this->wpdb->prepare("
                SELECT MAX(menu_order) FROM {$this->wpdb->posts} 
                WHERE ID IN (
                    SELECT post_id FROM {$this->wpdb->postmeta}
                    WHERE meta_key = '_ebm_team_id'
                    AND meta_value = %d
                )
            ", $this->id));
            $order = $max_order ? $max_order + 1 : 1;
        }

        update_post_meta($player_id, '_ebm_team_id', $this->id);
        wp_update_post(array(
            'ID' => $player_id,
            'menu_order' => $order
        ));
    }

    public function remove_player($player_id) {
        delete_post_meta($player_id, '_ebm_team_id', $this->id);
    }

    public function update_roster_order($player_ids) {
        foreach ($player_ids as $order => $player_id) {
            wp_update_post(array(
                'ID' => $player_id,
                'menu_order' => $order
            ));
        }
    }
}