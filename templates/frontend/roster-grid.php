<div class="ebm-roster-grid">
    <?php if ($players) : ?>
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
        </div>

        <div class="ebm-roster-grid-container">
            <?php foreach ($players as $player) : 
                $photo_id = get_post_thumbnail_id($player->ID);
                $photo_url = $photo_id ? wp_get_attachment_image_url($photo_id, 'large') : '';
                $jersey = get_post_meta($player->ID, '_ebm_jersey_number', true);
                $position = get_the_terms($player->ID, 'ebm_position');
                $position = $position ? $position[0]->name : '';
                $class_year = get_post_meta($player->ID, '_ebm_class_year', true);
                $height = get_post_meta($player->ID, '_ebm_height', true);
                $hometown = get_post_meta($player->ID, '_ebm_hometown', true);
            ?>
                <div class="ebm-player-card" 
                    data-position="<?php echo esc_attr($position); ?>"
                    data-class="<?php echo esc_attr($class_year); ?>">
                    
                    <div class="ebm-player-photo" 
                        style="background-image: url('<?php echo esc_url($photo_url); ?>')">
                        <div class="ebm-player-number"><?php echo esc_html($jersey); ?></div>
                    </div>

                    <div class="ebm-player-info">
                        <h3 class="ebm-player-name">
                            <a href="<?php echo get_permalink($player->ID); ?>">
                                <?php echo esc_html($player->post_title); ?>
                            </a>
                        </h3>
                        <div class="ebm-player-details">
                            <span class="ebm-player-position"><?php echo esc_html($position); ?></span>
                            <span class="ebm-player-separator">•</span>
                            <span class="ebm-player-class"><?php echo esc_html($class_year); ?></span>
                            <?php if ($height) : ?>
                                <span class="ebm-player-separator">•</span>
                                <span class="ebm-player-height"><?php echo esc_html($height); ?></span>
                            <?php endif; ?>
                        </div>
                        <?php if ($hometown) : ?>
                            <div class="ebm-player-hometown">
                                <?php echo esc_html($hometown); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else : ?>
        <p><?php _e('No players found for this team.', 'elite-basketball-manager'); ?></p>
    <?php endif; ?>
</div>