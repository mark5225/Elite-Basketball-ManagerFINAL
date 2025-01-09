<?php
namespace EBM\Core;

use EBM\DB\Database;

class Plugin {
    private static $instance = null;
    private $db;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->db = Database::get_instance();
        $this->load_dependencies();
    }

    private function load_dependencies() {
        if (is_admin()) {
            add_action('admin_menu', [$this, 'register_admin_menus']);
            add_action('admin_post_ebm_save_team', [$this, 'handle_team_form']);
        }
        return true;
    }

    public function register_admin_menus() {
        // Add main menu
        add_menu_page(
            'Basketball Manager',
            'Basketball Manager',
            'manage_options',
            'basketball-manager',
            [$this, 'render_dashboard'],
            'dashicons-groups',
            30
        );

        // Add submenus
        $this->add_submenu_pages();
    }

    private function add_submenu_pages() {
        $submenus = [
            [
                'parent_slug' => 'basketball-manager',
                'page_title' => 'Dashboard',
                'menu_title' => 'Dashboard',
                'capability' => 'manage_options',
                'menu_slug' => 'basketball-manager',
                'callback' => [$this, 'render_dashboard']
            ],
            [
                'parent_slug' => 'basketball-manager',
                'page_title' => 'Teams',
                'menu_title' => 'Teams',
                'capability' => 'manage_options',
                'menu_slug' => 'basketball-manager-teams',
                'callback' => [$this, 'render_teams']
            ]
        ];

        foreach ($submenus as $submenu) {
            add_submenu_page(
                $submenu['parent_slug'],
                $submenu['page_title'],
                $submenu['menu_title'],
                $submenu['capability'],
                $submenu['menu_slug'],
                $submenu['callback']
            );
        }
    }

    public function render_dashboard() {
        echo '<div class="wrap">';
        echo '<h1>Basketball Manager Dashboard</h1>';
        echo '<div class="dashboard-widgets">';
        echo '<p>Welcome to Basketball Manager! More features coming soon.</p>';
        echo '</div>';
        echo '</div>';
    }

    public function render_teams() {
        $action = isset($_GET['action']) ? $_GET['action'] : 'list';
        
        switch($action) {
            case 'add':
            case 'edit':
                $team_id = isset($_GET['team']) ? intval($_GET['team']) : 0;
                $team = $team_id ? $this->db->get_team($team_id) : null;
                include EBM_PATH . 'includes/Views/team-form.php';
                break;
                
            case 'delete':
                $this->handle_team_delete();
                break;
                
            default:
                include EBM_PATH . 'includes/Views/team-list.php';
        }
    }

    public function handle_team_form() {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        check_admin_referer('ebm_save_team');

        $team_data = [
            'team_name' => sanitize_text_field($_POST['team_name']),
            'team_type' => sanitize_text_field($_POST['team_type']),
            'location' => sanitize_text_field($_POST['location']),
            'head_coach' => sanitize_text_field($_POST['head_coach']),
            'season' => sanitize_text_field($_POST['season'])
        ];

        if (isset($_POST['team_id'])) {
            $team_data['id'] = intval($_POST['team_id']);
        }

        $this->db->save_team($team_data);

        wp_redirect(add_query_arg(
            ['page' => 'basketball-manager-teams', 'message' => 'team_saved'],
            admin_url('admin.php')
        ));
        exit;
    }

    private function handle_team_delete() {
        $team_id = isset($_GET['team']) ? intval($_GET['team']) : 0;
        
        if ($team_id && wp_verify_nonce($_REQUEST['_wpnonce'], 'delete_team_' . $team_id)) {
            $this->db->delete_team($team_id);
            wp_redirect(add_query_arg(
                ['page' => 'basketball-manager-teams', 'message' => 'team_deleted'],
                admin_url('admin.php')
            ));
            exit;
        }
    }
}