<?php
namespace EBM;

class PostTypes {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        // Private constructor to enforce singleton
    }

    public function register_post_types() {
        $this->register_team_post_type();
        $this->register_player_post_type();
    }

    private function register_team_post_type() {
        $labels = [
            'name'               => 'Teams',
            'singular_name'      => 'Team',
            'menu_name'          => 'Teams',
            'add_new'           => 'Add New Team',
            'add_new_item'      => 'Add New Team',
            'edit_item'         => 'Edit Team',
            'new_item'          => 'New Team',
            'view_item'         => 'View Team',
            'search_items'      => 'Search Teams',
            'not_found'         => 'No teams found',
            'not_found_in_trash'=> 'No teams found in trash'
        ];

        $args = [
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'team'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => ['title', 'editor', 'thumbnail'],
            'menu_icon'          => 'dashicons-groups'
        ];

        register_post_type('ebm_team', $args);
    }

    private function register_player_post_type() {
        $labels = [
            'name'               => 'Players',
            'singular_name'      => 'Player',
            'menu_name'          => 'Players',
            'add_new'           => 'Add New Player',
            'add_new_item'      => 'Add New Player',
            'edit_item'         => 'Edit Player',
            'new_item'          => 'New Player',
            'view_item'         => 'View Player',
            'search_items'      => 'Search Players',
            'not_found'         => 'No players found',
            'not_found_in_trash'=> 'No players found in trash'
        ];

        $args = [
            'labels'              => $labels,
            'public'              => true,
            'publicly_queryable'  => true,
            'show_ui'            => true,
            'show_in_menu'       => false,
            'query_var'          => true,
            'rewrite'            => ['slug' => 'player'],
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => ['title', 'editor', 'thumbnail'],
            'menu_icon'          => 'dashicons-businessman'
        ];

        register_post_type('ebm_player', $args);
    }
}