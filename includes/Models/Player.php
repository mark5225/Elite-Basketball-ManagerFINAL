<?php
namespace EBM\Models;

class Player {
    private $id;
    private $wpdb;
    private $table_prefix;

    public function __construct($player_id = null) {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_prefix = $wpdb->prefix . 'ebm_';
        $this->id = $player_id;
    }

    public function get_stats($game_id = null) {
        $sql = "SELECT * FROM {$this->table_prefix}game_stats WHERE player_id = %d";
        $params = [$this->id];

        if ($game_id) {
            $sql .= " AND game_id = %d";
            $params[] = $game_id;
        }

        $sql .= " ORDER BY game_date DESC";
        
        return $this->wpdb->get_results($this->wpdb->prepare($sql, $params));
    }

    public function get_averages() {
        return $this->wpdb->get_row($this->wpdb->prepare("
            SELECT 
                COUNT(*) as games_played,
                AVG(points) as ppg,
                AVG(rebounds) as rpg,
                AVG(assists) as apg,
                AVG(steals) as spg,
                AVG(blocks) as bpg,
                SUM(fg_made) as total_fg_made,
                SUM(fg_attempted) as total_fg_attempted,
                SUM(three_made) as total_three_made,
                SUM(three_attempted) as total_three_attempted,
                SUM(ft_made) as total_ft_made,
                SUM(ft_attempted) as total_ft_attempted
            FROM {$this->table_prefix}game_stats 
            WHERE player_id = %d
        ", $this->id));
    }

    public function add_stats($data) {
        $defaults = [
            'player_id' => $this->id,
            'game_date' => current_time('mysql'),
            'minutes_played' => 0,
            'points' => 0,
            'assists' => 0,
            'rebounds' => 0,
            'steals' => 0,
            'blocks' => 0,
            'fg_made' => 0,
            'fg_attempted' => 0,
            'three_made' => 0,
            'three_attempted' => 0,
            'ft_made' => 0,
            'ft_attempted' => 0
        ];

        $stats = wp_parse_args($data, $defaults);
        
        // Validate data
        if (!$this->validate_stats($stats)) {
            return false;
        }

        return $this->wpdb->insert(
            $this->table_prefix . 'game_stats',
            $stats,
            [
                '%d', '%s', '%d', '%d', '%d', '%d', '%d', '%d',
                '%d', '%d', '%d', '%d', '%d', '%d'
            ]
        );
    }

    public function update_stats($stat_id, $data) {
        // Validate data
        if (!$this->validate_stats($data)) {
            return false;
        }

        return $this->wpdb->update(
            $this->table_prefix . 'game_stats',
            $data,
            ['id' => $stat_id, 'player_id' => $this->id],
            null,
            ['%d', '%d']
        );
    }

    public function delete_stats($stat_id) {
        return $this->wpdb->delete(
            $this->table_prefix . 'game_stats',
            ['id' => $stat_id, 'player_id' => $this->id],
            ['%d', '%d']
        );
    }

    public function get_recruitment_info() {
        return $this->wpdb->get_row($this->wpdb->prepare("
            SELECT * FROM {$this->table_prefix}recruitment
            WHERE player_id = %d
        ", $this->id));
    }

    public function update_recruitment($data) {
        $existing = $this->get_recruitment_info();
        
        if ($existing) {
            return $this->wpdb->update(
                $this->table_prefix . 'recruitment',
                $data,
                ['player_id' => $this->id]
            );
        } else {
            $data['player_id'] = $this->id;
            return $this->wpdb->insert(
                $this->table_prefix . 'recruitment',
                $data
            );
        }
    }

    public function get_career_stats() {
        // Get regular season stats
        $regular_season = $this->wpdb->get_row($this->wpdb->prepare("
            SELECT 
                COUNT(DISTINCT game_date) as games_played,
                SUM(points) as total_points,
                SUM(rebounds) as total_rebounds,
                SUM(assists) as total_assists,
                SUM(steals) as total_steals,
                SUM(blocks) as total_blocks,
                SUM(fg_made) as fg_made,
                SUM(fg_attempted) as fg_attempted,
                SUM(three_made) as three_made,
                SUM(three_attempted) as three_attempted,
                SUM(ft_made) as ft_made,
                SUM(ft_attempted) as ft_attempted
            FROM {$this->table_prefix}game_stats
            WHERE player_id = %d
        ", $this->id));

        // Calculate averages and percentages
        if ($regular_season && $regular_season->games_played > 0) {
            $career_stats = [
                'games_played' => $regular_season->games_played,
                'ppg' => round($regular_season->total_points / $regular_season->games_played, 1),
                'rpg' => round($regular_season->total_rebounds / $regular_season->games_played, 1),
                'apg' => round($regular_season->total_assists / $regular_season->games_played, 1),
                'spg' => round($regular_season->total_steals / $regular_season->games_played, 1),
                'bpg' => round($regular_season->total_blocks / $regular_season->games_played, 1),
                'fg_pct' => $regular_season->fg_attempted > 0 ? 
                    round(($regular_season->fg_made / $regular_season->fg_attempted) * 100, 1) : 0,
                'three_pct' => $regular_season->three_attempted > 0 ? 
                    round(($regular_season->three_made / $regular_season->three_attempted) * 100, 1) : 0,
                'ft_pct' => $regular_season->ft_attempted > 0 ? 
                    round(($regular_season->ft_made / $regular_season->ft_attempted) * 100, 1) : 0
            ];

            return $career_stats;
        }

        return false;
    }

    private function validate_stats($stats) {
        // Check for negative values
        $numeric_fields = ['minutes_played', 'points', 'assists', 'rebounds', 'steals', 'blocks',
                          'fg_made', 'fg_attempted', 'three_made', 'three_attempted', 'ft_made', 'ft_attempted'];
        
        foreach ($numeric_fields as $field) {
            if (isset($stats[$field]) && $stats[$field] < 0) {
                return false;
            }
        }

        // Validate shooting stats
        if (isset($stats['fg_made'], $stats['fg_attempted']) && $stats['fg_made'] > $stats['fg_attempted']) {
            return false;
        }
        if (isset($stats['three_made'], $stats['three_attempted']) && $stats['three_made'] > $stats['three_attempted']) {
            return false;
        }
        if (isset($stats['ft_made'], $stats['ft_attempted']) && $stats['ft_made'] > $stats['ft_attempted']) {
            return false;
        }

        // Validate total points calculation
        if (isset($stats['points'], $stats['fg_made'], $stats['three_made'], $stats['ft_made'])) {
            $calculated_points = ($stats['fg_made'] - $stats['three_made']) * 2 + 
                               $stats['three_made'] * 3 + 
                               $stats['ft_made'];
            if ($calculated_points != $stats['points']) {
                return false;
            }
        }

        return true;
    }
}