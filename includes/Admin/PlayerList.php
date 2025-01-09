<?php
namespace EBM\Admin;

class PlayerList extends \WP_List_Table {
    public function __construct() {
        parent::__construct(array(
            'singular' => 'player',
            'plural' => 'players',
            'ajax' => false
        ));
    }

    public function get_columns() {
        return array(
            'cb' => '<input type="checkbox" />',
            'photo' => __('Photo', 'elite-basketball-manager'),
            'title' => __('Name', 'elite-basketball-manager'),
            'position' => __('Position', 'elite-basketball-manager'),
            'team' => __('Team', 'elite-basketball-manager'),
            'class_year' => __('Class', 'elite-basketball-manager'),
            'stats' => __('Stats', 'elite-basketball-manager'),
            'recruiting' => __('Recruiting', 'elite-basketball-manager')
        );
    }

    public function prepare_items() {
        $per_page = 20;
        $current_page = $this->get_pagenum();
        
        $search = isset($_REQUEST['s']) ? sanitize_text_field($_REQUEST['s']) : '';
        $team_filter = isset($_REQUEST['team']) ? intval($_REQUEST['team']) : 0;
        $position_filter = isset($_REQUEST['position']) ? sanitize_text_field($_REQUEST['position']) : '';

        $args = array(
            'post_type' => 'ebm_player',
            'posts_per_page' => $per_page,
            'paged' => $current_page,
            'orderby' => 'title',
            'order' => 'ASC',
            's' => $search
        );

        // Add team filter
        if ($team_filter) {
            $args['meta_query'][] = array(
                'key' => '_ebm_team_id',
                'value' => $team_filter
            );
        }

        // Add position filter
        if ($position_filter) {
            $args['tax_query'][] = array(
                'taxonomy' => 'ebm_position',
                'field' => 'slug',
                'terms' => $position_filter
            );
        }

        $query = new \WP_Query($args);
        $this->items = $query->posts;
        
        $this->set_pagination_args(array(
            'total_items' => $query->found_posts,
            'per_page' => $per_page,
            'total_pages' => ceil($query->found_posts / $per_page)
        ));
    }

    public function column_default($item, $column_name) {
        switch ($column_name) {
            case 'photo':
                if (has_post_thumbnail($item->ID)) {
                    return get_the_post_thumbnail($item->ID, array(50, 50));
                }
                return '<div class="ebm-no-photo"></div>';
            
            case 'position':
                $terms = get_the_terms($item->ID, 'ebm_position');
                return $terms ? $terms[0]->name : '—';
            
            case 'team':
                $team_id = get_post_meta($item->ID, '_ebm_team_id', true);
                if ($team_id) {
                    $team = get_post($team_id);
                    return sprintf(
                        '<a href="%s">%s</a>',
                        get_edit_post_link($team_id),
                        $team->post_title
                    );
                }
                return '—';
            
            case 'class_year':
                return get_post_meta($item->ID, '_ebm_class_year', true) ?: '—';
            
            case 'stats':
                global $wpdb;
                $stats = $wpdb->get_row($wpdb->prepare("
                    SELECT 
                        COUNT(DISTINCT game_date) as games,
                        AVG(points) as ppg,
                        AVG(rebounds) as rpg,
                        AVG(assists) as apg
                    FROM {$wpdb->prefix}ebm_game_stats
                    WHERE player_id = %d
                ", $item->ID));

                if ($stats && $stats->games > 0) {
                    return sprintf(
                        '%d G, %.1f/%.1f/%.1f',
                        $stats->games,
                        $stats->ppg,
                        $stats->rpg,
                        $stats->apg
                    );
                }
                return '—';
            
            case 'recruiting':
                global $wpdb;
                $recruitment = $wpdb->get_row($wpdb->prepare("
                    SELECT status, college_name, division
                    FROM {$wpdb->prefix}ebm_recruitment
                    WHERE player_id = %d
                ", $item->ID));

                if ($recruitment) {
                    if ($recruitment->status === 'committed') {
                        return sprintf(
                            '<span class="ebm-committed">%s (%s)</span>',
                            esc_html($recruitment->college_name),
                            esc_html($recruitment->division)
                        );
                    } else {
                        return sprintf(
                            '<span class="ebm-recruiting">%s</span>',
                            ucfirst($recruitment->status)
                        );
                    }
                }
                return '—';
            
            default:
                return print_r($item, true);
        }
    }

    public function column_title($item) {
        $actions = array(
            'edit' => sprintf(
                '<a href="%s">%s</a>',
                get_edit_post_link($item->ID),
                __('Edit', 'elite-basketball-manager')
            ),
            'stats' => sprintf(
                '<a href="%s">%s</a>',
                add_query_arg('view', 'stats', get_edit_post_link($item->ID)),
                __('Stats', 'elite-basketball-manager')
            ),
            'view' => sprintf(
                '<a href="%s">%s</a>',
                get_permalink($item->ID),
                __('View', 'elite-basketball-manager')
            ),
            'delete' => sprintf(
                '<a href="%s">%s</a>',
                get_delete_post_link($item->ID),
                __('Delete', 'elite-basketball-manager')
            )
        );

        return sprintf(
            '%1$s %2$s',
            '<strong>' . $item->post_title . '</strong>',
            $this->row_actions($actions)
        );
    }

    public function column_cb($item) {
        return sprintf(
            '<input type="checkbox" name="players[]" value="%s" />',
            $item->ID
        );
    }

    public function get_sortable_columns() {
        return array(
            'title' => array('title', true),
            'class_year' => array('class_year', false)
        );
    }

    public function get_bulk_actions() {
        return array(
            'delete' => __('Delete', 'elite-basketball-manager'),
            'assign_team' => __('Assign to Team', 'elite-basketball-manager'),
            'remove_team' => __('Remove from Team', 'elite-basketball-manager')
        );
    }

    protected function extra_tablenav($which) {
        if ($which === 'top') {
            // Team filter
            $teams = get_posts(array(
                'post_type' => 'ebm_team',
                'posts_per_page' => -1,
                'orderby' => 'title',
                'order' => 'ASC'
            ));

            // Position filter
            $positions = get_terms(array(
                'taxonomy' => 'ebm_position',
                'hide_empty' => false
            ));

            ?>
            <div class="alignleft actions">
                <select name="team">
                    <option value=""><?php _e('All Teams', 'elite-basketball-manager'); ?></option>
                    <?php foreach ($teams as $team) : ?>
                        <option value="<?php echo esc_attr($team->ID); ?>" <?php selected(isset($_REQUEST['team']) ? $_REQUEST['team'] : '', $team->ID); ?>>
                            <?php echo esc_html($team->post_title); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="position">
                    <option value=""><?php _e('All Positions', 'elite-basketball-manager'); ?></option>
                    <?php foreach ($positions as $position) : ?>
                        <option value="<?php echo esc_attr($position->slug); ?>" <?php selected(isset($_REQUEST['position']) ? $_REQUEST['position'] : '', $position->slug); ?>>
                            <?php echo esc_html($position->name); ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <?php submit_button(__('Filter', 'elite-basketball-manager'), '', 'filter_action', false); ?>
            </div>
            <?php
        }
    }
}