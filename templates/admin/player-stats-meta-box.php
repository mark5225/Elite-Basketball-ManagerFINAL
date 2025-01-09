<div class="ebm-meta-box">
    <div class="ebm-stats-table-wrapper">
        <table class="ebm-stats-table">
            <thead>
                <tr>
                    <th><?php _e('Date', 'elite-basketball-manager'); ?></th>
                    <th><?php _e('Opponent', 'elite-basketball-manager'); ?></th>
                    <th><?php _e('MIN', 'elite-basketball-manager'); ?></th>
                    <th><?php _e('PTS', 'elite-basketball-manager'); ?></th>
                    <th><?php _e('AST', 'elite-basketball-manager'); ?></th>
                    <th><?php _e('REB', 'elite-basketball-manager'); ?></th>
                    <th><?php _e('STL', 'elite-basketball-manager'); ?></th>
                    <th><?php _e('BLK', 'elite-basketball-manager'); ?></th>
                    <th><?php _e('FG', 'elite-basketball-manager'); ?></th>
                    <th><?php _e('3PT', 'elite-basketball-manager'); ?></th>
                    <th><?php _e('FT', 'elite-basketball-manager'); ?></th>
                    <th><?php _e('Actions', 'elite-basketball-manager'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($stats)) : ?>
                    <?php foreach ($stats as $stat) : ?>
                        <tr>
                            <td><?php echo esc_html(date('M j, Y', strtotime($stat->game_date))); ?></td>
                            <td><?php echo esc_html($stat->opponent); ?></td>
                            <td><?php echo esc_html($stat->minutes_played); ?></td>
                            <td><?php echo esc_html($stat->points); ?></td>
                            <td><?php echo esc_html($stat->assists); ?></td>
                            <td><?php echo esc_html($stat->rebounds); ?></td>
                            <td><?php echo esc_html($stat->steals); ?></td>
                            <td><?php echo esc_html($stat->blocks); ?></td>
                            <td><?php echo esc_html($stat->fg_made . '/' . $stat->fg_attempted); ?></td>
                            <td><?php echo esc_html($stat->three_made . '/' . $stat->three_attempted); ?></td>
                            <td><?php echo esc_html($stat->ft_made . '/' . $stat->ft_attempted); ?></td>
                            <td>
                                <button type="button" class="button button-small ebm-edit-stat" data-stat-id="<?php echo esc_attr($stat->id); ?>">
                                    <?php _e('Edit', 'elite-basketball-manager'); ?>
                                </button>
                                <button type="button" class="button button-small button-link-delete ebm-delete-stat" data-stat-id="<?php echo esc_attr($stat->id); ?>">
                                    <?php _e('Delete', 'elite-basketball-manager'); ?>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="12"><?php _e('No stats recorded yet.', 'elite-basketball-manager'); ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="ebm-add-stats-form">
        <h4><?php _e('Add New Game Stats', 'elite-basketball-manager'); ?></h4>
        <form id="ebm-game-stats-form" method="post">
            <input type="hidden" name="player_id" value="<?php echo esc_attr($post->ID); ?>">
            <input type="hidden" name="action" value="ebm_save_game_stats">
            <?php wp_nonce_field('ebm_game_stats', 'ebm_game_stats_nonce'); ?>
            
            <div class="ebm-form-grid">
                <div class="form-group">
                    <label for="game_date"><?php _e('Game Date', 'elite-basketball-manager'); ?></label>
                    <input type="date" id="game_date" name="game_date" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="opponent"><?php _e('Opponent', 'elite-basketball-manager'); ?></label>
                    <input type="text" id="opponent" name="opponent" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="minutes_played"><?php _e('Minutes', 'elite-basketball-manager'); ?></label>
                    <input type="number" id="minutes_played" name="minutes_played" class="form-control" min="0" max="48">
                </div>

                <div class="form-group">
                    <label for="points"><?php _e('Points', 'elite-basketball-manager'); ?></label>
                    <input type="number" id="points" name="points" class="form-control" min="0">
                </div>

                <div class="form-group">
                    <label for="assists"><?php _e('Assists', 'elite-basketball-manager'); ?></label>
                    <input type="number" id="assists" name="assists" class="form-control" min="0">
                </div>

                <div class="form-group">
                    <label for="rebounds"><?php _e('Rebounds', 'elite-basketball-manager'); ?></label>
                    <input type="number" id="rebounds" name="rebounds" class="form-control" min="0">
                </div>

                <div class="form-group">
                    <label for="steals"><?php _e('Steals', 'elite-basketball-manager'); ?></label>
                    <input type="number" id="steals" name="steals" class="form-control" min="0">
                </div>

                <div class="form-group">
                    <label for="blocks"><?php _e('Blocks', 'elite-basketball-manager'); ?></label>
                    <input type="number" id="blocks" name="blocks" class="form-control" min="0">
                </div>
            </div>

            <div class="ebm-shooting-stats">
                <h5><?php _e('Shooting Stats', 'elite-basketball-manager'); ?></h5>
                <div class="shooting-grid">
                    <div class="form-group">
                        <label><?php _e('Field Goals', 'elite-basketball-manager'); ?></label>
                        <div class="shooting-inputs">
                            <input type="number" name="fg_made" class="form-control" min="0" placeholder="Made">
                            <span class="separator">/</span>
                            <input type="number" name="fg_attempted" class="form-control" min="0" placeholder="Attempted">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php _e('3-Pointers', 'elite-basketball-manager'); ?></label>
                        <div class="shooting-inputs">
                            <input type="number" name="three_made" class="form-control" min="0" placeholder="Made">
                            <span class="separator">/</span>
                            <input type="number" name="three_attempted" class="form-control" min="0" placeholder="Attempted">
                        </div>
                    </div>

                    <div class="form-group">
                        <label><?php _e('Free Throws', 'elite-basketball-manager'); ?></label>
                        <div class="shooting-inputs">
                            <input type="number" name="ft_made" class="form-control" min="0" placeholder="Made">
                            <span class="separator">/</span>
                            <input type="number" name="ft_attempted" class="form-control" min="0" placeholder="Attempted">
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="button button-primary">
                    <?php _e('Add Stats', 'elite-basketball-manager'); ?>
                </button>
                <button type="reset" class="button">
                    <?php _e('Clear Form', 'elite-basketball-manager'); ?>
                </button>
            </div>
        </form>
    </div>
</div>