<?php
namespace EBM\Admin;

class RecruitingManager {
    private $wpdb;
    private $table_prefix;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->table_prefix = $wpdb->prefix . 'ebm_';

        add_action('admin_menu', array($this, 'add_recruiting_menu'));
        add_action('admin_init', array($this, 'handle_recruiting_actions'));
        add_action('wp_ajax_ebm_update_recruitment', array($this, 'ajax_update_recruitment'));
    }

    public function add_recruiting_menu() {
        add_submenu_page(
            'ebm-dashboard',
            __('Recruiting', 'elite-basketball-manager'),
            __('Recruiting', 'elite-basketball-manager'),
            'manage_options',
            'ebm-recruiting',
            array($this, 'render_recruiting_page')
        );
    }

    public function render_recruiting_page() {
        // Handle any POST actions
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
            $this->handle_recruiting_actions();
        }

        // Get recruiting data
        $recruiting_data = $this->get_recruiting_data();
        
        // Include template
        include EBM_TEMPLATES_DIR . 'admin/recruiting-dashboard.php';
    }

    private function get_recruiting_data() {
        return array(
            'active_recruits' => $this->get_active_recruits(),
            'committed_players' => $this->get_committed_players(),
            'division_breakdown' => $this->get_division_breakdown(),
            'recent_activity' => $this->get_recent_activity()
        );
    }

    private function get_active_recruits() {
        return $this->wpdb->get_results("
            SELECT 
                p.ID as player_id,
                p.post_title as player_name,
                r.*,
                COUNT(DISTINCT i.id) as interactions
            FROM {$this->wpdb->posts} p
            JOIN {$this->table_prefix}recruitment r ON p.ID = r.player_id
            LEFT JOIN {$this->table_prefix}recruiting_interactions i ON r.player_id = i.player_id
            WHERE r.status IN ('interested', 'offered', 'visiting')
            GROUP BY p.ID
            ORDER BY r.last_updated DESC
        ");
    }

    private function get_committed_players() {
        return $this->wpdb->get_results("
            SELECT 
                p.ID as player_id,
                p.post_title as player_name,
                r.*
            FROM {$this->wpdb->posts} p
            JOIN {$this->table_prefix}recruitment r ON p.ID = r.player_id
            WHERE r.status = 'committed'
            ORDER BY r.commitment_date DESC
        ");
    }

    private function get_division_breakdown() {
        return $this->wpdb->get_results("
            SELECT 
                division,
                COUNT(*) as count,
                COUNT(*) * 100.0 / (
                    SELECT COUNT(*) 
                    FROM {$this->table_prefix}recruitment 
                    WHERE status = 'committed'
                ) as percentage
            FROM {$this->table_prefix}recruitment
            WHERE status = 'committed'
            GROUP BY division
            ORDER BY count DESC
        ");
    }

    private function get_recent_activity() {
        return $this->wpdb->get_results("
            SELECT 
                p.ID as player_id,
                p.post_title as player_name,
                i.*
            FROM {$this->table_prefix}recruiting_interactions i
            JOIN {$this->wpdb->posts} p ON i.player_id = p.ID
            ORDER BY i.interaction_date DESC
            LIMIT 10
        ");
    }

    public function handle_recruiting_actions() {
        if (!isset($_POST['ebm_recruiting_nonce']) || 
            !wp_verify_nonce($_POST['ebm_recruiting_nonce'], 'ebm_recruiting_action')) {
            return;
        }

        $action = $_POST['action'];
        $player_id = isset($_POST['player_id']) ? intval($_POST['player_id']) : 0;

        switch ($action) {
            case 'add_interaction':
                $this->add_interaction($player_id, $_POST);
                break;

            case 'update_status':
                $this->update_recruitment_status($player_id, $_POST);
                break;

            case 'add_commitment':
                $this->add_commitment($player_id, $_POST);
                break;
        }
    }

    public function ajax_update_recruitment() {
        check_ajax_referer('ebm_recruiting_action', 'nonce');

        $player_id = isset($_POST['player_id']) ? intval($_POST['player_id']) : 0;
        $data = array(
            'status' => sanitize_text_field($_POST['status']),
            'notes' => sanitize_textarea_field($_POST['notes']),
            'last_updated' => current_time('mysql')
        );

        if ($_POST['status'] === 'committed') {
            $data['college_name'] = sanitize_text_field($_POST['college_name']);
            $data['division'] = sanitize_text_field($_POST['division']);
            $data['commitment_date'] = sanitize_text_field($_POST['commitment_date']);
        }

        $result = $this->wpdb->update(
            $this->table_prefix . 'recruitment',
            $data,
            array('player_id' => $player_id)
        );

        wp_send_json_success($result);
    }

    private function add_interaction($player_id, $data) {
        $this->wpdb->insert(
            $this->table_prefix . 'recruiting_interactions',
            array(
                'player_id' => $player_id,
                'type' => sanitize_text_field($data['interaction_type']),
                'notes' => sanitize_textarea_field($data['notes']),
                'interaction_date' => current_time('mysql'),
                'user_id' => get_current_user_id()
            )
        );
    }

    private function update_recruitment_status($player_id, $data) {
        $this->wpdb->update(
            $this->table_prefix . 'recruitment',
            array(
                'status' => sanitize_text_field($data['status']),
                'notes' => sanitize_textarea_field($data['notes']),
                'last_updated' => current_time('mysql')
            ),
            array('player_id' => $player_id)
        );
    }

    private function add_commitment($player_id, $data) {
        $this->wpdb->update(
            $this->table_prefix . 'recruitment',
            array(
                'status' => 'committed',
                'college_name' => sanitize_text_field($data['college_name']),
                'division' => sanitize_text_field($data['division']),
                'commitment_date' => sanitize_text_field($data['commitment_date']),
                'notes' => sanitize_textarea_field($data['notes']),
                'last_updated' => current_time('mysql')
            ),
            array('player_id' => $player_id)
        );
    }
}