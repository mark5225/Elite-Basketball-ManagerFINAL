<?php
namespace EBM\Models;

class Stats {
    private $wpdb;
    private $table_prefix;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_prefix = $wpdb->prefix . 'ebm_';
    }

    public function get_program_stats() {
        // Get overall program statistics
        return array(
            'total_players' => $this->get_total_players(),
            'active_teams' => $this->get_active_teams(),
            'college_commits' => $this->get_college_commits(),
            'career_stats' => $this->get_career_stats()
        );
    }

    public function get_total_players() {
        return $this->wpdb->get_var("
            SELECT COUNT(*) FROM {$this->wpdb->posts}
            WHERE post_type = 'ebm_player'
            AND post_status = 'publish'
        ");
    }

    public function get_active_teams() {
        return $this->wpdb->get_var("
            SELECT COUNT(*) FROM {$this->wpdb->posts}
            WHERE post_type = 'ebm_team'
            AND post_status = 'publish'
        ");
    }

    public function get_college_commits() {
        return $this->wpdb->get_var("
            SELECT COUNT(*) FROM {$this->table_prefix}recruitment
            WHERE status = 'committed'
        ");
    }

    public function get_career_stats() {
        return $this->wpdb->get_row("
            SELECT 
                AVG(points) as avg_points,
                AVG(rebounds) as avg_rebounds,
                AVG(assists) as avg_assists,
                COUNT(DISTINCT player_id) as total_players,
                COUNT(DISTINCT game_date) as total_games
            FROM {$this->table_prefix}game_stats
        ");
    }

    public function get_recruiting_breakdown() {
        return $this->wpdb->get_results("
            SELECT 
                division,
                COUNT(*) as count,
                COUNT(*) * 100.0 / (
                    SELECT COUNT(*) 
                    FROM {$this->table_prefix}recruitment 
                    WHERE status = 'committed'
                ) as percentage
            FROM {$this->table_prefix}recruitment
            WHERE status = 'committed'
            GROUP BY division
            ORDER BY count DESC
        ");
    }

    public function get_geographic_distribution() {
        return $this->wpdb->get_results("
            SELECT 
                college_state as state,
                COUNT(*) as count
            FROM {$this->table_prefix}recruitment
            WHERE status = 'committed'
            GROUP BY college_state
            ORDER BY count DESC
        ");
    }

    public function record_game_stats($game_data) {
        return $this->wpdb->insert(
            $this->table_prefix . 'game_stats',
            $game_data,
            array(
                '%d', // player_id
                '%s', // game_date
                '%s', // opponent
                '%d', // minutes_played
                '%d', // points
                '%d', // rebounds
                '%d', // assists
                '%d', // steals
                '%d', // blocks
                '%d', // fg_made
                '%d', // fg_attempted
                '%d', // three_made
                '%d', // three_attempted
                '%d', // ft_made
                '%d'  // ft_attempted
            )
        );
    }

    public function update_game_stats($stat_id, $game_data) {
        return $this->wpdb->update(
            $this->table_prefix . 'game_stats',
            $game_data,
            array('id' => $stat_id),
            null,
            array('%d')
        );
    }

    public function delete_game_stats($stat_id) {
        return $this->wpdb->delete(
            $this->table_prefix . 'game_stats',
            array('id' => $stat_id),
            array('%d')
        );
    }
}