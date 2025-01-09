<?php if (!defined('ABSPATH')) exit; ?>

<div class="ebm-team-stats">
    <div class="ebm-team-header">
        <div class="ebm-team-info">
            <?php if (has_post_thumbnail($team->ID)) : ?>
                <div class="ebm-team-logo">
                    <?php echo get_the_post_thumbnail($team->ID, 'medium'); ?>
                </div>
            <?php endif; ?>
            
            <div class="ebm-team-details">
                <h1 class="ebm-team-name"><?php echo esc_html($team->post_title); ?></h1>
                <?php 
                $season = get_post_meta($team->ID, '_ebm_season', true);
                $coach = get_post_meta($team->ID, '_ebm_coach', true);
                $record = get_post_meta($team->ID, '_ebm_record', true);
                ?>
                
                <div class="ebm-team-meta">
                    <?php if ($season) : ?>
                        <span class="ebm-team-season"><?php echo esc_html($season); ?></span>
                    <?php endif; ?>
                    
                    <?php if ($coach) : ?>
                        <span class="ebm-meta-separator">•</span>
                        <span class="ebm-team-coach"><?php echo esc_html($coach); ?></span>
                    <?php endif; ?>
                    
                    <?php if ($record) : ?>
                        <span class="ebm-meta-separator">•</span>
                        <span class="ebm-team-record"><?php echo esc_html($record); ?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="ebm-stats-nav">
        <button class="ebm-stats-tab active" data-target="#team-stats">
            <?php _e('Team Stats', 'elite-basketball-manager'); ?>
        </button>
        <button class="ebm-stats-tab" data-target="#player-stats">
            <?php _e('Player Stats', 'elite-basketball-manager'); ?>
        </button>
        <button class="ebm-stats-tab" data-target="#game-stats">
            <?php _e('Game Stats', 'elite-basketball-manager'); ?>
        </button>
    </div>

    <div class="ebm-stats-content" id="team-stats">
        <div class="ebm-stats-grid">
            <?php
            // Calculate team averages
            $total_games = count($stats);
            $team_stats = array(
                'points' => array_sum(array_column($stats, 'total_points')) / $total_games,
                'rebounds' => array_sum(array_column($stats, 'total_rebounds')) / $total_games,
                'assists' => array_sum(array_column($stats, 'total_assists')) / $total_games
            );
            ?>
            
            <div class="ebm-stat-card">
                <div class="ebm-stat-value"><?php echo number_format($team_stats['points'], 1); ?></div>
                <div class="ebm-stat-label"><?php _e('Points Per Game', 'elite-basketball-manager'); ?></div>
            </div>
            
            <div class="ebm-stat-card">
                <div class="ebm-stat-value"><?php echo number_format($team_stats['rebounds'], 1); ?></div>
                <div class="ebm-stat-label"><?php _e('Rebounds Per Game', 'elite-basketball-manager'); ?></div>
            </div>
            
            <div class="ebm-stat-card">
                <div class="ebm-stat-value"><?php echo number_format($team_stats['assists'], 1); ?></div>
                <div class="ebm-stat-label"><?php _e('Assists Per Game', 'elite-basketball-manager'); ?></div>
            </div>
        </div>

        <?php if ($atts['show_charts'] === 'yes') : ?>
            <div class="ebm-charts-grid">
                <!-- Points Distribution Chart -->
                <div class="ebm-chart-container">
                    <h3><?php _e('Scoring Distribution', 'elite-basketball-manager'); ?></h3>
                    <canvas class="ebm-stats-chart" data-chart-type="pie" data-chart-data='<?php 
                        echo json_encode(array(
                            'labels' => array_map(function($stat) {
                                return get_the_title($stat->player_id);
                            }, $stats),
                            'datasets' => array(array(
                                'data' => array_column($stats, 'total_points'),
                                'backgroundColor' => array('#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b')
                            ))
                        ));
                    ?>'></canvas>
                </div>

                <!-- Game-by-Game Scoring -->
                <div class="ebm-chart-container">
                    <h3><?php _e('Game-by-Game Scoring', 'elite-basketball-manager'); ?></h3>
                    <canvas class="ebm-stats-chart" data-chart-type="line" data-chart-data='<?php 
                        // Get game-by-game data
                        $games = $wpdb->get_results($wpdb->prepare("
                            SELECT game_date, SUM(points) as total_points
                            FROM {$wpdb->prefix}ebm_game_stats
                            WHERE player_id IN (
                                SELECT post_id FROM {$wpdb->postmeta}
                                WHERE meta_key = '_ebm_team_id'
                                AND meta_value = %d
                            )
                            GROUP BY game_date
                            ORDER BY game_date ASC
                        ", $team->ID));

                        echo json_encode(array(
                            'labels' => array_map(function($game) {
                                return date('M j', strtotime($game->game_date));
                            }, $games),
                            'datasets' => array(array(
                                'label' => __('Team Points', 'elite-basketball-manager'),
                                'data' => array_column($games, 'total_points'),
                                'borderColor' => '#4e73df',
                                'fill' => false
                            ))
                        ));
                    ?>'></canvas>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="ebm-stats-content" id="player-stats" style="display: none;">
        <table class="ebm-stats-table">
            <thead>
                <tr>
                    <th><?php _e('Player', 'elite-basketball-manager'); ?></th>
                    <th><?php _e('GP', 'elite-basketball-manager'); ?></th>
                    <th><?php _e('PPG', 'elite-basketball-manager'); ?></th>
                    <th><?php _e('RPG', 'elite-basketball-manager'); ?></th>
                    <th><?php _e('APG', 'elite-basketball-manager'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($stats as $stat) : 
                    $player = get_post($stat->player_id);
                    if (!$player) continue;
                    ?>
                    <tr>
                        <td>
                            <a href="<?php echo get_permalink($player->ID); ?>">
                                <?php echo esc_html($player->post_title); ?>
                            </a>
                        </td>
                        <td><?php echo esc_html($stat->games_played); ?></td>
                        <td><?php echo number_format($stat->total_points / $stat->games_played, 1); ?></td>
                        <td><?php echo number_format($stat->total_rebounds / $stat->games_played, 1); ?></td>
                        <td><?php echo number_format($stat->total_assists / $stat->games_played, 1); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="ebm-stats-content" id="game-stats" style="display: none;">
        <?php
        $games = $wpdb->get_results($wpdb->prepare("
            SELECT 
                gs.game_date,
                gs.opponent,
                SUM(gs.points) as team_points,
                GROUP_CONCAT(
                    CONCAT(
                        p.post_title,
                        '|',
                        gs.points
                    )
                    ORDER BY gs.points DESC
                    SEPARATOR ';'
                ) as scorers
            FROM {$wpdb->prefix}ebm_game_stats gs
            JOIN {$wpdb->posts} p ON gs.player_id = p.ID
            WHERE gs.player_id IN (
                SELECT post_id 
                FROM {$wpdb->postmeta}
                WHERE meta_key = '_ebm_team_id'
                AND meta_value = %d
            )
            GROUP BY gs.game_date, gs.opponent
            ORDER BY gs.game_date DESC
        ", $team->ID));
        ?>

        <div class="ebm-games-list">
            <?php foreach ($games as $game) : ?>
                <div class="ebm-game-item">
                    <div class="ebm-game-header">
                        <div class="ebm-game-date">
                            <?php echo date('F j, Y', strtotime($game->game_date)); ?>
                        </div>
                        <div class="ebm-game-opponent">
                            <?php echo esc_html($game->opponent); ?>
                        </div>
                        <div class="ebm-game-score">
                            <?php echo esc_html($game->team_points); ?> pts
                        </div>
                    </div>

                    <div class="ebm-game-details">
                        <div class="ebm-top-scorers">
                            <?php
                            $scorers = explode(';', $game->scorers);
                            foreach (array_slice($scorers, 0, 3) as $scorer) {
                                list($name, $points) = explode('|', $scorer);
                                echo '<div class="ebm-scorer">';
                                echo esc_html($name) . ' - ' . esc_html($points) . ' pts';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>