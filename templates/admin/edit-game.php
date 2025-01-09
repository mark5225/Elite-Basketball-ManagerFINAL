<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap ebm-edit-game">
    <h1 class="wp-heading-inline">
        <?php echo $game_id ? __('Edit Game', 'elite-basketball-manager') : __('Add New Game', 'elite-basketball-manager'); ?>
    </h1>

    <hr class="wp-header-end">

    <?php if (isset($message)) : ?>
        <div id="message" class="updated notice is-dismissible">
            <p><?php echo esc_html($message); ?></p>
        </div>
    <?php endif; ?>

    <form method="post" action="" class="ebm-game-form">
        <?php wp_nonce_field('ebm_save_game', 'ebm_game_nonce'); ?>
        <input type="hidden" name="game_id" value="<?php echo esc_attr($game_id); ?>">

        <div id="poststuff">
            <div id="post-body" class="metabox-holder columns-2">
                <div id="post-body-content">
                    <div class="postbox">
                        <h2 class="hndle"><?php _e('Game Details', 'elite-basketball-manager'); ?></h2>
                        <div class="inside">
                            <table class="form-table">
                                <tr>
                                    <th scope="row">
                                        <label for="game_date"><?php _e('Game Date', 'elite-basketball-manager'); ?></label>
                                    </th>
                                    <td>
                                        <input type="date" id="game_date" name="game_date" 
                                            value="<?php echo esc_attr($game_date); ?>" required>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="team_id"><?php _e('Team', 'elite-basketball-manager'); ?></label>
                                    </th>
                                    <td>
                                        <select id="team_id" name="team_id" required>
                                            <option value=""><?php _e('Select Team', 'elite-basketball-manager'); ?></option>
                                            <?php foreach ($teams as $team) : ?>
                                                <option value="<?php echo $team->ID; ?>" 
                                                    <?php selected($team_id, $team->ID); ?>>
                                                    <?php echo esc_html($team->post_title); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="opponent"><?php _e('Opponent', 'elite-basketball-manager'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" id="opponent" name="opponent" 
                                            value="<?php echo esc_attr($opponent); ?>" required>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">
                                        <label for="location"><?php _e('Location', 'elite-basketball-manager'); ?></label>
                                    </th>
                                    <td>
                                        <input type="text" id="location" name="location" 
                                            value="<?php echo esc_attr($location); ?>">
                                        <select id="game_location_type" name="game_location_type">
                                            <option value="home" <?php selected($location_type, 'home'); ?>>
                                                <?php _e('Home', 'elite-basketball-manager'); ?>
                                            </option>
                                            <option value="away" <?php selected($location_type, 'away'); ?>>
                                                <?php _e('Away', 'elite-basketball-manager'); ?>
                                            </option>
                                            <option value="neutral" <?php selected($location_type, 'neutral'); ?>>
                                                <?php _e('Neutral', 'elite-basketball-manager'); ?>
                                            </option>
                                        </select>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="postbox">
                        <h2 class="hndle"><?php _e('Game Stats', 'elite-basketball-manager'); ?></h2>
                        <div class="inside">
                            <div class="ebm-score-section">
                                <div class="ebm-team-score">
                                    <label for="team_score"><?php _e('Team Score', 'elite-basketball-manager'); ?></label>
                                    <input type="number" id="team_score" name="team_score" 
                                        value="<?php echo esc_attr($team_score); ?>" min="0">
                                </div>
                                <div class="ebm-opponent-score">
                                    <label for="opponent_score">
                                        <?php _e('Opponent Score', 'elite-basketball-manager'); ?>
                                    </label>
                                    <input type="number" id="opponent_score" name="opponent_score" 
                                        value="<?php echo esc_attr($opponent_score); ?>" min="0">
                                </div>
                            </div>

                            <div class="ebm-player-stats">
                                <h3><?php _e('Player Statistics', 'elite-basketball-manager'); ?></h3>
                                <div class="ebm-player-stats-toolbar">
                                    <button type="button" class="button" id="auto-calculate-stats">
                                        <?php _e('Auto-Calculate Stats', 'elite-basketball-manager'); ?>
                                    </button>
                                    <button type="button" class="button" id="clear-all-stats">
                                        <?php _e('Clear All Stats', 'elite-basketball-manager'); ?>
                                    </button>
                                </div>
                                <table class="wp-list-table widefat fixed striped">
                                    <thead>
                                        <tr>
                                            <th><?php _e('Player', 'elite-basketball-manager'); ?></th>
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
                                    <tbody id="ebm-player-stats-rows">
                                        <?php foreach ($players as $player) : 
                                            $stat = isset($player_stats[$player->ID]) ? $player_stats[$player->ID] : null;
                                        ?>
                                            <tr class="ebm-player-stat-row" data-player-id="<?php echo $player->ID; ?>">
                                                <td>
                                                    <?php echo esc_html($player->post_title); ?>
                                                    <input type="hidden" name="stats[<?php echo $player->ID; ?>][player_id]" 
                                                        value="<?php echo $player->ID; ?>">
                                                </td>
                                                <td>
                                                    <input type="number" name="stats[<?php echo $player->ID; ?>][minutes]" 
                                                        value="<?php echo $stat ? esc_attr($stat->minutes_played) : ''; ?>" 
                                                        min="0" max="48" class="small-text minutes-played">
                                                </td>
                                                <td>
                                                    <input type="number" name="stats[<?php echo $player->ID; ?>][points]" 
                                                        value="<?php echo $stat ? esc_attr($stat->points) : ''; ?>" 
                                                        min="0" class="small-text points" readonly>
                                                </td>
                                                <td>
                                                    <input type="number" name="stats[<?php echo $player->ID; ?>][rebounds]" 
                                                        value="<?php echo $stat ? esc_attr($stat->rebounds) : ''; ?>" 
                                                        min="0" class="small-text">
                                                </td>
                                                <td>
                                                    <input type="number" name="stats[<?php echo $player->ID; ?>][assists]" 
                                                        value="<?php echo $stat ? esc_attr($stat->assists) : ''; ?>" 
                                                        min="0" class="small-text">
                                                </td>
                                                <td>
                                                    <input type="number" name="stats[<?php echo $player->ID; ?>][steals]" 
                                                        value="<?php echo $stat ? esc_attr($stat->steals) : ''; ?>" 
                                                        min="0" class="small-text">
                                                </td>
                                                <td>
                                                    <input type="number" name="stats[<?php echo $player->ID; ?>][blocks]" 
                                                        value="<?php echo $stat ? esc_attr($stat->blocks) : ''; ?>" 
                                                        min="0" class="small-text">
                                                </td>
                                                <td class="shooting-stats">
                                                    <input type="number" name="stats[<?php echo $player->ID; ?>][fg_made]" 
                                                        value="<?php echo $stat ? esc_attr($stat->fg_made) : ''; ?>" 
                                                        min="0" class="tiny-text fg-made">
                                                    /
                                                    <input type="number" name="stats[<?php echo $player->ID; ?>][fg_attempted]" 
                                                        value="<?php echo $stat ? esc_attr($stat->fg_attempted) : ''; ?>" 
                                                        min="0" class="tiny-text fg-attempted">
                                                </td>
                                                <td class="shooting-stats">
                                                    <input type="number" name="stats[<?php echo $player->ID; ?>][three_made]" 
                                                        value="<?php echo $stat ? esc_attr($stat->three_made) : ''; ?>" 
                                                        min="0" class="tiny-text three-made">
                                                    /
                                                    <input type="number" name="stats[<?php echo $player->ID; ?>][three_attempted]" 
                                                        value="<?php echo $stat ? esc_attr($stat->three_attempted) : ''; ?>" 
                                                        min="0" class="tiny-text three-attempted">
                                                </td>
                                                <td class="shooting-stats">
                                                    <input type="number" name="stats[<?php echo $player->ID; ?>][ft_made]" 
                                                        value="<?php echo $stat ? esc_attr($stat->ft_made) : ''; ?>" 
                                                        min="0" class="tiny-text ft-made">
                                                    /
                                                    <input type="number" name="stats[<?php echo $player->ID; ?>][ft_attempted]" 
                                                        value="<?php echo $stat ? esc_attr($stat->ft_attempted) : ''; ?>" 
                                                        min="0" class="tiny-text ft-attempted">
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td><strong><?php _e('Totals', 'elite-basketball-manager'); ?></strong></td>
                                            <td id="total-minutes">0</td>
                                            <td id="total-points">0</td>
                                            <td id="total-rebounds">0</td>
                                            <td id="total-assists">0</td>
                                            <td id="total-steals">0</td>
                                            <td id="total-blocks">0</td>
                                            <td id="total-fg">0/0</td>
                                            <td id="total-three">0/0</td>
                                            <td id="total-ft">0/0</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div id="postbox-container-1" class="postbox-container">
                    <div class="postbox">
                        <h2 class="hndle"><?php _e('Game Actions', 'elite-basketball-manager'); ?></h2>
                        <div class="inside">
                            <div class="submitbox">
                                <div id="major-publishing-actions">
                                    <div id="publishing-action">
                                        <input type="submit" name="save_game" id="publish" 
                                            class="button button-primary button-large" 
                                            value="<?php _e('Save Game', 'elite-basketball-manager'); ?>">
                                    </div>
                                    <div class="clear"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="postbox">
                        <h2 class="hndle"><?php _e('Game Notes', 'elite-basketball-manager'); ?></h2>
                        <div class="inside">
                            <textarea name="game_notes" id="game_notes" rows="5" 
                                class="widefat"><?php echo esc_textarea($game_notes); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
jQuery(document).ready(function($) {
    // Auto-calculate points based on shooting stats
    $('.ebm-player-stat-row').on('change', '.shooting-stats input', function() {
        var row = $(this).closest('tr');
        var fgMade = parseInt(row.find('.fg-made').val()) || 0;
        var threeMade = parseInt(row.find('.three-made').val()) || 0;
        var ftMade = parseInt(row.find('.ft-made').val()) || 0;
        
        var points = (fgMade - threeMade) * 2 + threeMade * 3 + ftMade;
        row.find('.points').val(points);
        
        updateTotals();
    });

    // Update totals when any stat changes
    function updateTotals() {
        var totals = {
            minutes: 0,
            points: 0,
            rebounds: 0,
            assists: 0,
            steals: 0,
            blocks: 0,
            fgMade: 0,
            fgAttempted: 0,
            threeMade: 0,
            threeAttempted: 0,
            ftMade: 0,
            ftAttempted: 0
        };

        $('.ebm-playerstat-row').each(function() {
        totals.minutes += parseInt($(this).find('.minutes-played').val()) || 0;
        totals.points += parseInt($(this).find('.points').val()) || 0;
        totals.rebounds += parseInt($(this).find('input[name*="[rebounds]"]').val()) || 0;
        totals.assists += parseInt($(this).find('input[name*="[assists]"]').val()) || 0;
        totals.steals += parseInt($(this).find('input[name*="[steals]"]').val()) || 0;
        totals.blocks += parseInt($(this).find('input[name*="[blocks]"]').val()) || 0;
        totals.fgMade += parseInt($(this).find('.fg-made').val()) || 0;
        totals.fgAttempted += parseInt($(this).find('.fg-attempted').val()) || 0;
        totals.threeMade += parseInt($(this).find('.three-made').val()) || 0;
        totals.threeAttempted += parseInt($(this).find('.three-attempted').val()) || 0;
        totals.ftMade += parseInt($(this).find('.ft-made').val()) || 0;
        totals.ftAttempted += parseInt($(this).find('.ft-attempted').val()) || 0;
    });

    $('#total-minutes').text(totals.minutes);
    $('#total-points').text(totals.points);
    $('#total-rebounds').text(totals.rebounds);
    $('#total-assists').text(totals.assists);
    $('#total-steals').text(totals.steals);
    $('#total-blocks').text(totals.blocks);
    $('#total-fg').text(totals.fgMade + '/' + totals.fgAttempted);
    $('#total-three').text(totals.threeMade + '/' + totals.threeAttempted);
    $('#total-ft').text(totals.ftMade + '/' + totals.ftAttempted);

    // Update team score
    $('#team_score').val(totals.points);
}

    // Validate shooting stats
    function validateShootingStats() {
        var valid = true;
        $('.ebm-player-stat-row').each(function() {
            var fgMade = parseInt($(this).find('.fg-made').val()) || 0;
            var fgAttempted = parseInt($(this).find('.fg-attempted').val()) || 0;
            var threeMade = parseInt($(this).find('.three-made').val()) || 0;
            var threeAttempted = parseInt($(this).find('.three-attempted').val()) || 0;
            var ftMade = parseInt($(this).find('.ft-made').val()) || 0;
            var ftAttempted = parseInt($(this).find('.ft-attempted').val()) || 0;

            if (fgMade > fgAttempted) {
                alert('Field goals made cannot be greater than attempted');
                valid = false;
                return false;
            }
            if (threeMade > threeAttempted) {
                alert('Three pointers made cannot be greater than attempted');
                valid = false;
                return false;
            }
            if (ftMade > ftAttempted) {
                alert('Free throws made cannot be greater than attempted');
                valid = false;
                return false;
            }
            if (threeMade > fgMade) {
                alert('Three pointers made cannot be greater than total field goals made');
                valid = false;
                return false;
            }
        });
        return valid;
    }

    // Form submission
    $('.ebm-game-form').on('submit', function(e) {
        if (!validateShootingStats()) {
            e.preventDefault();
            return false;
        }
    });

    // Clear all stats
    $('#clear-all-stats').click(function() {
        if (confirm('Are you sure you want to clear all stats?')) {
            $('.ebm-player-stat-row input[type="number"]').val('');
            updateTotals();
        }
    });

    // Initialize totals
    updateTotals();
});
</script>