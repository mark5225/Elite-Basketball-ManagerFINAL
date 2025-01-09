<?php if (!defined('ABSPATH')) exit; ?>

<div class="ebm-roster-table-view">
    <div class="ebm-roster-filters">
        <select class="ebm-filter" data-filter="position">
            <option value=""><?php _e('All Positions', 'elite-basketball-manager'); ?></option>
            <?php
            $positions = get_terms(array(
                'taxonomy' => 'ebm_position',
                'hide_empty' => true
            ));
            foreach ($positions as $position) {
                echo '<option value="' . esc_attr($position->slug) . '">' . 
                    esc_html($position->name) . '</option>';
            }
            ?>
        </select>
        
        <select class="ebm-filter" data-filter="class">
            <option value=""><?php _e('All Classes', 'elite-basketball-manager'); ?></option>
            <option value="Freshman"><?php _e('Freshman', 'elite-basketball-manager'); ?></option>
            <option value="Sophomore"><?php _e('Sophomore', 'elite-basketball-manager'); ?></option>
            <option value="Junior"><?php _e('Junior', 'elite-basketball-manager'); ?></option>
            <option value="Senior"><?php _e('Senior', 'elite-basketball-manager'); ?></option>
        </select>

        <div class="ebm-search-filter">
            <input type="text" class="ebm-search" placeholder="<?php _e('Search players...', 'elite-basketball-manager'); ?>">
        </div>
    </div>

    <?php if ($players) : ?>
        <div class="ebm-table-responsive">
            <table class="ebm-roster-table">
                <thead>
                    <tr>
                        <th class="ebm-number-col">#</th>
                        <th class="ebm-player-col"><?php _e('Player', 'elite-basketball-manager'); ?></th>
                        <th class="ebm-position-col"><?php _e('Position', 'elite-basketball-manager'); ?></th>
                        <th class="ebm-height-col"><?php _e('Height', 'elite-basketball-manager'); ?></th>
                        <th class="ebm-weight-col"><?php _e('Weight', 'elite-basketball-manager'); ?></th>
                        <th class="ebm-class-col"><?php _e('Class', 'elite-basketball-manager'); ?></th>
                        <th class="ebm-hometown-col"><?php _e('Hometown', 'elite-basketball-manager'); ?></th>
                        <?php if ($atts['show_stats'] === 'yes') : ?>
                            <th class="ebm-stats-col"><?php _e('Stats', 'elite-basketball-manager'); ?></th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($players as $player) : 
                        $jersey = get_post_meta($player->ID, '_ebm_jersey_number', true);
                        $position = get_the_terms($player->ID, 'ebm_position');
                        $position = $position ? $position[0]->name : '';
                        $height = get_post_meta($player->ID, '_ebm_height', true);
                        $weight = get_post_meta($player->ID, '_ebm_weight', true);
                        $class_year = get_post_meta($player->ID, '_ebm_class_year', true);
                        $hometown = get_post_meta($player->ID, '_ebm_hometown', true);
                        
                        // Get player stats if enabled
                        if ($atts['show_stats'] === 'yes') {
                            global $wpdb;
                            $stats = $wpdb->get_row($wpdb->prepare("
                                SELECT 
                                    AVG(points) as ppg,
                                    AVG(rebounds) as rpg,
                                    AVG(assists) as apg
                                FROM {$wpdb->prefix}ebm_game_stats
                                WHERE player_id = %d
                            ", $player->ID));
                        }
                    ?>
                        <tr class="ebm-player-row" 
                            data-position="<?php echo esc_attr($position); ?>"
                            data-class="<?php echo esc_attr($class_year); ?>">
                            <td class="ebm-number-col"><?php echo esc_html($jersey); ?></td>
                            <td class="ebm-player-col">
                                <?php if (has_post_thumbnail($player->ID)) : ?>
                                    <div class="ebm-player-photo">
                                        <?php echo get_the_post_thumbnail($player->ID, 'thumbnail'); ?>
                                    </div>
                                <?php endif; ?>
                                <a href="<?php echo get_permalink($player->ID); ?>"><?php echo esc_html($player->post_title); ?></a>
                            </td>
                            <td class="ebm-position-col"><?php echo esc_html($position); ?></td>
                            <td class="ebm-height-col"><?php echo esc_html($height); ?></td>
                            <td class="ebm-weight-col"><?php echo $weight ? esc_html($weight) . ' lbs' : ''; ?></td>
                            <td class="ebm-class-col"><?php echo esc_html($class_year); ?></td>
                            <td class="ebm-hometown-col"><?php echo esc_html($hometown); ?></td>
                            <?php if ($atts['show_stats'] === 'yes' && $stats) : ?>
                                <td class="ebm-stats-col">
                                    <?php printf(
                                        '%0.1f/%0.1f/%0.1f',
                                        $stats->ppg,
                                        $stats->rpg,
                                        $stats->apg
                                    ); ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else : ?>
        <p><?php _e('No players found for this team.', 'elite-basketball-manager'); ?></p>
    <?php endif; ?>
</div>