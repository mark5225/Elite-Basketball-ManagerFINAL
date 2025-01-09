<?php if (!defined('ABSPATH')) exit; ?>

<div class="ebm-player-profile">
    <div class="ebm-player-header">
        <div class="ebm-player-photo-large">
            <?php if (has_post_thumbnail($player->ID)) : ?>
                <?php echo get_the_post_thumbnail($player->ID, 'large', array('class' => 'ebm-player-img')); ?>
            <?php else : ?>
                <div class="ebm-player-img-placeholder">
                    <i class="dashicons dashicons-businessman"></i>
                </div>
            <?php endif; ?>
            <div class="ebm-player-number"><?php echo esc_html($jersey); ?></div>
        </div>

        <div class="ebm-player-info-header">
            <h1 class="ebm-player-name"><?php echo esc_html($player->post_title); ?></h1>
            
            <div class="ebm-player-meta">
                <?php
                $position = get_the_terms($player->ID, 'ebm_position');
                if ($position) {
                    echo '<span class="ebm-player-position">' . esc_html($position[0]->name) . '</span>';
                }
                ?>
                
                <?php if ($class_year) : ?>
                    <span class="ebm-meta-separator">•</span>
                    <span class="ebm-player-class"><?php echo esc_html($class_year); ?></span>
                <?php endif; ?>

                <?php if ($height) : ?>
                    <span class="ebm-meta-separator">•</span>
                    <span class="ebm-player-height"><?php echo esc_html($height); ?></span>
                <?php endif; ?>

                <?php if ($weight) : ?>
                    <span class="ebm-meta-separator">•</span>
                    <span class="ebm-player-weight"><?php echo esc_html($weight); ?> lbs</span>
                <?php endif; ?>
            </div>
            
            <?php if ($hometown) : ?>
                <div class="ebm-player-hometown">
                    <i class="dashicons dashicons-location"></i>
                    <?php echo esc_html($hometown); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="ebm-profile-nav">
        <button class="ebm-profile-tab active" data-target="#stats">
            <?php _e('Stats', 'elite-basketball-manager'); ?>
        </button>
        <button class="ebm-profile-tab" data-target="#highlights">
            <?php _e('Highlights', 'elite-basketball-manager'); ?>
        </button>
        <?php if (!empty($recruitment)) : ?>
            <button class="ebm-profile-tab" data-target="#recruitment">
                <?php _e('Recruitment', 'elite-basketball-manager'); ?>
            </button>
        <?php endif; ?>
    </div>

    <div class="ebm-profile-content" id="stats">
        <?php if (!empty($stats)) : ?>
            <div class="ebm-season-stats">
                <div class="ebm-stats-header">
                    <h3><?php _e('Season Statistics', 'elite-basketball-manager'); ?></h3>
                    <div class="ebm-stats-actions">
                        <button class="ebm-stats-toggle" data-target=".ebm-stats-table" 
                            data-text="<?php _e('Show Full Stats', 'elite-basketball-manager'); ?>"
                            data-alt-text="<?php _e('Hide Full Stats', 'elite-basketball-manager'); ?>">
                            <?php _e('Show Full Stats', 'elite-basketball-manager'); ?>
                        </button>
                        <button class="ebm-print-stats">
                            <i class="dashicons dashicons-printer"></i>
                            <?php _e('Print', 'elite-basketball-manager'); ?>
                        </button>
                        <button class="ebm-export-stats">
                            <i class="dashicons dashicons-download"></i>
                            <?php _e('Export', 'elite-basketball-manager'); ?>
                        </button>
                    </div>
                </div>

                <div class="ebm-stats-overview">
                    <div class="ebm-stat-box">
                        <div class="ebm-stat-value ebm-career-ppg">
                            <?php
                            $total_games = count($stats);
                            $total_points = array_sum(array_column($stats, 'points'));
                            echo number_format($total_points / $total_games, 1);
                            ?>
                        </div>
                        <div class="ebm-stat-label"><?php _e('PPG', 'elite-basketball-manager'); ?></div>
                    </div>

                    <div class="ebm-stat-box">
                        <div class="ebm-stat-value ebm-career-rpg">
                            <?php
                            $total_rebounds = array_sum(array_column($stats, 'rebounds'));
                            echo number_format($total_rebounds / $total_games, 1);
                            ?>
                        </div>
                        <div class="ebm-stat-label"><?php _e('RPG', 'elite-basketball-manager'); ?></div>
                    </div>

                    <div class="ebm-stat-box">
                        <div class="ebm-stat-value ebm-career-apg">
                            <?php
                            $total_assists = array_sum(array_column($stats, 'assists'));
                            echo number_format($total_assists / $total_games, 1);
                            ?>
                        </div>
                        <div class="ebm-stat-label"><?php _e('APG', 'elite-basketball-manager'); ?></div>
                    </div>

                    <div class="ebm-stat-box">
                        <div class="ebm-stat-value ebm-career-fg">
                            <?php
                            $total_fg_made = array_sum(array_column($stats, 'fg_made'));
                            $total_fg_attempted = array_sum(array_column($stats, 'fg_attempted'));
                            echo number_format(($total_fg_made / $total_fg_attempted) * 100, 1) . '%';
                            ?>
                        </div>
                        <div class="ebm-stat-label"><?php _e('FG%', 'elite-basketball-manager'); ?></div>
                    </div>

                    <div class="ebm-stat-box">
                        <div class="ebm-stat-value ebm-career-three">
                            <?php
                            $total_three_made = array_sum(array_column($stats, 'three_made'));
                            $total_three_attempted = array_sum(array_column($stats, 'three_attempted'));
                            echo number_format(($total_three_made / $total_three_attempted) * 100, 1) . '%';
                            ?>
                        </div>
                        <div class="ebm-stat-label"><?php _e('3P%', 'elite-basketball-manager'); ?></div>
                    </div>
                </div>

                <div class="ebm-stats-table-wrapper" style="display: none;">
                    <table class="ebm-stats-table">
                        <thead>
                            <tr>
                                <th><?php _e('Date', 'elite-basketball-manager'); ?></th>
                                <th><?php _e('Opponent', 'elite-basketball-manager'); ?></th>
                                <th><?php _e('MIN', 'elite-basketball-manager'); ?></th>
                                <th><?php _e('PTS', 'elite-basketball-manager'); ?></th>
                                <th><?php _e('REB', 'elite-basketball-manager'); ?></th>
                                <th><?php _e('AST', 'elite-basketball-manager'); ?></th>
                                <th><?php _e('STL', 'elite-basketball-manager'); ?></th>
                                <th><?php _e('BLK', 'elite-basketball-manager'); ?></th>
                                <th><?php _e('FG', 'elite-basketball-manager'); ?></th>
                                <th><?php _e('3PT', 'elite-basketball-manager'); ?></th>
                                <th><?php _e('FT', 'elite-basketball-manager'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($stats as $stat) : ?>
                                <tr class="ebm-game-stat"
                                    data-points="<?php echo esc_attr($stat->points); ?>"
                                    data-rebounds="<?php echo esc_attr($stat->rebounds); ?>"
                                    data-assists="<?php echo esc_attr($stat->assists); ?>"
                                    data-steals="<?php echo esc_attr($stat->steals); ?>"
                                    data-blocks="<?php echo esc_attr($stat->blocks); ?>"
                                    data-fg-made="<?php echo esc_attr($stat->fg_made); ?>"
                                    data-fg-attempted="<?php echo esc_attr($stat->fg_attempted); ?>"
                                    data-three-made="<?php echo esc_attr($stat->three_made); ?>"
                                    data-three-attempted="<?php echo esc_attr($stat->three_attempted); ?>"
                                    data-ft-made="<?php echo esc_attr($stat->ft_made); ?>"
                                    data-ft-attempted="<?php echo esc_attr($stat->ft_attempted); ?>">
                                    <td><?php echo esc_html(date('M j, Y', strtotime($stat->game_date))); ?></td>
                                    <td><?php echo esc_html($stat->opponent); ?></td>
                                    <td><?php echo esc_html($stat->minutes_played); ?></td>
                                    <td><?php echo esc_html($stat->points); ?></td>
                                    <td><?php echo esc_html($stat->rebounds); ?></td>
                                    <td><?php echo esc_html($stat->assists); ?></td>
                                    <td><?php echo esc_html($stat->steals); ?></td>
                                    <td><?php echo esc_html($stat->blocks); ?></td>
                                    <td><?php echo esc_html($stat->fg_made . '/' . $stat->fg_attempted); ?></td>
                                    <td><?php echo esc_html($stat->three_made . '/' . $stat->three_attempted); ?></td>
                                    <td><?php echo esc_html($stat->ft_made . '/' . $stat->ft_attempted); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else : ?>
            <p class="ebm-no-stats"><?php _e('No stats available.', 'elite-basketball-manager'); ?></p>
        <?php endif; ?>
    </div>

    <div class="ebm-profile-content" id="highlights" style="display: none;">
        <?php
        $media_query = new WP_Query(array(
            'post_type' => 'ebm_news',
            'meta_query' => array(
                array(
                    'key' => '_ebm_featured_player',
                    'value' => $player->ID
                )
            )
        ));

        if ($media_query->have_posts()) :
            while ($media_query->have_posts()) : $media_query->the_post();
                ?>
                <div class="ebm-highlight-item">
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="ebm-highlight-thumb">
                            <?php the_post_thumbnail('medium'); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="ebm-highlight-content">
                        <h3><?php the_title(); ?></h3>
                        <div class="ebm-highlight-date">
                            <?php echo get_the_date(); ?>
                        </div>
                        <?php the_excerpt(); ?>
                        <a href="<?php the_permalink(); ?>" class="ebm-read-more">
                            <?php _e('Read More', 'elite-basketball-manager'); ?>
                        </a>
                    </div>
                </div>
                <?php
            endwhile;
            wp_reset_postdata();
        else :
            ?>
            <p class="ebm-no-highlights">
                <?php _e('No highlights available.', 'elite-basketball-manager'); ?>
            </p>
            <?php
        endif;
        ?>
    </div>

    <?php if (!empty($recruitment)) : ?>
        <div class="ebm-profile-content" id="recruitment" style="display: none;">
            <div class="ebm-recruitment-status">
                <?php if ($recruitment->status === 'committed') : ?>
                    <div class="ebm-commitment">
                        <div class="ebm-commitment-school">
                            <?php if ($recruitment->college_logo_id) : ?>
                                <img src="<?php echo wp_get_attachment_image_url($recruitment->college_logo_id, 'medium'); ?>"
                                     alt="<?php echo esc_attr($recruitment->college_name); ?>"
                                     class="ebm-school-logo">
                            <?php endif; ?>
                            <h3><?php echo esc_html($recruitment->college_name); ?></h3>
                        </div>
                        <div class="ebm-commitment-details">
                            <div class="ebm-commitment-division">
                                <?php echo esc_html($recruitment->division); ?>
                            </div>
                            <div class="ebm-commitment-date">
                                <?php 
                                printf(
                                    __('Committed on %s', 'elite-basketball-manager'),
                                    date('F j, Y', strtotime($recruitment->commitment_date))
                                );
                                ?>
                            </div>
                        </div>
                    </div>
                <?php else : ?>
                    <div class="ebm-recruiting-schools">
                        <h3><?php _e('Recruiting Timeline', 'elite-basketball-manager'); ?></h3>
                        <!-- Add recruiting timeline here -->
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>