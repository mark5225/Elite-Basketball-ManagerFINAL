<?php
namespace EBM\Frontend;

class Widgets {
    public function __construct() {
        add_action('widgets_init', array($this, 'register_widgets'));
    }

    public function register_widgets() {
        register_widget('EBM\Frontend\Widgets\TeamRoster');
        register_widget('EBM\Frontend\Widgets\PlayerStats');
        register_widget('EBM\Frontend\Widgets\RecruitingCommitments');
    }
}

class TeamRoster extends \WP_Widget {
    public function __construct() {
        parent::__construct(
            'ebm_team_roster',
            __('Team Roster', 'elite-basketball-manager'),
            array('description' => __('Display team roster with filters', 'elite-basketball-manager'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = apply_filters('widget_title', $instance['title']);
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $team_id = isset($instance['team_id']) ? $instance['team_id'] : 0;
        if ($team_id) {
            $players = get_posts(array(
                'post_type' => 'ebm_player',
                'posts_per_page' => -1,
                'meta_query' => array(
                    array(
                        'key' => '_ebm_team_id',
                        'value' => $team_id
                    )
                ),
                'orderby' => 'menu_order',
                'order' => 'ASC'
            ));

            if ($players) {
                echo '<div class="ebm-widget-roster">';
                foreach ($players as $player) {
                    $jersey = get_post_meta($player->ID, '_ebm_jersey_number', true);
                    $position = get_the_terms($player->ID, 'ebm_position');
                    $position = $position ? $position[0]->name : '';

                    echo '<div class="ebm-widget-player">';
                    if (has_post_thumbnail($player->ID)) {
                        echo '<div class="ebm-widget-player-photo">';
                        echo get_the_post_thumbnail($player->ID, 'thumbnail');
                        echo '</div>';
                    }
                    echo '<div class="ebm-widget-player-info">';
                    echo '<span class="ebm-widget-player-number">#' . esc_html($jersey) . '</span>';
                    echo '<a href="' . get_permalink($player->ID) . '">' . esc_html($player->post_title) . '</a>';
                    echo '<span class="ebm-widget-player-position">' . esc_html($position) . '</span>';
                    echo '</div>';
                    echo '</div>';
                }
                echo '</div>';
            } else {
                echo '<p>' . __('No players found.', 'elite-basketball-manager') . '</p>';
            }
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $team_id = isset($instance['team_id']) ? $instance['team_id'] : 0;

        $teams = get_posts(array(
            'post_type' => 'ebm_team',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
                <?php _e('Title:', 'elite-basketball-manager'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" 
                   name="<?php echo $this->get_field_name('title'); ?>" type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('team_id'); ?>">
                <?php _e('Team:', 'elite-basketball-manager'); ?>
            </label>
            <select class="widefat" id="<?php echo $this->get_field_id('team_id'); ?>" 
                    name="<?php echo $this->get_field_name('team_id'); ?>">
                <option value=""><?php _e('Select Team', 'elite-basketball-manager'); ?></option>
                <?php foreach ($teams as $team) : ?>
                    <option value="<?php echo $team->ID; ?>" <?php selected($team_id, $team->ID); ?>>
                        <?php echo esc_html($team->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['team_id'] = intval($new_instance['team_id']);
        return $instance;
    }
}

class PlayerStats extends \WP_Widget {
    public function __construct() {
        parent::__construct(
            'ebm_player_stats',
            __('Player Stats', 'elite-basketball-manager'),
            array('description' => __('Display player statistics', 'elite-basketball-manager'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = apply_filters('widget_title', $instance['title']);
        if (!empty($title)) {
            echo $args['before_title'] . $title . $args['after_title'];
        }

        $player_id = isset($instance['player_id']) ? $instance['player_id'] : 0;
        if ($player_id) {
            $player = new \EBM\Models\Player($player_id);
            $stats = $player->get_averages();

            if ($stats) {
                echo '<div class="ebm-widget-stats">';
                echo '<div class="ebm-widget-stat">';
                echo '<span class="ebm-widget-stat-label">' . __('PPG', 'elite-basketball-manager') . '</span>';
                echo '<span class="ebm-widget-stat-value">' . number_format($stats->ppg, 1) . '</span>';
                echo '</div>';
                echo '<div class="ebm-widget-stat">';
                echo '<span class="ebm-widget-stat-label">' . __('RPG', 'elite-basketball-manager') . '</span>';
                echo '<span class="ebm-widget-stat-value">' . number_format($stats->rpg, 1) . '</span>';
                echo '</div>';
                echo '<div class="ebm-widget-stat">';
                echo '<span class="ebm-widget-stat-label">' . __('APG', 'elite-basketball-manager') . '</span>';
                echo '<span class="ebm-widget-stat-value">' . number_format($stats->apg, 1) . '</span>';
                echo '</div>';
                echo '</div>';
            } else {
                echo '<p>' . __('No stats available.', 'elite-basketball-manager') . '</p>';
            }
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = isset($instance['title']) ? $instance['title'] : '';
        $player_id = isset($instance['player_id']) ? $instance['player_id'] : 0;

        $players = get_posts(array(
            'post_type' => 'ebm_player',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ));
        ?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>">
                <?php _e('Title:', 'elite-basketball-manager'); ?>
            </label>
            <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" 
                   name="<?php echo $this->get_field_name('title'); ?>" type="text" 
                   value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('player_id'); ?>">
                <?php _e('Player:', 'elite-basketball-manager'); ?>
            </label>
            <select class="widefat" id="<?php echo $this->get_field_id('player_id'); ?>" 
                    name="<?php echo $this->get_field_name('player_id'); ?>">
                <option value=""><?php _e('Select Player', 'elite-basketball-manager'); ?></option>
                <?php foreach ($players as $player) : ?>
                    <option value="<?php echo $player->ID; ?>" <?php selected($player_id, $player->ID); ?>>
                        <?php echo esc_html($player->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['player_id'] = intval($new_instance['player_id']);
        return $instance;
    }
}