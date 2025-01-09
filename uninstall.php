<?php
if (!defined('WP_UNINSTALL_PLUGIN')) {
    die;
}

// Delete all plugin data
global $wpdb;

// Delete custom post types and associated meta
$post_types = array('ebm_player', 'ebm_team', 'ebm_game');
foreach ($post_types as $post_type) {
    $posts = get_posts(array(
        'post_type' => $post_type,
        'numberposts' => -1,
        'post_status' => 'any'
    ));

    foreach ($posts as $post) {
        wp_delete_post($post->ID, true);
    }
}

// Delete custom taxonomies
$taxonomies = array('ebm_position', 'ebm_team_category');
foreach ($taxonomies as $taxonomy) {
    $terms = get_terms(array(
        'taxonomy' => $taxonomy,
        'hide_empty' => false
    ));

    foreach ($terms as $term) {
        wp_delete_term($term->term_id, $taxonomy);
    }
}

// Drop custom tables
$tables = array(
    'game_stats',
    'recruitment',
    'recruiting_interactions'
);

foreach ($tables as $table) {
    $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}ebm_{$table}");
}

// Delete options
$options = array(
    'ebm_version',
    'ebm_settings',
    'ebm_db_version'
);

foreach ($options as $option) {
    delete_option($option);
}