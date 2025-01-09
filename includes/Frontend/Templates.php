<?php
namespace EBM\Frontend;

class Templates {
    public function __construct() {
        add_filter('single_template', array($this, 'load_player_template'));
        add_filter('single_template', array($this, 'load_team_template'));
        add_filter('archive_template', array($this, 'load_archive_template'));
    }

    public function load_player_template($template) {
        if (is_singular('ebm_player')) {
            $new_template = EBM_TEMPLATES_DIR . 'frontend/single-player.php';
            if (file_exists($new_template)) {
                return $new_template;
            }
        }
        return $template;
    }

    public function load_team_template($template) {
        if (is_singular('ebm_team')) {
            $new_template = EBM_TEMPLATES_DIR . 'frontend/single-team.php';
            if (file_exists($new_template)) {
                return $new_template;
            }
        }
        return $template;
    }

    public function load_archive_template($template) {
        if (is_post_type_archive('ebm_player') || is_tax('ebm_position')) {
            $new_template = EBM_TEMPLATES_DIR . 'frontend/archive-player.php';
            if (file_exists($new_template)) {
                return $new_template;
            }
        } elseif (is_post_type_archive('ebm_team')) {
            $new_template = EBM_TEMPLATES_DIR . 'frontend/archive-team.php';
            if (file_exists($new_template)) {
                return $new_template;
            }
        }
        return $template;
    }

    public static function get_template_part($slug, $name = null, $args = array()) {
        if ($args && is_array($args)) {
            extract($args);
        }

        $template = '';

        if ($name) {
            $template = locate_template(array(
                "ebm/{$slug}-{$name}.php",
                "ebm/{$slug}.php"
            ));
        }

        if (!$template) {
            $template = EBM_TEMPLATES_DIR . "frontend/{$slug}.php";
        }

        if (file_exists($template)) {
            include $template;
        }
    }
}