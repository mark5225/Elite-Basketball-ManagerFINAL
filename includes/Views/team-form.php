<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap">
    <h1><?php echo isset($team) ? 'Edit Team' : 'Add New Team'; ?></h1>
    
    <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
        <input type="hidden" name="action" value="ebm_save_team">
        <?php wp_nonce_field('ebm_save_team'); ?>
        
        <?php if (isset($team['id'])): ?>
            <input type="hidden" name="team_id" value="<?php echo esc_attr($team['id']); ?>">
        <?php endif; ?>
        
        <table class="form-table">
            <tr>
                <th scope="row"><label for="team_name">Team Name</label></th>
                <td>
                    <input name="team_name" type="text" id="team_name" 
                           value="<?php echo esc_attr($team['team_name'] ?? ''); ?>" 
                           class="regular-text" required>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="team_type">Team Type</label></th>
                <td>
                    <select name="team_type" id="team_type" required>
                        <option value="">Select Type</option>
                        <?php
                        $types = ['club' => 'Club', 'school' => 'School', 'league' => 'League'];
                        foreach ($types as $value => $label) {
                            $selected = ($team['team_type'] ?? '') === $value ? 'selected' : '';
                            echo "<option value='" . esc_attr($value) . "' $selected>" . esc_html($label) . "</option>";
                        }
                        ?>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="location">Location</label></th>
                <td>
                    <input name="location" type="text" id="location" 
                           value="<?php echo esc_attr($team['location'] ?? ''); ?>" 
                           class="regular-text">
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="head_coach">Head Coach</label></th>
                <td>
                    <input name="head_coach" type="text" id="head_coach" 
                           value="<?php echo esc_attr($team['head_coach'] ?? ''); ?>" 
                           class="regular-text">
                </td>
            </tr>
            
            <tr>
                <th scope="row"><label for="season">Season</label></th>
                <td>
                    <input name="season" type="text" id="season" 
                           value="<?php echo esc_attr($team['season'] ?? ''); ?>" 
                           class="regular-text">
                </td>
            </tr>
        </table>
        
        <p class="submit">
            <input type="submit" name="submit" id="submit" class="button button-primary" 
                   value="<?php echo isset($team) ? 'Update Team' : 'Add Team'; ?>">
            <a href="?page=basketball-manager-teams" class="button">Cancel</a>
        </p>
    </form>
</div>