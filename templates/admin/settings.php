<?php if (!defined('ABSPATH')) exit; ?>

<div class="wrap ebm-settings">
    <h1><?php _e('Basketball Manager Settings', 'elite-basketball-manager'); ?></h1>

    <?php
    if (isset($_POST['ebm_settings_nonce']) && wp_verify_nonce($_POST['ebm_settings_nonce'], 'ebm_save_settings')) {
        update_option('ebm_program_name', sanitize_text_field($_POST['program_name']));
        update_option('ebm_program_logo', intval($_POST['program_logo']));
        update_option('ebm_stats_display', sanitize_text_field($_POST['stats_display']));
        update_option('ebm_enable_recruiting', isset($_POST['enable_recruiting']));
        update_option('ebm_player_positions', array_map('sanitize_text_field', explode("\n", $_POST['player_positions'])));
        
        echo '<div class="notice notice-success"><p>' . __('Settings saved successfully!', 'elite-basketball-manager') . '</p></div>';
    }

    $program_name = get_option('ebm_program_name', '');
    $program_logo = get_option('ebm_program_logo', 0);
    $stats_display = get_option('ebm_stats_display', 'detailed');
    $enable_recruiting = get_option('ebm_enable_recruiting', true);
    $player_positions = get_option('ebm_player_positions', array('PG', 'SG', 'SF', 'PF', 'C'));
    if (is_array($player_positions)) {
        $player_positions = implode("\n", $player_positions);
    }
    ?>

    <form method="post" action="" class="ebm-settings-form">
        <?php wp_nonce_field('ebm_save_settings', 'ebm_settings_nonce'); ?>

        <div class="ebm-settings-grid">
            <!-- General Settings -->
            <div class="postbox">
                <h2 class="hndle"><?php _e('General Settings', 'elite-basketball-manager'); ?></h2>
                <div class="inside">
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="program_name"><?php _e('Program Name', 'elite-basketball-manager'); ?></label>
                            </th>
                            <td>
                                <input type="text" id="program_name" name="program_name" 
                                    value="<?php echo esc_attr($program_name); ?>" class="regular-text">
                            </td>
                        </tr>

                        <tr>
                            <th scope="row">
                                <label for="program_logo"><?php _e('Program Logo', 'elite-basketball-manager'); ?></label>
                            </th>
                            <td>
                                <div class="ebm-logo-upload">
                                    <input type="hidden" name="program_logo" id="program_logo" 
                                        value="<?php echo esc_attr($program_logo); ?>">
                                    <div class="ebm-logo-preview">
                                        <?php if ($program_logo) : ?>
                                            <?php echo wp_get_attachment_image($program_logo, 'thumbnail'); ?>
                                        <?php endif; ?>
                                    </div>
                                    <button type="button" class="button ebm-upload-logo">
                                        <?php _e('Select Logo', 'elite-basketball-manager'); ?>
                                    </button>
                                    <button type="button" class="button ebm-remove-logo" 
                                        <?php echo !$program_logo ? 'style="display:none;"' : ''; ?>>
                                        <?php _e('Remove Logo', 'elite-basketball-manager'); ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Display Settings -->
            <div class="postbox">
                <h2 class="hndle"><?php _e('Display Settings', 'elite-basketball-manager'); ?></h2>
                <div class="inside">
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="stats_display"><?php _e('Stats Display', 'elite-basketball-manager'); ?></label>
                            </th>
                            <td>
                                <select name="stats_display" id="stats_display">
                                    <option value="basic" <?php selected($stats_display, 'basic'); ?>>
                                        <?php _e('Basic', 'elite-basketball-manager'); ?>
                                    </option>
                                    <option value="detailed" <?php selected($stats_display, 'detailed'); ?>>
                                        <?php _e('Detailed', 'elite-basketball-manager'); ?>
                                    </option>
                                    <option value="advanced" <?php selected($stats_display, 'advanced'); ?>>
                                        <?php _e('Advanced', 'elite-basketball-manager'); ?>
                                    </option>
                                </select>
                                <p class="description">
                                    <?php _e('Choose how detailed player statistics should be displayed.', 'elite-basketball-manager'); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Player Settings -->
            <div class="postbox">
                <h2 class="hndle"><?php _e('Player Settings', 'elite-basketball-manager'); ?></h2>
                <div class="inside">
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <label for="player_positions">
                                    <?php _e('Player Positions', 'elite-basketball-manager'); ?>
                                </label>
                            </th>
                            <td>
                                <textarea name="player_positions" id="player_positions" rows="5" class="large-text">
                                    <?php echo esc_textarea($player_positions); ?>
                                </textarea>
                                <p class="description">
                                    <?php _e('Enter one position per line. These will be available when adding/editing players.', 'elite-basketball-manager'); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Recruiting Settings -->
            <div class="postbox">
                <h2 class="hndle"><?php _e('Recruiting Settings', 'elite-basketball-manager'); ?></h2>
                <div class="inside">
                    <table class="form-table">
                        <tr>
                            <th scope="row">
                                <?php _e('Recruiting Features', 'elite-basketball-manager'); ?>
                            </th>
                            <td>
                                <label>
                                    <input type="checkbox" name="enable_recruiting" value="1" 
                                        <?php checked($enable_recruiting); ?>>
                                    <?php _e('Enable recruiting features', 'elite-basketball-manager'); ?>
                                </label>
                                <p class="description">
                                    <?php _e('Enable tracking of college commitments and recruiting activities.', 'elite-basketball-manager'); ?>
                                </p>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <p class="submit">
            <button type="submit" class="button button-primary">
                <?php _e('Save Settings', 'elite-basketball-manager'); ?>
            </button>
        </p>
    </form>
</div>

<script>
jQuery(document).ready(function($) {
    // Logo upload handling
    $('.ebm-upload-logo').click(function(e) {
        e.preventDefault();
        
        var button = $(this);
        var frame = wp.media({
            title: '<?php _e('Select Program Logo', 'elite-basketball-manager'); ?>',
            multiple: false
        });

        frame.on('select', function() {
            var attachment = frame.state().get('selection').first().toJSON();
            $('#program_logo').val(attachment.id);
            $('.ebm-logo-preview').html('<img src="' + attachment.url + '" alt="">');
            $('.ebm-remove-logo').show();
        });

        frame.open();
    });

    $('.ebm-remove-logo').click(function(e) {
        e.preventDefault();
        $('#program_logo').val('');
        $('.ebm-logo-preview').empty();
        $(this).hide();
    });
});
</script>