<?php if (!defined('ABSPATH')) exit; ?>

<div class="ebm-recruitment-stats">
    <div class="ebm-stats-header">
        <h2><?php _e('College Recruitment Statistics', 'elite-basketball-manager'); ?></h2>
        <div class="ebm-total-commits">
            <?php echo sprintf(
                __('%d Total College Commitments', 'elite-basketball-manager'),
                array_sum(array_column($stats, 'count'))
            ); ?>
        </div>
    </div>

    <div class="ebm-stats-container">
        <!-- Division Breakdown -->
        <div class="ebm-stats-section">
            <h3><?php _e('Division Breakdown', 'elite-basketball-manager'); ?></h3>
            <div class="ebm-division-stats">
                <?php foreach ($stats as $div) : ?>
                    <div class="ebm-stat-row">
                        <div class="ebm-stat-label"><?php echo esc_html($div->division); ?></div>
                        <div class="ebm-stat-bar-container">
                            <div class="ebm-stat-bar" style="width: <?php 
                                echo esc_attr(($div->count / max(array_column($stats, 'count'))) * 100); 
                            ?>%"></div>
                        </div>
                        <div class="ebm-stat-value"><?php echo esc_html($div->count); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if ($atts['show_map'] === 'yes') : ?>
            <!-- Geographic Distribution -->
            <div class="ebm-stats-section">
                <h3><?php _e('Geographic Distribution', 'elite-basketball-manager'); ?></h3>
                <div id="ebm-commits-map" class="ebm-map-container"></div>
                <div class="ebm-map-legend">
                    <div class="ebm-legend-item">
                        <span class="ebm-legend-color" style="background: #4e73df;"></span>
                        <span class="ebm-legend-label">1-3 Players</span>
                    </div>
                    <div class="ebm-legend-item">
                        <span class="ebm-legend-color" style="background: #1cc88a;"></span>
                        <span class="ebm-legend-label">4-7 Players</span>
                    </div>
                    <div class="ebm-legend-item">
                        <span class="ebm-legend-color" style="background: #36b9cc;"></span>
                        <span class="ebm-legend-label">8+ Players</span>
                    </div>
                </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const mapData = <?php
                    $map_data = array();
                    $commits_by_state = $wpdb->get_results("
                        SELECT 
                            college_state as state,
                            COUNT(*) as count
                        FROM {$wpdb->prefix}ebm_recruitment
                        WHERE status = 'committed'
                        GROUP BY college_state
                    ");
                    foreach ($commits_by_state as $state) {
                        $map_data[$state->state] = intval($state->count);
                    }
                    echo json_encode($map_data);
                ?>;

                // Initialize map here with your preferred mapping library
                // Example using jVectorMap:
                $('#ebm-commits-map').vectorMap({
                    map: 'us_aea',
                    backgroundColor: 'transparent',
                    series: {
                        regions: [{
                            values: mapData,
                            scale: ['#4e73df', '#1cc88a', '#36b9cc'],
                            normalizeFunction: 'polynomial'
                        }]
                    },
                    onRegionTipShow: function(e, label, code) {
                        if (mapData[code]) {
                            label.html(
                                label.html() + ': ' + mapData[code] + ' players'
                            );
                        }
                    }
                });
            });
            </script>
        <?php endif; ?>

        <!-- Recent Commitments -->
        <div class="ebm-stats-section">
            <h3><?php _e('Recent Commitments', 'elite-basketball-manager'); ?></h3>
            <div class="ebm-recent-commits">
                <?php
                $recent_commits = $wpdb->get_results("
                    SELECT 
                        r.*,
                        p.post_title as player_name,
                        p.ID as player_id
                    FROM {$wpdb->prefix}ebm_recruitment r
                    JOIN {$wpdb->posts} p ON r.player_id = p.ID
                    WHERE r.status = 'committed'
                    ORDER BY r.commitment_date DESC
                    LIMIT 5
                ");

                if ($recent_commits) : ?>
                    <?php foreach ($recent_commits as $commit) : ?>
                        <div class="ebm-commit-card">
                            <?php 
                            $player_photo = get_the_post_thumbnail_url($commit->player_id, 'thumbnail');
                            if ($player_photo) : ?>
                                <div class="ebm-commit-photo">
                                    <img src="<?php echo esc_url($player_photo); ?>" alt="">
                                </div>
                            <?php endif; ?>
                            
                            <div class="ebm-commit-info">
                                <div class="ebm-commit-player">
                                    <a href="<?php echo get_permalink($commit->player_id); ?>">
                                        <?php echo esc_html($commit->player_name); ?>
                                    </a>
                                </div>
                                <div class="ebm-commit-school">
                                    <?php echo esc_html($commit->college_name); ?>
                                    <span class="ebm-commit-division">
                                        (<?php echo esc_html($commit->division); ?>)
                                    </span>
                                </div>
                                <div class="ebm-commit-date">
                                    <?php echo date_i18n(
                                        get_option('date_format'), 
                                        strtotime($commit->commitment_date)
                                    ); ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else : ?>
                    <p><?php _e('No recent commitments.', 'elite-basketball-manager'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <!-- College Breakdown -->
        <div class="ebm-stats-section">
            <h3><?php _e('Top College Destinations', 'elite-basketball-manager'); ?></h3>
            <?php
            $top_colleges = $wpdb->get_results("
                SELECT 
                    college_name,
                    division,
                    COUNT(*) as count
                FROM {$wpdb->prefix}ebm_recruitment
                WHERE status = 'committed'
                GROUP BY college_name, division
                ORDER BY count DESC
                LIMIT 10
            ");
            ?>
            <div class="ebm-college-stats">
                <?php if ($top_colleges) : ?>
                    <div class="ebm-college-grid">
                        <?php foreach ($top_colleges as $college) : ?>
                            <div class="ebm-college-card">
                                <div class="ebm-college-name">
                                    <?php echo esc_html($college->college_name); ?>
                                </div>
                                <div class="ebm-college-meta">
                                    <span class="ebm-college-division">
                                        <?php echo esc_html($college->division); ?>
                                    </span>
                                    <span class="ebm-college-count">
                                        <?php echo sprintf(
                                            _n('%d Player', '%d Players', $college->count, 'elite-basketball-manager'),
                                            $college->count
                                        ); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <p><?php _e('No college commitment data available.', 'elite-basketball-manager'); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>