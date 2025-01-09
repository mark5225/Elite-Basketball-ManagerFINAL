<?php
namespace EBM\DB;

class Database {
    private static $instance = null;
    private $wpdb;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }

    public function create_tables() {
        $charset_collate = $this->wpdb->get_charset_collate();

        $sql = [];
        
        // Teams table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}ebm_teams (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            team_name varchar(100) NOT NULL,
            team_type varchar(50) NOT NULL,
            location varchar(100),
            head_coach varchar(100),
            season varchar(50),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Players table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}ebm_players (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            first_name varchar(50) NOT NULL,
            last_name varchar(50) NOT NULL,
            jersey_number varchar(10),
            position varchar(50),
            height varchar(20),
            weight varchar(20),
            birth_date date,
            grade_level varchar(20),
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        // Team Players (roster) table
        $sql[] = "CREATE TABLE IF NOT EXISTS {$this->wpdb->prefix}ebm_team_players (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            team_id bigint(20) NOT NULL,
            player_id bigint(20) NOT NULL,
            status varchar(20) DEFAULT 'active',
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY team_id (team_id),
            KEY player_id (player_id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        foreach($sql as $query) {
            dbDelta($query);
        }
    }

    public function get_teams($args = []) {
        $defaults = [
            'orderby' => 'team_name',
            'order' => 'ASC',
            'type' => '',
            'limit' => 10,
            'offset' => 0
        ];

        $args = wp_parse_args($args, $defaults);
        
        $where = "WHERE 1=1";
        if (!empty($args['type'])) {
            $where .= $this->wpdb->prepare(" AND team_type = %s", $args['type']);
        }

        $sql = "SELECT * FROM {$this->wpdb->prefix}ebm_teams 
                $where 
                ORDER BY {$args['orderby']} {$args['order']}
                LIMIT {$args['limit']} OFFSET {$args['offset']}";

        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    public function get_team($id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}ebm_teams WHERE id = %d",
                $id
            ),
            ARRAY_A
        );
    }

    public function save_team($data) {
        $defaults = [
            'team_name' => '',
            'team_type' => '',
            'location' => '',
            'head_coach' => '',
            'season' => ''
        ];

        $data = wp_parse_args($data, $defaults);

        if (isset($data['id'])) {
            return $this->wpdb->update(
                "{$this->wpdb->prefix}ebm_teams",
                $data,
                ['id' => $data['id']]
            );
        } else {
            return $this->wpdb->insert(
                "{$this->wpdb->prefix}ebm_teams",
                $data
            );
        }
    }

    public function delete_team($id) {
        return $this->wpdb->delete(
            "{$this->wpdb->prefix}ebm_teams",
            ['id' => $id],
            ['%d']
        );
    }

    public function count_teams($type = '') {
        $where = '';
        if (!empty($type)) {
            $where = $this->wpdb->prepare(" WHERE team_type = %s", $type);
        }

        return $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->wpdb->prefix}ebm_teams" . $where);
    }

    public function get_players($args = []) {
        $defaults = [
            'orderby' => 'last_name',
            'order' => 'ASC',
            'limit' => 10,
            'offset' => 0,
            'team_id' => null
        ];

        $args = wp_parse_args($args, $defaults);
        
        $where = "WHERE 1=1";
        $join = "";
        
        if (!empty($args['team_id'])) {
            $join = "INNER JOIN {$this->wpdb->prefix}ebm_team_players tp ON p.id = tp.player_id";
            $where .= $this->wpdb->prepare(" AND tp.team_id = %d", $args['team_id']);
        }

        $sql = "SELECT p.* 
                FROM {$this->wpdb->prefix}ebm_players p
                $join 
                $where 
                ORDER BY {$args['orderby']} {$args['order']}
                LIMIT {$args['limit']} OFFSET {$args['offset']}";

        return $this->wpdb->get_results($sql, ARRAY_A);
    }

    public function get_player($id) {
        return $this->wpdb->get_row(
            $this->wpdb->prepare(
                "SELECT * FROM {$this->wpdb->prefix}ebm_players WHERE id = %d",
                $id
            ),
            ARRAY_A
        );
    }

    public function save_player($data) {
        $defaults = [
            'first_name' => '',
            'last_name' => '',
            'jersey_number' => '',
            'position' => '',
            'height' => '',
            'weight' => '',
            'birth_date' => null,
            'grade_level' => '',
            'notes' => ''
        ];

        $data = wp_parse_args($data, $defaults);

        if (isset($data['id'])) {
            return $this->wpdb->update(
                "{$this->wpdb->prefix}ebm_players",
                $data,
                ['id' => $data['id']]
            );
        } else {
            return $this->wpdb->insert(
                "{$this->wpdb->prefix}ebm_players",
                $data
            );
        }
    }

    public function delete_player($id) {
        // First delete from team_players
        $this->wpdb->delete(
            "{$this->wpdb->prefix}ebm_team_players",
            ['player_id' => $id],
            ['%d']
        );
        
        // Then delete player
        return $this->wpdb->delete(
            "{$this->wpdb->prefix}ebm_players",
            ['id' => $id],
            ['%d']
        );
    }

    public function add_player_to_team($team_id, $player_id) {
        return $this->wpdb->insert(
            "{$this->wpdb->prefix}ebm_team_players",
            [
                'team_id' => $team_id,
                'player_id' => $player_id,
                'status' => 'active'
            ]
        );
    }

    public function remove_player_from_team($team_id, $player_id) {
        return $this->wpdb->delete(
            "{$this->wpdb->prefix}ebm_team_players",
            [
                'team_id' => $team_id,
                'player_id' => $player_id
            ]
        );
    }

    public function get_team_players($team_id) {
        $sql = "SELECT p.*, tp.status 
                FROM {$this->wpdb->prefix}ebm_players p
                INNER JOIN {$this->wpdb->prefix}ebm_team_players tp ON p.id = tp.player_id
                WHERE tp.team_id = %d
                ORDER BY p.last_name, p.first_name";
                
        return $this->wpdb->get_results(
            $this->wpdb->prepare($sql, $team_id),
            ARRAY_A
        );
    }

    public function count_players($team_id = null) {
        $where = '';
        if ($team_id) {
            $where = $this->wpdb->prepare(
                " WHERE team_id = %d",
                $team_id
            );
        }

        return $this->wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->wpdb->prefix}ebm_players" . $where
        );
    }
}