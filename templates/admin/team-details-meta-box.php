<div class="ebm-meta-box">
    <div class="ebm-field-row">
        <label for="ebm_season"><?php _e('Season', 'elite-basketball-manager'); ?></label>
        <select id="ebm_season" name="ebm_season">
            <?php
            $current_year = date('Y');
            for ($i = $current_year + 1; $i >= $current_year - 4; $i--) {
                $season = ($i - 1) . '-' . $i;
                echo '<option value="' . esc_attr($season) . '" ' . selected($season, $season, false) . '>' . 
                    esc_html($season) . '</option>';
            }
            ?>
        </select>
    </div>

    <div class="ebm-field-row">
        <label for="ebm_coach"><?php _e('Head Coach', 'elite-basketball-manager'); ?></label>
        <input type="text" id="ebm_coach" name="ebm_coach" value="<?php echo esc_attr($coach); ?>">
    </div>

    <div class="ebm-field-row">
        <label for="ebm_record"><?php _e('Record', 'elite-basketball-manager'); ?></label>
        <input type="text" id="ebm_record" name="ebm_record" value="<?php echo esc_attr($record); ?>" 
            placeholder="15-5">
    </div>

    <div class="ebm-field-row">
        <label for="ebm_roster"><?php _e('Team Roster', 'elite-basketball-manager'); ?></label>
        <div class="ebm-roster-list">
            <?php
            $roster = get_post_meta($post->ID, '_ebm_roster', true);
            $roster = $roster ? $roster : array();

            $players = get_posts(array(
                'post_type' => 'ebm_player',
                'posts_per_page' => -1,
                'orderby' => 'title',
                'order' => 'ASC'
            ));

            if ($players) :
            ?>
                <table class="widefat fixed">
                    <thead>
                        <tr>
                            <th><?php _e('Player', 'elite-basketball-manager'); ?></th>
                            <th><?php _e('Number', 'elite-basketball-manager'); ?></th>
                            <th><?php _e('Position', 'elite-basketball-manager'); ?></th>
                            <th><?php _e('Status', 'elite-basketball-manager'); ?></th>
                        </tr>
                    </thead>
                    <tbody id="ebm-roster-tbody">
                        <?php foreach ($players as $player) : 
                            $jersey = get_post_meta($player->ID, '_ebm_jersey_number', true);
                            $position = get_the_terms($player->ID, 'ebm_position');
                            $position = $position ? $position[0]->name : '';
                        ?>
                            <tr>
                                <td>
                                    <input type="checkbox" name="ebm_roster[]" value="<?php echo $player->ID; ?>"
                                        <?php checked(in_array($player->ID, $roster)); ?>>
                                    <?php echo esc_html($player->post_title); ?>
                                </td>
                                <td><?php echo esc_html($jersey); ?></td>
                                <td><?php echo esc_html($position); ?></td>
                                <td>
                                    <select name="ebm_player_status[<?php echo $player->ID; ?>]">
                                        <option value="active">Active</option>
                                        <option value="injured">Injured</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p><?php _e('No players found. Add players first.', 'elite-basketball-manager'); ?></p>
            <?php endif; ?>
        </div>
    </div>
</div>