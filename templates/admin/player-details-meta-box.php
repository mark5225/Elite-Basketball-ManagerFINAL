<div class="ebm-meta-box">
    <div class="ebm-field-row">
        <label for="ebm_height"><?php _e('Height', 'elite-basketball-manager'); ?></label>
        <input type="text" id="ebm_height" name="ebm_height" value="<?php echo esc_attr($height); ?>" placeholder="6'2&quot;">
    </div>

    <div class="ebm-field-row">
        <label for="ebm_weight"><?php _e('Weight (lbs)', 'elite-basketball-manager'); ?></label>
        <input type="number" id="ebm_weight" name="ebm_weight" value="<?php echo esc_attr($weight); ?>">
    </div>

    <div class="ebm-field-row">
        <label for="ebm_wingspan"><?php _e('Wingspan', 'elite-basketball-manager'); ?></label>
        <input type="text" id="ebm_wingspan" name="ebm_wingspan" value="<?php echo esc_attr($wingspan); ?>" placeholder="6'4&quot;">
    </div>

    <div class="ebm-field-row">
        <label for="ebm_vertical"><?php _e('Vertical (inches)', 'elite-basketball-manager'); ?></label>
        <input type="number" id="ebm_vertical" name="ebm_vertical" value="<?php echo esc_attr($vertical); ?>">
    </div>

    <div class="ebm-field-row">
        <label for="ebm_jersey_number"><?php _e('Jersey Number', 'elite-basketball-manager'); ?></label>
        <input type="number" id="ebm_jersey_number" name="ebm_jersey_number" value="<?php echo esc_attr($jersey); ?>">
    </div>

    <div class="ebm-field-row">
        <label for="ebm_class_year"><?php _e('Class Year', 'elite-basketball-manager'); ?></label>
        <select id="ebm_class_year" name="ebm_class_year">
            <option value=""><?php _e('Select Class Year', 'elite-basketball-manager'); ?></option>
            <?php
            $years = array('Freshman', 'Sophomore', 'Junior', 'Senior');
            foreach ($years as $year) {
                echo '<option value="' . esc_attr($year) . '" ' . selected($class_year, $year, false) . '>' . esc_html($year) . '</option>';
            }
            ?>
        </select>
    </div>
</div>