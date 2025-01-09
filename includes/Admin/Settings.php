<?php
namespace EBM\Admin;

class Settings {
    private $options;

    public function __construct() {
        add_action('admin_menu', array($this, 'add_settings_menu'));
        add_action('admin_init', array($this, 'initialize_settings'));
    }

    public function add_settings_menu() {
        add_submenu_page(
            'ebm-dashboard',
            __('Settings', 'elite-basketball-manager'),
            __('Settings', 'elite-basketball-manager'),
            'manage_options',
            'ebm-settings',
            array($this, 'render_settings_page')
        );
    }

    public function initialize_settings() {
        register_setting('ebm_settings', 'ebm_settings', array($this, 'sanitize_settings'));

        // General Settings Section
        add_settings_section(
            'ebm_general_settings',
            __('General Settings', 'elite-basketball-manager'),
            array($this, 'render_general_section'),
            'ebm-settings'
        );

        add_settings_field(
            'program_name',
            __('Program Name', 'elite-basketball-manager'),
            array($this, 'render_text_field'),
            'ebm-settings',
            'ebm_general_settings',
            array('field' => 'program_name')
        );

        add_settings_field(
            'program_logo',
            __('Program Logo', 'elite-basketball-manager'),
            array($this, 'render_media_uploader'),
            'ebm-settings',
            'ebm_general_settings',
            array('field' => 'program_logo')
        );

        // Stats Settings Section
        add_settings_section(
            'ebm_stats_settings',
            __('Stats Settings', 'elite-basketball-manager'),
            array($this, 'render_stats_section'),
            'ebm-settings'
        );

        add_settings_field(
            'stats_display',
            __('Stats Display', 'elite-basketball-manager'),
            array($this, 'render_select_field'),
            'ebm-settings',
            'ebm_stats_settings',
            array(
                'field' => 'stats_display',
                'options' => array(
                    'basic' => __('Basic', 'elite-basketball-manager'),
                    'detailed' => __('Detailed', 'elite-basketball-manager'),
                    'advanced' => __('Advanced', 'elite-basketball-manager')
                )
            )
        );

        // Recruiting Settings Section
        add_settings_section(
            'ebm_recruiting_settings',
            __('Recruiting Settings', 'elite-basketball-manager'),
            array($this, 'render_recruiting_section'),
            'ebm-settings'
        );

        add_settings_field(
            'enable_recruiting',
            __('Enable Recruiting Features', 'elite-basketball-manager'),
            array($this, 'render_checkbox_field'),
            'ebm-settings',
            'ebm_recruiting_settings',
            array('field' => 'enable_recruiting')
        );
    }

    public function render_settings_page() {
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }

        $this->options = get_option('ebm_settings');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('ebm_settings');
                do_settings_sections('ebm-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Section Renderers
    public function render_general_section() {
        echo '<p>' . __('Configure general settings for your basketball program.', 'elite-basketball-manager') . '</p>';
    }

    public function render_stats_section() {
        echo '<p>' . __('Configure how player and team statistics are displayed.', 'elite-basketball-manager') . '</p>';
    }

    public function render_recruiting_section() {
        echo '<p>' . __('Configure settings related to recruiting and college commitments.', 'elite-basketball-manager') . '</p>';
    }

    // Field Renderers
    public function render_text_field($args) {
        $field = $args['field'];
        $value = isset($this->options[$field]) ? $this->options[$field] : '';
        
        printf(
            '<input type="text" id="%1$s" name="ebm_settings[%1$s]" value="%2$s" class="regular-text">',
            esc_attr($field),
            esc_attr($value)
        );
    }

    public function render_media_uploader($args) {
        $field = $args['field'];
        $value = isset($this->options[$field]) ? $this->options[$field] : '';
        
        ?>
        <div class="ebm-media-uploader">
            <input type="hidden" name="ebm_settings[<?php echo esc_attr($field); ?>]" 
                   id="<?php echo esc_attr($field); ?>" 
                   value="<?php echo esc_attr($value); ?>">
            
            <div class="ebm-media-preview">
                <?php if ($value) : ?>
                    <?php echo wp_get_attachment_image($value, 'thumbnail'); ?>
                <?php endif; ?>
            </div>
            
            <input type="button" class="button ebm-upload-button" 
                   value="<?php _e('Select Image', 'elite-basketball-manager'); ?>">
            
            <input type="button" class="button ebm-remove-button" 
                   value="<?php _e('Remove Image', 'elite-basketball-manager'); ?>"
                   <?php echo !$value ? 'style="display:none;"' : ''; ?>>
        </div>
        <?php
    }

    public function render_select_field($args) {
        $field = $args['field'];
        $options = $args['options'];
        $value = isset($this->options[$field]) ? $this->options[$field] : '';
        
        echo '<select id="' . esc_attr($field) . '" name="ebm_settings[' . esc_attr($field) . ']">';
        foreach ($options as $key => $label) {
            echo '<option value="' . esc_attr($key) . '"' . selected($value, $key, false) . '>';
            echo esc_html($label);
            echo '</option>';
        }
        echo '</select>';
    }

    public function render_checkbox_field($args) {
        $field = $args['field'];
        $value = isset($this->options[$field]) ? $this->options[$field] : '';
        
        printf(
            '<input type="checkbox" id="%1$s" name="ebm_settings[%1$s]" value="1" %2$s>',
            esc_attr($field),
            checked(1, $value, false)
        );
    }

    public function sanitize_settings($input) {
        $sanitized = array();
        
        if (isset($input['program_name'])) {
            $sanitized['program_name'] = sanitize_text_field($input['program_name']);
        }
        
        if (isset($input['program_logo'])) {
            $sanitized['program_logo'] = absint($input['program_logo']);
        }
        
        if (isset($input['stats_display'])) {
            $sanitized['stats_display'] = sanitize_text_field($input['stats_display']);
        }
        
        $sanitized['enable_recruiting'] = isset($input['enable_recruiting']) ? 1 : 0;
        
        return $sanitized;
    }
}