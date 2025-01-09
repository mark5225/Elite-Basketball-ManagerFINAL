<?php
namespace EBM\Admin;

use EBM\DB\Database;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class TeamList extends \WP_List_Table {
    private $db;
    
    public function __construct() {
        parent::__construct([
            'singular' => 'team',
            'plural'   => 'teams',
            'ajax'     => false
        ]);
        
        $this->db = Database::get_instance();
    }

    public function get_columns() {
        return [
            'cb'            => '<input type="checkbox" />',
            'team_name'     => 'Team Name',
            'team_type'     => 'Type',
            'location'      => 'Location',
            'head_coach'    => 'Head Coach',
            'season'        => 'Season'
        ];
    }

    public function get_sortable_columns() {
        return [
            'team_name'    => ['team_name', true],
            'team_type'    => ['team_type', false],
            'location'     => ['location', false],
            'season'       => ['season', false]
        ];
    }

    protected function column_default($item, $column_name) {
        return $item[$column_name] ?? '';
    }

    protected function column_team_name($item) {
        $actions = [
            'edit'      => sprintf('<a href="?page=basketball-manager-teams&action=edit&team=%s">Edit</a>', $item['id']),
            'roster'    => sprintf('<a href="?page=basketball-manager-teams&action=roster&team=%s">View Roster</a>', $item['id']),
            'delete'    => sprintf(
                '<a href="%s" onclick="return confirm(\'Are you sure?\');">Delete</a>',
                wp_nonce_url(
                    sprintf('?page=basketball-manager-teams&action=delete&team=%s', $item['id']),
                    'delete_team_' . $item['id']
                )
            )
        ];

        return sprintf('%1$s %2$s',
            '<strong>' . esc_html($item['team_name']) . '</strong>',
            $this->row_actions($actions)
        );
    }

    protected function get_bulk_actions() {
        return [
            'delete' => 'Delete'
        ];
    }

    public function prepare_items() {
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $total_items = $this->db->count_teams();

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ]);

        $orderby = isset($_REQUEST['orderby']) ? $_REQUEST['orderby'] : 'team_name';
        $order = isset($_REQUEST['order']) ? $_REQUEST['order'] : 'ASC';
        
        $this->items = $this->db->get_teams([
            'orderby' => $orderby,
            'order' => $order,
            'limit' => $per_page,
            'offset' => ($current_page - 1) * $per_page
        ]);
    }

    protected function process_bulk_action() {
        if ('delete' === $this->current_action()) {
            $team_ids = isset($_REQUEST['team']) ? (array) $_REQUEST['team'] : [];
            
            if (!empty($team_ids)) {
                foreach ($team_ids as $id) {
                    if (wp_verify_nonce($_REQUEST['_wpnonce'], 'bulk-' . $this->_args['plural'])) {
                        $this->db->delete_team($id);
                    }
                }
            }
        }
    }
}