<?php
namespace EBM;

class Database {
    private static $instance = null;
    private $wpdb;
    private $charset_collate;

    private function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->charset_collate = $wpdb->get_charset_collate();
    }

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function create_tables() {
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

        $this->create_game_stats_table();
        $this->create_recruitment_table();
    }

    private function create_game_stats_table() {
        $table_name = $this->wpdb->prefix . 'ebm_game_stats';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            player_id bigint(20) NOT NULL,
            game_date date NOT NULL,
            minutes_played int(3),
            points int(3),
            rebounds int(3),
            assists int(3),
            steals int(3),
            blocks int(3),
            fg_made int(3),
            fg_attempted int(3),
            three_made int(3),
            three_attempted int(3),
            ft_made int(3),
            ft_attempted int(3),
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $this->charset_collate;";

        dbDelta($sql);
    }

    private function create_recruitment_table() {
        $table_name = $this->wpdb->prefix . 'ebm_recruitment';
        
        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
            id bigint(20) NOT NULL AUTO_INCREMENT,
            player_id bigint(20) NOT NULL,
            college_name varchar(255),
            division varchar(50),
            status varchar(50),
            commitment_date date,
            notes text,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id)
        ) $this->charset_collate;";

        dbDelta($sql);
    }
}